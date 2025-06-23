<?php
namespace TotalTheme\Topbar;

\defined( 'ABSPATH' ) || exit;

/**
 * Topbar Menu.
 */
class Menu extends Core {

	/**
	 * Renders the Topbar menu.
	 */
	public static function render() {
		\wp_nav_menu( [
			'fallback_cb'    => false,
			'container'      => false,
			'theme_location' => 'topbar_menu',
			'link_before'    => '<span class="link-inner">',
			'link_after'     => '</span>',
			'menu_class'     => \implode( ' ', self::get_class() ),
		] );
	}

	/**
	 * Returns the topbar menu class.
	 */
	protected static function get_class() {
		$class = [
			'top-bar-menu',
			// @todo add support for dropdowns? It creates a lot of complications and bloat...
		//	'wpex-dropdown-menu',
		//	'wpex-dropdown-menu--onclick',
			'wpex-inline-block',
			'wpex-m-0',
			'wpex-list-none',
			'wpex-last-mr-0',
		];

		if ( parent::get_content() ) {
			$class[] = 'wpex-mr-20';
		}

		/**
		 * Filters the top bar menu classes.
		 *
		 * @param array $class
		 */
		$class = (array) \apply_filters( 'totaltheme/topbar/menu/class', $class );

		return $class;
	}

}
