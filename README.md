# Earthbound Theme

A WordPress block theme blending old-time newspaper editorial design with warm, brass-and-parchment earth tones. Built for WordPress 6.7+ using Full Site Editing, the Interactivity API, and modern PHP 8.1+ features.

![Home page — desktop](assets/screenshots/home-desktop.webp)

## Features

- **Newspaper masthead header** with decorative rules, centered title, monospace dateline, and section navigation with vertical bar separators
- **Multi-column front page** with asymmetric 2/3 + 1/3 lead/sidebar layout, column rules, and below-the-fold grid
- **Editorial typography** — Playfair Display for headlines, Hanken Grotesk for body, Space Mono for code and labels (Lora ships as an optional serif family)
- **Fluid type scale** with dramatic range (hero up to 6rem) for broadsheet-style headline hierarchy
- **Brass accents** — engraved brass glow on headline hover, terminal badges, brass underline links, dark panel sections
- **Typographic flourishes** — thin-thick-thin newspaper rules, small-caps section labels, drop caps, pull quotes with hairline borders
- **Parchment-and-brass color palette** — warm parchment background, walnut ink, antique brass primary, moss and clay earth accents
- **Four style variations**: Patina, Desert Sunset, Forest Grove, Ocean Tide
- **Projects custom post type** for portfolio display
- **GitHub and WordPress Trac feed integration** via custom blocks
- **Full accessibility** — keyboard navigation, screen readers, reduced motion, focus indicators

## Screenshots

### Home Page

| Desktop | Mobile |
|---------|--------|
| ![Home desktop](assets/screenshots/home-desktop.webp) | ![Home mobile](assets/screenshots/home-mobile.webp) |

### Single Post

| Desktop | Mobile |
|---------|--------|
| ![Single desktop](assets/screenshots/single-desktop.webp) | ![Single mobile](assets/screenshots/single-mobile.webp) |

## Installation

1. Download or clone this repository into `wp-content/themes/`
2. Activate via **Appearance > Themes** in the WordPress admin
3. Set a static front page under **Settings > Reading** to use the newspaper layout

### Local Development

```bash
npx @wordpress/env start
```

> **Note:** On Docker Desktop for Mac, bind mounts may not sync to the Apache container. If the site renders blank, copy the theme into the container:
> ```bash
> docker cp . $(docker ps --format '{{.Names}}' | grep wordpress-1 | grep -v tests):/var/www/html/wp-content/themes/earthbound-theme/
> ```

## Building for Production

Package a clean, upload-ready zip containing only the files the theme needs at
runtime (no dev tooling, local config, or demo content):

```bash
npm install   # once, installs the archiver dev dependency
npm run build
```

This produces `earthbound.zip` with a single top-level `earthbound/` folder.
Upload it via **Appearance > Themes > Add New > Upload Theme**, or unzip it into
`wp-content/themes/`.

The list of bundled files lives in `bin/build.mjs` (the `INCLUDE` array) — add a
path there when shipping a new production file. Excluded by design: `.git`,
`node_modules`, `package.json`, `.wp-env.json`, `CLAUDE.md`,
`project-instructions.md`, this `README.md`, and the `assets/images` /
`assets/screenshots` demo assets.

## Style Variations

| Variation | Primary | Background |
|-----------|---------|------------|
| **Patina** (default) | Brass `#936F26` | Parchment `#ECE2CC` |
| **Desert Sunset** | Terracotta `#E07B53` | Parchment `#F2EDE6` |
| **Forest Grove** | Olive `#6B8E23` | Birch `#EFEEE8` |
| **Ocean Tide** | Cadet Blue `#5F9EA0` | Sea Foam `#ECEEED` |

## Typography

| Role | Font | Usage |
|------|------|-------|
| **Headings** | Playfair Display | Headlines, site title, buttons |
| **Body** | Hanken Grotesk | Body copy, excerpts, paragraphs |
| **Serif** | Lora | Optional serif accents |
| **Monospace** | Space Mono | Code blocks, datelines, terminal badges |

## Requirements

- WordPress 6.7+
- PHP 8.1+

## License

GPL-2.0-or-later
