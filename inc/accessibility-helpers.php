<?php
/**
 * Accessibility helper functions.
 *
 * @package Earthbound
 * @since 1.0.0
 */

declare(strict_types=1);

/**
 * Add skip link to the beginning of the page.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_skip_link(): void {
    echo '<a class="skip-link screen-reader-text" href="#main-content">' .
         esc_html__('Skip to main content', 'earthbound') .
         '</a>';
}
add_action('wp_body_open', 'earthbound_skip_link', 5);

/**
 * Generate screen reader text span.
 *
 * @since 1.0.0
 * @param string $text Text for screen readers.
 * @return string HTML span element.
 */
function earthbound_screen_reader_text(string $text): string {
    return sprintf(
        '<span class="screen-reader-text">%s</span>',
        esc_html($text)
    );
}

/**
 * Generate accessible icon button.
 *
 * @since 1.0.0
 * @param array $args Button arguments.
 * @return string HTML button element.
 */
function earthbound_icon_button(array $args): string {
    $defaults = array(
        'icon'       => '',
        'label'      => '',
        'class'      => '',
        'attributes' => array(),
    );

    $args = wp_parse_args($args, $defaults);

    $attributes_str = '';
    foreach ($args['attributes'] as $key => $value) {
        $attributes_str .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
    }

    return sprintf(
        '<button class="%1$s" type="button"%2$s>
            <span class="icon" aria-hidden="true">%3$s</span>
            <span class="screen-reader-text">%4$s</span>
        </button>',
        esc_attr($args['class']),
        $attributes_str,
        $args['icon'],
        esc_html($args['label'])
    );
}

/**
 * Generate accessible external link.
 *
 * @since 1.0.0
 * @param string $url  Link URL.
 * @param string $text Link text.
 * @param array  $args Additional arguments.
 * @return string HTML anchor element.
 */
function earthbound_external_link(string $url, string $text, array $args = array()): string {
    $defaults = array(
        'class'      => '',
        'new_window' => true,
    );

    $args = wp_parse_args($args, $defaults);

    $target_attrs = '';
    $sr_text      = '';

    if ($args['new_window']) {
        $target_attrs = ' target="_blank" rel="noopener noreferrer"';
        $sr_text      = earthbound_screen_reader_text(
            /* translators: accessibility text */
            __('(opens in new tab)', 'earthbound')
        );
    }

    return sprintf(
        '<a href="%1$s" class="%2$s"%3$s>%4$s%5$s</a>',
        esc_url($url),
        esc_attr($args['class']),
        $target_attrs,
        esc_html($text),
        $sr_text
    );
}

/**
 * Generate live region for dynamic content updates.
 *
 * @since 1.0.0
 * @param string $id       Unique ID for the region.
 * @param string $politeness ARIA live politeness setting (polite or assertive).
 * @return string HTML div element.
 */
function earthbound_live_region(string $id, string $politeness = 'polite'): string {
    $valid_politeness = in_array($politeness, array('polite', 'assertive'), true)
        ? $politeness
        : 'polite';

    return sprintf(
        '<div id="%1$s" aria-live="%2$s" aria-atomic="true" class="screen-reader-text"></div>',
        esc_attr($id),
        esc_attr($valid_politeness)
    );
}

/**
 * Add ARIA attributes for loading states.
 *
 * @since 1.0.0
 * @param bool $is_loading Whether content is loading.
 * @return string ARIA attributes string.
 */
function earthbound_loading_attrs(bool $is_loading): string {
    return sprintf(
        'aria-busy="%s"',
        $is_loading ? 'true' : 'false'
    );
}

/**
 * Generate heading with appropriate level.
 *
 * @since 1.0.0
 * @param int    $level Heading level (1-6).
 * @param string $text  Heading text.
 * @param array  $args  Additional arguments.
 * @return string HTML heading element.
 */
function earthbound_heading(int $level, string $text, array $args = array()): string {
    $level = max(1, min(6, $level));

    $defaults = array(
        'class' => '',
        'id'    => '',
    );

    $args = wp_parse_args($args, $defaults);

    $id_attr    = $args['id'] ? sprintf(' id="%s"', esc_attr($args['id'])) : '';
    $class_attr = $args['class'] ? sprintf(' class="%s"', esc_attr($args['class'])) : '';

    return sprintf(
        '<h%1$d%2$s%3$s>%4$s</h%1$d>',
        $level,
        $id_attr,
        $class_attr,
        esc_html($text)
    );
}

/**
 * Check if user prefers reduced motion.
 *
 * @since 1.0.0
 * @return bool Whether user prefers reduced motion.
 */
function earthbound_prefers_reduced_motion(): bool {
    // This is determined via CSS media query, but we can check
    // a user preference stored in a cookie if needed.
    return isset($_COOKIE['prefers-reduced-motion']) &&
           'true' === $_COOKIE['prefers-reduced-motion'];
}

/**
 * Add prefers-reduced-motion class to body.
 *
 * @since 1.0.0
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function earthbound_reduced_motion_body_class(array $classes): array {
    if (earthbound_prefers_reduced_motion()) {
        $classes[] = 'reduce-motion';
    }

    return $classes;
}
add_filter('body_class', 'earthbound_reduced_motion_body_class');
