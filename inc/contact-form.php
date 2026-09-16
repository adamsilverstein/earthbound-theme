<?php
/**
 * Contact form submission handling.
 *
 * Exposes a public REST endpoint that validates a submission and emails it to
 * the site owner. The recipient address is resolved on the server and is never
 * sent to the browser, so it stays out of the page markup, the block
 * attributes, and every API response.
 *
 * @package Earthbound
 * @since 1.0.0
 */

declare(strict_types=1);

/**
 * Maximum submissions allowed from a single IP address per hour.
 */
const EARTHBOUND_CONTACT_RATE_LIMIT = 5;

/**
 * Minimum milliseconds between form render and submission.
 *
 * Anything faster than this was almost certainly not typed by a person.
 */
const EARTHBOUND_CONTACT_MIN_FILL_MS = 3000;

/**
 * Register the contact form REST endpoint.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_register_contact_endpoint(): void {
    register_rest_route(
        'earthbound/v1',
        '/contact',
        array(
            'methods'  => 'POST',
            'callback' => 'earthbound_handle_contact_submission',
            /*
             * The form is public and the endpoint performs no privileged action,
             * so there is no capability to check. A nonce is deliberately not
             * required: the form is rendered inside pages that full page caching
             * serves to anonymous visitors, which means any nonce baked into the
             * markup would be stale for most submitters. Abuse is handled by the
             * honeypot, the minimum fill time, and the per-IP rate limit below.
             */
            'permission_callback' => '__return_true',
            'args'                => array(
                'name'    => array(
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'email'   => array(
                    'required'          => true,
                    'type'              => 'string',
                    /*
                     * Deliberately not sanitize_email(): it reduces a malformed
                     * address to an empty string, which is indistinguishable
                     * from an omitted one and produces the wrong error message.
                     * The value is validated with is_email() and normalised
                     * below instead.
                     */
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'message' => array(
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_textarea_field',
                ),
                'website' => array(
                    'type'              => 'string',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'elapsed' => array(
                    'type'              => 'integer',
                    'default'           => 0,
                    'sanitize_callback' => 'absint',
                ),
            ),
        )
    );
}
add_action('rest_api_init', 'earthbound_register_contact_endpoint');

/**
 * Resolve the address contact submissions are sent to.
 *
 * Defaults to the site administration email so the address lives in the
 * database rather than in the theme source. Never expose the return value to
 * the browser.
 *
 * @since 1.0.0
 * @return string Recipient email address.
 */
function earthbound_get_contact_recipient(): string {
    /**
     * Filters the address that contact form submissions are delivered to.
     *
     * @since 1.0.0
     * @param string $recipient Recipient email address.
     */
    $recipient = (string) apply_filters('earthbound_contact_form_recipient', get_option('admin_email', ''));

    return is_email($recipient) ? $recipient : '';
}

/**
 * Build the rate limit transient key for the current requester.
 *
 * The address is hashed so a raw IP is never used as a storage key.
 *
 * @since 1.0.0
 * @return string Transient key.
 */
function earthbound_get_contact_rate_key(): string {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) wp_unslash($_SERVER['REMOTE_ADDR']) : '';

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = 'unknown';
    }

    return 'earthbound_contact_' . md5($ip . wp_salt());
}

/**
 * Validate a submission and email it to the site owner.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response|WP_Error Response on success, error on failure.
 */
