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