<?php

/**
 * vcex_image_swap shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1.1
 */

defined( 'ABSPATH' ) || exit;

$source = $atts['source'] ?? '';

// Get images based on source.
switch ( $source ) {
	case 'featured':
		$post_id       = vcex_get_the_ID();
		$primary_image = get_post_thumbnail_id( $post_id );
		if ( function_exists( 'totaltheme_get_post_secondary_thumbnail_id' ) ) {
			$secondary_image = totaltheme_get_post_secondary_thumbnail_id( $post_id );
		}
		break;
	case 'custom_field':
		if ( ! empty( $atts['primary_image_custom_field'] ) ) {
			$primary_image = vcex_get_meta_value_attachment_id( $atts['primary_image_custom_field'] );
		}
		if ( ! empty( $atts['secondary_image_custom_field'] ) ) {
			$secondary_image = vcex_get_meta_value_attachment_id( $atts['secondary_image_custom_field'] );
		}
		break;
	case 'media_library':
	default:
		$primary_image   = $atts['primary_image']['id'] ?? $atts['primary_image'] ?? 0;
		$secondary_image = $atts['secondary_image']['id'] ?? $atts['secondary_image'] ?? $atts['primary_image'] ?? 0;
		break;
}

$primary_image   = (int) apply_filters( 'vcex_image_swap_primary_image', absint( $primary_image ), $atts );
$secondary_image = (int) apply_filters( 'vcex_image_swap_secondary_image', absint( $secondary_image ), $atts );

// Primary and secondary images required
if ( ( empty( $primary_image ) || empty( $secondary_image ) ) ) {
	if ( function_exists( 'wpex_get_placeholder_image' )
		&& ! vcex_is_card()
		&& ( vcex_vc_is_inline() || vcex_is_template_edit_mode() )
	) {
		$placeholder_image = true;
	} elseif ( 'featured' !== $source ) {
		return;
	}
}

// Declare emain vars
$onclick_attrs = vcex_get_shortcode_onclick_attributes( $atts, 'vcex_image_swap' );
$has_overlay = ( ! empty( $atts['overlay_style'] ) && 'none' !== $atts['overlay_style'] );

$wrap_attributes = [
	'id'    => ! empty( $atts['unique_id'] ) ? $atts['unique_id'] : null,
	'class' => '',
];

// Add classes
$wrap_class = [
	'vcex-module',
	'vcex-image-swap',
	'wpex-block',
];

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['container_width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$align = ! empty( $atts['align'] ) ? $atts['align'] : 'center';
	$wrap_class[] = vcex_parse_align_class( $atts['align'] );
}

if ( ! empty( $atts['classes'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['classes']  );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['css'] ) ) {
	$wrap_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_image_swap', $atts );

$output = '<div  class="' . esc_attr( trim( $wrap_class ) ) . '"' . vcex_get_unique_id( $atts ) . '>';

	$inner_class = 'vcex-image-swap-inner wpex-block wpex-relative wpex-overflow-hidden';

	if ( $has_overlay ) {
		$inner_class .= ' ' . vcex_image_overlay_classes( $atts['overlay_style'] );
	}

	$output .= '<figure class="' . esc_attr( $inner_class ) . '">';

		if ( ! empty( $onclick_attrs['href'] ) ) {
			$output .= '<a ' . vcex_parse_html_attributes( $onclick_attrs ) . '>';
		}

		// Display images
		if ( isset( $placeholder_image ) && true === $placeholder_image ) {
			$output .= wpex_get_placeholder_image();
		} else {

			$transition_duration = ! empty( $atts['hover_speed'] ) ? 'wpex-duration-' . absint( $atts['hover_speed'] ) : 'wpex-duration-500';

			$img_class = [
				'wpex-block',
				'wpex-w-100',
				'wpex-overflow-hidden',
				'wpex-transition-opacity',
				$transition_duration,
			];

			if ( ! empty( $atts['img_aspect_ratio'] ) ) {
				$img_class[] = vcex_parse_aspect_ratio_class( $atts['img_aspect_ratio'] );
				if ( ! empty( $atts['img_object_fit'] ) ) {
					$img_class[] = vcex_parse_object_fit_class( $atts['img_object_fit'] );
				}
			}

			$img_class = implode( ' ', array_unique( array_filter( $img_class ) ) );

			// Primary Image
			$output .= vcex_get_post_thumbnail( [
				'attachment' => $primary_image,
				'size'       => $atts['img_size'] ?? '',
				'crop'       => $atts['img_crop'] ?? '',
				'width'      => $atts['img_width'] ?? '',
				'height'     => $atts['img_height'] ?? '',
				'class'      => "vcex-image-swap-primary {$img_class}",
			] );

			// Secondary image
			$output .= vcex_get_post_thumbnail( [
				'attachment' => $secondary_image,
				'size'       => $atts['img_size'] ?? '',
				'crop'       => $atts['img_crop'] ?? '',
				'width'      => $atts['img_width'] ?? '',
				'height'     => $atts['img_height'] ?? '',
				'class'      => "vcex-image-swap-secondary wpex-opacity-0 wpex-absolute wpex-inset-0 {$img_class}",
			] );
		}

		if ( $has_overlay ) {
			$output .= vcex_get_image_overlay( 'inside_link', $atts['overlay_style'], $atts );
		}
	
		if ( ! empty( $onclick_attrs['href'] ) ) {
			$output .= '</a>';
		}
	
		if ( $has_overlay ) {
			$output .= vcex_get_image_overlay( 'outside_link', $atts['overlay_style'], $atts );
		}

	$output .= '</figure>';

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
