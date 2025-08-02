# Plugin Composer - Build Instructions

This document explains how to build and use the React-based admin settings panel.

## 🚀 Quick Start

### Prerequisites
- Node.js (version 14 or higher)
- npm or yarn
- WordPress development environment

### Installation

1. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

2. **Build the React components:**
   ```bash
   npm run build
   ```

3. **For development (with hot reloading):**
   ```bash
   npm run dev
   ```

## 📁 Project Structure

```
plugin-composer/
├── src/
│   └── admin/
│       └── settings.js          # React settings component
├── assets/
│   └── admin/
│       ├── settings.js          # Built JavaScript (generated)
│       └── settings.css         # Built CSS (generated)
├── includes/
│   └── Admin/
│       └── Settings.php         # PHP settings handler
├── package.json                 # Node.js dependencies
├── webpack.config.js           # Webpack configuration
└── BUILD.md                    # This file
```

## 🔧 Build Commands

### Development
```bash
npm run dev
```
- Watches for file changes
- Hot reloading enabled
- Unminified code for debugging

### Production
```bash
npm run build
```
- Minified and optimized code
- Ready for production use

### Code Quality
```bash
npm run lint:js          # Lint JavaScript
npm run lint:css         # Lint CSS
npm run lint:php         # Lint PHP (via composer)
npm run format           # Format code
```

## 🎯 Admin Settings Features

### General Settings
- **Allow Guest Access**: Toggle to enable/disable guest user access
- **Required Capability**: Set minimum user capability for logged-in users
- **Default Plugin Type**: Choose default plugin type (Classic or Container-based)

### Rate Limiting
- **Rate Limit Attempts**: Maximum attempts per time period (1-50)
- **Rate Limit Duration**: Time period in seconds

### Validation Rules
- **Max Plugin Name Length**: Maximum characters for plugin names
- **Max Description Length**: Maximum characters for descriptions
- **Max License Length**: Maximum characters for license text
- **Max Author Name Length**: Maximum characters for author names

### File Settings
- **File Permissions**: Octal permissions for generated files

## 🔌 REST API Endpoints

### Get Settings
```
GET /wp-json/plugin-composer/v1/settings
```

### Update Settings
```
POST /wp-json/plugin-composer/v1/settings
```

**Required Headers:**
- `Content-Type: application/json`
- `X-WP-Nonce: {nonce}`

**Example Request Body:**
```json
{
  "allow_guest_access": true,
  "required_capability": "edit_posts",
  "rate_limit_attempts": 10,
  "max_plugin_name_length": 150
}
```

## 🎨 Customization

### Adding New Settings

1. **Update PHP Settings Handler:**
   ```php
   // In includes/Admin/Settings.php
   $settings_to_update = [
       'your_new_setting' => 'sanitize_text_field',
   ];
   ```

2. **Update React Component:**
   ```jsx
   // In src/admin/settings.js
   <TextControl
       label={__('Your New Setting', 'welabs-plugin-composer')}
       value={settings.your_new_setting || ''}
       onChange={(value) => updateSetting('your_new_setting', value)}
   />
   ```

3. **Update Config Class:**
   ```php
   // In includes/Config.php
   private static $defaults = [
       'your_new_setting' => 'default_value',
   ];
   ```

### Styling

The React components use WordPress components by default. To add custom styles:

1. Create `src/admin/settings.scss`
2. Import in `src/admin/settings.js`
3. Build with `npm run build`

## 🚨 Troubleshooting

### Build Issues
- **Node modules not found**: Run `npm install`
- **Webpack errors**: Check `webpack.config.js` syntax
- **Permission errors**: Ensure write permissions to `assets/admin/`

### Runtime Issues
- **React not loading**: Check if `settings.js` is built and enqueued
- **API errors**: Verify REST API is enabled and nonce is valid
- **Settings not saving**: Check user capabilities and API permissions

### Development Tips
- Use browser dev tools to debug React components
- Check WordPress debug log for PHP errors
- Use `console.log()` in React for debugging
- Test API endpoints with Postman or similar tool

## 📦 Deployment

1. **Build for production:**
   ```bash
   npm run build
   ```

2. **Verify built files exist:**
   - `assets/admin/settings.js`
   - `assets/admin/settings.css`

3. **Deploy to WordPress:**
   - Upload plugin files
   - Activate plugin
   - Access settings at: `Settings > Plugin Composer`

## 🔒 Security Notes

- All settings are sanitized before saving
- REST API requires `manage_options` capability
- Nonce verification is enforced
- Input validation is applied on both client and server

## 📚 Additional Resources

- [WordPress React Components](https://developer.wordpress.org/block-editor/packages/packages-components/)
- [WordPress REST API](https://developer.wordpress.org/rest-api/)
- [Webpack Configuration](https://webpack.js.org/configuration/)
- [WordPress Scripts](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/) 