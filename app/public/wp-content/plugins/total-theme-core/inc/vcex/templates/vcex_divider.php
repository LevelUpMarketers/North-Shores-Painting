<?php

/**
 * vcex_divider shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.8.8
 */

defined( 'ABSPATH' ) || exit;

// Extract shortcode attributes.
extract( $atts );

$output = '';
$style = ! empty( $atts['style'] ) ? $atts['style'] : 'solid';
$height = ! empty( $atts['height'] ) ? vcex_validate_px( $atts['height'], 'px' ) : '';

if ( 'zig-zag' === $style ) {
	if ( ! $height  ) {
		$height = '14px';
	}
	$zig_zag_svg = '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg width="' . esc_attr( ( intval( $height ) + 2 ) . 'px' ) . '" height="' . esc_attr( intval( $height ) . 'px' ) . '" viewBox="0 0 18 15" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon fill="currentColor" points="8.98762301 0 0 9.12771969 0 14.519983 9 5.40479869 18 14.519983 18 9.12771969"></polygon></svg>';
	$image_mask = rawurlencode( $zig_zag_svg );
}

if ( ! empty( $atts['icon_alternative_character'] ) ) {
	$icon_html = vcex_parse_text_safe( $atts['icon_alternative_character'] );
} else {
	$icon_html = vcex_get_icon_html( $atts, 'icon' );
}

/*-------------------------------------------------*/
/* [ Style Based Utility Classes ]
/*-------------------------------------------------*/
$util_border        = '';
$util_border_style  = '';
$util_border_color  = '';
$util_inner_padding = '';

switch ( $style ) {
	case 'solid' :
		$util_border       = 'wpex-border-b';
		$util_border_style = 'wpex-border-solid';
		$util_border_color = 'wpex-border-main';
	break;
	case 'dashed' :
		$util_border       = 'wpex-border-b-2';
		$util_border_style = 'wpex-border-dashed';
		$util_border_color = 'wpex-border-main';
	break;
	case 'dotted-line' :
		$util_border       = 'wpex-border-b-2';
		$util_border_style = 'wpex-border-dotted';
		$util_border_color = 'wpex-border-main';
	break;
	case 'double' :
		$util_border        = 'wpex-border-y';
		$util_border_style  = 'wpex-border-solid';
		$util_border_color  = 'wpex-border-main';
	break;
}

/*-------------------------------------------------*/
/* [ Wrap Classes ]
/*-------------------------------------------------*/
$wrap_class = [
	'vcex-module',
	'vcex-divider',
	'vcex-divider-' . sanitize_html_class( $style ),
];

