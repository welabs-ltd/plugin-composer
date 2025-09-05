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
		add_filter( 'plugin_action_links_' . PLUGIN_STUB_BASENAME, array( $this, 'plugin_action_link' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_settings_scripts' ), 10 );
		}
	}

	/**
	 * Register settings main menu
	 *
	 * @return void
	 */
	public function add_admin_settings_menu() {
        add_menu_page(
            __( 'PluginStub Settings', 'plugin-stub' ),
            __( 'PluginStub', 'plugin-stub' ),
            'manage_options',
            'plugin_stub-settings',
            array( $this, 'settings_page_content' ),
            'dashicons-admin-generic',
            55.5
        );
	}

	/**
	 * Add Settings action link on the plugin screen.
	 *
	 * @param mixed $links Plugin Action links.
	 *
	 * @return array
	 */
    public function plugin_action_link( $links ){
        $plugin_action_links = array(
        '<a href="' . esc_url( admin_url( 'admin.php?page=plugin_stub-settings' ) ) . '"> '. __('Settings', 'plugin-stub') . '</a>',
        );
        return array_merge( $links, $plugin_action_links );
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

	/**
	 * Enqueue admin settings scripts
	 *
	 * @return void
	 */
	public function enqueue_admin_settings_scripts() {
		$screen = get_current_screen();
		
		if ( 'toplevel_page_plugin_stub-settings' == $screen->id ) {
			$asset_file_path = PLUGIN_STUB_DIR . '/assets/build/admin/script.asset.php';

			if( file_exists( $asset_file_path ) ) {
				$asset_file = include $asset_file_path;
				wp_enqueue_script(
					'plugin_stub_admin_page',
					PLUGIN_STUB_PLUGIN_ASSET . '/build/admin/script.js',
					$asset_file['dependencies'],
					$asset_file['version'],
					true
				);
	
				wp_enqueue_style(
					'plugin_stub_admin_styles',
					PLUGIN_STUB_PLUGIN_ASSET . '/build/admin.css',
					array( 'wp-components' ),
					$asset_file['version'] ?? null,
				);
	
				wp_enqueue_style( 'wp-components' );
			}
		}
	}
}