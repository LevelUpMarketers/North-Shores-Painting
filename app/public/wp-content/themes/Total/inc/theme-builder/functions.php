<?php

defined( 'ABSPATH' ) || exit;

/**
 * Do location.
 */
function wpex_theme_do_location( $location = '' ): bool {
	return (bool) totaltheme_call_non_static( 'Theme_Builder', 'do_location', $location );
}

/**
 * Check if we are currently in header builder edit mode.
 */
function wpex_is_header_builder_page(): bool {
	return totaltheme_call_static( 'Header\Core', 'get_template_id' ) == wpex_get_current_post_id();
}

/**
 * Check if we are currently in footer builder edit mode.
 */
function wpex_is_footer_builder_page(): bool {
	return totaltheme_call_static( 'Footer\Core', 'get_template_id' ) == wpex_get_current_post_id();
}

/**
 * Returns post ID when using a dynamic template.
 */
function wpex_get_dynamic_post_id(): int {
	return (int) apply_filters( 'wpex_get_dynamic_post_id', wpex_get_current_post_id() );
}

/**
 * Checks if a specific location has a defined template.
 *
 * @note This function only works properly after did_action( 'wp' ).
 */
function totaltheme_location_has_template( string $location ): bool {
	$instance = totaltheme_get_instance_of( 'Theme_Builder' );
	return $instance && $instance->location_has_template( $location );
}

/**
 * Renders a template.
 */
function totaltheme_render_template( $template_id ) {
	$template_id  = wpex_parse_obj_id( $template_id, 'page' ) ?: $template_id;
	$builder_type = totaltheme_get_post_builder_type( $template_id );
	if ( 'elementor' === $builder_type ) {
		echo wpex_get_elementor_content_for_display( $template_id );
	} else {
		if ( 'wpbakery' === $builder_type ) {
			if ( $wpb_style = totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' ) ) {
				$wpb_style->render_style( $template_id, true );
			}
		}
		echo totaltheme_render_content( get_post_field( 'post_content', $template_id ) );
	}
}
