# MPRO Portfolio

> A complete portfolio / case-study management system for WordPress, with custom card styles, Elementor template support, and Rank Math SEO integration. Part of the MPRO plugin suite.

[![Version](https://img.shields.io/badge/version-1.0.0-black?style=flat-square)](https://github.com/moghadam-pro/portfolio-wp-plugin/releases)
[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-3858e9?style=flat-square&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/license-GPL--2.0-green?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Overview

**MPRO Portfolio** is a custom post type plugin built for showcasing project case studies. Each portfolio item supports a full set of structured fields — short description, cover image, meta details, tags, categories, dates, project duration, tools used, and people involved — alongside a standard WordPress content editor for the full case-study write-up.

Items can be displayed using one of three built-in card styles, or a custom Elementor template registered as an additional style. A dedicated single-item template renders the full case study, and shortcodes make it easy to feature selected items anywhere on the site — including manually curated "Selected Work" sections on a homepage.

---

## Features

- **Custom post type** — `Portfolio`, registered with full Gutenberg/block editor and REST API support
- **Two taxonomies** — hierarchical `Categories` and flat `Tags`, both manageable like standard post terms
- **Structured project fields**:
  - Short description (used on cards and archive listings)
  - Cover image (standard featured image)
  - Meta details — unlimited custom label/value pairs
  - Implementation date (separate from the WordPress publish date)
  - Project duration (value + unit: days / weeks / months)
  - Tools used — repeatable list
  - People involved — repeatable name + role pairs
  - Role in project, client name, project URL
  - Full post content editor for the case-study write-up
- **Three built-in card styles** (Minimal, Detailed, List Row) — intentionally simple baseline layouts, designed to evolve in future versions
- **Elementor template support** — any saved Elementor template can be registered as an additional card style or as the single-item template, directly from the Styles settings screen
- **Default single template** — a clean two-column case-study layout (content + detail sidebar) used automatically when no Elementor template is selected
- **Default archive template** — grid-based archive page using the configured archive card style
- **Three shortcodes**:
  - `[mpro_portfolio_grid]` — query-based archive grid, filterable by category/tag
  - `[mpro_portfolio_featured]` — manually curated set of items (ideal for homepage sections)
  - `[mpro_portfolio_single]` — embed a single item's card anywhere
- **Rank Math SEO PRO integration** — Portfolio items and taxonomies are registered with Rank Math's sitemap, schema, and content-analysis systems
- **Admin list table columns** — Cover, Category, Tags, Role, and Duration columns added to the All Portfolio screen
- **MPRO suite integration** — registers its own top-level admin menu positioned directly above the shared MPRO menu

---

## Installation

### From ZIP

1. Download `mpro-portfolio.zip` from the [Releases](https://github.com/moghadam-pro/portfolio-wp-plugin/releases) page
2. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Select the ZIP and click **Install Now**
4. Click **Activate Plugin**

### From Source

```bash
cd wp-content/plugins/
git clone https://github.com/moghadam-pro/portfolio-wp-plugin.git mpro-portfolio
```

Then activate from **Plugins → Installed Plugins**.

On activation, the plugin registers its custom post type and taxonomies and flushes rewrite rules automatically.

---

## Admin Menu

After activation, a **Portfolio** menu item appears in the WordPress sidebar, positioned directly above the shared **MPRO** menu used by other MPRO plugins.

| Submenu | Description |
|---------|-------------|
| **All Portfolio** | Standard list table of all portfolio items, with custom columns for cover image, category, tags, role, and duration |
| **Add Portfolio** | Create a new portfolio item |
| **Categories** | Manage hierarchical categories |
| **Tags** | Manage flat tags |
| **Styles** | Configure default card styles, archive style, and Elementor template assignments |

---

## Adding a Portfolio Item

Each portfolio item edit screen includes the following meta boxes, in addition to the standard title and content editor:

### Summary
A short description (1–2 sentences) shown on cards and archive listings. The full case-study write-up goes in the main content editor below.

### Project Details
| Field | Description |
|-------|-------------|
| Implementation Date | The actual delivery/build date, kept separate from the WordPress publish date |
| Project Duration | A numeric value plus a unit (days, weeks, or months) |
| Role in Project | Free text, e.g. "Full-stack Developer" |
| Client | Client or company name |
| Project URL | Live link to the finished project |

### Tools Used
A repeatable list of tools, frameworks, or technologies used on the project. Add or remove rows freely.

### People Involved
A repeatable list of name + role pairs for everyone who contributed to the project.

### Meta Details
A repeatable list of custom label/value pairs for any additional structured detail you want displayed in the project sidebar — e.g. "Industry: Real Estate" or "Platform: WooCommerce".

### Display Style
A per-item override for which card style is used when this item appears in a grid. If left on the default, the global default set in **Portfolio → Styles** is used.

---

## Display Styles

Configured from **Portfolio → Styles**.

### Built-in card styles

| Style | Layout |
|-------|--------|
| **Style 1 — Minimal** | Cover image with title and category as a bottom overlay |
| **Style 2 — Detailed** | Cover image, title, short description, and tag list below |
| **Style 3 — List Row** | Horizontal layout — image on one side, details on the other; suited to dense archive listings |

These are intentionally simple baseline layouts, designed to be refined or replaced entirely in future plugin versions.

### Elementor templates as styles

Any saved Elementor template (Templates → Saved Templates) can be registered as:

- An additional **card style**, selectable per-item or via shortcode `style` parameter using `elementor-{template_id}`
- The **single-item template**, replacing the plugin's default single template across all portfolio item pages

This lets card and single-item layouts be redesigned visually in Elementor without touching plugin code, and is intended as the primary path for iterating on portfolio design over time.

---

## Usage

### Shortcodes

**Archive-style grid** — queries portfolio items directly, with optional category/tag filtering:

```
[mpro_portfolio_grid count="12" category="" tag="" style="" columns="3"]
```

| Attribute | Default | Description |
|-----------|---------|-------------|
| `count` | `-1` | Number of items to show (`-1` for all) |
| `category` | — | Filter by category slug(s), comma-separated |
| `tag` | — | Filter by tag slug(s), comma-separated |
| `style` | global default | Card style key, or leave empty to use each item's own setting |
| `columns` | `3` | Grid column count (1–6) |
| `orderby` / `order` | `date` / `DESC` | Standard `WP_Query` ordering |

**Featured / curated section** — for manually selected items, e.g. a homepage "Selected Work" block:

```
[mpro_portfolio_featured ids="12,45,67" style="style-2" columns="3"]
```

**Single embed** — drop one item's card anywhere on the site:

```
[mpro_portfolio_single id="123" style="style-1"]
```

### Homepage "Selected Work" + full archive pattern

A common setup: use `[mpro_portfolio_featured]` with a hand-picked list of IDs in a homepage section, and let the automatically generated archive page at `/portfolio/` (or `[mpro_portfolio_grid]` on a custom page) list everything. Both the featured section and the archive can use different card styles independently via the `style` attribute.

---

## Rank Math SEO PRO Integration

When Rank Math SEO PRO is active, this plugin:

- Adds the `Portfolio` post type to the XML sitemap
- Enables Rank Math's SEO score and readability analysis on portfolio item edit screens
- Allows Schema markup (e.g. CreativeWork) to be assigned to portfolio items via the Rank Math meta box
- Ensures portfolio categories and tags are included in the sitemap rather than excluded by default

No additional configuration is required beyond having Rank Math SEO PRO installed and active — the integration registers automatically.

---

## File Structure

```
mpro-portfolio/
├── mpro-portfolio.php              # Main plugin file — bootstraps all classes
├── README.md
├── includes/
│   ├── class-cpt.php               # Custom post type registration
│   ├── class-taxonomies.php        # Category and Tag taxonomies
│   ├── class-meta-boxes.php        # All project detail fields + save logic
│   ├── class-styles.php            # Card style definitions + Elementor template dispatch
│   ├── class-shortcodes.php        # Grid, featured, and single shortcodes
│   ├── class-elementor.php         # Elementor template discovery
│   ├── class-seo.php               # Rank Math SEO PRO integration
│   ├── class-admin-menu.php        # Top-level menu + Styles settings screen
│   ├── class-admin-columns.php     # Custom All Portfolio list table columns
│   └── class-templates.php         # Single/archive template routing
├── templates/
│   ├── single-portfolio.php        # Default single case-study template
│   ├── single-elementor-bridge.php # Renders a selected Elementor single template
│   └── archive-portfolio.php       # Default archive grid template
├── css/
│   ├── mpro-portfolio.css          # Frontend card and single/archive styles
│   └── mpro-portfolio-admin.css    # Admin repeater field styles
└── js/
    ├── mpro-portfolio.js           # Frontend JS (reserved for future features)
    └── mpro-portfolio-admin.js     # Admin repeater add/remove row logic
```

---

## Theme Overrides

This plugin's templates can be overridden from your active theme by adding either of the following files:

```
your-theme/single-mpro_portfolio.php
your-theme/archive-mpro_portfolio.php
```

If present, WordPress's standard template hierarchy will use the theme's version instead of the plugin's default.

---

## Requirements

| | Minimum |
|--|---------|
| WordPress | 5.8 |
| PHP | 7.4 |
| Elementor | Optional — required only for using Elementor templates as card/single styles |
| Rank Math SEO PRO | Optional — required only for the SEO integration features |

---

## MPRO Suite

This plugin is part of the **MPRO** collection — a set of custom WordPress plugins built for fine-grained creative control. MPRO Portfolio's top-level menu is positioned directly above the shared MPRO admin menu used by other plugins in the suite.

| Plugin | Description |
|--------|-------------|
| **Portfolio** | Portfolio / case-study management with custom card styles and Elementor template support |
| [**Text Scramble**](https://github.com/moghadam-pro/text-scramble-wp-plugin) | Character decode animation for text, links, and buttons |
| [**Background Motion**](https://github.com/moghadam-pro/background-motion-wp-plugin) | Interactive pixel displacement canvas for site backgrounds |

More plugins coming soon.

---

## Changelog

### 1.0.0

- Initial release
- Custom post type with Category and Tag taxonomies
- Full project detail meta boxes: short description, implementation date, duration, role, client, project URL, tools used, people involved, custom meta details
- Three built-in card styles (Minimal, Detailed, List Row)
- Elementor template support as additional card styles and as the single-item template
- Default single and archive templates with theme override support
- Three shortcodes: grid, featured, single embed
- Rank Math SEO PRO integration (sitemap, schema, content analysis, taxonomy inclusion)
- Custom All Portfolio admin list table columns
- Top-level admin menu positioned above the shared MPRO menu

---

## Author

**Moghadam.pro** — [moghadam.pro](https://moghadam.pro)

---

## License

Licensed under the [GNU General Public License v2.0](https://www.gnu.org/licenses/gpl-2.0.html).
