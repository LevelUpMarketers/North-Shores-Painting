<?php

/**
 * vcex_teaser shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Define vars.
$output        = '';
$el_tag_safe   = 'div';
$style         = ! empty( $atts['style'] ) ? sanitize_text_field( $atts['style'] ) : '';
$heading_safe  = ! empty( $atts['heading'] ) ? vcex_parse_text_safe( $atts['heading'] ) : '';
$visibility    = ! empty( $atts['visibility'] ) ? sanitize_text_field( $atts['visibility'] ) : '';
$onclick_el    = ! empty( $atts['onclick_el'] ) ? sanitize_text_field( $atts['onclick_el'] ) : '';
$url_wrap      = ( 'container' === $onclick_el && ! str_contains( $content, '<a' ) ) ? true : false;
$onclick_attrs = vcex_get_shortcode_onclick_attributes( $atts, 'vcex_teaser' );
$has_link      = ! empty( $onclick_attrs['href'] );
$show_button   = $has_link && vcex_validate_att_boolean( 'show_button', $atts );

if ( $show_button && ! $url_wrap ) {
	$onclick_el = 'button';
}

// Wrap attributes.
$wrap_attrs = [
	'id'    => ! empty( $atts['unique_id'] ) ? $atts['unique_id'] : null,
	'class' => '',
];

// Wrap classes.
$wrap_class = [
	'vcex-module',
	'vcex-teaser',
];

if ( $style ) {
	$wrap_class[] = 'vcex-teaser-' . sanitize_html_class( $style );
}

// Get onclick attributes.
if ( $has_link ) {
	if ( $url_wrap ) {
		$el_tag_safe = 'a';
		$wrap_class[] = 'vcex-teaser-has-link';
		$wrap_class[] = 'wpex-inherit-color';
		$wrap_class[] = 'wpex-no-underline';
		if ( isset( $onclick_attrs['class'] ) && is_array( $onclick_attrs['class'] ) ) {
			$wrap_class = array_merge( $wrap_class, $onclick_attrs['class'] );
		}
		unset( $onclick_attrs['class'] );
		$wrap_attrs = array_merge( $wrap_attrs, $onclick_attrs );
	} else {
		if ( isset( $onclick_attrs['class'] ) && is_array( $onclick_attrs['class'] ) ) {
			$onclick_attrs['class'] = array_merge( [ 'wpex-no-underline' ], $onclick_attrs['class'] );
		}
		$open_link_el = '<a' . vcex_parse_html_attributes( $onclick_attrs ) . '>';
	}
}

// Important for url_wrap and button.
$wrap_class[] = 'wpex-flex';
$wrap_class[] = 'wpex-flex-col';

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['shadow'] ) ) {
	$wrap_class[] = vcex_parse_shadow_class( $atts['shadow'] );
}

if ( ! empty( $atts['classes'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['classes'] );
}

if ( ! empty( $atts['text_align'] ) ) {
	$wrap_class[] = vcex_parse_text_align_class( $atts['text_align'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( trim( $atts['hover_animation'] ?? '' ) ) ) {
	$wrap_class[] = vcex_hover_animation_class( $atts['hover_animation'] );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

// Style specific classes.
switch( $style ) {
	case 'two':
		$wrap_class[] = 'wpex-surface-2';
		$wrap_class[] = 'wpex-p-20';
		if ( empty( $atts['border_radius'] ) ) {
			$wrap_class[] = 'wpex-rounded';
		}
		break;
	case 'three':
		$wrap_class[] = 'wpex-surface-2';
		break;
	case 'four':
		$wrap_class[] = 'wpex-border wpex-border-solid wpex-border-main';
		break;
}

if ( $atts['css'] ) {
	$wrap_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
}

// Add inline style for main div (uses special code because of extra checks).
$wrap_style = [];

if ( ! empty( $atts['padding'] ) && ( 'two' === $style || 'three' === $style ) ) {
	$wrap_style['padding'] = $atts['padding'];
}

if ( ! empty( $atts['background'] ) ) {
	if ( 'two' === $style || ( 'three' === $style && empty( $atts['content_background'] ) ) ) {
		$wrap_style['background'] = $atts['background'];
	}
}

if ( ! empty( $atts['border_radius'] ) ) {
	$wrap_class[] = 'wpex-overflow-hidden';
}

$wrap_style = vcex_inline_style( $wrap_style, false );

// Media and Content classes for different styles.
$media_classes = [
	'vcex-teaser-media',
];

$content_classes = 'vcex-teaser-content';

if ( $show_button ) {
	$content_classes .= ' wpex-flex wpex-flex-grow wpex-flex-col';
}

if ( in_array( $style, array( 'three', 'four' ) )
	|| ( ! empty( $atts['shadow'] ) ) && in_array( $style, [ '', 'one' ] )
) {
	$content_classes .= ' wpex-p-20';
} elseif ( empty( $atts['img_bottom_margin'] ) ) {
	$media_classes[] = 'wpex-mb-20';
}

if ( ! empty( $atts['img_bottom_margin'] ) ) {
	$media_classes[] = vcex_parse_margin_class( $atts['img_bottom_margin'], 'bottom' );
}

// Parse wrap classes.
$wrap_attrs['class'] = vcex_parse_shortcode_classes( $wrap_class, 'vcex_teaser', $atts );

// Add wrapper css.
if ( $wrap_style ) {
	$wrap_attrs['style'] = $wrap_style;
}

/*-------------------------------------------------------------------------------*/
/* [ Begin Output ]
/*-------------------------------------------------------------------------------*/
$output .= '<' . $el_tag_safe . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	/*-------------------------------------------------------------------------------*/
	/* [ Display Video ]
	/*-------------------------------------------------------------------------------*/
	if ( ! empty( $atts['video'] ) ) {

		if ( ! empty( $atts['img_border_radius'] ) ) {
			$img_border_radius_class = vcex_parse_border_radius_class( $atts['img_border_radius'] );
			if ( $img_border_radius_class ) {
				$media_classes[] = $img_border_radius_class;
				$media_classes[] = 'wpex-overflow-hidden';
			}
		}

		$output .= '<div class="' . esc_attr( implode( ' ', $media_classes ) ) . ' responsive-video-wrap">';

			if ( apply_filters( 'wpex_has_oembed_cache', true ) ) { // filter added for testing purposes.
				global $wp_embed;
				if ( $wp_embed && is_object( $wp_embed ) ) {
					$video_html = $wp_embed->shortcode( [], wp_strip_all_tags( $atts['video'] ) );
					// Check if output is a shortcode because if the URL is self hosted
					// it will pass through wp_embed_handler_video which returns a video shortcode
					if ( ! empty( $video_html )
						&& is_string( $video_html )
						&& str_starts_with( $video_html, '[video' )
					) {
						$video_html = do_shortcode( $video_html );
					}
					$output .= $video_html;
				}
			} else {
				$video_html = wp_oembed_get( wp_strip_all_tags( $atts['video'] ) );
				if ( ! empty( $video_html ) && ! is_wp_error( $video_html ) ) {
					$output .= '<div class="wpex-responsive-media">' . $video_html . '</div>';
				}
			}

		$output .= '</div>';

	}

	/*-------------------------------------------------------------------------------*/
	/* [ Image ]
	/*-------------------------------------------------------------------------------*/
	if ( $image = vcex_get_image_from_source( $atts['image_source'] ?? 'media_library', $atts ) ) {

		// Image classes.
		if ( ! empty( $atts['img_filter'] ) ) {
			$media_classes[] = vcex_image_filter_class( $atts['img_filter'] );
		}

		if ( ! empty( $atts['img_hover_style'] ) ) {
			$media_classes[] = vcex_image_hover_classes( $atts['img_hover_style'] );
		}

		if ( ! empty( $atts['img_align'] ) ) {
			$media_classes[] = vcex_parse_text_align_class( $atts['img_align'] );
		}

		if ( ! empty( $atts['img_style'] ) && 'stretch' === $atts['img_style'] ) {
			$media_classes[] = 'stretch-image';
		}

		$output .= '<div class="' . esc_attr( implode( ' ', $media_classes ) ) . '">';

			if ( ! $onclick_el && ! empty( $open_link_el ) ) {
				$output .= $open_link_el;
			}

			$image_class = 'wpex-align-middle';

			if ( ! empty( $atts['img_border_radius'] ) ) {
				$img_border_radius_class = vcex_parse_border_radius_class( $atts['img_border_radius'] );
				if ( $img_border_radius_class ) {
					$image_class .= ' ' . $img_border_radius_class;
				}
			}

			if ( ! empty( $atts['img_aspect_ratio'] ) && $aspect_ratio_class = vcex_parse_aspect_ratio_class( $atts['img_aspect_ratio'] ) ) {
				$image_class .= " {$aspect_ratio_class}";
				$object_fit = ! empty( $atts['img_object_fit'] ) ? $atts['img_object_fit'] : 'cover';
				if ( $object_fit_class = vcex_parse_object_fit_class( $atts['img_object_fit'] ) ) {
					$image_class .= " {$object_fit_class}";
				}
			}

			// Image attachment.
			if ( is_numeric( $image ) ) {
				$thumbnail_args = [
					'attachment' => $image,
					'crop'       => $atts['img_crop'] ?? '',
					'size'       => $atts['img_size'] ?? '',
					'width'      => $atts['img_width'] ?? '',
					'height'     => $atts['img_height'] ?? '',
					'alt'        => $atts['image_alt'] ?? '',
					'class'      => $image_class,
				];

				if ( ! vcex_validate_att_boolean( 'img_lazy_load', $atts, true ) ) {
					$thumbnail_args['lazy'] = false;
				}

				if ( ! empty( $atts['img_fetchpriority'] ) && 'auto' !== $atts['img_fetchpriority'] ) {
					$thumbnail_args['attributes']['fetchpriority'] = esc_attr( $atts['img_fetchpriority'] );
				}

				$output .= vcex_get_post_thumbnail( $thumbnail_args );
			}

			// Non image attachment.
			elseif ( is_string( $image ) ) {
				$img_attrs = [
					'src'   => $image,
					'class' => $image_class,
				];

				if ( vcex_validate_att_boolean( 'img_lazy_load', $atts, true ) ) {
					$img_attrs['loading'] = 'lazy';
					$img_attrs['decoding'] = 'async';
				}

				if ( ! empty( $atts['img_fetchpriority'] ) && 'auto' !== $atts['img_fetchpriority'] ) {
					$img_attrs['attributes']['fetchpriority'] = esc_attr( $atts['img_fetchpriority'] );
				}

				$img_attrs_string = trim( vcex_parse_html_attributes( $img_attrs ) );

				$output .= "<img {$img_attrs_string}>";
			}

			if ( ! $onclick_el && ! empty( $open_link_el ) ) {
				$output .= '</a>';
			}

		$output .= '</div>';

	} // End image output.

	/*-------------------------------------------------------------------------------*/
	/* [ Details ]
	/*-------------------------------------------------------------------------------*/
	if ( $content || $heading_safe ) {

		$output .= '<div class="' . esc_attr( $content_classes ) . '">';

			/*-------------------------------------------------------------------------------*/
			/* [ Heading ]
			/*-------------------------------------------------------------------------------*/
			if ( $heading_safe ) {

				// Define heading tag.
				$safe_heading_tag = tag_escape( ! empty( $atts['heading_type'] ) ? $atts['heading_type'] : 'h2' );

				// Heading class.
				$heading_class = [
					'vcex-teaser-heading',
				];

				if ( ! empty( $atts['heading_typography_style'] ) ) {
					$heading_class[] = vcex_parse_typography_style_class( $atts['heading_typography_style'] );
					$heading_class[] = 'wpex-m-0';
					if ( 'span' === $safe_heading_tag ) {
						$heading_class[] = 'wpex-block';
					}
				} else {
					$heading_class[] = 'wpex-heading';
					$heading_class[] = vcex_has_classic_styles() ? 'wpex-text-lg' : 'wpex-text-xl';
				}

				if ( ! empty( $atts['heading_color'] ) && ! $url_wrap && ! $show_button && isset( $open_link_el ) ) {
					$heading_class[] = 'wpex-child-inherit-color';
				}

				// Heading output..
				$output .= '<' . $safe_heading_tag. ' class="' . esc_attr( implode( ' ', $heading_class ) ) . '">';

					// Open URL.
					if ( ! $onclick_el && ! empty( $open_link_el ) ) {
						$output .= $open_link_el;
					}

						$output .= $heading_safe;

					// Close URL.
					if ( ! $onclick_el && ! empty( $open_link_el ) ) {
						$output .= '</a>';
					}

				$output .= "</{$safe_heading_tag}>";

			} // End heading.

			/*-------------------------------------------------------------------------------*/
			/* [ Content ]
			/*-------------------------------------------------------------------------------*/
			if ( $content ) {
				$content_text_class = [
					'vcex-teaser-text',
					'wpex-mt-' . absint( $atts['content_top_margin'] ?: 10 ),
					'wpex-last-mb-0',
					'wpex-clr',
				];
				if ( $show_button ) {
					$content_text_class[] = 'wpex-mb-20';
				}
				$output .= '<div class="' . esc_attr( implode( ' ', $content_text_class ) ) . '">';
					$output .= vcex_the_content( $content );
				$output .= '</div>';
			}

			/*-------------------------------------------------------------------------------*/
			/* [ Button ]
			/*-------------------------------------------------------------------------------*/
			if ( $show_button ) {
				$output .= '<div class="vcex-teaser-button-wrap wpex-mt-auto">';
					$button_text = ! empty( $atts['button_text'] ) ? vcex_parse_text_safe( $atts['button_text'] ) : esc_html__( 'Learn more', 'total-theme-core' );
					$button_class = 'vcex-teaser-button theme-button';
					if ( ! empty( $atts['button_class'] ) ) {
						$button_class .= ' ' . trim( esc_attr( $atts['button_class'] ) );
					}
					if ( $url_wrap ) {
						$output .= '<span class="' . esc_attr( $button_class ) . '">' . $button_text . '</span>';
					} else {
						if ( isset( $onclick_attrs['class'] ) && is_array( $onclick_attrs['class'] ) ) {
							$onclick_attrs['class'][] = $button_class;
							foreach (  $onclick_attrs['class'] as $class_k => $class_v ) {
								if ( 'wpex-no-underline' === $class_v ) {
									unset( $onclick_attrs['class'][ $class_k ] );
									break;
								}
							}
						} else {
							$onclick_attrs['class'] = $button_class;
						}
						$output .= '<a' . vcex_parse_html_attributes( $onclick_attrs ) . '>' . $button_text . '</a>';
					}
				$output .= '</div>';
			}

		$output .= '</div>'; // End content

	} // End heading & content display.

$output .= "</{$el_tag_safe}>";

// @codingStandardsIgnoreLine
echo $output;
