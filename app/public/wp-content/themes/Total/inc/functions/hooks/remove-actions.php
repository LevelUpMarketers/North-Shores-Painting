<?php

defined( 'ABSPATH' ) || exit;

/**
 * Helper function to remove all actions.
 */
function wpex_remove_actions() {
	$hooks = wpex_theme_hooks();
	foreach ( $hooks as $section => $array ) {
		if ( ! empty( $array['hooks'] ) && is_array( $array['hooks'] ) ) {
			foreach ( $array['hooks'] as $hook ) {
				remove_all_actions( $hook, false );
			}
		}
	}
}

/**
 * Remove default theme actions.
 */
function wpex_maybe_modify_theme_actions() {
	$blank_templates = [ 'templates/landing-page.php', 'templates/blank.php' ];
	$check = is_page_template( $blank_templates ) || is_singular( 'wpex_card' );

	if ( ! $check
		&& totaltheme_is_integration_active( 'post_types_unlimited' )
		&& is_singular()
		&& wpex_get_ptu_type_mod( get_post_type(), 'use_blank_template' )
	) {
		$check = true;
	}

	if ( ! $check && is_404() && true === get_theme_mod( 'error_page_use_blank_template' ) ) {
		$check = true;
	}

	$check = (bool) apply_filters( 'totaltheme/blank_template/is_enabled', $check );

	if ( $check ) {
		return wpex_remove_actions();
	}
}
add_action( 'template_redirect', 'wpex_maybe_modify_theme_actions' );
