<?php

namespace WeLabs\PluginComposer\Admin;

use WeLabs\PluginComposer\Contracts\Hookable;

class Settings implements Hookable {
    /**
     * Settings page slug
     */
    const PAGE_SLUG = 'plugin-composer-settings';

    /**
     * Register hooks
     */
    public function register_hooks(): void {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu(): void {
        add_options_page(
            __( 'Plugin Composer Settings', 'welabs-plugin-composer' ),
            __( 'Plugin Composer', 'welabs-plugin-composer' ),
            'manage_options',
            self::PAGE_SLUG,
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts( string $hook ): void {
        if ( 'settings_page_' . self::PAGE_SLUG !== $hook ) {
            return;
        }

        wp_enqueue_script(
            'plugin-composer-admin',
            PLUGIN_COMPOSER_PLUGIN_ASSET . '/admin/settings.js',
            [ 'wp-element', 'wp-components', 'wp-api-fetch', 'wp-i18n', 'wp-dom-ready' ],
            PLUGIN_COMPOSER_PLUGIN_VERSION,
            true
        );

        wp_enqueue_style(
            'plugin-composer-admin',
            PLUGIN_COMPOSER_PLUGIN_ASSET . '/admin/settings.css',
            [ 'wp-components' ],
            PLUGIN_COMPOSER_PLUGIN_VERSION
        );

        wp_localize_script(
            'plugin-composer-admin', 'pluginComposerSettings', [
				'apiUrl' => rest_url( 'plugin-composer/v1/settings' ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'strings' => [
					'saveSettings' => __( 'Save Settings', 'welabs-plugin-composer' ),
					'settingsSaved' => __( 'Settings saved successfully!', 'welabs-plugin-composer' ),
					'errorSaving' => __( 'Error saving settings.', 'welabs-plugin-composer' ),
					'loading' => __( 'Loading...', 'welabs-plugin-composer' ),
				],
			]
        );
    }

    /**
     * Render settings page
     */
    public function render_settings_page(): void {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Plugin Composer Settings', 'welabs-plugin-composer' ); ?></h1>
            <div id="plugin-composer-settings-app"></div>
        </div>
        <?php
    }
}
