<?php

/**
 * vcex_heading shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------*/
/* [ Get Heading Text ]
/*-------------------------------------------------*/
if ( ! empty( $atts['source'] ) && 'custom' !== $atts['source'] ) {
	$text = vcex_get_source_value( $atts['source'], $atts );
} elseif ( isset( $atts['text'] ) && is_string( $atts['text'] ) ) {
	$text = trim( $atts['text'] );
}

$text_safe = vcex_parse_text_safe( (string) $text );
$text_safe = (string) apply_filters( 'vcex_heading_text', $text_safe );

if ( empty( $text_safe ) ) {
	return;
}

/*-------------------------------------------------*/
/* [ Define main variables ]
/*-------------------------------------------------*/
$output = '';
$style = ! empty( $atts['style'] ) ? sanitize_text_field( $atts['style'] ) : 'plain';
$default_tag = ( $default_tag = get_theme_mod( 'vcex_heading_default_tag', 'div' ) ) ?: 'div';
$tag = ! empty( $atts['tag'] ) ? $atts['tag'] : $default_tag;
$tag = apply_filters( 'vcex_heading_default_tag', $tag );
$tag_escaped = tag_escape( $tag );
$custom_css = vcex_vc_shortcode_custom_css_class( $atts['css'] ?? '' );
$icon = vcex_get_icon_html( $atts, 'icon' );
$default_border_width = ( 'side-border' === $style ) ? 3 : 1;
$border_width = absint( ! empty( $atts['border_width'] ) ? $atts['border_width'] : $default_border_width );
$border_style = ! empty( $atts['border_style'] ) ? $atts['border_style'] : 'solid';
$inline_display = 'inline-block';
$add_css_to_inner = $atts['add_css_to_inner'] ?? false;
$text_align = ! empty( $atts['text_align'] ) ? $atts['text_align'] : '';
$badge_safe = ! empty( $atts['badge'] ) ? vcex_parse_text_safe( $atts['badge'] ) : '';

if ( 'plain' === $style || 'side-border' === $style || 'bottom-border' === $style ) {
	$add_css_to_inner = vcex_validate_boolean( $add_css_to_inner );
} else {
	$add_css_to_inner = false;
}

/*-------------------------------------------------*/
/* [ Parse Link ]
/*-------------------------------------------------*/
$onclick_attrs = vcex_get_shortcode_onclick_attributes( $atts, 'vcex_heading' );
$link          = $onclick_attrs['href'] ?? '';
$has_link      = (bool) $link;

/*-------------------------------------------------*/
/* [ Parse Icon ]
/*-------------------------------------------------*/
if ( $icon || ! empty( $atts['icon_alternative_character'] ) ) {
	$icon_position = ! empty( $atts['icon_position'] ) ? sanitize_text_field( $atts['icon_position'] ) : 'left';
	$icon_margin = ! empty( $atts['icon_side_margin'] ) ? absint( sanitize_text_field( $atts['icon_side_margin'] ) ) : '10';
	$icon_margin_dir = ( $icon_position === 'right' ) ? 'l' : 'r';

	$icon_class = [
		'vcex-heading-icon',
		"vcex-heading-icon-{$icon_position}",
		'vcex-icon-wrap',
		"wpex-m{$icon_margin_dir}-{$icon_margin}",
	];

	if ( ! empty( $atts['icon_alternative_character'] ) ) {
		$icon = vcex_parse_text_safe( $atts['icon_alternative_character'] );
	}

	$icon_output = '<span class="' . implode( ' ', $icon_class ) . '">' . $icon . '</span>';

	if ( 'left' === $icon_position ) {
		$icon_left_escaped = $icon_output;
	} else {
		$icon_right_escaped = $icon_output;
	}

}

/*-------------------------------------------------*/
/* [ Heading Classes ]
/*-------------------------------------------------*/
$wrap_class = [
	'vcex-heading',
	"vcex-heading-{$style}",
];

if ( $badge_safe ) {
	$wrap_class[] = 'vcex-heading-w-badge';
}

$wrap_class[] = 'vcex-module';
//$wrap_class[] = 'wpex-font-normal'; // deprecated v1.8

if ( vcex_validate_att_boolean( 'text_balance', $atts ) ) {
	$wrap_class[] = 'wpex-text-balance';
}

