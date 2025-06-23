<?php
/**
 * vcex_icon shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

$output = $data_attributes = '';
$is_custom_icon = false;

if ( ! empty( $atts['icon_alternative_classes'] ) || ! empty( $atts['icon_alternative_character'] ) ) {
	$is_custom_icon = true;
}

if ( ! $is_custom_icon ) {
	$icon = vcex_get_icon_html( $atts );
	if ( ! $icon ) {
		return;
	}
}

// Shortcode class.
$shortcode_class = [
	'vcex-icon',
	'vcex-module',
];

if ( ! empty( $atts['style'] ) && $style_safe = sanitize_html_class( $atts['style'] ) ) {
	$shortcode_class[] = "vcex-icon-{$style_safe}"; // deprecated param
}

if ( ! empty( $atts['size'] ) && $size_safe = sanitize_html_class( $atts['size'] ) ) {
	$shortcode_class[] = "vcex-icon-{$size_safe}";
}

if ( ! empty( $atts['float'] ) ) {
	$float = sanitize_text_field( $atts['float'] );
	if ( ! vcex_is_bidirectional() ) {
		$float = vcex_parse_direction( $float );
	}
	switch ( $float ) {
		case 'left':
			$shortcode_class[] = 'wpex-float-left';
			$shortcode_class[] = 'wpex-mr-20';
			break;
		case 'center':
			$shortcode_class[] = 'wpex-float-none';
			$shortcode_class[] = 'wpex-m-auto';
			if ( empty( $align ) ) {
				$shortcode_class[] = 'wpex-text-center';
			}
			break;
		case 'right':
			$shortcode_class[] = 'wpex-float-right';
			$shortcode_class[] = 'wpex-ml-20';
			break;
	}
} elseif ( ! empty( $atts['align'] ) && $align_safe = sanitize_html_class( $atts['align'] ) ) {
	$shortcode_class[] = "wpex-text-{$align_safe}";
}

if ( ! empty( $atts['css_animation'] ) ) {
	$shortcode_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$shortcode_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['el_class'] ) ) {
	$shortcode_class[] = vcex_get_extra_class( $atts['el_class'] );
}

// Parse shortcode classes.
$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_icon', $atts );

// Begin shortcode output.
$output .= '<div class="' . esc_attr( trim( $shortcode_class ) ) . '"' . vcex_get_unique_id( $atts['unique_id'] ) . '>';

	// Link wrap.
	$onclick_attrs = vcex_get_shortcode_onclick_attributes( $atts, 'vcex_icon' );
	if ( ! empty( $onclick_attrs['href'] ) ) {
		$link_class = [
			'vcex-icon-link',
			'wpex-no-underline',
		];
		if ( ! empty( $atts['color'] ) ) {
			$link_class[] = 'wpex-text-current wpex-hover-text-current';
		}
		if ( ! empty( $atts['aria_label'] ) ) {
			$onclick_attrs['aria-label'] = vcex_parse_text_safe( $atts['aria_label'] );
		}
		if ( ! empty( $atts['onclick'] ) && 'cart_toggle' === $atts['onclick'] && vcex_validate_att_boolean( 'cart_badge', $atts, true ) ) {
			$has_cart_badge = true;
			$link_class[] = 'wpex-relative wpex-inline-block';
		}
		$onclick_attrs['class'] = array_merge( $link_class, $onclick_attrs['class'] );
		$output .= '<a' . vcex_parse_html_attributes( $onclick_attrs ) . '>';
	}

	// Icon classes.
	$icon_class = [
		'vcex-icon-wrap',
		'wpex-inline-flex',
		'wpex-items-center',
		'wpex-justify-center',
		'wpex-leading-none', // keep since it was always here, but not really needed.
	];

	if ( ! empty( $atts['background'] ) && ( empty( $atts['height'] ) && empty( $atts['width'] ) ) ) {
		$icon_class[] = 'wpex-p-20';
	}

	if ( ! empty( $atts['hover_animation'] ) ) {
		$icon_class[] = vcex_hover_animation_class( $atts['hover_animation'] );
	}

	if ( ! empty( $atts['border'] ) ) {
		$icon_class[] = 'wpex-box-content'; // prevent issues when adding borders to icons.
	}

	if ( empty( $atts['hover_animation'] )
		&& ( ! empty( $atts['background_hover'] ) || ! empty( $atts['color_hover'] ) )
		&& vcex_has_classic_styles()
	) {
		$icon_class[] = 'wpex-transition-colors';
		$icon_class[] = 'wpex-duration-200';
	}

	$output .= '<div class="' . esc_attr( implode( ' ', $icon_class ) ) . '">';

		if ( ! empty( $atts['icon_alternative_classes'] ) ) {
			$output .= '<span class="' . esc_attr( do_shortcode( $atts['icon_alternative_classes'] ) ) . '"></span>';
		} elseif ( ! empty( $atts['icon_alternative_character'] ) ) {
			$output .= '<span>' . vcex_parse_text_safe( $atts['icon_alternative_character'] ) . '</span>';
		} elseif ( isset( $icon ) ) {
			if ( ! empty( $atts['onclick'] ) && 'toggle_element' === $atts['onclick'] && ! empty( $atts['icon_active'] ) ) {
				$output .= '<span data-open-text>' . $icon . '</span>';
				$output .= '<span data-close-text>' . vcex_get_theme_icon_html( $atts['icon_active'] ) . '</span>';
			} else {
				$output .= $icon;
			}
		}

	$output .= '</div>';

	if ( isset( $has_cart_badge ) ) {
		$output .= totalthemecore_call_static(
			'Vcex\WooCommerce',
			'get_cart_badge',
			vcex_validate_att_boolean( 'cart_badge_count', $atts )
		);
	}

	if ( ! empty( $onclick_attrs['href'] ) ) {
		$output .= '</a>';
	}

$output .= '</div>';

if ( isset( $float ) && vcex_vc_is_inline() ) {
	$output .= '<div class="wpex-clear"></div>';
}

// @codingStandardsIgnoreLine.
echo $output;
