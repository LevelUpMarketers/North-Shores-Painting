<?php

/**
 * vcex_milestone shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1.1
 */

defined( 'ABSPATH' ) || exit;

extract( $atts );

// Define vars & define defaults
$el_tag_safe = 'div';
$number = ! empty( $atts['number'] ) ? sanitize_text_field( $atts['number'] ) : '';
$animated = vcex_validate_att_boolean( 'animated', $atts, true );
$url = ! empty( $atts['url'] ) ? vcex_parse_text_safe( $atts['url'] ) : '';
$caption_safe  = vcex_parse_text_safe( $atts['caption'] ?? '' );
$has_classic_styles = vcex_has_classic_styles();

// Define URL attributes
if ( $url ) {
	$url_wrap = vcex_validate_att_boolean( 'url_wrap', $atts );
	$url_classes = [];
	if ( $url_wrap ) {
		$el_tag_safe = 'a';
	} else {
		$url_classes[] ='wpex-inherit-color';
	}
	$link_attrs = [
		'href'   => $url,
		'rel'    => $url_rel,
		'target' => $url_target,
		'class'  => $url_classes,
	];
}

// Milestone default args
extract( apply_filters( 'vcex_milestone_settings', [
	'separator' => isset( $atts['separator'] ) ? sanitize_text_field( $atts['separator'] ) : ',', // allow empty
	'decimal'   => ! empty( $atts['decimal_separator'] ) ? sanitize_text_field( $atts['decimal_separator'] ) : '.',
] ) );

// Parse number
if ( is_callable( $number ) && vcex_validate_user_func( $number ) ) {
	$number = (string) intval( call_user_func( $number ) );
}

if ( $number ) {
	$number = vcex_parse_text( $number );
	// remove the thousand simbol when the number is animated
	if ( $animated && $number && str_contains( $number, ',' ) ) {
		$number = str_replace( ',', '', $number );
	}
}

// Wrap atrrs
$wrap_attrs = [
	'id'    => ! empty( $atts['unique_id'] ) ? $atts['unique_id'] : null,
	'class' => '',
];

// Wrapper Classes
$wrap_class = [
	'vcex-module',
	'vcex-milestone',
];

if ( $url && $url_wrap ) {
	$wrap_class[] = 'vcex-milestone-has-link';
	$wrap_class[] = 'wpex-block';
	$wrap_class[] = 'wpex-no-underline';
	if ( isset( $link_attrs['class'] ) ) {
		$wrap_class = array_merge( $wrap_class, $link_attrs['class'] );
		unset( $link_attrs['class'] );
	}
	$wrap_attrs = array_merge( $wrap_attrs, $link_attrs );
}

if ( $url && $url_wrap ) {
	$wrap_class[] = 'wpex-hover-text-4';
}

switch ( $style ) {
	case 'boxed':
		$wrap_class[] = 'wpex-boxed';
		break;
	case 'bordered':
		$wrap_class[] = 'wpex-bordered';
		break;
}

if ( $atts['hover_animation'] ) {
	$wrap_class[] = vcex_hover_animation_class( $atts['hover_animation'] );
} elseif ( $atts['shadow_hover'] ) {
	$wrap_class[] = 'wpex-transition-shadow';
	$wrap_class[] = 'wpex-duration-300';
}

// Generate Icon if enabled.
if ( 'true' == $enable_icon ) {
	$icon_position = ! empty( $atts['icon_position'] ) ? sanitize_text_field( $atts['icon_position'] ) : 'inline';

	$wrap_class[] = "vcex-ip-{$icon_position}";

	$icon_classes = [
		'vcex-milestone-icon',
	];

	if ( ! $has_classic_styles && 'inline' !== $icon_position ) {
		$icon_classes[] = 'wpex-text-5xl';
	}

	$icon_classes_xtra = [];

	$icon_spacing = $icon_spacing ? absint( $icon_spacing ) : '15';

	switch ( $icon_position ) {
		case 'inline':
			$icon_tag = 'span';
			$icon_classes_xtra[] = "wpex-mr-{$icon_spacing}";
			$icon_classes_xtra[] = 'wpex-inline-block';
			break;
		case 'top':
			$icon_tag = 'span';
			$icon_classes_xtra[] = 'wpex-inline-block';
			$icon_classes_xtra[] = 'wpex-leading-none';
			$icon_classes_xtra[] = "wpex-mb-{$icon_spacing}";
			break;
		case 'left':
			$icon_tag = 'div';
			$icon_classes_xtra[] = 'wpex-leading-none';
			$icon_classes_xtra[] = "wpex-mr-{$icon_spacing}";
			break;
		case 'right':
			$icon_tag = 'div';
			$icon_classes_xtra[] = 'wpex-leading-none';
			$icon_classes_xtra[] = "wpex-ml-{$icon_spacing}";
			break;
		default:
			$icon_tag = 'span';
			break;
	}

	if ( $icon_classes_xtra ) {
		$icon_classes = array_merge( $icon_classes, $icon_classes_xtra );
	}

	$icon_tag_escaped = tag_escape( $icon_tag );

	$icon_html = '<' . $icon_tag_escaped . ' class="' . esc_attr( trim( implode( ' ', array_filter( $icon_classes ) ) ) ) . '">';
		if ( $icon_alternative_classes ) {
			$icon_html .= '<span class="' . esc_attr( do_shortcode( $icon_alternative_classes ) ) . '"></span>';
		} elseif ( $icon = vcex_get_icon_html( $atts, 'icon' ) ) {
			$icon_html .= $icon;
		}
	$icon_html .= '</' . $icon_tag_escaped . '>';

}

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	if ( ! empty( $atts['float'] ) ) {
		$wrap_class[] = vcex_parse_align_class( $atts['float'] );
	}
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation']  );
}

