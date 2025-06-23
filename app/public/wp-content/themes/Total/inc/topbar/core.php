<?php

namespace TotalTheme\Topbar;

\defined( 'ABSPATH' ) || exit;

/**
 * Topbar Core.
 */
class Core {

	/**
	 * Topbar is enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * Topbar has content or not.
	 */
	protected static $has_content;

	/**
	 * The topbar style.
	 */
	protected static $style;

	/**
	 * The topbar breakpoint.
	 */
	protected static $breakpoint;

	/**
	 * Stores the topbar template id if defined.
	 */
	protected static $template_id;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Checks if the topbar is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		if ( \totaltheme_call_static( 'Integration\Elementor', 'location_exists', 'topbar' ) ) {
			$check = true;
		} else {
			$check = \get_theme_mod( 'top_bar', true );
		}

		// Check meta.
		$post_id = \wpex_get_current_post_id();
		if ( $post_id && $meta = \get_post_meta( $post_id, 'wpex_disable_top_bar', true ) ) {
			if ( 'on' === $meta ) {
				$check = false;
			} elseif ( 'enable' === $meta ) {
				$check = true;
			}
		}

		$check = \apply_filters( 'wpex_is_top_bar_enabled', $check ); // @deprecated
		$check = \apply_filters( 'wpex_has_topbar', $check ); // @deprecated
		
		self::$is_enabled = (bool) \apply_filters( 'totaltheme/topbar/is_enabled', $check );

