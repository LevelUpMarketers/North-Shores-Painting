<?php

defined( 'ABSPATH' ) || exit;

/**
 * 3.0.0
 */
function totaltheme_update_300() {
	// Reset customizer panels since things were modified.
	delete_option( 'wpex_customizer_panels' );

	// Remove old CSS and typography cache.
	if ( get_theme_mod( 'wpex_customizer_css_cache' ) ) {
		remove_theme_mod( 'wpex_customizer_css_cache' );
	}

	if ( get_theme_mod( 'wpex_customizer_typography_cache' ) ) {
		remove_theme_mod( 'wpex_customizer_typography_cache' );
	}

	// Make sure blog entry builder has a title and meta
	$blog_blocks = get_theme_mod( 'blog_entry_composer' );
	if ( ! empty( $blog_blocks ) ) {
		$blog_blocks = str_replace( 'title_meta', 'title,meta', $blog_blocks );
		$blog_blocks = str_replace( 'title_excerpt_content', 'title,meta,excerpt_content', $blog_blocks );
		set_theme_mod( 'blog_entry_composer', $blog_blocks );
	}

	// Disable entry meta if was previously disabled.
	$blog_entry_meta = get_theme_mod( 'blog_entry_meta' );
	if ( ! empty( $blog_entry_meta ) ) {
		$blog_blocks = get_theme_mod( 'blog_entry_composer' );
		if ( ! empty( $blog_blocks ) ) {
			$blog_blocks = str_replace( 'meta,', '', $blog_blocks );
			set_theme_mod( 'blog_entry_composer', $blog_blocks );
		}
		remove_theme_mod( 'blog_entry_meta' );
	}

	// Make sure blog single builder has a title and meta.
	$blog_single_composer = get_theme_mod( 'blog_single_composer' );
	if ( ! empty( $blog_single_composer ) ) {
		$blog_single_composer = str_replace( 'title_meta', 'title,meta', $blog_single_composer );
		$blog_single_composer = str_replace( 'title_post_series', 'title,meta', $blog_single_composer );
		set_theme_mod( 'blog_single_composer', $blog_single_composer );
	}

	// Disable single meta if was previously disabled.
	$blog_post_meta = get_theme_mod( 'blog_post_meta' );
	if ( ! empty( $blog_post_meta ) ) {
		$blog_single_composer = get_theme_mod( 'blog_single_composer' );
		if ( ! empty( $blog_single_composer ) ) {
			$blog_single_composer = str_replace( 'meta,', '', $blog_single_composer );
			set_theme_mod( 'blog_single_composer', $blog_single_composer );
		}
		remove_theme_mod( 'blog_post_meta' );
	}

	// Move tracking to options.
	$tracking = get_theme_mod( 'tracking' );
	if ( ! empty( $tracking ) ) {
		$actions = get_option( 'wpex_custom_actions' );
		if ( ! empty( $actions ) && is_array( $actions ) ) {
			if ( isset( $actions['wp_head']['action'] ) ) {
				$actions['wp_head']['action'] .= $tracking;
			} else {
				$actions['wp_head']['action'] = $tracking;
			}
		} else {
			$actions = [ 'wp_head' => [ 'action' => $tracking ] ];
		}
		update_option( 'wpex_custom_actions', $actions, false );
		remove_theme_mod( 'tracking' );
	}

	// Update user license.
	$envato_license_key = get_theme_mod( 'envato_license_key' );
	if ( $envato_license_key ) {
		update_option( 'wpex_product_license', $envato_license_key );
		remove_theme_mod( 'envato_license_key' );
	}

	// Menu Search.
	$main_search = get_theme_mod( 'main_search', 'prevent_false_negative' );
	if ( $main_search !== 'prevent_false_negative' ) {
		if ( false === wp_validate_boolean( $main_search ) ) {
			set_theme_mod( 'menu_search_style', 'disabled' ); // set correct menu style
			set_theme_mod( 'header_aside_search', 'disabled' ); // disable header 2 search
		} elseif ( true === wp_validate_boolean( $main_search ) ) {
			set_theme_mod( 'menu_search_style', $main_search );
		}
		remove_theme_mod( 'main_search' );
	}

	// Update social style.
	$top_bar_social_style = get_theme_mod( 'top_bar_social_style' );
	if ( $top_bar_social_style && 'font_icons' == $top_bar_social_style ) {
		set_theme_mod( 'top_bar_social_style', 'none' );
	}
}

