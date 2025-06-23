<?php

/**
 * vcex_image_banner shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0.3
 */

defined( 'ABSPATH' ) || exit;

$onclick_attrs = vcex_get_shortcode_onclick_attributes( $atts, 'vcex_image_banner' );
$has_link = ! empty( $onclick_attrs['href'] );
$use_img_tag = vcex_validate_att_boolean( 'use_img_tag', $atts );
$content_align = ! empty( $atts['content_align'] ) ? sanitize_text_field( $atts['content_align'] ) : false;
$text_align = ! empty( $atts['text_align'] ) ? sanitize_text_field( $atts['text_align'] ) : false;
$has_button = vcex_validate_att_boolean( 'button', $atts ) && ! empty( $atts['button_text'] );
$justify = ! empty( $atts['justify_content'] ) ? sanitize_html_class( $atts['justify_content'] ) : 'center';
$show_on_hover = vcex_validate_att_boolean( 'show_on_hover', $atts );
$show_on_hover_anim = ! empty( $atts['show_on_hover_anim'] ) ? sanitize_text_field( $atts['show_on_hover_anim'] ) : 'fade-up';
$has_overlay = vcex_validate_att_boolean( 'overlay', $atts, true );
$caption_safe = ! empty( $atts['caption'] ) ? vcex_parse_text_safe( $atts['caption'] ) : '';
$has_link_wrap = $has_link && ( ! $has_button || vcex_validate_att_boolean( 'link_wrap', $atts, true ) );
$el_tag_safe = $has_link_wrap ? 'a' : 'div';
$transition_duration = 'wpex-duration-500';

// Wrap atrrs
$wrap_attrs = [
	'id'    => ! empty( $atts['unique_id'] ) ? $atts['unique_id'] : null,
	'class' => '',
];

// Wrap classes.
$wrap_class = [
	'vcex-module',
	'vcex-image-banner',
];

if ( vcex_validate_att_boolean( 'fill_column', $atts ) ) {
	$wrap_class[] = 'vcex-fill-column';
}

if ( $has_link && $has_link_wrap ) {
	$wrap_class[] = 'vcex-image-banner-has-link';
	$wrap_class[] = 'wpex-no-underline';
	if ( ! empty( $onclick_attrs['class'] ) && is_array( $onclick_attrs['class'] ) ) {
		$wrap_class = array_merge( $wrap_class, $onclick_attrs['class'] );
		unset( $onclick_attrs['class'] );
	}
	$wrap_attrs = array_merge( $wrap_attrs, $onclick_attrs );
}

// Utility classes
$wrap_class = array_merge( $wrap_class, [
	'wpex-flex',
	'wpex-flex-col',
	'wpex-w-100', // important because it's a flex element - so it works correctly in grid containers.
	"wpex-justify-{$justify}",
	'wpex-relative',
	'wpex-overflow-hidden',
	'wpex-bg-gray-900',
	'wpex-text-white',
	'wpex-hover-text-white',
] );

// Bottom margin.
if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

// Alignment.
if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$wrap_class[] = vcex_parse_align_class( ! empty( $atts['align'] ) ? $atts['align'] : 'center' );
}

// CSS animation.
if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

// Text alignment.
if ( ! $content_align && ! $text_align ) {
	$wrap_class[] = 'wpex-text-center';
} else {
	if ( $text_align ) {
		$wrap_class[] = vcex_parse_text_align_class( $text_align );
	} elseif ( $content_align ) {
		$wrap_class[] = vcex_parse_text_align_class( $content_align );
	}
}

// Custom border radius.
if ( ! empty( $atts['border_radius'] ) ) {
	$wrap_class[] = vcex_parse_border_radius_class( $atts['border_radius'] );
}

// Shadow.
if ( ! empty( $atts['shadow'] ) ) {
	$wrap_class[] = vcex_parse_shadow_class( $atts['shadow'] );
}

// Hover classes.
if ( $show_on_hover ) {
	$wrap_class[] = 'vcex-soh';
	$wrap_class[] = 'vcex-anim-' . sanitize_html_class( $show_on_hover_anim );
}

// Zoom class.
if ( vcex_validate_att_boolean( 'image_zoom', $atts ) ) {
	$wrap_class[] = 'vcex-h-zoom';
	$wrap_class[] = 'wpex-isolate';
}

// Image tag class.
if ( $use_img_tag ) {
	$wrap_class[] = 'vcex-has-img-tag';
}

// Button class.
if ( $has_button ) {
	$wrap_class[] = 'vcex-has-button';
}

// Custom Class.
if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'] );
}

