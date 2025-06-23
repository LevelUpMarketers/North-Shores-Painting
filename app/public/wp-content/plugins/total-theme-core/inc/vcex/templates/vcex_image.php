<?php

/**
 * vcex_image shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0.3
 */

defined( 'ABSPATH' ) || exit;

// Define vars
$output = '';
$has_overlay = ( ! empty( $atts['overlay_style'] ) && 'none' !== $atts['overlay_style'] );
$lazy_load = vcex_validate_att_boolean( 'lazy_load', $atts, true );
$source = $atts['source'] ?? 'media_library';
$onclick = $atts['onclick'] ?? '';
$dark_mode_check = vcex_validate_att_boolean( 'dark_mode_check', $atts, true )
	&& function_exists( 'totaltheme_call_static' )
	&& totaltheme_call_static( 'Dark_Mode', 'is_enabled' );

// Get link attributes (get early incase we need to modify them for this specific shortcode).
$onclick_attrs = vcex_get_shortcode_onclick_attributes( $atts, 'vcex_image' );

// Get image from source.
$get_image = vcex_get_image_from_source( $source, $atts, true );

// Check if the image is an attachment or a URL.
if ( is_numeric( $get_image ) ) {
	$attachment = $get_image;
} elseif ( is_string( $get_image ) ) {
	$image_url = $get_image;
}

// Define image classes.
$image_classes = [
	'vcex-image-img',
	'wpex-align-middle',
];

if ( ! empty( $atts['padding_all'] ) ) {
	$image_classes[] = vcex_parse_padding_class( $atts['padding_all'] );
}

if ( ! empty( $atts['border_width'] ) ) {
	$image_classes[] = vcex_parse_border_width_class( $atts['border_width'] );
	$image_classes[] = vcex_parse_border_style_class( ! empty( $atts['border_style'] ) ? sanitize_text_field( $atts['border_style'] ) : 'solid' );
}

if ( isset( $atts['width'] ) && '100%' === $atts['width'] ) {
	$image_classes[] = 'wpex-w-100';
}

if ( ! empty( $atts['aspect_ratio'] ) ) {
	$image_classes[] = vcex_parse_aspect_ratio_class( $atts['aspect_ratio'] );
}

if ( ! empty( $atts['height'] ) || ! empty( $atts['aspect_ratio'] ) ) {
	if ( ! empty( $atts['object_fit'] ) ) {
		$object_fit_safe = sanitize_html_class( $atts['object_fit'] );
		$image_classes[] = "wpex-object-{$object_fit_safe}";
	}
}

if ( ! empty( $atts['shadow'] ) && $shadow_class_safe = sanitize_html_class( $atts['shadow'] ) ) {
	$image_classes[] = "wpex-{$shadow_class_safe}";
}

if ( ! empty( $atts['mix_blend_mode'] ) ) {
	$image_classes[] = 'wpex-mix-blend-' . sanitize_html_class( $atts['mix_blend_mode'] );
}

// Set image title for overlays.
if ( ! empty( $atts['img_title'] ) ) {
	$atts['post_title'] = $atts['img_title'];
} elseif ( 'external' === $source && ! empty( $atts['alt_attr'] ) ) {
	$atts['post_title'] = $atts['alt_attr'];
}

// Set image excerpt for overlays.
if ( ! empty( $atts['img_caption'] ) ) {
	$atts['post_excerpt'] = $atts['img_caption'];
	$atts['overlay_excerpt'] = $atts['img_caption'];
}

