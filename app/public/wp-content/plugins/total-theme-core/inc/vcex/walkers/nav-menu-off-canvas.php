<?php

namespace TotalThemeCore\Vcex\Walkers;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom walker used to create mobile menus.
 */
class Nav_Menu_Off_Canvas extends \Walker_Nav_Menu {

	/**
	 * Check if currently inside a mega menu.
	 */
	protected $is_mega = false;

	/**
	 * Check if mega menu headings are disabled.
	 */
	protected $mega_no_headings = false;

	/**
	 * Starts the list before the elements are added.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( $this->is_mega && $this->mega_no_headings && 1 === $depth ) {
			return;
		}

		$add_hooks = vcex_validate_att_boolean( 'add_hooks', $args->vcex_atts, true );

		$classes = [
			'vcex-off-canvas-menu-nav__sub',
			'wpex-list-none',
		];

		if ( $args->vcex_atts['nav_centered'] ) {
			$classes[] = 'wpex-self-justify-center';
		}

		if ( $args->vcex_atts['nav_centered'] || $args->vcex_atts['sub_border_enable'] ) {
			if ( $args->vcex_atts['item_divider'] ) {
				$classes[] = 'wpex-mt-0';
				$classes[] = 'wpex-mx-0';
				$classes[] = 'wpex-mb-15';
			} else {
				$classes[] = 'wpex-m-0';
			}
		} else {
			if ( 0 === $depth ) {
				$side_margin = ! empty( $args->vcex_atts['sub_margin_start'] ) ? absint( $args->vcex_atts['sub_margin_start'] ) : 15;
			} else {
				$side_margin = ! empty( $args->vcex_atts['sub_sub_margin_start'] ) ? absint( $args->vcex_atts['sub_sub_margin_start'] ) : 15;
			}
			if ( $args->vcex_atts['item_divider'] ) {
				$classes[] = 'wpex-mt-0';
				$classes[] = 'wpex-mb-15';
			} else {
				$classes[] = 'wpex-my-0';
			}
			$classes[] = "wpex-mr-0 wpex-ml-{$side_margin}";
		}

		if ( $args->vcex_atts['sub_border_enable'] && ! $args->vcex_atts['nav_centered'] ) {
			$classes[] = 'wpex-border-l-2 wpex-border-solid wpex-border-surface-3 wpex-pl-20';
		}

		if ( $add_hooks ) {
			$classes = apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth );
		}

		$atts = [];
		$atts['class'] = ! empty( $classes ) ? implode( ' ', $classes ) : '';
		$attributes = $this->build_atts( $atts );

		$output .= "<ul{$attributes}>";
	}

	/**
	 * Ends the list of after the elements are added.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( $this->is_mega && $this->mega_no_headings && 1 === $depth ) {
			return;
		}

		$output .= '</ul>';
		if ( 0 === $depth ) {
			$output .= '</details>';
		}
	}

	/**
	 * Starts the element output.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		// Restores the more descriptive, specific name for use within this method
		$menu_item = $data_object;

		// Check if we should add the core hooks
		$add_hooks = vcex_validate_att_boolean( 'add_hooks', $args->vcex_atts, true );

		// Filter args
		if ( $add_hooks ) {
			$args = apply_filters( 'nav_menu_item_args', $args, $menu_item, $depth );
		}

		// Checks if the menu item has a description
		$has_description = ! empty( $menu_item->description );

		// Check if menu item has a link
		$has_link = ! empty( $menu_item->url ) && '#' !== $menu_item->url;

		// Check if has details
		$has_details = 0 === $depth && ! empty( $this->has_children ) && ! $args->vcex_atts['sub_expanded'];

		// Get menu item icon
		$icon = ( $icon = \get_post_meta( $menu_item->ID, '_menu_item_totaltheme_icon', true ) ) ? \sanitize_text_field( $icon ) : '';
		if ( $icon ) {
			$icon_class = 'vcex-off-canvas-menu-nav__icon wpex-flex-shrink-0 wpex-icon--w';
			if ( $args->vcex_atts['item_transition_duration'] ) {
				$icon_class .= " wpex-transition-{$args->vcex_atts['item_transition_duration']}";
			}
			$menu_item_icon_html = \vcex_get_theme_icon_html( $icon, $icon_class );
		}

		// Li classes
		$classes = empty( $menu_item->classes ) ? [] : (array) $menu_item->classes;
		$classes[] = \esc_attr( "menu-item-{$menu_item->ID}" );

		if ( 0 === $depth ) {
			$mega_cols = \get_post_meta( $menu_item->ID, '_menu_item_totaltheme_mega_cols', true );
			if ( \is_numeric( $mega_cols ) && (int) $mega_cols > 0 ) {
				$classes[] = 'megamenu'; // send to parse_menu_item_classes()
			}
		}

		// Parse classes
		$classes = $this->parse_menu_item_classes( $classes, $args, $depth);

		// Megamenu check (resets at the start of each lvl 0 item)
		if ( 0 === $depth ) {
			$this->is_mega = \in_array( 'vcex-off-canvas-menu-nav__item--has_mega', $classes, true );
			if ( $this->is_mega ) {
				$this->mega_no_headings = \in_array( 'vcex-off-canvas-menu-nav__item--has_mega-no-headings', $classes, true );
			}
		}

		if ( $args->vcex_atts['nav_centered'] ) {
			$classes[] = 'wpex-text-center';
		}

		// Add main li class to top of array
		\array_unshift( $classes, 'vcex-off-canvas-menu-nav__item' );

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

		// No mega menu headings - don't add content, and combine li into parent.
		if ( $this->is_mega && $this->mega_no_headings && 1 === $depth ) {
			$has_content = false;
		} else {
			$has_content = true;
		}

		// Open menu item content
		if ( $has_content ) {

			$content_class = [
				'vcex-off-canvas-menu-nav__item-content',
				$args->vcex_atts['nav_centered'] ? 'wpex-inline-flex' : 'wpex-flex',
				'wpex-gap-5',
				'wpex-relative',
				'wpex-text-2',
				'wpex-hover-text-2',
				'wpex-no-underline',
			];

			// Justify content
			if ( ! empty( $args->vcex_atts['item_justify_content'] ) ) {
				$content_class[] = vcex_parse_justify_content_class( $args->vcex_atts['item_justify_content'] );
			}

			// Padding Y
			if ( empty( $args->vcex_atts['item_padding_block'] ) ) {
				$content_class[] = 0 === $depth ? 'wpex-py-15' : 'wpex-py-10';
			}

			// Transition duration
			if ( $args->vcex_atts['item_transition_duration'] ) {
				$content_class[] = "wpex-duration-{$args->vcex_atts['item_transition_duration']}";
			}

			// Turn content class insto string
			$content_class_string = implode( ' ', $content_class );

			if ( $has_details ) {
				$output .= '<details class="wpex-m-0"><summary class="' . \esc_attr( $content_class_string ) . ' wpex-list-none">';
			} elseif ( $has_link ) {
				$has_link = true;
				$link_atts = [
					'class' => \esc_attr( $content_class_string ),
				];

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
				$output .= '<div class="' . \esc_attr( $content_class_string ) . '">';
			}

			// Open icon wrapper
			if ( $icon && $menu_item_icon_html ) {
				$icon_wrap_class = 'vcex-off-canvas-menu-nav__icon-wrap wpex-flex wpex-self-center';
				if ( $args->vcex_atts['nav_centered'] ) {
					$icon_wrap_class .= ' wpex-text-left';
				}
				if ( $has_description ) {
					$icon_wrap_class .= ' wpex-gap-15';
				} else {
					$icon_wrap_class .= ' wpex-gap-10';
				}
				$output .= '<div class="' . \esc_attr( $icon_wrap_class ) . '">' . $menu_item_icon_html;
			}

			// Text element
			$text_class = [
				'vcex-off-canvas-menu-nav__item-text',
				'wpex-flex',
			];

			if ( $has_description ) {
				$text_class[] = 'wpex-flex-col';
				$text_class[] = 'wpex-self-center';
			} else {
				$text_class[] = 'wpex-items-center';
			}

			$output .= '<div class="' . esc_attr( implode( ' ', $text_class ) ) . '">';

				if ( $add_hooks ) {
					$menu_item_title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );
				} else {
					$menu_item_title = wp_kses_post( do_shortcode( sanitize_text_field( $menu_item->title ) ) );
				}

				// Menu item text wrapper
				if ( $this->is_mega && 1 === $depth ) {
					$text_heading_class = 'vcex-off-canvas-menu-nav__mega-heading wpex-bold wpex-text-lg';
				} elseif ( $has_description ) {
					$text_heading_class = 'vcex-off-canvas-menu-nav__item-heading wpex-bold wpex-mb-5';
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
					$output .= '<p class="vcex-off-canvas-menu-nav__item-desc wpex-text-pretty wpex-text-sm wpex-m-0">' . \esc_html( (string) $menu_item->description ) . '</p>';
				}

			// closes inner element
			$output .= '</div>';

			// Close icon wrap
			if ( $icon && $menu_item_icon_html ) {
				$output .= '</div>';
			}

			// Close menu item content
			if ( $has_details ) {
				$output .= '</div>';
				if ( $args->vcex_atts['sub_arrow_enable'] ) {
					$output .= $this->_get_down_arrow( $args );
				}
				$output .= '</summary>';
			} else if ( $has_link ) {
				$output .= '</a>';
			} else {
				$output .= '</div>';
			}

		}

		if ( $add_hooks ) {
			$output = apply_filters( 'walker_nav_menu_start_el', $output, $menu_item, $depth, $args );
		}
	}

	/**
	 * Ends the element output, if needed.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		if ( 0 === $depth && ! empty( $this->has_children ) ) {
			$output .= '</details>';
		}
		if ( $args->vcex_atts['item_divider'] && 0 === $depth ) {
			$output .= '<div class="vcex-off-canvas-menu-nav__item-divider wpex-h-1px wpex-surface-3"></div>';
		}
		$output .= '</li>';
	}

	/**
	 * Parses the menu item classes to remove/alter them.
	 */
	protected function parse_menu_item_classes( array $classes, $args = [] ): array {
		foreach ( $classes as $class_k => $class_v ) {
			if ( ! \is_string( $class_v ) ) {
				continue;
			}
			if ( 'hide-headings' === $class_v ) {
				$classes[ $class_k ] = 'vcex-off-canvas-menu-nav__item--has_mega-no-headings';
			} elseif ( 'megamenu' === $class_v ) {
				$classes[ $class_k ] = 'vcex-off-canvas-menu-nav__item--has_mega';
			} elseif ( 'flip-dropdown' === $class_v
				|| 'megamenu' === $class_v
				|| 'hide-headings' === $class_v
				|| 'megamenu' === $class_v
				|| \str_starts_with( $class_v, 'col-' )
			) {
				unset( $classes[ $class_k ] );
			}
		}
		if ( ! \vcex_validate_att_boolean( 'mega_heading_enabled', $args->vcex_atts, true )
			&& ! \in_array( 'vcex-off-canvas-menu-nav__item--has_mega-no-headings', $classes, true )
		) {
			$classes[] = 'vcex-off-canvas-menu-nav__item--has_mega-no-headings';
		}
		return $classes;
	}

	/**
	 * Returns down arrow.
	 */
	private function _get_down_arrow( $args = [] ) {
		$icon = $args->vcex_atts['sub_arrow_icon'];
		return (string) \vcex_get_theme_icon_html( "{$icon}-down", 'vcex-off-canvas-menu-nav__arrow-icon wpex-flex wpex-items-center', 'xs', false );
	}

}
