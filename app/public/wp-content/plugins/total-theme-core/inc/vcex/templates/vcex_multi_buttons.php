<?php

/**
 * vcex_multi_buttons shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.8
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $atts['buttons'] ) ) {
	return;
}

$buttons = (array) vcex_vc_param_group_parse_atts( $atts['buttons'] );

if ( ! $buttons || ! is_array( $buttons ) ) {
	return;
}

$output = '';
$buttons_count = count( $buttons );

$shortcode_class = [
	'vcex-multi-buttons',
	'wpex-flex',
	'wpex-flex-wrap',
	'wpex-items-center',
	'wpex-gap-10',
];

if ( ! empty( $atts['align'] ) ) {
	$shortcode_class[] = vcex_parse_justify_content_class( $atts['align'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$shortcode_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['visibility'] ) ) {
	$shortcode_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['el_class'] ) ) {
	$shortcode_class[] = vcex_get_extra_class( $atts['el_class'] );
}

if ( vcex_validate_att_boolean( 'small_screen_full_width', $atts ) ) {
	$shortcode_class[] = 'vcex-small-screen-full-width';
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_multi_buttons', $atts );

$wrap_attrs = [
	'class' => $shortcode_class,
];

// Define output.
$output .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	$count = 0;
	foreach ( $buttons as $button ) {
		$count++;

		// Button url is required.
		if ( ! isset( $button['link'] ) ) {
			continue;
		}

		// Get link data.
		$link_data = vcex_build_link( $button['link'] );

		// Link is required.
		if ( ! isset( $link_data['url'] ) ) {
			continue;
		}

		// Sanitize text.
		$text = $button['text'] ?? esc_html__( 'Button', 'total-theme-core' );

		// Get button style.
		$style        = $button['style'] ?? '';
		$color        = $button['color'] ?? '';
		$custom_color = isset( $button['custom_color'] ) ? vcex_parse_color( $button['custom_color'] ) : '';
		$hover_color  = isset( $button['custom_color_hover'] ) ? vcex_parse_color( $button['custom_color_hover'] ) : '';

		// Fallback from original release to include only styles that make sense!
		if ( 'minimal-border' === $style ) {
			$style = 'outline';
		} elseif ( 'three-d' === $style || 'graphical' === $style ) {
			$style = 'flat';
		} elseif ( 'clean' === $style ) {
			$style = 'flat';
			$color = 'white';
		}

		// Button css.
		$button_css_args = [
			'animation_delay'    => $button['animation_delay'] ?? '',
			'animation_duration' => $button['animation_duration'] ?? '',
		];

		if ( $custom_color ) {
			switch ( $style ) {
				case 'plain-text';
					$button_css_args['color'] = $custom_color;
					break;
				case 'flat':
				case 'graphical':
				case 'three-d':
					$button_css_args['background'] = $custom_color;
					break;
				case 'minimal-border';
					$button_css_args['color'] = $custom_color;
					$button_css_args['border-color'] = $custom_color;
				case 'outline';
					$button_css_args['--wpex-accent'] = $custom_color;
					break;
			}
		}

		$button_css = (string) vcex_inline_style( $button_css_args, false );

		// Define button classes.
		$button_classes = vcex_get_button_classes( $style, $color );
		if ( ! str_contains( $button_classes, 'outline' ) ) {
			$button_classes .= ' outline-transparent';
		}

		if ( isset( $button['local_scroll'] ) && 'true' == $button['local_scroll'] ) {
			$button_classes .= ' local-scroll-link';
		}

		// Alignment fix (mostly used when adding custom width).
		$button_classes .= ' wpex-text-center';

		// Add animation to button classes.
		if ( isset( $button['css_animation'] ) && 'none' !== $button['css_animation'] ) {
			$button_classes .= ' ' . vcex_get_css_animation( $button['css_animation'] );
		}

		// Add counter to button class => Useful for custom styling purposes.
		$button_classes .= ' vcex-count-' . sanitize_html_class( $count );

		// Define button attributes.
		$attrs = [
			'href'   => esc_url( do_shortcode( $link_data['url'] ) ),
			'title'  => isset( $link_data['title'] ) ? do_shortcode( $link_data['title'] ) : '',
			'class'  => $button_classes,
			'target' => $link_data['target'] ?? '',
			'rel'    => $link_data['rel'] ?? '',
			'style'  => $button_css,
		];

		// Hover data/class.
		if ( $custom_color || $hover_color ) {
			$hover_data = [];
			if ( $hover_color ) {
				if ( 'plain-text' === $style ) {
					$hover_data['color'] = esc_attr( $hover_color );
				} else {
					$hover_data['background'] = esc_attr( $hover_color );
				}
			}
			if ( $hover_data ) {
				$attrs['data-wpex-hover'] = htmlspecialchars( wp_json_encode( $hover_data ) );
			}
		}

		// Download attribute.
		if ( isset( $button['download_attribute'] ) && 'true' == $button['download_attribute'] ) {
			$attrs['download'] = 'download';
		}

		// Button html output.
		$output .= '<a' . vcex_parse_html_attributes( $attrs ) . '>';
			$output .= vcex_parse_text_safe( $text );
		$output .= '</a>';

	}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
