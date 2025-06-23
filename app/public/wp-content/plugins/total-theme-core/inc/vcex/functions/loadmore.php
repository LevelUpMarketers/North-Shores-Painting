<?php

defined( 'ABSPATH' ) || exit;

// @todo Deprecate.

/**
 * Checks if shortcode has load more.
 */
function vcex_shortcode_has_loadmore( $atts, $vcex_query ): bool {
	return vcex_validate_att_boolean( 'pagination_loadmore', $atts ) && ! empty( $vcex_query->max_num_pages );
}

/**
 * Check if we are currently loading new posts.
 */
function vcex_doing_loadmore(): bool {
	return ! empty( $_REQUEST['action'] ) && 'vcex_loadmore_ajax_render' === $_REQUEST['action'];
}

/**
 * Load More Scripts.
 */
function vcex_loadmore_scripts() {
	$dependencies = [
		'jquery',
		'imagesloaded',
	];

	if ( apply_filters( 'vcex_loadmore_enqueue_mediaelement', false ) ) {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

	// Enqueue load more script.
	wp_enqueue_script(
		'vcex-loadmore',
		vcex_get_js_file( 'frontend/loadmore' ),
		$dependencies,
		TTC_VERSION,
		true
	);

	// Localize load more script.
	wp_localize_script(
		'vcex-loadmore',
		'vcex_loadmore_params',
		[
			'ajax_url' => esc_url( set_url_scheme( admin_url( 'admin-ajax.php' ) ) ),
		]
	);
}

/**
 * Load More Button.
 */
function vcex_get_loadmore_button( $shortcode_tag, $atts, $query, $infinite_scroll = false ) {
	return totalthemecore_call_non_static( 'Vcex\Ajax', 'get_loadmore_button', $shortcode_tag, $atts, $query, $infinite_scroll );
}

/**
 *  Load More AJAX.
 */
function vcex_loadmore_ajax_render() {
	check_ajax_referer( 'vcex-ajax-pagination-nonce', 'nonce' );

	if ( empty( $_POST['shortcodeParams'] ) || empty( $_POST[ 'shortcodeTag' ] ) ) {
		wp_die();
	}

	$tag = sanitize_text_field( wp_unslash( $_POST['shortcodeTag'] ) );

	$allowed_tags = [
		'vcex_blog_grid',
		'vcex_image_grid',
		'vcex_portfolio_grid',
		'vcex_post_type_archive',
		'vcex_post_type_grid',
		'vcex_recent_news',
		'vcex_staff_grid',
		'vcex_testimonials_grid',
	];

	if ( ! in_array( $tag, $allowed_tags, true ) ) {
		wp_die();
	}

	if ( class_exists( 'WPBMap' ) ) {
		WPBMap::addAllMappedShortcodes(); // fix for WPBakery not working in ajax
	}

	$params = wp_unslash( array_map( 'sanitize_text_field', $_POST['shortcodeParams'] ) );

	wp_send_json_success( vcex_do_shortcode_function( $tag, $params ) );

	wp_die();
}
add_action( 'wp_ajax_vcex_loadmore_ajax_render', 'vcex_loadmore_ajax_render' );
add_action( 'wp_ajax_nopriv_vcex_loadmore_ajax_render', 'vcex_loadmore_ajax_render' );