		return self::$is_enabled;
	}

	/**
	 * Returns the topbar style.
	 */
	public static function style(): string {
		if ( ! \is_null( self::$style ) ) {
			return self::$style;
		}
		$style = ( $style = \get_theme_mod( 'top_bar_style' ) ) ? \sanitize_text_field( $style ) : 'one';
		self::$style = (string) \apply_filters( 'wpex_top_bar_style', $style );
		return self::$style;
	}

	/**
	 * Checks if the topbar is set to fullwidth.
	 */
	public static function is_fullwidth(): bool {
		return 'full-width' === \wpex_site_layout() && wp_validate_boolean( \get_theme_mod( 'top_bar_fullwidth' ) );
	}

	/**
	 * Returns the topbar breakpoint.
	 */
	public static function breakpoint(): string {
		if ( ! is_null( self::$breakpoint ) ) {
			return self::$breakpoint;
		}
		$breakpoint = ( $breakpoint = \get_theme_mod( 'topbar_split_breakpoint' ) ) ? \sanitize_text_field( $breakpoint ) : 'md';
		if ( $breakpoint && 'none' !== $breakpoint && 'md' !== $breakpoint && ! array_key_exists( $breakpoint, \wpex_utl_breakpoints() ) ) {
			$breakpoint = 'md';
		}
		$breakpoint = \apply_filters( 'wpex_topbar_split_breakpoint', $breakpoint ); // @deprecated
		$breakpoint = (string) \apply_filters( 'totaltheme/topbar/breakpoint', $breakpoint );
		self::$breakpoint = ( 'none' === $breakpoint ) ? '' : $breakpoint;
		return self::$breakpoint;
	}

	/**
	 * Returns the default alignment (mobile first).
	 */
	public static function alignment(): string {
		return ( $align = \get_theme_mod( 'topbar_alignment' ) ) ? \sanitize_text_field( $align ) : 'center';
	}

	/**
	 * Return default content.
	 */
	public static function get_default_content(): string {
		$space = \is_customize_preview() ? "\r\n\r\n" : '';
		return '[topbar_item icon="phone" text="1-800-987-654" link="tel:1-800-987-654"/]' . $space . '[topbar_item icon="envelope" text="admin@totalwptheme.com" link="mailto:admin@totalwptheme.com"/]' . $space . '[topbar_item type="login" icon="user" icon_logged_in="sign-out" text="User Login" text_logged_in="Log Out" logout_text="Logout"/]';
	}

	/**
	 * Return the topbar content from the theme mod.
	 */
	private static function get_content_from_mod() {
		return \wpex_get_translated_theme_mod( 'top_bar_content', self::get_default_content() );
	}

	/**
	 * Return template ID.
	 */
	public static function get_template_id(): int {
		if ( ! is_null( self::$template_id ) ) {
			return self::$template_id;
		}
		$template_id = 0;
		$content = self::get_content_from_mod();
		if ( \is_numeric( $content ) ) {
			$post_id = \wpex_parse_obj_id( $content, 'page' );
			$post    = \get_post( $post_id );
			if ( $post && ! \is_wp_error( $post ) ) {
				$template_id = (int) $post_id;
			}
		}
		self::$template_id = $template_id;
		return self::$template_id;
	}

	/**
	 * Return topbar content.
	 */
	public static function get_content(): string {
		if ( $template_id = self::get_template_id() ) {
			$content = \totaltheme_shortcode_unautop( \get_post_field( 'post_content', $template_id ) );
		} else {
			$content = self::get_content_from_mod();
		}
		$content = \apply_filters( 'wpex_top_bar_content', $content ); // @deprecated 5.4.5
		$content = \apply_filters( 'wpex_topbar_content', $content ); // @deprecated
		return (string) \apply_filters( 'totaltheme/topbar/content', $content );
	}

	/**
	 * Check if the topbar has content.
	 */
	public static function has_content(): bool {
		if ( ! \is_null( self::$has_content ) ) {
			return self::$has_content;
		}
		self::$has_content = \has_nav_menu( 'topbar_menu' ) || self::get_content();
		return self::$has_content;
	}

	/**
	 * Returns wrapper classes.
	 */
	public static function get_wrapper_classes(): string {
		$classes = [];
		$style   = self::style();

		if ( self::is_fullwidth() ) {
			$classes[] = 'top-bar-full-width';
		}

		if ( \totaltheme_call_static( 'Topbar\Sticky', 'is_enabled' ) ) {
			$classes[] = 'wpex-top-bar-sticky';
			$classes[] = 'wpex-z-sticky';
			$classes[] = 'wpex-surface-1';
		}

		if ( \get_theme_mod( 'top_bar_bottom_border', true ) ) {
			$classes[] = 'wpex-border-b';
			$classes[] = 'wpex-border-main';
			$classes[] = 'wpex-border-solid';
		}

		if ( \totaltheme_has_classic_styles() ) {
			$classes[] = 'wpex-text-sm';
		}

		if ( $visibility = \get_theme_mod( 'top_bar_visibility' ) ) {
			$classes[] = \totaltheme_get_visibility_class( $visibility );
		}

		if ( 'three' === $style ) {
			$classes[] = 'wpex-text-center';
		}

		$classes[] = 'wpex-print-hidden';

		$classes = \apply_filters( 'wpex_topbar_wrap_class', $classes ); // @deprecated
		$classes = \apply_filters( 'wpex_get_topbar_classes', $classes ); // @deprecated
		$classes = (array) \apply_filters( 'totaltheme/topbar/wrapper_class', $classes );

		return \implode( ' ', array_filter( $classes ) );
	}

	/**
	 * Echo class attribute for the the topbar wrapper element.
	 */
	public static function wrapper_class(): void {
		if ( $classes = self::get_wrapper_classes() ) {
			echo 'class="' . \esc_attr( $classes ) . '"';
		}
	}

	/**
	 * Echo class attribute for the the topbar inner element.
	 */
	public static function inner_class(): void {
		$topbar_style = self::style();
		$split_bk     = self::breakpoint();

		$class = [
			'container',
		];

		$class[] = 'wpex-relative';  // !! important !!!
		$class[] = 'wpex-py-15';

		if ( 'one' === $topbar_style || 'two' === $topbar_style ) {
			if ( $split_bk ) {
				$class[] = "wpex-{$split_bk}-flex";
			} else {
				$class[] = 'wpex-flex';
				$class[] = 'wpex-overflow-x-auto';
				$class[] = 'wpex-hide-scrollbar';
			}
			$class[] = 'wpex-justify-between';
			$class[] = 'wpex-items-center';
			if ( $split_bk ) {
				if ( $alignment = self::alignment() ) {
					$class[] = "wpex-text-{$alignment}";
				}
				$class[] = "wpex-{$split_bk}-text-initial";
			}
		}

		if ( 'one' === $topbar_style && ! self::has_content() ) {
			$class[] = 'wpex-flex-row-reverse';
		}

		if ( 'two' === $topbar_style && ! \totaltheme_call_static( 'Topbar\Social', 'is_enabled' ) ) {
			$class[] = 'wpex-flex-row-reverse';
		}

		if ( 'three' === $topbar_style && $alignment = self::alignment() ) {
			$class[] = "wpex-text-{$alignment}";
			if ( $split_bk ) {
				$class[] = "wpex-{$split_bk}-text-center";
			}
		}

		if ( ! $split_bk ) {
			$class[] = 'wpex-gap-30';
		}

		$class = \apply_filters( 'wpex_topbar_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/topbar/inner_class', $class );

		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', array_filter( $class ) ) ) . '"';
		}
	}

	/**
	 * Returns content classes.
	 */
	public static function get_content_classes(): string {
		$classes      = [];
		$topbar_style = self::style();
		$split_bk     = self::breakpoint();

		if ( self::get_content() ) {
			$classes[] = 'has-content';
		}

		switch ( $topbar_style ) {
			case 'one':
				$classes[] = 'top-bar-left';
				break;
			case 'two':
				$classes[] = 'top-bar-right';
				if ( $split_bk && \totaltheme_call_static( 'Topbar\Social', 'is_enabled' ) ) {
					$classes[] = 'wpex-mt-10';
					$classes[] = "wpex-{$split_bk}-mt-0";
				}
				break;
			case 'three':
				$classes[] = 'top-bar-centered';
				break;
		}

		if ( ! $split_bk ) {
			$classes[] = 'wpex-flex-shrink-0';
		}

		$classes[] = 'wpex-clr';

		$classes = \apply_filters( 'wpex_top_bar_classes', $classes ); // @deprecated
		$classes = \apply_filters( 'wpex_topbar_content_class', $classes ); // @deprecated
		$classes = (array) \apply_filters( 'totaltheme/topbar/content_class', $classes );

		return \implode( ' ', array_filter( $classes ) );
	}

	/**
	 * Echo class attribute for the the topbar content element.
	 */
	public static function content_class(): void {
		if ( $classes = self::get_content_classes() ) {
			echo 'class="' . \esc_attr( $classes ) . '"';
		}
	}

	/**
	 * Checks if the topbar is sticky.
	 *
	 * @deprecated 5.20
	 */
	public static function is_sticky(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 5.20', 'TotalTheme\TopBar\Sticky::is_enabled()' );
	}

}
