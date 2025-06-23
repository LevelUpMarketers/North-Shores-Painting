<?php

/**
 * vcex_image_ba shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$handle_style = ! empty( $atts['handle_style'] ) ? sanitize_html_class( $atts['handle_style'] ) : 'outline';

// Get image based on source.
switch ( $atts['source'] ) {
	case 'featured':
		$before_img = get_post_thumbnail_id();
		if ( function_exists( 'totaltheme_get_post_secondary_thumbnail_id' ) ) {
			$after_img = totaltheme_get_post_secondary_thumbnail_id( vcex_get_the_ID() );
		}
		break;
	case 'custom_field':
		if ( ! empty( $atts['before_img_custom_field'] ) ) {
			$before_img = vcex_get_meta_value_attachment_id( $atts['before_img_custom_field'] );
		}
		if ( ! empty( $atts['after_img_custom_field'] ) ) {
			$after_img = vcex_get_meta_value_attachment_id( $atts['after_img_custom_field'] );
		}
		break;
	case 'media_library':
	default:
		$before_img = $atts['before_img']['id'] ?? $atts['before_img'] ?? null;
		$after_img  = $atts['after_img']['id'] ?? $atts['after_img'] ?? null;
		break;
}

// Primary and secondary images required.
if ( empty( $before_img ) || empty( $after_img ) ) {
	if ( function_exists( 'wpex_get_placeholder_image' )
		&& ! vcex_is_card()
		&& ( vcex_vc_is_inline() || vcex_is_template_edit_mode() )
	) {
		$placeholder_image = wpex_get_placeholder_image( [
			'class' => 'wpex-w-100',
		] );
	} else {
		return;
	}
}

// Sanitize offset.
if ( ! isset( $atts['default_offset_pct'] ) ) {
	$default_offset_pct = '0.5';
} else {

	$default_offset_pct = str_replace( '%', '', $atts['default_offset_pct'] );

	if ( ! is_numeric( $default_offset_pct ) ) {
		$default_offset_pct = '0.5';
	} else {
		if ( $default_offset_pct > 1 ) {
			$default_offset_pct = $default_offset_pct / 100;
		}
		if ( $default_offset_pct <= 1 ) {
			$default_offset_pct = floatval( $default_offset_pct );
		}
	}

}

// Define shortcode classes.
$wrap_class = [
	'vcex-image-ba-wrap',
];

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['css'] ) ) {
	$wrap_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
}

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	if ( ! empty( $atts['align'] ) ) {
		$wrap_class[] = vcex_parse_align_class( $atts['align'] );
	}
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_image_ba', $atts );

// Begin html output.
$output = '<div class="' . esc_attr( $wrap_class ) . '">';

	// Figure classes.
	$figure_classes = [
		'vcex-module',
		'vcex-image-ba',
		'vcex-image-ba-handle-' . $handle_style,
		'twentytwenty-container', // add before JS to prevent potential rendering issues.
	];

	if ( $atts['el_class'] ) {
		$figure_classes[] = vcex_get_extra_class( $atts['el_class'] );
	}

	$options = [
		'orientation' => ! empty( $atts['orientation'] ) ? esc_attr( $atts['orientation'] ) : 'horizontal',
		'default_offset_pct' => esc_attr( $default_offset_pct ),
		'no_overlay' => ! vcex_validate_att_boolean( 'overlay', $atts, true ),
		'before_label' => ! empty( $atts['before_label'] ) ? esc_attr( $atts['before_label'] ) : esc_attr__( 'Before', 'total-theme-core' ),
		'after_label' => ! empty( $atts['after_label'] ) ? esc_attr( $atts['after_label'] ) : esc_attr__( 'After', 'total-theme-core' ),
	];

	$figure_attrs = [
		'class'        => $figure_classes,
		'data-options' => htmlspecialchars( wp_json_encode( $options ) ),
	];

	$output .= '<figure' . vcex_parse_html_attributes( $figure_attrs ) . '>';

		if ( ! empty( $placeholder_image ) ) {
			$output .= $placeholder_image . $placeholder_image;
		} else {

			// Before Image.
			$output .= vcex_get_post_thumbnail( [
				'attachment' => $before_img,
				'size'       => $atts['img_size'] ?? null,
				'crop'       => $atts['img_crop'] ?? null,
				'width'      => $atts['img_width'] ?? null,
				'height'     => $atts['img_height'] ?? null,
				'class'      => 'vcex-before',
				'lazy'       => false,
			] );

			// After image
			$output .= vcex_get_post_thumbnail( [
				'attachment' => $after_img,
				'size'       => $atts['img_size'] ?? null,
				'crop'       => $atts['img_crop'] ?? null,
				'width'      => $atts['img_width'] ?? null,
				'height'     => $atts['img_height'] ?? null,
				'class'      => 'vcex-after',
				'lazy'       => false,
			] );
		}

	$output .= '</figure>';

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
