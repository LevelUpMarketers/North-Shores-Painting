<?php

defined( 'ABSPATH' ) || exit;

/**
 * Returns correct ID for any object.
 * Used to fix issues with translation plugins such as WPML & Polylang.
 */
function wpex_parse_obj_id( $id = '', $type = 'page', $key = '' ) {
	if ( ! $id ) {
		return;
	}
	if ( defined( 'WPEX_WPML_ACTIVE' ) && WPEX_WPML_ACTIVE ) {
		$type = ( 'term' === $type && $key ) ? $key : $type;
		if ( 'page' === $type ) {
			$type = get_post_type( $id ); // Fixes issues when using templatera for example for the topbar, header, footer, etc
		}
		$id = apply_filters( 'wpml_object_id', $id, $type, true );
	} elseif ( function_exists( 'pll_get_post' ) ) {
		$type = taxonomy_exists( $type ) ? 'term' : $type; // Fixes issue where type may be set to 'category' instead of term
		if ( 'page' === $type || 'post' === $type ) {
			$id = pll_get_post( $id );
		} elseif ( 'term' === $type && function_exists( 'pll_get_term' ) ) {
			$id = pll_get_term( $id );
		}
	}
	return $id;
}

/**
 * Retrives a theme mod value and translates it.
 * Translated strings do not have any defaults in the Customizer because they all have localized fallbacks.
 */
function wpex_get_translated_theme_mod( $id, $default = '' ) {
	return wpex_translate_theme_mod( $id, get_theme_mod( $id, $default ) );
}

/**
 * Provides translation support for theme_mods.
 */
function wpex_translate_theme_mod( $id = '', $val = '' ) {
	if ( ! $val || ! $id ) {
		return;
	}
	if ( function_exists( 'icl_t' ) ) {
		$val = icl_t( 'Theme Settings', $id, $val ); // WPML.
	} elseif ( function_exists( 'pll__' ) ) {
		$val = pll__( $val ); // Polylang.
	}
	return $val;
}

/**
 * Register theme mods for translations.
 */
function wpex_register_theme_mod_strings() {
	$strings = [
		'custom_logo'                    => false,
		'retina_logo'                    => false,
		'logo_icon_img'                  => false,
		'fixed_header_logo'              => false,
		'fixed_header_logo_retina'       => false,
		'overlay_header_logo'            => false,
		'overlay_header_logo_retina'     => false,
		'logo_height'                    => false,
		'mobile_menu_title'              => '',
		'mobile_menu_logo'               => '',
		'error_page_title'               => 'Error 404',
		'error_page_text'                => false,
		// @todo create Top_Bar class and include default_content method.
		'top_bar_content'                => TotalTheme\Topbar\Core::get_default_content(),
		'top_bar_social_alt'             => false,
		'header_aside'                   => false,
		'header_flex_aside_content'      => false,
		'breadcrumbs_home_title'         => false,
		'menu_search_placeholder'        => false,
		'comment_form_title_reply'       => '',
		'blog_entry_readmore_text'       => 'Read more',
		'social_share_heading'           => 'Share This',
		'portfolio_related_title'        => 'Related Projects',
		'staff_related_title'            => 'Related Staff',
		'blog_related_title'             => 'Related Posts',
		'callout_text'                   => 'I am the footer call-to-action block, here you can add some relevant/important information about your company or product. I can be disabled in the Customizer.',
		'callout_link'                   => '#',
		'callout_link_txt'               => 'Get In Touch',
		'footer_copyright_text'          =>  TotalTheme\Footer\Bottom\Copyright::get_default_content(),
		'blog_single_header_custom_text' => 'Blog',
		'mobile_menu_toggle_text'        => 'Menu',
		'mobile_menu_icon_label'         => false,
		'page_animation_loading'         => 'Loading&hellip;',
	];
	if ( totaltheme_is_integration_active( 'woocommerce' ) ) {
		$strings['woo_shop_single_title']     = 'Store';
		$strings['woo_menu_icon_custom_link'] = '';
		$strings['woo_sale_flash_text']       = '';
	}
	if ( function_exists( 'wpex_social_share_items' ) ) {
		$social_share_items = wpex_social_share_items();
		if ( is_array( $social_share_items ) ) {
			foreach ( $social_share_items as $k => $v ) {
				$strings["social_share_{$k}_label"] = false;
			}
		}
	}
	return (array) apply_filters( 'wpex_register_theme_mod_strings', $strings );
}

/**
 * Prevent issues with WPGlobus trying to translate certain theme settings.
 *
 * @todo move to TotalTheme\Integration
 */
function wpex_modify_wpglobus_customize_disabled_setting_mask( $disabled_setting_mask ) {
	$disabled_setting_mask[] = '_bg';
	$disabled_setting_mask[] = '_background';
	$disabled_setting_mask[] = '_border';
	$disabled_setting_mask[] = '_padding';
	return $disabled_setting_mask;
}
add_filter( 'wpglobus_customize_disabled_setting_mask', 'wpex_modify_wpglobus_customize_disabled_setting_mask' );
