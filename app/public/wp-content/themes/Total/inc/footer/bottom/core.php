<?php

namespace TotalTheme\Footer\Bottom;

\defined( 'ABSPATH' ) || exit;

/**
 * Footer Bottom.
 */
class Core {

	/**
	 * Check if enabled.
	 */
	protected static $is_enabled;

	/**
	 * Stackimg breakpoint.
	 */
	protected static $breakpoint;

	/**
	 * Checks if the header is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		$post_id = \wpex_get_current_post_id();

		if ( \totaltheme_call_static( 'Integration\Elementor', 'location_exists', 'footer_bottom' ) ) {
			$check = true;
		} elseif ( totaltheme_call_static( 'Footer\Core', 'is_custom' ) ) {
			// @todo rename to be same as default.
			$check = \get_theme_mod( 'footer_builder_footer_bottom', false );
		} else {
			$check = \get_theme_mod( 'footer_bottom', true );
		}

		if ( $post_id ) {
			$meta = \get_post_meta( $post_id, 'wpex_footer_bottom', true );
			if ( 'on' === $meta ) {
				$check = true;
			} elseif ( 'off' === $meta ) {
				$check = false;
			}
		}

		$check = \apply_filters( 'wpex_has_footer_bottom', $check, $post_id ); // @deprecated

		self::$is_enabled = (bool)\apply_filters( 'totaltheme/footer/bottom/is_enabled', $check, $post_id );

		return self::$is_enabled;
	}

	/**
	 * Returns the topbar breakpoint.
	 */
	public static function breakpoint(): string {
		if ( is_null( self::$breakpoint ) ) {
			$breakpoint = ( $breakpoint = \get_theme_mod( 'bottom_footer_breakpoint' ) ) ? \sanitize_text_field( $breakpoint ) : 'md';
			if ( $breakpoint && 'none' !== $breakpoint && 'md' !== $breakpoint && ! array_key_exists( $breakpoint, \wpex_utl_breakpoints() ) ) {
				$breakpoint = 'md';
			}
			$breakpoint = (string) \apply_filters( 'totaltheme/footer/bottom/breakpoint', $breakpoint );
			self::$breakpoint = ( 'none' === $breakpoint ) ? '' : $breakpoint;
		}
		return self::$breakpoint;
	}

	/**
	 * Get alignment.
	 */
	public static function alignment(): string {
		$align = \get_theme_mod( 'bottom_footer_text_align' );
		if ( $align && \in_array( $align, [ 'left', 'center', 'right' ], true ) ) {
			return $align;
		}
		return '';
	}

	/**
	 * Get stack alignment.
	 */
	public static function stack_alignment(): string {
		$align = \get_theme_mod( 'bottom_footer_stack_align' );
		if ( $align && \in_array( $align, [ 'left', 'center', 'right' ], true ) ) {
			return $align;
		}
		return 'center';
	}

	/**
	 * Get gap.
	 */
	protected static function gap(): string {
		return ( $gap = get_theme_mod( 'footer_bottom_gap' ) ) ? absint( get_theme_mod( 'footer_bottom_gap' ) ) : 10;
	}

	/**
	 * Output wrapper class.
	 */
	public static function wrapper_class(): void {
		$align      = self::alignment();
		$breakpoint = self::breakpoint();

		$class = [
			'wpex-py-20',
		];

		if ( \totaltheme_has_classic_styles() ) {
			$class[] = 'wpex-text-sm';
		}

		if ( \get_theme_mod( 'footer_bottom_dark_surface', true ) ) {
			$class[] = 'wpex-surface-dark';
			$class[] = 'wpex-bg-gray-900';
		}

		if ( $align && \in_array( $align, [ 'left', 'center', 'right' ], true ) ) {
			$class[] = "wpex-text-{$align}";
		} else {
			$stack_align = self::stack_alignment();
			if ( 'center' === $stack_align || 'right' === $stack_align ) {
				$class[] = "wpex-text-{$stack_align} wpex-{$breakpoint}-text-left";
			}
		}

		$class[] = 'wpex-print-hidden';

		$class = \apply_filters( 'wpex_footer_bottom_classes', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/footer/bottom/wrapper_class', $class );

		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Output inner class.
	 */
	public static function inner_class(): void {
		$class = [
			'container',
		];
		$class = \apply_filters( 'wpex_footer_bottom_inner_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/footer/bottom/inner_class', $class );
		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Get flex container class.
	 */
	public static function flex_class(): void {
		$align = self::alignment();
		$gap   = self::gap();
		$class = [
			'footer-bottom-flex',
			'wpex-flex',
			'wpex-flex-col',
			"wpex-gap-{$gap}",
		];
		if ( ! $align ) {
			$breakpoint = self::breakpoint();
			if ( $breakpoint ) {
				if ( \wp_validate_boolean( \get_theme_mod( 'bottom_footer_stack_flip' ) ) ) {
					$class[] = 'wpex-flex-col-reverse';
				}
				$class[] = "wpex-{$breakpoint}-flex-row";
				$class[] = "wpex-{$breakpoint}-justify-between";
				$class[] = "wpex-{$breakpoint}-items-center";
			} else {
				$class[] = 'wpex-flex-row';
				$class[] = 'wpex-justify-between';
				$class[] = 'wpex-items-center';
			}
		}
		$class = (array) \apply_filters( 'totaltheme/footer/bottom/flex_class', $class );
		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

}
