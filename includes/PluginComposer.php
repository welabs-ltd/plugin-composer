<?php

namespace WeLabs\PluginComposer;

use WeLabs\PluginComposer\Assets;
use WeLabs\PluginComposer\Lib\FileSystem;
use WeLabs\PluginComposer\Lib\PluginBuilder;
use WeLabs\PluginComposer\ShortCode;

/**
 * PluginComposer class.
 *
 * @class PluginComposer The class that holds the entire PluginComposer plugin
 */
final class PluginComposer {
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '1.2.0';

	/**
	 * Instance of self
	 *
	 * @var PluginComposer
	 */
	private static $instance = null;

	/**
	 * Holds various class instances
	 *
	 * @since 2.6.10
	 *
	 * @var array
	 */
	private $container = array();

	/**
	 * Constructor for the PluginComposer class
	 *
	 * Sets up all the appropriate hooks and actions
	 * within our plugin.
	 */
	private function __construct() {
		$this->define_constants();

		register_activation_hook( PLUGIN_COMPOSER_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( PLUGIN_COMPOSER_FILE, array( $this, 'deactivate' ) );

		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		add_action( 'woocommerce_flush_rewrite_rules', array( $this, 'flush_rewrite_rules' ) );
	}

	/**
	 * Initializes the PluginComposer() class
	 *
	 * Checks for an existing PluginComposer instance
	 * and if it doesn't find one, creates it.
	 */
	public static function init() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Magic getter to bypass referencing objects
	 *
	 * @since 2.6.10
	 *
	 * @param string $prop
	 *
	 * @return Class Instance
	 */
	public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
			return apply_filters( 'welabs_pc_container_' . $prop, $this->container[ $prop ] );
		}
	}

	/**
	 * Placeholder for activation function
	 *
	 * Nothing being called here yet.
	 */
	public function activate() {
		// Rewrite rules during plugin_composer activation
		if ( $this->has_woocommerce() ) {
			$this->flush_rewrite_rules();
		}
	}

	/**
	 * Flush rewrite rules after plugin_composer is activated or woocommerce is activated
	 *
	 * @since 3.2.8
	 */
	public function flush_rewrite_rules() {
		// fix rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Placeholder for deactivation function
	 *
	 * Nothing being called here yet.
	 */
	public function deactivate() {
	}

	/**
	 * Define all constants
	 *
	 * @return void
	 */
	public function define_constants() {
		$this->define( 'PLUGIN_COMPOSER_PLUGIN_VERSION', $this->version );
		$this->define( 'PLUGIN_COMPOSER_DIR', dirname( PLUGIN_COMPOSER_FILE ) );
		$this->define( 'PLUGIN_COMPOSER_INC_DIR', PLUGIN_COMPOSER_DIR . '/includes' );
		$this->define( 'PLUGIN_COMPOSER_TEMPLATE_DIR', PLUGIN_COMPOSER_DIR . '/templates' );
		$this->define( 'PLUGIN_COMPOSER_PLUGIN_ASSET', plugins_url( 'assets', PLUGIN_COMPOSER_FILE ) );

		// give a way to turn off loading styles and scripts from parent theme
		$this->define( 'PLUGIN_COMPOSER_LOAD_STYLE', true );
		$this->define( 'PLUGIN_COMPOSER_LOAD_SCRIPTS', true );
	}

	/**
	 * Define constant if not already defined
	 *
	 * @param string      $name
	 * @param string|bool $value
	 *
	 * @return void
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Load the plugin after WP User Frontend is loaded
	 *
	 * @return void
	 */
	public function init_plugin() {
		$this->includes();
		$this->init_hooks();

		do_action( 'plugin_composer_loaded' );
	}

	/**
	 * Initialize the actions
	 *
	 * @return void
	 */
	public function init_hooks() {
		// initialize the classes
		add_action( 'init', array( $this, 'init_classes' ), 4 );
		add_action( 'plugins_loaded', array( $this, 'after_plugins_loaded' ) );
		do_action( 'welabs_plugin_composer_loaded' );
	}

	/**
	 * Include all the required files
	 *
	 * @return void
	 */
	public function includes() {
		// include_once STUB_PLUGIN_DIR . '/functions.php';
	}

	/**
	 * Init all the classes
	 *
	 * @return void
	 */
	public function init_classes() {
		$this->container['shortcode']   = new ShortCode();
		$this->container['assets']      = new Assets();
		$this->container['file_system'] = new FileSystem();
		$this->container['builder']     = new PluginBuilder( $this->get_file_system() );
	}

	/**
	 * Get the filesystem class.
	 *
	 * @return \WeLabs\PluginComposer\Contracts\FileSystemContract
	 */
	public function get_file_system() {
		if ( ! $this->file_system ) {
			$this->container['file_system'] = new FileSystem();
		}

		return $this->file_system;
	}

	/**
	 * Get the plugin builder.
	 *
	 * @return \WeLabs\PluginComposer\Contracts\BuilderContract
	 */
	public function get_builder() {
		return $this->builder;
	}

	/**
	 * Executed after all plugins are loaded
	 *
	 * At this point plugin_composer Pro is loaded
	 *
	 * @since 2.8.7
	 *
	 * @return void
	 */
	public function after_plugins_loaded() {
		// Initiate background processes and other tasks
	}

	/**
	 * Check whether woocommerce is installed and active
	 *
	 * @since 2.9.16
	 *
	 * @return bool
	 */
	public function has_woocommerce() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Check whether woocommerce is installed
	 *
	 * @since 3.2.8
	 *
	 * @return bool
	 */
	public function is_woocommerce_installed() {
		return in_array( 'woocommerce/woocommerce.php', array_keys( get_plugins() ), true );
	}
}
