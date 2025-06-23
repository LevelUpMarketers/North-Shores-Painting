<?php

namespace TotalTheme\Walkers;

use Walker_Nav_Menu;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Walker_Nav_Menu for the core menu widget.
 */
class Widget_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * Returns true if the theme's scripts have been loaded.
	 */
	private $wpex_script_loaded = false;

	/**
	 * Alters the element display.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		$id_field = $this->db_fields['id'];

		if ( ! empty( $children_elements[ $element->$id_field ] )
			&& $down_arrow = $this->dropdown_span()
		) {
			if ( ! $this->wpex_script_loaded && \wp_script_is( 'wpex-widget-nav-menu', 'registered' ) ) {
				\wp_enqueue_script( 'wpex-widget-nav-menu' );
				$this->wpex_script_loaded = true;
			}
			$element->title .= $down_arrow;
		//	$element->title = "<span class='widget_nav_menu-link-inner'>{$element->title}</span>";
		}

		Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	/**
	 * Renders the dropdown arrow icon.
	 * which is used for non-screen readers to display an icon for opening/closing sub-menus.
	 *
	 * @note We don't need to add aria-hidden because it's already added by the icon html.
	 */
	private function dropdown_span( $title = '' ) {
		$down_arrow = \totaltheme_get_icon(
			\apply_filters( 'wpex_widget_nav_menu_open_submenu_icon', 'material-arrow-down-ios' ),
			'wpex-open-submenu__icon wpex-mr-10'
		);
		if ( $down_arrow ) {
			return '<span class="wpex-open-submenu wpex-items-center wpex-justify-end wpex-absolute wpex-top-0 wpex-right-0 wpex-h-100 wpex-w-100 wpex-cursor-pointer wpex-overflow-hidden">' . $down_arrow . '</span>';
		}
	}

}
