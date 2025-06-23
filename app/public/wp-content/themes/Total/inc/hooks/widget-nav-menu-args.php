<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "widget_nav_menu_args".
 */
final class Widget_Nav_Menu_args {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $nav_menu_args, $nav_menu, $args, $instance ) {
		// @todo should also add the classname "widget_nav_menu_accordion" somehow if possible.
		if ( \class_exists( '\TotalTheme\Walkers\Widget_Nav_Menu' ) ) {
			$nav_menu_args['walker'] = new \TotalTheme\Walkers\Widget_Nav_Menu;
		}
		return $nav_menu_args;
	}

}
