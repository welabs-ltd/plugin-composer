<?php

namespace WeLabs\PluginStub\Admin;

/**
 * Plugin admin page settings class
 */
class Settings {
	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_settings_menu' ), 100 );
	}

	/**
	 * Register settings main menu
	 *
	 * @return void
	 */
	public function add_admin_settings_menu() {
        add_menu_page(
            'PluginStub Settings',
            'PluginStub',
            'manage_options',
            'plugin_stub-settings',
            array( $this, 'settings_page_content' ),
            'dashicons-admin-generic',
            55.5
        );
	}

	/**
	 * Plugin settings page
	 *
	 * @return void
	 */
	public function settings_page_content() {
		?>
		<div class="wrap">
			<div id="PluginStubSettings"></div>
		</div>
		<?php
	}
}