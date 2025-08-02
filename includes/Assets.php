<?php

namespace WeLabs\PluginComposer;

use WeLabs\PluginComposer\Contracts\Hookable;

class Assets implements Hookable {
    public function register_hooks(): void {
        add_action( 'init', [ $this, 'register_scripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    public function register_scripts() {
        $file = PLUGIN_COMPOSER_DIR . '/assets/style.css';
        $file_url = PLUGIN_COMPOSER_PLUGIN_ASSET . '/style.css';

        wp_register_style( 'welabs_plugin_composer_style', $file_url, [], filemtime( $file ) );
    }

    public function enqueue_scripts() {
        global $post;

        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, ShortCode::NAME ) ) {
			wp_enqueue_style( 'welabs_plugin_composer_style' );
		}
    }
}
