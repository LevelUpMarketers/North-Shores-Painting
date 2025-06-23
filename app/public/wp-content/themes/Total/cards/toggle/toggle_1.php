<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'vcex_do_shortcode_function' ) || ! shortcode_exists( 'vcex_toggle' ) ) {
	return;
}

$html = '';

if ( ! empty( $this->args['title_font_size'] )
	&& array_key_exists( $this->args['title_font_size'], wpex_utl_font_sizes() )
) {
	$title_font_size = 'wpex-text-' . $this->args['title_font_size'];
} else {
	$title_font_size = totaltheme_has_classic_styles() ? 'wpex-text-md' : 'wpex-text-lg';
}

if ( $title_font_size ) {
	$title_font_size = ' ' . trim( $title_font_size );
}

if ( $this->post_id ) {
	$content_id = sanitize_html_class( get_post_field( 'post_name', $this->post_id ) );
	if ( WPEX_VC_ACTIVE && $wpb_style = totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' ) ) {
		$html .= $wpb_style->get_style( $this->post_id );
	}
} else {
	$content_id = 'wpex-card-toggle_1--' . sanitize_html_class( $this->get_var( 'running_count' ) );
}

$atts = [
	'heading'          => $this->get_the_title(),
	'content_id'       => $content_id,
	'heading_el_class' => 'wpex-card-title wpex-heading wpex-child-inherit-color' . $title_font_size,
	'icon_position'    => 'right',
	'heading_tag'      => $this->get_title_tag(),
	'animate'          => 'true',
	'icon_type'        => 'angle',
	'state'            => 'closed',
	'parse_content'    => false, // the excerpt function will parse the content.
];

if ( isset( $extra_atts ) ) {
	$atts = wp_parse_args( $extra_atts, $atts );
}

$excerpt_args = [
	'length' => '-1',
];

$html .= vcex_do_shortcode_function( 'vcex_toggle', $atts, $this->get_the_excerpt( $excerpt_args ) );

return $html;
