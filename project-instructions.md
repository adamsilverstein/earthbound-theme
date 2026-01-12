# Earthbound Theme Rebuild Plan

This is a comprehensive specification for rebuilding earthbound.com as a modern WordPress block theme. Hand this document to Claude Code in your empty repository to begin implementation.

---

## Project Overview

**Theme Name:** Earthbound  
**Theme Slug:** `earthbound`  
**Target WordPress Version:** 6.7+  
**PHP Minimum:** 8.1  
**Description:** A retro 70s-inspired personal portfolio theme with modern WordPress APIs

---

## 1. Architecture & File Structure

```
earthbound/
├── assets/
│   ├── fonts/
│   │   └── (self-hosted retro fonts)
│   ├── images/
│   │   └── (decorative elements, patterns)
│   └── js/
│       └── (compiled Interactivity API modules)
├── blocks/
│   ├── navigation/
│   │   ├── block.json
│   │   ├── render.php
│   │   ├── view.js (Interactivity API)
│   │   └── style.css
│   ├── project-card/
│   │   ├── block.json
│   │   ├── render.php
│   │   ├── view.js
│   │   └── style.css
│   ├── github-feed/
│   │   ├── block.json
│   │   ├── render.php
│   │   ├── view.js
│   │   └── style.css
│   └── trac-feed/
│       ├── block.json
│       ├── render.php
│       ├── view.js
│       └── style.css
├── inc/
│   ├── block-registration.php
│   ├── api-endpoints.php (REST API for GitHub/Trac)
│   ├── transient-caching.php
│   └── accessibility-helpers.php
├── parts/
│   ├── header.html
│   ├── footer.html
│   └── sidebar.html
├── patterns/
│   ├── hero.php
│   ├── my-work-section.php
│   ├── project-grid.php
│   └── about-section.php
├── styles/
│   ├── desert-sunset.json
│   ├── forest-grove.json
│   ├── ocean-tide.json
│   └── harvest-gold.json
├── templates/
│   ├── index.html
│   ├── front-page.html
│   ├── single.html
│   ├── page.html
│   ├── page-my-work.html
│   └── 404.html
├── functions.php
├── style.css
├── theme.json
└── readme.txt
```

---

## 2. Design System

### 2.1 Color Palettes

Create four switchable style variations in `/styles/`. Each should evoke 70s aesthetics with earth tones, warm colors, and muted pastels.

**Primary Palette: Harvest Gold (default)**
```json
{
  "primary": "#D4A03C",
  "secondary": "#8B4513",
  "tertiary": "#C17817",
  "background": "#FDF5E6",
  "background-alt": "#F5E6D3",
  "foreground": "#3D2914",
  "foreground-muted": "#6B5344",
  "accent": "#B8860B",
  "error": "#A0522D",
  "success": "#6B8E23"
}
```

**Desert Sunset**
```json
{
  "primary": "#E07B53",
  "secondary": "#8B5A2B",
  "tertiary": "#CD853F",
  "background": "#FFF8F0",
  "background-alt": "#FFE4C4",
  "foreground": "#4A3728",
  "foreground-muted": "#7D6B5D",
  "accent": "#D2691E",
  "error": "#B22222",
  "success": "#808000"
}
```

**Forest Grove**
```json
{
  "primary": "#6B8E23",
  "secondary": "#556B2F",
  "tertiary": "#8FBC8F",
  "background": "#F5F5DC",
  "background-alt": "#E8E4C9",
  "foreground": "#2F4F2F",
  "foreground-muted": "#4A5D4A",
  "accent": "#9ACD32",
  "error": "#8B0000",
  "success": "#228B22"
}
```

**Ocean Tide**
```json
{
  "primary": "#5F9EA0",
  "secondary": "#2F4F4F",
  "tertiary": "#20B2AA",
  "background": "#F0FFFF",
  "background-alt": "#E0EEEE",
  "foreground": "#1C3D3D",
  "foreground-muted": "#4A6B6B",
  "accent": "#48D1CC",
  "error": "#CD5C5C",
  "success": "#3CB371"
}
```

### 2.2 Typography

Use self-hosted fonts for performance and reliability. Suggested pairings (choose one set):

**Option A (Recommended):**
- Headings: **Righteous** or **Titan One** (Google Fonts, OFL)
- Body: **Nunito** or **Quicksand** (rounded, friendly)

