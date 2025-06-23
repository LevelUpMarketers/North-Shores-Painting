<?php

/**
 * vcex_breadcrumbs shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0.2
 */

defined( 'ABSPATH' ) || exit;

// Define main vars
$crumbs    = '';
$is_custom = false;

// Separator type
if ( ! empty( $atts['separator'] ) ) {
	switch ( $atts['separator'] ) {
		case 'dash':
			$separator = '&ndash;';
		break;
		case 'long_dash':
			$separator = '&mdash;';
		break;
		case 'dot':
			$separator = '&middot;';
		break;
		case 'arrow':
			$separator = is_rtl() ? '&larr;' : '&rarr;';
		break;
		case 'double_arrow':
			$separator = is_rtl() ? '&laquo;' : '&raquo;';
		break;
		case 'forward_slash':
			$separator = '&sol;';
		break;
		case 'backslash':
			$separator = '&bsol;';
		break;
		case 'pipe':
			$separator = '&vert;';
		break;
		case 'angle':
			$icon = is_rtl() ? 'angle-left' : 'angle-right';
			$separator = do_shortcode( '[ticon icon="' . $icon . '"]' );
		break;
	}
}

// Sample crumbs for template edit mode
if ( vcex_is_template_edit_mode() ) {
	$separator = ! empty( $separator ) ? $separator : '&raquo';
	$crumbs = '<span class="breadcrumb-trail"><span class="trail-begin"><a href="#"><span>' . esc_html__( 'Home', 'total-theme-core' ) . '</span></a></span><span class="sep"> ' . $separator . ' </span><span><span><a href="#"><span>' . esc_html__( 'Category', 'total-theme-core' ) . '</span></a></span></span></span>';
	$trail_end = ! empty( $atts['show_trail_end'] ) ? vcex_validate_boolean( $atts['show_trail_end'] ) : get_theme_mod( 'breadcrumbs_show_trail_end' );
	if ( $trail_end ) {
		$crumbs .= '<span class="sep"> ' . $separator . ' </span><span class="trail-end">' . esc_html__( 'Post Title', 'total-theme-core' ) . '</span>';
	}
} else {

	// Rank math breadcrumbs
	if ( ! $crumbs && function_exists( 'rank_math_get_breadcrumbs' ) ) {
		$crumbs = rank_math_get_breadcrumbs();
	}

	// Yoast breadcrumbs
	if ( ! $crumbs && function_exists( 'yoast_breadcrumb' ) ) {
		$crumbs = yoast_breadcrumb( '', '', false );
	}

	// Custom breadcrumbs
	if ( ! $crumbs && $custom_crumbs = apply_filters( 'wpex_custom_breadcrumbs', null ) ) {
		$crumbs = wp_kses_post( $custom_crumbs );
	}
}

if ( $crumbs ) {
	$is_custom = true;
}

// Theme breadcrumbs
if ( ! $is_custom && class_exists( 'WPEX_Breadcrumbs' ) ) {
	$args = [];
	if ( isset( $separator ) ) {
		$args['separator'] = $separator;
	}
	if ( ! empty( $atts['home_text'] ) ) {
		$args['home_text'] = $atts['home_text'];
	}
	if ( ! empty( $atts['show_parents'] ) ) {
		$args['show_parents'] = vcex_validate_boolean( $atts['show_parents'] );
	}
	if ( ! empty( $atts['first_term_only'] ) ) {
		$args['first_term_only'] = vcex_validate_boolean( $atts['first_term_only'] );
	}
	if ( ! empty( $atts['show_trail_end'] ) ) {
		$args['show_trail_end'] = vcex_validate_boolean( $atts['show_trail_end'] );
	}
	$crumbs = (new WPEX_Breadcrumbs( $args ))->generate_crumbs(); // @note needs to generate it's own to prevent issues with theme stuff
}

// Return if no crumbs
if ( ! $crumbs ) {
	return;
}

// Shortcode classes
$shortcode_class = [
	'vcex-breadcrumbs',
];

if ( vcex_validate_att_boolean( 'link_inherit_color', $atts, true ) ) {
	$shortcode_class[] = 'vcex-breadcrumbs--links-inherit-color';
}

if ( ! empty( $atts['color'] ) ) {
	$shortcode_class[] = 'vcex-breadcrumbs--custom-color';
}

if ( ! empty( $atts['font_style'] ) && 'italic' === $atts['font_style'] ) {
	$shortcode_class[] = 'wpex-italic';
}

if ( $extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_breadcrumbs' ) ) {
	$shortcode_class = array_merge( $shortcode_class, $extra_classes );
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_breadcrumbs', $atts );

// Display breadcrumbs
echo '<nav class="' . esc_attr( $shortcode_class ) . '">' . $crumbs . '</nav>';
