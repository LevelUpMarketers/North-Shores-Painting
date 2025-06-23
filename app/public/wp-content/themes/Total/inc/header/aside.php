<?php

namespace TotalTheme\Header;

\defined( 'ABSPATH' ) || exit;

/**
 * Header Aside.
 */
class Aside {

	/**
	 * Stores the header aside template id if defined.
	 */
	protected static $template_id;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns array of header styles that allow aside content.
	 */
	public static function supported_header_styles(): array {
		$supported_styles = [ 'two', 'three', 'four', 'dev' ];
		$supported_styles = \apply_filters( 'wpex_get_header_styles_with_aside_support', $supported_styles ); // @deprecated
		return (array) \apply_filters( 'totaltheme/header/aside/supported_header_styles', $supported_styles );
	}

	/**
	 * Check if the header aside area is supported by the current theme setup.
	 */
	public static function is_supported(): bool {
		$check = \in_array( \totaltheme_call_static( 'Header\Core', 'style' ), self::supported_header_styles(), true );
		$check = \apply_filters( 'wpex_header_supports_aside', $check ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/header/aside/is_supported', $check );
	}

	/**
	 * Return the aside content from the theme mod.
	 */
	private static function get_content_from_mod(): string {
		return \wpex_get_translated_theme_mod( 'header_aside' ) ?: '';
	}

	/**
	 * Return template ID.
	 */
	public static function get_template_id() {
		if ( ! \is_null( self::$template_id ) ) {
			return self::$template_id;
		}
		$template_id = ''; // make sure it isn't null to prevent extra checks.
		$content = self::get_content_from_mod();
		if ( \is_numeric( $content ) ) {
			$post_id = \wpex_parse_obj_id( $content, 'page' );
			$post    = \get_post( $post_id );
			if ( $post && ! \is_wp_error( $post ) ) {
				$template_id = $post_id;
			}
		}
		self::$template_id = $template_id;
		return self::$template_id;
	}

	/**
	 * Return header aside content.
	 */
	public static function get_content(): string {
		if ( $template_id = self::get_template_id() ) {
			$content = \totaltheme_shortcode_unautop( \get_post_field( 'post_content', $template_id ) );
		} else {
			$content = self::get_content_from_mod();
		}
		$content = \apply_filters( 'wpex_header_aside_content', $content ); // @deprecated
		$content = (string) \apply_filters( 'totaltheme/header/aside/content', $content );
		$content = \totaltheme_replace_vars( $content );
		if ( $content ) {
			// This is a good spot to hook into the mobile menu to auto insert the aside content into the mobile menu
			$mobile_hook = \get_theme_mod( 'header_aside_mobile_menu_insert_hook' );
			if ( $mobile_hook && in_array( $mobile_hook, [ 'top', 'bottom' ], true ) ) {
				$priority = 'top' === $mobile_hook ? 15 : 5;
				$priority = \apply_filters( 'totaltheme/header/aside/mobile_menu_insert_hook_priority', $priority );
				\add_action( "wpex_hook_mobile_menu_{$mobile_hook}", function() use ( $content ) {
					echo '<div id="wpex-mobile-menu__header-aside" class="header-aside-content wpex-clr">' . \do_shortcode( \wp_kses_post( \do_blocks( $content ) ) ) . '</div>';
				}, $priority );
			}
		}
		return $content;
	}

	/**
	 * Returns the visibility class for the header aside element.
	 */
	private static function get_visibility_class() {
		if ( $visibility = \get_theme_mod( 'header_aside_visibility', 'hide-at-mm-breakpoint' ) ) {
			return \totaltheme_get_visibility_class( $visibility );
		}
	}

	/**
	 * Echo class attribute for the the header aside wrapper element.
	 */
	public static function wrapper_class(): void {
		$class        = [];
		$header_style = \totaltheme_call_static( 'Header\Core', 'style' );

		if ( $header_style ) {
			$class[] = "header-{$header_style}-aside";
		}

		switch ( $header_style ) {
			case 'two':
				$class[] = 'wpex-min-float-right';
				$class[] = 'wpex-min-text-right';
				if ( \totaltheme_has_classic_styles() ) {
					$class[] = 'wpex-text-md';
				}
				break;
			case 'three':
			case 'four':
				$class[] = 'wpex-text-center';
				$class[] = 'wpex-mt-10';
				$class[] = 'wpex-clear'; // must clear any floats - such as logo on mobile
				break;
		}

		if ( \get_theme_mod( 'header_flex_items', false ) ) {
			$class[] = 'wpex-ml-auto';
			$class[] = 'wpex-order-2';
		}

		if ( $visibility_class = self::get_visibility_class() ) {
			$class[] = $visibility_class;
		}

		$class = \apply_filters( 'wpex_header_aside_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/header/aside/wrapper_class', $class );

		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Mobile Spacer.
	 */
	public static function mobile_spacer() {
		if ( 'two' === \totaltheme_call_static( 'Header\Core', 'style' ) ) {
			$class = 'header-aside-mobile-spacer wpex-pt-20 wpex-clear wpex-min-hidden';
			if ( $visibility_class = self::get_visibility_class() ) {
				$class .= " {$visibility_class}"; // needs to also hide if the aside content is hidden
			}
			echo '<div class="' . \esc_attr( $class ) . '"></div>';
		}
	}

}