function earthbound_handle_contact_submission(WP_REST_Request $request): WP_REST_Response|WP_Error {
    /*
     * Honeypot. The field is hidden from people and left empty by them, so
     * anything in it means an automated submission. Report success rather than
     * an error, which gives a bot nothing to adapt to.
     */
    if ('' !== trim((string) $request->get_param('website'))) {
        return new WP_REST_Response(
            array(
                'success' => true,
                'message' => earthbound_get_contact_success_message(),
            ),
            200
        );
    }

    // Forms completed faster than a person could type are treated the same way.
    $elapsed = (int) $request->get_param('elapsed');
    if ($elapsed > 0 && $elapsed < EARTHBOUND_CONTACT_MIN_FILL_MS) {
        return new WP_REST_Response(
            array(
                'success' => true,
                'message' => earthbound_get_contact_success_message(),
            ),
            200
        );
    }

    // Per-IP rate limit.
    $rate_key = earthbound_get_contact_rate_key();
    $attempts = (int) get_transient($rate_key);

    if ($attempts >= EARTHBOUND_CONTACT_RATE_LIMIT) {
        return new WP_Error(
            'earthbound_contact_rate_limited',
            esc_html__('Too many messages sent from this connection. Please try again later.', 'earthbound'),
            array('status' => 429)
        );
    }

    $name    = trim((string) $request->get_param('name'));
    $email   = trim((string) $request->get_param('email'));
    $message = trim((string) $request->get_param('message'));

    $errors = array();

    if ('' === $name) {
        $errors['name'] = esc_html__('Please enter your name.', 'earthbound');
    } elseif (mb_strlen($name) > 100) {
        $errors['name'] = esc_html__('Please keep your name under 100 characters.', 'earthbound');
    }

    if ('' === $email) {
        $errors['email'] = esc_html__('Please enter your email address.', 'earthbound');
    } elseif (!is_email($email)) {
        $errors['email'] = esc_html__('Please enter a valid email address.', 'earthbound');
    } else {
        $email = sanitize_email($email);
    }

    if ('' === $message) {
        $errors['message'] = esc_html__('Please enter a message.', 'earthbound');
    } elseif (mb_strlen($message) > 5000) {
        $errors['message'] = esc_html__('Please keep your message under 5000 characters.', 'earthbound');
    }

    if (!empty($errors)) {
        return new WP_Error(
            'earthbound_contact_invalid',
            esc_html__('Please correct the highlighted fields and try again.', 'earthbound'),
            array(
                'status' => 400,
                'fields' => $errors,
            )
        );
    }

    $recipient = earthbound_get_contact_recipient();

    if ('' === $recipient) {
        // Never name the missing address in a public response.
        return new WP_Error(
            'earthbound_contact_unavailable',
            esc_html__('The contact form is not available right now. Please try again later.', 'earthbound'),
            array('status' => 500)
        );
    }

    $sent = wp_mail(
        $recipient,
        earthbound_build_contact_subject($name),
        earthbound_build_contact_body($name, $email, $message),
        earthbound_build_contact_headers($name, $email)
    );

    if (!$sent) {
        return new WP_Error(
            'earthbound_contact_send_failed',
            esc_html__('The message could not be sent. Please try again later.', 'earthbound'),
            array('status' => 500)
        );
    }

    set_transient($rate_key, $attempts + 1, HOUR_IN_SECONDS);

    return new WP_REST_Response(
        array(
            'success' => true,
            'message' => earthbound_get_contact_success_message(),
        ),
        200
    );
}

/**
 * The confirmation shown after a message is accepted.
 *
 * @since 1.0.0
 * @return string Success message.
 */
function earthbound_get_contact_success_message(): string {
    return esc_html__('Thanks for getting in touch. Your message is on its way.', 'earthbound');
}

/**
 * Build the email subject line.
 *
 * @since 1.0.0
 * @param string $name Sender name.
 * @return string Subject line.
 */
function earthbound_build_contact_subject(string $name): string {
    $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);

    return earthbound_strip_header_breaks(
        sprintf(
            /* translators: 1: Site name, 2: Name of the person who submitted the form. */
            esc_html__('[%1$s] Contact form message from %2$s', 'earthbound'),
            $site_name,
            $name
        )
    );
}

/**
 * Build the plain text email body.
 *
 * @since 1.0.0
 * @param string $name    Sender name.
 * @param string $email   Sender email address.
 * @param string $message Message body.
 * @return string Email body.
 */
function earthbound_build_contact_body(string $name, string $email, string $message): string {
    $lines = array(
        esc_html__('New message from the contact form.', 'earthbound'),
        '',
        sprintf(/* translators: %s: Name of the person who submitted the form. */ esc_html__('Name: %s', 'earthbound'), $name),
        sprintf(/* translators: %s: Email address of the person who submitted the form. */ esc_html__('Email: %s', 'earthbound'), $email),
        sprintf(
            /* translators: %s: Date and time the form was submitted. */
            esc_html__('Sent: %s', 'earthbound'),
            date_i18n(get_option('date_format') . ' ' . get_option('time_format'))
        ),
        '',
        esc_html__('Message:', 'earthbound'),
        $message,
        '',
        '--',
        sprintf(/* translators: %s: Site URL. */ esc_html__('Sent from %s', 'earthbound'), home_url('/')),
    );

    return implode("\r\n", $lines);
}

/**
 * Build the email headers.
 *
 * The From address stays on the site's own domain so the message is not
 * rejected by SPF or DMARC. The submitter's address goes in Reply-To instead,
 * which keeps replying to them a single click away.
 *
 * @since 1.0.0
 * @param string $name  Sender name.
 * @param string $email Sender email address.
 * @return array<int, string> Email headers.
 */
function earthbound_build_contact_headers(string $name, string $email): array {
    $reply_email = earthbound_strip_header_breaks($email);

    /*
     * Quote the display name so characters that are significant in a header,
     * such as a colon or an angle bracket, cannot be read as structure by a
     * mail parser. Line breaks are already removed, so this is belt and braces.
     */
    $reply_name = str_replace(
        array('\\', '"'),
        array('\\\\', '\\"'),
        earthbound_strip_header_breaks($name)
    );

    return array(
        'Content-Type: text/plain; charset=UTF-8',
        sprintf('Reply-To: "%s" <%s>', $reply_name, $reply_email),
    );
}

/**
 * Remove characters that would let a value break out of an email header.
 *
 * @since 1.0.0
 * @param string $value Raw value.
 * @return string Value safe to place in a header.
 */
function earthbound_strip_header_breaks(string $value): string {
    return trim(str_replace(array("\r", "\n", "\t", "\0"), ' ', $value));
}
