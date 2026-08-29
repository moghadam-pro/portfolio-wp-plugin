# Changelog

All notable changes to MPRO Portfolio are documented here.

## [1.1.1] - 2026-08-29

### Fixed

- Repeater template rows no longer submit. `hidden` does not keep a field out
  of a form, so every save posted an empty tools entry and a collaborator and
  meta-detail row keyed literally `__INDEX__`. The template fields are now
  disabled, and the script re-enables the copy it clones.
- Select fields in the Display Options side box no longer push past the panel
  and give the editor sidebar a horizontal scrollbar; `.widefat` alone is not
  enough in a side meta box.
- Rank Math no longer reports Portfolio as a newly detected post type on every
  admin screen. The plugin records the post type and both taxonomies in Rank
  Math's own "known" lists before its watcher compares them, and clears any
  notification already queued.

### Changed

- Front-end styles read the active theme's design tokens when it exposes them
  (radius, hairline, accent, surface, display and mono families), falling back
  to the plugin's own values everywhere else, so archives and single portfolio
  pages sit inside the Moghadam theme without looking bolted on.
- The newly added repeater row takes focus.

## [1.1.0] - 2026-07-01

### Rebuilt architecture

- Replaced mixed automatic/manual admin menu handling with a single custom menu owner.
- Disabled automatic CPT and taxonomy menu generation to prevent duplicate Tags and Categories.
- Enforced the requested submenu order: All Portfolio, Add Portfolio, Tags, Categories, Styles.
- Kept the Portfolio top-level menu at position 4 and before another MPRO menu when present.

### Added

- Separate native Featured Image and custom Cover Image.
- Structured duration value and unit fields.
- Repeatable tools, collaborators with URLs, and additional metadata rows.
- REST API registration for portfolio metadata.
- Three rebuilt card styles: Clean, Editorial, and Immersive.
- Theme-overridable card, single, archive, and taxonomy templates.
- Unified `[mpro_portfolio]` shortcode plus compatibility aliases.
- Elementor Portfolio Grid widget with manual selection, filters, responsive columns, style selection, and pagination.
- Explicit opt-in for Elementor saved templates used as card styles.
- Per-item card and single-template overrides.
- Rank Math variables for short description, role, duration, tools, and client.
- Compatibility reads for metadata created by the previous MPRO and alternative 1.0.x builds.

### Changed

- Archive rendering now uses the WordPress main query.
- Rank Math integration now uses documented public hooks only.
- Elementor widget registration now uses `elementor/widgets/register`.
- Plugin uninstall removes settings but preserves portfolio content.
