<?php
/**
 * Contact Form block render template.
 *
 * The address submissions are delivered to is resolved server side when the
 * REST request is handled, so it never appears in this markup.
 *
 * @package Earthbound
 * @since 1.0.0
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

declare(strict_types=1);

$submit_label = $attributes['submitLabel'] ?? esc_html__('Send Message', 'earthbound');

// Unique per instance so the block can be used more than once on a page.
$instance_id  = wp_unique_id('earthbound-contact-');
$name_id      = $instance_id . '-name';
$email_id     = $instance_id . '-email';
$message_id   = $instance_id . '-message';
$website_id   = $instance_id . '-website';

$context = array(
    'name'           => '',
    'email'          => '',
    'message'        => '',
    'website'        => '',
    'errors'         => array(
        'name'    => '',
        'email'   => '',
        'message' => '',
    ),
    'formError'      => '',
    'successMessage' => '',
    'isSubmitting'   => false,
    'isSent'         => false,
    'loadedAt'       => 0,
    'submitLabel'    => $submit_label,
    'restUrl'        => esc_url_raw(rest_url('earthbound/v1/contact')),
);

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class'               => 'contact-form',
        'data-wp-interactive' => 'earthbound/contact-form',
        'data-wp-context'     => wp_json_encode($context),
        'data-wp-init'        => 'callbacks.init',
    )
);
?>

<div <?php echo $wrapper_attributes; ?>>

    <form
        class="contact-form__form"
        method="post"
        novalidate
        data-wp-on--submit="actions.submit"
        data-wp-bind--hidden="state.isSent"
    >

        <div class="contact-form__field">
            <label class="contact-form__label" for="<?php echo esc_attr($name_id); ?>">
                <?php esc_html_e('Name', 'earthbound'); ?>
                <span class="contact-form__required" aria-hidden="true">*</span>
                <span class="screen-reader-text"><?php esc_html_e('(required)', 'earthbound'); ?></span>
            </label>
            <input
                class="contact-form__input"
                id="<?php echo esc_attr($name_id); ?>"
                type="text"
                name="name"
                autocomplete="name"
                required
                aria-required="true"
                aria-describedby="<?php echo esc_attr($name_id); ?>-error"
                data-wp-bind--value="context.name"
                data-wp-bind--aria-invalid="state.hasNameError"
                data-wp-bind--disabled="context.isSubmitting"
                data-wp-on--input="actions.updateName"
            />
            <p
                class="contact-form__error"
                id="<?php echo esc_attr($name_id); ?>-error"
                data-wp-text="context.errors.name"
                data-wp-bind--hidden="!state.hasNameError"
            ></p>
        </div>

        <div class="contact-form__field">
            <label class="contact-form__label" for="<?php echo esc_attr($email_id); ?>">
                <?php esc_html_e('Email', 'earthbound'); ?>
                <span class="contact-form__required" aria-hidden="true">*</span>
                <span class="screen-reader-text"><?php esc_html_e('(required)', 'earthbound'); ?></span>
            </label>
            <input
                class="contact-form__input"
                id="<?php echo esc_attr($email_id); ?>"
                type="email"
                name="email"
                autocomplete="email"
                required
                aria-required="true"
                aria-describedby="<?php echo esc_attr($email_id); ?>-error"
                data-wp-bind--value="context.email"
                data-wp-bind--aria-invalid="state.hasEmailError"
                data-wp-bind--disabled="context.isSubmitting"
                data-wp-on--input="actions.updateEmail"
            />
            <p
                class="contact-form__error"
                id="<?php echo esc_attr($email_id); ?>-error"
                data-wp-text="context.errors.email"
                data-wp-bind--hidden="!state.hasEmailError"
            ></p>
        </div>

        <div class="contact-form__field">
            <label class="contact-form__label" for="<?php echo esc_attr($message_id); ?>">
                <?php esc_html_e('Message', 'earthbound'); ?>
                <span class="contact-form__required" aria-hidden="true">*</span>
                <span class="screen-reader-text"><?php esc_html_e('(required)', 'earthbound'); ?></span>
            </label>
            <textarea
                class="contact-form__input contact-form__textarea"
                id="<?php echo esc_attr($message_id); ?>"
                name="message"
                rows="8"
                required
                aria-required="true"
                aria-describedby="<?php echo esc_attr($message_id); ?>-error"
                data-wp-bind--aria-invalid="state.hasMessageError"
                data-wp-bind--disabled="context.isSubmitting"
                data-wp-on--input="actions.updateMessage"
            ></textarea>
            <p
                class="contact-form__error"
                id="<?php echo esc_attr($message_id); ?>-error"
                data-wp-text="context.errors.message"
                data-wp-bind--hidden="!state.hasMessageError"
            ></p>
        </div>

        <?php /* Honeypot. Hidden from people and from assistive technology; only bots fill it in. */ ?>
        <div class="contact-form__trap" aria-hidden="true">
            <label for="<?php echo esc_attr($website_id); ?>">
                <?php esc_html_e('Leave this field empty', 'earthbound'); ?>
            </label>
            <input
                id="<?php echo esc_attr($website_id); ?>"
                type="text"
                name="website"
                tabindex="-1"
                autocomplete="off"
                data-wp-bind--value="context.website"
                data-wp-on--input="actions.updateWebsite"
            />
        </div>

        <div class="contact-form__actions">
            <button
                class="contact-form__submit"
                type="submit"
                data-wp-bind--disabled="context.isSubmitting"
                data-wp-bind--aria-busy="context.isSubmitting"
            >
                <span data-wp-text="state.buttonText"><?php echo esc_html($submit_label); ?></span>
            </button>
        </div>

        <p
            class="contact-form__form-error"
            role="alert"
            data-wp-text="context.formError"
            data-wp-bind--hidden="!state.hasFormError"
        ></p>

    </form>

    <div
        class="contact-form__success"
        role="status"
        tabindex="-1"
        data-wp-text="context.successMessage"
        data-wp-bind--hidden="!state.isSent"
    ></div>

    <div
        class="screen-reader-text"
        aria-live="polite"
        aria-atomic="true"
        data-wp-text="state.statusMessage"
    ></div>

    <noscript>
        <p class="contact-form__noscript">
            <?php esc_html_e('This form needs JavaScript to send your message. Please enable it and reload the page.', 'earthbound'); ?>
        </p>
    </noscript>

</div>
