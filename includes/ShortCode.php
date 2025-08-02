<?php

namespace WeLabs\PluginComposer;

use WeLabs\PluginComposer\Contracts\Hookable;
use WeLabs\PluginComposer\Logger;
use WeLabs\PluginComposer\Config;

class ShortCode implements Hookable {
    public const NAME = 'wlb_plugin_composer';
    protected $error_messages = [];

    public function register_hooks(): void {
        add_shortcode( self::NAME, [ $this, 'shortcode' ], 2 );
        add_action( 'template_redirect', [ $this, 'handle_form_submission' ] );
    }

    public function shortcode( $attr, $content ) {
        $attr = shortcode_atts(
            [
				'class' => '',
				'submit-text' => 'Submit',
			], $attr
        );

        // Check if guest access is allowed
        $allow_guest_access = Config::get( 'allow_guest_access', false );

        // Check if user is logged in (only if guest access is not allowed)
        if ( ! $allow_guest_access && ! is_user_logged_in() ) {
            $login_message = apply_filters(
                'plugin_composer_guest_message',
                '<p>' . __( 'You must be logged in to generate plugins.', 'welabs-plugin-composer' ) . '</p>' .
                '<p><a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' . __( 'Click here to log in', 'welabs-plugin-composer' ) . '</a></p>'
            );
            return $content . $login_message;
        }

        // Check user capabilities for display (only for logged-in users)
        if ( is_user_logged_in() ) {
            $required_capability = Config::get( 'required_capability', 'edit_posts' );
            if ( ! current_user_can( $required_capability ) ) {
                $permission_message = apply_filters(
                    'plugin_composer_permission_message',
                    '<p>' . __( 'You do not have sufficient permissions to generate plugins.', 'welabs-plugin-composer' ) . '</p>'
                );
                return $content . $permission_message;
            }
        }

        $error_messages = apply_filters( 'get_welabs_plugin_compose_form_errors', $this->error_messages );
        $form_template = apply_filters( 'get_welabs_plugin_compose_form', PLUGIN_COMPOSER_TEMPLATE_DIR . '/compose-form.php' );

        // Get default values from settings
        $default_values = [
            'plugin_type' => Config::get( 'default_plugin_type', 'container_based' ),
            'author_name' => Config::get( 'default_author_name', 'Your Name' ),
            'author_url' => Config::get( 'default_author_url', 'https://example.com' ),
            'namespace' => Config::get( 'default_namespace', 'MyPlugin' ),
        ];

        ob_start();
        include $form_template;
        $content = $content . ob_get_clean();

        return $content;
    }

