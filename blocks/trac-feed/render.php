<?php
/**
 * Trac Feed block render template.
 *
 * @package Earthbound
 * @since 1.0.0
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

declare(strict_types=1);

$per_page = $attributes['perPage'] ?? 10;

// Fetch initial data.
$request = new WP_REST_Request('GET', '/earthbound/v1/trac-activity');
$request->set_param('page', 1);
$request->set_param('per_page', $per_page);

$response = rest_do_request($request);
$data = $response->get_data();

$items = $data['items'] ?? array();
$total = $data['total'] ?? 0;
$has_more = count($items) < $total;

$context = array(
    'items'       => $items,
    'isLoading'   => false,
    'currentPage' => 1,
    'totalItems'  => $total,
    'perPage'     => $per_page,
    'error'       => null,
);

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class'               => 'trac-feed',
        'data-wp-interactive' => 'earthbound/trac-feed',
        'data-wp-context'     => wp_json_encode($context),
    )
);
?>

<div <?php echo $wrapper_attributes; ?>>
    <ul class="trac-feed__list" role="list">
        <?php foreach ($items as $item) : ?>
            <li class="trac-feed__item feed-item">
                <h4 class="trac-feed__title feed-item__title">
                    <a
                        href="<?php echo esc_url($item['url']); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <?php echo esc_html($item['title']); ?>
                        <span class="screen-reader-text">
                            <?php esc_html_e('(opens in new tab)', 'earthbound'); ?>
                        </span>
                    </a>
                </h4>
                <div class="trac-feed__meta feed-item__meta">
                    <?php if (!empty($item['ticket_id'])) : ?>
                        <span class="trac-feed__ticket">
                            #<?php echo esc_html($item['ticket_id']); ?>
                        </span>
                        <span class="trac-feed__separator" aria-hidden="true">&middot;</span>
                    <?php endif; ?>
                    <?php if (!empty($item['date'])) : ?>
                        <time
                            class="trac-feed__date"
                            datetime="<?php echo esc_attr($item['date']); ?>"
                        >
                            <?php
                            echo esc_html(
                                date_i18n(
                                    get_option('date_format'),
                                    strtotime($item['date'])
                                )
                            );
                            ?>
                        </time>
                    <?php endif; ?>
                </div>
                <?php if (!empty($item['description'])) : ?>
                    <p class="trac-feed__description">
                        <?php echo esc_html(wp_trim_words($item['description'], 20, '...')); ?>
                    </p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (empty($items)) : ?>
        <p class="trac-feed__empty">
            <?php esc_html_e('No Trac activity found.', 'earthbound'); ?>
        </p>
    <?php endif; ?>

    <?php if ($has_more) : ?>
        <div class="trac-feed__actions">
            <button
                type="button"
                class="trac-feed__load-more load-more-button"
                data-wp-on--click="actions.loadMore"
                data-wp-on--keydown="actions.handleKeydown"
                data-wp-bind--disabled="state.isLoading"
                data-wp-bind--aria-busy="state.isLoading"
                data-wp-bind--hidden="!state.hasMore"
            >
                <span data-wp-text="state.buttonText">
                    <?php esc_html_e('Load More', 'earthbound'); ?>
                </span>
            </button>
        </div>
    <?php endif; ?>

    <div
        aria-live="polite"
        aria-atomic="true"
        class="screen-reader-text"
        data-wp-text="state.statusMessage"
    ></div>

    <p
        class="trac-feed__error"
        data-wp-bind--hidden="!state.error"
        data-wp-text="state.error"
        role="alert"
    ></p>
</div>
