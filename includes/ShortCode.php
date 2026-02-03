<?php

namespace WeLabs\PluginComposer;

class ShortCode {
    public const NAME = 'wlb_plugin_composer';
    protected $error_messages = [];

    public function __construct() {
        add_shortcode( self::NAME, [ $this, 'shortcode' ], 2 );
        add_action( 'template_redirect', [ $this, 'handle_form_submission' ] );
    }

    public function shortcode( $attr, $content ) {
        $attr = shortcode_atts(
            [
				'class' => '',
				'submit-text' => __( 'Build Plugin', 'plugin-composer' ),
			], $attr
        );
        $error_messages = apply_filters( 'get_welabs_plugin_compose_form_errors', $this->error_messages );
        $form_template = apply_filters( 'get_welabs_plugin_compose_form', PLUGIN_COMPOSER_TEMPLATE_DIR . '/compose-form.php' );

        ob_start();
        include $form_template;
        $content = $content . ob_get_clean();

        return $content;
    }

    public function handle_form_submission() {
        if ( ! isset( $_POST['wlb-compose-plugin'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wlb-compose-plugin'] ), 'wlb-compose-plugin' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
        }

        $post_data = $_POST;

        if ( ! isset( $post_data['plugin_name'] ) ) {
            return;
        }

        $plugin_name = sanitize_title( $post_data['plugin_name'] ?? '' );

        if ( isset( $post_data['plugin_name'] ) && $plugin_name === '' ) {
            $this->error_messages = [
                'plugin_name' => __( 'Plugin name is required.', 'welabs-plugin-composer' ),
            ];
            return;
        }

        $request_data['plugin_name'] = sanitize_text_field( $post_data['plugin_name'] ?? '' );
        $request_data['plugin_description'] = sanitize_text_field( $post_data['plugin_description'] ?? '' );
        $request_data['plugin_license'] = sanitize_text_field( $post_data['plugin_license'] ?? '' );
        $request_data['plugin_uri'] = sanitize_text_field( $post_data['plugin_uri'] ?? '' );

        $request_data['plugin_author_name'] = sanitize_text_field( $post_data['plugin_author_name'] ?? '' );
        $request_data['plugin_author_email'] = sanitize_text_field( $post_data['plugin_author_email'] ?? '' );
        $request_data['plugin_author_uri'] = sanitize_text_field( $post_data['plugin_uri'] ?? '' );
        $request_data['plugin_requires'] = sanitize_text_field( $post_data['plugin_requires'] ?? '' );
        $request_data['plugin_is_settings_included'] = sanitize_text_field( $post_data['plugin_is_settings_included'] ?? 'no' );
        $request_data['plugin_is_wpvip_supported'] = sanitize_text_field( $post_data['plugin_is_wpvip_supported'] ?? 'no' );

        $request_data = apply_filters( 'welabs_plugin_composer_form_data', array_filter( $request_data ) );

        $builder = welabs_plugin_composer()->get_builder();
        $builder->set_placeholders( $request_data );
        $zip_name = $builder->build( $request_data['plugin_name'] );

        $plugin_folder_name = sanitize_title( $request_data['plugin_name'] );

        header( 'Content-type: application/zip' ); //this could be a different header
        header( 'Content-Disposition: attachment; filename="' . $plugin_folder_name . '.zip"' );

        ignore_user_abort( true );

        $context = stream_context_create();

        $file = fopen( $zip_name, 'rb', false, $context );

        while ( ! feof( $file ) ) {
            echo stream_get_contents( $file, 2014 );
        }

        fclose( $file );

        flush();

        if ( file_exists( $zip_name ) ) {
            unlink( $zip_name );
        }

        wp_safe_redirect( '' );
    }
}
