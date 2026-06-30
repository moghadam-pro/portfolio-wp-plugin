# Changelog

All notable changes to MPRO Portfolio are documented in this file.

The project follows [Semantic Versioning](https://semver.org/).

## [1.0.1] - 2026-06-30

### Fixed

- `in_array()` call in the Styles settings screen now uses strict type comparison, preventing Elementor template checkboxes from incorrectly appearing checked due to PHP type coercion.
- Elementor card style now falls back to Style 1 (Minimal) when Elementor is deactivated after a template was selected, instead of silently rendering nothing.
- Removed a dead `admin_init` hook and its empty `register_settings()` method from `MPRO_PF_Admin_Menu` — settings registration was already handled correctly by `MPRO_PF_Styles`.
- Admin repeater "Add Tool" button now builds new rows via jQuery DOM methods instead of HTML string concatenation, eliminating a potential XSS vector from unescaped data attributes.

### Added

- `.vscode/settings.json` with Intelephense WordPress stubs enabled, resolving all false-positive "Undefined function" IDE diagnostics across every plugin file.

---

## [1.0.0] - 2026-06-30

### Added

- Portfolio custom post type with a dedicated top-level admin menu.
- Native portfolio categories and tags.
- Project overview, implementation date, duration, role, tools, metadata, collaborators, cover image, featured status, and presentation settings.
- Clean, Editorial, and Immersive built-in card styles.
- Built-in single and archive templates with theme override support.
- Elementor Portfolio Grid widget with manual selection, taxonomy, featured, ordering, layout, and pagination controls.
- Automatic discovery and rendering of published Elementor Saved Templates.
- Portfolio grid and single-card shortcodes.
- Rank Math defaults, fallback description, and portfolio replacement variables.
- Custom post-list columns, sorting, and filters.
- GitHub Actions PHP syntax validation workflow.
