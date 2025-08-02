<?php

namespace WeLabs\PluginComposer;

/**
 * Configuration class for Plugin Composer
 */
class Config {
    /**
     * Default plugin settings
     *
     * @var array
     */
    private static $defaults = [
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
    ];

    /**
     * Get configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get( string $key, $default = null ) {
        $value = apply_filters( "plugin_composer_config_{$key}", self::$defaults[ $key ] ?? $default );
        return $value;
    }

    /**
     * Get all configuration
     *
     * @return array
     */
    public static function all(): array {
        return apply_filters( 'plugin_composer_config', self::$defaults );
    }

    /**
     * Set configuration value
     *
     * @param string $key
     * @param mixed $value
     */
    public static function set( string $key, $value ): void {
        self::$defaults[ $key ] = $value;
    }

    /**
     * Get default placeholders
     *
     * @return array
     */
    public static function get_default_placeholders(): array {
        return apply_filters(
            'plugin_composer_default_placeholders', [
				'plugin_description' => 'Custom plugin by weLabs',
				'plugin_license' => 'GPL2',
				'plugin_uri' => 'https://welabs.dev',
				'plugin_author_name' => 'WeLabs',
				'plugin_author_email' => 'contact@welabs.dev',
				'plugin_author_uri' => 'https://welabs.dev',
			]
        );
    }

    /**
     * Get validation rules
     *
     * @return array
     */
    public static function get_validation_rules(): array {
        return apply_filters(
            'plugin_composer_validation_rules', [
				'plugin_name' => [
					'required' => true,
					'max_length' => self::get( 'max_plugin_name_length' ),
					'pattern' => '/^[a-zA-Z0-9\s\-_]+$/',
				],
				'plugin_description' => [
					'required' => false,
					'max_length' => self::get( 'max_description_length' ),
				],
				'plugin_license' => [
					'required' => false,
					'max_length' => self::get( 'max_license_length' ),
				],
				'plugin_uri' => [
					'required' => false,
					'type' => 'url',
				],
				'plugin_author_name' => [
					'required' => false,
					'max_length' => self::get( 'max_author_name_length' ),
				],
				'plugin_author_email' => [
					'required' => false,
					'type' => 'email',
				],
				'plugin_author_uri' => [
					'required' => false,
					'type' => 'url',
				],
			]
        );
    }
}