// Wrap tab index
$wrap_tabindex = '';
if ( $show_on_hover && ! $has_link ) {
	$wrap_attrs['tabindex'] = '0';
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_image_banner', $atts );

$wrap_attrs['class'] = $wrap_class;

/*-------------------------------------------------------------------------------*/
/* [ Output Starts here ]
/*-------------------------------------------------------------------------------*/
$el_attrs_string = vcex_parse_html_attributes( $wrap_attrs );

$output = "<{$el_tag_safe}{$el_attrs_string}>";

	/*-------------------------------------------------------------------------------*/
	/* [ Image ]
	/*-------------------------------------------------------------------------------*/
	$image = vcex_get_image_from_source( $atts['image_source'] ?? 'media_library', $atts );

	if ( $image ) {

		$img_classes = [
			'vcex-ib-img',
			'wpex-block',
			'wpex-transition-all',
			$transition_duration,
		];

		if ( $use_img_tag ) {
			if ( vcex_validate_att_boolean( 'img_cover', $atts ) ) {
				$img_classes[] = 'wpex-block';
				$img_classes[] = 'wpex-flex-grow';
				$img_classes[] = 'wpex-h-100';
				$img_classes[] = 'wpex-w-100';
				$img_classes[] = 'wpex-object-cover';
			}

			if ( ! empty( $atts['img_aspect_ratio'] ) ) {
				$img_classes[] = vcex_parse_aspect_ratio_class( $atts['img_aspect_ratio'] );
			}
		}

		// Parse image.
		if ( is_numeric( $image ) ) {
			$img_url = vcex_get_post_thumbnail( [
				'attachment' => $image,
				'size'       => $atts['img_size'] ?? '',
				'crop'       => $atts['img_crop'] ?? '',
				'width'      => $atts['img_width'] ?? '',
				'height'     => $atts['img_height'] ?? '',
				'return'     => 'url',
			] );
			$img_alt = vcex_get_attachment_data( $image, 'alt' );
		} else {
			$img_url = $image;
		}

		// Display image.
		if ( $use_img_tag ) {
			$image_alt = '';
			$image_attrs = [
				'src'   => $img_url,
				'class' => $img_classes,
				'alt'   => $img_alt ?? '',
			];
			$output .= '<img ' . vcex_parse_html_attributes( $image_attrs )  . '>';
		} else {
			$img_classes[] = 'wpex-bg-cover';
			$img_classes[] = 'wpex-absolute';
			$img_classes[] = 'wpex-inset-0';
			$style = vcex_inline_style( [
				'background_image'    => esc_url( $img_url ),
				'background_position' => $atts['image_position'] ?? null,
			] );
			$output .= '<span class="' . esc_attr( implode( ' ', $img_classes ) ) . '"' . $style . '></span>';
		}

	}

	/*-------------------------------------------------------------------------------*/
	/* [ Overlay ]
	/*-------------------------------------------------------------------------------*/
	if ( $has_overlay ) {

		$overlay_classes = [
			'vcex-ib-overlay',
			'wpex-absolute',
			'wpex-inset-0',
			'wpex-transition-all',
			$transition_duration,
		];

		$output .= '<div class="' . esc_attr( implode( ' ', $overlay_classes ) ) . '">';

			$overlay_color_classes = [
				'vcex-ib-overlay-bg',
				'wpex-absolute',
				'wpex-inset-0',
				'wpex-bg-black',
				'wpex-opacity-30',
			];

			if ( ! empty( $atts['overlay_blend'] ) ) {
				$overlay_color_classes[] = 'wpex-mix-blend-' . sanitize_html_class( $atts['overlay_blend'] );
			}

			$output .= '<div class="' . esc_attr( implode( ' ', $overlay_color_classes ) ) . '"></div>';

		$output .= '</div>';

	}

	/*-------------------------------------------------------------------------------*/
	/* [ Inner Border ]
	/*-------------------------------------------------------------------------------*/
	if ( vcex_validate_att_boolean( 'inner_border', $atts ) ) {

		$inner_border_margin = ! empty( $atts['inner_border_margin'] ) ? absint( $atts['inner_border_margin'] ) : '15';
		$inner_border_style  = ! empty( $atts['inner_border_style'] ) ? (string) $atts['inner_border_style'] : 'solid';

		$inner_border_class = [
			'vcex-ib-border',
			'wpex-absolute',
			'wpex-inset-0',
			'wpex-m-' . sanitize_html_class( $inner_border_margin ),
			'wpex-border',
			'wpex-border-' . sanitize_html_class( $inner_border_style ),
			'wpex-pointer-events-none',
			'wpex-transition-all',
			$transition_duration,
		];

		if ( ! empty( $atts['inner_border_radius'] ) ) {
			$border_radius_class = vcex_sanitize_border_radius( $atts['inner_border_radius'] );
			$inner_border_class[] = "wpex-{$border_radius_class}";
		}

		if ( ! empty( $atts['inner_border_color'] ) ) {
			$inner_border_class[] = 'wpex-border-white';
		}

		$output .= '<div class="' . esc_attr( implode( ' ', $inner_border_class ) ) . '"></div>';
	}

	/*-------------------------------------------------------------------------------*/
	/* [ Content wrap open]
	/*-------------------------------------------------------------------------------*/
	$content_classes = [
		'vcex-ib-content-wrap',
		'wpex-w-100',
		'wpex-transition-all',
		$transition_duration,
	];

	if ( $use_img_tag ) {
		$content_classes[] = 'wpex-absolute';
		$content_classes[] = 'wpex-inset-0';
		$content_classes[] = 'wpex-flex';
		$content_classes[] = 'wpex-items-' . sanitize_html_class( ! empty( $atts['flex_align'] ) ? $atts['flex_align'] : 'center' );
		$content_classes[] = 'wpex-p-30';
	} else {
		$content_classes[] = 'wpex-py-100 wpex-px-40';
		$content_classes[] = 'wpex-relative';
	}

	if ( $show_on_hover && 'fade-up' == $show_on_hover_anim ) {
		$content_classes[] = 'wpex-translate-y-50';
	}

	$output .= '<div class="' . esc_attr( implode( ' ', $content_classes ) ) . '">';

		// Content class
		$content_class = [
			'vcex-ib-content',
		];

		if ( ! empty( $atts['content_bg'] ) ) {
			$content_class[] = 'wpex-p-15';
		}

		if ( $use_img_tag && empty( $atts['content_width'] ) ) {
			$content_class[] = 'wpex-flex-grow'; // @todo is this even needed?
		}

		if ( $content_align ) {
			switch ( $content_align ) {
				case 'center' :
					$content_class[] = 'wpex-mx-auto';
					break;
				case 'left':
					$content_class[] = 'wpex-mr-auto';
					break;
				case 'right':
					$content_class[] = 'wpex-ml-auto';
					break;
			}
		} else {
			$content_class[] = 'wpex-mx-auto';
		}

		$output .= '<div class="' . esc_attr( implode( ' ', $content_class ) ) . '">';

			/*-------------------------------------------------------------------------------*/
			/* [ Heading ]
			/*-------------------------------------------------------------------------------*/
			$heading_safe = vcex_parse_text_safe( $atts['heading'] ?? '' );
			if ( $heading_safe ) {

				// Sanitize custom heading tag.
				$heading_tag = tag_escape( ! empty( $atts['heading_tag'] ) ? $atts['heading_tag'] : 'div' );

				// Heading classes.
				$heading_classes = [
					'vcex-ib-title',
					'wpex-heading',
				];

				if ( ! vcex_has_classic_styles() ) {
					$heading_classes[] = 'wpex-text-4xl';
				}

				if ( empty( $atts['heading_color'] ) ) {
					$heading_classes[] = 'wpex-inherit-color-important';
				}

				/**
				 * Filters the Image Banner heading classes.
				 *
				 * @param array $heading_classes
				 * @param array $shortcode_atts
				 */
				$heading_classes = apply_filters( 'vcex_image_banner_heading_class', $heading_classes, $atts );

				$output .= vcex_parse_html( $heading_tag, [ 'class' => $heading_classes ], $heading_safe );
			}

			/*-------------------------------------------------------------------------------*/
			/* [ Caption ]
			/*-------------------------------------------------------------------------------*/
			if ( $caption_safe ) {

				$caption_classes = [
					'vcex-ib-caption',
					'wpex-text-lg',
					'wpex-last-mb-0',
				];

				if ( $has_button && ! ! empty( $atts['caption_bottom_padding'] ) ) {
					$caption_classes[] = 'wpex-pb-10';
				}

				/**
				 * Filters the Image Banner caption classes.
				 *
				 * @param array $caption_class
				 * @param array $shortcode_atts
				 */
				$caption_classes = (array) apply_filters( 'vcex_image_banner_caption_class', $caption_classes, $atts );

				$output .= vcex_parse_html( 'div', [ 'class' => $caption_classes ], $caption_safe );

			}

			/*-------------------------------------------------------------------------------*/
			/* [ Button ]
			/*-------------------------------------------------------------------------------*/
			$button_text_safe = vcex_parse_text_safe( ! empty( $atts['button_text'] ) ? (string) $atts['button_text'] : esc_html__( 'learn more', 'total-theme-core' ) );

			if ( $has_button && $button_text_safe ) {

				$button_style = ! empty( $atts['button_style'] ) ? $atts['button_style'] : '';
				$button_color = ! empty( $atts['button_color'] ) ? $atts['button_color'] : '';

				$button_classes = [
					'vcex-ib-button-inner',
					vcex_get_button_classes( $button_style, $button_color ),
				];

				if ( $has_link && ! $has_link_wrap ) {
					$button_inner_tag = 'a';
					$onclick_attrs['class'] = array_merge( $button_classes, $onclick_attrs['class'] );
					$button_attributes = $onclick_attrs;
				} else {
					$button_inner_tag = 'span';
					$button_attributes = [
						'class' => $button_classes,
					];
				}

				$output .= '<div class="vcex-ib-button">';
					$output .= vcex_parse_html( $button_inner_tag, $button_attributes, $button_text_safe );
				$output .= '</div>';
			}

		$output .= '</div>';

	$output .= '</div>';

$output .= "</{$el_tag_safe}>";

// @codingStandardsIgnoreLine.
echo $output;
