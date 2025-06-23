<?php

namespace TotalThemeCore\Vcex\Walkers;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom nav walker for the horizontal menu.
 */
class Nav_Menu_Horizontal extends \Walker_Nav_Menu {

	/**
	 * Stores the current lvl item for use with other methods.
	 */
	protected $current_el;

	/**
	 * Check if currently inside a mega menu.
	 */
	protected $is_mega = false;

	/**
	 * Mega menu columns
	 */
	protected $mega_cols = 0;

	/**
	 * Check if mega menu headings are disabled.
	 */
	protected $mega_no_headings = false;

	/**
	 * Starts the list before the elements are added.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$add_hooks = vcex_validate_att_boolean( 'add_hooks', $args->vcex_atts, true );

		$classes = [
			'vcex-horizontal-menu-nav__sub',
			'wpex-list-none',
			'wpex-m-0',
		];

		// First level dropdowns or non-mega menus
		if ( ! $this->is_mega || 0 === $depth ) {
			$classes[] = 'sub-menu';
			$classes[] = 'wpex-surface-1';

			if ( empty( $args->vcex_atts['sub_padding'] ) ) {
				$classes[] = 'wpex-p-5';
			}

			$shadow = ! empty( $args->vcex_atts['sub_shadow'] ) ? \sanitize_html_class( $args->vcex_atts['sub_shadow'] ) : 'shadow-lg';
			if ( 'shadow-none' !== $shadow ) {
				$classes[] = "wpex-{$shadow}";
			}

			if ( empty( $args->vcex_atts['sub_border_radius'] ) ) {
				$classes[] = 'wpex-rounded-sm';
			}
		}

		// Mega menu classes
		if ( $this->is_mega ) {
			if ( 0 === $depth ) {
				$classes[] = 'vcex-horizontal-menu-nav__mega';
				$classes[] = 'wpex-grid';
				$classes[] = "wpex-grid-cols-{$this->mega_cols}";
				if ( empty( $args->vcex_atts['mega_row_gap'] ) ) {
					$classes[] = 'wpex-gap-y-10';
				}
				$classes[] = 'wpex-w-max-content';
				$classes[] = 'wpex-max-w-none';
				$classes[] = 'wpex-overflow-auto';
			} else {
				$classes[0] = 'vcex-horizontal-menu-nav__mega-list';
				$classes[] = 'wpex-static';
				$classes[] = 'wpex-w-100';
				if ( ( ! empty( $args->vcex_atts['mega_width'] ) && 'max-content' !== $args->vcex_atts['mega_width'] )
					|| \in_array( 'megamenu-col-full', $this->current_el->classes, true )
				) {
					$classes[] = 'wpex-max-w-none';
				}
			}
		}
		// Non megamenu classes
		else {
			if ( 0 === $depth && empty( $args->vcex_atts['sub_min_width'] ) ) {
				$classes[] = 'wpex-min-w-100';
			}
		}

		if ( $add_hooks ) {
			$classes = apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth );
		}

		$atts = [];
		$atts['class'] = ! empty( $classes ) ? implode( ' ', $classes ) : '';

		if ( $add_hooks ) {
			$atts = apply_filters( 'nav_menu_submenu_attributes', $atts, $args, $depth );
		}

		$attributes = $this->build_atts( $atts );

		$output .= "<ul{$attributes}>";
	}

	/**
	 * Starts the element output.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		// Restores the more descriptive, specific name for use within this method
		$menu_item = $data_object;
		$this->current_el = $menu_item;

		// Check if we should add the core hooks
		$add_hooks = vcex_validate_att_boolean( 'add_hooks', $args->vcex_atts, true );

		// Filter args
		if ( $add_hooks ) {
			$args = apply_filters( 'nav_menu_item_args', $args, $menu_item, $depth );
		}

		// Check if is extra
		$is_extra = \str_starts_with( $menu_item->ID, 'vcex-extra-' ) && \function_exists( '\totaltheme_get_instance_of' );
		if ( $is_extra ) {
			$extra_id = \str_replace( 'vcex-extra-', '', $menu_item->ID );
		}

		// Checks if the menu item has a description
		$has_description = ! empty( $menu_item->description ) && ! $is_extra;

		// Check if dropdown arrow is enabled
		$show_sub_arrows = \vcex_validate_att_boolean( 'sub_arrow_enable', $args->vcex_atts, true );
		$has_sub_arrow = $show_sub_arrows && ( ! $this->is_mega || 0 === $depth ) && ! empty( $this->has_children );

		// Check if menu item has a link
		$has_link = ! empty( $menu_item->url ) && '#' !== $menu_item->url;

		// Get menu item icon
		$icon = ( $icon = \get_post_meta( $menu_item->ID, '_menu_item_totaltheme_icon', true ) ) ? \sanitize_text_field( $icon ) : '';
		if ( $icon ) {
			$icon_class = 'vcex-horizontal-menu-nav__icon wpex-flex-shrink-0 wpex-icon--w';
			$icon_transition_duration = ! empty( $args->vcex_atts['item_transition_duration'] ) ? \absint( $args->vcex_atts['item_transition_duration'] ) : 0;
			if ( $icon_transition_duration ) {
				$icon_class .= " wpex-transition-{$icon_transition_duration}";
			}
			$menu_item_icon_html = \vcex_get_theme_icon_html( $icon, $icon_class );
		}

		// Li classes
		$classes = empty( $menu_item->classes ) ? [] : (array) $menu_item->classes;
		if ( $is_extra ) {
			$classes[] = \esc_attr( "vcex-horizontal-menu-nav__item--extra menu-item-{$extra_id}" );
		} else {
			$classes[] = \esc_attr( "menu-item-{$menu_item->ID}" );
		}

		if ( 0 === $depth ) {
			$this->mega_cols = 0; // reset on each iteration
			$mega_cols = \get_post_meta( $menu_item->ID, '_menu_item_totaltheme_mega_cols', true );
			if ( \is_numeric( $mega_cols ) && (int) $mega_cols > 0 ) {
				$classes[] = 'megamenu'; // send to parse_menu_item_classes()
				$this->mega_cols = $mega_cols;
			}
		}

		$classes = $this->parse_menu_item_classes( $classes, $args, $depth );

		// Megamenu check (resets at the start of each lvl 0 item)
		if ( 0 === $depth ) {
			$this->is_mega = \in_array( 'vcex-horizontal-menu-nav__item--has_mega', $classes, true );
			$this->mega_no_headings = \in_array( 'vcex-horizontal-menu-nav__item--has_mega-no-headings', $classes, true );
		}

		// Add flex and alignment classes
		if ( ! $this->is_mega || 0 === $depth ) {
			$classes[] = 'wpex-flex';
			$menu_item_align = ! empty( $args->vcex_atts['item_align'] ) ? \sanitize_text_field( $args->vcex_atts['item_align'] ) : 'stretch';
			if ( $menu_item_align ) {
				$classes[] = \vcex_parse_align_items_class( $menu_item_align );
			}
		}

		// Add main li class to top of array
		\array_unshift( $classes, 'vcex-horizontal-menu-nav__item' );

		// Add text highlight
		$is_current = 0 === $depth && in_array( 'current-menu-item', $classes, true ) && \vcex_validate_att_boolean( 'item_current_highlight', $args->vcex_atts, true );

		// Filter classes
		if ( $add_hooks ) {
			$classes = apply_filters( 'nav_menu_css_class', $classes, $menu_item, $args, $depth );
		}

		// Generate li attributes
		$li_atts          = [];
		$li_atts['class'] = \implode( ' ', \array_filter( $classes ) );

		if ( $add_hooks ) {
			$li_id = apply_filters( 'nav_menu_item_id', $menu_item->ID, $menu_item, $args, $depth );
			if ( $li_id ) {
				$li_atts['id'] = \esc_attr( $li_id );
			}

			$li_atts = apply_filters( 'nav_menu_item_attributes', $li_atts, $menu_item, $args, $depth );
		}

		// Build attributes
		$li_attributes = $this->build_atts( $li_atts );

		// Open li
		$output .= '<li' . $li_attributes . '>';

		// Open menu item content
		$content_class = [
			'vcex-horizontal-menu-nav__item-content',
			'wpex-flex',
			'wpex-flex-grow',
			'wpex-gap-5',
			'wpex-relative',
			$is_current ? 'wpex-text-accent' : 'wpex-text-2',
			'wpex-hover-text-accent',
		];

		if ( $is_current && \vcex_validate_att_boolean( 'item_current_underline', $args->vcex_atts ) ) {
			$content_class[] = 'wpex-underline';
			$content_class[] = 'wpex-decoration-current';
			$content_class[] = 'wpex-decoration-solid';
			$thickness = ! empty( $args->vcex_atts['item_current_underline_thickness'] ) ? \absint( $args->vcex_atts['item_current_underline_thickness'] ) : '';
			if ( $thickness ) {
				$content_class[] = "wpex-decoration-{$thickness}";
			}
			$offset = ! empty( $args->vcex_atts['item_current_underline_offset'] ) ? \absint( $args->vcex_atts['item_current_underline_offset'] ) : '';
			if ( $offset ) {
				$content_class[] = "wpex-underline-offset-{$offset}";
			}
		} else {
			$content_class[] = 'wpex-no-underline';
		}

		if ( \vcex_validate_att_boolean( 'item_bg_hover_enable', $args->vcex_atts, true ) ) {
			$content_class[] = 'wpex-hover-surface-2';
		//	$content_class[] = 'wpex-hover-text-2';
		}

		// Padding Y
		if ( empty( $args->vcex_atts['item_padding_block'] ) ) {
			$content_class[] = 'wpex-py-10';
		}

		// Padding X
		if ( empty( $args->vcex_atts['item_padding_inline'] ) ) {
			$content_class[] = 'wpex-px-15';
		}

		// Transition duration
		$t_duration = ! empty( $args->vcex_atts['item_transition_duration'] ) ? \absint( $args->vcex_atts['item_transition_duration'] ) : 0;
		if ( $t_duration ) {
			$content_class[] = "wpex-duration-{$t_duration}";
		}

		// Border radius
		if ( empty( $args->vcex_atts['item_border_radius'] ) ) {
			$content_class[] = 'wpex-rounded-sm';
		}

		if ( empty( $args->vcex_atts['sub_arrow_spacing'] ) ) {
			$content_class[] = 'wpex-gap-10';
		}

		if ( $this->is_mega && $this->mega_no_headings && 1 === $depth ) {
			$content_class = array_combine( $content_class, $content_class );
			unset( $content_class['wpex-flex'] );
			$content_class[] = 'wpex-hidden';
		}

		// Convert content_class to string
		$content_class_string = implode( ' ', $content_class );

		// Open content wrapper
		if ( $is_extra ) {
			$content_class_string .= ' wpex-unstyled-button';
			$extra_tag = 'button';
			switch ( $extra_id ) {
				case 'search_toggle':
					\totaltheme_get_instance_of( 'Search\Modal' );
					$output .= '<button class="' . \esc_attr( $content_class_string ) . ' wpex-open-modal" aria-controls="wpex-search-modal" aria-expanded="false" aria-label="' . \vcex_get_aria_label( 'search' ) . '">';
					break;
				case 'dark_mode_toggle':
					if ( function_exists( 'totaltheme_call_static' ) && totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
						$output .= '<button class="' . \esc_attr( $content_class_string ) . '" data-wpex-toggle="theme" aria-label="' . \vcex_get_aria_label( 'dark_mode_toggle' ) . '">';
					}
					break;
				case 'cart_toggle':
					if ( \class_exists( '\WooCommerce', false ) ) {
						$has_cart_badge = \vcex_validate_att_boolean( 'cart_badge', $args->vcex_atts );
						if ( \class_exists( '\TotalTheme\Integration\WooCommerce\Cart\Off_Canvas', false ) ) {
							$output .= '<button class="' . \esc_attr( $content_class_string ) . '" data-wpex-toggle="off-canvas" aria-controls="wpex-off-canvas-cart" aria-expanded="false" aria-label="' . \vcex_get_aria_label( 'cart_open' ) . '">';
						} else {
							$extra_tag = 'a';
							$cart_link = \function_exists( '\wc_get_cart_url' ) ? \wc_get_cart_url() : '#';
							$output .= '<a href="' . \esc_url( $cart_link ) . '" class="' . \esc_attr( $content_class_string ) . '">';
						}
					}
					break;
			}
		} elseif ( $has_link ) {
			$has_link = true;
			$link_atts = [ 'class' => \esc_attr( $content_class_string ) ];
			$link_atts['title']  = ! empty( $menu_item->attr_title ) ? $menu_item->attr_title : '';
			$link_atts['target'] = ! empty( $menu_item->target ) ? $menu_item->target : '';

			if ( '_blank' === $menu_item->target && empty( $menu_item->xfn ) ) {
				$link_atts['rel'] = 'noopener';
			} else {
				$link_atts['rel'] = $menu_item->xfn;
			}

			if ( ! empty( $menu_item->url ) ) {
				$link_atts['href'] = $menu_item->url;
			} else {
				$link_atts['href'] = '';
			}

			$link_atts['aria-current'] = $menu_item->current ? 'page' : '';

			if ( $add_hooks ) {
				$link_atts = apply_filters( 'nav_menu_link_attributes', $link_atts, $menu_item, $args, $depth );
			}

			$output .= '<a' . $this->build_atts( $link_atts ) . '>';

		} else {
			$has_link = false;
			if ( $depth > 0 && $this->is_mega ) {
				$content_class_string .= ' wpex-pointer-events-none';
			}
			if ( $this->is_mega ) {
				$output .= '<div class="' . \esc_attr( $content_class_string ) . '">';
			} else {
				$output .= '<div class="' .  \esc_attr( $content_class_string ) . '" tabindex="0">'; // tab index needed to trigger drops
			}
		}

		// Open icon wrapper
		if ( $icon && $menu_item_icon_html ) {
			$icon_wrap_class = 'vcex-horizontal-menu-nav__icon-wrap wpex-flex wpex-self-center';
			if ( $has_description ) {
				$icon_wrap_class .= ' wpex-gap-15';
			} else {
				$icon_wrap_class .= ' wpex-gap-10';
			}
			$output .= '<div class="' . \esc_attr( $icon_wrap_class ) . '">' . $menu_item_icon_html;
		}

		// Text element
		$text_class = 'vcex-horizontal-menu-nav__item-text wpex-flex ';

		if ( $has_description ) {
			$text_class .= ' wpex-flex-col wpex-self-center';
		} else {
			$text_class .= ' wpex-items-center';
		}

		if ( $is_extra && 'cart_toggle' === $extra_id && isset( $has_cart_badge ) && true === $has_cart_badge ) {
			$text_class .= ' wpex-relative';
		}

		$output .= '<div class="' . esc_attr( $text_class ) . '">';

			if ( $is_extra ) {
				switch ( $extra_id ) {
					case 'search_toggle':
						$search_toggle_icon = ! empty( $args->vcex_atts['search_toggle_icon'] ) ? \sanitize_text_field( $args->vcex_atts['search_toggle_icon'] ) : 'search';
						$search_toggle_icon_size = \str_starts_with( $search_toggle_icon, 'material' ) ? 'md' : '';
						$output .= \vcex_get_theme_icon_html( $search_toggle_icon, 'wpex-flex', $search_toggle_icon_size );
						break;
					case 'dark_mode_toggle':
						if ( function_exists( 'totaltheme_call_static' )
							&& totaltheme_call_static( 'Dark_Mode', 'is_enabled' )
							&& \is_callable( 'TotalTheme\Dark_mode::get_icon_name' )
						) {
							$output .= \vcex_get_theme_icon_html( \TotalTheme\Dark_mode::get_icon_name( 'dark' ), 'hidden-dark-mode wpex-flex' );
							$output .= \vcex_get_theme_icon_html( \TotalTheme\Dark_mode::get_icon_name( 'light' ), 'visible-dark-mode wpex-flex' );
						}
						break;
					case 'cart_toggle':
						if ( \class_exists( '\WooCommerce', false ) ) {
							$cart_toggle_icon = ! empty( $args->vcex_atts['cart_toggle_icon'] ) ? \sanitize_text_field( $args->vcex_atts['cart_toggle_icon'] ) : 'shopping-cart-alt';
							$cart_toggle_icon_size = \str_starts_with( $cart_toggle_icon, 'material' ) ? 'md' : '';
							$output .= \vcex_get_theme_icon_html( $cart_toggle_icon, 'wpex-flex', $cart_toggle_icon_size );
							if ( isset( $has_cart_badge ) && true === $has_cart_badge ) {
								$output .= totalthemecore_call_static(
									'Vcex\WooCommerce',
									'get_cart_badge',
									\vcex_validate_att_boolean( 'cart_badge_count', $args->vcex_atts )
								);
							}
						}
						break;
				}
			} else {

				if ( $add_hooks ) {
					$menu_item_title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );
				} else {
					$menu_item_title = wp_kses_post( do_shortcode( sanitize_text_field( $menu_item->title ) ) );
				}

				// Menu item text wrapper
				if ( $this->is_mega && 1 === $depth ) {
					$text_heading_class = 'vcex-horizontal-menu-nav__mega-heading wpex-bold wpex-text-lg';
				} elseif ( $has_description ) {
					$text_heading_class = 'vcex-horizontal-menu-nav__item-heading wpex-bold wpex-mb-5';
				} else {
					$text_heading_class = ''; // must reset after each item
				}

				if ( $text_heading_class ) {
					$output .= '<div class="' . \esc_attr( $text_heading_class ) . '">' . $menu_item_title . '</div>';
				} else {
					$output .= $menu_item_title;
				}

				// Add menu item description
				if ( $has_description ) {
					$output .= '<p class="vcex-horizontal-menu-nav__item-desc wpex-text-pretty wpex-text-sm wpex-m-0">' . \esc_html( (string) $menu_item->description ) . '</p>';
				}
			}

		// closes text element
		$output .= '</div>';

		// Close icon wrap
		if ( $icon && $menu_item_icon_html ) {
			$output .= '</div>';
		}

		// Horizontal Menu Dropdown arrows
		if ( $has_sub_arrow ) {
			$sub_arrow = '';
			if ( $show_sub_arrows ) {
				if ( 0 === $depth ) {
					$sub_arrow = self::_get_down_arrow( $args );
				} else {
					$sub_arrow = self::_get_side_arrow( $args );
				}
			}
			if ( $sub_arrow ) {
				$output .= '<span aria-hidden="true" class="vcex-horizontal-menu-nav__arrow wpex-flex wpex-items-center wpex-justify-end wpex-leading-none wpex-ml-auto">' . $sub_arrow . '</span>';
			}
		}

		// Close menu item content
		if ( $is_extra ) {
			$output .= "</{$extra_tag}>";
		} elseif ( $has_link ) {
			$output .= '</a>';
		} else {
			$output .= '</div>';
		}

		if ( $add_hooks ) {
			$output = apply_filters( 'walker_nav_menu_start_el', $output, $menu_item, $depth, $args );
		}
	}

	/**
	 * Ends the list of after the elements are added.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$this->current_el = null;
		$output .= '</ul>';
	}

	/**
	 * Parses the menu item classes to remove/alter them.
	 */
	protected function parse_menu_item_classes( array $classes, $args = [] ): array {
		foreach ( $classes as $class_k => $class_v ) {
			if ( ! \is_string( $class_v ) ) {
				continue;
			}
			// These classes are not allowed for the menu element
			if ( 'nav-no-click' === $class_v ) {
				unset( $classes[ $class_k ] );
			} elseif ( 'megamenu' === $class_v ) {
				$classes[ $class_k ] = 'vcex-horizontal-menu-nav__item--has_mega';
				if ( ! empty( $args->vcex_atts['mega_width'] )
					&& ( \str_ends_with( (string) $args->vcex_atts['mega_width'], '%' ) || '100vw' === $args->vcex_atts['mega_width'] )
				) {
					$classes[] = 'wpex-static';
				}
			} elseif ( 'hide-headings' === $class_v ) {
				$classes[ $class_k ] = 'vcex-horizontal-menu-nav__item--has_mega-no-headings';
			} elseif ( 'megamenu-col-full' === $class_v ) {
				$classes[ $class_k ] = 'wpex-col-span-full';
			} elseif( \str_starts_with( $class_v, 'col-' ) ) {
				unset( $classes[ $class_k ] );
				$this->mega_cols = \str_replace( 'col-', '', $class_v );
			}
		}
		if ( ! \vcex_validate_att_boolean( 'mega_heading_enabled', $args->vcex_atts, true )
			&& ! \in_array( 'vcex-horizontal-menu-nav__item--has_mega-no-headings', $classes, true )
		) {
			$classes[] = 'vcex-horizontal-menu-nav__item--has_mega-no-headings';
		}
		return $classes;
	}

	/**
	 * Returns side arrow.
	 */
	private function _get_side_arrow( $args = [] ) {
		$icon = ! empty( $args->vcex_atts['sub_arrow_icon'] ) ? \sanitize_text_field( $args->vcex_atts['sub_arrow_icon'] ) : 'chevron';
		return (string) \vcex_get_theme_icon_html( "{$icon}-right", 'vcex-horizontal-menu-nav__arrow-icon', 'xs', true );
	}

	/**
	 * Returns down arrow.
	 */
	private function _get_down_arrow( $args = [] ) {
		$icon = ! empty( $args->vcex_atts['sub_arrow_icon'] ) ? \sanitize_text_field( $args->vcex_atts['sub_arrow_icon'] ) : 'chevron';
		return (string) \vcex_get_theme_icon_html( "{$icon}-down", 'vcex-horizontal-menu-nav__arrow-icon', 'xs', false );
	}
	
}
