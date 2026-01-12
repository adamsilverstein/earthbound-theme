<?php
/**
 * GitHub Feed block render template.
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
$request = new WP_REST_Request('GET', '/earthbound/v1/github-activity');
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
        'class'               => 'github-feed',
        'data-wp-interactive' => 'earthbound/github-feed',
        'data-wp-context'     => wp_json_encode($context),
    )
);
?>

<div <?php echo $wrapper_attributes; ?>>
    <ul class="github-feed__list" role="list" data-wp-watch="callbacks.updateList">
        <?php foreach ($items as $item) : ?>
            <li class="github-feed__item feed-item">
                <h4 class="github-feed__title feed-item__title">
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
                <div class="github-feed__meta feed-item__meta">
                    <span class="github-feed__repo">
                        <?php echo esc_html($item['repo']); ?>
                    </span>
                    <span class="github-feed__separator" aria-hidden="true">&middot;</span>
                    <span class="github-feed__state github-feed__state--<?php echo esc_attr($item['state']); ?>">
                        <?php echo esc_html(ucfirst($item['state'])); ?>
                    </span>
                    <?php if (!empty($item['closed_at'])) : ?>
                        <span class="github-feed__separator" aria-hidden="true">&middot;</span>
                        <time
                            class="github-feed__date"
                            datetime="<?php echo esc_attr($item['closed_at']); ?>"
                        >
                            <?php
                            echo esc_html(
                                date_i18n(
                                    get_option('date_format'),
                                    strtotime($item['closed_at'])
                                )
                            );
                            ?>
                        </time>
                    <?php endif; ?>
                </div>
                <?php if (!empty($item['labels'])) : ?>
                    <div class="github-feed__labels">
                        <?php foreach ($item['labels'] as $label) : ?>
                            <span class="github-feed__label feed-item__label">
                                <?php echo esc_html($label); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (empty($items)) : ?>
        <p class="github-feed__empty" data-wp-bind--hidden="state.hasItems">
            <?php esc_html_e('No GitHub activity found.', 'earthbound'); ?>
        </p>
    <?php endif; ?>

    <?php if ($has_more) : ?>
        <div class="github-feed__actions">
            <button
                type="button"
                class="github-feed__load-more load-more-button"
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
        class="github-feed__error"
        data-wp-bind--hidden="!state.error"
        data-wp-text="state.error"
        role="alert"
    ></p>
</div>
