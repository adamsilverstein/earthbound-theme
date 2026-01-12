<?php
/**
 * REST API endpoints for GitHub and Trac feeds.
 *
 * @package Earthbound
 * @since 1.0.0
 */

declare(strict_types=1);

/**
 * Register REST API endpoints.
 *
 * @since 1.0.0
 * @return void
 */
function earthbound_register_rest_endpoints(): void {
    // GitHub activity endpoint.
    register_rest_route(
        'earthbound/v1',
        '/github-activity',
        array(
            'methods'             => 'GET',
            'callback'            => 'earthbound_get_github_activity',
            'permission_callback' => '__return_true',
            'args'                => array(
                'page'     => array(
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                ),
                'per_page' => array(
                    'default'           => 10,
                    'sanitize_callback' => 'absint',
                ),
            ),
        )
    );

    // Trac activity endpoint.
    register_rest_route(
        'earthbound/v1',
        '/trac-activity',
        array(
            'methods'             => 'GET',
            'callback'            => 'earthbound_get_trac_activity',
            'permission_callback' => '__return_true',
            'args'                => array(
                'page'     => array(
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                ),
                'per_page' => array(
                    'default'           => 10,
                    'sanitize_callback' => 'absint',
                ),
            ),
        )
    );
}
add_action('rest_api_init', 'earthbound_register_rest_endpoints');

/**
 * Get GitHub activity.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request REST request object.
 * @return WP_REST_Response|WP_Error REST response or error.
 */
function earthbound_get_github_activity(WP_REST_Request $request): WP_REST_Response|WP_Error {
    $page     = $request->get_param('page');
    $per_page = min($request->get_param('per_page'), 100);

    $cache_key = "earthbound_github_{$page}_{$per_page}";
    $cached    = get_transient($cache_key);

    if (false !== $cached) {
        return rest_ensure_response($cached);
    }

    $username = get_theme_mod('earthbound_github_username', 'adamsilverstein');

    // Fetch from GitHub API.
    $response = wp_remote_get(
        'https://api.github.com/search/issues?' . http_build_query(
            array(
                'q'        => "author:{$username} type:issue state:closed",
                'sort'     => 'updated',
                'order'    => 'desc',
                'per_page' => $per_page,
                'page'     => $page,
            )
        ),
        array(
            'headers' => array(
                'Accept'     => 'application/vnd.github.v3+json',
                'User-Agent' => 'Earthbound-Theme',
            ),
            'timeout' => 15,
        )
    );

    if (is_wp_error($response)) {
        return new WP_Error(
            'github_error',
            esc_html__('Failed to fetch GitHub data', 'earthbound'),
            array('status' => 500)
        );
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (! is_array($body) || ! isset($body['items'])) {
        return new WP_Error(
            'github_parse_error',
            esc_html__('Invalid response from GitHub', 'earthbound'),
            array('status' => 500)
        );
    }

    $formatted = array_map(
        function (array $issue): array {
            return array(
                'id'         => $issue['id'],
                'title'      => $issue['title'],
                'url'        => $issue['html_url'],
                'repo'       => earthbound_extract_repo_name($issue['repository_url'] ?? ''),
                'state'      => $issue['state'],
                'created_at' => $issue['created_at'],
                'closed_at'  => $issue['closed_at'] ?? null,
                'labels'     => array_map(
                    fn(array $label): string => $label['name'],
                    $issue['labels'] ?? array()
                ),
            );
        },
        $body['items']
    );

    $result = array(
        'items'    => $formatted,
        'total'    => $body['total_count'] ?? 0,
        'page'     => $page,
        'per_page' => $per_page,
    );

    $cache_duration = (int) get_theme_mod('earthbound_cache_duration', HOUR_IN_SECONDS);
    set_transient($cache_key, $result, $cache_duration);

    return rest_ensure_response($result);
}

/**
 * Extract repository name from GitHub API URL.
 *
 * @since 1.0.0
 * @param string $repository_url GitHub repository URL.
 * @return string Repository name (owner/repo format).
 */
function earthbound_extract_repo_name(string $repository_url): string {
    if (empty($repository_url)) {
        return '';
    }

    $parts = explode('/', rtrim($repository_url, '/'));

    if (count($parts) >= 2) {
        return $parts[count($parts) - 2] . '/' . $parts[count($parts) - 1];
    }

    return '';
}

/**
 * Get Trac activity.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request REST request object.
 * @return WP_REST_Response|WP_Error REST response or error.
 */
function earthbound_get_trac_activity(WP_REST_Request $request): WP_REST_Response|WP_Error {
    $page     = $request->get_param('page');
    $per_page = min($request->get_param('per_page'), 100);

    $cache_key = "earthbound_trac_{$page}_{$per_page}";
    $cached    = get_transient($cache_key);

    if (false !== $cached) {
        return rest_ensure_response($cached);
    }

    $username = get_theme_mod('earthbound_trac_username', 'adamsilverstein');

    // Fetch from WordPress Trac RSS feed.
    $feed_url = 'https://core.trac.wordpress.org/query?' . http_build_query(
        array(
            'reporter' => $username,
            'or'       => '',
            'owner'    => $username,
            'format'   => 'rss',
            'order'    => 'changetime',
            'desc'     => '1',
        )
    );

    $response = wp_remote_get(
        $feed_url,
        array(
            'timeout' => 15,
        )
    );

    if (is_wp_error($response)) {
        return new WP_Error(
            'trac_error',
            esc_html__('Failed to fetch Trac data', 'earthbound'),
            array('status' => 500)
        );
    }

    $body = wp_remote_retrieve_body($response);

    // Suppress XML errors.
    $use_errors = libxml_use_internal_errors(true);
    $xml        = simplexml_load_string($body);
    libxml_use_internal_errors($use_errors);

    if (false === $xml || ! isset($xml->channel->item)) {
        return new WP_Error(
            'trac_parse_error',
            esc_html__('Invalid response from Trac', 'earthbound'),
            array('status' => 500)
        );
    }

    $items     = array();
    $counter   = 0;
    $start_idx = ($page - 1) * $per_page;

    foreach ($xml->channel->item as $item) {
        if ($counter >= $start_idx + $per_page) {
            break;
        }

        if ($counter >= $start_idx) {
            $items[] = array(
                'title'       => (string) $item->title,
                'url'         => (string) $item->link,
                'description' => wp_strip_all_tags((string) $item->description),
                'date'        => (string) $item->pubDate,
                'ticket_id'   => earthbound_extract_trac_id((string) $item->link),
            );
        }

        $counter++;
    }

    $result = array(
        'items'    => $items,
        'total'    => $counter,
        'page'     => $page,
        'per_page' => $per_page,
    );

    $cache_duration = (int) get_theme_mod('earthbound_cache_duration', HOUR_IN_SECONDS * 2);
    set_transient($cache_key, $result, $cache_duration);

    return rest_ensure_response($result);
}

/**
 * Extract ticket ID from Trac URL.
 *
 * @since 1.0.0
 * @param string $url Trac ticket URL.
 * @return string Ticket ID or empty string.
 */
function earthbound_extract_trac_id(string $url): string {
    if (preg_match('/ticket\/(\d+)/', $url, $matches)) {
        return $matches[1];
    }

    return '';
}
