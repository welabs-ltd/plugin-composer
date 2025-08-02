# Plugin Composer - Wordpress plugin boilerplate #
**Contributors:**  [weLabs](https://profiles.wordpress.org/welabs/), [Mahbub](https://profiles.wordpress.org/mrabbani/)

**Tags:** wordpress, wp-plugin, plugin-development.  
**Requires at least:** 5.4  
**Tested up to:** 6.2.2  
**Requires PHP:** 7.4  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Generate wordpress plugin boilerplate having `PSR4` auto-loading with enhanced security features.

## Description ##

You may easily generate a PSR4 auto-loading WordPress plugin by submitting a simple form. You can render the plugin composer form any where using the `wlb_plugin_composer`.

```
[wlb_plugin_composer]
// With supported attributes
[wlb_plugin_composer submit-text='Build Plugin' class="form-class"]
```


👉 **Official Demo Link:** Try out the [Plugin Composer](https://welabs.dev/plugin-composer).

## 🔒 Security Features

This plugin includes comprehensive security measures:

- **Input Validation**: All form inputs are validated and sanitized
- **XSS Protection**: All outputs are properly escaped
- **Path Traversal Protection**: File operations are secured against directory traversal
- **Rate Limiting**: Protection against abuse with configurable limits
- **User Capability Checks**: Only authorized users can generate plugins
- **Comprehensive Logging**: Security events and errors are logged
- **Configuration Management**: Centralized, filterable configuration

For detailed security information, see [SECURITY.md](SECURITY.md).

### Build Release:

```
bin/build.sh
```