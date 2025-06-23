<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into the after_setup_theme hook.
 * 
 */
class After_Setup_Theme {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback() {
		\load_theme_textdomain( 'total', \WPEX_THEME_DIR . '/languages' );
		\add_post_type_support( 'page', 'excerpt' );
		self::set_content_width_global_var();
		self::add_theme_support();
		self::register_nav_menus();
	}

	/**
	 * Content width.
	 */
	public static function set_content_width_global_var() {
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$custom_width = \get_theme_mod( 'main_container_width' );
			$content_width = \is_numeric( $custom_width ) ? \intval( $custom_width ) : 980;
		}
	}

	/**
	 * Register theme support.
	 */
	public static function add_theme_support() {
	//	\add_theme_support( 'appearance-tools' ); // @todo - doesn't seem to do anything atm.
		\add_theme_support( 'automatic-feed-links' );
		\add_theme_support( 'post-thumbnails' );
		\add_theme_support( 'title-tag' );
		\add_theme_support( 'customize-selective-refresh-widgets' );
		\add_theme_support( 'align-wide' );
		\add_theme_support( 'responsive-embeds' );

		\add_theme_support( 'post-formats', [
			'image',
			'video',
			'gallery',
			'audio',
			'quote',
			'link',
		] );

		\add_theme_support( 'html5', [
			'comment-list',
			'comment-form',
			'search-form',
			'gallery',
			'caption',
			'style',
			'script',
		] );

		if ( ! \wpex_has_customizer_panel( 'header' ) ) {
			\add_theme_support( 'custom-logo' ); // Enable Custom Logo if the header customizer section isn't enabled.
		}
	}

	/**
	 * Register menus.
	 */
	public static function register_nav_menus() {
		\register_nav_menus( [
			'topbar_menu'     => \esc_html__( 'Top Bar', 'total' ),
			'main_menu'       => \esc_html__( 'Main/Header', 'total' ),
			'mobile_menu_alt' => \esc_html__( 'Mobile Menu Alternative', 'total' ),
			'mobile_menu'     => \esc_html__( 'Mobile Icons', 'total' ),
			'footer_menu'     => \esc_html__( 'Footer', 'total' ),
		] );
	}

}
