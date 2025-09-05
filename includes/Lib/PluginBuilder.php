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
        'plugin_is_settings_included' => 'no',
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

        $this->get_stub_plugin_settings_files_and_folders( $dest_dir );
        $this->process_stub_plugin_settings( $dest_dir );
        // $this->process_stub_plugin_settings_node_commands( $dest_dir );
        $this->replace_stub_plugin_settings( $dest_dir . '/README.md', "NODE_DEVELOPMENT_COMMANDS", "npm install\nnpm run start" );
        
        $this->replace_stub_plugin_settings( $dest_dir . '/README.md', "NODE_PRODUCTION_COMMANDS", "npm install\nnpm run build" );

        $this->replace_stub_plugin_settings( $dest_dir . '/bin/build.sh', "NODE_PRODUCTION_COMMANDS", "status 'Installing npm dependencies... 📦'\nnpm install\nnpm run build" );
        
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
     * @type string    $plugin_is_settings_included           Whether to include plugin settings.
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

    /**
     * Copy settings related files and folders if settings is included
     *
     * @param string $dest_dir
     * @return void
     */
    public function get_stub_plugin_settings_files_and_folders( $dest_dir ): void {
        if ( $this->placeholders['plugin_is_settings_included'] === 'yes' ) {
            foreach ( $this->settings_files_and_folders as $item ) {
                $src = $this->get_stub_plugin_path() . '/' . $item;
                $dest = $dest_dir . '/' . $item;
                $this->file_system->copy( $src, $dest );
            }
        }
    }

    /**
     * Process settings related code in PluginStub.php
     *
     * @param string $dest_dir
     * @return void
     */
    public function process_stub_plugin_settings( $dest_dir ): void {
        $plugin_stub_path = $dest_dir . '/includes/PluginStub.php';
        $plugin_stub_content = file_get_contents( $plugin_stub_path );
        
        if ( $this->placeholders['plugin_is_settings_included'] === 'yes' ) {
            // Add Register settings REST route code
            $register_rest_route_code = <<<PHP
            \$this->container['admin_settings_rest']->register_routes();
            PHP;

            // Add Init settings classes code
            $init_settings_classes_code = <<<PHP
            \$this->container['admin_settings'] = new Admin\Settings();
            \t\t\$this->container['admin_settings_rest'] = new Admin\REST\SettingsController();
            PHP;
        } else {
            $register_rest_route_code = '// Register your REST routes here';
            $init_settings_classes_code = '';
        }
        
        // Replace placeholders in PluginStub.php
        $plugin_stub_content = str_replace( '// REGISTER_SETTINGS_REST_ROUTE', $register_rest_route_code, $plugin_stub_content );

        if ( empty( $init_settings_classes_code ) ) {
            // Remove the whole line containing the placeholder
            $plugin_stub_content = preg_replace('/^[ \t]*\/\/ INIT_PLUGIN_SETTINGS_CLASSES.*\R?/m', '', $plugin_stub_content);
        } else {
            $plugin_stub_content = str_replace( '// INIT_PLUGIN_SETTINGS_CLASSES', $init_settings_classes_code, $plugin_stub_content );
        }
        file_put_contents( $plugin_stub_path, $plugin_stub_content );
    }

    /**
     * Replace settings related node commands.
     *
     * @param string $dest_dir
     * @return void
     */
    public function replace_stub_plugin_settings( $file_path, $replace_to, $replace_by ): void {
        $readme_content = file_get_contents($file_path);
        
        if ( $this->placeholders['plugin_is_settings_included'] === 'yes' ) {
            $replace_to_content = $replace_by;
        } else {
            $replace_to_content = '';
        }

        $readme_content = str_replace($replace_to, $replace_to_content, $readme_content);
        file_put_contents($file_path, $readme_content);
    }
}
