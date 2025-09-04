<?php

namespace WeLabs\PluginStub\Admin\REST;

use WP_REST_Controller;
use WP_REST_Server;

/**
 * Admin settings REST API controller.
 */
class SettingsController extends WP_REST_Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * The base of this controller's route.
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Constructor.
	 *
	 * Sets the namespace and rest base for the controller.
	 */
	public function __construct() {
		$this->namespace = 'plugin-stub/v1';
		$this->rest_base = 'settings';
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'get_settings_permissions_check' ),
					'args'                => array(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'update_settings_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
			)
		);
	}

	/**
	 * Get the settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error The response or error object.
	 */
	public function get_settings( $request ) {
		$settings = get_option( 'plugin_stub_settings', array() );

		return rest_ensure_response( $settings );
	}

	/**
	 * Update the settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error The response or error object.
	 */
	public function update_settings( $request ) {
		$plugin_stub_settings = get_option( 'plugin_stub_settings', array() );

		if ( $request->has_param( 'plugin_stub_dashboard_page_id' ) ) {
			$plugin_stub_settings['plugin_stub_dashboard_page_id'] = sanitize_text_field( $request->get_param( 'plugin_stub_dashboard_page_id' ) );
		}

		if ( $request->has_param( 'plugin_stub_product_per_page' ) ) {
			$plugin_stub_settings['plugin_stub_product_per_page'] = sanitize_text_field( $request->get_param( 'plugin_stub_product_per_page' ) );
		}

		if ( $request->has_param( 'plugin_stub_page_title' ) ) {
			$plugin_stub_settings['plugin_stub_page_title'] = sanitize_text_field( $request->get_param( 'plugin_stub_page_title' ) );
		}

		update_option( 'plugin_stub_settings', $plugin_stub_settings );

		return $this->get_settings( $request );
	}

	/**
	 * Check if a given request has access to get the settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool True if the request has access, false otherwise.
	 */
	public function get_settings_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check if a given request has access to update the settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool True if the request has access, false otherwise.
	 */
	public function update_settings_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get the schema for a single item, if any.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => array(
				'plugin_stub_dashboard_page_id' => array(
					'description' => __( 'Dashboard Page.', 'plugin-stub' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'plugin_stub_product_per_page'  => array(
					'description' => __( 'Products Per Page.', 'plugin-stub' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'plugin_stub_page_title'  => array(
					'description' => __( 'Page Title.', 'plugin-stub' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
			),
		);
	}
}