if ( ! empty( $atts['margin_y'] ) ) {
	$wrap_class[] = 'wpex-my-' . sanitize_html_class( absint( $atts['margin_y'] ) );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

$align = ! empty( $atts['align'] ) ? $atts['align'] : 'center';
if ( ! empty( $align ) && 'none' !== $align ) {
	$wrap_class[] = 'vcex-divider-' . sanitize_html_class( $atts['align'] );
	$wrap_class[] = vcex_parse_align_class( $align );
}

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

// Add icon utility classes.
if ( $icon_html ) {
	$wrap_class[] = 'vcex-divider-has-icon';
	$wrap_class[] = 'wpex-flex';
	$wrap_class[] = 'wpex-items-center';
}

// Add border utility classes (only when an icon isn't defined).
else {
	$wrap_class[] = 'wpex-block';
	$wrap_class[] = 'wpex-h-0';
	if ( $util_border ) {
		$wrap_class[] = $util_border;
	}
	if ( $util_border_style ) {
		$wrap_class[] = $util_border_style;
	}
	if ( $util_border_color ) {
		$wrap_class[] = $util_border_color;
	}
	if ( $util_inner_padding ) {
		$wrap_class[] = $util_inner_padding;
	}
	switch ( $style ) {
		case 'double':
			$wrap_class[] = 'wpex-pb-5';
			break;
	}
}

// Add custom classes last.
if ( $el_class ) {
	$wrap_class[] = vcex_get_extra_class( $el_class );
}

// Turn wrap classes into a string.
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_divider', $atts );

/*-------------------------------------------------*/
/* [ Icon Checks ]
/*-------------------------------------------------*/
if ( $icon_html ) {

	$icon_spacing = ! empty( $atts['icon_spacing'] ) ? absint( $atts['icon_spacing'] ) : 20;

	// Icon style
	$icon_style_args = [
		'font_size'     => $icon_size,
		'border_radius' => $icon_border_radius,
		'color'         => $icon_color ?: $color,
		'background'    => $icon_bg,
		'height'        => $icon_height,
		'width'         => $icon_width,
	];

	if ( ! empty( $atts['icon_padding'] ) && empty( $atts['icon_height'] ) && empty( $atts['icon_width'] ) ) {
		$icon_style_args['padding'] = $atts['icon_padding'];
	}

	$icon_style = vcex_inline_style( $icon_style_args );

	// Inner border style.
	$inner_border_style = [];

	switch ( $style ) {
		case 'zig-zag':
			if ( $height ) {
				$inner_border_style['height'] = $height;
			}
			if ( ! empty( $image_mask ) ) {
				$inner_border_style['--wpex-mask-image'] = "url('data:image/svg+xml;utf-8,{$image_mask}')";
				$inner_border_style['-webkit-mask-image'] = 'var(--wpex-mask-image)';
				$inner_border_style['mask-image'] = 'var(--wpex-mask-image)';
				$inner_border_style['background-color'] = ! empty( $color ) ? $color : 'currentColor';
			}
			break;
		case 'dotted':
			if ( $height ) {
				$inner_border_style['height'] = $height;
			}
			break;
		default:
			if ( $color ) {
				$inner_border_style['border_color'] = $color;
			}
			if ( $height ) {
				$inner_border_style['border_bottom_width'] = $height;
				if ( 'double' === $style ) {
					$inner_border_style['border_top_width'] = $height;
				}
			}
			break;
	}

	$inner_border_style = $inner_border_style ? vcex_inline_style( $inner_border_style ) : '';

	// Inner border class.
	$inner_border_class = [
		'vcex-divider-border',
		'wpex-flex-grow',
	];

	if ( $util_border ) {
		$inner_border_class[] = $util_border;
	}

	if ( $util_border_style ) {
		$inner_border_class[] = $util_border_style;
	}

	if ( $util_border_color ) {
		$inner_border_class[] = $util_border_color;
	}

	if ( $util_inner_padding ) {
		$inner_border_class[] = $util_inner_padding;
	}

	switch ( $style ) {
		case 'double':
			$inner_border_class[] = 'wpex-pb-5';
			break;
	}

	// Reset vars if icon is defined so styles aren't duplicated in main wrapper - important!!!
	$height = $color = '';

}

/*-------------------------------------------------*/
/* [ Inline Wrap Style ]
/*-------------------------------------------------*/
$wrap_style = [
	'width'              => $atts['width'] ?? '',
	'margin'             => $atts['margin'] ?? '',
	'animation_delay'    => $atts['animation_delay'] ?? '',
	'animation_duration' => $atts['animation_duration'] ?? '',
];

switch ( $style ) {
	case 'zig-zag':
		if ( $color ) {
			$wrap_style['color'] = $color;
		}
		if ( $height ) {
			$wrap_style['min_height'] = $height;
		}
		break;
	case 'dotted':
		if ( $height ) {
			$wrap_style['min_height'] = $height; // use min-height to prevent issues with icon if taller then divider.
		}
		break;
	default:
		if ( 'double' === $style ) {
			$wrap_style['border_top_width'] = $height;
		}
		if ( $height ) {
			$wrap_style['border_bottom_width'] = $height;
		}
		if ( $color ) {
			$wrap_style['border_color'] = $color;
		}
		break;
}

// Add mask image.
if ( ! $icon_html && ! empty( $image_mask ) ) {
	$wrap_style['--wpex-mask-image'] = "url('data:image/svg+xml;utf-8,{$image_mask}')";
	$wrap_style['-webkit-mask-image'] = 'var(--wpex-mask-image)';
	$wrap_style['mask-image'] = 'var(--wpex-mask-image)';
	$wrap_style['background-color'] = 'currentColor';
}

$wrap_style = vcex_inline_style( $wrap_style );

/*-------------------------------------------------*/
/* [ Divider Output Starts Here ]
/*-------------------------------------------------*/
$output .= '<div class="' . esc_attr( $wrap_class ) . '"' . $wrap_style . '>';

	/*-------------------------------------------------*/
	/* [ Display icon if set ]
	/*-------------------------------------------------*/
	if ( $icon_html ) {

		// Icon before span (left border when icon is set).
		$output .= '<div class="' . esc_attr( implode( ' ', $inner_border_class ) ) . '"' . $inner_border_style . '></div>';

		// Icon output.
		$icon_inner_class = [
			'vcex-divider-icon-span',
			'wpex-inline-flex',
			'wpex-justify-center',
			'wpex-items-center',
			'wpex-box-content',
			'wpex-inline-block',
			'wpex-text-center',
			'wpex-text-lg',
		];

		if ( $icon_bg ) {
			$icon_inner_class[] = 'wpex-mx-' . sanitize_html_class( $icon_spacing );
		}

		if ( ! $icon_height ) {
			$icon_inner_class[] = 'wpex-py-10';
		}

		if ( ! $icon_width ) {
			$icon_inner_class[] = 'wpex-px-' . sanitize_html_class( $icon_spacing );
		}

		$output .= '<span class="' . esc_attr( implode( ' ', $icon_inner_class ) ) . '"' . $icon_style . '>';

			$output .= $icon_html;

		$output .= '</span>';

		// Icon after span (right border when icon is set).
		$output .= '<div class="' . esc_attr( implode( ' ', $inner_border_class ) ) . '"' . $inner_border_style . '></div>';

	}

// Close main wrapper.
$output .= '</div>';

// @codingStandardsIgnoreLine.
echo $output;
