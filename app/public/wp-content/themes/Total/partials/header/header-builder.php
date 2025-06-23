<?php

/**
 * Header Builder.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.1
 */

defined( 'ABSPATH' ) || exit;

$template_id = totaltheme_call_static( 'Header\Core', 'get_template_id' );

if ( ! $template_id ) {
	return;
}

if ( wpex_is_header_builder_page() && ( totaltheme_is_wpb_frontend_editor() || totaltheme_is_elementor_editor() ) ) {
	while ( have_posts() ) : the_post();
		the_content();
	endwhile;
} else {
	if ( totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) {
		$overlay_template = get_theme_mod( 'header_builder_overlay_page_id' );
		if ( is_numeric( $overlay_template ) && 'publish' === get_post_status( $overlay_template ) ) {
			$has_sticky = totaltheme_call_static( 'Header\Sticky', 'is_enabled' );
			if ( $has_sticky ) {
				echo '<div class="hidden-stuck">';
					totaltheme_render_template( $overlay_template );
				echo '</div>';
			} else {
				totaltheme_render_template( $overlay_template );
			}
			if ( ! $has_sticky ) {
				return;
			}
			echo '<div class="visible-stuck">';
				totaltheme_render_template( $template_id );
			echo '</div>';
			return; // bail early!
		}
	}
	totaltheme_render_template( $template_id );
}