**Option B:**
- Headings: **Abril Fatface** (display serif, 70s magazine feel)
- Body: **Source Sans 3**

**Option C:**
- Headings: **Cooper Hewitt** (geometric, free)
- Body: **Work Sans**

Define fluid typography in `theme.json`:
```json
{
  "typography": {
    "fluid": true,
    "fontSizes": [
      { "slug": "small", "size": "0.875rem", "fluid": { "min": "0.75rem", "max": "0.875rem" } },
      { "slug": "medium", "size": "1rem", "fluid": { "min": "0.875rem", "max": "1rem" } },
      { "slug": "large", "size": "1.25rem", "fluid": { "min": "1rem", "max": "1.25rem" } },
      { "slug": "x-large", "size": "1.75rem", "fluid": { "min": "1.25rem", "max": "1.75rem" } },
      { "slug": "xx-large", "size": "2.5rem", "fluid": { "min": "1.75rem", "max": "2.5rem" } },
      { "slug": "hero", "size": "4rem", "fluid": { "min": "2.5rem", "max": "4rem" } }
    ]
  }
}
```

### 2.3 Retro Design Elements

Incorporate these 70s-inspired visual elements:

1. **Rounded corners** everywhere (border-radius: 1rem or more)
2. **Subtle paper textures** via CSS (noise overlay or subtle pattern)
3. **Warm drop shadows** using accent colors (not pure black)
4. **Organic shapes** for decorative elements (blobs, waves)
5. **Chunky borders** on interactive elements
6. **Groovy dividers** between sections (wavy SVG patterns)
7. **Vintage photography treatment** (slight desaturation filter option)

---

## 3. Interactivity API Navigation

### 3.1 Navigation Block Structure

Create a custom navigation block that uses the Interactivity API for smooth, accessible interactions.

**Features:**
- Keyboard-navigable dropdown menus
- Mobile slide-out menu with focus trap
- Smooth open/close animations via CSS (no JS animation libraries)
- Skip link integration
- Reduced motion support

**`blocks/navigation/view.js`:**
```javascript
import { store, getContext } from '@wordpress/interactivity';

const { state, actions } = store('earthbound/navigation', {
  state: {
    get isMenuOpen() {
      return getContext().isOpen;
    },
    get currentSubmenu() {
      return getContext().activeSubmenu;
    }
  },
  actions: {
    toggleMenu() {
      const context = getContext();
      context.isOpen = !context.isOpen;
      
      if (context.isOpen) {
        // Trap focus within menu
        actions.trapFocus();
      } else {
        // Return focus to toggle button
        actions.releaseFocus();
      }
    },
    openSubmenu(event) {
      const context = getContext();
      context.activeSubmenu = event.target.dataset.submenu;
    },
    closeSubmenu() {
      const context = getContext();
      context.activeSubmenu = null;
    },
    handleKeydown(event) {
      const context = getContext();
      
      switch(event.key) {
        case 'Escape':
          actions.closeAll();
          break;
        case 'ArrowDown':
          actions.focusNext();
          break;
        case 'ArrowUp':
          actions.focusPrevious();
          break;
        case 'ArrowRight':
          actions.openSubmenu(event);
          break;
        case 'ArrowLeft':
          actions.closeSubmenu();
          break;
      }
    },
    trapFocus() { /* implementation */ },
    releaseFocus() { /* implementation */ },
    focusNext() { /* implementation */ },
    focusPrevious() { /* implementation */ },
    closeAll() { /* implementation */ }
  }
});
```

**`blocks/navigation/render.php`:**
```php
<?php
$context = [
    'isOpen' => false,
    'activeSubmenu' => null,
];
?>
<nav 
    <?php echo get_block_wrapper_attributes(['class' => 'earthbound-nav']); ?>
    data-wp-interactive="earthbound/navigation"
    <?php echo wp_interactivity_data_wp_context($context); ?>
    data-wp-on--keydown="actions.handleKeydown"
>
    <a href="#main-content" class="skip-link">
        <?php esc_html_e('Skip to main content', 'earthbound'); ?>
    </a>
    
    <button 
        class="nav-toggle"
        data-wp-on--click="actions.toggleMenu"
        data-wp-bind--aria-expanded="state.isMenuOpen"
        aria-controls="nav-menu"
    >
        <span class="nav-toggle__icon" aria-hidden="true"></span>
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
        <?php echo $content; // Block inner content ?>
    </ul>
</nav>
```

