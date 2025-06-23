<?php

/**
 * vcex_navbar shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0.2
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $atts['menu'] ) ) {
	return;
}

// Main vars
$output           = '';
$menu_id           = $atts['menu'] ?? '';
$preset_design     = ! empty( $atts['preset_design'] ) ? $atts['preset_design'] : 'none';
$button_style      = ! empty( $atts['button_style'] ) ? $atts['button_style'] : '';
$button_layout     = ! empty( $atts['button_layout'] ) ? $atts['button_layout'] : '';
$url_sort          = vcex_validate_att_boolean( 'url_sort', $atts, false );
$has_mobile_select = vcex_validate_att_boolean( 'mobile_select', $atts, false );
$filter_menu       = $atts['filter_menu'] ?? false;
$filter_mode       = ( $filter_menu && ! empty( $atts['filter_layout_mode'] ) ) ? $atts['filter_layout_mode'] : 'masonry';
$show_term_count   = vcex_validate_att_boolean( 'term_count', $atts );

if ( 'current_tax_terms' === $menu_id && ( ! is_tax() && ! is_category() && ! is_tag() ) ) {
	return;
}

// Get menu object
switch ( $menu_id ) {
	case 'current_tax_terms':
		if ( is_category() ) {
			$current_tax = 'category';
		} elseif ( is_tag() ) {
			$current_tax = 'post_tag';
		} else {
			$current_tax = get_query_var( 'taxonomy' );
		}
		$args = [
			'hide_empty' => true,
			'taxonomy'   => $current_tax,
			'child_of'   => get_queried_object_id(),
		];
		if ( is_callable( 'Vcex_Navbar_Shortcode::get_menu_items_from_terms' ) ) {
			$menu_items = Vcex_Navbar_Shortcode::get_menu_items_from_terms( $args, $atts );
		}
		break;
	case 'post_terms':
	case 'dynamic_terms':
		if ( ! empty( $atts['taxonomy'] ) && taxonomy_exists( $atts['taxonomy'] ) ) {
			$args = [
				'hide_empty' => false,
				'taxonomy'   => $atts['taxonomy'],
			];

			if ( $filter_menu && 'ajax' === $filter_mode ) {
				$args['hide_empty'] = true;
			}

			if ( ! empty( $atts['terms_not_in'] ) ) {
				$terms_not_in = vcex_string_to_array( $atts['terms_not_in'] );
				if ( $terms_not_in && is_array( $terms_not_in ) ) {
					$args['exclude'] = $terms_not_in;
				}
			}

			if ( ! empty( $atts['child_of'] ) && is_scalar( $atts['child_of'] ) ) {
				$args['child_of'] = $atts['child_of'];
			}

			if ( vcex_validate_att_boolean( 'parent_terms_only', $atts ) ) {
				$args['parent'] = 0;
			}

			if ( is_callable( 'Vcex_Navbar_Shortcode::get_menu_items_from_terms' ) ) {
				$menu_items = Vcex_Navbar_Shortcode::get_menu_items_from_terms( $args, $atts );
			}
		}
		break;
	case 'post_children':
		$menu_items = Vcex_Navbar_Shortcode::get_post_children_menu_items( $atts );
		break;
	default:
		$wp_nav_menu_obj = wp_get_nav_menu_object( $menu_id );
		if ( ! empty( $wp_nav_menu_obj->term_id ) ) {
			$menu_items = wp_get_nav_menu_items( $wp_nav_menu_obj->term_id );
		}
		break;
}

// Bail early if there aren't any menu items
if ( empty( $menu_items ) || ! is_array( $menu_items ) ) {
	return;
}

if ( isset( $atts['align'] ) && ! in_array( $button_layout, [ 'expanded', 'spaced_out' ] ) ) {
	$align = $atts['align'];
} else {
	$align = '';
}

// Link margins
$default_link_margin_side = ( ! $button_layout || 'spaced_out' === $button_layout ) ? '5' : '';
$default_link_margin_side = ( 'none' === $preset_design ) ? $default_link_margin_side : '10';
$link_margin_side = ! empty( $atts['link_margin_side'] ) ? absint( $atts['link_margin_side'] ) : $default_link_margin_side;
$default_link_margin_bottom = ( 'none' === $preset_design ) ? '5' : '';
$link_margin_bottom = ! empty( $atts['link_margin_bottom'] ) ? absint( $atts['link_margin_bottom'] ) : $default_link_margin_bottom;

// Hover animation
if ( ! empty( $atts['hover_animation'] ) ) {
	$atts['hover_animation'] = vcex_hover_animation_class( $atts['hover_animation'] );
}

// Wrap classes
$wrap_classes = [
	'vcex-navbar',
];

if ( $preset_design && 'none' !== $preset_design ) {
	$wrap_classes[] = 'vcex-navbar--' . sanitize_html_class( $preset_design );
}

if ( $button_layout ) {
	$wrap_classes[] = 'vcex-navbar--' . sanitize_html_class( $button_layout );
}

$wrap_classes[] = 'vcex-module';

$wrap_data = [];

if ( $filter_menu ) {

	switch ( $filter_mode ) {

		// AJAX Filter
		case 'ajax':
			$wrap_data[] = 'data-vcex-ajax-filter="1"';
			$wrap_data[] = 'data-vcex-ajax-filter-target="' . trim( esc_attr( $filter_menu ) ) . '"';
			$filter_relation = ! empty( $atts['filter_relation'] ) ? strtoupper( $atts['filter_relation'] ) : '';

			if ( in_array( $filter_relation, [ 'AND', 'OR' ] ) ) {
				$wrap_data[] = 'data-vcex-ajax-filter-multiple="1"';
				$wrap_data[] = 'data-vcex-ajax-filter-relation="' . trim( esc_attr( $filter_relation ) ) . '"';
			} else {
				$wrap_data[] = 'data-vcex-ajax-filter-multiple="0"';
			}

			if ( ! empty( $atts['filter_active_item'] ) ) {
				$wrap_data[] = 'data-vcex-ajax-filter-ignore-tax-query="1"';
			}

			if ( $show_term_count ) {
				if ( 'and' === strtolower( $filter_relation ) ) {
					$wrap_data[] = 'data-vcex-ajax-filter-update-counts="1"';
				}
				$wrap_data[] = 'data-vcex-ajax-filter-set-counts="' . trim( absint( $show_term_count ) ) . '"';
			}

			// Move active term to the front of the array
			if ( ! empty( $atts['filter_active_item'] ) ) {
				foreach ( $menu_items as $menu_item_k => $menu_item ) {
					if ( isset( $menu_item->object_id )
						&& absint( $menu_item->object_id ) === absint( $atts['filter_active_item'] )
					) {
						unset( $menu_items[$menu_item_k] );
						$menu_items = array_merge( [ $menu_item ], $menu_items );
						break;
					}
				}
			}
			break;

		// Masonry or Show/Hide Filter
		default:
			$wrap_classes[] = 'vcex-filter-nav';

			if ( ! empty( $atts['taxonomy'] ) ) {
				$filter_group = $atts['taxonomy'];
			} elseif ( ! empty( $atts['unique_id'] ) ) {
				$filter_group = $atts['unique_id'];
			} elseif ( $menu_id ) {
				$filter_group = $menu_id;
			} else {
				$filter_group = uniqid();
			}

			$wrap_data[] = 'data-filter-group="' . esc_attr( $filter_group ) . '"';
			$wrap_data[] = 'data-filter-grid="' . esc_attr( $filter_menu ) . '"';

			if ( 'masonry' === $filter_mode || 'fitRows' === $filter_mode ) {
				if ( 'fitRows' === $filter_mode ) {
					$wrap_data[] = 'data-layout-mode="fitRows"';
				}
				if ( $atts['filter_transition_duration'] ) {
					$wrap_data[] = 'data-transition-duration="'. esc_attr( $atts['filter_transition_duration'] ) .'"';
				}
			}

			$active_filter_item = vcex_grid_filter_get_active_item();

			break;
	}
}

if ( 'none' !== $preset_design ) {
	$wrap_classes[] = 'vcex-navbar-' . sanitize_html_class( $preset_design );
	switch ( $preset_design ) {
		case 'dark':
			$wrap_classes[] = 'wpex-bg-gray-A900';
			$wrap_classes[] = 'wpex-p-20';
			break;
	}
}

if ( vcex_validate_att_boolean( 'sticky', $atts, false ) ) {
	$wrap_classes[] = 'vcex-navbar-sticky';
	$wrap_classes[] = 'wpex-transition-opacity';
	$wrap_classes[] = 'wpex-duration-300';
	if ( vcex_validate_att_boolean( 'sticky_offset_nav_height', $atts, true ) ) {
		$wrap_classes[] = 'vcex-navbar-sticky-offset';
		$wrap_classes[] = 'wpex-ls-offset';
	}
	if ( ! empty( $atts['sticky_endpoint'] ) ) {
		$wrap_data[] = 'data-sticky-endpoint="' . esc_attr( $atts['sticky_endpoint'] ) . '"';
	}
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_classes[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( $atts['bottom_margin'] ) {
	$wrap_classes[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( $align ) {
	$wrap_classes[] = 'align-' . sanitize_html_class( $align );
	switch ( $align ) {
		case 'center':
			$wrap_classes[] = 'wpex-text-center';
			break;
		case 'left':
		case 'right':
			$wrap_classes[] = 'wpex-clr';
			break;
	}
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_classes[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['wrap_css'] ) ) {
	$wrap_classes[] = vcex_vc_shortcode_custom_css_class( $atts['wrap_css'] );
}

if ( ! empty( $atts['classes'] ) ) {
	$wrap_classes[] = vcex_get_extra_class( $atts['classes'] );
}

// Wrap attributes
$wrap_attrs = [
	'id'    => ! empty( $atts['unique_id'] ) ? $atts['unique_id'] : null,
	'class' => vcex_parse_shortcode_classes( $wrap_classes, 'vcex_navbar', $atts ),
	'data'  => $wrap_data,
];

if ( ! empty( $atts['aria_label'] ) ) {
	$wrap_attrs['aria-label'] = esc_attr( $atts['aria_label'] );
}

// Begin output
$output .= '<nav' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	// Inner classes
	$inner_classes = 'vcex-navbar-inner';

	switch ( $align ) {
		case 'right':
			$inner_classes .= ' wpex-float-right';
			break;
		case 'left':
			$inner_classes .= ' wpex-float-left';
			break;
	}

	switch ( $button_layout ) {
		case 'spaced_out':
			$inner_classes .= ' wpex-flex wpex-flex-wrap wpex-justify-between wpex-items-center';
			$inner_classes .= ' wpex-last-mr-0';
			break;
		default:
			$inner_classes .= ' wpex-clr';
			break;
	}

	if ( vcex_validate_att_boolean( 'full_screen_center', $atts, false ) ) {
		$inner_classes .= ' container';
	}

	if ( $link_margin_side ) {
		$inner_classes .= ' wpex-last-mr-0';
	}

	if ( $has_mobile_select ) {
		$inner_classes .= ' visible-desktop';
	}

	// Beginner inner output
	$output .= '<div class="'. esc_attr( $inner_classes ) .'">';

		// Classes added to all links
		$all_link_class = [
			'vcex-navbar-link',
		];

		if ( 'center' === $align || 'dark' === $preset_design ) {
			$all_link_class[] = 'wpex-inline-block';
		} else {
			if ( 'plain-text' === $button_style ) {
				$all_link_class[] = 'wpex-inline-block';
			} else {
				$all_link_class[] = 'wpex-block';
			}
		}

		if ( 'center' !== $align ) {
			$all_link_class[] = ( 'right' === $align && 'list' === $button_layout ) ? 'wpex-float-right' : 'wpex-float-left';
		}

		switch ( $preset_design ) {
			case 'dark':
				$all_link_class[] = 'wpex-text-white';
				$all_link_class[] = 'wpex-hover-text-white';
				$all_link_class[] = 'wpex-no-underline';
				$all_link_class[] = 'wpex-opacity-70';
				$all_link_class[] = 'wpex-hover-opacity-100';
				$all_link_class[] = 'wpex-active-opacity-100';
				$all_link_class[] = 'wpex-transition-1500';
				break;
		}

		if ( $link_margin_side ) {
			$all_link_class[] = 'wpex-mr-' . absint( $link_margin_side );
		}

		if ( $link_margin_bottom ) {
			$all_link_class[] = 'wpex-mb-' . absint( $link_margin_bottom );
		}

		if ( 'none' === $preset_design ) {
			$all_link_class[] = vcex_get_button_classes( $button_style, $atts['button_color'] );
			if ( 'spaced_out' === $button_layout ) {
				if ( vcex_validate_att_boolean( 'expand_links', $atts, false ) ) {
					$all_link_class[] = 'wpex-flex-grow wpex-text-center';
				}
			} else {
				$all_link_class[] = $button_layout;
			}
		}

		if ( ! empty( $atts['font_weight'] ) ) {
			$all_link_class[] = vcex_font_weight_class( $atts['font_weight'] );
		}

		if ( vcex_validate_att_boolean( 'local_scroll', $atts, false ) ) {
			$all_link_class[] = 'local-scroll';
		}

		if ( ! empty( $atts['font_size'] ) ) {
			$all_link_class[] = 'wpex-text-1em'; // @important to override button font size.
		}

		if ( ! empty( $atts['link_transition_duration'] ) ) {
			$all_link_class[] = 'wpex-transition-all';
		}

		if ( ! empty( $atts['hover_animation'] ) ) {
			$all_link_class[] = $atts['hover_animation'];
		}

		if ( ! empty( $atts['hover_bg'] ) ) {
			$all_link_class[] = 'has-bg-hover';
		}

		if ( ! empty( $atts['border_radius'] ) ) {
			$all_link_class[] = vcex_get_border_radius_class( $atts['border_radius'] );
		}

		if ( ! empty( $atts['css'] ) ) {
			$all_link_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
		}

		// Define counter var
		$counter = 0;

		// Store some checks to prevent extra checks later
		$is_singular       = is_singular();
		$is_home           = is_home();
		$is_shop           = function_exists( 'is_shop' ) && is_shop();
		$is_tax_archive    = is_tax() || is_tag() || is_category();
		$queried_object_id = (int) get_queried_object_id();
		$page_for_posts    = (int) get_option( 'page_for_posts' );

		if ( $is_singular ) {
			$post_id      = (int) vcex_get_the_ID();
			$post_parents = get_post_ancestors( $post_id );
			$post_type    = get_post_type();
		}

		if ( $is_shop ) {
			$shop_page_id = function_exists( 'totaltheme_wc_get_page_id' ) ? (int) totaltheme_wc_get_page_id( 'shop' ) : 0;
		}

		// Used for mobile select
		$select_ops = [];

		// Loop through menu items
		foreach ( $menu_items as $menu_item ) :
			$menu_item_type   = $menu_item->type ?? false;
			$menu_item_obj    = $menu_item->object ?? null;
			$menu_item_id     = $menu_item->ID ?? 0;
			$menu_item_obj_id = isset( $menu_item->object_id ) ? (int) $menu_item->object_id : null;
			$link_text        = ! empty( $menu_item->title ) ? do_shortcode( wp_kses_post( $menu_item->title ) ) : '';

			// Reset these vars on each iteration
			$is_active              = false;
			$is_all_link            = false;
			$disable_pointer_events = false;
			$menu_item_filter_slug  = '';

			// Add to counter
			$counter++;

			// Link Classes.
			$link_class = $all_link_class; // !! must reset link_class on each iteration !!

			if ( $menu_item_obj_id ) {
				array_splice( $link_class, 1, 0, "vcex-navbar-link--{$menu_item_obj_id}" );
			}

			if ( ! empty( $menu_item->menu_item_parent ) ) {
				array_splice( $link_class, 1, 0, 'vcex-navbar-link--child' );
			}

			if ( ! empty( $menu_item->classes ) && is_array( $menu_item->classes ) ) {
				$link_class = array_merge( $link_class, $menu_item->classes );
			}

			// Add active class based on the current page
			if ( $is_singular && 'taxonomy' !== $menu_item_type ) {
				if ( $menu_item_obj_id === $post_id ) {
					$is_active = true;
				} elseif ( $post_parents && in_array( $menu_item_obj_id, $post_parents ) ) {
					$is_active = true;
				} elseif ( in_array( $post_type, [ 'portfolio', 'staff', 'testimonials', 'post' ], true ) ) {
					$type_page = ( 'post' === $post_type ) ? get_theme_mod( 'blog_page' ) : get_theme_mod( "{$post_type}_page" );
					if ( $menu_item_obj_id === (int) $type_page ) {
						$is_active = true;
					}
				}
			} elseif ( $is_shop ) {
				if ( $menu_item_obj_id === $shop_page_id ) {
					$is_active = true;
				}
			} elseif ( 'taxonomy' === $menu_item_type && $is_tax_archive && $menu_item_obj_id === $queried_object_id ) {
				$is_active = true;
			} elseif ( $is_home ) {
				if ( $page_for_posts === $menu_item_obj_id ) {
					$is_home_link = true;
					$is_active = true;
				}
			}

			// Add special classes for filtering by terms
			$data_filter = ''; // reset filter
			if ( $filter_menu ) {
				switch ( $filter_mode ) :
					case 'ajax':
						if ( ! empty( $atts['filter_active_item'] ) ) {
							if ( $menu_item_obj_id === (int) $atts['filter_active_item'] ) {
								$is_active = true;
								$disable_pointer_events = true;
							}
						} elseif ( '#' === $menu_item->url || '#all' === $menu_item->url ) {
							if ( ! $url_sort
								|| ! totalthemecore_call_static( 'Vcex\Url_Sort_Query', 'has_query' )
							) {
								$is_active = true;
								$disable_pointer_events = true;
							}
						} elseif ( $url_sort
							&& totalthemecore_call_static(
								'Vcex\Url_Sort_Query',
								'is_trigger_active',
								[
									'type'  => $menu_item_obj,
									'value' => $menu_item_obj_id
								]
							)
						) {
							$is_active = true;
							if ( ! $filter_relation ) {
								$disable_pointer_events = true;
							}
						}
						if ( isset( $disable_pointer_events ) && true === $disable_pointer_events ) {
							$link_class[] = 'wpex-pointer-events-none';
						}
						break;
					default:
						// Active tax link
						if ( ! empty( $atts['filter_active_item'] ) ) {
							if ( $menu_item_obj_id === (int) $atts['filter_active_item'] ) {
								$is_active = true;
							}
						} elseif ( 1 === $counter && '#' === $menu_item->url ) {
							$data_filter = '*';
							$is_all_link = true;
							if ( ! $active_filter_item ) {
								$is_active = true;
							}
						}
						// Taxonomy links
						if ( 'taxonomy' === $menu_item_type ) {
							if ( $menu_item_obj ) {
								$prefix = $menu_item_obj;
								if ( 'category' === $menu_item_obj ) {
									$prefix = 'cat';
								} else {
									foreach ( vcex_theme_post_types() as $type ) {
										if ( str_contains( $prefix, $type ) ) {
											$search  = [ "{$type}_category", "{$type}_tag" ];
											$replace = [ 'cat', 'tag' ];
											$prefix  = str_replace( $search, $replace, $prefix );
										}
									}
								}
								$data_filter = ".{$prefix}-{$menu_item_obj_id}";
								$menu_item_term = get_term_by( 'id', $menu_item_obj_id, $menu_item_obj );
								if ( $menu_item_term ) {
									$menu_item_filter_slug = $menu_item_term->slug ?? '';
									// Check for hexidecimal text as it could be Hebrew or another language.
									if ( $menu_item_filter_slug && str_contains( $menu_item_filter_slug, '%d' ) ) {
										$menu_item_filter_slug = str_replace( '.', '', $data_filter );
									}
								}
							}
						}

						if ( ! $data_filter && str_starts_with( $menu_item->url, '#' ) ) {
							$menu_item_filter_slug = str_replace( '#', '', $menu_item->url );
							$data_filter = '.' . $menu_item_filter_slug;
						}

						// Add active filter class
						if ( ! $is_active && ( $data_filter === ".{$active_filter_item}" || $menu_item_filter_slug === $active_filter_item ) ) {
							$is_active = true;
						}

						break;

				endswitch;

			}

			// Add active styles and class
			if ( $is_active ) {
				$link_class[] = 'active';
			}

			// Define href
			if ( $filter_menu && $data_filter ) {
				if ( ! empty( $menu_item_filter_slug ) ) {
					$current_url = $current_url ?? wpex_get_current_url();
					$filter_url_param = $filter_url_param ?? vcex_grid_filter_url_param();
					$href = "{$current_url}?{$filter_url_param}={$menu_item_filter_slug}";
				} else {
					$href = '#';
				}
			} else {
				$href = $menu_item->url;
			}

			// Sanitize link classes
			$link_class = array_filter( array_unique( $link_class ) );
			$link_class = array_filter( $link_class, 'trim' );
			$link_class = array_filter( $link_class, 'esc_attr' );

			// Link attributes
			$link_attrs = [
				'href'   => esc_url( $href ),
				'class'  => $link_class,
				'title'  => isset( $menu_item->attr_title ) ? esc_attr( $menu_item->attr_title ) : '',
				'target' => isset( $menu_item->target ) ? $menu_item->target : '',
			];

			// Add data filter for masonry/simple filter
			if ( $data_filter ) {
				$link_attrs['data-filter'] = $data_filter;
			}

			// Add ajax data attributes
			if ( $filter_menu && 'ajax' === $filter_mode ) {
				if ( 'custom' === $menu_item_type
					|| ( in_array( $menu_id, [ 'post_terms', 'dynamic_terms', 'current_tax_terms' ], true ) && false === $menu_item_type )
				) {
					if ( '#' === $menu_item->url ) {
						$is_all_link = true;
						$link_attrs['data-vcex-type'] = 'all';
					}
				} else {
					if ( $menu_item_obj ) {
						$link_attrs['data-vcex-type'] = esc_attr( $menu_item_obj );
					}
					$link_attrs['data-vcex-value'] = esc_attr( $menu_item_obj_id );
				//	$link_attrs['data-vcex-operator'] = 'IN';
				}
				if ( $is_active ) {
					$link_attrs['data-vcex-selected'] = '1';
				}
				if ( ! $is_all_link ) {
					$link_attrs['data-vcex-name'] = wp_strip_all_tags( strtolower( trim( $link_text ) ) );
				}
			}

			// Open list item div
			if ( 'list' === $button_layout ) {
				$list_item_class = 'vcex-navbar-list-item wpex-list-item';
				if ( ! empty( $menu_item->menu_item_parent ) ) {
					$list_item_class .= ' wpex-list-item--child';
				}
				$list_item_class .= ' wpex-clear';
				$output .= '<div class="' . esc_attr( $list_item_class ) . '">';
			}

				// Filter link item attributes
				$link_attrs = (array) apply_filters( 'vcex_navbar_link_attributes', $link_attrs, $menu_item );

				// Link item output
				$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) .'>';

					// Display meta defined icon
					$icon = ( $menu_item_id && $icon = \get_post_meta( $menu_item_id, '_menu_item_totaltheme_icon', true ) ) ? \sanitize_text_field( $icon ) : '';
					if ( $icon ) {
						$link_text = \vcex_get_theme_icon_html( $icon, 'vcex-navbar-link-icon wpex-mr-5 wpex-icon--w' ) . $link_text;
					}
					// Add margins to icons inside links
					else {
						$link_text = str_replace( 'class="ticon', 'class="vcex-navbar-link-icon wpex-mr-5 ticon', $link_text );
						$link_text = str_replace( 'class="wpex-icon', 'class="vcex-navbar-link-icon wpex-icon wpex-mr-5', $link_text );
					}

					$output .= '<span class="vcex-navbar-link-text">' . $link_text . '</span>';

					if ( $filter_menu
						&& ( 'taxonomy' === $menu_item_type || $is_all_link )
						&& 'ajax' === $filter_mode
						&& $show_term_count
					) {
						$count_text = (string) apply_filters( 'vcex_ajax_filter_count_default', '(-)', $menu_item_obj, $menu_item_obj_id );
						$output .= ' <span class="vcex-navbar-link-count vcex-ajax-filter-count">' . $count_text . '</span>';
					}

				$output .= '</a>';

			// Close list item div.
			if ( 'list' === $button_layout ) {
				$output .= '</div>';
			}

			// Save links into select options array.
			$select_args = [
				'href'         => $href,
				'text_escaped' => $link_text,
			];

			if ( $is_active ) {
				$select_args['selected'] = true;
			}

			$select_ops[] = $select_args;

		endforeach; // End menu item loop.

	$output .= '</div>';

	if ( $has_mobile_select && ! empty( $select_ops ) ) {

		/*
		// @todo display an html dropdown instead to allow multi select?
		$is_multiple_select = false;
		if ( ! empty( $filter_relation ) && in_array( $filter_relation, [ 'AND', 'OR' ] ) ) {
			$is_multiple_select = true;
		}
		*/

		$output .= '<div class="vcex-navbar-mobile-select hidden-desktop wpex-select-wrap">';

			$output .= '<select autocomplete="off">';

				if ( $atts['mobile_select_browse_txt'] ) {
					$output .= '<option value="">' . do_shortcode( esc_html( $atts['mobile_select_browse_txt'] ) ) . '</option>';
				}

				$has_selected = false;
				foreach ( $select_ops as $option ) {
					$selected = '';
					if ( ! $has_selected && isset( $option['selected'] ) && true === $option['selected'] ) {
						$has_selected = true;
						$selected = ' selected';
					}
					$output .= '<option value="' . esc_attr( $option['href'] ) . '"' . $selected . '>' . wp_strip_all_tags( $option['text_escaped'] ) . '</option>';
				}

			$output .= '</select>';

		if ( is_callable( [ 'TotalTheme\Forms\Select_Wrap', 'arrow' ] ) ) {
			ob_start();
				TotalTheme\Forms\Select_Wrap::arrow();
			$output .= ob_get_clean();
		}

		$output .= '</div>';

	}

$output .= '</nav>';

echo $output; // @codingStandardsIgnoreLine
