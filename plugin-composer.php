<?php
/**
 * Plugin Name: Plugin-composer
 * Plugin URI:  https://welabs.dev/
 * Description: Customized plugin. Powered by weLabs.
 * Version: 1.2.0
 * Author: weLabs
 * Author URI: https://welabs.dev/
 * Text Domain: plugin-composer
 * WC requires at least: 5.0.0
 * WC tested up to: 7.6.0
 * Domain Path: /languages/
 * License: GPL2
 */

use WeLabs\PluginComposer\PluginComposer;

// Don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'PLUGIN_COMPOSER_FILE' ) ) {
	define( 'PLUGIN_COMPOSER_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load Plugin_Composer Plugin when all plugins loaded
 *
 * @return Plugin_Composer
 */
function welabs_plugin_composer(): PluginComposer {
	return PluginComposer::init();
}

// Lets Go....
welabs_plugin_composer();