/**
 * 3.3.0
 */
function totaltheme_update_330() {
	// Turn retina logo height into just logo height and delete old theme mod.
	if ( $mod = get_theme_mod( 'retina_logo_height' ) ) {
		set_theme_mod( 'logo_height', $mod );
		remove_theme_mod( 'retina_logo_height' );
	}

	// WooMenu cart enable/disable.
	if ( ! get_theme_mod( 'woo_menu_icon', true ) ) {
		set_theme_mod( 'woo_menu_cart_enable', false );
		remove_theme_mod( 'woo_menu_icon' );
	}

	// Sidebar heading color => remove duplicate setting.
	if ( $mod = get_theme_mod( 'sidebar_headings_color' ) ) {
		$mod2 = get_theme_mod( 'sidebar_widget_title_typography' );
		if ( is_array( $mod2 ) ) {
			$mod2['color'] = $mod;
		} else {
			$mod2 = [ 'color' => $mod ];
		}
		set_theme_mod( 'sidebar_widget_title_typography', $mod2 );
		remove_theme_mod( 'sidebar_headings_color' );
	}

	// Remove license key.
	delete_option( 'wpex_product_license' );

	if ( get_theme_mod( 'envato_license_key' ) ) {
		remove_theme_mod( 'envato_license_key' );
	}

	// New single product thumb image sizes | Set equal to current post thumbnail size.
	if ( class_exists( 'WooCommerce', false ) ) {
		if ( $mod = get_theme_mod( 'woo_post_width' ) ) {
			set_theme_mod( 'woo_post_thumb_width', $mod );
		}
		if ( $mod = get_theme_mod( 'woo_post_height' ) ) {
			set_theme_mod( 'woo_post_thumb_height', $mod );
		}
		if ( $mod = get_theme_mod( 'woo_post_image_crop' ) ) {
			set_theme_mod( 'woo_post_thumb_crop', $mod );
		}
	}

	// Auto updates removed.
	delete_option( 'wpex_product_license' );
}

/**
 * 3.3.2
 */
function totaltheme_update_332() {
	// Set correct related image sizes => Portfolio.
	if ( $mod = get_theme_mod( 'portfolio_entry_image_width' ) ) {
		set_theme_mod( 'portfolio_related_image_width', $mod );
	}
	if ( $mod = get_theme_mod( 'portfolio_entry_image_height' ) ) {
		set_theme_mod( 'portfolio_related_image_height', $mod );
	}
	if ( $mod = get_theme_mod( 'portfolio_entry_image_crop' ) ) {
		set_theme_mod( 'portfolio_related_image_crop', $mod );
	}
	// Set correct related image sizes => Staff.
	if ( $mod = get_theme_mod( 'staff_entry_image_width' ) ) {
		set_theme_mod( 'staff_related_image_width', $mod );
	}
	if ( $mod = get_theme_mod( 'staff_entry_image_height' ) ) {
		set_theme_mod( 'staff_related_image_height', $mod );
	}
	if ( $mod = get_theme_mod( 'staff_entry_image_crop' ) ) {
		set_theme_mod( 'staff_related_image_crop', $mod );
	}
}

/**
 * 3.3.3
 */
function totaltheme_update_333() {
	delete_option( 'wpex_portfolio_branding' );
	delete_option( 'wpex_staff_branding' );
	delete_option( 'wpex_testimonials_branding' );
}

/**
 * 3.4.0
 */
function totaltheme_update_340() {
	if ( ! get_theme_mod( 'fixed_header', true ) ) {
		set_theme_mod( 'fixed_header_style', 'disabled' );
		remove_theme_mod( 'fixed_header' );
	}

	if ( get_theme_mod( 'shink_fixed_header' ) ) {
		remove_theme_mod( 'shink_fixed_header' );
	}
}

/**
 * 3.5.0
 */
