# MPRO Portfolio

A structured WordPress portfolio and case-study manager with reusable card styles, native archives, Elementor integration, shortcodes, REST-ready metadata, and Rank Math SEO support.

**Current version:** 1.1.0  
**Release date:** July 1, 2026  
**License:** GPL-2.0-or-later

---

## Overview

MPRO Portfolio adds a dedicated Portfolio content type to WordPress while preserving the familiar post-editing workflow. Each portfolio item can contain full editor content, a native Featured Image, an independent Cover Image, structured project metadata, categories, tags, display settings, and optional Elementor templates.

The plugin is designed for:

- Product design case studies
- UI/UX portfolios
- Digital product showcases
- Agency work archives
- Selected work sections on landing pages
- Project libraries that need structured metadata

Version 1.1.0 introduces a rebuilt architecture focused on predictable WordPress behavior, maintainability, migration safety, and removal of duplicated taxonomy menus.

---

## Table of Contents

- [Key Features](#key-features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Updating from an Earlier Build](#updating-from-an-earlier-build)
- [Admin Menu Architecture](#admin-menu-architecture)
- [Portfolio Content Model](#portfolio-content-model)
- [Built-in Card Styles](#built-in-card-styles)
- [Styles Settings](#styles-settings)
- [Shortcodes](#shortcodes)
- [PHP Template Helpers](#php-template-helpers)
- [Elementor Integration](#elementor-integration)
- [Rank Math SEO Integration](#rank-math-seo-integration)
- [Single and Archive Templates](#single-and-archive-templates)
- [Theme Overrides](#theme-overrides)
- [URLs and Rewrite Rules](#urls-and-rewrite-rules)
- [REST API and Metadata](#rest-api-and-metadata)
- [Admin List Features](#admin-list-features)
- [Data Compatibility and Migration](#data-compatibility-and-migration)
- [Data Safety and Uninstall Behavior](#data-safety-and-uninstall-behavior)
- [Project Structure](#project-structure)
- [Testing and Verification](#testing-and-verification)
- [Staging Acceptance Checklist](#staging-acceptance-checklist)
- [Known Verification Limits](#known-verification-limits)
- [Development Workflow](#development-workflow)
- [Versioning](#versioning)
- [Changelog](#changelog)
- [Security](#security)
- [License](#license)

---

## Key Features

### WordPress-native portfolio management

- Dedicated public `mpro_portfolio` post type
- Full block/classic editor content support
- Excerpt support
- Featured Image support
- Revisions
- Author, comments, trackbacks, custom fields, and page attributes
- Public archive and single portfolio pages
- REST API support

### Structured project metadata

- Short description
- Separate Cover Image
- Client or company
- External project URL
- Implementation date
- Structured duration value and unit
- Role in project
- Repeatable tools
- Repeatable collaborators with name, role, and URL
- Repeatable custom metadata rows
- Featured portfolio flag
- Per-item card style
- Per-item single template

### Taxonomies

- Hierarchical Portfolio Categories
- Non-hierarchical Portfolio Tags
- Public taxonomy archives
- REST-enabled terms
- Admin filters and list-table integration

### Frontend presentation

- Three built-in card styles
- Built-in single portfolio template
- Built-in archive and taxonomy template
- Responsive grid rendering
- Manual portfolio selection
- Featured portfolio queries
- Category and tag filtering
- Pagination
- Theme template overrides
- Optional Elementor saved-template rendering

### Integrations

- Elementor Portfolio Grid widget
- Rank Math and Rank Math PRO compatibility
- Rank Math replacement variables
- REST-ready metadata
- PHP helper functions for theme development

---

## Requirements

| Requirement | Minimum / Status |
|---|---|
| WordPress | 6.0 or newer |
| PHP | 7.4 or newer |
| Elementor | Optional |
| Rank Math SEO | Optional |
| Rank Math SEO PRO | Optional |

The plugin remains operational when Elementor or Rank Math is not installed or inactive.

---

## Installation

### Install from a ZIP file

1. Download the plugin ZIP package.
2. In WordPress, open **Plugins → Add New Plugin → Upload Plugin**.
3. Select the ZIP file.
4. Click **Install Now**.
5. Activate **MPRO Portfolio**.
6. Open **Portfolio → Styles** and review the default display settings.
7. Create a test portfolio item.
8. If portfolio URLs return a 404 response, open **Settings → Permalinks** and click **Save Changes** once.

### Manual installation

1. Extract the package.
2. Upload the `mpro-portfolio` directory to:

```text
/wp-content/plugins/
```

3. Activate **MPRO Portfolio** from the WordPress Plugins screen.

---

## Updating from an Earlier Build

Before replacing an earlier MPRO Portfolio build:

1. Create a full database backup.
2. Back up the existing plugin directory.
3. Deactivate the previous build.
4. Upload version 1.1.0.
5. Choose **Replace current with uploaded** when WordPress displays the replacement prompt.
6. Activate the new version.
7. Open **Settings → Permalinks** and save once.
8. Verify the [Staging Acceptance Checklist](#staging-acceptance-checklist).

Version 1.1.0 includes compatibility readers for metadata created by:

- The earlier MPRO Portfolio build
- A compatible alternative 1.0.x build using `_mpro_pf_*` metadata keys

When an existing portfolio item is saved, the plugin writes the canonical version 1.1.0 metadata structure.

---

## Admin Menu Architecture

The admin menu is intentionally controlled by one component only.

WordPress automatic menu registration is disabled for:

- The Portfolio post type
- Portfolio Categories
- Portfolio Tags

The plugin then creates one top-level Portfolio menu and exactly five submenus:

1. **All Portfolio**
2. **Add Portfolio**
3. **Tags**
4. **Categories**
5. **Styles**

This single-owner architecture prevents the duplicated Categories and Tags issue that can occur when automatic and manual submenu registration are mixed.

The plugin targets the fourth visible admin-menu position and attempts to place Portfolio before another menu containing `mpro` in its slug.

---

## Portfolio Content Model

### Native WordPress fields

| Field | Description |
|---|---|
| Title | Portfolio or case-study title |
| Editor content | Full project content using normal WordPress editing tools |
| Excerpt | Optional summary and fallback card description |
| Featured Image | Native WordPress thumbnail |
| Publish date | Native WordPress publication date |
| Author | Native WordPress author |
| Revisions | Native WordPress revision history |
| Comments | Optional native comments |
| Page attributes | Supports menu order and related attributes |

### MPRO project fields

| Field | Storage type | Description |
|---|---|---|
| Short Description | String | Concise project summary used by cards and SEO variables |
| Cover Image | Attachment ID | Independent project cover; falls back to Featured Image when empty |
| Client or Company | String | Client, organization, or product owner |
| Project URL | URL | External link to the project |
| Implementation Date | Date | Date the project was implemented or delivered |
| Duration Value | Numeric string | Project duration amount |
| Duration Unit | String | Days, weeks, months, or years |
| Role | String | The author’s role in the project |
| Tools | Array | Repeatable list of tools used |
| Collaborators | Array | Repeatable name, role, and URL entries |
| Additional Details | Array | Repeatable label and value rows |
| Featured | Boolean | Marks the project for featured queries |
| Card Style | String | Per-item card presentation override |
| Single Style | String | Per-item single-page template override |

### Canonical metadata keys

```text
_mpro_portfolio_short_description
_mpro_portfolio_cover_id
_mpro_portfolio_client
_mpro_portfolio_project_url
_mpro_portfolio_implementation_date
_mpro_portfolio_duration_value
_mpro_portfolio_duration_unit
_mpro_portfolio_role
_mpro_portfolio_tools
_mpro_portfolio_collaborators
_mpro_portfolio_meta_details
_mpro_portfolio_featured
_mpro_portfolio_card_style
_mpro_portfolio_single_style
```

---

## Built-in Card Styles

Version 1.1.0 includes three reusable card styles:

| Identifier | Name | Intended use |
|---|---|---|
| `clean` | Clean | Minimal, balanced portfolio grids |
| `editorial` | Editorial | Case-study and content-led layouts |
| `immersive` | Immersive | Image-forward featured work sections |

A card uses the Cover Image first. When no Cover Image is set, it falls back to the native Featured Image.

Card style priority:

1. Explicit style passed by shortcode, PHP, or Elementor
2. Per-item card style
3. Global default card style

An explicitly enabled Elementor saved template can also become a card style using an internal identifier such as:

```text
elementor:123
```

---

## Styles Settings

Open **Portfolio → Styles** to configure:

- Default Card Style
- Archive Card Style
- Archive Columns
- Default Single Template
- Elementor saved templates explicitly allowed as card styles

Elementor templates are not automatically exposed as card styles. An administrator must opt in to each template on the Styles screen. This prevents unrelated Elementor templates from appearing in portfolio style selectors.

Published Elementor templates can be selected as single-page templates without being enabled as card templates.

---

## Shortcodes

### Main portfolio grid

```text
[mpro_portfolio]
```

Default behavior:

- Nine published items
- Ordered by publish date
- Descending order
- Three desktop columns
- Two tablet columns
- One mobile column
- Global or per-item card style

### Shortcode attributes

| Attribute | Default | Accepted values | Description |
|---|---:|---|---|
| `ids` | Empty | Comma-separated post IDs | Manually selected items; supplied order is preserved |
| `featured` | `false` | `true` / `false` | Restrict results to featured portfolio items |
| `category` | Empty | Comma-separated slugs or term IDs | Filter by Portfolio Category |
| `tag` | Empty | Comma-separated slugs or term IDs | Filter by Portfolio Tag |
| `style` | Empty | `clean`, `editorial`, `immersive`, or an enabled Elementor style | Force one card style; empty uses item/global resolution |
| `columns` | `3` | `1`–`6` | Desktop columns |
| `columns_tablet` | `2` | `1`–`4` | Tablet columns |
| `columns_mobile` | `1` | `1`–`2` | Mobile columns |
| `count` | `9` | `1`–`100` or `-1` | Number of items; `-1` returns all matches |
| `posts_per_page` | Empty | Same as `count` | Compatibility alias for `count` |
| `orderby` | `date` | `date`, `implementation_date`, `title`, `menu_order`, `rand`, `modified`, `ID` | Query ordering field |
| `order` | `DESC` | `ASC` / `DESC` | Query direction |
| `pagination` | `false` | `true` / `false` | Enable query-string pagination |
| `page_var` | `mpro_page` | Valid query-variable name | Unique query parameter for pagination |

### Examples

#### Latest portfolio items

```text
[mpro_portfolio count="9" style="clean" columns="3"]
```

#### Manual homepage selection

```text
[mpro_portfolio ids="12,45,67" style="editorial" columns="3"]
```

The order of the supplied IDs is preserved.

#### Featured work

```text
[mpro_portfolio featured="true" count="6" style="immersive" columns="2"]
```

#### Filter by category

```text
[mpro_portfolio category="product-design" count="12"]
```

#### Filter by category and tags

```text
[mpro_portfolio category="product-design" tag="saas,fintech" count="12"]
```

When both category and tag filters are present, they are combined with an `AND` relationship.

#### Order by implementation date

```text
[mpro_portfolio orderby="implementation_date" order="DESC" count="12"]
```

#### Show all matching items

```text
[mpro_portfolio category="case-study" count="-1"]
```

#### Enable pagination

```text
[mpro_portfolio count="12" pagination="true" page_var="work_page"]
```

Use a unique `page_var` when multiple paginated grids appear on the same page.

### Featured compatibility shortcode

```text
[mpro_portfolio_featured]
```

Without explicit IDs, this displays six featured portfolio items.

Manual selection is also supported:

```text
[mpro_portfolio_featured ids="12,45,67" style="editorial"]
```

### Single card shortcode

```text
[mpro_portfolio_single id="123" style="clean"]
```

### Compatibility aliases

The following shortcode names are registered:

```text
[mpro_portfolio]
[mpro_portfolio_grid]
[mpro_portfolio_featured]
[mpro_portfolio_single]
```

---

## PHP Template Helpers

The plugin exposes public helper functions for themes and custom templates.

### Return one card

```php
$card = mpro_portfolio_get_card( 123, 'immersive' );
echo $card;
```

### Print one card

```php
mpro_portfolio_the_card( get_the_ID(), 'editorial' );
```

### Return a grid

```php
$grid = mpro_portfolio_get_grid(
    array(
        'category' => 'product-design',
        'style'    => 'clean',
        'columns'  => 3,
        'count'    => 9,
    )
);

echo $grid;
```

### Print a manually curated grid

```php
mpro_portfolio_the_grid(
    array(
        'ids'            => array( 12, 45, 67 ),
        'style'          => 'editorial',
        'columns'        => 3,
        'columns_tablet' => 2,
        'columns_mobile' => 1,
    )
);
```

Passing an empty card style allows the item/global style resolution to run. Passing `item` explicitly requests the per-item style and then falls back to the global default.

---

## Elementor Integration

When Elementor is active, the plugin registers:

```text
MPRO Portfolio → Portfolio Grid
```

The widget uses Elementor’s current widget registration hook:

```text
elementor/widgets/register
```

### Widget sources

- Latest Portfolio
- Manual Selection
- Featured Portfolio

### Query controls

- Manual item selection
- Categories
- Tags
- Number of items
- Order by
- Ascending or descending order
- Pagination

### Layout controls

- Card style
- Responsive desktop columns
- Responsive tablet columns
- Responsive mobile columns

### Supported order fields

- Publish Date
- Implementation Date
- Title
- Menu Order
- Random

Each Elementor widget instance receives its own pagination query variable to reduce conflicts when more than one portfolio widget is used on a page.

### Elementor saved templates as cards

To use an Elementor saved template as a card:

1. Create and publish a saved Elementor template.
2. Open **Portfolio → Styles**.
3. Enable the template under the Elementor card-template options.
4. Select it globally, per item, in the shortcode-compatible style list, or in the Portfolio Grid widget.

The selected portfolio post is temporarily assigned as the current WordPress post context while the Elementor template is rendered. Dynamic post widgets can therefore resolve the active portfolio item.

### Elementor saved templates as single pages

Published saved templates can be selected as:

- The global default single template
- A per-item single template

When an Elementor single template is selected, the plugin routes the request through its Elementor single wrapper.

---

## Rank Math SEO Integration

The Portfolio post type is public, queryable, REST-enabled, and compatible with Rank Math’s normal post-type discovery.

When Rank Math or Rank Math PRO is active, the plugin adds:

- A Portfolio icon in Rank Math post-type settings
- Portfolio Category and Portfolio Tag icons
- An overrideable default Article schema suggestion
- Initial title and description defaults when Rank Math creates new settings
- Initial sitemap inclusion defaults when Rank Math creates new settings
- Portfolio-specific replacement variables

The plugin does not continuously overwrite an administrator’s existing Rank Math configuration.

### Rank Math replacement variables

| Variable | Output |
|---|---|
| `%portfolio_short_description%` | Portfolio short description |
| `%portfolio_role%` | Project role |
| `%portfolio_duration%` | Formatted duration value and unit |
| `%portfolio_tools%` | Comma-separated tools |
| `%portfolio_client%` | Client or company |

Example description template:

```text
%portfolio_short_description%
```

Example title template:

```text
%title% %sep% %sitename%
```

### Recommended Rank Math checks

After installation on the target site, verify:

1. Portfolio appears in **Rank Math → Titles & Meta**.
2. Portfolio appears in **Rank Math → Sitemap Settings**.
3. Portfolio single pages are indexable as intended.
4. Category and tag archives follow the site’s taxonomy SEO policy.
5. The selected schema type is appropriate for the project content.
6. Replacement variables render correctly in previews and frontend metadata.

---

## Single and Archive Templates

### Built-in single template

The built-in single template displays:

- Categories
- Portfolio title
- Short description
- Cover Image or Featured Image fallback
- Full editor content
- Client
- Role
- Implementation date
- Duration
- Publish date
- External project URL
- Additional metadata
- Tools
- Collaborators
- Tags

### Built-in archive template

The built-in archive template supports:

- Main Portfolio archive
- Portfolio Category archives
- Portfolio Tag archives
- Archive title
- Taxonomy description
- Configurable archive card style
- Configurable archive columns
- Native WordPress main query
- Native WordPress pagination

The archive template does not create a nested secondary query. It consumes the WordPress main query so normal archive pagination and query behavior remain available.

---

## Theme Overrides

Create an `mpro-portfolio` directory inside the active theme or child theme.

### Card overrides

```text
/wp-content/themes/your-theme/mpro-portfolio/cards/card-clean.php
/wp-content/themes/your-theme/mpro-portfolio/cards/card-editorial.php
/wp-content/themes/your-theme/mpro-portfolio/cards/card-immersive.php
```

### Single override

Preferred path:

```text
/wp-content/themes/your-theme/mpro-portfolio/single-mpro_portfolio.php
```

Fallback path also supported:

```text
/wp-content/themes/your-theme/single-mpro_portfolio.php
```

### Main archive override

Preferred path:

```text
/wp-content/themes/your-theme/mpro-portfolio/archive-mpro_portfolio.php
```

Fallback path also supported:

```text
/wp-content/themes/your-theme/archive-mpro_portfolio.php
```

### Category archive override

```text
/wp-content/themes/your-theme/mpro-portfolio/taxonomy-mpro_portfolio_category.php
```

or:

```text
/wp-content/themes/your-theme/taxonomy-mpro_portfolio_category.php
```

### Tag archive override

```text
/wp-content/themes/your-theme/mpro-portfolio/taxonomy-mpro_portfolio_tag.php
```

or:

```text
/wp-content/themes/your-theme/taxonomy-mpro_portfolio_tag.php
```

When a taxonomy-specific override is unavailable, the plugin falls back to the shared Portfolio archive template.

---

## URLs and Rewrite Rules

Default public URLs:

| Resource | Default URL pattern |
|---|---|
| Portfolio archive | `/portfolio/` |
| Single portfolio | `/portfolio/{post-slug}/` |
| Portfolio category | `/portfolio-category/{term-slug}/` |
| Portfolio tag | `/portfolio-tag/{term-slug}/` |

The post type and taxonomy rewrites use `with_front => false`.

Activation and deactivation flush WordPress rewrite rules. If routes still return 404 responses after installation or migration, save **Settings → Permalinks** once.

---

## REST API and Metadata

The Portfolio post type and both taxonomies use `show_in_rest => true`.

Canonical metadata fields are registered through the WordPress metadata API. This supports authenticated editor integrations and structured access through WordPress REST responses where permitted.

Metadata authorization requires the current user to have the `edit_posts` capability.

Registered REST metadata types include:

- Strings
- Integer attachment IDs
- Boolean featured state
- Arrays of tools
- Arrays of collaborators
- Arrays of custom metadata details

The plugin sanitizes metadata before storage using WordPress sanitization functions and field-specific callbacks.

---

## Admin List Features

The **All Portfolio** screen follows the native WordPress post-list pattern and adds portfolio-specific information.

### Columns

- Featured image
- Title
- Categories
- Tags
- Role
- Implementation date
- Card style
- Featured status
- Publish date

### Filters

- Portfolio Category
- Portfolio Tag
- Card Style
- Featured Only
- Not Featured

### Sorting

- Implementation date
- Role

Portfolio items can still use the standard WordPress bulk actions, search, status views, author handling, and date controls provided by the post list.

---

## Data Compatibility and Migration

Version 1.1.0 uses a canonical `_mpro_portfolio_*` metadata namespace.

For migration safety, read compatibility is included for:

- Metadata produced by the earlier MPRO Portfolio build
- Compatible `_mpro_pf_*` fields produced by an alternative 1.0.x implementation
- Legacy duration values stored as a single text string

Legacy duration text is parsed into:

- Duration value
- Duration unit

Saving a migrated portfolio item writes canonical version 1.1.0 fields.

Recommended migration process:

1. Back up the database.
2. Install version 1.1.0 on staging.
3. Open representative old portfolio items.
4. Confirm imported values.
5. Save the items.
6. Validate card, archive, and single output.
7. Move the tested release to production.

---

## Data Safety and Uninstall Behavior

Uninstalling MPRO Portfolio removes only plugin-owned options:

```text
mpro_portfolio_settings
mpro_portfolio_version
```

The following data is intentionally preserved:

- Portfolio posts
- Portfolio metadata
- Portfolio Categories
- Portfolio Tags
- Uploaded images and attachments
- WordPress revisions

This non-destructive uninstall behavior reduces the risk of accidental portfolio data loss.

To permanently remove preserved content, delete the Portfolio items, terms, and related media manually before or after uninstalling the plugin.

---

## Project Structure

```text
mpro-portfolio/
├── .github/
│   └── workflows/
│       └── php-lint.yml
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── portfolio.css
│   └── js/
│       └── admin.js
├── includes/
│   ├── elementor/
│   │   └── class-mpro-portfolio-grid-widget.php
│   ├── class-mpro-portfolio-admin-list.php
│   ├── class-mpro-portfolio-admin-menu.php
│   ├── class-mpro-portfolio-content-types.php
│   ├── class-mpro-portfolio-elementor.php
│   ├── class-mpro-portfolio-meta-boxes.php
│   ├── class-mpro-portfolio-plugin.php
│   ├── class-mpro-portfolio-rank-math.php
│   ├── class-mpro-portfolio-renderer.php
│   ├── class-mpro-portfolio-settings.php
│   ├── class-mpro-portfolio-shortcodes.php
│   ├── class-mpro-portfolio-templates.php
│   └── functions.php
├── templates/
│   ├── cards/
│   │   ├── card-clean.php
│   │   ├── card-editorial.php
│   │   └── card-immersive.php
│   ├── archive-mpro_portfolio.php
│   ├── single-elementor.php
│   └── single-mpro_portfolio.php
├── CHANGELOG.md
├── GITHUB_DESCRIPTION.txt
├── README.md
├── VERSION
├── mpro-portfolio.php
├── readme.txt
└── uninstall.php
```

---

## Testing and Verification

Version 1.1.0 was verified through runtime and static tests before packaging.

### Runtime environment

| Component | Tested value |
|---|---|
| WordPress | 6.5 |
| PHP | 8.5 |
| Runtime | WordPress Playground |
| Plugin version | 1.1.0 |

### Runtime verification results

The following assertions passed in a real WordPress runtime:

- Plugin activation completed without a plugin fatal error.
- The `mpro_portfolio` post type registered successfully.
- Portfolio Category registered successfully.
- Portfolio Tag registered successfully.
- Automatic CPT menu generation was disabled.
- Automatic taxonomy menu generation was disabled.
- A published portfolio item was created successfully.
- Clean card rendering completed successfully.
- A manual Editorial shortcode grid rendered successfully.
- Cover metadata registered through the WordPress metadata API.
- Collaborator metadata registered through the WordPress metadata API.
- Legacy duration text was parsed into a structured value and unit.
- `count="-1"` rendered all matching portfolio items.
- The admin menu contained exactly one Portfolio top-level entry.
- The submenu order was exactly All Portfolio, Add Portfolio, Tags, Categories, Styles.
- No duplicate Tags submenu was created.
- No duplicate Categories submenu was created.

### Static verification results

- All 21 PHP files passed `php -l` syntax validation.
- The admin JavaScript file passed `node --check`.
- The plugin bootstrap completed in an isolated WordPress-function stub.
- A dedicated menu architecture test passed all assertions.
- No Arabic or Persian script was found in source files, documentation, labels, or comments.
- No `.DS_Store` files were included.
- No `.vscode` directory was included.
- No `.idea` directory was included.
- No `node_modules` directory was included.
- The release archive passed ZIP integrity validation.

### Architecture assertions verified

- The admin menu has one owner.
- Automatic CPT and taxonomy menus remain disabled.
- Featured Image and Cover Image remain independent.
- Duration, tools, collaborators, and additional details use structured metadata.
- Archive templates consume the WordPress main query.
- Elementor registration uses the current widget hook.
- Rank Math integration uses public documented filters.
- Uninstall preserves portfolio content.

---

## Staging Acceptance Checklist

Run this checklist on the actual target site before production deployment.

### Installation and admin

- [ ] Plugin activates without warnings or fatal errors.
- [ ] Portfolio is the fourth visible top-level menu where the current admin-menu environment allows it.
- [ ] Portfolio appears before another MPRO menu.
- [ ] Exactly one Portfolio top-level menu exists.
- [ ] Tags appears once.
- [ ] Categories appears once.
- [ ] Submenu order is All Portfolio, Add Portfolio, Tags, Categories, Styles.

### Content editing

- [ ] A new Portfolio item can be created.
- [ ] Title and full editor content save correctly.
- [ ] Excerpt saves correctly.
- [ ] Featured Image saves correctly.
- [ ] Cover Image saves independently.
- [ ] Short description survives save and reload.
- [ ] Client or company survives save and reload.
- [ ] Project URL survives save and reload.
- [ ] Implementation date survives save and reload.
- [ ] Duration value and unit survive save and reload.
- [ ] Role survives save and reload.
- [ ] Multiple tools survive save and reload.
- [ ] Collaborator names, roles, and URLs survive save and reload.
- [ ] Additional metadata rows survive save and reload.
- [ ] Featured status survives save and reload.
- [ ] Per-item card style survives save and reload.
- [ ] Per-item single template survives save and reload.

### Frontend

- [ ] Clean card renders correctly.
- [ ] Editorial card renders correctly.
- [ ] Immersive card renders correctly.
- [ ] Manual shortcode selection preserves ID order.
- [ ] Featured shortcode returns only featured items.
- [ ] Category filters work.
- [ ] Tag filters work.
- [ ] Combined category and tag filters work.
- [ ] Pagination works with the chosen `page_var`.
- [ ] Portfolio archive renders correctly.
- [ ] Portfolio Category archive renders correctly.
- [ ] Portfolio Tag archive renders correctly.
- [ ] Single portfolio template renders correctly.
- [ ] Cover Image falls back to Featured Image when empty.
- [ ] Theme overrides are discovered correctly.

### Elementor

- [ ] The MPRO Portfolio widget category appears.
- [ ] The Portfolio Grid widget appears.
- [ ] Latest source works.
- [ ] Manual source works.
- [ ] Featured source works.
- [ ] Category and tag filters work.
- [ ] Responsive column controls work.
- [ ] Pagination works.
- [ ] Enabled Elementor card templates render in portfolio post context.
- [ ] Elementor single templates render correctly.

### Rank Math

- [ ] Portfolio appears in Titles & Meta settings.
- [ ] Portfolio appears in Sitemap settings.
- [ ] Portfolio SEO metabox appears on the editor screen.
- [ ] Schema selection works.
- [ ] Portfolio replacement variables resolve correctly.
- [ ] Existing site-level Rank Math settings are not unexpectedly overwritten.

### Migration

- [ ] Representative content from the previous MPRO build loads correctly.
- [ ] Representative `_mpro_pf_*` metadata loads correctly.
- [ ] Legacy duration text is interpreted correctly.
- [ ] Saving migrated content writes canonical metadata.

---

## Known Verification Limits

The following distinctions are intentional and important:

### Runtime verified

- WordPress 6.5
- PHP 8.5
- Core plugin activation
- Post type and taxonomy registration
- Admin menu structure
- Portfolio creation
- Core metadata registration
- Built-in card rendering
- Manual grid rendering
- Legacy duration compatibility
- Unlimited result count behavior

### Source-compatible but not runtime-verified in the available environment

- PHP 7.4

The code avoids PHP language features introduced after PHP 7.4, and the plugin header declares PHP 7.4 as the minimum. A complete WordPress runtime using PHP 7.4 was not available during the final local test.

### Statically verified and guarded, but requires target-site UI testing

- Elementor integration
- Rank Math integration
- Rank Math PRO integration
- Compatibility with the target theme
- Compatibility with the exact production plugin versions
- Interaction with admin-menu customization plugins
- Interaction with caching, optimization, and security plugins

No compatibility claim should replace staging verification on the exact production stack.

---

## Development Workflow

### PHP syntax check

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
```

### JavaScript syntax check

```bash
node --check assets/js/admin.js
```

### ZIP integrity check

```bash
unzip -t mpro-portfolio-1.1.0.zip
```

### Search for unwanted development artifacts

```bash
find . \
  -name '.DS_Store' -o \
  -name '.vscode' -o \
  -name '.idea' -o \
  -name 'node_modules'
```

### GitHub Actions

The repository includes a PHP lint workflow at:

```text
.github/workflows/php-lint.yml
```

The workflow provides automated PHP syntax validation for repository changes.

---

## Versioning

MPRO Portfolio follows Semantic Versioning:

```text
MAJOR.MINOR.PATCH
```

- **MAJOR** — incompatible architecture or public API changes
- **MINOR** — backward-compatible features and integrations
- **PATCH** — backward-compatible fixes and refinements

Current version:

```text
1.1.0
```

The canonical version is stored in:

- The plugin header
- `MPRO_PORTFOLIO_VERSION`
- `VERSION`
- `readme.txt`
- `CHANGELOG.md`

---

## Changelog

### 1.1.0 — July 1, 2026

#### Rebuilt architecture

- Replaced mixed automatic/manual admin-menu handling with a single custom menu owner.
- Disabled automatic CPT and taxonomy menu generation.
- Eliminated duplicated Tags and Categories submenus.
- Enforced the requested submenu order.
- Kept the Portfolio menu at the fourth target position and before another MPRO menu when present.

#### Added

- Independent Featured Image and Cover Image behavior.
- Structured duration value and unit.
- Repeatable tools.
- Repeatable collaborators with URLs.
- Repeatable additional metadata.
- REST API metadata registration.
- Rebuilt Clean, Editorial, and Immersive cards.
- Theme-overridable card, single, archive, and taxonomy templates.
- Unified `[mpro_portfolio]` shortcode.
- Compatibility shortcode aliases.
- Elementor Portfolio Grid widget.
- Manual selection, filters, responsive columns, styles, and pagination in Elementor.
- Explicit opt-in for Elementor card templates.
- Per-item card and single-template overrides.
- Rank Math replacement variables.
- Metadata compatibility readers for previous builds.

#### Changed

- Archive output now uses the WordPress main query.
- Rank Math integration now uses public filters.
- Elementor widget registration now uses `elementor/widgets/register`.
- Uninstall removes settings while preserving portfolio content.

For the complete release history, see [`CHANGELOG.md`](CHANGELOG.md).

---

## Security

When reporting a security issue, do not publish exploit details in a public issue before a fix is available.

Include:

- A clear description of the issue
- Affected plugin version
- WordPress and PHP versions
- Reproduction steps
- Required user role or permissions
- Expected and actual behavior
- Any proof-of-concept code needed to verify the report

The plugin applies WordPress capability checks, nonces, sanitization, escaping, and metadata authorization callbacks throughout its admin and rendering workflows.

---

## License

MPRO Portfolio is licensed under the GNU General Public License v2.0 or later.

```text
GPL-2.0-or-later
```

You may use, modify, and redistribute the plugin under the terms of that license.

---

## Repository Description

```text
A structured WordPress portfolio manager with project metadata, reusable card styles, Elementor integration, native archives, shortcodes, REST support, and Rank Math SEO compatibility.
```

---

## Author

**Sayid Moghadam**  
Product Designer and plugin author
