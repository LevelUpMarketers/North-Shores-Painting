<?php

namespace TotalTheme\Footer;

\defined( 'ABSPATH' ) || exit;

/**
 * Footer Widgets.
 */
class Widgets {

	/**
	 * Are the footer widgets enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * Checks if the footer widgets are enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		if ( totaltheme_call_static( 'Footer\Core', 'is_custom' ) ) {
			//@todo make the option same value as Customizer?
			$check = \get_theme_mod( 'footer_builder_footer_widgets', false );
		} else {
			$check = \get_theme_mod( 'footer_widgets', true );
		}

		$post_id = \wpex_get_current_post_id();

		if ( $post_id && $meta = \get_post_meta( $post_id, 'wpex_disable_footer_widgets', true ) ) {
			if ( 'on' === $meta ) {
				$check = false;
			} elseif ( 'enable' === $meta ) {
				$check = true;
			}
		}

		$check = \apply_filters( 'wpex_display_footer_widgets', $check ); // @deprecated

		self::$is_enabled = (bool) \apply_filters( 'totaltheme/footer/widgets/is_enabled', $check );

		return self::$is_enabled;
	}

	/**
	 * Returns footer widgets widget title tag arguments.
	 */
	public static function widget_title_args(): array {
		$tag_escaped = ( $tag = \get_theme_mod( 'footer_headings' ) ) ? \tag_escape( $tag ) : 'div';
		$font_size = totaltheme_has_classic_styles() ? 'wpex-text-md' : 'wpex-text-lg';
		return [
			'before' => "<{$tag_escaped} class='widget-title wpex-heading {$font_size} wpex-mb-20'>",
			'after'  => "</{$tag_escaped}>",
		];
	}

	/**
	 * Returns wrapper classes.
	 */
	public static function get_wrapper_classes(): array {
		$columns = (int) \get_theme_mod( 'footer_widgets_columns', 4 );
		$class = [
			'wpex-row',
			'wpex-clr',
		];
		if ( 1 === $columns ) {
			$class[] = 'single-col-footer'; // legacy class.
		}
		if ( $gap = \get_theme_mod( 'footer_widgets_gap', '30' ) ) {
			$class[] = \wpex_gap_class( $gap );
		}
		$class = \apply_filters( 'wpex_footer_widgets_class', $class ); // @deprecated
		return (array) \apply_filters( 'totaltheme/footer/widgets/wrapper_class', $class );
	}

	/**
	 * Returns the wrapper class.
	 */
	public static function wrapper_class(): void {
		if ( $classes = self::get_wrapper_classes() ) {
			$classes = \implode( ' ', $classes );
			$classes = \apply_filters( 'wpex_footer_widget_row_classes', $classes ); // @deprecated
			echo 'class="' . \esc_attr( $classes ) . '"';
		}
	}

}
