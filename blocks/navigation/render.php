<?php
/**
 * Navigation block render template.
 *
 * @package Earthbound
 * @since 1.0.0
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

declare(strict_types=1);

$context = array(
    'isOpen'        => false,
    'activeSubmenu' => null,
);

$menu_slug = $attributes['menuSlug'] ?? 'primary';
$menu_items = earthbound_get_menu_items($menu_slug);

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class'                      => 'earthbound-nav',
        'data-wp-interactive'        => 'earthbound/navigation',
        'data-wp-context'            => wp_json_encode($context),
        'data-wp-on--keydown'        => 'actions.handleKeydown',
    )
);
?>

<nav <?php echo $wrapper_attributes; ?> aria-label="<?php esc_attr_e('Primary navigation', 'earthbound'); ?>">
    <a href="#main-content" class="skip-link">
        <?php esc_html_e('Skip to main content', 'earthbound'); ?>
    </a>

    <button
        class="nav-toggle"
        type="button"
        data-wp-on--click="actions.toggleMenu"
        data-wp-bind--aria-expanded="state.isMenuOpen"
        aria-controls="nav-menu"
    >
        <span class="nav-toggle__icon" aria-hidden="true">
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
        </span>
        <span class="screen-reader-text">
            <?php esc_html_e('Toggle navigation', 'earthbound'); ?>
        </span>
    </button>

    <ul
        id="nav-menu"
        class="nav-menu"
        data-wp-class--is-open="state.isMenuOpen"
        role="menubar"
    >
        <?php foreach ($menu_items as $item) : ?>
            <?php if (!empty($item['children'])) : ?>
                <li
                    class="nav-menu__item nav-menu__item--has-children"
                    role="none"
                    data-submenu="<?php echo esc_attr($item['id']); ?>"
                >
                    <button
                        class="nav-menu__link nav-menu__link--parent"
                        role="menuitem"
                        aria-haspopup="true"
                        data-wp-bind--aria-expanded="state.isSubmenuOpen"
                        data-wp-on--click="actions.toggleSubmenu"
                        data-wp-on--keydown="actions.handleSubmenuKeydown"
                        data-submenu="<?php echo esc_attr($item['id']); ?>"
                    >
                        <?php echo esc_html($item['title']); ?>
                        <span class="nav-menu__arrow" aria-hidden="true"></span>
                    </button>
                    <ul
                        class="nav-submenu"
                        role="menu"
                        data-wp-class--is-open="state.isSubmenuActive"
                        aria-label="<?php echo esc_attr($item['title']); ?>"
                    >
                        <?php foreach ($item['children'] as $child) : ?>
                            <li class="nav-submenu__item" role="none">
                                <a
                                    href="<?php echo esc_url($child['url']); ?>"
                                    class="nav-submenu__link"
                                    role="menuitem"
                                >
                                    <?php echo esc_html($child['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php else : ?>
                <li class="nav-menu__item" role="none">
                    <a
                        href="<?php echo esc_url($item['url']); ?>"
                        class="nav-menu__link"
                        role="menuitem"
                        <?php echo $item['is_current'] ? 'aria-current="page"' : ''; ?>
                    >
                        <?php echo esc_html($item['title']); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <div
        aria-live="polite"
        aria-atomic="true"
        class="screen-reader-text"
        data-wp-text="state.statusMessage"
    ></div>
</nav>

<?php
/**
 * Get menu items for the navigation block.
 *
 * @param string $menu_slug Menu location slug.
 * @return array Menu items with children.
 */
function earthbound_get_menu_items(string $menu_slug): array {
    $locations = get_nav_menu_locations();

    if (!isset($locations[$menu_slug])) {
        return array();
    }

    $menu_id = $locations[$menu_slug];
    $menu_items = wp_get_nav_menu_items($menu_id);

    if (!$menu_items) {
        return array();
    }

    $current_url = home_url(add_query_arg(array(), $GLOBALS['wp']->request ?? ''));
    $items = array();
    $children = array();

    // Separate top-level and child items.
    foreach ($menu_items as $item) {
        $formatted_item = array(
            'id'         => $item->ID,
            'title'      => $item->title,
            'url'        => $item->url,
            'parent'     => (int) $item->menu_item_parent,
            'is_current' => $item->url === $current_url,
            'children'   => array(),
        );

        if ($formatted_item['parent'] === 0) {
            $items[$item->ID] = $formatted_item;
        } else {
            $children[$formatted_item['parent']][] = $formatted_item;
        }
    }

    // Attach children to parents.
    foreach ($children as $parent_id => $child_items) {
        if (isset($items[$parent_id])) {
            $items[$parent_id]['children'] = $child_items;
        }
    }

    return array_values($items);
}
