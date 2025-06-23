<?php

/**
 * vcex_callout shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Checks & sanitization.
$style       = ! empty( $atts['style'] ) ? $atts['style'] : 'boxed';
$layout      = ! empty( $atts['layout'] ) ? $atts['layout'] : '75-25';
$is_full     = '100-100' === $layout;
$has_button  = ( $atts['button_url'] && $atts['button_text'] ) ? true : false;
$breakpoint  = ( $atts['breakpoint'] && ! $is_full ) ? $atts['breakpoint'] : 'md';
$legacy_typo = vcex_has_classic_styles();

// Get layout.
if ( 'auto' === $layout ) {
	$content_width = 'auto';
	$button_width  = 'auto';
} elseif ( in_array( $layout, [ '75-25', '60-40', '50-50', '80-20', '100-100' ] ) ) {
	$layout = explode( '-', $layout );
	$content_width = $layout[0];
	$button_width  = $layout[1];
} else {
	$content_width = '75';
	$button_width  = '25';
}

// Shortcode classes.
$wrap_class = [
	'vcex-callout',
	'vcex-module',
];

if ( $style && 'none' !== $style ) {
	$wrap_class[] = sanitize_html_class( "wpex-{$style}" );
}

if ( $is_full ) {
	$wrap_class[] = 'wpex-text-center';
}

if ( ! empty( $atts['shadow'] ) && empty( $atts['padding_all'] ) && 'none' === $style ) {
	$wrap_class[] = 'wpex-p-20';
}

if ( $has_button ) {
	$wrap_class[] = 'with-button';
	if ( ! $is_full ) {
		$wrap_class[] = 'wpex-text-center';
		$wrap_class[] = sanitize_html_class( "wpex-{$breakpoint}-text-initial" );
		$wrap_class[] = sanitize_html_class( "wpex-{$breakpoint}-flex" );
		$wrap_class[] = sanitize_html_class( "wpex-{$breakpoint}-items-center" );
	}
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_callout' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_callout', $atts );

$output = '<div class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $atts ) . '>';

	// Display content.
	if ( $content ) {

		$caption_class = [
			'vcex-callout-caption',
			'wpex-last-mb-0',
		];

		if ( $legacy_typo ) {
			$caption_class[] = 'wpex-text-md';
		} else {
			$caption_class[] = 'wpex-text-xl';
		}

		if ( $has_button ) {
			$caption_class[] = 'wpex-mb-20';
			if ( ! $is_full ) {
				$caption_class[] = sanitize_html_class( "wpex-{$breakpoint}-w-{$content_width}" );
				$caption_class[] = sanitize_html_class( "wpex-{$breakpoint}-pr-20" );
				$caption_class[] = sanitize_html_class( "wpex-{$breakpoint}-mb-0" );
			}
		}

		$output .= '<div class="' . esc_attr( implode( ' ', $caption_class ) ) . '">';
			$output .= vcex_the_content( $content );
		$output .= '</div>';

	}

	// Display button.
	if ( $has_button ) {

		$button_wrap_classes = [
			'vcex-callout-button',
		];

		if ( 'auto' === $layout ) {
			$button_wrap_classes[] = 'wpex-flex-shrink-0';
		}

		if ( $is_full ) {
			$button_align = $atts['button_align'] ?: 'center';
			$button_wrap_classes[] = sanitize_html_class( "wpex-text-{$button_align}" );
		} else {
			$button_align = $atts['button_align'] ?: 'right';
			$button_wrap_classes[] = sanitize_html_class( "wpex-{$breakpoint}-w-{$button_width}" );
			$button_wrap_classes[] = sanitize_html_class( "wpex-{$breakpoint}-text-{$button_align}" );
		}

		$output .= '<div class="' . esc_attr( implode( ' ', $button_wrap_classes ) ) . '">';

			$button_attrs = [
				'href'   => vcex_parse_text( $atts['button_url'] ),
				'title'  => vcex_parse_text( $atts['button_text'] ),
				'target' => $atts['button_target'],
				'rel'    => $atts['button_rel'],
				'class'  => [
					'vcex-callout-link',
					vcex_get_button_classes( $atts['button_style'], $atts['button_color'] )
				],
			];

			if ( 'local' === $atts['button_target'] ) {
				$button_attrs['class'][] = 'local-scroll-link';
			}

			if ( 'true' == $atts['button_full_width'] ) {
				$button_attrs['class'][] = 'full-width';
			}

			$button_attrs['class'][] = 'wpex-text-center';

			if ( $legacy_typo ) {
				$button_attrs['class'][] = 'wpex-text-base';
			}

			$output .= '<a' . vcex_parse_html_attributes( $button_attrs ) . '>';

				if ( $icon_left_safe = vcex_get_icon_html( $atts, 'button_icon_left', 'theme-button-icon-left' ) ) {
					$output .= $icon_left_safe;
				}

				$output .= vcex_parse_text_safe( $atts['button_text'] );

				if ( $icon_right_safe = vcex_get_icon_html( $atts, 'button_icon_right', 'theme-button-icon-right' ) ) {
					$output .= $icon_right_safe;
				}

			$output .= '</a>';

		$output .= '</div>';

	}

$output .= '</div>';

// @codingStandardsIgnoreLine.
echo $output;
