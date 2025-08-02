<?php
/**
 * Plugin Name: Plugin Stub
 * Plugin URI:  plugin_uri
 * Description: plugin_description
 * Version: 0.0.1
 * Author: plugin_author_name
 * Author URI: plugin_author_uri
 * Text Domain: plugin-stub
 * WC requires at least: 5.0.0
 * Domain Path: /languages/
 * License: plugin_license
 */
use BaseNameSpace\PluginStub\PluginStub;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'PLUGIN_STUB_FILE' ) ) {
    define( 'PLUGIN_STUB_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load Plugin_Stub Plugin when all plugins loaded
 *
 * @return \BaseNameSpace\PluginStub\PluginStub
 */
function BaseNameSpace_plugin_stub() {
    return PluginStub::init();
}

// Lets Go....
BaseNameSpace_plugin_stub();
