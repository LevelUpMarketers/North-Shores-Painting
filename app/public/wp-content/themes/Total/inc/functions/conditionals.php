<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*/

	# General
	# Header
	# WooCommerce
	# WPBakery
	# Elementor

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Checks if legacy typography is enabled.
 */
function totaltheme_has_classic_styles(): bool {
	static $check = null;
	if ( null === $check ) {
		$check = (bool) get_theme_mod( 'classic_styles_enable' );
	}
	return $check;
}

/**
 * Check if we are in the context of a live search.
 */
function totaltheme_is_live_search(): bool {
	return ! empty( $_REQUEST['action'] ) && 'wpex_live_search' === $_REQUEST['action'];
}

/**
 * Check if currently displaying a card.
 */
function totaltheme_is_card(): bool {
	return (bool) totaltheme_card_instance();
}

/**
 * Check if a specific customizer section is enabled.
 */
function wpex_has_customizer_panel( $section ): bool {
	$disabled_panels = (array) get_option( 'wpex_disabled_customizer_panels' );
	return ! ( $disabled_panels && in_array( $section, $disabled_panels ) );
}

/**
 * Check if responsiveness is enabled.
 */
function wpex_is_layout_responsive(): bool {
	return (bool) apply_filters( 'wpex_is_layout_responsive', true );
}

/**
 * Check if the post edit links should display on the page.
 *
 * @todo rename to wpex_has_retina_support
 */
function wpex_is_retina_enabled(): bool {
	return ( get_theme_mod( 'image_resizing', true ) && get_theme_mod( 'retina', false ) );
}

/**
 * Check if metadata exists.
 */
function wpex_has_post_meta( $key = '' ): bool {
	return (bool) get_post_meta( wpex_get_current_post_id(), $key, true );
}

/**
 * Check if google services are disabled.
 */
function wpex_disable_google_services(): bool {
	$check = wp_validate_boolean( get_theme_mod( 'disable_gs', false ) );
	return (bool) apply_filters( 'wpex_disable_google_services', $check );
}

/**
 * Check if comments should display or not.
 */
function wpex_show_comments(): bool {
	if ( post_password_required() || ( ! comments_open() && get_comments_number() < 1 ) ) {
		$check = false;
	} else {
		$check = true;
	}
	return (bool) apply_filters( 'wpex_show_comments', $check );
}

/**
 * Check if a post has media (used for entry classes)
 *
 * @todo rename to wpex_has_post_media
 */
function wpex_post_has_media( $post = '', $deprecated = false ): bool {
	$post  = get_post( $post );
	$type  = get_post_type( $post );
	$check = false;
	if ( $type ) {
		switch ( $type ) {
			case 'post':
				$check = (bool) wpex_blog_entry_media_type();
				break;
			case 'portfolio':
				$check = (bool) wpex_portfolio_entry_media_type();
				break;
			case 'staff':
				$check = (bool) wpex_staff_entry_media_type();
				break;
			default:
				$check = (bool) wpex_cpt_entry_media_type();
				break;
		}
	}
	return (bool) apply_filters( 'wpex_post_has_media', $check, $post->ID );
}

/**
 * Check if the next/previous links should display.
 */
function wpex_has_next_prev(): bool {
	if ( ! is_singular() ) {
		return false;
	}

	// Get current post type
	$post_type = get_post_type();

	// Not needed for these types
	if ( in_array( $post_type, [ 'page', 'attachment', 'templatera', 'elementor_library', 'wpex_templates' ] ) ) {
		return false;
	}

	// Enabled by default
	$check = true;

	// WooCommerce check
	if ( totaltheme_is_integration_active( 'woocommerce' )
		&& is_singular( 'product' )
		&& is_woocommerce()
	) {
		$check = get_theme_mod( 'woo_next_prev', true );
	}

	// We use "blog" for the post post type mod
	if ( 'post' === $post_type ) {
		$post_type = 'blog';
	}

	// Check if enabled for specific post type
	$check = get_theme_mod( "{$post_type}_next_prev", $check );
	$ptu_check = wpex_get_ptu_type_mod( $post_type, 'next_prev' );

	if ( isset( $ptu_check ) ) {
		$check = $ptu_check; // we use isset incase it's "0"
	}

	return (bool) apply_filters( 'wpex_has_next_prev', wp_validate_boolean( $check ), $post_type );
}

/**
 * Check if the readmore button should display.
 *
 * @todo fix "blog_exceprt" typo.
 */
