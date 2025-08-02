<?php

namespace WeLabs\PluginComposer\Api\Admin;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;

class SettingsController extends WP_REST_Controller {

    /**
     * Constructor
     */
    public function __construct() {
        $this->namespace = 'plugin-composer/v1';
        $this->rest_base = 'settings';
    }

    /**
     * Register the routes
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [ $this, 'get_settings' ],
                    'permission_callback' => [ $this, 'check_permissions' ],
                ],
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [ $this, 'update_settings' ],
                    'permission_callback' => [ $this, 'check_permissions' ],
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/reset',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [ $this, 'reset_settings' ],
                    'permission_callback' => [ $this, 'check_permissions' ],
                ],
            ]
        );
    }

    /**
     * Check permissions
     */
    public function check_permissions(): bool {
        return current_user_can( 'manage_options' );
    }

    /**
     * Get settings
     */
    public function get_settings(): \WP_REST_Response {
        $settings = [
            'rate_limit_attempts' => get_option( 'plugin_composer_rate_limit_attempts', 5 ),
            'rate_limit_duration' => get_option( 'plugin_composer_rate_limit_duration', HOUR_IN_SECONDS ),
            'max_plugin_name_length' => get_option( 'plugin_composer_max_plugin_name_length', 100 ),
            'max_description_length' => get_option( 'plugin_composer_max_description_length', 500 ),
            'max_license_length' => get_option( 'plugin_composer_max_license_length', 50 ),
            'max_author_name_length' => get_option( 'plugin_composer_max_author_name_length', 100 ),
            'allowed_plugin_types' => get_option( 'plugin_composer_allowed_plugin_types', [ 'classic', 'container_based' ] ),
            'default_plugin_type' => get_option( 'plugin_composer_default_plugin_type', 'container_based' ),
            'file_permissions' => get_option( 'plugin_composer_file_permissions', 0755 ),
            'allowed_file_extensions' => get_option( 'plugin_composer_allowed_file_extensions', [ 'php', 'js', 'css', 'json', 'md', 'txt', 'xml' ] ),
            'required_capability' => get_option( 'plugin_composer_required_capability', 'edit_posts' ),
            'allow_guest_access' => get_option( 'plugin_composer_allow_guest_access', true ),
            'enable_debug_mode' => get_option( 'plugin_composer_enable_debug_mode', false ),
            'auto_cleanup_files' => get_option( 'plugin_composer_auto_cleanup_files', true ),
            'file_cleanup_delay' => get_option( 'plugin_composer_file_cleanup_delay', 30 ),
            'enable_plugin_preview' => get_option( 'plugin_composer_enable_plugin_preview', false ),
            'default_namespace' => get_option( 'plugin_composer_default_namespace', 'MyPlugin' ),
            'default_author_name' => get_option( 'plugin_composer_default_author_name', 'Your Name' ),
            'default_author_url' => get_option( 'plugin_composer_default_author_url', 'https://example.com' ),
        ];

        return new \WP_REST_Response( $settings, 200 );
    }