    public function handle_form_submission() {
        // Check if form was submitted
        if ( ! isset( $_POST['wlb-compose-plugin'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wlb-compose-plugin'] ), 'wlb-compose-plugin' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
        }

        // Check if guest access is allowed
        $allow_guest_access = Config::get( 'allow_guest_access', false );

        // Check if user is logged in (only if guest access is not allowed)
        if ( ! $allow_guest_access && ! is_user_logged_in() ) {
            wp_die( esc_html__( 'You must be logged in to generate plugins.', 'welabs-plugin-composer' ) );
        }

        // Check user capabilities (only for logged-in users)
        if ( is_user_logged_in() ) {
            $required_capability = Config::get( 'required_capability', 'edit_posts' );
            if ( ! current_user_can( $required_capability ) ) {
                wp_die( esc_html__( 'You do not have sufficient permissions to generate plugins.', 'welabs-plugin-composer' ) );
            }
        }

        // Rate limiting
        $user_id = get_current_user_id();
        if ( $user_id ) {
            // For logged-in users, use user ID
            $rate_limit_key = "plugin_composer_rate_limit_{$user_id}";
        } else {
            // For guest users, use IP address
            $ip = $this->get_client_ip();
            $rate_limit_key = "plugin_composer_rate_limit_guest_{$ip}";
        }

        $attempts = get_transient( $rate_limit_key );
        if ( false === $attempts ) {
            $attempts = 0;
        }

        if ( $attempts > 5 ) {
            wp_die( esc_html__( 'Too many attempts. Please try again later.', 'welabs-plugin-composer' ) );
        }

        set_transient( $rate_limit_key, $attempts + 1, HOUR_IN_SECONDS );

        $post_data = $_POST;

        if ( ! isset( $post_data['plugin_name'] ) ) {
            $this->add_error( 'plugin_name', __( 'Plugin name is required.', 'welabs-plugin-composer' ) );
            return;
        }

        // Validate and sanitize input
        $validation_result = $this->validate_form_data( $post_data );
        if ( ! $validation_result['valid'] ) {
            $this->error_messages = $validation_result['errors'];
            return;
        }

        $request_data = $validation_result['data'];
        $request_data = apply_filters( 'welabs_plugin_composer_form_data', array_filter( $request_data ) );

        $plugin_type = sanitize_text_field( $post_data['plugin_type'] ?? 'container_based' );

        try {
            $plugin_stub_src_dir = $plugin_type === 'classic' ? Constants::CLASSIC_PLUGIN_STUB_SRC_DIR : Constants::PLUGIN_STUB_SRC_DIR;
            $builder = welabs_plugin_composer()->get_builder();
            $builder->set_stub_plugin_path( $plugin_stub_src_dir )
                ->set_placeholders( $request_data );

            $zip_name = $builder->build( $request_data['plugin_name'] );

            if ( ! file_exists( $zip_name ) ) {
                throw new \Exception( __( 'Failed to generate plugin file.', 'plugin-composer' ) );
            }

            $plugin_folder_name = sanitize_title( $request_data['plugin_name'] );

            // Log the plugin generation
            if ( $user_id ) {
                Logger::log_plugin_generation( $request_data['plugin_name'], $user_id, $request_data );
            } else {
                Logger::log_plugin_generation( $request_data['plugin_name'], 0, array_merge( $request_data, [ 'guest_ip' => $this->get_client_ip() ] ) );
            }

            // Serve the file for download
            $this->serve_file_download( $zip_name, $plugin_folder_name . '.zip' );
        } catch ( \Exception $e ) {
            Logger::error(
                'Failed to generate plugin: ' . $e->getMessage(), [
					'plugin_name' => $request_data['plugin_name'] ?? 'Unknown',
					'user_id' => $user_id,
				]
            );
            $this->add_error( 'general', __( 'Failed to generate plugin. Please try again.', 'welabs-plugin-composer' ) );
        }
    }

    /**
     * Validate form data
     *
     * @param array $post_data
     * @return array
     */
    private function validate_form_data( array $post_data ): array {
        $errors = [];
        $data = [];

        // Plugin name validation
        $plugin_name = sanitize_text_field( $post_data['plugin_name'] ?? '' );
        if ( empty( $plugin_name ) ) {
            $errors['plugin_name'] = __( 'Plugin name is required.', 'plugin-composer' );
        } elseif ( strlen( $plugin_name ) > 100 ) {
            $errors['plugin_name'] = __( 'Plugin name must be less than 100 characters.', 'plugin-composer' );
        } elseif ( ! preg_match( '/^[a-zA-Z0-9\s\-_]+$/', $plugin_name ) ) {
            $errors['plugin_name'] = __( 'Plugin name contains invalid characters.', 'plugin-composer' );
        } else {
            $data['plugin_name'] = $plugin_name;
        }

        // Plugin description validation
        $description = sanitize_textarea_field( $post_data['plugin_description'] ?? '' );
        if ( ! empty( $description ) && strlen( $description ) > 500 ) {
            $errors['plugin_description'] = __( 'Plugin description must be less than 500 characters.', 'plugin-composer' );
        } else {
            $data['plugin_description'] = $description;
        }

        // Plugin license validation
        $license = sanitize_text_field( $post_data['plugin_license'] ?? '' );
        if ( ! empty( $license ) && strlen( $license ) > 50 ) {
            $errors['plugin_license'] = __( 'Plugin license must be less than 50 characters.', 'plugin-composer' );
        } else {
            $data['plugin_license'] = $license;
        }

        // Plugin URI validation
        $plugin_uri = esc_url_raw( $post_data['plugin_uri'] ?? '' );
        if ( ! empty( $plugin_uri ) && ! filter_var( $plugin_uri, FILTER_VALIDATE_URL ) ) {
            $errors['plugin_uri'] = __( 'Please enter a valid plugin URL.', 'plugin-composer' );
        } else {
            $data['plugin_uri'] = $plugin_uri;
        }

        // Author name validation
        $author_name = sanitize_text_field( $post_data['plugin_author_name'] ?? '' );
        if ( ! empty( $author_name ) && strlen( $author_name ) > 100 ) {
            $errors['plugin_author_name'] = __( 'Author name must be less than 100 characters.', 'plugin-composer' );
        } else {
            $data['plugin_author_name'] = $author_name;
        }

        // Author email validation
        $author_email = sanitize_email( $post_data['plugin_author_email'] ?? '' );
        if ( ! empty( $author_email ) && ! is_email( $author_email ) ) {
            $errors['plugin_author_email'] = __( 'Please enter a valid email address.', 'plugin-composer' );
        } else {
            $data['plugin_author_email'] = $author_email;
        }

        // Author URI validation
        $author_uri = esc_url_raw( $post_data['plugin_author_uri'] ?? '' );
        if ( ! empty( $author_uri ) && ! filter_var( $author_uri, FILTER_VALIDATE_URL ) ) {
            $errors['plugin_author_uri'] = __( 'Please enter a valid author URL.', 'plugin-composer' );
        } else {
            $data['plugin_author_uri'] = $author_uri;
        }

        // Plugin namespace validation
        $namespace = sanitize_text_field( $post_data['plugin_namespace'] ?? '' );
        if ( ! empty( $namespace ) ) {
            // Support multi-word namespaces like "AB\AC" or single word like "MyPlugin"
            if ( ! preg_match( '/^[A-Z][a-zA-Z0-9_]*(\/[A-Z][a-zA-Z0-9_]*)*$/', $namespace ) ) {
                $errors['plugin_namespace'] = __( 'Namespace must start with a capital letter and contain only letters, numbers, and underscores. Multi-word namespaces should use forward slashes (e.g., AB/AC).', 'plugin-composer' );
            } elseif ( strlen( $namespace ) > 100 ) {
                $errors['plugin_namespace'] = __( 'Namespace must be less than 100 characters.', 'plugin-composer' );
            } else {
                $data['plugin_namespace'] = $namespace;
            }
        } else {
            // Use default namespace if not provided
            $data['plugin_namespace'] = Config::get( 'default_namespace', 'MyPlugin' );
        }

        return [
            'valid' => empty( $errors ),
            'errors' => $errors,
            'data' => $data,
        ];
    }

    /**
     * Add error message
     *
     * @param string $field
     * @param string $message
     */
    private function add_error( string $field, string $message ): void {
        $this->error_messages[ $field ] = $message;
    }



    /**
     * Serve file for download
     *
     * @param string $file_path
     * @param string $filename
     */
    private function serve_file_download( string $file_path, string $filename ): void {
        if ( ! file_exists( $file_path ) ) {
            wp_die( esc_html__( 'File not found.', 'plugin-composer' ) );
        }

        // Validate file path is within allowed directory
        $allowed_path = PLUGIN_COMPOSER_DIR . '/';
        $real_file_path = realpath( $file_path );
        if ( ! $real_file_path || strpos( $real_file_path, $allowed_path ) !== 0 ) {
            wp_die( esc_html__( 'Invalid file path detected.', 'plugin-composer' ) );
        }

        // Set headers for file download
        header( 'Content-Type: application/zip' );
        header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
        header( 'Content-Length: ' . filesize( $file_path ) );
        header( 'Cache-Control: no-cache, must-revalidate' );
        header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );

        // Read and output file
        readfile( $file_path );

        // Clean up
        if ( file_exists( $file_path ) ) {
            unlink( $file_path );
        }

        exit;
    }

    /**
     * Get client IP address
     *
     * @return string
     */
    private function get_client_ip(): string {
        $ip_keys = [ 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ];

        foreach ( $ip_keys as $key ) {
            if ( array_key_exists( $key, $_SERVER ) === true ) {
                $ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
                if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                    return $ip;
                }
            }
        }

        return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' ) );
    }
}