function totaltheme_update_350() {
	$composer = [ 'content' ];
	if ( get_theme_mod( 'page_featured_image' ) ) {
		unset( $composer[0] );
		$composer[] = 'media';
		$composer[] = 'content';
	}
	if ( get_theme_mod( 'social_share_pages' ) ) {
		$composer[] = 'share';
	}
	if ( get_theme_mod( 'page_comments' ) ) {
		$composer[] = 'comments';
	}
	$composer = implode( ',', $composer );
	set_theme_mod( 'page_composer', $composer );

	if ( get_theme_mods() ) {
		remove_theme_mod( 'page_featured_image' );
		remove_theme_mod( 'social_share_pages' );
		remove_theme_mod( 'page_comments' );
	}
}

/**
 * 4.0
 */
function totaltheme_update_40() {
	// Port custom CSS to new WP custom CSS function if WP is up to date.
	if ( function_exists( 'wp_get_custom_css' )
		&& function_exists( 'wp_update_custom_css_post' )
		&& $deprecated_css = get_theme_mod( 'custom_css', null )
	) {
		$core_css = wp_get_custom_css();
		$return = wp_update_custom_css_post( $core_css . $deprecated_css );
		if ( ! is_wp_error( $return ) && get_theme_mod( 'custom_css' ) ) {
			remove_theme_mod( 'custom_css' );
		}
	}

	// Update patterns bg url.
	if ( $pattern = get_theme_mod( 'background_pattern' ) ) {
		$pattern = str_replace( [ '.png', WPEX_THEME_URI . '/images/patterns/' ], '', $pattern );
		set_theme_mod( 'background_pattern', $pattern );
	}

	// Update load custom font 1 setting.
	if ( $mod = get_theme_mod( 'load_custom_font_1_typography' ) ) {
		$font_family = $mod['font-family'] ?? '';
		set_theme_mod( 'load_custom_google_font_1', $mod['font-family'] );
		remove_theme_mod( 'load_custom_font_1_typography' );
	}

	// Fix for removed social_share_heading_enable setting.
	// which wasn't needed because you could just leave the sharing text empty instead.
	if ( ! get_theme_mod( 'social_share_heading_enable', true ) ) {
		set_theme_mod( 'social_share_heading', '' );
		remove_theme_mod( 'social_share_heading_enable' );
	}
}

/**
 * 4.3
 */
function totaltheme_update_43() {
	$mods = [
		'footer_widget_title_typography'  => 'footer_headings_color',
		'sidebar_widget_title_typography' => 'sidebar_headings_color',
	];

	foreach ( $mods as $old => $new ) {
		$mod = get_theme_mod( $old );
		if ( isset( $mod['color'] ) ) {
			set_theme_mod( $new, $mod['color'] );
			unset( $mod['color'] );
			set_theme_mod( $old, $mod );
		}
	}

	// Convert some settings to prevent conflicts.
	$mods = [
		'background_image',
		'background_color',
		'background_style',
		'background_pattern',
	];
	foreach ( $mods as $mod ) {
		if ( $val = get_theme_mod( $mod ) ) {
			set_theme_mod( 't_' . $mod, $val );
			remove_theme_mod( $mod );
		}
	}

	// Update Customizer image settings.
	if ( function_exists( 'attachment_url_to_postid' ) ) {
		$media_settings = [
			'custom_logo',
			'retina_logo',
			'fixed_header_logo',
			'fixed_header_logo_retina',
			'background_image',
			'page_header_background_img',
		];
		foreach ( $media_settings as $setting ) {
			if ( $mod = get_theme_mod( $setting ) ) {
				$mod_id = attachment_url_to_postid( $mod );
				if ( $mod_id ) {
					set_theme_mod( $setting, $mod_id );
				}
			}
		}
	}
}

/**
 * 4.4.1
 */
function totaltheme_update_441() {
	if ( $mod = get_theme_mod( 'woo_shop_slider' ) ) {
		if ( function_exists( 'wc_get_page_id' ) ) {
			$shop_id = wc_get_page_id( 'shop' );
			if ( $shop_id ) {
				update_post_meta( $shop_id, 'wpex_post_slider_shortcode', $mod );
				update_post_meta( $shop_id, 'wpex_post_slider_bottom_margin', '30px' );
			}
		}
		remove_theme_mod( 'woo_shop_slider' );
	}
}