    /**
     * Update settings
     */
    public function update_settings( \WP_REST_Request $request ): \WP_REST_Response {
        $params = $request->get_params();
        $errors = [];

        $settings_to_update = [
            'rate_limit_attempts' => [
                'sanitize' => 'intval',
                'validate' => function ( $value ) {
                    return $value >= 1 && $value <= 100;
                },
                'error_message' => __( 'Rate limit attempts must be between 1 and 100.', 'welabs-plugin-composer' ),
            ],
            'rate_limit_duration' => [
                'sanitize' => 'intval',
                'validate' => function ( $value ) {
                    return $value >= 60 && $value <= 86400; // 1 minute to 24 hours
                },
                'error_message' => __( 'Rate limit duration must be between 60 and 86400 seconds.', 'welabs-plugin-composer' ),
            ],
            'max_plugin_name_length' => [
                'sanitize' => 'intval',
                'validate' => function ( $value ) {
                    return $value >= 10 && $value <= 200;
                },
                'error_message' => __( 'Plugin name length must be between 10 and 200 characters.', 'welabs-plugin-composer' ),
            ],
            'max_description_length' => [
                'sanitize' => 'intval',
                'validate' => function ( $value ) {
                    return $value >= 50 && $value <= 2000;
                },
                'error_message' => __( 'Description length must be between 50 and 2000 characters.', 'welabs-plugin-composer' ),
            ],
            'max_license_length' => [
                'sanitize' => 'intval',
                'validate' => function ( $value ) {
                    return $value >= 10 && $value <= 100;
                },
                'error_message' => __( 'License length must be between 10 and 100 characters.', 'welabs-plugin-composer' ),
            ],
            'max_author_name_length' => [
                'sanitize' => 'intval',
                'validate' => function ( $value ) {
                    return $value >= 10 && $value <= 200;
                },
                'error_message' => __( 'Author name length must be between 10 and 200 characters.', 'welabs-plugin-composer' ),
            ],
            'allowed_plugin_types' => [
                'sanitize' => 'array',
                'validate' => function ( $value ) {
                    $allowed_types = [ 'container_based', 'classic' ];
                    return ! empty( $value ) && count( $value ) >= 1 && array_intersect( $value, $allowed_types ) === $value;
                },
                'error_message' => __( 'At least one plugin type must be selected and all types must be valid.', 'welabs-plugin-composer' ),
            ],
            'default_plugin_type' => [
                'sanitize' => 'sanitize_text_field',
                'validate' => function ( $value ) use ( $params ) {
                    $allowed_types = [ 'container_based', 'classic' ];
                    $allowed_plugin_types = isset( $params['allowed_plugin_types'] ) ? $params['allowed_plugin_types'] : get_option( 'plugin_composer_allowed_plugin_types', [ 'container_based', 'classic' ] );
                    return in_array( $value, $allowed_types ) && in_array( $value, $allowed_plugin_types );
                },
                'error_message' => __( 'Default plugin type must be valid and must be one of the allowed plugin types.', 'welabs-plugin-composer' ),
            ],
            'file_permissions' => [
                'sanitize' => 'intval',
                'validate' => function ( $value ) {
                    return $value >= 400 && $value <= 777;
                },
                'error_message' => __( 'File permissions must be between 400 and 777.', 'welabs-plugin-composer' ),
            ],
            'allowed_file_extensions' => [
                'sanitize' => 'array',
                'validate' => function ( $value ) {
                    return ! empty( $value ) && count( $value ) <= 20;
                },
                'error_message' => __( 'Allowed file extensions must not be empty and limited to 20 extensions.', 'welabs-plugin-composer' ),
            ],
            'required_capability' => [
                'sanitize' => 'sanitize_text_field',
                'validate' => function ( $value ) {
                    return ! empty( $value ) && current_user_can( $value );
                },
                'error_message' => __( 'Required capability must be valid and accessible.', 'welabs-plugin-composer' ),
            ],
            'allow_guest_access' => [
                'sanitize' => 'boolval',
                'validate' => function ( $value ) {
                    return is_bool( $value );
                },
                'error_message' => __( 'Allow guest access must be a boolean value.', 'welabs-plugin-composer' ),
            ],
            'enable_debug_mode' => [
                'sanitize' => 'boolval',
                'validate' => function ( $value ) {
                    return is_bool( $value );
                },
                'error_message' => __( 'Debug mode must be a boolean value.', 'welabs-plugin-composer' ),
            ],
            'auto_cleanup_files' => [
                'sanitize' => 'boolval',
                'validate' => function ( $value ) {
                    return is_bool( $value );
                },
                'error_message' => __( 'Auto cleanup files must be a boolean value.', 'welabs-plugin-composer' ),
            ],
            'file_cleanup_delay' => [
                'sanitize' => 'intval',
                'validate' => function ( $value ) {
                    return $value >= 1 && $value <= 1440; // 1 minute to 24 hours
                },
                'error_message' => __( 'File cleanup delay must be between 1 and 1440 minutes.', 'welabs-plugin-composer' ),
            ],
            'enable_plugin_preview' => [
                'sanitize' => 'boolval',
                'validate' => function ( $value ) {
                    return is_bool( $value );
                },
                'error_message' => __( 'Plugin preview must be a boolean value.', 'welabs-plugin-composer' ),
            ],
            'default_namespace' => [
                'sanitize' => 'sanitize_text_field',
                'validate' => function ( $value ) {
                    return ! empty( $value ) && preg_match( '/^[A-Z][a-zA-Z0-9_]*(\/[A-Z][a-zA-Z0-9_]*)*$/', $value );
                },
                'error_message' => __( 'Default namespace must not be empty and must start with a capital letter and contain only letters, numbers, and underscores. Multi-word namespaces should use forward slashes (e.g., AB/AC).', 'welabs-plugin-composer' ),
            ],
            'default_author_name' => [
                'sanitize' => 'sanitize_text_field',
                'validate' => function ( $value ) {
                    return ! empty( $value ) && strlen( $value ) <= 100;
                },
                'error_message' => __( 'Default author name must not be empty and must be 100 characters or less.', 'welabs-plugin-composer' ),
            ],
            'default_author_url' => [
                'sanitize' => 'esc_url_raw',
                'validate' => function ( $value ) {
                    return ! empty( $value ) && filter_var( $value, FILTER_VALIDATE_URL );
                },
                'error_message' => __( 'Default author URL must be a valid URL.', 'welabs-plugin-composer' ),
            ],
        ];

        foreach ( $settings_to_update as $key => $config ) {
            if ( isset( $params[ $key ] ) ) {
                $value = $params[ $key ];

                // Sanitize the value
                if ( $config['sanitize'] === 'array' ) {
                    $value = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : [];
                } elseif ( $config['sanitize'] === 'boolval' ) {
                    $value = (bool) $value;
                } else {
                    $value = $config['sanitize']( $value );
                }

                // Validate the value
                if ( ! $config['validate']( $value ) ) {
                    $errors[] = $config['error_message'];
                    continue;
                }

                update_option( 'plugin_composer_' . $key, $value );
            }
        }

        if ( ! empty( $errors ) ) {
            return new \WP_REST_Response(
                [
                    'success' => false,
                    'errors' => $errors,
                ], 400
            );
        }

        return new \WP_REST_Response( [ 'success' => true ], 200 );
    }

    /**
     * Reset settings to defaults
     */
    public function reset_settings(): \WP_REST_Response {
        $default_settings = [
            'rate_limit_attempts' => 5,
            'rate_limit_duration' => HOUR_IN_SECONDS,
            'max_plugin_name_length' => 100,
            'max_description_length' => 500,
            'max_license_length' => 50,
            'max_author_name_length' => 100,
            'allowed_plugin_types' => [ 'classic', 'container_based' ],
            'default_plugin_type' => 'container_based',
            'file_permissions' => 0755,
            'allowed_file_extensions' => [ 'php', 'js', 'css', 'json', 'md', 'txt', 'xml' ],
            'required_capability' => 'edit_posts',
            'allow_guest_access' => true,
            'enable_debug_mode' => false,
            'auto_cleanup_files' => true,
            'file_cleanup_delay' => 30,
            'enable_plugin_preview' => false,
            'default_namespace' => 'MyPlugin',
            'default_author_name' => 'Your Name',
            'default_author_url' => 'https://example.com',
        ];

        foreach ( $default_settings as $key => $value ) {
            update_option( 'plugin_composer_' . $key, $value );
        }

        return new \WP_REST_Response( [ 'success' => true ], 200 );
    }
}
