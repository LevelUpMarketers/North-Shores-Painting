<?php

/**
 * vcex_custom_field shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $atts['key'] )
	|| empty( $atts['template'] )
	|| 'publish' !== get_post_status( (int) $atts['template'] )
	|| ! function_exists( 'get_field' )
	|| ! function_exists( 'have_rows' )
	|| ! function_exists( 'the_row' )
	|| ! function_exists( 'get_sub_field' )
	|| ! function_exists( 'totaltheme_get_post_builder_type' )
) {
	return;
}

$display_type = ! empty( $atts['display_type'] ) ? sanitize_text_field( $atts['display_type'] ) : 'list';

$wrap_class = [
	'vcex-acf-repeater',
];

if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'] );
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_acf_repeater' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

// Turn classes into string, sanitize and apply filters.
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_acf_repeater', $atts );

$output = '<div class="' . \esc_attr( $wrap_class ) . '">';
$template_content = get_post_field( 'post_content', $atts['template'] );

if ( ! $template_content ) {
	return;
}

$template_type = totaltheme_get_post_builder_type( $atts['template'] );

if ( 'wpbakery' === $template_type && is_callable( 'TotalTheme\Integration\WPBakery\Shortcode_Inline_Style::instance' ) ) {
	$wpb_style = TotalTheme\Integration\WPBakery\Shortcode_Inline_Style::instance();
	$output .= $wpb_style->get_style( $atts['template'], true );
}

$heading = ! empty( $atts['heading'] ) ? \vcex_parse_text_safe( $atts['heading'] ) : '';
$heading_html = '';

if ( $heading && function_exists( 'wpex_get_heading' ) ) {
	$heading_html = wpex_get_heading( [
		'tag'     => ! empty( $atts['heading_tag'] ) ? sanitize_text_field( $atts['heading_tag'] ) : 'h2',
		'style'   => ! empty( $atts['heading_style'] ) ? sanitize_text_field( $atts['heading_style'] ) : '',
		'align'   => ! empty( $atts['heading_align'] ) ? sanitize_text_field( $atts['heading_align'] ) : '',
		'classes' => [ 'vcex-acf-repeater__heading' ],
		'content' => $heading,
	] );
}

$template_edit_mode = vcex_get_template_edit_mode();
$post_id = ( $template_edit_mode && ! empty( $atts['preview_id'] ) ) ? $atts['preview_id'] : false;
$field_val = get_field( $atts['key'], $post_id );
$total_rows = is_array( $field_val ) ? count( $field_val ) : 1;

if ( have_rows( $atts['key'], $post_id ) ) :

	$output .= $heading_html;

	$list_tag_safe = $item_tag_safe = 'div';

	$list_class = [
		'vcex-acf-repeater__list',
	];

	switch ( $display_type ) {
		case 'ul_list':
			$list_class[] = 'wpex-flex wpex-flex-col wpex-m-0 wpex-p-0 wpex-list-none';
			$list_tag_safe = 'ul';
			$item_tag_safe = 'li';
			break;
		case 'ol_list':
			$list_class[] = 'wpex-flex wpex-flex-col wpex-m-0 wpex-p-0 wpex-list-none';
			$list_tag_safe = 'ol';
			$item_tag_safe = 'li';
			break;
		case 'grid':
			$list_class[] = 'wpex-grid';
			$grid_columns = ! empty( $atts['grid_columns'] ) ? absint( $atts['grid_columns'] ) : 3;
			$grid_is_responsive = vcex_validate_att_boolean( 'grid_columns_responsive', $atts, true );
			if ( $grid_is_responsive && ! empty( $atts['grid_columns_responsive_settings'] ) ) {
				$r_grid_columns = vcex_parse_multi_attribute( $atts['grid_columns_responsive_settings'] );
				if ( $r_grid_columns && is_array( $r_grid_columns ) ) {
					$r_grid_columns['d'] = $grid_columns;
					$grid_columns = $r_grid_columns;
				}
			}
			if ( $grid_is_responsive && function_exists( 'wpex_grid_columns_class' ) ) {
				$list_class[] = wpex_grid_columns_class( $grid_columns );
			} else {
				$list_class[] = "wpex-grid-cols-{$grid_columns}";
			}
			break;
		case 'carousel':
			\vcex_enqueue_carousel_scripts();

			// All carousels need a unique classname.
			if ( empty( $atts['vcex_class'] ) ) {
				$atts['vcex_class'] = \vcex_element_unique_classname();
			}

			// Get carousel settings.
			$carousel_settings = \vcex_get_carousel_settings( $atts, 'vcex_acf_repeater', false );
			$carousel_css = \vcex_get_carousel_inline_css( $atts['vcex_class'] . ' .vcex-acf-repeater__list', $carousel_settings );

			$list_class[] = 'wpex-carousel';

			if ( ! empty( $atts['carousel_bleed'] ) && \in_array( $atts['carousel_bleed'], [ 'end', 'start-end' ], true ) ) {
				$list_class[] = "wpex-carousel--bleed-{$atts['carousel_bleed']}";
			}

			if ( isset( $atts['items'] ) && 1 === (int) $atts['items'] ) {
				$list_class[] = 'wpex-carousel--single';
			}

			if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
				$list_class[] = 'owl-carousel';
			}

			if ( $carousel_css ) {
				$output .= $carousel_css;
				$list_class[] = 'wpex-carousel--render-onload';
			}

			// Flex carousel.
			if ( empty( $atts['auto_height'] ) || 'false' === $atts['auto_height'] ) {
				$list_class[] = 'wpex-carousel--flex';
			}

			// No margins.
			if ( isset( $atts['items_margin'] )
				&& '' !== $atts['items_margin']
				&& 0 === \absint( $atts['items_margin'] )
			) {
				$list_class[] = 'wpex-carousel--no-margins';
			} elseif ( ! vcex_validate_att_boolean( 'center', $atts, false ) ) {
				$list_class[] = 'wpex-carousel--offset-fix';
			}

			// Arrow style.
			$arrows_style = ! empty( $atts['arrows_style'] ) ? \sanitize_text_field( $atts['arrows_style'] ) : 'default';
			$list_class[] = "arrwstyle-{$arrows_style}";

			// Arrow position.
			$arrow_position = ! empty( $atts['arrows_position'] ) ? \sanitize_text_field( $atts['arrows_position'] ) : 'default';
			$list_class[] = "arrwpos-{$arrow_position}";
			break;
		case 'flex_wrap':
			$list_class[] = 'wpex-flex wpex-flex-wrap';
			if ( ! empty( $atts['flex_justify'] ) ) {
				$list_class[] = \vcex_parse_justify_content_class( $atts['flex_justify'] );
			}
			break;
		case 'flex':
			$list_class[] = 'wpex-flex';
			$flex_bk = ! empty( $atts['flex_breakpoint'] ) ? sanitize_text_field( $atts['flex_breakpoint'] ) : '';
			if ( $flex_bk && 'false' !== $flex_bk ) {
				$list_class[] = 'wpex-flex-col';
				$list_class[] = "wpex-{$flex_bk}-flex-row";
			}
			if ( ! empty( $atts['flex_justify'] ) ) {
				$list_class[] = \vcex_parse_justify_content_class( $atts['flex_justify'], $flex_bk );
				if ( $flex_bk && 'false' !== $flex_bk ) {
					$list_class[] = \vcex_parse_align_items_class( $atts['flex_justify'] );
					$list_class[] = \vcex_parse_align_items_class( 'stretch', $flex_bk );
				}
			}
			$list_class[] = 'wpex-overflow-x-auto';
			if ( \vcex_validate_att_boolean( 'hide_scrollbar', $atts ) ) {
				$list_class[] = 'wpex-hide-scrollbar';
			}
			$snap_type = ! empty( $atts['flex_scroll_snap_type'] ) ? sanitize_text_field( $atts['flex_scroll_snap_type'] ) : 'proximity';
			if ( 'proximity' === $snap_type || 'mandatory' === $snap_type ) {
				$has_scroll_snap = true;
				$list_class[] = 'wpex-snap-x';
				$list_class[] = "wpex-snap-{$snap_type}";
			}
			break;
		case 'list':
		default:
			$list_class[] = 'wpex-flex wpex-flex-col';
			break;
	}

	if ( empty( $atts['gap'] ) && ! in_array( $display_type, [ 'carousel', 'ul_list', 'ol_list' ] ) ) {
		$list_class[] = 'wpex-gap-25';
	}

	$output .= '<' . $list_tag_safe . ' class="' . esc_attr( implode( ' ', $list_class ) ) . '"';
		if ( ! empty( $carousel_settings ) ) {
			$output .= ' data-wpex-carousel="' . esc_attr( \vcex_carousel_settings_to_json( $carousel_settings ) ) . '"';
		}
	$output .= '>';

		$item_class = [
			'vcex-acf-repeater__item',
		];

		switch ( $display_type ) {
			case 'ol_list':
				$item_class[] = 'wpex-flex wpex-gap-5';
				break;
			case 'carousel':
				$item_class[] = 'wpex-carousel-slide';
				break;
			case 'flex':
				$item_class[] = 'wpex-flex';
				$item_class[] = 'wpex-flex-col';
				$item_class[] = 'wpex-max-w-100';
				if ( ! empty( $atts['flex_basis'] )
					|| ! vcex_validate_att_boolean( 'flex_shrink', $atts, true )
				) {
					$item_class[] = 'wpex-flex-shrink-0';
				} else {
					$item_class[] = 'wpex-flex-grow';
				}
				if ( isset( $has_scroll_snap ) && true === $has_scroll_snap ) {
					$item_class[] = 'wpex-snap-start';
				}
				break;
			case 'flex-wrap':
				$item_class[] = 'wpex-flex';
				$item_class[] = 'wpex-flex-col';
				break;
		}

		$divider_html = '';

		if ( in_array( $display_type, [ 'list', 'ul_list', 'ol_list' ], true ) && vcex_validate_att_boolean( 'list_divider', $atts ) ) {
			$divider_class = [
				'vcex-acf-repeater__divider',
				'wpex-divider',
				'wpex-divider-' . \sanitize_html_class( $atts['list_divider'] ),
				'wpex-my-0',
			];
			if ( ! empty( $atts['list_divider_size'] ) ) {
				$divider_size = \absint( $atts['list_divider_size'] );
				if ( 1 === $divider_size ) {
					$divider_class[] = 'wpex-border-b';
				} else {
					$divider_class[] = "wpex-border-b-{$divider_size}";
				}
			}
			$divider_html = '<div class="' . esc_attr( implode( ' ', $divider_class ) ) . '"></div>';
		}

		$count=0;
		while ( have_rows( $atts['key'], $post_id ) ) : the_row();
		$count++;
			if ( $divider_html && 1 === $count && vcex_validate_att_boolean( 'list_divider_before', $atts, true ) ) {
				$output .= $divider_html;
			}
			switch ( $template_type ) {
				case 'wpbakery':
					$field_content = do_shortcode( totaltheme_shortcode_unautop( $template_content ) ); // removes <p> tags around shortcodes.
					break;
				case 'elementor':
					if ( function_exists( 'wpex_get_elementor_content_for_display' ) ) {
						$field_content = wpex_get_elementor_content_for_display( $atts['template'] );
					}
					break;
				case 'gutenberg':
					if ( function_exists( 'totaltheme_render_content' ) ) {
						$field_content = totaltheme_render_content( $template_content );
					}
					break;
			}
			if ( ! empty( $field_content ) ) {
				$output .= '<' . $item_tag_safe . ' class="' . esc_attr( implode( ' ', $item_class ) ) . '">';
					if ( 'ol_list' === $display_type ) {
						$output .= '<div>' . esc_html( $count ) . '.</div>';
					}
					$output .= $field_content;
				$output .= '</' . $item_tag_safe . '>';
				if ( $divider_html && $count !== $total_rows ) {
					$output .= $divider_html;
				}
			}
		endwhile;
		if ( $divider_html && $count === $total_rows && vcex_validate_att_boolean( 'list_divider_after', $atts, true ) ) {
			$output .= $divider_html;
		}
	$output .= "</{$list_tag_safe}>";

else :
	if ( ! empty( $atts['fallback'] ) ) {
		$output .= $heading_html . '<p>' . vcex_parse_text_safe( $atts['fallback'] ) . '</p>';
	}
endif;

$output .= '</div>';

echo $output; // @codingStandardsIgnoreLine
