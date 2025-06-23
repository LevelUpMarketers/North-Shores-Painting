<?php

namespace TotalTheme\Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Register Widget Areas.
 */
final class Register_Widget_Areas {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		\add_action( 'widgets_init', [ self::class, '_register_sidebars' ] );
		\add_action( 'widgets_init', [ self::class, '_register_footer_widgets' ], 40 );
	}

	/**
	 * Register sidebar areas.
	 */
	public static function _register_sidebars() {
		$sidebars = [
			'sidebar' => \esc_html__( 'Main Sidebar', 'total' ),
		];

		if ( \get_theme_mod( 'pages_custom_sidebar', true ) ) {
			$sidebars['pages_sidebar'] = \esc_html__( 'Pages Sidebar', 'total' );
		}

		if ( \get_theme_mod( 'blog_custom_sidebar', false ) ) {
			$sidebars['blog_sidebar'] = \esc_html__( 'Blog Sidebar', 'total' );
		}

		if ( \get_theme_mod( 'search_custom_sidebar', true ) ) {
			$sidebars['search_sidebar'] = \esc_html__( 'Search Results Sidebar', 'total' );
		}

		if ( totaltheme_is_integration_active( 'woocommerce' )
			&& \get_theme_mod( 'woo_custom_sidebar', true )
		) {
			$sidebars['woo_sidebar'] = \esc_html__( 'WooCommerce Sidebar', 'total' );
		}

		$sidebars = (array) \apply_filters( 'wpex_register_sidebars_array', $sidebars );

		foreach ( $sidebars as $k => $v ) {
			if ( is_array( $v ) ) {
				$args = $args;
			} else {
				$args = [
					'id'   => $k,
					'name' => $v,
				];
			}
			\totaltheme_call_static( 'Sidebars\Primary', 'register_sidebar', $args );
		}
	}

	/**
	 * Register footer areas.
	 */
	public static function _register_footer_widgets() {
		if ( \totaltheme_call_static( 'Footer\Core', 'is_custom' ) ) {
			$maybe_register = \get_theme_mod( 'footer_builder_footer_widgets', false );
		} else {
			$maybe_register = \get_theme_mod( 'footer_widgets', true );
		}

		$maybe_register = (bool) \apply_filters( 'wpex_register_footer_sidebars', $maybe_register );

		if ( ! $maybe_register ) {
			return;
		}

		// Footer widget columns.
		$footer_columns = (int) \get_theme_mod( 'footer_widgets_columns', 4 );

		// Check if we are in the customizer.
		$customizing = (bool) \is_customize_preview();

		// Footer 1.
		\totaltheme_init_class( 'Helpers\Register_Widget_Area', 'footer', [
			'name' => \esc_html__( 'Footer Column 1', 'total' ),
			'id'   => 'footer_one',
		] );

		// Footer 2.
		if ( $footer_columns > 1 || $customizing ) {
			\totaltheme_init_class( 'Helpers\Register_Widget_Area', 'footer', [
				'name' => \esc_html__( 'Footer Column 2', 'total' ),
				'id' => 'footer_two',
			] );
		}

		// Footer 3.
		if ( $footer_columns > 2 || $customizing ) {
			\totaltheme_init_class( 'Helpers\Register_Widget_Area', 'footer', [
				'name' => \esc_html__( 'Footer Column 3', 'total' ),
				'id' => 'footer_three',
			] );
		}

		// Footer 4.
		if ( $footer_columns > 3 || $customizing ) {
			\totaltheme_init_class( 'Helpers\Register_Widget_Area', 'footer', [
				'name' => \esc_html__( 'Footer Column 4', 'total' ),
				'id' => 'footer_four',
			] );
		}

		// Footer 5.
		if ( $footer_columns > 4 || $customizing ) {
			\totaltheme_init_class( 'Helpers\Register_Widget_Area', 'footer', [
				'name' => \esc_html__( 'Footer Column 5', 'total' ),
				'id' => 'footer_five',
			] );
		}

		// Footer 6.
		if ( $footer_columns > 5 || $customizing ) {
			\totaltheme_init_class( 'Helpers\Register_Widget_Area', 'footer', [
				'name' => \esc_html__( 'Footer Column 6', 'total' ),
				'id' => 'footer_six',
			] );
		}
	}

}
