<?php
/**
 * Earthbound Theme functions and definitions
 *
 * @package Earthbound
 * @since 1.0.0
 */

declare(strict_types=1);

// Define theme version.
define('EARTHBOUND_VERSION', '1.0.0');

// Include required files.
require_once get_template_directory() . '/inc/block-registration.php';
require_once get_template_directory() . '/inc/api-endpoints.php';
require_once get_template_directory() . '/inc/transient-caching.php';
require_once get_template_directory() . '/inc/accessibility-helpers.php';
require_once get_template_directory() . '/inc/customizer.php';

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_setup(): void {
    // Add support for block styles.
    add_theme_support('wp-block-styles');

    // Add support for responsive embeds.
    add_theme_support('responsive-embeds');

    // Add support for editor styles.
    add_theme_support('editor-styles');

    // Enqueue editor styles.
    add_editor_style('style.css');

    // Add support for post thumbnails.
    add_theme_support('post-thumbnails');

    // Add custom image sizes.
    add_image_size('earthbound-card', 600, 400, true);
    add_image_size('earthbound-hero', 1920, 800, true);

    // Register navigation menus.
    register_nav_menus(
        array(
            'primary'   => esc_html__('Primary Menu', 'earthbound'),
            'footer'    => esc_html__('Footer Menu', 'earthbound'),
            'social'    => esc_html__('Social Menu', 'earthbound'),
        )
    );

    // Make theme available for translation.
    load_theme_textdomain('earthbound', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'earthbound_setup');

/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_scripts(): void {
    // Enqueue main stylesheet.
    wp_enqueue_style(
        'earthbound-style',
        get_stylesheet_uri(),
        array(),
        EARTHBOUND_VERSION
    );

    // Enqueue block styles.
    wp_enqueue_style(
        'earthbound-blocks',
        get_template_directory_uri() . '/assets/css/blocks.css',
        array(),
        EARTHBOUND_VERSION
    );
}
add_action('wp_enqueue_scripts', 'earthbound_scripts');

/**
 * Register block pattern categories.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_register_pattern_categories(): void {
    register_block_pattern_category(
        'earthbound',
        array(
            'label'       => esc_html__('Earthbound', 'earthbound'),
            'description' => esc_html__('Patterns included with the Earthbound theme.', 'earthbound'),
        )
    );

    register_block_pattern_category(
        'earthbound-hero',
        array(
            'label'       => esc_html__('Hero Sections', 'earthbound'),
            'description' => esc_html__('Hero section patterns for landing pages.', 'earthbound'),
        )
    );

    register_block_pattern_category(
        'earthbound-portfolio',
        array(
            'label'       => esc_html__('Portfolio', 'earthbound'),
            'description' => esc_html__('Portfolio and project display patterns.', 'earthbound'),
        )
    );
}
add_action('init', 'earthbound_register_pattern_categories');

/**
 * Register Projects custom post type.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_register_projects_cpt(): void {
    $labels = array(
        'name'                  => _x('Projects', 'Post Type General Name', 'earthbound'),
        'singular_name'         => _x('Project', 'Post Type Singular Name', 'earthbound'),
        'menu_name'             => esc_html__('Projects', 'earthbound'),
        'name_admin_bar'        => esc_html__('Project', 'earthbound'),
        'archives'              => esc_html__('Project Archives', 'earthbound'),
        'attributes'            => esc_html__('Project Attributes', 'earthbound'),
        'parent_item_colon'     => esc_html__('Parent Project:', 'earthbound'),
        'all_items'             => esc_html__('All Projects', 'earthbound'),
        'add_new_item'          => esc_html__('Add New Project', 'earthbound'),
        'add_new'               => esc_html__('Add New', 'earthbound'),
        'new_item'              => esc_html__('New Project', 'earthbound'),
        'edit_item'             => esc_html__('Edit Project', 'earthbound'),
        'update_item'           => esc_html__('Update Project', 'earthbound'),
        'view_item'             => esc_html__('View Project', 'earthbound'),
        'view_items'            => esc_html__('View Projects', 'earthbound'),
        'search_items'          => esc_html__('Search Project', 'earthbound'),
        'not_found'             => esc_html__('Not found', 'earthbound'),
        'not_found_in_trash'    => esc_html__('Not found in Trash', 'earthbound'),
        'featured_image'        => esc_html__('Featured Image', 'earthbound'),
        'set_featured_image'    => esc_html__('Set featured image', 'earthbound'),
        'remove_featured_image' => esc_html__('Remove featured image', 'earthbound'),
        'use_featured_image'    => esc_html__('Use as featured image', 'earthbound'),
        'insert_into_item'      => esc_html__('Insert into project', 'earthbound'),
        'uploaded_to_this_item' => esc_html__('Uploaded to this project', 'earthbound'),
        'items_list'            => esc_html__('Projects list', 'earthbound'),
        'items_list_navigation' => esc_html__('Projects list navigation', 'earthbound'),
        'filter_items_list'     => esc_html__('Filter projects list', 'earthbound'),
    );

    $args = array(
        'label'               => esc_html__('Project', 'earthbound'),
        'description'         => esc_html__('Portfolio projects', 'earthbound'),
        'labels'              => $labels,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'taxonomies'          => array('category', 'post_tag'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-portfolio',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
        'rewrite'             => array('slug' => 'projects'),
        'template'            => array(
            array('core/paragraph', array('placeholder' => esc_html__('Project description...', 'earthbound'))),
        ),
    );

    register_post_type('eb_project', $args);

    // Register custom fields for project metadata.
    register_post_meta(
        'eb_project',
        'project_url',
        array(
            'type'         => 'string',
            'single'       => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    register_post_meta(
        'eb_project',
        'project_repo',
        array(
            'type'         => 'string',
            'single'       => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    register_post_meta(
        'eb_project',
        'project_status',
        array(
            'type'         => 'string',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 'active',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    register_post_meta(
        'eb_project',
        'project_year',
        array(
            'type'         => 'string',
            'single'       => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
}
add_action('init', 'earthbound_register_projects_cpt');

/**
 * Register block styles.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_register_block_styles(): void {
    // Chunky button style.
    register_block_style(
        'core/button',
        array(
            'name'  => 'chunky',
            'label' => esc_html__('Chunky', 'earthbound'),
        )
    );

    // Groovy card style for groups.
    register_block_style(
        'core/group',
        array(
            'name'  => 'groovy-card',
            'label' => esc_html__('Groovy Card', 'earthbound'),
        )
    );

    // Retro image style.
    register_block_style(
        'core/image',
        array(
            'name'  => 'retro',
            'label' => esc_html__('Retro', 'earthbound'),
        )
    );

    // Newspaper rule separator style.
    register_block_style(
        'core/separator',
        array(
            'name'  => 'newspaper-rule',
            'label' => esc_html__('Newspaper Rule', 'earthbound'),
        )
    );
}
add_action('init', 'earthbound_register_block_styles');

/**
 * Add custom body classes.
 *
 * @since 1.0.0
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function earthbound_body_classes(array $classes): array {
    // Add a class for reduced motion preference.
    $classes[] = 'earthbound';

    if (is_singular('eb_project')) {
        $classes[] = 'single-project';
    }

    return $classes;
}
add_filter('body_class', 'earthbound_body_classes');

/**
 * Customize the excerpt length.
 *
 * @since 1.0.0
 * @param int $length Default excerpt length.
 * @return int Modified excerpt length.
 */
function earthbound_excerpt_length(int $length): int {
    return 25;
}
add_filter('excerpt_length', 'earthbound_excerpt_length');

/**
 * Customize the excerpt more string.
 *
 * @since 1.0.0
 * @param string $more Default more string.
 * @return string Modified more string.
 */
function earthbound_excerpt_more(string $more): string {
    return '&hellip;';
}
add_filter('excerpt_more', 'earthbound_excerpt_more');
