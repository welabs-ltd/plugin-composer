<?php
/**
 * Test script to verify settings saving
 */

// Test saving and retrieving options
$test_key = 'plugin_composer_rate_limit_attempts';
$test_value = 15;

echo "Testing WordPress options saving...\n";

// Save the option
$saved = update_option( $test_key, $test_value );
echo 'Option saved: ' . ( $saved ? 'YES' : 'NO' ) . "\n";

// Retrieve the option
$retrieved = get_option( $test_key, 'NOT_FOUND' );
echo 'Option retrieved: ' . $retrieved . "\n";

// Check if it matches
echo 'Values match: ' . ( $retrieved === $test_value ? 'YES' : 'NO' ) . "\n";

// List all plugin_composer options
echo "\nAll plugin_composer options:\n";
global $wpdb;
$options = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'plugin_composer_%'" );
foreach ( $options as $option ) {
    echo "- {$option->option_name}: {$option->option_value}\n";
}
