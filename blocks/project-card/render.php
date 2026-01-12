<?php
/**
 * Project Card block render template.
 *
 * @package Earthbound
 * @since 1.0.0
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

declare(strict_types=1);

$project_id = $attributes['projectId'] ?? 0;

if ($project_id === 0) {
    // If no specific project, try to get from context (e.g., in a query loop).
    $project_id = get_the_ID();
}

if (!$project_id || get_post_type($project_id) !== 'eb_project') {
    return;
}

$project = get_post($project_id);
if (!$project) {
    return;
}

$project_url = get_post_meta($project_id, 'project_url', true);
$project_repo = get_post_meta($project_id, 'project_repo', true);
$project_status = get_post_meta($project_id, 'project_status', true) ?: 'active';
$project_year = get_post_meta($project_id, 'project_year', true);
$thumbnail_id = get_post_thumbnail_id($project_id);
$permalink = get_permalink($project_id);
$excerpt = get_the_excerpt($project);

$status_labels = array(
    'active'      => __('Active', 'earthbound'),
    'completed'   => __('Completed', 'earthbound'),
    'archived'    => __('Archived', 'earthbound'),
    'in-progress' => __('In Progress', 'earthbound'),
);

$status_label = $status_labels[$project_status] ?? $status_labels['active'];

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class' => 'project-card groovy-card',
    )
);
?>

<article <?php echo $wrapper_attributes; ?>>
    <?php if ($thumbnail_id) : ?>
        <a href="<?php echo esc_url($permalink); ?>" class="project-card__image-link">
            <?php
            echo wp_get_attachment_image(
                $thumbnail_id,
                'earthbound-card',
                false,
                array(
                    'class' => 'project-card__image',
                    'loading' => 'lazy',
                )
            );
            ?>
        </a>
    <?php endif; ?>

    <div class="project-card__content">
        <div class="project-card__meta">
            <span class="project-card__status project-card__status--<?php echo esc_attr($project_status); ?>">
                <?php echo esc_html($status_label); ?>
            </span>
            <?php if ($project_year) : ?>
                <span class="project-card__year"><?php echo esc_html($project_year); ?></span>
            <?php endif; ?>
        </div>

        <h3 class="project-card__title">
            <a href="<?php echo esc_url($permalink); ?>">
                <?php echo esc_html($project->post_title); ?>
            </a>
        </h3>

        <?php if ($excerpt) : ?>
            <p class="project-card__excerpt"><?php echo esc_html($excerpt); ?></p>
        <?php endif; ?>

        <div class="project-card__links">
            <?php if ($project_url) : ?>
                <a
                    href="<?php echo esc_url($project_url); ?>"
                    class="project-card__link project-card__link--live"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <?php esc_html_e('View Project', 'earthbound'); ?>
                    <span class="screen-reader-text">
                        <?php esc_html_e('(opens in new tab)', 'earthbound'); ?>
                    </span>
                </a>
            <?php endif; ?>

            <?php if ($project_repo) : ?>
                <a
                    href="<?php echo esc_url($project_repo); ?>"
                    class="project-card__link project-card__link--repo"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <?php esc_html_e('View Code', 'earthbound'); ?>
                    <span class="screen-reader-text">
                        <?php esc_html_e('(opens in new tab)', 'earthbound'); ?>
                    </span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>
