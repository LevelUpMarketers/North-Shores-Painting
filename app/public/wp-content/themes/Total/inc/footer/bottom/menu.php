<?php

namespace TotalTheme\Footer\Bottom;

use TotalTheme\Footer\Bottom\Core as Footer_Bottom;

\defined( 'ABSPATH' ) || exit;

/**
 * Footer Menu.
 */
class Menu {

	/**
	 * Returns theme location for the footer bottom menu.
	 */
	public static function get_theme_location(): string {
		$location = 'footer_menu';
		$location = \apply_filters( 'wpex_footer_menu_location', $location ); // @deprecated
		return (string) \apply_filters( 'totaltheme/footer/bottom/menu/theme_location', $location );
	}

	/**
	 * Get the menu.
	 */
	public static function get_menu() {
		return wp_nav_menu( [
			'theme_location' => self::get_theme_location(),
			'menu_class'     => self::get_menu_class(),
			'sort_column'    => 'menu_order',
			'fallback_cb'    => false,
			'echo'           => false,
		] );
	}

	/**
	 * Get menu class.
	 */
	public static function get_menu_class(): string {
		$class = [
			'menu',
			'wpex-flex',
			'wpex-flex-wrap',
			'wpex-gap-x-20',
			'wpex-gap-y-5',
			'wpex-m-0',
			'wpex-list-none',
		];

		$alignment = Footer_Bottom::alignment();

		if ( 'center' === $alignment ) {
			$class[] = 'wpex-justify-center';
		} elseif ( 'right' === $alignment ) {
			$class[] = 'wpex-justify-end';
		} elseif ( ! $alignment ) {
			$breakpoint = Footer_Bottom::breakpoint();
			if ( $breakpoint && 'none' !== $breakpoint ) {
				$stack_align = Footer_Bottom::stack_alignment();
				if ( 'center' === $stack_align ) {
					$class[] = 'wpex-justify-center';
					$class[] = "wpex-{$breakpoint}-justify-end";
				} elseif ( 'right' === $stack_align ) {
					$class[] = 'wpex-justify-end';
				} else {
					$class[] = "wpex-{$breakpoint}-justify-end";
				}
			} else {
				$class[] = 'wpex-justify-end';
			}
		}

		$class = (array) \apply_filters( 'totaltheme/footer/bottom/menu/ul_class', $class );

		return \implode( ' ', $class );
	}

	/**
	 * Output wrapper class.
	 */
	public static function wrapper_class(): void {
		$classes = [];
		$classes = \apply_filters( 'wpex_footer_bottom_menu_class', $classes ); // @deprecated
		$classes = (array) \apply_filters( 'totaltheme/footer/bottom/menu/wrapper_class', $classes );
		if ( $classes ) {
			echo 'class="' . \esc_attr( \implode( ' ', \array_unique( $classes ) ) ) . '"';
		}
	}

}
