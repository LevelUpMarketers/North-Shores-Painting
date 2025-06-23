<?php

/**
 * vcex_bullets shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $content ) ) {
	return;
}

// Define main vars
$is_elementor = isset( $atts['is_elementor_widget'] ) && true === $atts['is_elementor_widget'];
$content_safe = wp_kses_post( $content ); // Escape content early to prevent issues with RGBA colors not working with wp_kses_post.
$has_icon     = $is_elementor || vcex_validate_att_boolean('has_icon', $atts, 'true' );
$alignment    = ! empty( $atts['alignment'] ) ? sanitize_text_field( $atts['alignment'] ) : 'vertical';

// Wrap classes
$wrap_class = [
	'vcex-module',
	'vcex-bullets',
	"vcex-bullets--{$alignment}",
];

if ( ! empty( $atts['gap'] ) && $gap_class = vcex_parse_gap_class( $atts['gap'] ) ) {
	$wrap_class[] = $gap_class;
} elseif ( 'horizontal' === $alignment ) {
	$wrap_class[] = 'wpex-gap-20';
}

if ( 'horizontal' === $alignment && ! empty( $atts['justify'] ) && $justify = vcex_parse_justify_content_class( $atts['justify'] ) ) {
	$wrap_class[] = $justify;
}

if ( $has_icon ) {

	// Pre-defined bullet styles
	if ( ! $is_elementor && $atts['style'] && ! $atts['icon_type'] ) {
		$wrap_class[] = 'vcex-bullets-' . sanitize_html_class( $atts['style'] );
	}

	// Custom Icon
	else {

		// Get icon html
		if ( $is_elementor && empty( $atts['icon']['value'] ) ) {
			$atts['icon'] = 'dot-circle-o';
		}
		$icon_html = vcex_get_icon_html( $atts, 'icon', 'vcex-bullets-icon vcex-icon' );

		// Show Icon
		if ( $icon_html ) {
			$add_icon = '<div class="vcex-bullets-ci-wrap wpex-inline-flex"><span class="vcex-bullets-icon-wrap vcex-icon-wrap wpex-mr-10">' . $icon_html . '</span><div class="vcex-content wpex-flex-grow">';

			// Standard bullets search/replace
			$content = $content_safe;
			$content = str_replace( '<li>', '<li>' . $add_icon, $content );

			// Fix bugs with inline center align (lots of customers centered the bullets before align option was added)
			$content = str_replace( '<li style="text-align:center">', '<li style="text-align:center;">', $content );
			$content = str_replace( '<li style="text-align: center">', '<li style="text-align:center;">', $content );
			$content = str_replace( '<li style="text-align: center;">', '<li style="text-align:center;">', $content );
			$content = str_replace( '<li style="text-align:center;">', '<li style="text-align:center;">' . $add_icon, $content );

			// Close elements
			$content = str_replace( '</li>', '</div></div></li>', $content );
			$content_safe = $content;

			// Add custom icon wrap class
			$wrap_class[] = 'custom-icon';

		}

	}

} else {
	$wrap_class[] = 'vcex-bullets-ni';
}

// Get extra classes
if ( $extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_bullets' ) ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

// Turn shortcode classes array into string
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_bullets', $atts );

// Define wrap attributes
$wrap_attributes = [
	'id'    => ! empty( $atts['unique_id'] ) ? sanitize_text_field( $atts['unique_id'] ) : '',
	'class' => $wrap_class,
];

// Add list role to ul for accessibility since the list-style is set to none
$content_safe = str_replace( '<ul>', '<ul role="list">', $content_safe );

// Begin html output
echo vcex_parse_html( 'div', $wrap_attributes, vcex_parse_text( $content_safe ) );
