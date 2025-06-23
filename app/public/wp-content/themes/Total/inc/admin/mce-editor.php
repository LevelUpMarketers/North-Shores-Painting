<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizations for the WP tinymce editor.
 */
final class Mce_Editor {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Mce_Editor.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		if ( \totaltheme_has_classic_styles() ) {
			\add_filter( 'mce_buttons_2', [ $this, 'enable_fontsizeselect_button' ] );
			\add_filter( 'tiny_mce_before_init', [ $this, 'custom_fontsize_formats' ] );
		}

		if ( \wp_validate_boolean( \get_theme_mod( 'editor_formats_enable', true ) ) ) {
			\add_filter( 'mce_buttons', [ $this, 'enable_styleselect_button' ] );
			\add_filter( 'tiny_mce_before_init', [ $this, 'add_formats' ] );
		}
	}

	/**
	 * Enable the font size button in the editor.
	 */
	public function enable_fontsizeselect_button( $buttons ) {
		\array_push( $buttons, 'fontsizeselect' );
		return $buttons;
	}

	/**
	 * Custom font size options for the editor.
	 */
	public function custom_fontsize_formats( $settings ) {
		$settings['fontsize_formats'] = '10px 13px 14px 16px 18px 21px 24px 28px 32px 36px';
		return $settings;
	}

	/**
	 * Enable the Formats button in the editor.
	 */
	public function enable_styleselect_button( $buttons ) {
		\array_push( $buttons, 'styleselect' );
		return $buttons;
	}

	/**
	 * Adds custom styles to the formats dropdown by altering the $settings.
	 */
	public function add_formats( $settings ) {

		// General.
		$total = (array) \apply_filters( 'wpex_tiny_mce_formats_items', [
			[
				'title'    => \esc_html__( 'Theme Button', 'total' ),
				'selector' => 'a',
				'classes'  => 'theme-button',
			],
			[
				'title'   => \esc_html__( 'Highlight', 'total' ),
				'inline'  => 'span',
				'classes' => 'text-highlight',
			],
			[
				'title'    => \esc_html__( 'Check List', 'total' ),
				'selector' => 'ul',
				'classes'  => 'check-list'
			],
		] );

		// Font Sizes.
		$font_sizes = (array) \apply_filters( 'wpex_tiny_mce_formats_font_sizes', [
			[
				'title' => '7xl',
				'inline' => 'span',
				'classes'  => 'wpex-text-7xl',
			],
			[
				'title' => '6xl',
				'inline' => 'span',
				'classes'  => 'wpex-text-6xl',
			],
			[
				'title' => '5xl',
				'inline' => 'span',
				'classes'  => 'wpex-text-5xl',
			],
			[
				'title' => '4xl',
				'inline' => 'span',
				'classes'  => 'wpex-text-4xl',
			],
			[
				'title' => '3xl',
				'inline' => 'span',
				'classes'  => 'wpex-text-3xl',
			],
			[
				'title' => '2xl',
				'inline' => 'span',
				'classes'  => 'wpex-text-2xl',
			],
			[
				'title' => 'xl',
				'inline' => 'span',
				'classes'  => 'wpex-text-xl',
			],
			[
				'title' => 'lg',
				'inline' => 'span',
				'classes'  => 'wpex-text-lg',
			],
			[
				'title' => 'md',
				'inline' => 'span',
				'classes'  => 'wpex-text-md',
			],
			[
				'title' => 'sm',
				'inline' => 'span',
				'classes'  => 'wpex-text-sm',
			],
		] );

		// Alerts.
		$alerts = (array) \apply_filters( 'wpex_tiny_mce_formats_alerts', [
			[
				'title'   => \esc_html__( 'Info', 'total' ),
				'block'   => 'div',
				'classes' => 'wpex-alert wpex-alert-info',
			],
			[
				'title'   => \esc_html__( 'Success', 'total' ),
				'block'   => 'div',
				'classes' => 'wpex-alert wpex-alert-success',
			],
			[
				'title'   => \esc_html__( 'Warning', 'total' ),
				'block'   => 'div',
				'classes' => 'wpex-alert wpex-alert-warning',
			],
			[
				'title'   => \esc_html__( 'Error', 'total' ),
				'block'   => 'div',
				'classes' => 'wpex-alert wpex-alert-error',
			],
		] );

		// Dropcaps.
		$dropcaps = (array) \apply_filters( 'wpex_tiny_mce_formats_dropcaps', [
			[
				'title'   => \esc_html__( 'Dropcap', 'total' ),
				'inline'  => 'span',
				'classes' => 'dropcap',
			],
			[
				'title'   => \esc_html__( 'Boxed Dropcap', 'total' ),
				'inline'  => 'span',
				'classes' => 'dropcap boxed',
			],
		] );

		// Color buttons.
		$color_buttons = (array) apply_filters( 'wpex_tiny_mce_formats_color_buttons', [
			[
				'title'     => \esc_html__( 'Blue', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button blue',
			],
			[
				'title'     => \esc_html__( 'Black', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button black',
			],
			[
				'title'     => \esc_html__( 'Red', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button red',
			],
			[
				'title'     => \esc_html__( 'Orange', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button orange',
			],
			[
				'title'     => \esc_html__( 'Green', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button green',
			],
			[
				'title'     => \esc_html__( 'Gold', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button gold',
			],
			[
				'title'     => \esc_html__( 'Teal', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button teal',
			],
			[
				'title'     => \esc_html__( 'Purple', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button purple',
			],
			[
				'title'     => \esc_html__( 'Pink', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button pink',
			],
			[
				'title'     => \esc_html__( 'Brown', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button brown',
			],
			[
				'title'     => \esc_html__( 'Rosy', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button rosy',
			],
			[
				'title'     => \esc_html__( 'White', 'total' ),
				'selector'  => 'a',
				'classes'   => 'color-button white',
			],
		] );

		$formats = [];

		if ( $total ) {
			$formats[] = [
				'title' => \esc_html__( 'Theme Styles', 'total' ),
				'items' => (object) $total,
			];
		}

		if ( $font_sizes ) {
			$formats[] = [
				'title' => \esc_html__( 'Font Sizes', 'total' ),
				'items' => (object) $font_sizes,
			];
		}

		if ( $alerts ) {
			$formats[] = [
				'title' => \esc_html__( 'Alerts', 'total' ),
				'items' => (object) $alerts,
			];
		}

		if ( $dropcaps ) {
			$formats[] = [
				'title' => \esc_html__( 'Dropcaps', 'total' ),
				'items' => (object) $dropcaps,
			];
		}

		if ( $color_buttons ) {
			$formats[] = [
				'title' => \esc_html__( 'Color Buttons', 'total' ),
				'items' => (object) $color_buttons,
			];
		}

		if ( $formats ) {
			$settings['style_formats_merge'] = true;
			$settings['style_formats'] = \json_encode( $formats );
		}

		return $settings;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