/**
 * 4.5.2
 */
function totaltheme_update_452() {
	if ( $mod = get_theme_mod( 'wpex_ybtt_trim_title' ) ) {
		set_theme_mod( 'breadcrumbs_title_trim', $mod );
		remove_theme_mod( 'wpex_ybtt_trim_title' );
	}
}

/**
 * 5.0
 */
function totaltheme_update_50() {
	// Update scroll to top position theme_mod.
	$scroll_top_left_position = get_theme_mod( 'scroll_top_left_position' );
	if ( $scroll_top_left_position && ! get_theme_mod( 'scroll_top_right_position' ) ) {
		set_theme_mod( 'scroll_top_right_position', $scroll_top_left_position );
		remove_theme_mod( 'scroll_top_left_position' );
	}

	// Update breadcrumbs position theme_mod.
	$breadcrumbs_position = get_theme_mod( 'breadcrumbs_position' );
	if ( 'absolute' == $breadcrumbs_position ) {
		set_theme_mod( 'breadcrumbs_position', 'page_header_aside' );
	} elseif ( 'under-title' == $breadcrumbs_position ) {
		set_theme_mod( 'breadcrumbs_position', 'page_header_content' );
	}

	// Update page_header_table_height theme_mod.
	$page_header_table_height = get_theme_mod( 'page_header_table_height' );
	if ( $page_header_table_height ) {
		set_theme_mod( 'page_header_min_height', $page_header_table_height );
		remove_theme_mod( 'page_header_table_height' );
	}

	// Resave aria_labels as single array.
	$aria_labels = [];
	$main_menu_aria_label = get_theme_mod( 'main_menu_aria_label' );
	if ( $main_menu_aria_label ) {
		$aria_labels['site_navigation'] = $main_menu_aria_label;
		remove_theme_mod( 'main_menu_aria_label' );
	}
	$footer_callout_aria_label = get_theme_mod( 'footer_callout_aria_label' );
	if ( $footer_callout_aria_label ) {
		$aria_labels['footer_callout'] = $footer_callout_aria_label;
		remove_theme_mod( 'footer_callout_aria_label' );
	}
	$footer_menu_aria_label = get_theme_mod( 'footer_menu_aria_label' );
	if ( $footer_menu_aria_label ) {
		$aria_labels['footer_bottom_menu'] = $footer_menu_aria_label;
		remove_theme_mod( 'footer_menu_aria_label' );
	}
	$mobile_menu_toggle_aria_label = get_theme_mod( 'mobile_menu_toggle_aria_label' );
	if ( $mobile_menu_toggle_aria_label ) {
		$aria_labels['mobile_menu_toggle'] = $mobile_menu_toggle_aria_label;
		remove_theme_mod( 'mobile_menu_toggle_aria_label' );
	}
	$mobile_menu_aria_label = get_theme_mod( 'mobile_menu_aria_label' );
	if ( $mobile_menu_aria_label ) {
		$aria_labels['mobile_menu'] = $mobile_menu_aria_label;
		remove_theme_mod( 'mobile_menu_aria_label' );
	}
	if ( ! empty( $aria_labels ) ) {
		set_theme_mod( 'aria_labels', $aria_labels );
	}

	// Delete old customizer panels setting.
	delete_option( 'wpex_customizer_panels' );
}

/**
 * 5.4
 */
