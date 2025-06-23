<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "respond_link".
 */
final class Nav_Menu_CSS_Class {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback.
	 */
	public static function callback( $classes, $item, $args ) {
		$slug = $args->menu->slug ?? '';
		if ( 'main' === $slug ) {
			if ( is_array( $classes ) && in_array( 'megamenu-col-full', $classes ) ) {
				$classes[] = 'wpex-col-span-full';
			}
		}
		return $classes;
	}

}
