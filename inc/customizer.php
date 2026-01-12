<?php
/**
 * Theme Customizer settings.
 *
 * @package Earthbound
 * @since 1.0.0
 */

declare(strict_types=1);

/**
 * Register customizer settings.
 *
 * @since 1.0.0
 * @param WP_Customize_Manager $wp_customize Customizer object.
 * @return void
 */
function earthbound_customize_register(WP_Customize_Manager $wp_customize): void {
    // Add Earthbound Settings section.
    $wp_customize->add_section(
        'earthbound_settings',
        array(
            'title'       => esc_html__('Earthbound Settings', 'earthbound'),
            'description' => esc_html__('Configure API integrations and caching.', 'earthbound'),
            'priority'    => 30,
        )
    );

    // GitHub Username setting.
    $wp_customize->add_setting(
        'earthbound_github_username',
        array(
            'default'           => 'adamsilverstein',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'earthbound_github_username',
        array(
            'label'       => esc_html__('GitHub Username', 'earthbound'),
            'description' => esc_html__('Your GitHub username for the activity feed.', 'earthbound'),
            'section'     => 'earthbound_settings',
            'type'        => 'text',
        )
    );

    // Trac Username setting.
    $wp_customize->add_setting(
        'earthbound_trac_username',
        array(
            'default'           => 'adamsilverstein',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'earthbound_trac_username',
        array(
            'label'       => esc_html__('WordPress Trac Username', 'earthbound'),
            'description' => esc_html__('Your WordPress.org username for Trac activity.', 'earthbound'),
            'section'     => 'earthbound_settings',
            'type'        => 'text',
        )
    );

    // Cache Duration setting.
    $wp_customize->add_setting(
        'earthbound_cache_duration',
        array(
            'default'           => 3600,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'earthbound_cache_duration',
        array(
            'label'       => esc_html__('API Cache Duration', 'earthbound'),
            'description' => esc_html__('How long to cache API responses (in seconds). Default: 3600 (1 hour).', 'earthbound'),
            'section'     => 'earthbound_settings',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 300,
                'max'  => 86400,
                'step' => 300,
            ),
        )
    );

    // GitHub Token setting (optional, for higher rate limits).
    $wp_customize->add_setting(
        'earthbound_github_token',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'earthbound_github_token',
        array(
            'label'       => esc_html__('GitHub Personal Access Token', 'earthbound'),
            'description' => esc_html__('Optional. Provide a token to increase GitHub API rate limits.', 'earthbound'),
            'section'     => 'earthbound_settings',
            'type'        => 'password',
        )
    );

    // Items per page setting.
    $wp_customize->add_setting(
        'earthbound_items_per_page',
        array(
            'default'           => 10,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'earthbound_items_per_page',
        array(
            'label'       => esc_html__('Items Per Page', 'earthbound'),
            'description' => esc_html__('Number of items to show in feeds.', 'earthbound'),
            'section'     => 'earthbound_settings',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 5,
                'max'  => 50,
                'step' => 5,
            ),
        )
    );
}
add_action('customize_register', 'earthbound_customize_register');

/**
 * Enqueue customizer preview scripts.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_customize_preview_js(): void {
    wp_enqueue_script(
        'earthbound-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        array('customize-preview'),
        EARTHBOUND_VERSION,
        true
    );
}
add_action('customize_preview_init', 'earthbound_customize_preview_js');
