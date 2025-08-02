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
 * Requires Plugins: plugin_requires
 * License: plugin_license
 */
use WeLabs\PluginStub\PluginStub;
use WeLabs\PluginStub\DependencyManagement\Container;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'PLUGIN_STUB_FILE' ) ) {
    define( 'PLUGIN_STUB_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

// Instantiate the container
$GLOBALS['welabs_plugin_stub_container'] = new Container();

// Register the service providers
$GLOBALS['welabs_plugin_stub_container']->addServiceProvider( new \WeLabs\PluginStub\Providers\ServiceProvider() );

/**
 * Get the container.
 *
 * @return Container
 */
function welabs_plugin_stub_get_container(): Container {
	return $GLOBALS['welabs_plugin_stub_container'];
}

/**
 * Load Plugin_Stub Plugin when all plugins loaded
 *
 * @return \WeLabs\PluginStub\PluginStub
 */
function welabs_plugin_stub() {
    return PluginStub::init();
}

// Lets Go....
welabs_plugin_stub();
