<?php

/**
 * vcex_post_next_prev shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

// Define vars
$prev = $next = $icon_left = $icon_right = '';
$expand = vcex_validate_att_boolean( 'expand', $atts );
$has_prev_link = vcex_validate_att_boolean( 'previous_link', $atts, true );
$has_next_link = vcex_validate_att_boolean( 'next_link', $atts, true );
$is_order_reverse = vcex_validate_att_boolean( 'reverse_order', $atts );
$has_loop = vcex_validate_att_boolean( 'loop', $atts );
$in_same_term = vcex_validate_att_boolean( 'in_same_term', $atts );
$icon_style = ( ! empty( $atts['icon_style'] ) && 'none' !== $atts['icon_style'] ) ? sanitize_text_field( $atts['icon_style'] ) : null;
$same_term_tax = ! empty( $atts['same_term_tax'] ) ? sanitize_text_field( $atts['same_term_tax'] ) : vcex_get_post_type_cat_tax();
$link_format = $atts['link_format'] ?? 'icon';
$is_edit_mode = vcex_is_template_edit_mode();

// Post Series check
if ( $in_same_term && taxonomy_exists( 'post_series' ) && get_the_terms( get_the_id(), 'post_series' ) ) {
	$same_term_tax = 'post_series';
	if ( ! $is_order_reverse && 'DESC' === (string) totalthemecore_call_static( 'Post_Series', 'get_query_order' ) ) {
		$is_order_reverse = true;
	}
}

// Define wrap class
$wrap_class = [
	'vcex-post-next-prev',
	'vcex-module',
];

if ( 'card' !== $link_format ) {
	$wrap_class[] = 'wpex-flex';
	$wrap_class[] = 'wpex-flex-wrap';
	$col_spacing = ! empty( $atts['spacing'] ) ? absint( $atts['spacing'] ) : 5;
	$col_spacing = $col_spacing * 2;
	$wrap_class[] = vcex_parse_gap_class( $col_spacing );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( $expand ) {
	$wrap_class[] = 'wpex-justify-between';
}

if ( ! empty( $atts['align'] ) && $align_safe = sanitize_html_class( $atts['align'] ) ) {
	$wrap_class[] = "text{$align_safe}";
	if ( 'card' !== $link_format && ! $expand ) {
		switch ( $align_safe ) {
			case 'left':
				$wrap_class[] = 'wpex-justify-start';
				break;
			case 'center':
				$wrap_class[] = 'wpex-justify-center';
				break;
			case 'right':
				$wrap_class[] = 'wpex-justify-end';
				break;
		}
	}
}

switch( $atts['link_format'] ) {
	case 'icon':
		$wrap_class[] = 'vcex-icon-only';
	break;
	case 'card':
		$wrap_class[] = 'wpex-grid';
		if ( $has_prev_link && $has_next_link ) {
			$grid_bk = ! empty( $atts['grid_bk'] ) ? $atts['grid_bk'] : 'sm';
			if ( 'none' === $grid_bk ) {
				$wrap_class[] = 'wpex-grid-cols-2';
			} else {
				$wrap_class[] = 'wpex-' . sanitize_html_class( $grid_bk ) . '-grid-cols-2';
			}
		}
		$grid_gap = ! empty( $atts['grid_gap'] ) ? $atts['grid_gap'] : '20';
		$wrap_class[] = 'wpex-gap-' . sanitize_html_class( $grid_gap );
	break;
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['max_width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$wrap_class[] = vcex_parse_align_class( ! empty( $atts['float'] ) ? $atts['float'] : 'center' );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'], true );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_post_next_prev', $atts );

// Begin output
$html = '<div class="' . esc_attr( $wrap_class ) . '">';

	// Set vars for non card style formats
	if ( 'card' !== $link_format ) {

		// Define icon HTML
		if ( $icon_style ) {

			// Sanitize icon spacing
			if ( 'icon' === $link_format ) {
				$icon_margin_right = $icon_margin_left = '';
			} else {
				$icon_margin = ! empty( $atts['icon_margin'] ) ? absint( $atts['icon_margin'] ) : 10;
				$icon_margin_right = "wpex-mr-{$icon_margin}";
				$icon_margin_left  = "wpex-ml-{$icon_margin}";
			}

			// Parse icon style
			if ( 'ios' === $icon_style ) {
				$icon_left = 'material-arrow-back-ios';
				$icon_right = 'material-arrow-forward-ios';
			} else {
				$icon_left = "{$icon_style}-left";
				$icon_right = "{$icon_style}-right";
			}

			// Left icon
			$left_icon_class = "vcex-post-next-prev__icon vcex-post-next-prev__icon--left {$icon_margin_right}";
			$icon_left = vcex_get_theme_icon_html( $icon_left, $left_icon_class, '', true );

			// Right icon
			$right_icon_class = "vcex-post-next-prev__icon vcex-post-next-prev__icon--right {$icon_margin_left}";
			$icon_right = vcex_get_theme_icon_html( $icon_right, $right_icon_class, '', true );

		}

		// Get button class
		$button_style = $atts['button_style'] ?? '';
		$button_color = $atts['button_color'] ?? null;
		$button_class = 'vcex-post-next-prev__link ' . vcex_get_button_classes( $button_style, $button_color );
		$button_class .= ' wpex-flex wpex-items-center';

		if ( ! empty( $atts['line_height'] ) ) {
			$button_class .= ' wpex-inherit-leading';
		}

		if ( 'icon' === $link_format
			|| ( 'plain-text' === $button_style && vcex_validate_att_boolean( 'button_no_underline', $atts ) )
		) {
			$button_class .= ' wpex-no-underline';
		}
	}

	// Display previous link
	if ( $has_prev_link ) {

		if ( $is_edit_mode ) {
			$get_prev = vcex_get_dummy_post();
		} else {
			$get_prev = get_previous_post( $in_same_term, '', $same_term_tax );
		}

		if ( $has_loop && ! $get_prev ) {
			$get_prev = VCEX_Post_Next_Prev_Shortcode::get_first_last_post( 'first', $in_same_term, $same_term_tax );
		}

		if ( $get_prev ) {

			if ( 'card' === $link_format ) {

				if ( function_exists( 'wpex_get_card' ) ) {
					$card_style = $atts['card_style'] ?? null;
					$is_card_template = str_starts_with( $card_style, 'template_' );
					if ( $is_card_template ) {
						global $post;
						$post = get_post( $get_prev->ID );
					}
					$prev = wpex_get_card( [
						'style'   => $card_style,
						'post_id' => $get_prev->ID,
					] );
					if ( $is_card_template ) {
						wp_reset_postdata();
					}
				}

			} else {

				switch ( $link_format ) {
					case 'icon':
						$prev_format_escaped = $is_order_reverse ? $icon_right : $icon_left;
						break;
					case 'title':
						$title = get_the_title( $get_prev->ID );
						$prev_format_escaped = $is_order_reverse ? $title . $icon_right : $icon_left . $title;
						break;
					case 'custom':

						$prev_text = esc_html( $atts['previous_link_custom_text'] );

						if ( ! $prev_text ) {
							$prev_text = esc_html__( 'Previous', 'total-theme-core' );
						}

						$prev_format_escaped = $is_order_reverse ? $prev_text . $icon_right : $icon_left . $prev_text;
						break;
					default :
						$prev_format_escaped = '';
						break;
				}

				if ( $prev_format_escaped ) {
					$prev = '<a href="' . esc_url( get_permalink( $get_prev->ID ) ) . '" class="' . esc_attr( $button_class ) . ' wpex-text-center wpex-max-w-100">' . $prev_format_escaped . '</a>';

					$prev = apply_filters( 'vcex_post_next_prev_link_next_html', $prev, $get_prev, $prev_format_escaped, $atts );
				}

			}

		}

	}

	if ( $has_next_link ) {

		if ( $is_edit_mode ) {
			$get_next = vcex_get_dummy_post();
		} else {
			$get_next = get_next_post( $in_same_term, '', $same_term_tax );
		}

		if ( $has_loop && ! $get_next ) {
			$get_next = VCEX_Post_Next_Prev_Shortcode::get_first_last_post( 'last', $in_same_term, $same_term_tax );
		}

		if ( $get_next ) {

			if ( 'card' === $link_format ) {
				if ( function_exists( 'wpex_get_card' ) ) {
					$card_style = $atts['card_style'] ?? null;
					$is_card_template = str_starts_with( $card_style, 'template_' );
					if ( $is_card_template ) {
						global $post;
						$post = get_post( $get_next->ID );
					}
					$next = wpex_get_card( [
						'style'   => $card_style,
						'post_id' => $get_next->ID,
					] );
					if ( $is_card_template ) {
						wp_reset_postdata();
					}
				}
			} else {

				switch ( $link_format ) {

					case 'icon':
						$next_format_escaped = $is_order_reverse ? $icon_left : $icon_right;
						break;
					case 'title':
						$title = get_the_title( $get_next->ID );
						$next_format_escaped = $is_order_reverse ? $icon_left . $title : $title . $icon_right;
						break;
					case 'custom':

						$next_text = esc_html( $atts['next_link_custom_text'] );

						if ( ! $next_text ) {
							$next_text = esc_html__( 'Next', 'total-theme-core' );
						}

						$next_format_escaped = $is_order_reverse ? $icon_left . $next_text : $next_text . $icon_right;
						break;
					default:
						$next_format_escaped = '';
						break;

				}

				if ( $next_format_escaped ) {
					$next = '<a href="' . esc_url( get_permalink( $get_next->ID ) ) . '" class="' . esc_attr( $button_class ) . ' wpex-text-center wpex-max-w-100">' . $next_format_escaped . '</a>';

					$next = apply_filters( 'vcex_post_next_prev_link_prev_html', $next, $get_next, $next_format_escaped, $atts );
				}

			}

		}

	}

	if ( 'card' === $link_format ) {

		if ( $is_order_reverse ) {
			if ( $has_next_link ) {
				$html .= '<div class="vcex-post-next-prev__item vcex-post-next-prev__next wpex-flex">' . $next .'</div>';
			}
			if ( $has_prev_link ) {
				$html .= '<div class="vcex-post-next-prev__item vcex-post-next-prev__prev wpex-flex">' . $prev .'</div>';
			}
		} else {
			if ( $has_prev_link ) {
				$html .= '<div class="vcex-post-next-prev__item vcex-post-next-prev__prev wpex-flex">' . $prev .'</div>';
			}
			if ( $has_next_link ) {
				$html .= '<div class="vcex-post-next-prev__item vcex-post-next-prev__next wpex-flex">' . $next .'</div>';
			}
		}

	} else {

		// Sanitize col spacing
		$col_spacing_safe = ! empty( $atts['spacing'] ) ? absint( $atts['spacing'] ) : 5;

		if ( $is_order_reverse ) {
			if ( $has_next_link && ( $next || $expand ) ) {
				$html .= '<div class="vcex-post-next-prev__item vcex-post-next-prev__next vcex-col wpex-inline-block">' . $next .'</div>';
			}
			if ( $has_prev_link && ( $prev || $expand ) ) {
				$html .= '<div class="vcex-post-next-prev__item vcex-post-next-prev__prev vcex-col wpex-inline-block">' . $prev .'</div>';
			}
		} else {
			if ( $has_prev_link && ( $prev || $expand ) ) {
				$html .= '<div class="vcex-post-next-prev__item vcex-post-next-prev__prev vcex-col wpex-inline-block">' . $prev .'</div>';
			}
			if ( $has_next_link && ( $next || $expand ) ) {
				$html .= '<div class="vcex-post-next-prev__item vcex-post-next-prev__next vcex-col wpex-inline-block">' . $next .'</div>';
			}
		}

	}

$html .= '</div>';

// @codingStandardsIgnoreLine
echo $html;
