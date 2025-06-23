<?php

namespace TotalTheme\Walkers;

use Walker_Nav_Menu;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Walker_Nav_Menu for the main menu.
 */
class Main_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * Check if currently inside a mega menu.
	 */
	protected $is_mega = false;

	/**
	 * Starts the list before the elements are added.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );

		// Default class.
		if ( $this->is_mega && $depth > 0 ) {
			$classes = [ 'megamenu__inner-ul' ];
		} else {
			$classes = [ 'sub-menu' ];
		}

		/**
		 * Filters the CSS class(es) applied to a menu list element.
		 */
		$classes = apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth );
		
		$class_names = implode( ' ', $classes );

		$atts          = array();
		$atts['class'] = ! empty( $class_names ) ? $class_names : '';

		/**
		 * Filters the HTML attributes applied to a menu list element.
		 */
		$atts       = apply_filters( 'nav_menu_submenu_attributes', $atts, $args, $depth );
		$attributes = $this->build_atts( $atts );

		$output .= "{$n}{$indent}<ul{$attributes}>{$n}";
	}

	/**
	 * Starts the element output.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		// Restores the more descriptive, specific name for use within this method.
		$menu_item = $data_object;

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes   = empty( $menu_item->classes ) ? array() : (array) $menu_item->classes;
		$classes[] = 'menu-item-' . $menu_item->ID;

		if ( 0 === $depth ) {
			$mega_cols = \get_post_meta( $menu_item->ID, '_menu_item_totaltheme_mega_cols', true );
			if ( \is_numeric( $mega_cols ) && (int) $mega_cols > 0 ) {
				$classes[] = 'megamenu';
				$classes[] = "col-{$mega_cols}";
			}
		}

		/**
		 * Filters the arguments for a single nav menu item.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $menu_item, $depth );

		/**
		 * Filters the CSS classes applied to a menu item's list item element.
		 */
		$classes = apply_filters( 'nav_menu_css_class', array_filter( $classes ), $menu_item, $args, $depth );

		// Remove current menu item when using local-scroll class.
		if ( in_array( 'local-scroll', $classes, true ) && in_array( 'current-menu-item', $classes, true ) ) {
			$key = array_search( 'current-menu-item', $classes );
			unset( $classes[ $key ] );
		}

		// Megamenu check (resets at the start of each lvl 0 item).
		if ( 0 === $depth ) {
			$this->is_mega = \in_array( 'megamenu', $classes, true );
		}

		// Drop class.
		if ( ! empty( $this->has_children ) ) {
			$classes[] = 'dropdown';
		}

		$class_names = implode( ' ', $classes );

		/**
		 * Filters the ID attribute applied to a menu item's list item element.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $menu_item->ID, $menu_item, $args, $depth );

		$li_atts          = array();
		$li_atts['id']    = ! empty( $id ) ? $id : '';
		$li_atts['class'] = ! empty( $class_names ) ? $class_names : '';

		/**
		 * Filters the HTML attributes applied to a menu's list item element.
		 */
		$li_atts       = apply_filters( 'nav_menu_item_attributes', $li_atts, $menu_item, $args, $depth );
		$li_attributes = $this->build_atts( $li_atts );

		$output .= $indent . '<li' . $li_attributes . '>';

		$atts           = array();
		$atts['title']  = ! empty( $menu_item->attr_title ) ? $menu_item->attr_title : '';
		$atts['target'] = ! empty( $menu_item->target ) ? $menu_item->target : '';
		if ( '_blank' === $menu_item->target && empty( $menu_item->xfn ) ) {
			$atts['rel'] = 'noopener';
		} else {
			$atts['rel'] = $menu_item->xfn;
		}

		if ( ! empty( $menu_item->url ) ) {
			if ( get_privacy_policy_url() === $menu_item->url ) {
				$atts['rel'] = empty( $atts['rel'] ) ? 'privacy-policy' : $atts['rel'] . ' privacy-policy';
			}

			$atts['href'] = $menu_item->url;
		} else {
			$atts['href'] = '';
		}

		$atts['aria-current'] = $menu_item->current ? 'page' : '';

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 */
		$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $menu_item, $args, $depth );
		$attributes = $this->build_atts( $atts );

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );

		/**
		 * Filters a menu item's title.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $menu_item, $args, $depth );

		/**
		 * Title mods (must be after the filter).
		 */

		// Down Arrows.
		if ( ! empty( $this->has_children ) ) {
			if ( 0 === (int) $depth ) {
				if ( $down_arrow = $this->get_down_arrow() ) {
					$title .= ' <span class="nav-arrow top-level">' . $down_arrow . '</span>';
				}
			} else {
				if ( $side_arrow = $this->get_side_arrow() ) {
					$title .= ' <span class="nav-arrow second-level">' . $side_arrow . '</span>';
				}
			}
		}

		// Mega menu arrows.
		if ( $this->is_mega && $depth > 2 ) {
			$megamenu_icon = apply_filters( 'wpex_megamenu_sub_item_icon', 'angle-right' );
			if ( $megamenu_icon ) {
				$megamenu_icon = totaltheme_get_icon( $megamenu_icon, '', 'xs' );
				if ( $megamenu_icon ) {
					$title = '<span class="megamenu-sub-item-icon wpex-inline-block wpex-mr-10 hide-at-mm-breakpoint">' . $megamenu_icon . '</span>' . $title;
				}
			}
		}

		/* Menu descriptions.
		if ( ! empty( $menu_item->description ) && $depth > 0 ) {
			$title = '<span class="menu-item-heading wpex-bold">' . $title . '</span><p class="menu-item-description wpex-text-sm wpex-m-0">' . \esc_html( $menu_item->description ) . '</p>';
		}*/

		/**
		 * The actual menu item output.
		 */
		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filters a menu item's starting output.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args );
	}

	/**
	 * Helper: Returns drop arrow type.
	 */
	private function get_drop_arrow_type() {
		return ( $arrow = get_theme_mod( 'menu_arrow' ) ) ? sanitize_text_field( $arrow ) : 'angle';
	}

	/**
	 * Helper: Returns drop arrow size.
	 */
	private function get_drop_arrow_size() {
		$size = get_theme_mod( 'menu_arrow_size', 'xs' );
		return in_array( $size, [ '2xs', 'xs', 'sm' ], true ) ? $size : '';
	}

	/**
	 * Helper: returns drop down arrow.
	 */
	private function get_down_arrow() {
		if ( ! wp_validate_boolean( get_theme_mod( 'menu_arrow_down', false ) ) ) {
			return;
		}

		$type = $this->get_drop_arrow_type();
		$size = $this->get_drop_arrow_size();

		if ( 'plus' === $type ) {
			$icon = 'plus';
		} else {
			$dir = 'six' === totaltheme_call_static( 'Header\Core', 'style' ) ? 'right' : 'down';
			$icon = "{$type}-{$dir}";
		}

		$down_arrow = totaltheme_get_icon( $icon, 'nav-arrow__icon', $size );
		return (string) apply_filters( 'wpex_header_menu_down_arrow_html', $down_arrow, $type );
	}
	
	/**
	 * Helper: returns drop side arrow.
	 */
	private function get_side_arrow() {
		if ( ! wp_validate_boolean( get_theme_mod( 'menu_arrow_side', true ) ) ) {
			return;
		}

		$type = $this->get_drop_arrow_type();
		$size = $this->get_drop_arrow_size();

		if ( 'plus' === $type ) {
			$icon = 'plus';
			$bidi = false;
		} else  {
			$icon = "{$type}-right";
		}
		
		$side_arrow = totaltheme_get_icon( $icon, 'nav-arrow__icon', $size, $bidi ?? true );
		return apply_filters( 'wpex_header_menu_down_side_html', $side_arrow, $type );
	}
	
}