---

## 4. "My Work" Section

### 4.1 Data Architecture

The My Work section combines three data sources:

1. **Curated Projects** — Custom post type with manual entry
2. **GitHub Issues** — Fetched via GitHub API, cached
3. **Trac Tickets** — Fetched via Trac XML-RPC or scraping, cached

### 4.2 Custom Post Type: Projects

```php
// inc/post-types.php
function earthbound_register_projects_cpt() {
    register_post_type('eb_project', [
        'labels' => [
            'name' => __('Projects', 'earthbound'),
            'singular_name' => __('Project', 'earthbound'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'projects'],
        'menu_icon' => 'dashicons-portfolio',
        'template' => [
            ['core/paragraph', ['placeholder' => 'Project description...']],
        ],
    ]);
    
    // Register custom fields for project metadata
    register_post_meta('eb_project', 'project_url', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
    ]);
    register_post_meta('eb_project', 'project_repo', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
    ]);
    register_post_meta('eb_project', 'project_status', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'default' => 'active',
    ]);
    register_post_meta('eb_project', 'project_year', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'earthbound_register_projects_cpt');
```

### 4.3 GitHub Feed Block

**Configuration (stored in block attributes or theme customizer):**
```php
$github_config = [
    'username' => 'adamsilverstein', // Make configurable
    'repos' => [], // Empty = all repos, or specify array
    'issue_states' => ['closed'], // Show completed work
    'labels' => [], // Optional filter
    'per_page' => 10,
    'cache_duration' => HOUR_IN_SECONDS,
];
```

**REST API Endpoint:**
```php
// inc/api-endpoints.php
function earthbound_register_github_endpoint() {
    register_rest_route('earthbound/v1', '/github-activity', [
        'methods' => 'GET',
        'callback' => 'earthbound_get_github_activity',
        'permission_callback' => '__return_true',
        'args' => [
            'page' => [
                'default' => 1,
                'sanitize_callback' => 'absint',
            ],
            'per_page' => [
                'default' => 10,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);
}
add_action('rest_api_init', 'earthbound_register_github_endpoint');

function earthbound_get_github_activity($request) {
    $page = $request->get_param('page');
    $per_page = $request->get_param('per_page');
    $cache_key = "earthbound_github_{$page}_{$per_page}";
    
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return rest_ensure_response($cached);
    }
    
    $username = get_theme_mod('earthbound_github_username', 'adamsilverstein');
    
    // Fetch from GitHub API
    $response = wp_remote_get(
        "https://api.github.com/search/issues?" . http_build_query([
            'q' => "author:{$username} type:issue state:closed",
            'sort' => 'updated',
            'order' => 'desc',
            'per_page' => $per_page,
            'page' => $page,
        ]),
        [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'Earthbound-Theme',
                // Add: 'Authorization' => 'token ' . GITHUB_TOKEN for higher rate limits
            ],
        ]
    );
    
    if (is_wp_error($response)) {
        return new WP_Error('github_error', 'Failed to fetch GitHub data', ['status' => 500]);
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    $formatted = array_map(function($issue) {
        return [
            'id' => $issue['id'],
            'title' => $issue['title'],
            'url' => $issue['html_url'],
            'repo' => earthbound_extract_repo_name($issue['repository_url']),
            'state' => $issue['state'],
            'created_at' => $issue['created_at'],
            'closed_at' => $issue['closed_at'],
            'labels' => array_map(fn($l) => $l['name'], $issue['labels']),
        ];
    }, $body['items'] ?? []);
    
    $result = [
        'items' => $formatted,
        'total' => $body['total_count'] ?? 0,
        'page' => $page,
        'per_page' => $per_page,
    ];
    
    set_transient($cache_key, $result, HOUR_IN_SECONDS);
    
    return rest_ensure_response($result);
}
```

