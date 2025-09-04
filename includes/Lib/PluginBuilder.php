<?php

namespace WeLabs\PluginComposer\Lib;

use WeLabs\PluginComposer\Contracts\BuilderContract;
use WeLabs\PluginComposer\Contracts\FileSystemContract;

class PluginBuilder implements BuilderContract {

    /**
     * @var \WeLabs\PluginComposer\Contracts\FileSystemContract
     */
    protected $file_system;

    protected $placeholders = [
        'plugin_description' => 'Custom plugin by weLabs',
        'plugin_license' => 'GPL2',
        'plugin_uri' => 'https://welabs.dev',
        'plugin_author_name' => 'WeLabs',
        'plugin_author_email' => 'contact@welabs.dev',
        'plugin_author_uri' => 'https://welabs.dev',
        'plugin_requires' => '',
    ];

    protected $required_files_and_folders = [
        '.github',
        'assets',
        'bin',
        'includes/Assets.php',
        'includes/PluginStub.php',
        'templates',
        '.gitignore',
        'composer.json',
        'phpcs.xml',
        'plugin-stub.php',
        'README.md',
    ];

    protected $settings_files_and_folders = [
        'src',
        'includes/Admin',
        'package.json',
        'postcss.config.js',
        'tailwind.config.js',
        'webpack.config.js',
    ];

    public function __construct( FileSystemContract $file_system ) {
        $this->file_system = $file_system;
    }

    public function build( $plugin_name ): string {
        $plugin_dir_name = $this->get_plugin_directory_name( $plugin_name );
        $dest_dir = $this->get_dest_plugin_path( $plugin_dir_name );

        // Copy only required files and folders
        foreach ( $this->required_files_and_folders as $item ) {
            $src = $this->get_stub_plugin_path() . '/' . $item;
            $dest = $dest_dir . '/' . $item;
            $this->file_system->copy( $src, $dest );
        }

        // Conditionally copy settings assets
        $this->placeholders['include_settings'] = true; // need to implement checkbox on the composer form shortcode
        if ( $this->placeholders['include_settings'] ?? false ) {
            foreach ( $this->settings_files_and_folders as $item ) {
                $src = $this->get_stub_plugin_path() . '/' . $item;
                $dest = $dest_dir . '/' . $item;
                $this->file_system->copy( $src, $dest );
            }
        }

        $zip_path = $dest_dir . time() . '.zip';

        $placeholders = $this->get_placeholders( $plugin_name );
        $plugin_class_name = $placeholders['PluginStub'];

        $this->file_system->replace( $dest_dir, $placeholders );
        $this->file_system->rename( $dest_dir . '/plugin-stub.php', $dest_dir . '/' . $plugin_dir_name . '.php' );
        $this->file_system->rename( $dest_dir . '/includes/PluginStub.php', $dest_dir . '/includes/' . $plugin_class_name . '.php' );
        $this->file_system->zip( $dest_dir, $zip_path );
        $this->file_system->remove( $dest_dir );

        return $zip_path;
    }

    protected function get_stub_plugin_path(): string {
        return PLUGIN_COMPOSER_DIR . '/plugin-stub';
    }

    protected function get_dest_plugin_path( $plugin_dir_name ): string {
        return PLUGIN_COMPOSER_DIR . '/' . $plugin_dir_name;
    }

    /**
     * Set the information of the plugin.
     *
     * @param array $plugin_info {
     *                           Optional. Array or string of Plugin information.
     *
     * @type string    $plugin_description The description of the plugin.
     * @type string    $plugin_license     The plugin under the license like GPL2, MIT, etc.
     * @type string    $plugin_uri         The URI of the plugin.
     * @type string    $plugin_author_name        The name of the plugin author.
     * @type string    $plugin_author_email       The email of the plugin author.
     * @type string    $plugin_author_uri         The url of the plugin author profile.
     * @type string    $plugin_requires           Comma separated require plugins slug.
     * }
     *
     * @return void
     */
    public function set_placeholders( array $plugin_info ): void {
        $this->placeholders = wp_parse_args( $plugin_info, $this->placeholders );
    }

    public function get_placeholders( $plugin_name ): array {
        $plugin_name = $this->get_plugin_directory_name( $plugin_name );
        $plugin_name = str_replace( '-', ' ', $plugin_name );
        $default = [
            'Plugin_Stub' => str_replace( ' ', '_', ucwords( $plugin_name ) ),
            'PluginStub' => str_replace( ' ', '', ucwords( $plugin_name ) ),
            'Plugin_stub' => str_replace( ' ', '_', ucwords( $plugin_name ) ),
            'plugin_stub' => str_replace( ' ', '_', strtolower( $plugin_name ) ),
            'PLUGIN_STUB' => str_replace( ' ', '_', strtoupper( $plugin_name ) ),
            'Plugin Stub'  => ucwords( $plugin_name ),
            'plugin-stub' => str_replace( ' ', '-', strtolower( $plugin_name ) ),
        ];

        return array_merge( $default, $this->placeholders );
    }

    protected function get_plugin_directory_name( $plugin_name ): string {
        return sanitize_title( $plugin_name );
    }
}