function totaltheme_update_54() {
	if ( $mod = get_theme_mod( 'breadcrumbs_disable_taxonomies' ) ) {
		set_theme_mod( 'breadcrumbs_show_terms', false );
		remove_theme_mod( 'breadcrumbs_disable_taxonomies' );
	}

	if ( $mod = get_theme_mod( 'mega_menu_title' ) ) {
		set_theme_mod( 'megamenu_heading_color', $mod );
		remove_theme_mod( 'mega_menu_title' );
	}

	if ( $mod = get_theme_mod( 'menu_li_left_margin' ) ) {
		if ( 'one' === get_theme_mod( 'header_style', 'one' ) ) {
			set_theme_mod( 'main_nav_gutter', $mod );
		}
		remove_theme_mod( 'menu_li_left_margin' );
	}

	if ( $mod = get_theme_mod( 'menu_a_padding' ) ) {
		if ( in_array( get_theme_mod( 'header_style', 'one' ), [ 'two', 'three', 'four', 'five' ] ) ) {
			set_theme_mod( 'main_nav_link_padding_x', $mod );
		}
		remove_theme_mod( 'menu_a_padding' );
	}

	if ( $mod = get_theme_mod( 'page_header_border_width' ) ) {
		set_theme_mod( 'page_header_top_border_width', $mod );
		set_theme_mod( 'page_header_bottom_border_width', $mod );
		remove_theme_mod( 'page_header_border_width' );
	}

	if ( $mod = get_theme_mod( 'accent_color' ) ) {
		if ( ! get_theme_mod( 'menu_link_color_hover' ) ) {
			set_theme_mod( 'menu_link_color_hover', $mod );
		}
		if ( ! get_theme_mod( 'menu_link_color_active' ) ) {
			set_theme_mod( 'menu_link_color_active', $mod );
		}
	}
}

/**
 * 5.7.2
 */
function totaltheme_update_572() {
	if ( get_theme_mod( 'error_page_redirect' ) ) {
		remove_theme_mod( 'error_page_redirect' );
	}
}

/**
 * 5.10
 */
function totaltheme_update_510() {
	// Remove deprecated options.
	delete_option( 'total_version' );
	delete_option( 'wpex_customizer_options' );
	delete_option( 'wpex_disabled_customizer_sections' );
	delete_option( 'wpex_portfolio_editor' );
	delete_option( 'wpex_staff_editor' );
	delete_option( 'wpex_testimonials_editor' );
	delete_option( 'wpex_accessibility_settings' );
	delete_option( 'wpex_total_customizer_backup' );
	delete_option( 'wpex_custom_css_backup' );
	delete_option( 'total_import_theme_mods_backup' );

	// Renamed tgmpa id.
	delete_metadata( 'user', null, 'tgmpa_dismissed_notice_wpex_theme', null, true );
}

/**
 * 5.15
 */
function totaltheme_update_515() {
	// Remove deprecated options.
	delete_option( 'wpex_custom_css' ); // we used to store the custom css in a backup when saving the panel.

	// Rename theme mods.
	$rename_mods = [
		'woo_entry_width'           => 'shop_catalog_image_width',
		'woo_entry_height'          => 'shop_catalog_image_height',
		'woo_entry_image_crop'      => 'shop_catalog_image_crop',
		
		'woo_post_width'            => 'shop_single_image_width',
		'woo_post_height'           => 'shop_single_image_height',
		'woo_post_image_crop'       => 'shop_single_image_crop',

		'woo_post_thumb_width'      => 'shop_single_thumbnail_image_width',
		'woo_post_thumb_height'     => 'shop_single_thumbnail_image_height',
		'woo_post_thumb_crop'       => 'shop_single_thumbnail_image_crop',

		'woo_cat_entry_width'       => 'shop_category_image_width',
		'woo_cat_entry_height'      => 'shop_category_image_height',
		'woo_cat_entry_image_crop'  => 'shop_category_image_crop',

		'woo_shop_thumbnail_width'  => 'shop_cart_image_width',
		'woo_shop_thumbnail_height' => 'shop_cart_image_height',
		'woo_shop_thumbnail_crop'   => 'shop_cart_image_crop',
	];

	foreach ( $rename_mods as $old_name => $new_name ) {
		if ( $mod = get_theme_mod( $old_name ) ) {
			$new_mod = set_theme_mod( $new_name, $mod );
			if ( $new_mod ) {
				remove_theme_mod( $old_name );
			}
		}
	}
}

/**
 * 5.16
 */
function totaltheme_update_516() {
	// Deprecates the theme's enable_yoast_breadcrumbs theme_mod and enables the new Yoast setting.
	if ( class_exists( 'WPSEO_Options', false )
		&& get_theme_mod( 'enable_yoast_breadcrumbs', true )
		&& is_callable( 'WPSEO_Options::set' )
	) {
		WPSEO_Options::set( 'breadcrumbs-enable', true );
	}
}

