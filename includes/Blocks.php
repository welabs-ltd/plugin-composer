<?php

namespace WeLabs\PluginComposer;

/**
 * Registers Gutenberg blocks bundled with Plugin Composer.
 */
class Blocks {

	/**
	 * Block attribute (camelCase) => shortcode attribute (snake_case).
	 */
	protected const PLACEHOLDER_ATTRIBUTE_MAP = array(
		'placeholderPluginName'        => 'placeholder_plugin_name',
		'placeholderPluginDescription' => 'placeholder_plugin_description',
		'placeholderPluginRequires'    => 'placeholder_plugin_requires',
		'placeholderPluginLicense'     => 'placeholder_plugin_license',
		'placeholderPluginUri'         => 'placeholder_plugin_uri',
		'placeholderPluginAuthorName'  => 'placeholder_plugin_author_name',
		'placeholderPluginAuthorEmail' => 'placeholder_plugin_author_email',
		'placeholderPluginAuthorUri'   => 'placeholder_plugin_author_uri',
	);

	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	public function register_blocks(): void {
		$blocks_dir = PLUGIN_COMPOSER_DIR . '/dist/blocks';

		if ( ! is_dir( $blocks_dir ) ) {
			return;
		}

		register_block_type(
			$blocks_dir . '/plugin-composer',
			array(
				'render_callback' => array( $this, 'render_plugin_composer' ),
			)
		);
	}

	/**
	 * Server-side render for welabs/plugin-composer.
	 *
	 * Delegates to [wlb_plugin_composer] so the form template, validation,
	 * and submission handling stay in one place.
	 */
	public function render_plugin_composer( array $attributes ): string {
		$show_settings = ! isset( $attributes['showSettingsField'] ) || (bool) $attributes['showSettingsField'];
		$show_wpvip    = ! isset( $attributes['showWpvipField'] ) || (bool) $attributes['showWpvipField'];

		$pairs = array(
			sprintf( 'submit-text="%s"', esc_attr( $attributes['submitText'] ?? '' ) ),
			sprintf( 'class="%s"', esc_attr( $attributes['className'] ?? '' ) ),
			sprintf( 'show_settings_field="%s"', $show_settings ? 'yes' : 'no' ),
			sprintf( 'show_wpvip_field="%s"', $show_wpvip ? 'yes' : 'no' ),
		);

		$color_map = array(
			'buttonBgColor'        => 'button_bg_color',
			'buttonTextColor'      => 'button_text_color',
			'buttonBgHoverColor'   => 'button_bg_hover_color',
			'buttonTextHoverColor' => 'button_text_hover_color',
		);
		foreach ( $color_map as $block_key => $shortcode_key ) {
			$value = isset( $attributes[ $block_key ] ) ? (string) $attributes[ $block_key ] : '';
			if ( $value === '' ) {
				continue;
			}
			$pairs[] = sprintf( '%s="%s"', $shortcode_key, esc_attr( $value ) );
		}

		foreach ( self::PLACEHOLDER_ATTRIBUTE_MAP as $block_key => $shortcode_key ) {
			$value = isset( $attributes[ $block_key ] ) ? (string) $attributes[ $block_key ] : '';
			if ( $value === '' ) {
				continue;
			}
			$pairs[] = sprintf( '%s="%s"', $shortcode_key, esc_attr( $value ) );
		}

		$shortcode = sprintf( '[%s %s]', ShortCode::NAME, implode( ' ', $pairs ) );

		return do_shortcode( $shortcode );
	}
}
