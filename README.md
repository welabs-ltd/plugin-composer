# Plugin Composer - Wordpress plugin boilerplate #
**Contributors:**  [weLabs](https://profiles.wordpress.org/welabs/), [Mahbub](https://profiles.wordpress.org/mrabbani/)

**Tags:** wordpress, wp-plugin, plugin-development.  
**Requires at least:** 5.4  
**Tested up to:** 6.2.2  
**Requires PHP:** 7.4  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Generate wordpress plugin boilerplate having `PSR4` auto-loading.

## Description ##

You may easily generate a PSR4 auto-loading WordPress plugin by submitting a simple form. You can render the plugin composer form any where using the `wlb_plugin_composer`.

```
[wlb_plugin_composer]
// With supported attributes
[wlb_plugin_composer submit-text='Build Plugin' class="form-class"]
```


👉 **Official Demo Link:** Try out the [Plugin Composer](https://welabs.dev/compose-plugin/).

### Gutenberg Block ###

A **Plugin Composer** block is bundled (category: *Widgets*). Insert it from the block inserter to render the same form a shortcode would — no shortcode markup needed.

Sidebar controls:

- **Form settings** — submit button label, toggles to show/hide the *Include Plugin Settings?* and *WP VIP Support* fields.
- **Submit button colors** — background and text color for both normal and hover states (applied via CSS variables on the form wrapper, so hover works without inline `<style>` injection).
- **Field placeholders** — override the placeholder text for any of the eight inputs (Plugin Name, Description, Requires, License, URL, Author Name/Email/URL).

The block is **dynamic** — it has no `save` output. Rendering is delegated server-side to the `[wlb_plugin_composer]` shortcode, so the form template, validation, and submission flow stay in one place. Every sidebar control maps to a shortcode attribute, so `[wlb_plugin_composer submit-text="Go" button_bg_color="#000" show_wpvip_field="no" placeholder_plugin_name="My awesome plugin"]` works identically.

### Block Development:

```
npm install
npm run start
```

### Build Release:

```
bin/build.sh
```

## Changelog ##

### 1.5.0 ###
- **New:** Gutenberg block (`welabs/plugin-composer`) — dynamic block that renders the existing shortcode server-side. Sidebar controls: submit button label, show/hide toggles for *Include Plugin Settings?* and *WP VIP Support* fields, submit button background/text colors (normal + hover via CSS variables), and overridable placeholders for all eight inputs.
- **New:** Shortcode attributes mirroring every block control — `placeholder_plugin_*`, `show_settings_field`, `show_wpvip_field`, `button_bg_color` / `button_text_color` / `button_bg_hover_color` / `button_text_hover_color`.
- **New:** `vendor_namespace` placeholder in `PluginBuilder` — rebrands `WeLabs` / `welabs` (PHP namespace, composer vendor, function prefix, CSS classes, JS strings, URLs) across the generated plugin in one shot.
- **Fix:** Plugin zip download was returning corrupted files when an output buffer (e.g. Redis Object Cache) or gzip handler was active. The handler now clears buffers, disables compression, sends `Content-Length`, and exits after streaming.

### 1.4.0 ###
- Introduced WP VIP coding standard support.
- Improved form UI.

### 1.3.0 ###
- Built zip filenames now include the version.
- `get_template()` accepts template args.

### 1.2.0 ###
- React-based plugin settings scaffold with dynamic settings links and node-command injection.
- Modernized form design.