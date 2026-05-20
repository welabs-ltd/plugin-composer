# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A WordPress plugin that **generates other WordPress plugins**. The `[wlb_plugin_composer]` shortcode renders a form; submission copies `plugin-stub/`, rewrites tokens, zips it, and streams the zip back. No admin UI.

## Commands

```bash
composer install
composer phpcs        # WPCS sniff
composer phpcbf       # auto-fix
npm install
npm run start         # block dev (watch) -> dist/
npm run build         # block prod build  -> dist/
bin/build.sh          # full release: npm build + composer no-dev + zip
```

No tests are wired up (phpunit is in `require-dev` but there's no `tests/`).

## Architecture

**Two codebases in one repo:**
- `includes/` — this plugin (namespace `WeLabs\PluginComposer\`, PSR-4).
- `plugin-stub/` — template data, **not runtime code**. Strings like `plugin-stub`, `PluginStub`, `Plugin_Stub`, `PLUGIN_STUB` are intentional placeholders. Don't lint or refactor it as part of `includes/`.

**Flow:** `PluginComposer` (singleton container) → on `init` builds services → `ShortCode::handle_form_submission` (on `template_redirect`) sanitizes POST → `PluginBuilder::build()` copies stub, optionally layers in settings files and/or WP-VIP variants (`phpcs-wpvip.xml`, `composer-wpvip.json`, `phpcs-wpvip.yml`), rewrites sentinel comments, runs `FileSystem::replace()` across the tree, renames the two entry files, zips, deletes the working dir.

**Sentinels in `plugin-stub/includes/PluginStub.php` — keep these strings intact:**
- `// REGISTER_SETTINGS_REST_ROUTE`
- `// INIT_PLUGIN_SETTINGS_CLASSES`
- `NODE_DEVELOPMENT_COMMANDS` / `NODE_PRODUCTION_COMMANDS` (in stub's README/build.sh)

**When adding a new template variable**, update all three: `PluginBuilder::$placeholders`, `templates/compose-form.php`, and the sanitizer list in `ShortCode::handle_form_submission`.

**Filters:** `welabs_pc_container_{service}`, `get_welabs_plugin_compose_form`, `get_welabs_plugin_compose_form_errors`, `welabs_plugin_composer_form_data`.

## Gutenberg block

`src/blocks/plugin-composer/` (built to `dist/blocks/plugin-composer/` via `@wordpress/scripts` + a `webpack.config.js` that overrides output to `dist/`). The block is dynamic — `save` returns `null`; `Blocks::render_plugin_composer` builds a `[wlb_plugin_composer ...]` string and runs `do_shortcode()`, so the form template and submission handling stay shortcode-driven. `dist/` is gitignored; `bin/build.sh` runs `npm install && npm run build` before staging.
