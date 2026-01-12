<?php
/**
 * Block registration functionality.
 *
 * @package Earthbound
 * @since 1.0.0
 */

declare(strict_types=1);

/**
 * Register custom blocks.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_register_blocks(): void {
    $blocks = array(
        'navigation',
        'project-card',
        'github-feed',
        'trac-feed',
    );

    foreach ($blocks as $block) {
        $block_path = get_template_directory() . '/blocks/' . $block;

        if (file_exists($block_path . '/block.json')) {
            register_block_type($block_path);
        }
    }
}
add_action('init', 'earthbound_register_blocks');

/**
 * Enqueue block editor assets.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_enqueue_block_editor_assets(): void {
    wp_enqueue_style(
        'earthbound-editor-styles',
        get_template_directory_uri() . '/assets/css/editor.css',
        array(),
        EARTHBOUND_VERSION
    );
}
add_action('enqueue_block_editor_assets', 'earthbound_enqueue_block_editor_assets');

/**
 * Register block categories.
 *
 * @since 1.0.0
 * @param array $categories Existing block categories.
 * @return array Modified block categories.
 */
function earthbound_block_categories(array $categories): array {
    return array_merge(
        array(
            array(
                'slug'  => 'earthbound',
                'title' => esc_html__('Earthbound', 'earthbound'),
                'icon'  => 'portfolio',
            ),
        ),
        $categories
    );
}
add_filter('block_categories_all', 'earthbound_block_categories');