if ( ! empty( $atts['typography_style'] ) ) {
	$wrap_class[] = vcex_parse_typography_style_class( $atts['typography_style'] );
	if ( 'span' === $tag_escaped ) {
		$wrap_class[] = 'wpex-block';
	}
} else {
	$wrap_class[] = 'wpex-heading'; // !!! important !!!
	$wrap_class[] = 'wpex-text-2xl';

	// Customizer setting that inserts the customizer styles automatically into the heading.
	// This setting is used only for customizer styles so we still need to add "wpex-heading"
	if ( wp_validate_boolean( get_theme_mod( 'vcex_heading_typography_tag_styles', false ) )
		&& in_array( $tag_escaped, [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', true ] )
	) {
		$wrap_class[] = "wpex-{$tag_escaped}";
	}
}

if ( 'side-border' === $style ) {
	$wrap_class[] = 'wpex-flex';
	$wrap_class[] = 'wpex-items-center';
}

if ( ! $add_css_to_inner ) {
	if ( 'plain' === $style ) {
		if ( ! empty( $atts['padding_x'] ) ) {
			$wrap_class[] = vcex_parse_padding_class( $atts['padding_x'], 'inline' );
		}
		if ( ! empty( $atts['padding_y'] ) ) {
			$wrap_class[] = vcex_parse_padding_class( $atts['padding_y'], 'block' );
		}
		if ( ! empty( $atts['border_radius'] ) ) {
			$wrap_class[] = vcex_parse_border_radius_class( $atts['border_radius'] );
		}
	}
	if ( ! empty( $atts['shadow'] ) ) {
		$wrap_class[] = vcex_parse_shadow_class( $atts['shadow'] );
	}
}

if ( ! empty( $atts['top_margin'] ) && empty( $atts['typography_style'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['top_margin'], 'top' );
}

if ( ! empty( $atts['bottom_margin'] ) && empty( $atts['typography_style'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( $text_align ) {
	$wrap_class[] = vcex_parse_text_align_class( $text_align );
}

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$align = ! empty( $atts['align'] ) ? $atts['align'] : 'center';
	if ( vcex_validate_att_boolean( 'float', $atts ) ) {
		$wrap_class[] = "wpex-float-{$align}";
	} else {
		$wrap_class[] = vcex_parse_align_class( $align );
	}
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( vcex_validate_att_boolean( 'italic', $atts ) ) {
	$wrap_class[] = 'wpex-italic';
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( $custom_css && ! $add_css_to_inner ) {
	$wrap_class[] = $custom_css;
}

// Auto Responsive Text (min-max)
if ( ! empty( $atts['responsive_text_min_max'] ) || vcex_validate_att_boolean( 'responsive_text', $atts ) ) {
	$min_max = $atts['responsive_text_min_max'] ?? '';
	if ( $min_max && is_string( $min_max ) ) {
		$min_max = explode( '|', $min_max );
	}
	$max_font_size = $min_max[1] ?? $font_size ?? null;
	$min_font_size = $min_max[0] ?? $min_font_size ?? null;
	if ( $max_font_size && $min_font_size ) {
		$max_font_size = vcex_parse_min_max_text_font_size( $max_font_size );
		$min_font_size = vcex_parse_min_max_text_font_size( $min_font_size );
		if ( $max_font_size && $min_font_size ) {
			$inline_display = 'block';
			$min_font_size = apply_filters( 'wpex_vcex_heading_min_font_size', $min_font_size );
			if ( empty( $atts['font_size'] ) ) {
				$atts['font_size'] = $max_font_size; // set default font size to the max size.
			}
			wp_enqueue_script( 'vcex-responsive-text' );
			$has_min_max_font_size = true;
		}
	}
}

/*-------------------------------------------------*/
/* [ Tweak classes based on heading style ]
/*-------------------------------------------------*/
switch ( $style ) {
	case 'graphical':
		break;
	case 'bottom-border-w-color':
		$wrap_class[] = 'wpex-border-b-2';
		$wrap_class[] = 'wpex-border-solid';
		$wrap_class[] = 'wpex-border-main';
		break;
	case 'bottom-border':
		if ( $border_width > 1 ) {
			$wrap_class[] = 'wpex-border-b-' . sanitize_html_class( $border_width );
		} else {
			$wrap_class[] = 'wpex-border-b';
		}
		$wrap_class[] = 'wpex-border-' . sanitize_html_class( $border_style  );
		$wrap_class[] = 'wpex-border-main';
		break;
	case 'side-border':
		if ( 'right' === $text_align ) {
			$wrap_class[] = 'wpex-flex-row-reverse';
		}
		break;
}

/*-------------------------------------------------*/
/* [ Hover Data Attributes ]
/*-------------------------------------------------*/
if ( ! empty( $atts['background_hover'] ) ) {
	$wrap_class[] = 'transition-all';
}

/*-------------------------------------------------*/
/* [ Parse Heading Attributes ]
/*-------------------------------------------------*/

// Add custom classes last.
if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'] );
}

// Turn wrap classes into string and apply filter.
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_heading', $atts );

/*-------------------------------------------------*/
/* [ Parse HTMl for side border ]
/*-------------------------------------------------*/
if ( 'side-border' === $style ) {

	$side_border_classes = [
		'vcex-heading-side-border__border',
		'wpex-flex-grow',
		'wpex-h-0',
		'wpex-border-' . sanitize_html_class( $border_style ),
	];

	if ( empty( $atts['color'] ) ) {
		$side_border_classes[] = 'wpex-border-gray-900';
	}

	$border_side_margin_safe = ! empty( $atts['border_side_margin'] ) ? absint( $atts['border_side_margin'] ) : 15;

	switch ( $text_align ) {
		case 'right':
			$side_border_classes['margin'] = "wpex-mr-{$border_side_margin_safe}";
			break;
		default:
			$side_border_classes['margin'] = "wpex-ml-{$border_side_margin_safe}";
			break;
	}

	if ( $border_width > 1 ) {
		$side_border_classes[] = 'wpex-border-b-' . sanitize_html_class( $border_width );
	} else {
		$side_border_classes[] = 'wpex-border-b';
	}

	$side_border_classes = apply_filters( 'vcex_heading_side_border_class', $side_border_classes, $atts );

	// Default side border.
	$side_border_out = '<span class="' . esc_attr( implode( ' ', $side_border_classes ) ) . '"></span>';

	// Left side border for center text_align.
	if ( 'center' === $text_align ) {
		unset( $side_border_classes['margin'] );
		$side_border_classes['margin'] = "wpex-mr-{$border_side_margin_safe}";
		$side_border_left_out = '<span class="' . esc_attr( implode( ' ', $side_border_classes ) ) . '"></span>';
	}

}

/*-------------------------------------------------*/
/* [ Heading Output Starts here ]
/*-------------------------------------------------*/
$output .= '<' . $tag_escaped . ' class="' . esc_attr( $wrap_class ) . '">';

	// Extra side border for center text_align
	if ( ! empty( $side_border_left_out ) ) {
		$output .= $side_border_left_out;
	}

	/*-------------------------------------------------*/
	/* [ Open Link Element ]
	/*-------------------------------------------------*/
	if ( $has_link ) {
		$link_attrs = $onclick_attrs;

		$link_classes = [
			'wpex-no-underline',
		];

		if ( ! empty( $atts['color'] ) || ! empty( $atts['color_hover'] ) ) {
			// used to override --wpex-hover-heading-link-color
			$link_classes[] = 'wpex-inherit-color-important';
		}

		if ( ! empty( $atts['background_hover'] ) && ! $add_css_to_inner ) {
			$link_classes[] = 'wpex-block';
		}

		if ( isset( $link_attrs['class'] ) ) {
			$link_attrs['class'] = array_merge( $link_attrs['class'], $link_classes );
		} else {
			$link_attrs['class'] = $link_classes;
		}

		$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';
	}

	/*-------------------------------------------------*/
	/* [ Inner Span ]
	/*-------------------------------------------------*/
	$inner_class = [
		'vcex-heading-inner',
		"wpex-{$inline_display}",
	];

	switch ( $style ) {
		case 'bottom-border-w-color':
			$inner_class[] = 'wpex-relative';
			$inner_class[] = 'wpex-pb-5';
			$inner_class[] = 'wpex-border-b-2';
			$inner_class[] = 'wpex-border-solid';
			$inner_class[] = 'wpex-border-accent';
			break;
	}

	if ( $add_css_to_inner ) {
		if ( 'plain' === $style ) {
			if ( ! empty( $atts['padding_x'] ) ) {
				$inner_class[] = vcex_parse_padding_class( $atts['padding_x'], 'inline' );
			}
			if ( ! empty( $atts['padding_y'] ) ) {
				$inner_class[] = vcex_parse_padding_class( $atts['padding_y'], 'block' );
			}
			if ( ! empty( $atts['border_radius'] ) ) {
				$inner_class[] = vcex_parse_border_radius_class( $atts['border_radius'] );
			}
		}
		if ( ! empty( $atts['shadow'] ) ) {
			$inner_class[] = vcex_parse_shadow_class( $atts['shadow'] );
		}
		if ( $custom_css ) {
			$inner_class[] = $custom_css;
		}
	}

	$output .= '<span class="' . esc_attr( implode( ' ', array_filter( $inner_class ) ) ) . '">';

		// Open min max font size wrapper
		if ( isset( $has_min_max_font_size ) && true === $has_min_max_font_size ) {
			$output .= '<span class="wpex-responsive-txt" data-max-font-size="' . absint( $max_font_size ) . '" data-data-min-font-size="' . absint( $min_font_size ) .'">';
		}

		// Left Icon
		if ( ! empty( $icon_left_escaped ) ) {
			$output .= $icon_left_escaped;
		}

		// The heading Text
		$output .= $text_safe;

		// Right Icon
		if ( ! empty( $icon_right_escaped ) ) {
			$output .= $icon_right_escaped;
		}

		// Badge
		if ( $badge_safe ) {
			$output .= ' <span class="vcex-heading-badge wpex-badge">' . $badge_safe . '</span>';
		}

		if ( isset( $has_min_max_font_size ) && true === $has_min_max_font_size ) {
			$output .= '</span>';
		}

	// Close inner
	$output .= '</span>';

	if ( $has_link ) {
		$output .= '</a>';
	}

	// Side border for left/right text_align
	if ( ! empty( $side_border_out ) ) {
		$output .= $side_border_out;
	}

$output .= '</' . $tag_escaped . '>';

// @codingStandardsIgnoreLine
echo $output;
