<?php

/**
 * vcex_spacing shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.8
 */

defined( 'ABSPATH' ) || exit;

// Define vars.
$size = $atts['size'] ?? null;
$is_responsive = vcex_validate_att_boolean( 'responsive', $atts, false );

// Core class.
$classes = [
	'vcex-spacing',
	'wpex-w-100',
	'wpex-clear',
];

// Visiblity Class.
if ( ! empty( $atts['visibility'] ) ) {
    $classes[] = vcex_parse_visibility_class( $atts['visibility'] );
}

// Front-end composer class.
if ( vcex_vc_is_inline() ) {
    $classes[] = 'vc-spacing-shortcode';
}

// Add unique classname.
if ( $is_responsive ) {
	$unique_class = vcex_element_unique_classname();
	$classes[]    = sanitize_html_class( $unique_class );
}

// Custom Class.
if ( ! empty( $atts['class']  ) ) {
    $classes[] = vcex_get_extra_class( $atts['class'] );
}

// Generate inline css.
$inline_style = '';

if ( $is_responsive && ! empty( $atts['size_responsive'] ) ) { ?>
	<style><?php
		echo vcex_responsive_attribute_css( $atts['size_responsive'], $unique_class, 'height' );
	?></style>
<?php } elseif ( $size ) {

	// Sanitize size.
	$size = trim( $size );
	if ( is_numeric( $size ) || str_ends_with( $size, 'px' ) ) {
		$size = floatval( $size ) . 'px';
	} elseif ( str_ends_with( $size, '%' )
		|| str_ends_with( $size, 'em' )
		|| str_ends_with( $size, 'rem' )
		|| str_ends_with( $size, 'vw' )
		|| str_ends_with( $size, 'vh' )
		|| str_ends_with( $size, 'vmin' )
		|| str_ends_with( $size, 'vmax' )
		|| str_starts_with( $size, 'var' )
		|| str_starts_with( $size, 'calc' )
		|| str_starts_with( $size, 'clamp' )
		|| str_starts_with( $size, 'min' )
		|| str_starts_with( $size, 'max' )
	) {
		$size = strip_tags( $size );
	} elseif ( $size = floatval( $size ) ) {
		$size = "{$size}px";
	}

	$inline_style = ' style="height:' . esc_attr( trim( $size ) ) . ';"';
}

// Parse classes.
$classes = vcex_parse_shortcode_classes( $classes, 'vcex_spacing', $atts );

// Echo output.
echo '<div class="' . esc_attr( $classes ) . '"' . $inline_style . '></div>';