// Generate image html.
if ( ! empty( $attachment ) ) {

	$translate_image = (bool) apply_filters( 'vcex_image_translate_attachment', true );

	if ( function_exists( 'wpex_parse_obj_id' ) && $translate_image ) {
		$attachment = wpex_parse_obj_id( $attachment, 'attachment' ); // WPML translation.
	}

	$img_args = [
		'attachment' => $attachment,
		'size'       => $atts['img_size'] ?? null,
		'crop'       => $atts['img_crop'] ?? null,
		'width'      => $atts['img_width'] ?? null,
		'height'     => $atts['img_height'] ?? null,
		'class'      => $image_classes,
		'attributes' => [],
	];

	if ( ! $lazy_load ) {
		$img_args['lazy'] = false;
		$img_args['decoding'] = false;
	}

	if ( ! empty( $atts['fetchpriority'] ) && 'auto' !== $atts['fetchpriority'] ) {
		$img_args['attributes']['fetchpriority'] = esc_attr( $atts['fetchpriority'] );
	}

	// Add width to SVG images to fix rendering issues.
	$attachment_mime_type = get_post_mime_type( $attachment );
	if ( 'image/svg+xml' === $attachment_mime_type ) {
		if ( empty( $atts['width'] ) ) {
			$img_args['attributes']['width'] = '9999';
		} else {
			$width_attribute = $atts['width'];
			$width_attribute = str_replace( 'px', '', $width_attribute );
			if ( is_numeric( $width_attribute ) ) {
				$img_args['attributes']['width'] = esc_attr( $width_attribute );
			} else {
				$img_args['attributes']['width'] = '9999';
			}
		}
	}

	if ( ! empty( $atts['alt_attr'] ) ) {
		$img_args['alt'] = esc_attr( $atts['alt_attr'] );
	}

	if ( $dark_mode_check ) {
		if ( $attachment_slug = get_post_field( 'post_name', $attachment ) ) {
			$dark_attachment = get_page_by_path( "{$attachment_slug}-dark", OBJECT, 'attachment' );
			if ( ! empty( $dark_attachment->ID ) ) {
				$dark_img_args = $img_args;
				$dark_img_args['attachment'] = $dark_attachment->ID;
				$dark_img_args['class']['visible-dark-mode'] = 'visible-dark-mode';
				$dark_image = vcex_get_post_thumbnail( $dark_img_args );
				unset( $dark_img_args );
				if ( $dark_image ) {
					$img_args['class'][] = 'hidden-dark-mode';
				}
			}
		}
	}

	$image = vcex_get_post_thumbnail( $img_args );

	// Lightbox image fallback.
	if ( empty( $onclick_attrs['href'] ) && $onclick === 'lightbox_image' ) {
		$onclick_attrs['href'] = vcex_get_lightbox_image( $attachment );
	}

}

// Display non-attachment image.
elseif ( ! empty( $image_url ) ) {
	$image_url_safe = set_url_scheme( esc_url( $image_url ) );

	if ( $dark_mode_check ) {
		$image_classes[] = 'hidden-dark-mode';
	}

	// Define image attributes.
	$image_attrs = [
		'src'   => $image_url_safe,
		'class' => $image_classes,
		'alt'   => ! empty( $atts['alt_attr'] ) ? esc_attr( $atts['alt_attr'] ) :  '',
	];

	if ( $lazy_load ) {
		$image_attrs['load'] = 'lazy';
	}
	if ( ! empty( $atts['fetchpriority'] ) && 'auto' !== $atts['fetchpriority'] ) {
		$image_attrs['fetchpriority'] = esc_attr( $atts['fetchpriority'] );
	}

	// Set dimensions for avatars.
	if ( in_array( $source, [ 'author_avatar', 'user_avatar' ] ) ) {
		$image_attrs['width']  = $atts['img_width'] ?? '';
		$image_attrs['height'] = $atts['img_width'] ?? '';
	}
	// Add width to SVG images to fix rendering issues.
	elseif ( str_contains( $image_url, '.svg' ) ) {
		if ( empty( $atts['width'] ) ) {
			$image_attrs['width'] = '99999';
		} else {
			$width_attribute = $atts['width'];
			$width_attribute = str_replace( 'px', '', $width_attribute );
			$image_attrs['width'] = esc_attr( $width_attribute );
		}
	}

	// Set image output.
	$image = '<img' . vcex_parse_html_attributes( $image_attrs ) . '>';

	if ( $dark_mode_check ) {
		foreach ( $image_attrs['class'] as $k => $class ) {
			if ( 'hidden-dark-mode' === $class ) {
				$image_attrs['class'][ $k ] = 'visible-dark-mode';
			}
		}
		$image_attrs['src'] = vcex_add_dark_suffix( $image_attrs['src'] );
		$dark_image = '<img' . vcex_parse_html_attributes( $image_attrs ) . '>';
	}
}

// Placeholder image for frontend editor.
if ( empty( $image ) && ( vcex_vc_is_inline() || vcex_is_template_edit_mode() ) ) {
	$ph_size = ! empty( $atts['img_size'] ) && empty( $atts['aspect_ratio'] ) ? $atts['img_size'] : '';
	$image = vcex_get_placeholder_image( $ph_size, [
		'class' => $image_classes,
	] );
}

// Return if no image has been defined.
if ( empty( $image ) ) {
	return;
}

// Define wrap classes.
$wrap_classes = [
	'vcex-image',
	'vcex-module',
];

