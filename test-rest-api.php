<?php
/**
 * Test REST API endpoints
 */

// Test GET settings
echo "Testing GET /plugin-composer/v1/settings\n";
$get_response = wp_remote_get( home_url( '/wp-json/plugin-composer/v1/settings' ) );
if ( is_wp_error( $get_response ) ) {
    echo 'GET Error: ' . $get_response->get_error_message() . "\n";
} else {
    $get_body = wp_remote_retrieve_body( $get_response );
    $get_data = json_decode( $get_body, true );
    echo 'GET Response: ' . print_r( $get_data, true ) . "\n";
}

// Test POST settings
echo "\nTesting POST /plugin-composer/v1/settings\n";
$post_data = [
    'rate_limit_attempts' => 25,
    'rate_limit_duration' => 1800,
    'max_plugin_name_length' => 150,
    'max_description_length' => 1000,
    'max_license_length' => 75,
    'max_author_name_length' => 150,
    'allowed_plugin_types' => [ 'classic', 'container_based' ],
    'default_plugin_type' => 'classic',
    'file_permissions' => 644,
    'allowed_file_extensions' => [ 'php', 'js', 'css', 'json', 'md', 'txt', 'xml' ],
    'required_capability' => 'edit_posts',
    'allow_guest_access' => false,
    'enable_debug_mode' => true,
    'auto_cleanup_files' => true,
    'file_cleanup_delay' => 60,
    'enable_plugin_preview' => true,
    'default_namespace' => 'TestPlugin',
    'default_author_name' => 'Test Author',
    'default_author_url' => 'https://test.example.com',
];

$post_response = wp_remote_post(
    home_url( '/wp-json/plugin-composer/v1/settings' ), [
		'headers' => [
			'Content-Type' => 'application/json',
		],
		'body' => json_encode( $post_data ),
	]
);

if ( is_wp_error( $post_response ) ) {
    echo 'POST Error: ' . $post_response->get_error_message() . "\n";
} else {
    $post_body = wp_remote_retrieve_body( $post_response );
    $post_data = json_decode( $post_body, true );
    echo 'POST Response: ' . print_r( $post_data, true ) . "\n";
}

// Test GET settings again to verify they were saved
echo "\nTesting GET /plugin-composer/v1/settings again\n";
$get_response2 = wp_remote_get( home_url( '/wp-json/plugin-composer/v1/settings' ) );
if ( is_wp_error( $get_response2 ) ) {
    echo 'GET Error: ' . $get_response2->get_error_message() . "\n";
} else {
    $get_body2 = wp_remote_retrieve_body( $get_response2 );
    $get_data2 = json_decode( $get_body2, true );
    echo 'GET Response after POST: ' . print_r( $get_data2, true ) . "\n";
}
