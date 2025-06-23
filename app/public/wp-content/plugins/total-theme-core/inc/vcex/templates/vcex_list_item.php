<?php

/**
 * vcex_list_item shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$text_source = ! empty( $atts['text_source'] ) ? $atts['text_source'] : 'custom_text';

// Sanitize content/text.
switch ( $text_source ) {
	case 'custom_field':
		if ( ! empty( $atts['text_custom_field'] ) ) {
			$content = vcex_get_meta_value( $atts['text_custom_field'] );
		}
		break;
	case 'callback_function':
		if ( ! empty( $atts['text_callback_function'] )
			&& function_exists( $atts['text_callback_function'] )
			&& vcex_validate_user_func( $atts['text_callback_function'] )
		) {
			$content = call_user_func( $atts['text_callback_function'] );
		}
		break;
}

// Content is required.
if ( empty( $content ) || ! is_string( $content ) ) {
	return;
}

// Define main vars.
$output        = '';
$html_tag_safe = ! empty( $atts['tag'] ) ? tag_escape( $atts['tag'] ) : 'div';
$onclick_attrs = vcex_get_shortcode_onclick_attributes( $atts, 'vcex_list_item' );

// Wrap classes.
$wrap_class = [
	'vcex-list_item',
	'vcex-module',
	'wpex-m-0', // removes default heading margin.
	'wpex-max-w-100',
];

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
} else {
	$wrap_class[] = 'wpex-mb-5';
}

if ( ! empty( $atts['css_animation_class'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation_class'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['css'] ) ) {
	$wrap_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
}

if ( ! empty( $atts['text_align'] ) ) {
	$wrap_class[] = vcex_parse_text_align_class( $atts['text_align'] );
}

if ( ! empty( $atts['responsive_text_min_max'] ) || vcex_validate_att_boolean( 'responsive_font_size', $atts ) ) {
	$min_max = $atts['responsive_text_min_max'];
	if ( $min_max && is_string( $min_max ) ) {
		$min_max = explode( '|', $min_max );
	}
	$max_font_size = $min_max[1] ?? $font_size ?? '';
	$min_font_size = $min_max[0] ?? $min_font_size ?? '';

	if ( $max_font_size && $min_font_size ) {

		// Sanitize sizes.
		$max_font_size = vcex_parse_min_max_text_font_size( $max_font_size );
		$min_font_size = vcex_parse_min_max_text_font_size( $min_font_size );

		// Add wrap classes and data.
		if ( $max_font_size && $min_font_size ) {
			wp_enqueue_script( 'vcex-responsive-text' );
			if ( empty( $atts['font_size'] ) ) {
				$atts['font_size'] = $max_font_size;
			}
			$minmax_wrapper = '<div class="wpex-responsive-txt" data-max-font-size="' . absint( $max_font_size ) . '" data-data-min-font-size="' . absint( $min_font_size ) .'">';
		}
	}

}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_list_item' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

// Turn classes into string, sanitize and apply filters.
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_list_item', $atts );

// Begin output.
$output .= '<' . $html_tag_safe . ' class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $atts ) . '>';

	if ( ! empty( $onclick_attrs['href'] ) ) {
		$link_class = [
			'vcex-list-item-link',
			'wpex-no-underline',
		];

		if ( ! empty( $atts['font_color'] ) ) {
			$link_class[] = 'wpex-inherit-color';
		}

		if ( isset( $minmax_wrapper ) ) {
			$link_class[] = 'wpex-block';
		}

		$onclick_attrs['class'] = array_merge( $link_class, $onclick_attrs['class'] );

		$output .= '<a'. vcex_parse_html_attributes( $onclick_attrs ) . '>';
	}

	if ( isset( $minmax_wrapper ) ) {
		$output .= $minmax_wrapper;
	}

	$inner_classes = apply_filters( 'vcex_list_item_inner_class', [
		'vcex-list-item-inner',
		'wpex-inline-flex',
		'wpex-flex-no-wrap',
	] );

	if ( ! empty( $atts['flex_align'] ) && 'start' !== $atts['flex_align'] ) {
		$inner_classes[] = 'wpex-items-' . sanitize_html_class( $atts['flex_align'] );
	}

	$output .= '<div class="' . esc_attr( implode( ' ', $inner_classes ) ) . '">';

		// Display icon.
		if ( ! empty( $atts['icon'] )
			|| ! empty( $atts['icon_alternative_classes'] )
			|| ! empty( $atts['icon_alternative_character'] )
		) {

			$icon_classes = [
				'vcex-list-item-icon',
			];

			if ( ! empty( $atts['icon_spacing'] ) ) {
				$icon_classes[] = 'wpex-mr-' . sanitize_html_class( absint( $atts['icon_spacing'] ) );
			} else {
				$icon_classes[] = 'wpex-mr-10';
			}

			// List item icon.
			$output .= '<div class="' . esc_attr( implode( ' ', $icon_classes ) ) . '">';

				$output .= '<div class="vcex-list-item-icon-inner vcex-icon-wrap wpex-inline-flex wpex-justify-center wpex-items-center wpex-leading-none wpex-child-inherit-color">';

					if ( ! empty( $atts['icon_alternative_classes'] ) ) {
						$output .= '<span class="' . esc_attr( do_shortcode( $atts['icon_alternative_classes'] ) ) . '"></span>';
					} elseif ( ! empty( $atts['icon_alternative_character'] ) ) {
						$output .= vcex_parse_text_safe( $atts['icon_alternative_character'] );
					} else {
						$icon_class = ( empty( $atts['icon_width'] ) && empty( $atts['icon_background'] ) ) ? 'wpex-icon--w' : '';
						$output .= (string) vcex_get_icon_html( $atts, 'icon', $icon_class );
					}

				$output .= '</div>';

			$output .= '</div>';

		}

		$output .= '<div class="vcex-list-item-text vcex-content wpex-flex-grow">';

			$output .= vcex_parse_text_safe( $content );

		$output .= '</div>';

	// Close inner.
	$output .= '</div>';

	if ( isset( $minmax_wrapper ) ) {
		$output .= '</div>';
	}

	if ( ! empty( $onclick_attrs['href'] ) ) {
		$output .= '</a>';
	}

$output .= '</' . $html_tag_safe . '>';

// @codingStandardsIgnoreLine
echo $output;
