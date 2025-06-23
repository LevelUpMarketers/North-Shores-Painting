<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

final class Register_Shortcodes {

	/**
	 * Init.
	 */
	public static function init(): void {
		if ( self::register_check() ) {
			self::register();
		}
		\add_filter( 'the_content', [ self::class, 'fix_content_shortcodes' ] );
		\add_filter( 'wp_nav_menu_items', 'do_shortcode' );
		\add_filter( 'the_excerpt', 'shortcode_unautop' );
		\add_filter( 'the_excerpt', 'do_shortcode' );
		\add_filter( 'widget_text', 'do_shortcode' );
	}

	/**
	 * Returns array of shortcodes classes.
	 */
	private static function get_shortcodes_list(): array {
		$list = [
			'Shortcode_Topbar_Item',
			'Shortcode_Header_Search_Icon',
			'Shortcode_Span',
		//	'Shortcode_Scribble_Underline',
			'Shortcode_Site_URL',
			'Shortcode_Site_Name',
			'Shortcode_Home_URL',
			'Shortcode_Menu_Site_URL',
			'Shortcode_Highlight',
			'Shortcode_Underline',
			'Shortcode_Line_Break',
			'Shortcode_WP_Login_Link',
			'Shortcode_Username',
			'Shortcode_Select_menu',
			'Shortcode_Ticon',
			'Shortcode_Date',
			'Shortcode_Staff_Social',
			'Shortcode_Searchform',
			'Shortcode_Current_Year',
			'Shortcode_Cf_Value',
			'Shortcode_Post_Title',
			'Shortcode_Post_Permalink',
			'Shortcode_Post_Permalink',
			'Shortcode_Post_Date',
			'Shortcode_Post_Date_Modified',
			'Shortcode_Post_Author',
			'Shortcode_Comments_Number',
			'Shortcode_Enqueue_Imagesloaded',
			'Shortcode_Enqueue_Lightbox',
			'Shortcode_Cart_Link',
			'Shortcode_Header_Cart_Icon',
		];
		
		if ( \is_callable( '\TotalTheme\Dark_Mode::is_enabled' ) && \Totaltheme\Dark_Mode::is_enabled() ) {
			$list[] = 'Shortcode_Header_Dark_Mode_Icon';
		}

		if ( \class_exists( '\Polylang' ) ) {
			$list[] = 'Shortcode_Polylang_Switcher';
		}

		if ( \class_exists( '\SitePress' ) ) {
			$list[] = 'Shortcode_Wpml_Translate';
			$list[] = 'Shortcode_Wpml_Language_Selector';
		}

		return $list;
	}

	/**
	 * Check if we should register shortcodes or not.
	 *
	 * Prevent shortcode rendering via $_REQUEST - security patch for WP core issue.
	 */
	protected static function register_check(): bool {
		return ! isset( $_REQUEST['action'] ) || 'parse-media-shortcode' !== $_REQUEST['action'];
	}

	/**
	 * Register shortcodes.
	 */
	protected static function register(): void {
		foreach ( self::get_shortcodes_list() as $class_name ) {
			totalthemecore_init_class( 'Shortcodes\\' . $class_name );
		}
	}

	/**
	 * Cleanup shortcodes.
	 *
	 * Filters the_content to make sure shortcodes aren't being wrapped in p tags or have br tags after them.
	 * 
	 * @todo is this still needed?
	 */
	public static function fix_content_shortcodes( $content ) {
		if ( $content && \is_string( $content ) ) {
			$content = \strtr( $content, [
				'<p>['    => '[',
				']</p>'   => ']',
				']<br />' => ']',
			] ) ;
		}
		return $content;
	}

}
