<?php
/**
 * WooCommerce Actions.
 *
 * @package TotalTheme
 * @subpackage Integration/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*/

	# Product Card
	# Move store notice
	# Remove woo category description
	# Move ratings and price on product page
	# Display shop text on paginated pages
	# Alter Upsell display
	# Alter Related display
	# Alter Cart collaterals (cross-sells)
	# Move star ratings on reviews to after meta
	# Add social share
	# Add clearfix after product summary
	# Add flexwrap around "add_to_cart_button"

/*-------------------------------------------------------------------------------*/

/**
 * Override product entries with custom card output.
 */
function wpex_woo_custom_card( $template, $slug, $name ) {
	if ( 'content-product' === $slug . '-' . $name ) {
		$product_card = wpex_product_entry_card_style();
		if ( $product_card ) {
			$template = get_template_part( 'woocommerce/theme-card' );
		}
	}
	return $template;
}
add_action( 'wc_get_template_part', 'wpex_woo_custom_card', 3, PHP_INT_MAX );

/**
 * Remove demo store notice from wp_footer place top of site.
 */
remove_action( 'wp_footer', 'woocommerce_demo_store' );
add_action( 'wpex_hook_wrap_top', 'woocommerce_demo_store', 0 );

/**
 * Remove woo category description (these are added already by the theme).
 */
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );

/**
 * Move ratings and price on product page.
 */
function wpex_woo_move_product_rating_price() {
	if ( ! get_theme_mod( 'woo_move_rating_price', true ) ) {
		return;
	}
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
}
// add_action( 'init', 'wpex_woo_move_product_rating_price' ); // disabled in v5.1

/**
 * Display WooCommerce archive description on paginated shop page.
 */
function wpex_woo_paginated_shop_description() {
	if ( ! get_theme_mod( 'woo_paginated_shop_description', true )
		|| ! wpex_is_woo_shop()
		|| ! is_paged()
		|| ! function_exists( 'wc_format_content' )
	) {
		return;
	}
	$shop_id = totaltheme_wc_get_page_id( 'shop' );
	if ( $shop_id && $shop_page = get_post( $shop_id ) ) {
		$allowed_html = wp_kses_allowed_html( 'post' );
		// This is needed for the search product block to work.
		$allowed_html = array_merge(
			$allowed_html,
			[
				'form'   => [
					'action'         => true,
					'accept'         => true,
					'accept-charset' => true,
					'enctype'        => true,
					'method'         => true,
					'name'           => true,
					'target'         => true,
				],
				'input'  => [
					'type'        => true,
					'id'          => true,
					'class'       => true,
					'placeholder' => true,
					'name'        => true,
					'value'       => true,
				],
				'button' => [
					'type'  => true,
					'class' => true,
					'label' => true,
				],
				'svg'    => [
					'hidden'    => true,
					'role'      => true,
					'focusable' => true,
					'xmlns'     => true,
					'width'     => true,
					'height'    => true,
					'viewbox'   => true,
				],
				'path'   => [
					'd' => true,
				],
			]
		);
		$description_safe = wc_format_content( wp_kses( $shop_page->post_content, $allowed_html ) );
		if ( $description_safe ) {
			echo '<div class="page-description">' . $description_safe . '</div>';
		}
	}
}
add_action( 'woocommerce_archive_description', 'wpex_woo_paginated_shop_description' );

/**
 * Alter upsell display.
 */
function wpex_woocommerce_upsell_display() {
	$count = get_theme_mod( 'woocommerce_upsells_count', null );

	if ( ! isset( $count ) || ( is_string( $count ) && '' == trim( $count ) ) ) {
		$count = 4;
	}

	if ( ! empty( $count ) ) {
		$columns = wpex_get_array_first_value( get_theme_mod( 'woocommerce_upsells_columns' ) );
		if ( empty( $columns ) ) {
			$columns = 4;
		}
		woocommerce_upsell_display( $count, $columns );
	}
}
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product_summary', 'wpex_woocommerce_upsell_display', 15 );

/**
 * Alter related display.
 */
function wpex_woocommerce_output_related_products() {
	$count = get_theme_mod( 'woocommerce_related_count', null );

	if ( ! isset( $count ) || ( is_string( $count ) && '' == trim( $count ) ) ) {
		$count = 4;
	}

	if ( ! empty( $count ) ) {

		$columns = wpex_get_array_first_value( get_theme_mod( 'woocommerce_related_columns' ) );

		if ( empty( $columns ) ) {
			$columns = 4;
		}

		$args = [
			'posts_per_page' => $count,
			'columns'        => $columns,
			'orderby'        => 'rand', // @codingStandardsIgnoreLine.
		];

		$args = (array) apply_filters( 'woocommerce_output_related_products_args', $args );

		woocommerce_related_products( $args );
	}
}
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
add_action( 'woocommerce_after_single_product_summary', 'wpex_woocommerce_output_related_products', 20 );

/**
 * Alter related display.
 */
function wpex_woocommerce_cross_sell_display() {
	$count = get_theme_mod( 'woocommerce_cross_sells_count', null );

	if ( ! isset( $count ) || ( is_string( $count ) && '' == trim( $count ) ) ) {
		$count = 2;
	}

	if ( ! empty( $count ) ) {

		$columns = wpex_get_array_first_value( get_theme_mod( 'woocommerce_cross_sells_columns' ) );

		if ( empty( $columns ) ) {
			$columns = 2;
		}

		woocommerce_cross_sell_display( $count, $columns );

	}
}
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_cart_collaterals', 'wpex_woocommerce_cross_sell_display' );

/**
 * Move star ratings on reviews to after meta.
 */
remove_action( 'woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating', 10 );
add_action( 'woocommerce_review_before_comment_text', 'woocommerce_review_display_rating', 0 );

/**
 * Add social share.
 */
function wpex_woocommerce_social_share() {
	$get = false;

	$current_filter = current_filter();

	$share_location = get_theme_mod( 'woo_product_social_share_location' ) ?: 'after_summary';

	/**
	 * Filters the woocommerce social share position.
	 *
	 * @param string $share_position
	 */
	$share_location = apply_filters( 'wpex_woocommerce_social_share_location', $share_location );

	switch ( $current_filter ) {
		case 'woocommerce_share':
			if ( 'woocommerce_share' == $share_location ) {
				$get = true;
			}
			break;
		case 'woocommerce_after_single_product_summary':
			if ( 'after_summary' == $share_location ) {
				$get = true;
			}
			break;
	}

	if ( $get ) {
		wpex_social_share();
	}
}
add_action( 'woocommerce_share', 'wpex_woocommerce_social_share' );
add_action( 'woocommerce_after_single_product_summary', 'wpex_woocommerce_social_share', 11 );

/**
 * Add clearfix after product summary.
 */
function wpex_woocommerce_after_summary_clearfix() {
	echo '<div class="wpex-clear-after-summary wpex-clear"></div>';
}
add_action( 'woocommerce_after_single_product_summary', 'wpex_woocommerce_after_summary_clearfix', 1 );