**GitHub Feed Block with Interactivity API:**
```javascript
// blocks/github-feed/view.js
import { store, getContext } from '@wordpress/interactivity';

const { state, actions } = store('earthbound/github-feed', {
  state: {
    items: [],
    isLoading: false,
    currentPage: 1,
    totalItems: 0,
    error: null,
    
    get hasMore() {
      const ctx = getContext();
      return ctx.items.length < ctx.totalItems;
    }
  },
  
  actions: {
    *loadMore() {
      const context = getContext();
      if (state.isLoading) return;
      
      context.isLoading = true;
      context.error = null;
      
      try {
        const response = yield fetch(
          `/wp-json/earthbound/v1/github-activity?page=${context.currentPage + 1}`
        );
        const data = yield response.json();
        
        context.items = [...context.items, ...data.items];
        context.currentPage = data.page;
        context.totalItems = data.total;
      } catch (err) {
        context.error = 'Failed to load more items';
      } finally {
        context.isLoading = false;
      }
    },
    
    handleKeydown(event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        actions.loadMore();
      }
    }
  }
});
```

### 4.4 WordPress Trac Feed Block

**Trac Data Fetching Strategy:**

WordPress Trac doesn't have a clean REST API, so use one of these approaches:

**Option A: RSS Feed Parsing (Recommended)**
```php
function earthbound_get_trac_activity() {
    $cache_key = 'earthbound_trac_activity';
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    $username = get_theme_mod('earthbound_trac_username', 'adamsilverstein');
    $feed_url = "https://core.trac.wordpress.org/query?" . http_build_query([
        'reporter' => $username,
        'or' => '',
        'owner' => $username,
        'format' => 'rss',
        'order' => 'changetime',
        'desc' => '1',
    ]);
    
    $response = wp_remote_get($feed_url);
    
    if (is_wp_error($response)) {
        return [];
    }
    
    $xml = simplexml_load_string(wp_remote_retrieve_body($response));
    $items = [];
    
    foreach ($xml->channel->item as $item) {
        $items[] = [
            'title' => (string) $item->title,
            'url' => (string) $item->link,
            'description' => wp_strip_all_tags((string) $item->description),
            'date' => (string) $item->pubDate,
            'ticket_id' => earthbound_extract_trac_id((string) $item->link),
        ];
    }
    
    set_transient($cache_key, $items, HOUR_IN_SECONDS * 2);
    
    return $items;
}
```

**Option B: Trac XML-RPC (if available)**
```php
// More complex but gives structured data
// Requires XML-RPC client library
```

### 4.5 My Work Page Template

**`templates/page-my-work.html`:**
```html
<!-- wp:template-part {"slug":"header"} /-->

<!-- wp:group {"tagName":"main","metadata":{"name":"Main Content"},"layout":{"type":"constrained"}} -->
<main id="main-content" class="wp-block-group" tabindex="-1">
    
    <!-- wp:heading {"level":1,"className":"page-title"} -->
    <h1 class="wp-block-heading page-title">My Work</h1>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph {"className":"page-intro"} -->
    <p class="page-intro">A collection of projects I've built and contributed to.</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:group {"className":"work-section work-section--projects"} -->
    <div class="wp-block-group work-section work-section--projects">
        
        <!-- wp:heading {"level":2} -->
        <h2 class="wp-block-heading">Featured Projects</h2>
        <!-- /wp:heading -->
        
        <!-- wp:pattern {"slug":"earthbound/project-grid"} /-->
        
    </div>
    <!-- /wp:group -->
    
    <!-- wp:group {"className":"work-section work-section--github"} -->
    <div class="wp-block-group work-section work-section--github">
        
        <!-- wp:heading {"level":2} -->
        <h2 class="wp-block-heading">GitHub Contributions</h2>
        <!-- /wp:heading -->
        
        <!-- wp:earthbound/github-feed {"perPage":10} /-->
        
    </div>
    <!-- /wp:group -->
    
    <!-- wp:group {"className":"work-section work-section--trac"} -->
    <div class="wp-block-group work-section work-section--trac">
        
        <!-- wp:heading {"level":2} -->
        <h2 class="wp-block-heading">WordPress Core Contributions</h2>
        <!-- /wp:heading -->
        
        <!-- wp:earthbound/trac-feed {"perPage":10} /-->
        
    </div>
    <!-- /wp:group -->
    
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer"} /-->
```

---

## 5. Accessibility Requirements

### 5.1 Keyboard Navigation Checklist

All interactive elements must be operable via keyboard:

| Element | Tab | Enter/Space | Arrow Keys | Escape |
|---------|-----|-------------|------------|--------|
| Navigation links | Focus | Activate | Move between items | Close submenu |
| Mobile menu toggle | Focus | Open/close | — | Close menu |
| Submenu items | Focus | Activate | Up/Down navigation | Close to parent |
| Load more buttons | Focus | Trigger action | — | — |
| Project cards | Focus | Navigate to project | — | — |
| Skip links | Focus | Jump to target | — | — |