/**
 * 5.99
 * 
 * Technically 6.0 but we use 5.99 so anyone trying the beta (5.99) won't have issues.
 */
function totaltheme_update_599() {
	if ( ! get_theme_mods() ) {
		return;
	}

	// Enable legacy typography
	set_theme_mod( 'classic_styles_enable', true );

	// Disable new WooCommerce block notices
	set_theme_mod( 'woo_block_notices_enable', false );

	// Renamed aria labels
	if ( $aria_labels = get_theme_mod( 'aria_labels' ) ) {
		if ( isset( $aria_labels['mobile_menu_toggle'] ) ) {
			$aria_labels['mobile_menu_open'] = $aria_labels['mobile_menu_toggle'];
			unset( $aria_labels['mobile_menu_toggle'] );
		}
		set_theme_mod( 'aria_labels', $aria_labels );
	}

	// Disable wpbakery theme styles if it wasn't enabled because it wasn't on by default before (unless you were using slim mode)
	if ( ! get_theme_mod( 'vcex_theme_style_is_default' ) && ! get_theme_mod( 'wpb_slim_mode_enable' ) ) {
		set_theme_mod( 'vcex_theme_style_is_default', false );
	}

	// Deprecated mobile_menu_toggle_position_static setting
	if ( get_theme_mod( 'mobile_menu_toggle_position_static', true ) && 'toggle' === get_theme_mod( 'mobile_menu_style' ) ) {
		set_theme_mod( 'mobile_menu_style', 'toggle_inline' );
		remove_theme_mod( 'mobile_menu_toggle_position_static' );
	}

	// Deprecated mobile_menu_full_screen_under_header setting
	if ( get_theme_mod( 'mobile_menu_full_screen_under_header' ) && 'full_screen' === get_theme_mod( 'mobile_menu_style' ) ) {
		set_theme_mod( 'mobile_menu_style', 'full_screen_under_header' );
		remove_theme_mod( 'mobile_menu_full_screen_under_header' );
	}

	// Set cart icon style to drop_down since the new default is off-canvas
	if ( 'default' === get_theme_mod( 'woo_menu_icon_style', 'default' ) ) {
		set_theme_mod( 'woo_menu_icon_style', 'drop_down' );
	}

	// Breadcrumbs trail title is now disabled by default
	if ( 'default' === get_theme_mod( 'breadcrumbs_show_trail_end', 'default' ) ) {
		set_theme_mod( 'breadcrumbs_show_trail_end', true );
	}

	// Favicons panel is now disabled by default
	if ( 'default' === get_theme_mod( 'favicons_enable', 'default' ) ) {
		set_theme_mod( 'favicons_enable', true );
	}

	// VC tab animations disabled by default
	if ( 'default' === get_theme_mod( 'vc_tta_animation_enable', 'default' ) ) {
		set_theme_mod( 'vc_tta_animation_enable', true );
	}

	// If using a flex header we need to disable header menu search/cart icons
	$header_style = get_theme_mod( 'header_style', 'one' );
	if ( 'seven' === $header_style || 'eight' === $header_style || 'nine' === $header_style || 'ten' === $header_style ) {
		set_theme_mod( 'menu_search_enable', false );
		set_theme_mod( 'woo_menu_cart_enable', false );
	}

}

/**
 * 6.1
 */
function totaltheme_update_61() {
	if ( get_theme_mods() ) {
		// Disable elementor compatibility for people updating
		set_theme_mod( 'elementor_container_compat', 0 );
		// Move the old remove_posttype_slugs setting to each cpt
		if ( get_theme_mod( 'remove_posttype_slugs', false ) ) {
			set_theme_mod( 'portfolio_slug', 0 );
			set_theme_mod( 'staff_slug', 0 );
			set_theme_mod( 'testimonials_slug', 0 );
		}
	}
}


/**
 * 6.3
 */
function totaltheme_update_63() {
	if ( get_theme_mods() ) {
		if ( $header_search_input = get_theme_mod( 'menu_search_placeholder' ) ) {
			$modal_settings = [
				'input_placeholder' => sanitize_text_field( $header_search_input ),
			];
			set_theme_mod( 'search_modal', $modal_settings );
		}
	}
}