if ( vcex_validate_att_boolean( 'fill_column', $atts ) ) {
	$wrap_classes[] = 'vcex-fill-column';
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_classes[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['align'] ) ) {
	$wrap_classes[] = vcex_parse_text_align_class( $atts['align'] );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_classes[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_classes[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['el_class'] ) ) {
	$wrap_classes[] = vcex_get_extra_class( $atts['el_class'] );
}

$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_image', $atts );

// Link to self if onclick is set to image.
if ( isset( $atts['onclick'] ) && 'image' === $atts['onclick'] && empty( $onclick_attrs['href'] ) ) {
	if ( ! isset( $image_url_safe ) ) {
		$image_url_safe = ! empty( $attachment ) ? wp_get_attachment_image_url( $attachment, 'full', false ) : '';
	}
	$onclick_attrs['href'] = $image_url_safe;
}

// Define custom $atts when links exist.
if ( ! empty( $onclick_attrs['href'] ) ) {

	// Define post_permalink for use with Image overlay styles.
	$atts['post_permalink'] = esc_url( (string) $onclick_attrs['href'] );

	// Define link target.
	if ( ! empty( $onclick_attrs['target'] ) ) {
		$atts['link_target'] = $onclick_attrs['target'];
	}

	// Define link rel.
	if ( ! empty( $onclick_attrs['rel'] ) ) {
		$atts['link_rel'] = $onclick_attrs['rel'];
	}

	// Define lightbox data for use with overlay styles.
	if ( $has_overlay
		&& ( 'popup' === $onclick || str_starts_with( $onclick, 'lightbox_' ) )
	) {
		$atts['lightbox_link'] = $onclick_attrs['href'];

		$lightbox_settings = vcex_get_shortcode_onclick_lightbox_settings( $atts );

		if ( $lightbox_settings ) {
			$parsed_data = [];
			foreach ( $lightbox_settings as $k => $v ) {
				$parsed_data[] = 'data-' . $k . '="' . $v . '"';
			}
			$atts['lightbox_data'] = $parsed_data;
			if ( ! empty( $lightbox_settings['gallery'] ) ) {
				$atts['lightbox_class']= 'wpex-lightbox-gallery';
			}

		}

	}

}

// Start output.
$output .= '<figure class="' . esc_attr( $wrap_classes ) . '">';

	$inner_classes = [
		'vcex-image-inner',
		'wpex-relative',
	];

	if ( empty( $atts['width'] ) || '100%' !== $atts['width'] ) {
		$inner_classes[] = 'wpex-inline-block';
	}

	if ( ! empty( $atts['width'] ) && '100%' === $atts['width'] ) {
		$inner_classes[] = 'wpex-w-100';
	}

	if ( ! empty( $atts['img_filter'] ) ) {
		$inner_classes[] = vcex_image_filter_class( $atts['img_filter'] );
	}

	if ( ! empty( $atts['img_hover_style'] ) ) {
		$inner_classes[] = vcex_image_hover_classes( $atts['img_hover_style'] );
	}

	if ( $has_overlay ) {
		$inner_classes[] = vcex_image_overlay_classes( $atts['overlay_style'] );
	}

	if ( ! empty( $atts['hover_animation'] ) ) {
		$inner_classes[] = vcex_hover_animation_class( $atts['hover_animation'] );
	}

	if ( ! empty( $atts['css'] ) ) {
		$inner_classes[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
	}

	// Setup post data which is used for image overlays.
	// Checks to make sure it's not displaying inside a custom loop.
	if ( isset( $attachment ) && ! vcex_is_card() && $get_post = get_post( $attachment ) ) {
		global $post;
		$temp_post = $post;
		$post = $get_post;
	}

	// Begin module output.
	$output .= '<div class="' . esc_attr( implode( ' ', $inner_classes ) ) . '">';

		if ( ! empty( $onclick_attrs['href'] ) ) {
			$output .= '<a' . vcex_parse_html_attributes( $onclick_attrs ) . '>';
		}

		$output .= $image;

		if ( $dark_mode_check && ! empty( $dark_image ) ) {
			$output .= $dark_image;
		}

		if ( $has_overlay ) {
			$output .= vcex_get_image_overlay( 'inside_link', $atts['overlay_style'], $atts );
		}

		if ( ! empty( $onclick_attrs['href'] ) ) {
			if ( ! $has_overlay && vcex_validate_att_boolean( 'onclick_video_overlay_icon', $atts ) ) {
				$output .= vcex_get_image_overlay( 'inside_link', 'video-icon' );
			}
			$output .= '</a>';
		}

		if ( $has_overlay ) {
			$output .= vcex_get_image_overlay( 'outside_link', $atts['overlay_style'], $atts );
		}

	$output .= '</div>'; // close inner class.

	if ( vcex_validate_att_boolean( 'caption', $atts ) ) {
		if ( ! empty( $atts['img_caption'] ) ) {
			$caption_text = $atts['img_caption'];
		} elseif ( isset( $attachment ) ) {
			$caption_text = wp_get_attachment_caption( $attachment );
		}
		if ( isset( $caption_text ) && $caption_text_safe = vcex_parse_text_safe( $caption_text ) ) {
			$output .= '<figcaption class="vcex-image-caption wpex-mt-10">' . $caption_text_safe . '</figcaption>';
		}
	}

	if ( isset( $temp_post ) ) {
		$post = $temp_post;
	}

$output .= '</figure>';

// @codingStandardsIgnoreLine.
echo $output;
