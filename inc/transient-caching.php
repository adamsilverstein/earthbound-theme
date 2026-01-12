<?php
/**
 * Transient caching functionality.
 *
 * @package Earthbound
 * @since 1.0.0
 */

declare(strict_types=1);

/**
 * Clear all Earthbound transients.
 *
 * @since 1.0.0
 * @return int Number of transients deleted.
 */
function earthbound_clear_all_transients(): int {
    global $wpdb;

    $count = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            '_transient_earthbound_%',
            '_transient_timeout_earthbound_%'
        )
    );

    return (int) $count;
}

/**
 * Clear GitHub transients.
 *
 * @since 1.0.0
 * @return int Number of transients deleted.
 */
function earthbound_clear_github_transients(): int {
    global $wpdb;

    $count = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            '_transient_earthbound_github_%',
            '_transient_timeout_earthbound_github_%'
        )
    );

    return (int) $count;
}

/**
 * Clear Trac transients.
 *
 * @since 1.0.0
 * @return int Number of transients deleted.
 */
function earthbound_clear_trac_transients(): int {
    global $wpdb;

    $count = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            '_transient_earthbound_trac_%',
            '_transient_timeout_earthbound_trac_%'
        )
    );

    return (int) $count;
}

/**
 * Add admin bar menu for cache clearing.
 *
 * @since 1.0.0
 * @param WP_Admin_Bar $admin_bar Admin bar object.
 * @return void
 */
function earthbound_admin_bar_cache_menu(WP_Admin_Bar $admin_bar): void {
    if (! current_user_can('manage_options')) {
        return;
    }

    $admin_bar->add_node(
        array(
            'id'    => 'earthbound-cache',
            'title' => esc_html__('Earthbound Cache', 'earthbound'),
            'href'  => '#',
        )
    );

    $admin_bar->add_node(
        array(
            'id'     => 'earthbound-clear-github-cache',
            'parent' => 'earthbound-cache',
            'title'  => esc_html__('Clear GitHub Cache', 'earthbound'),
            'href'   => wp_nonce_url(
                admin_url('admin-post.php?action=earthbound_clear_github_cache'),
                'earthbound_clear_cache'
            ),
        )
    );

    $admin_bar->add_node(
        array(
            'id'     => 'earthbound-clear-trac-cache',
            'parent' => 'earthbound-cache',
            'title'  => esc_html__('Clear Trac Cache', 'earthbound'),
            'href'   => wp_nonce_url(
                admin_url('admin-post.php?action=earthbound_clear_trac_cache'),
                'earthbound_clear_cache'
            ),
        )
    );

    $admin_bar->add_node(
        array(
            'id'     => 'earthbound-clear-all-cache',
            'parent' => 'earthbound-cache',
            'title'  => esc_html__('Clear All Cache', 'earthbound'),
            'href'   => wp_nonce_url(
                admin_url('admin-post.php?action=earthbound_clear_all_cache'),
                'earthbound_clear_cache'
            ),
        )
    );
}
add_action('admin_bar_menu', 'earthbound_admin_bar_cache_menu', 100);

/**
 * Handle GitHub cache clear action.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_handle_clear_github_cache(): void {
    if (! current_user_can('manage_options') || ! check_admin_referer('earthbound_clear_cache')) {
        wp_die(esc_html__('Unauthorized', 'earthbound'));
    }

    $count = earthbound_clear_github_transients();

    wp_safe_redirect(
        add_query_arg(
            array(
                'earthbound_cache_cleared' => 'github',
                'count'                    => $count,
            ),
            wp_get_referer() ?: admin_url()
        )
    );
    exit;
}
add_action('admin_post_earthbound_clear_github_cache', 'earthbound_handle_clear_github_cache');

/**
 * Handle Trac cache clear action.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_handle_clear_trac_cache(): void {
    if (! current_user_can('manage_options') || ! check_admin_referer('earthbound_clear_cache')) {
        wp_die(esc_html__('Unauthorized', 'earthbound'));
    }

    $count = earthbound_clear_trac_transients();

    wp_safe_redirect(
        add_query_arg(
            array(
                'earthbound_cache_cleared' => 'trac',
                'count'                    => $count,
            ),
            wp_get_referer() ?: admin_url()
        )
    );
    exit;
}
add_action('admin_post_earthbound_clear_trac_cache', 'earthbound_handle_clear_trac_cache');

/**
 * Handle all cache clear action.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_handle_clear_all_cache(): void {
    if (! current_user_can('manage_options') || ! check_admin_referer('earthbound_clear_cache')) {
        wp_die(esc_html__('Unauthorized', 'earthbound'));
    }

    $count = earthbound_clear_all_transients();

    wp_safe_redirect(
        add_query_arg(
            array(
                'earthbound_cache_cleared' => 'all',
                'count'                    => $count,
            ),
            wp_get_referer() ?: admin_url()
        )
    );
    exit;
}
add_action('admin_post_earthbound_clear_all_cache', 'earthbound_handle_clear_all_cache');

/**
 * Show admin notice after cache clear.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_cache_clear_notice(): void {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if (! isset($_GET['earthbound_cache_cleared'])) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $type  = sanitize_text_field(wp_unslash($_GET['earthbound_cache_cleared']));
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $count = isset($_GET['count']) ? absint($_GET['count']) : 0;

    $message = sprintf(
        /* translators: 1: cache type, 2: number of items cleared */
        esc_html__('Earthbound %1$s cache cleared (%2$d items).', 'earthbound'),
        $type,
        $count
    );

    printf(
        '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
        esc_html($message)
    );
}
add_action('admin_notices', 'earthbound_cache_clear_notice');
