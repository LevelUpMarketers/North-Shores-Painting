<?php
/**
 * vcex_page_title shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.7.1
 */

defined( 'ABSPATH' ) || exit;

if ( vcex_is_template_edit_mode() ) {
	$title = esc_html( 'Page Title Placeholder', 'total-theme-core' );
} else {
	$title = vcex_get_the_title();
}

if ( empty( $title ) ) {
	return;
}

$tag_escaped  = ! empty( $atts['html_tag'] ) ? tag_escape( $atts['html_tag'] ) : 'h1';
$link_to_post = vcex_validate_att_boolean( 'link_to_post', $atts );

// Define shortcode classes.
$wrap_class = [
	'vcex-page-title',
	'vcex-module',
];

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$wrap_class[] = vcex_parse_align_class( ! empty( $atts['float'] ) ? $atts['float'] : 'center' );
}

// Custom user classes.
$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_page_title' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

// Filters shortcode classes.
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_page_title', $atts );

// Begin output.
$output = '<div class="' . esc_attr( trim( $wrap_class ) ) . '">';

	if ( $link_to_post ) {
		$output .= '<a href="' . esc_url( get_permalink( vcex_get_the_ID() ) ) . '" class="vcex-page-title__link wpex-no-underline wpex-inherit-color">';
	}

	$heading_classes = [
		'vcex-page-title__heading',
		'wpex-heading',
		'wpex-text-3xl',
	];

	$output .= '<' . $tag_escaped . ' class="' . implode( ' ', $heading_classes ) . '">';

		if ( ! empty( $atts['before_text'] ) ) {
			$output .= '<span class="vcex-page-title__before">' . vcex_parse_text_safe( $atts['before_text'] ) . '</span> ';
		}

		$output .= '<span class="vcex-page-title__text">' . vcex_parse_text_safe( $title ) . '</span>';

		if ( ! empty( $atts['after_text'] ) ) {
			$output .= ' <span class="vcex-page-title__after">' . vcex_parse_text_safe( $atts['after_text'] ) . '</span>';
		}

	$output .= '</' . $tag_escaped . '>';

	if ( $link_to_post ) {
		$output .= '</a>';
	}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