### 5.2 Focus Management

```css
/* Visible focus indicators - never remove, only enhance */
:focus-visible {
    outline: 3px solid var(--wp--preset--color--primary);
    outline-offset: 2px;
    border-radius: 2px;
}

/* Focus trap styles for modals/mobile menu */
.focus-trap-active body > *:not(.nav-menu) {
    visibility: hidden;
}
```

### 5.3 ARIA Implementation

```php
// Navigation ARIA
<nav aria-label="<?php esc_attr_e('Primary navigation', 'earthbound'); ?>">
<ul role="menubar">
<li role="none">
    <a role="menuitem" aria-haspopup="true" aria-expanded="false">

// Live regions for dynamic content
<div 
    aria-live="polite" 
    aria-atomic="true" 
    class="screen-reader-text"
    data-wp-text="state.statusMessage"
></div>

// Loading states
<button 
    data-wp-bind--aria-busy="state.isLoading"
    data-wp-bind--aria-disabled="state.isLoading"
>
```

### 5.4 Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}
```

### 5.5 Color Contrast

Ensure all color palettes meet WCAG 2.1 AA standards:
- Normal text: 4.5:1 minimum
- Large text (18px+ or 14px bold): 3:1 minimum
- UI components: 3:1 minimum

Test each palette combination and document in `styles/*.json`.

---

## 6. theme.json Configuration

```json
{
    "$schema": "https://schemas.wp.org/trunk/theme.json",
    "version": 3,
    "settings": {
        "appearanceTools": true,
        "useRootPaddingAwareAlignments": true,
        "layout": {
            "contentSize": "720px",
            "wideSize": "1200px"
        },
        "color": {
            "palette": [
                { "slug": "primary", "color": "#D4A03C", "name": "Primary" },
                { "slug": "secondary", "color": "#8B4513", "name": "Secondary" },
                { "slug": "tertiary", "color": "#C17817", "name": "Tertiary" },
                { "slug": "background", "color": "#FDF5E6", "name": "Background" },
                { "slug": "background-alt", "color": "#F5E6D3", "name": "Background Alt" },
                { "slug": "foreground", "color": "#3D2914", "name": "Foreground" },
                { "slug": "foreground-muted", "color": "#6B5344", "name": "Foreground Muted" },
                { "slug": "accent", "color": "#B8860B", "name": "Accent" }
            ],
            "gradients": [
                {
                    "slug": "warm-sunset",
                    "gradient": "linear-gradient(135deg, var(--wp--preset--color--primary) 0%, var(--wp--preset--color--tertiary) 100%)",
                    "name": "Warm Sunset"
                }
            ],
            "custom": true,
            "customDuotone": true,
            "customGradient": true,
            "defaultPalette": false,
            "defaultGradients": false
        },
        "typography": {
            "fluid": true,
            "fontFamilies": [
                {
                    "fontFamily": "Righteous, cursive",
                    "slug": "heading",
                    "name": "Heading",
                    "fontFace": [
                        {
                            "fontFamily": "Righteous",
                            "fontWeight": "400",
                            "fontStyle": "normal",
                            "src": ["file:./assets/fonts/righteous-v17-latin-regular.woff2"]
                        }
                    ]
                },
                {
                    "fontFamily": "Nunito, sans-serif",
                    "slug": "body",
                    "name": "Body",
                    "fontFace": [
                        {
                            "fontFamily": "Nunito",
                            "fontWeight": "400",
                            "fontStyle": "normal",
                            "src": ["file:./assets/fonts/nunito-v26-latin-regular.woff2"]
                        },
                        {
                            "fontFamily": "Nunito",
                            "fontWeight": "700",
                            "fontStyle": "normal",
                            "src": ["file:./assets/fonts/nunito-v26-latin-700.woff2"]
                        }
                    ]
                }
            ],
            "fontSizes": [
                { "slug": "small", "size": "0.875rem", "fluid": { "min": "0.75rem", "max": "0.875rem" } },
                { "slug": "medium", "size": "1rem", "fluid": { "min": "0.875rem", "max": "1rem" } },
                { "slug": "large", "size": "1.25rem", "fluid": { "min": "1rem", "max": "1.25rem" } },
                { "slug": "x-large", "size": "1.75rem", "fluid": { "min": "1.25rem", "max": "1.75rem" } },
                { "slug": "xx-large", "size": "2.5rem", "fluid": { "min": "1.75rem", "max": "2.5rem" } },
                { "slug": "hero", "size": "4rem", "fluid": { "min": "2.5rem", "max": "4rem" } }
            ],
            "customFontSize": true,
            "lineHeight": true,
            "dropCap": false
        },
        "spacing": {
            "units": ["rem", "em", "%", "vw"],
            "spacingScale": {
                "steps": 7
            },
            "blockGap": true,
            "margin": true,
            "padding": true
        },
        "border": {
            "color": true,
            "radius": true,
            "style": true,
            "width": true
        },
        "shadow": {
            "presets": [
                {
                    "slug": "natural",
                    "shadow": "0 4px 6px -1px rgba(61, 41, 20, 0.1), 0 2px 4px -1px rgba(61, 41, 20, 0.06)",
                    "name": "Natural"
                },
                {
                    "slug": "warm",
                    "shadow": "0 10px 25px -5px rgba(212, 160, 60, 0.25)",
                    "name": "Warm"
                },
                {
                    "slug": "chunky",
                    "shadow": "4px 4px 0 var(--wp--preset--color--secondary)",
                    "name": "Chunky"
                }
            ]
        },
        "custom": {
            "border-radius": {
                "small": "0.5rem",
                "medium": "1rem",
                "large": "1.5rem",
                "pill": "9999px"
            },
            "transition": {
                "fast": "150ms ease",
                "normal": "250ms ease",
                "slow": "400ms ease"
            }
        }
    },
    "styles": {
        "color": {
            "background": "var(--wp--preset--color--background)",
            "text": "var(--wp--preset--color--foreground)"
        },
        "typography": {
            "fontFamily": "var(--wp--preset--font-family--body)",
            "fontSize": "var(--wp--preset--font-size--medium)",
            "lineHeight": "1.6"
        },
        "spacing": {
            "blockGap": "1.5rem",
            "padding": {
                "top": "0",
                "right": "clamp(1rem, 5vw, 2rem)",
                "bottom": "0",
                "left": "clamp(1rem, 5vw, 2rem)"
            }
        },
        "elements": {
            "heading": {
                "typography": {
                    "fontFamily": "var(--wp--preset--font-family--heading)",
                    "fontWeight": "400",
                    "lineHeight": "1.2"
                },
                "color": {
                    "text": "var(--wp--preset--color--secondary)"
                }
            },
            "link": {
                "color": {
                    "text": "var(--wp--preset--color--primary)"
                },
                ":hover": {
                    "color": {
                        "text": "var(--wp--preset--color--secondary)"
                    }
                },
                ":focus": {
                    "color": {
                        "text": "var(--wp--preset--color--secondary)"
                    }
                }
            },
            "button": {
                "color": {
                    "background": "var(--wp--preset--color--primary)",
                    "text": "var(--wp--preset--color--background)"
                },
                "border": {
                    "radius": "var(--wp--custom--border-radius--pill)"
                },
                "typography": {
                    "fontFamily": "var(--wp--preset--font-family--heading)",
                    "fontSize": "var(--wp--preset--font-size--medium)",
                    "fontWeight": "400"
                },
                ":hover": {
                    "color": {
                        "background": "var(--wp--preset--color--secondary)"
                    }
                },
                ":focus": {
                    "color": {
                        "background": "var(--wp--preset--color--secondary)"
                    }
                }
            }
        },
        "blocks": {
            "core/site-title": {
                "typography": {
                    "fontFamily": "var(--wp--preset--font-family--heading)",
                    "fontSize": "var(--wp--preset--font-size--x-large)"
                }
            },
            "core/navigation": {
                "typography": {
                    "fontFamily": "var(--wp--preset--font-family--heading)"
                }
            }
        }
    },
    "templateParts": [
        { "name": "header", "title": "Header", "area": "header" },
        { "name": "footer", "title": "Footer", "area": "footer" }
    ],
    "customTemplates": [
        { "name": "page-my-work", "title": "My Work", "postTypes": ["page"] },
        { "name": "blank", "title": "Blank Canvas", "postTypes": ["page", "post"] }
    ]
}
```

---

## 7. Implementation Phases

### Phase 1: Foundation (Week 1)
1. Initialize theme structure with all directories
2. Create `theme.json` with full color/typography configuration
3. Build all four style variations
4. Set up `functions.php` with proper enqueueing
5. Create header and footer template parts
6. Implement skip links and basic accessibility features

### Phase 2: Navigation (Week 2)
1. Build custom navigation block with Interactivity API
2. Implement mobile menu with focus trap
3. Add keyboard navigation (arrow keys, escape)
4. Test with screen readers
5. Add reduced motion support

### Phase 3: Content Blocks (Week 3)
1. Register Projects custom post type
2. Build project-card block
3. Create GitHub feed block with REST API endpoint
4. Create Trac feed block with RSS parsing
5. Implement "Load More" functionality with Interactivity API

### Phase 4: Templates & Patterns (Week 4)
1. Build all page templates
2. Create block patterns (hero, project grid, about section)
3. Build front-page template
4. Build My Work page template
5. Style all components with retro 70s aesthetic

### Phase 5: Polish & Testing (Week 5)
1. Full keyboard navigation audit
2. Screen reader testing (VoiceOver, NVDA)
3. Color contrast verification for all palettes
4. Performance optimization (fonts, caching)
5. Cross-browser testing
6. Documentation

---

## 8. Configuration Options

Create a simple settings page or use Customizer for:

```php
// Theme Customizer settings
add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('earthbound_settings', [
        'title' => __('Earthbound Settings', 'earthbound'),
        'priority' => 30,
    ]);
    
    // GitHub Username
    $wp_customize->add_setting('earthbound_github_username', [
        'default' => 'adamsilverstein',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('earthbound_github_username', [
        'label' => __('GitHub Username', 'earthbound'),
        'section' => 'earthbound_settings',
        'type' => 'text',
    ]);
    
    // Trac Username
    $wp_customize->add_setting('earthbound_trac_username', [
        'default' => 'adamsilverstein',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('earthbound_trac_username', [
        'label' => __('WordPress Trac Username', 'earthbound'),
        'section' => 'earthbound_settings',
        'type' => 'text',
    ]);
    
    // Cache Duration
    $wp_customize->add_setting('earthbound_cache_duration', [
        'default' => 3600,
        'sanitize_callback' => 'absint',
    ]);
    
    $wp_customize->add_control('earthbound_cache_duration', [
        'label' => __('API Cache Duration (seconds)', 'earthbound'),
        'section' => 'earthbound_settings',
        'type' => 'number',
    ]);
});
```

---

## 9. Testing Checklist

### Accessibility
- [ ] All interactive elements reachable via Tab key
- [ ] Focus visible on all focusable elements
- [ ] Skip link works and focuses main content
- [ ] Navigation operable with arrow keys
- [ ] Escape closes open menus/modals
- [ ] Screen reader announces all content correctly
- [ ] ARIA labels present on all interactive elements
- [ ] Color contrast meets WCAG AA (4.5:1 text, 3:1 UI)
- [ ] Reduced motion respected
- [ ] Focus trap works in mobile menu

### Functionality
- [ ] Navigation works on mobile and desktop
- [ ] Projects CPT displays correctly
- [ ] GitHub feed loads and paginates
- [ ] Trac feed loads and displays
- [ ] Load More buttons work with keyboard
- [ ] All style variations apply correctly
- [ ] Fonts load correctly (self-hosted)
- [ ] API caching works (check transients)

### Performance
- [ ] No CLS from font loading (use `font-display: swap`)
- [ ] Images have width/height or aspect-ratio
- [ ] CSS/JS properly enqueued and minified
- [ ] API responses cached appropriately

---

## 10. Notes for Claude Code

When implementing this theme:

1. **Start with `theme.json`** — it's the foundation for all styling
2. **Use `@wordpress/scripts`** for building JS/CSS (`wp-scripts build`)
3. **Interactivity API requires** the `wp-interactivity` script dependency
4. **Test keyboard navigation continuously** — don't leave it for the end
5. **Self-host fonts** — download from Google Fonts or Fontsource
6. **Use `wp_interactivity_data_wp_context()`** for server-side context
7. **Generator functions (function\*)** are used in Interactivity API for async
8. **Transient caching** is essential for GitHub/Trac API calls
9. **Block registration** can use `block.json` metadata (modern approach)
10. **Style variations** go in `/styles/*.json` with full color overrides

Good luck! 🌻