// Get extra classes.
$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_milestone' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

// Parse wrap classes.
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_milestone', $atts );

$wrap_attrs['class'] = $wrap_class;

/*------------------------------------------------------*/
/* [ Begin Output ]
/*------------------------------------------------------*/
$el_attrs_string = vcex_parse_html_attributes( $wrap_attrs );

$output = "<{$el_tag_safe}{$el_attrs_string}>";

	// Open Inner wrapper.
	$inner_classes = [
		'vcex-milestone-inner',
		'wpex-inline-block',
	];

	$output .= '<div class="' . esc_attr( implode( ' ', $inner_classes ) ) . '">';

		// Open flex container for left/right positioned icons
		if ( 'left' === $icon_position || 'right' === $icon_position ) {
			$flex_classes = [
				'vcex-milestone-flex-wrap',
				'wpex-flex',
				'wpex-items-center'
			];
			if ( 'right' === $icon_position ) {
				$flex_classes[] = 'wpex-flex-row-reverse';
			}
			$output .= '<div class="' . esc_attr( implode( ' ', $flex_classes ) ) . '">';
		}

		// Add icon for top/left/right positions
		if ( ! empty( $icon_html ) && in_array( $icon_position, [ 'top', 'left', 'right' ], true ) ) {
			$output .= $icon_html;
		}

		// Desc classes
		$desc_classes = array( 'vcex-milestone-desc' );

		// Open description element
		$output .= '<div class="' . esc_attr( implode( ' ', $desc_classes ) ) . '">';

			// Number classes
			$number_classes = [
				'vcex-milestone-number',
				'wpex-leading-none',
			];

			if ( ! $has_classic_styles ) {
				$number_classes[] = 'wpex-text-5xl';
			}

			if ( ! $number_weight ) {
				$number_classes[] = 'wpex-font-semibold';
			}

			$number_classes = (array) apply_filters( 'vcex_milestone_number_class', $number_classes );

			// Display number
			$output .= '<div class="' . esc_attr( implode( ' ', $number_classes ) ) . '">';

				if ( $before || 'true' == $enable_icon ) {
					$output .= '<span class="vcex-milestone-before">';
						if ( ! empty( $icon_html ) && 'inline' == $icon_position ) {
							$output .= $icon_html;
						}
						$output .= vcex_parse_text_safe( $before );
					$output .= '</span>';
				}

				// Output milestone number
				if ( $animated ) {
					$speed = ! empty( $atts['speed'] ) ? intval( $atts['speed'] ) : 2500;
					$settings = [
						'startVal'        => floatval( ! empty( $atts['startval'] ) ? vcex_parse_text_safe( $atts['startval'] ) : 0 ),
						'endVal'          => $number, // make sure we strip crap out
						'duration'        => $speed ? $speed/1000 : 2.5,
						'decimals'        => ! empty( $atts['decimals'] ) ? absint( $atts['decimals'] ) : 0,
						'separator'       => esc_js( $separator ),
						'decimal'         => esc_js( $decimal ),
						'animateOnScroll' => vcex_validate_att_boolean( 'animate_onscroll', $atts ),
					];
					$output .= '<span class="vcex-milestone-time vcex-countup" data-options="' . htmlspecialchars( wp_json_encode( $settings ) ) . '" aria-hidden="true">' . esc_html( $startval ) . '</span>';
					$output .= '<span class="screen-reader-text">' . esc_html( $number ) . '</span>';
				} else {
					$output .= '<span class="vcex-milestone-time">' . esc_html( $number ) . '</span>';
				}

				// Display after text if defined
				if ( $after ) {
					$output .= '<span class="vcex-milestone-after">' . vcex_parse_text_safe( $after ) . '</span>';
				}

			// Close number/after container
			$output .= '</div>';

			// Display caption
			if ( $caption_safe ) {

				// Caption classes
				$caption_classes = [
					'vcex-milestone-caption',
					'wpex-mt-5',
				];

				if ( ! $atts['caption_size'] ) {
					$caption_classes[] = 'wpex-text-lg';
				}

				$caption_classes = (array) apply_filters( 'vcex_milestone_caption_class', $caption_classes );

				// Load custom font
				if ( $atts['caption_font_family'] ) {
					vcex_enqueue_google_font( $atts['caption_font_family'] );
				}

				// Display caption
				$output .= '<div class="' . esc_attr( implode( ' ', $caption_classes ) ) . '">';
					if ( $url && ! $url_wrap ) {
						$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>' . $caption_safe . '</a>';
					} else {
						$output .= $caption_safe;
					}
				$output .= '</div>';

			}

		$output .= '</div>'; // end desc

		// Close flex container
		if ( 'left' === $atts['icon_position'] || 'right' === $atts['icon_position'] ) {
			$output .= '</div>';
		}

	$output .= '</div>';

$output .= "</{$el_tag_safe}>";

// @codingStandardsIgnoreLine
echo $output;
