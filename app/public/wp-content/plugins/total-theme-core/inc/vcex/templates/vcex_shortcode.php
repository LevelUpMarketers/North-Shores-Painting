<?php

/**
 * vcex_shortcode shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.8
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $content ) || ! is_string( $content ) ) {
	return;
}

$shortcode_class = [
	'vcex-shortcode',
	'wpex-clr',
];

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_shortcode' );

if ( $extra_classes ) {
	$shortcode_class = array_merge( $shortcode_class, $extra_classes );
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_shortcode', $atts );

if ( function_exists( 'totaltheme_replace_vars' ) ) {
	$content = totaltheme_replace_vars( $content );
}

if ( ! vcex_vc_is_inline() || str_starts_with( trim( $content ), '[' ) ) {
	$content = do_shortcode( wp_kses_post( $content ) );
}

echo '<div class="' . esc_attr( $shortcode_class ) . '"' . vcex_get_unique_id( $atts ) . '>' . $content . '</div>';
