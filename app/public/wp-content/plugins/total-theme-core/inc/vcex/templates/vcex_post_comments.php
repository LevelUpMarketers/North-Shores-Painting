<?php
/**
 * vcex_post_comments shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$wrap_class = [
	'vcex-post-comments',
	'vcex-comments',
];

if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( empty( $atts['show_heading'] ) || 'false' == $atts['show_heading'] ) {
	$wrap_class[] = 'vcex-comments-hide-heading';
}

if ( ! empty( $atts['max_width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$wrap_class[] = vcex_parse_align_class( ! empty( $atts['align'] ) ? $atts['align'] : 'center' );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_post_comments', $atts );

$output = '<div class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $atts['unique_id'] ?? null ) . '>';

	if ( vcex_is_template_edit_mode() ) {
		ob_start();
			add_filter( 'comments_open', '__return_true' );
			comment_form();
		$output .= ob_get_clean();
	} else {
		ob_start();
			comments_template();
		$output .= ob_get_clean();
	}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