function wpex_has_readmore(): bool {
	$check = true;
	if ( post_password_required() ) {
		$check = false;
	} elseif ( 'post' === get_post_type() ) {
		// @todo fix "blog_exceprt" typo.
		if ( ! get_theme_mod( 'blog_exceprt', true ) ) {
			$post_content = (string) (get_post()->post_content ?? '');
			if ( ! $post_content || ! \str_contains( $post_content, '<!--more-->' ) ) {
				$check = false;
			}
		}
	}
	return (bool) apply_filters( 'wpex_has_readmore', $check );
}

/**
 * Check if the breadcrumbs is enabled.
 */
function wpex_has_breadcrumbs(): bool {
	$check   = wp_validate_boolean( get_theme_mod( 'breadcrumbs', true ) );
	$post_id = wpex_get_current_post_id();
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_disable_breadcrumbs', true ) ) {
		if ( 'on' === $meta ) {
			$check = false;
		} elseif ( 'enable' === $meta ) {
			$check = true;
		}
	}
	if ( is_front_page() ) {
		$check = false;
	}
	return (bool) apply_filters( 'wpex_has_breadcrumbs', $check );
}

/**
 * Check if current page has a sidebar.
 */
function wpex_has_sidebar( $post_id = '' ): bool {
	$check = in_array( wpex_content_area_layout( $post_id ), [ 'left-sidebar', 'right-sidebar' ], true  );
	return (bool) apply_filters( 'wpex_has_sidebar', $check, $post_id );
}

/**
 * Check if Google Services are enabled.
 */
function wpex_has_google_services_support(): bool {
	return ! wpex_disable_google_services();
}

/*-------------------------------------------------------------------------------*/
/* [ Header ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if the header style is in dev mode.
 */
function wpex_has_dev_style_header() {
	return ( 'dev' === totaltheme_call_static( 'Header\Core', 'style' ) );
}

/**
 * Check if the header style is not in dev mode.
 */
function wpex_hasnt_dev_style_header() {
	return ! wpex_has_dev_style_header();
}

/*-------------------------------------------------------------------------------*/
/* [ WooCommerce  ]
/*-------------------------------------------------------------------------------*/

/**
 * Checks if on the WooCommerce shop page.
 */
function wpex_is_woo_shop(): bool {
	return function_exists( 'is_shop' ) && totaltheme_is_integration_active( 'woocommerce' ) && is_shop();
}

/**
 * Check if WooCommerce default output should be disabled.
 */
function wpex_woo_archive_has_loop(): bool {
	$check = true;
	if ( get_theme_mod( 'woo_shop_disable_default_output' ) && wpex_is_woo_shop() && ! is_search() ) {
		$check = false;
	}
	return (bool) apply_filters( 'wpex_woo_archive_has_loop', $check );
}

/**
 * Checks if on a WooCommerce tax.
 */
if ( ! function_exists( 'wpex_is_woo_tax' ) ) {
	function wpex_is_woo_tax(): bool {
		if ( ! totaltheme_is_integration_active( 'woocommerce' ) ) {
			return false; // important check since we use Woo only functions.
		}
		$check = is_product_category() || is_product_tag();
		if ( ! $check && is_tax() && function_exists( 'taxonomy_is_product_attribute' ) ) {
			$tax_obj = get_queried_object();
			if ( is_object( $tax_obj ) && ! empty( $tax_obj->taxonomy ) ) {
				$is_product_attribute = taxonomy_is_product_attribute( $tax_obj->taxonomy );
				if ( $is_product_attribute ) {
					$check = true;
				}
			}
		}
		return (bool) apply_filters( 'wpex_is_woo_tax', $check );
	}
}

/**
 * Checks if on singular WooCommerce product post.
 */
function wpex_is_woo_single(): bool {
	return function_exists( 'is_woocommerce' ) && is_singular( 'product' ) && is_woocommerce();
}

/**
 * Check if product is in stock.
 */
function wpex_woo_product_instock(): bool {
	if ( 'yes' !== get_option( 'woocommerce_manage_stock', 'yes' ) ) {
		return true;
	}
	global $product;
	return ! $product || ( is_object( $product ) && $product->is_in_stock() );
}

/*-------------------------------------------------------------------------------*/
/* [ WPBakery ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if user is currently editing in front-end editor mode.
 */
function totaltheme_is_wpb_frontend_editor(): bool {
	return \function_exists( 'vc_is_inline' ) && \vc_is_inline();
}

/*-------------------------------------------------------------------------------*/
/* [ Elementor ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if user is currently editing in front-end editor mode.
 *
 * @note This function works only at init hook >= 0
 */
function totaltheme_is_elementor_editor(): bool {
	return ( class_exists( 'Elementor\Plugin' )
		&& is_object( \Elementor\Plugin::$instance->preview )
		&& is_callable( [ \Elementor\Plugin::$instance->preview, 'is_preview_mode' ] )
		&& \Elementor\Plugin::$instance->preview->is_preview_mode()
	);
}
