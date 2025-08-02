<?php
/**
 * Test user permissions
 */

echo 'Current user ID: ' . get_current_user_id() . "\n";
echo 'Current user can manage_options: ' . ( current_user_can( 'manage_options' ) ? 'YES' : 'NO' ) . "\n";
echo 'Current user can edit_posts: ' . ( current_user_can( 'edit_posts' ) ? 'YES' : 'NO' ) . "\n";
echo 'Current user can edit_pages: ' . ( current_user_can( 'edit_pages' ) ? 'YES' : 'NO' ) . "\n";

// List all user capabilities
$user = wp_get_current_user();
echo 'User roles: ' . implode( ', ', $user->roles ) . "\n";
echo 'User capabilities: ' . implode( ', ', array_keys( $user->allcaps ) ) . "\n";
