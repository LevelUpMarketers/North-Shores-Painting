<?php

defined( 'ABSPATH' ) || exit;

/*-----------------------------------------------------------------------------------*/
/* - Deprecated constants
/*-----------------------------------------------------------------------------------*/
define( 'WPEX_THEME_STYLE_HANGLE', WPEX_THEME_STYLE_HANDLE );
define( 'WPEX_THEME_JS_HANGLE', WPEX_THEME_JS_HANDLE );
define( 'WPEX_WOOCOMMERCE_ACTIVE', class_exists( 'WooCommerce', false ) );

/*-----------------------------------------------------------------------------------*/
/*  - Renamed functions
/*-----------------------------------------------------------------------------------*/
function wpex_header_has_menu() {
	return totaltheme_call_static( 'Header\Menu', 'is_enabled' );
}
function wpex_header_has_mobile_menu() {
	return wpex_has_header_mobile_menu();
}
function wpex_post_has_slider( $post_id = '' ) {
	return wpex_has_post_slider( $post_id );
}
function wpex_post_has_gallery( $post_id = '' ) {
	return wpex_has_post_gallery( $post_id );
}
function wpex_pagejump( $pages = '', $range = 4, $echo = true ) {
	if ( $echo ) {
		echo wpex_get_archive_next_prev_links( $pages, $range );
	} else {
		return wpex_get_archive_next_prev_links( $pages, $range );
	}
}
function wpex_post_has_vc_content( $post_id = null ) {
	return wpex_has_post_wpbakery_content( $post_id );
}
function wpex_get_awesome_icons( $return = 'all', $default = 'none' ) {
	_deprecated_function( 'wpex_get_awesome_icons', 'Total 6.0' );
}
function wpex_has_footer_builder() {
	return totaltheme_call_static( 'Footer\Core', 'is_custom' );
}
function wpex_display_callout() {
	return wpex_has_callout();
}
function wpex_display_page_header() {
	wpex_page_header();
}
function wpex_display_page_header_title() {
	wpex_page_header_title();
}

function wpex_header_layout() {}

function wpex_toggle_bar_active() {
	return wpex_has_togglebar();
}

function wpex_toggle_bar_btn() {
	return wpex_toggle_bar_button();
}

function wpex_post_layout() {
	echo wpex_content_area_layout();
}

function wpex_get_post_layout_class() {
	return wpex_content_area_layout();
}
function wpex_overlay_classname() {
	return wpex_overlay_classes();
}
function wpex_img_animation_classes() {
	return wpex_entry_image_animation_classes();
}
function wpex_post_entry_author_avatar_enabled() {
	return get_theme_mod( 'blog_entry_author_avatar' );
}
function wpex_has_menu_search() {
	return true;
}
function wpex_single_meta_blocks() {
	return wpex_meta_blocks();
}
function gds_get_star_rating( $rating = '', $post_id = '' ) {
	return wpex_get_star_rating( $rating, $post_id );
}
function wpex_get_the_id() {
	return wpex_get_current_post_id();
}
function wpex_get_header_logo_width() {
	return wpex_header_logo_img_width();
}
function wpex_get_header_logo_height() {
	return wpex_header_logo_img_height();
}
function wpex_get_post_layout() {
	return wpex_content_area_layout();
}
function wpex_get_page_subheading() {
	return wpex_page_header_subheading_content();
}
function wpex_is_front_end_composer() {
	_deprecated_function( 'wpex_is_front_end_composer', 'Total Theme 6.0', 'totaltheme_is_wpb_frontend_editor' );
	return totaltheme_is_wpb_frontend_editor();
}
function wpex_breadcrumbs( $post_id = '' ) {
	$breadcrumbs = new WPEX_Breadcrumbs();
	$breadcrumbs->display();
}
function wpex_has_footer_widgets() {
	return wpex_footer_has_widgets();
}
function wpex_display_footer_widgets() {
	return wpex_footer_has_widgets();
}
function wpex_topbar_output() {
	return wpex_topbar_content();
}
function wpex_top_bar_classes() {
	return wpex_topbar_content_classes();
}
function wpex_footer_reveal_enabled( $post_id = '' ) {
	return wpex_footer_has_reveal( $post_id );
}
function wpex_footer_has_reveal( $post_id = '' ) {
	return wpex_has_footer_reveal();
}
function wpex_page_header_background_image_meta() {
	return wpex_page_header_background_image();
}
function wpex_toggle_bar_classes() {
	return wpex_togglebar_classes();
}
function wpex_portfolio_post_blocks() {
	return wpex_portfolio_single_blocks();
}
function wpex_staff_post_blocks() {
	return wpex_staff_single_blocks();
}
function wpex_ilightbox_stylesheet( $skin = null ) {
	return '';
}
function wpex_get_meta_awesome_icons() {
	return [];
}
function wpex_get_mobile_menu_extra_icons() {
	ob_start();
		wpex_mobile_menu_toggle_extra_icons();
	return ob_get_clean();

}
function wpex_get_social_items() {
	return wpex_social_share_items();
}
function wpex_has_callout() {
	return wpex_has_footer_callout();
}
function wpex_callout_content() {
	ob_start();
		wpex_footer_callout_content();
	return ob_get_clean();
}
function wpex_top_bar_content() {
	wpex_topbar_content();
}

/*-----------------------------------------------------------------------------------*/
/*  - Completely Deprecated functions
/*-----------------------------------------------------------------------------------*/

function wpex_get_mods() {
	_deprecated_function( 'wpex_get_mods', 'Total 5.0', 'get_theme_mods' );
}
function wpex_header_search_placeholder() {
	_deprecated_function( 'wpex_header_search_placeholder', 'Total 3.0.0' );
}
function wpex_option() {
	_deprecated_function( 'wpex_option', 'Total 1.6.0', 'wpex_get_mod' );
}
function wpex_image() {
	_deprecated_function( 'wpex_image', 'Total 2.0.0', 'wpex_get_post_thumbnail' );
}
function wpex_mobile_menu() {
	_deprecated_function( 'wpex_mobile_menu', 'Total 2.0.0', 'wpex_mobile_menu_icons' );
}
function wpex_post_has_composer() {
	_deprecated_function( 'wpex_post_has_composer', 'Total 2.0.0', 'wpex_has_composer' );
}
function wpex_display_header() {
	_deprecated_function( 'wpex_display_header', 'Total 2.0.0', 'wpex_has_header' );
}
function wpex_display_footer() {
	_deprecated_function( 'wpex_display_footer', 'Total 2.0.0', 'wpex_has_footer' );
}
function wpex_page_title() {
	_deprecated_function( 'wpex_page_title', 'Total 2.0.0', 'wpex_title' );
}
function wpex_post_subheading() {
	_deprecated_function( 'wpex_post_subheading', 'Total 2.0.0', 'wpex_page_header_subheading' );
}
function wpex_hook_header_before_default() {
	_deprecated_function( 'wpex_hook_header_before_default', 'Total 2.0.0' );
}
function wpex_hook_header_inner_default() {
	_deprecated_function( 'wpex_hook_header_inner_default', 'Total 2.0.0' );
}
function wpex_hook_header_bottom_default() {
	_deprecated_function( 'wpex_hook_header_bottom_default', 'Total 2.0.0' );
}
function wpex_hook_main_top_default() {
	_deprecated_function( 'wpex_hook_main_top_default', 'Total 2.0.0' );
}
function wpex_hook_sidebar_inner_default() {
	_deprecated_function( 'wpex_hook_sidebar_inner_default', 'Total 2.0.0' );
}
function wpex_hook_footer_before_default() {
	_deprecated_function( 'wpex_hook_footer_before_default', 'Total 2.0.0' );
}
function wpex_hook_footer_inner_default() {
	_deprecated_function( 'wpex_hook_footer_inner', 'Total 2.0.0' );
}
function wpex_hook_footer_after_default() {
	_deprecated_function( 'wpex_hook_footer_after', 'Total 2.0.0' );
}
function wpex_hook_wrap_after_default() {
	_deprecated_function( 'wpex_hook_wrap_after_default', 'Total 2.0.0' );
}
function wpex_theme_setup() {
	_deprecated_function( 'wpex_theme_setup', 'Total 1.6.0' );
}
function wpex_active_post_types() {
	_deprecated_function( 'wpex_active_post_types', 'Total 1.6.0' );
}
function wpex_jpeg_quality() {
	_deprecated_function( 'wpex_jpeg_quality', 'Total 1.6.0' );
}
function wpex_favicons() {
	_deprecated_function( 'wpex_favicons', 'Total 1.6.0' );
}
function wpex_get_woo_product_first_cat() {
	_deprecated_function( 'wpex_get_woo_product_first_cat', 'Total 1.6.0' );
}
function wpex_global_config() {
	_deprecated_function( 'wpex_global_config', 'Total 1.6.0' );
}
function wpex_ie8_css() {
	_deprecated_function( 'wpex_ie8_css', 'Total 1.6.0' );
}
function wpex_html5() {
	_deprecated_function( 'wpex_html5', 'Total 1.6.0' );
}
function wpex_load_scripts() {
	_deprecated_function( 'wpex_load_scripts', 'Total 1.6.0' );
}
function wpex_remove_wp_ver_css_js() {
	_deprecated_function( 'wpex_remove_wp_ver_css_js', 'Total 1.6.0' );
}
function wpex_output_css() {
	_deprecated_function( 'wpex_output_css', 'Total 1.6.0' );
}
function wpex_header_output() {
	_deprecated_function( 'wpex_header_output', 'Total 1.6.0', 'wpex_header_layout' );
}
function wpex_footer_copyright() {
	_deprecated_function( 'wpex_footer_copyright', 'Total 1.6.0', 'get_template_part' );
}
function wpex_top_bar_social() {
	_deprecated_function( 'wpex_top_bar_social', 'Total 1.6.0', 'get_template_part' );
}
function wpex_portfolio_single_media() {
	_deprecated_function( 'wpex_portfolio_single_media', 'Total 1.6.0', 'get_template_part' );
}
function wpex_portfolio_related() {
	_deprecated_function( 'wpex_portfolio_related', 'Total 1.6.0', 'get_template_part' );
}
function wpex_staff_entry_media() {
	_deprecated_function( 'wpex_staff_entry_media', 'Total 1.6.0', 'get_template_part' );
}
function wpex_staff_related() {
	_deprecated_function( 'wpex_staff_related', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_related() {
	_deprecated_function( 'wpex_blog_related', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_entry_display() {
	_deprecated_function( 'wpex_blog_entry_display', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_entry_image() {
	_deprecated_function( 'wpex_blog_entry_image', 'Total 1.6.0', 'get_template_part' );
}
function wpex_post_entry_author_avatar() {
	_deprecated_function( 'wpex_post_entry_author_avatar', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_entry_title() {
	_deprecated_function( 'wpex_blog_entry_title', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_entry_header() {
	_deprecated_function( 'wpex_blog_entry_header', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_entry_content() {
	_deprecated_function( 'wpex_blog_entry_content', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_entry_media() {
	_deprecated_function( 'wpex_blog_entry_media', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_entry_link_format_image() {
	_deprecated_function( 'wpex_blog_entry_link_format_image', 'Total 1.6.0', 'get_template_part' );
}
function wpex_post_readmore_link() {
	_deprecated_function( 'wpex_post_readmore_link', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_entry_video() {
	_deprecated_function( 'wpex_blog_entry_video', 'Total 1.6.0', 'get_template_part' );
}
function wpex_blog_entry_audio() {
	_deprecated_function( 'wpex_blog_entry_audio', 'Total 1.6.0', 'get_template_part' );
}
function wpex_post_meta() {
	_deprecated_function( 'wpex_post_meta', 'Total 1.6.0', 'get_template_part' );
}
function wpex_post_entry_classes() {
	_deprecated_function( 'wpex_post_entry_classes', 'Total 1.6.0' );
}
function vcex_advanced_parallax() {
	_deprecated_function( 'vcex_advanced_parallax', 'Total 2.0.2', 'vcex_parallax_bg' );
}
function vcex_front_end_carousel_js() {
	_deprecated_function( 'vcex_front_end_carousel_js', 'Total 2.0.0', 'vcex_inline_js' );
}
function wpex_breadcrumbs_get_parents() {
	_deprecated_function( 'wpex_breadcrumbs_get_parents', 'Total 3.0.9' );
}
function wpex_breadcrumbs_get_term_parents() {
	_deprecated_function( 'wpex_breadcrumbs_get_term_parents', 'Total 3.0.9' );
}
function wpex_vc_column_inner_extra_attributes() {
	_deprecated_function( 'wpex_vc_column_inner_extra_attributes', 'Total 4.1' );
}
function wpex_global_obj() {
	_deprecated_function( 'wpex_get_mods', '4.0' );
}
function wpex_landing_page_remove_actions() {
	_deprecated_function( 'wpex_landing_page_remove_actions', 'Total 5.0' );
}
function wpex_element() {
	_deprecated_function( 'wpex_element', 'Total 5.0' );
}
function wpex_is_mod_enabled() {
	_deprecated_function( 'wpex_validate_boolean', 'Total 5.0' );
}
function wpex_portfolio_first_cat() {
	_deprecated_function( 'wpex_get_first_term', 'Total 5.0' );
}
function wpex_portfolio_cats() {
	_deprecated_function( 'wpex_list_post_terms', 'Total 5.0' );
}
function wpex_enqueue_archive_scripts() {
	_deprecated_function( 'wpex_enqueue_archive_scripts', 'Total 5.0' );
}
function wpex_posts_columns() {
	_deprecated_function( 'wpex_posts_columns', 'Total 5.0.6' );
}
function wpex_posts_custom_columns() {
	_deprecated_function( 'wpex_posts_custom_columns', 'Total 5.0.6' );
}
function wpex_vc_add_params() {
	_deprecated_function( 'wpex_vc_add_params', 'Total 5.1.1' );
}
function wpex_vc_modify_params() {
	_deprecated_function( 'wpex_vc_modify_params', 'Total 5.1.1' );
}
function wpex_portfolio_style_supports_equal_heights() {
	_deprecated_function( 'wpex_portfolio_style_supports_equal_heights', 'Total 5.1.1' );
}
function wpex_page_header_title_table_wrap_open() {
	_deprecated_function( 'wpex_page_header_title_table_wrap_open', 'Total 5.1.1' );
}
function wpex_page_header_title_table_wrap_close() {
	_deprecated_function( 'wpex_page_header_title_table_wrap_close', 'Total 5.1.1' );
}
function wpex_envato_hosted() {
	_deprecated_function( 'wpex_envato_hosted', 'Total 5.1.1' );
}
function wpex_portfolio_entry_terms() {
	_deprecated_function( 'wpex_portfolio_entry_terms', 'Total 5.1.1' );
}
function wpex_enqueue_font_awesome_in_admin() {
	_deprecated_function( 'wpex_enqueue_font_awesome_in_admin', 'Total 5.1.1' );
}
function wpex_custom_widgets_list() {
	_deprecated_function( 'wpex_custom_widgets_list', 'Total 5.1.1' );
	return [];
}
function wpex_social_sharing_supports_heading() {
	_deprecated_function( 'wpex_social_sharing_supports_heading', 'Total 5.1.1', 'wpex_has_social_share_heading' );
	return wpex_has_social_share_heading();
}
function wpex_string_to_array( $list ) {
	_deprecated_function( 'wpex_string_to_array', 'Total 5.1.1', 'wp_parse_list' );
	return wp_parse_list( $list );
}
function wpex_sidr_close() {
	_deprecated_function( 'wpex_sidr_close', 'Total 5.1.1' );
}
function vcex_parse_deprecated_row_css() {
	_deprecated_function( 'vcex_parse_deprecated_row_css', 'Total 5.1.1' );
}
function vcex_offset_vc() {
	_deprecated_function( 'vcex_offset_vc', 'Total 5.1.1' );
}

/*-----------------------------------------------------------------------------------*/
/*  4.9.3 Lightbox switch
/*-----------------------------------------------------------------------------------*/
function wpex_enqueue_ilightbox_scripts() {
	_deprecated_function( 'wpex_enqueue_ilightbox_scripts', 'Total 4.9.3', 'wpex_enqueue_ilightbox_scripts' );
	wpex_enqueue_lightbox_scripts();
}
function wpex_enqueue_ilightbox_skin() {
	_deprecated_function( 'wpex_enqueue_ilightbox_skin', 'Total 4.9.3', 'wpex_enqueue_lightbox_scripts' );
	wpex_enqueue_lightbox_scripts();
}
function wpex_ilightbox_skins() {
	_deprecated_function( 'wpex_ilightbox_skins', 'Total 4.9.3' );
	return [];
}
function wpex_ilightbox_skin() {
	_deprecated_function( 'wpex_ilightbox_skin', 'Total 4.9.3' );
	return 'default';
}

/*-----------------------------------------------------------------------------------*/
/*  5.1
/*-----------------------------------------------------------------------------------*/
function wpex_header_logo_img_retina_js() {
	_deprecated_function( 'wpex_header_logo_img_retina_js', 'Total 5.1' );
}
function wpex_register_admin_scripts() {
	_deprecated_function( 'wpex_register_admin_scripts', 'Total 5.1' );
}
function wpex_ticons_admin_enqueue( $hook ) {
	_deprecated_function( 'wpex_ticons_admin_enqueue', 'Total 5.1' );
}
function wpex_custom_excerpt_length() {
	_deprecated_function( 'wpex_custom_excerpt_length', 'Total 5.1' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.1.2
/*-----------------------------------------------------------------------------------*/
function wpex_grid_class( $columns = '4' ) {
	_deprecated_function( 'wpex_grid_class', 'Total 5.1.2', 'wpex_row_column_width_class' );
	return wpex_row_column_width_class( $columns );
}
function vcex_parallax_bg( $atts = array() ) {
	_deprecated_function( 'vcex_parallax_bg', 'Total 5.1.2', 'TotalTheme\Integration\WPBakery::render_parallax_bg' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.3.1
/*-----------------------------------------------------------------------------------*/
function wpex_header_logo_img_retina_height() {
	_deprecated_function( 'wpex_custom_excerpt_length', 'Total 5.3.1' );
}
function wpex_overlay_header_logo_img_retina_height() {
	_deprecated_function( 'wpex_overlay_header_logo_img_retina_height', 'Total 5.3.1' );
}
function wpex_sticky_header_logo_img_retina_height() {
	_deprecated_function( 'wpex_sticky_header_logo_img_retina_height', 'Total 5.3.1' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.4
/*-----------------------------------------------------------------------------------*/
function wpex_blank_img_src() {
	_deprecated_function( 'wpex_blank_img_src', '5.4' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.4
/*-----------------------------------------------------------------------------------*/
function wpex_woocommerce_before_add_to_cart_button_open_flex_wrap() {
	_deprecated_function( 'wpex_woocommerce_before_add_to_cart_button_open_flex_wrap', '5.4' );
}
function wpex_woocommerce_before_add_to_cart_button_close_flex_wrap() {
	_deprecated_function( 'wpex_woocommerce_before_add_to_cart_button_close_flex_wrap', '5.4' );
}
function wpex_js_localize_data() {
	_deprecated_function( 'wpex_js_localize_data', '5.4' );
}
function wpex_register_scripts() {
	_deprecated_function( 'wpex_register_scripts', '5.4', 'TotalTheme\Scripts\JS\::register' );
}
function wpex_enqueue_front_end_main_css() {
	_deprecated_function( 'wpex_enqueue_front_end_main_css', '5.4', 'TotalTheme\Scripts\CSS\::enqueue' );
}
function wpex_enqueue_front_end_js() {
	_deprecated_function( 'wpex_enqueue_front_end_js', '5.4', 'TotalTheme\Initialize\::scripts' );
}
function wpex_body_class() {
	_deprecated_function( 'wpex_body_class', '5.4', 'TotalTheme\Body_Class::add_classes' );
}
function wpex_widget_tag_cloud_args() {
	_deprecated_function( 'wpex_widget_tag_cloud_args', '5.4', 'TotalTheme\Widgets\Tag_Cloud::args' );
}
function wpex_post_class() {
	_deprecated_function( 'wpex_post_class', '5.4', 'TotalTheme\Post_Class::add_classes' );
}
function wpex_pre_get_posts( $query ) {
	_deprecated_function( 'wpex_pre_get_posts', '5.4', 'TotalTheme\Pre_Get_Posts::set_query' );
}
function wpex_post_redirect() {
	_deprecated_function( 'wpex_post_redirect', '5.4', 'TotalTheme\Redirections::maybe_redirect' );
}
function wpex_site_overlay() {
	_deprecated_function( 'wpex_site_overlay', '5.4' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.4.2
/*-----------------------------------------------------------------------------------*/
function wpex_blog_single_layout_blocks() {
	return totaltheme_call_static( 'Blog\Single_Blocks', 'get' );
}
function wpex_portfolio_single_blocks() {
	return totaltheme_call_static( 'Portfolio\Single_Blocks', 'get' );
}
function wpex_staff_single_blocks() {
	return totaltheme_call_static( 'Staff\Single_Blocks', 'get' );
}

function wpex_page_single_blocks_class() {
	return totaltheme_call_static( 'Page\Single_Blocks', 'wrapper_class' );
}
function wpex_blog_single_blocks_class() {
	return totaltheme_call_static( 'Blog\Single_Blocks', 'wrapper_class' );
}
function wpex_portfolio_single_blocks_class() {
	return totaltheme_call_static( 'Portfolio\Single_Blocks', 'wrapper_class' );
}
function wpex_staff_single_blocks_class() {
	return totaltheme_call_static( 'Staff\Single_Blocks', 'wrapper_class' );
}
function wpex_cpt_single_blocks_class() {
	return totaltheme_call_static( 'CPT\Single_Blocks', 'wrapper_class' );
}

function wpex_blog_entry_layout_blocks() {
	return totaltheme_call_static( 'Blog\Entry_Blocks', 'get' );
}
function wpex_entry_blocks() {
	return totaltheme_call_static( 'CPT\Entry_Blocks', 'get' );
}
function wpex_meta_blocks() {
	return totaltheme_call_static( 'CPT\Meta_Blocks', 'get' );
}

function wpex_blog_entry_meta_sections() {
	return totaltheme_call_static( 'Blog\Meta_Blocks', 'get', false );
}
function wpex_blog_single_meta_sections() {
	return totaltheme_call_static( 'Blog\Meta_Blocks', 'get', true );
}
function wpex_portfolio_single_meta_sections() {
	return totaltheme_call_static( 'Portfolio\Meta_Blocks', 'get', true );
}
function wpex_staff_single_meta_sections() {
	return totaltheme_call_static( 'Staff\Meta_Blocks', 'get', true );
}

function wpex_cpt_meta_class( $is_custom = false ) {
	return totaltheme_call_static( 'CPT\Meta_Blocks', 'wrapper_class', $is_custom );
}
function wpex_blog_entry_meta_class() {
	return totaltheme_call_static( 'Blog\Meta_Blocks', 'wrapper_class', false );
}
function wpex_blog_single_meta_class() {
	return totaltheme_call_static( 'Blog\Meta_Blocks', 'wrapper_class', true );
}
function wpex_portfolio_single_meta_class() {
	return totaltheme_call_static( 'Portfolio\Meta_Blocks', 'wrapper_class', true );
}
function wpex_staff_single_meta_class() {
	return totaltheme_call_static( 'Staff\Meta_Blocks', 'wrapper_class', true );
}

/*-----------------------------------------------------------------------------------*/
/*  5.4.3
/*-----------------------------------------------------------------------------------*/

// Converted logo functions into a singular class.
function wpex_header_logo_img( $parse_logo = true ) {
	if ( $parse_logo ) {
		return totaltheme_call_static( 'Header\Logo', 'get_image_url' );
	} else {
		return totaltheme_call_static( 'Header\Logo', 'get_image_id' );
	}
}
function wpex_header_logo_text() {
	return totaltheme_call_static( 'Header\Logo', 'get_text' );
}
function wpex_header_logo_title() {
	return totaltheme_call_static( 'Header\Logo', 'get_text' );
}
function wpex_header_logo_img_retina() {
	return totaltheme_call_static( 'Header\Logo', 'get_retina_image_url' );
}
function wpex_header_logo_icon() {
	return totaltheme_call_static( 'Header\Logo', 'get_icon' );
}
function wpex_header_logo_class() {
	totaltheme_call_static( 'Header\Logo', 'wrapper_class' );
}
function wpex_header_logo_classes() {
	return totaltheme_call_static( 'Header\Logo', 'getget_wrapper_classes_icon' );
}
function wpex_header_logo_inner_class() {
	return totaltheme_call_static( 'Header\Logo', 'inner_class' );
}
function wpex_header_logo_img_src() {
	return totaltheme_call_static( 'Header\Logo', 'get_image_src' );
}
function wpex_header_logo_img_class() {
	return totaltheme_call_static( 'Header\Logo', 'get_image_class' );
}
function wpex_header_has_text_logo() {
	return ! totaltheme_call_static( 'Header\Logo', 'get_image_url' );
}
function wpex_header_logo_img_width() {
	return totaltheme_call_static( 'Header\Logo', 'get_image_width' );
}
function wpex_header_logo_img_height() {
	return totaltheme_call_static( 'Header\Logo', 'get_image_height' );
}
function wpex_header_logo_img_is_svg() {
	return totaltheme_call_static( 'Header\Logo', 'is_image_svg' );
}
function wpex_header_logo_txt_class() {
	return totaltheme_call_static( 'Header\Logo', 'get_text_class' );
}
function wpex_header_logo_url() {
	return totaltheme_call_static( 'Header\Logo', 'get_link_url' );
}
function wpex_header_logo_scroll_top() {
	return totaltheme_call_static( 'Header\Logo', 'has_scroll_top_link' );
}
function wpex_header_inner_class() {
	return totaltheme_call_static( 'Header\Core', 'inner_class' );
}
function wpex_header_has_fixed_height( $header_style = 'deprecated' ) {
	return totaltheme_call_static( 'Header\Core', 'has_fixed_height' );
}
function wpex_has_flex_header() {
	return totaltheme_call_static( 'Header\Core', 'has_flex_container' );
}
function wpex_header_classes() {
	return totaltheme_call_static( 'Header\Core', 'get_wrapper_classes' );
}
function wpex_header_background_image() {
	return totaltheme_call_static( 'Header\Core', 'get_background_image_url' );
}
function wpex_header_menu_mobile_style() {
	return totaltheme_call_static( 'Mobile\Menu', 'style' );
}
function wpex_header_style( $post_id = 'deprecated' ) {
	return totaltheme_call_static( 'Header\Core', 'style' );
}
function wpex_has_header_mobile_menu() {
	return totaltheme_call_static( 'Mobile\Menu', 'is_enabled' );
}
function wpex_header_menu_mobile_breakpoint() {
	return totaltheme_call_static( 'Mobile\Menu', 'breakpoint' );
}
function wpex_overlay_header_style() {
	return totaltheme_call_static( 'Header\Overlay', 'style' );
}
function wpex_is_overlay_header_global() {
	return totaltheme_call_static( 'Header\Overlay', 'is_global' );
}
function wpex_overlay_header_logo_img( $parse_logo = true ) {
	return totaltheme_call_static( 'Header\Overlay', 'logo_img', $parse_logo );
}
function wpex_overlay_header_logo_img_retina() {
	return totaltheme_call_static( 'Header\Overlay', 'logo_img_retina' );
}
function wpex_has_overlay_header() {
	return totaltheme_call_static( 'Header\Overlay', 'is_enabled' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.4.5
/*-----------------------------------------------------------------------------------*/

// header aside
function wpex_header_aside_class() {
	return totaltheme_call_static( 'Header\Aside', 'wrapper_class' );
	
}
function wpex_header_supports_aside( $header_style = 'deprecated' ) {
	return totaltheme_call_static( 'Header\Aside', 'is_supported' );
}
function wpex_header_aside_content() {
	return totaltheme_call_static( 'Header\Aside', 'get_content' );
}
function wpex_get_header_styles_with_aside_support() {
	return totaltheme_call_static( 'Header\Aside', 'supported_header_styles' );
}

// topbar
function wpex_has_topbar( $post_id = 'deprecated' ) {
	return totaltheme_call_static( 'Topbar\Core', 'is_enabled' );
}
function wpex_topbar_style() {
	return totaltheme_call_static( 'Topbar\Core', 'style' );
}
function wpex_topbar_split_breakpoint() {
	return totaltheme_call_static( 'Topbar\Core', 'breakpoint' );
}
function wpex_topbar_is_fullwidth() {
	return totaltheme_call_static( 'Topbar\Core', 'is_fullwidth' );
}
function wpex_has_topbar_content() {
	return totaltheme_call_static( 'Topbar\Core', 'has_content' );
}
function wpex_topbar_wrap_class() {
	return totaltheme_call_static( 'Topbar\Core', 'wrapper_class' );
}
function wpex_topbar_classes() {
	return totaltheme_call_static( 'Topbar\Core', 'get_wrapper_classes' );
}
function wpex_topbar_class() {
	return totaltheme_call_static( 'Topbar\Core', 'inner_class' );
}
function wpex_topbar_content( $deprecated = '' ) {
	return totaltheme_call_static( 'Topbar\Core', 'get_content' );
}
function wpex_topbar_content_class() {
	return totaltheme_call_static( 'Topbar\Core', 'content_class' );
}
function wpex_topbar_content_classes() {
	return totaltheme_call_static( 'Topbar\Core', 'get_content_classes' );
}
function wpex_has_topbar_social() {
	return totaltheme_call_static( 'Topbar\Social', 'is_enabled' );
}
function wpex_topbar_social_alt_content( $deprecated = '' ) {
	return totaltheme_call_static( 'Topbar\Social', 'get_alt_content' );
}
function wpex_topbar_social_style() {
	totaltheme_call_static( 'Topbar\Social', 'get_icon_style' );
}
function wpex_topbar_social_class() {
	totaltheme_call_static( 'Topbar\Social', 'wrapper_class' );
}
function wpex_topbar_social_list() {
	totaltheme_call_static( 'Topbar\Social', 'render_list' );
}
function wpex_get_topbar_social_profiles() {
	return totaltheme_call_static( 'Topbar\Social', 'get_registered_profiles' );
}
function wpex_topbar_social_options() {
	return totaltheme_call_static( 'Topbar\Social', 'get_profile_options' );
}

function wpex_infinite_scroll() {
	_deprecated_function( 'wpex_infinite_scroll', 'Total 5.4.5' );
}

function wpex_get_loadmore_text() {
	return totaltheme_call_static( 'Pagination\Load_More', 'get_more_text' );
}

function wpex_get_loadmore_loading_text() {
	return totaltheme_call_static( 'Pagination\Load_More', 'get_loading_text' );
}

function wpex_get_loadmore_failed_text() {
	return totaltheme_call_static( 'Pagination\Load_More', 'get_failed_text' );
}

function wpex_get_loadmore_gif() {
	return totaltheme_call_static( 'Pagination\Load_More', 'get_loader_gif' );
}

function wpex_enqueue_loadmore_scripts() {
	return totaltheme_call_static( 'Pagination\Load_More', 'enqueue_scripts' );
}

function wpex_loadmore( $args = array() ) {
	return totaltheme_call_static( 'Pagination\Load_More', 'render_button', $args );
}

function wpex_get_loadmore_data( $key = '' ) {
	return totaltheme_call_static( 'Pagination\Load_More', 'get_data', $key );
}

function wpex_str_starts_with( $haystack = '', $needle = '' ) {
	return str_starts_with( $haystack, $needle );
}

if ( ! function_exists( 'wpex_blog_pagination' ) ) {
	function wpex_blog_pagination( $args = array() ) {
		return totaltheme_call_static( 'Pagination\Core', 'render', 'blog' );
	}
}

function wpex_archive_next_prev_links( $query = '' ) {
	if ( class_exists( 'TotalTheme\Pagination\Next_Prev' ) ) {
		(new TotalTheme\Pagination\Next_Prev( $query ))->render();
	}
}

function wpex_get_archive_next_prev_links( $query = '' ) {
	ob_start();
		wpex_archive_next_prev_links();
	return ob_get_clean();
}

if ( ! function_exists( 'wpex_pagination' ) ) {
	function wpex_pagination( $query = '', $echo = true ) {
		_deprecated_function( 'wpex_pagination', 'Total 5.4.5', 'TotalTheme\Pagination\Standard::render' );

		if ( class_exists( 'TotalTheme\Pagination\Standard' ) ) {
			$pagination = new TotalTheme\Pagination\Standard( $query );
			if ( $echo ) {
				$pagination->render();
			} else {
				ob_start();
					$pagination->render();
				return ob_get_clean();
			}
		}
	}
}

function wpex_get_pagination() {
	_deprecated_function( 'wpex_get_pagination', 'Total 5.4.5', 'TotalTheme\Pagination\Standard::render' );
}

function wpex_has_footer_bottom( $post_id = 'deprecated' ) {
	return totaltheme_call_static( 'Footer\Bottom\Core', 'is_enabled' );
}

function wpex_footer_bottom_class() {
	totaltheme_call_static( 'Footer\Bottom\Core', 'wrapper_class' );
}

function wpex_footer_bottom_menu_class() {
	totaltheme_call_static( 'Footer\Bottom\Menu', 'wrapper_class' );
}

function wpex_vc_meta_inline_style( $id = '' ) {
	totaltheme_call_non_static( 'Integration\WPBakery\Shortcode_Inline_Style', 'render_style', $post_id );
}

function wpex_get_vc_meta_inline_style( $post_id = 0 ) {
	return totaltheme_call_non_static( 'Integration\WPBakery\Shortcode_Inline_Style', 'get_style', $post_id );
}

function wpex_has_singular_template() {
	return totaltheme_call_static( 'Theme_Builder\Post_Template', 'has_template' );
}

function wpex_get_singular_template_id( $post_type = '' ) {
	return totaltheme_call_static( 'Theme_Builder\Post_Template', 'get_template_id', $post_type );
}

function wpex_get_singular_template_content( $post_type = '' ) {
	return totaltheme_call_static( 'Theme_Builder\Post_Template', 'get_template_content', $post_type );
}

function wpex_singular_template( $template_content = '' ) {
	echo totaltheme_call_static( 'Theme_Builder\Post_Template', 'render_template', $template_content );
}

function wpex_has_sticky_header() {
	return totaltheme_call_static( 'Header\Sticky', 'is_enabled' );
}

function wpex_sticky_header_style() {
	return totaltheme_call_static( 'Header\Sticky', 'style' );
}

function wpex_sticky_header_logo_img_src() {
	return totaltheme_call_static( 'Header\Sticky', 'get_logo_image_src' );
}

function wpex_sticky_header_logo_img() {
	return totaltheme_call_static( 'Header\Sticky', 'get_logo_image_url' );
}

function wpex_sticky_header_logo_img_height() {
	return totaltheme_call_static( 'Header\Sticky', 'get_logo_image_height' );
}

function wpex_sticky_header_logo_img_width() {
	return totaltheme_call_static( 'Header\Sticky', 'get_logo_image_width' );
}

function wpex_sticky_header_logo_img_retina() {
	return totaltheme_call_static( 'Header\Sticky', 'get_retina_logo_image_url' );
}

function wpex_has_shrink_sticky_header() {
	return totaltheme_call_static( 'Header\Sticky', 'is_shrink_enabled' );
}

function wpex_has_shrink_sticky_header_mobile() {
	return totaltheme_call_static( 'Header\Sticky', 'is_shrink_enabled_mobile' );
}

function wpex_sticky_header_start_position() {
	return totaltheme_call_static( 'Header\Sticky', 'get_start_position' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.6
/*-----------------------------------------------------------------------------------*/
function wpex_header_menu_supports_search() {
	return totaltheme_call_static( 'Header\Menu\Search', 'is_supported' );
}
function wpex_header_menu_search_style() {
	return totaltheme_call_static( 'Header\Menu\Search', 'style' );
}
function wpex_get_header_menu_search_form() {
	return totaltheme_call_static( 'Header\Menu\Search', 'get_form' );
}
function wpex_get_header_menu_search_form_placeholder() {
	return totaltheme_call_static( 'Header\Menu\Search', 'get_placeholder_text' );
}
function wpex_add_search_to_menu( $items, $args ) {
	_deprecated_function( 'wpex_add_search_to_menu', '5.6' );
	return $items;
}
function wpex_header_drop_widget_search_class() {
	return totaltheme_call_static( 'Header\Menu\Search', 'drop_widget_class' );
}
function wpex_sidebar_class() {
	totaltheme_call_static( 'Sidebars\Primary', 'wrapper_class' );
}
function wpex_sidebar_inner_class() {
	return totaltheme_call_static( 'Sidebars\Primary', 'inner_class' );
}
function wpex_get_sidebar( $sidebar = '', $post_id = '' ) {
	return totaltheme_call_static( 'Sidebars\Primary', 'get_sidebar_name' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.6.1
/*-----------------------------------------------------------------------------------*/
function wpex_title( $post_id = '' ) {
	if ( $post_id ) {
		return totaltheme_call_non_static( 'Title', 'get_unfiltered_post_title', $post_id );
	}
	return totaltheme_call_non_static( 'Title', 'get' );
}

function vcex_insert_top_shape_divider() {
	_deprecated_function( 'vcex_insert_top_shape_divider', 'Total 5.6.1' );
}
function vcex_insert_bottom_shape_divider() {
	_deprecated_function( 'vcex_insert_top_shape_divider', 'Total 5.6.1' );
}
function wpex_get_shape_divider_types() {
	_deprecated_function( 'wpex_get_shape_divider_types', 'Total 5.6.1' );
}
function wpex_get_shape_divider_settings() {
	_deprecated_function( 'wpex_get_shape_divider_settings', 'Total 5.6.1' );
}
function wpex_shape_divider_rotate( $position, $type, $invert ) {
	_deprecated_function( 'wpex_shape_divider_rotate', 'Total 5.6.1' );
}
function wpex_get_shape_divider( $position = 'top', $type = 'deprecated', $atts = [] ) {
	return totaltheme_call_non_static( 'Shape_Dividers', 'get_divider', $position, $atts );
}
function wpex_get_shape_dividers_svg( $type = '', $settings = [] ) {
	return totaltheme_call_non_static( 'Shape_Dividers', 'get_svg', $type, $settings );
}

function wpex_footer_has_widgets() {
	return totaltheme_call_static( 'Footer\Widgets', 'is_enabled' );
}

function wpex_header_flex_aside_class() {
	return totaltheme_call_static( 'Header\Flex\Aside', 'wrapper_class' );
}
function wpex_header_flex_aside_content() {
	return totaltheme_call_static( 'Header\Flex\Aside', 'get_content' );
}

function wpex_has_footer_callout() {
	return totaltheme_call_static( 'Footer\Callout', 'is_enabled' );
}
function wpex_footer_callout_content() {
	if ( $content = totaltheme_call_static( 'Footer\Callout', 'get_content' ) ) {
		echo do_shortcode( wp_kses_post( $content ) );
	}
}
function wpex_footer_callout_button() {
	if ( $button = totaltheme_call_static( 'Footer\Callout', 'get_button' ) ) {
		echo wp_kses_post( $button );
	}
}
function wpex_footer_callout_wrap_class() {
	totaltheme_call_static( 'Footer\Callout', 'wrapper_class' );
}
function wpex_footer_callout_class() {
	totaltheme_call_static( 'Footer\Callout', 'inner_class' );
}
function wpex_footer_callout_left_class() {
	totaltheme_call_static( 'Footer\Callout', 'content_class' );
}
function wpex_footer_callout_right_class() {
	totaltheme_call_static( 'Footer\Callout', 'button_class' );
}
function wpex_has_footer_callout_content( ) {
	return (bool) wpex_callout_content();
}
function wpex_has_footer_callout_button( ) {
	return ( wpex_footer_callout_button_link() && wpex_footer_callout_button_text() );
}
function wpex_footer_callout_button_link() {
	return totaltheme_call_static( 'Footer\Callout', 'get_button_link' );
}
function wpex_footer_callout_button_text() {
	return totaltheme_call_static( 'Footer\Callout', 'get_button_text' );
}
function wpex_footer_callout_button_icon() {
	return totaltheme_call_static( 'Footer\Callout', 'get_button_icon' );
}

function wpex_has_footer() {
	return totaltheme_call_static( 'Footer\Core', 'is_enabled' );
}
function wpex_has_footer_reveal( $post_id = 'deprecated' ) {
	return totaltheme_call_static( 'Footer\Core', 'has_reveal' );
}
function wpex_footer_class() {
	if ( $classes = totaltheme_call_static( 'Footer\Core', 'get_wrapper_classes' ) ) {
		return implode( ' ', $classes );
	}
}
function wpex_footer_widgets_class() {
	$class = totaltheme_call_static( 'Footer\Widgets', 'get_wrapper_classes' );
	if ( $class && is_array( $class ) ) {
		$class = apply_filters( 'wpex_footer_widget_row_classes', implode( ' ', $class ) );
		return $class;
	}
}

/*-----------------------------------------------------------------------------------*/
/*  5.8.0
/*-----------------------------------------------------------------------------------*/
function wpex_has_header( $post_id = '' ) {
	return totaltheme_call_static( 'Header\Core', 'is_enabled' );
}
function wpex_has_custom_header() {
	return totaltheme_call_static( 'Header\Core', 'is_custom' );
}
function wpex_has_vertical_header() {
	return totaltheme_call_static( 'Header\Vertical', 'is_enabled' );
}
function wpex_is_header_menu_custom() {
	return totaltheme_call_static( 'Header\Menu', 'is_custom' );
}
function wpex_post_has_dynamic_template() {
	return totaltheme_call_static( 'Theme_Builder\Post_Template', 'has_template' );
}
function wpex_has_header_menu() {
	return totaltheme_call_static( 'Header\Menu', 'is_enabled' );
}
function wpex_screen_reader_text( string $text ) {
	_deprecated_function( 'wpex_screen_reader_text', 'Total 5.8' );
	echo '<span class="screen-reader-text">' . esc_html( $text ) . '</span>';
}
function wpex_user_can_access( $check, $custom_callback = '' ) {
	_deprecated_function( 'wpex_user_can_access', 'Total 5.8', 'TotalTheme\Restrict_Content::check_restriction' );
	$check = $custom_callback ?: $check;
	return totaltheme_call_non_static( 'Restrict_Content', 'check_restriction', $check );
}
function wpex_get_post_class( $class = '', $post_id = null ) {
	return 'class="' . esc_attr( implode( ' ', get_post_class( $class, $post_id ) ) ) . '"';
}
function wpex_is_tribe_events() {
	_deprecated_function( 'wpex_is_tribe_events', 'Total 5.8', 'TotalTheme\Integration\Tribe_Events::is_event_page' );
	return totaltheme_call_static( 'Integration\Tribe_Events', 'is_event_page' );
}
function wpex_is_wpbakery_enabled() {
	_deprecated_function( 'wpex_is_wpbakery_enabled', 'Total 5.8' );
	return class_exists( 'Vc_Manager', false );
}
function wpex_has_post_wpbakery_content( $post_id = null ) {
	return totaltheme_call_static( 'Integration\WPBakery\Helpers', 'post_has_wpbakery', $post_id );
}
function wpex_has_page_header() {
	return totaltheme_call_static( 'Page\Header', 'is_enabled' );
}
function wpex_page_header_breakpoint() {
	return totaltheme_call_static( 'Page\Header', 'breakpoint' );
}
function wpex_page_header_style() {
	return totaltheme_call_static( 'Page\Header', 'style' );
}
function wpex_get_global_page_header_style() {
	return totaltheme_call_static( 'Page\Header', 'global_style' );
}
function wpex_is_global_page_header_style( $style = 'deprecated' ) {
	return totaltheme_call_static( 'Page\Header', 'is_global_style' );
}
function wpex_has_page_header_title() {
	return totaltheme_call_static( 'Page\Header', 'has_title' );
}
function wpex_page_header_has_subheading() {
	return totaltheme_call_static( 'Page\Header', 'has_subheading' );
}
function wpex_page_header_subheading_content() {
	return totaltheme_call_static( 'Page\Header', 'get_subheading' );
}
function wpex_choices_dynamic_templates() {
	_deprecated_function( 'wpex_choices_dynamic_templates', 'Total 5.8' );
	return [];
}

/*-----------------------------------------------------------------------------------*/
/*  5.8.1
/*-----------------------------------------------------------------------------------*/
function wpex_parse_vc_content( $content = '' ) {
	_deprecated_function( 'wpex_parse_vc_content', 'Total 5.8.1', 'totaltheme_shortcode_unautop' );
	return totaltheme_shortcode_unautop( $content );
}
function wpex_header_menu_location() {
	_deprecated_function( 'wpex_header_menu_location', 'Total 5.8.1', 'TotalTheme\Header\Menu::get_theme_location' );
	return totaltheme_call_static( 'Header\Menu', 'get_theme_location' );
}
function wpex_custom_menu() {
	_deprecated_function( 'wpex_custom_menu', 'Total 5.8.1', 'TotalTheme\Header\Menu::get_wp_menu' );
	return totaltheme_call_static( 'Header\Menu', 'get_wp_menu' );
}
function wpex_header_overlay_styles() {
	_deprecated_function( 'wpex_header_overlay_styles', 'Total 5.8.1', 'TotalTheme\Header\Overlay::style_choices' );
	totaltheme_call_static( 'Header\Overlay', 'style_choices' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.10
/*-----------------------------------------------------------------------------------*/
function wpex_aria_landmark( $location ) {
	return '';
}
function wpex_esc_attr( $val = null, $fallback = null ) {
	return esc_attr( $val ) ?: $fallback;
}
function wpex_esc_html( $val = null, $fallback = null ) {
	return esc_html( $val ) ?: $fallback;
}
function wpex_intval( $val = null, $fallback = null ) {
	return intval( $val ) ?: $fallback;
}
function wpex_overlay_styles_array() {
	return (array) totaltheme_call_static( 'Overlays', 'get_style_choices' );
}
if ( ! function_exists( 'wpex_overlay_style' ) ) {
	function wpex_overlay_style( $post_type = '' ) {
		return totaltheme_call_static( 'Overlays', 'get_entry_image_overlay_style', $post_type );
	}
}
function wpex_blog_entry_overlay_style() {
	return totaltheme_call_static( 'Overlays', 'get_entry_image_overlay_style', 'post' );
}
if ( ! function_exists( 'wpex_overlay_classes' ) ) {
	function wpex_overlay_classes( $style = '' ) {
		return (string) totaltheme_call_static( 'Overlays', 'get_parent_class', $style );
	}
}
if ( ! function_exists( 'wpex_overlay' ) ) {
	function wpex_overlay( string $position = 'inside_link', string $style = '', array $args = [] ) {
		if ( empty( $style ) ) {
			$style = totaltheme_call_static(
				'Overlays',
				'get_entry_image_overlay_style'
			);
		}
		return totaltheme_render_overlay( $position, $style, $args );
	}
}
function wpex_overlay_speed( $style, $speed = '' ) {
	return totaltheme_get_overlay_speed( $speed );
}
function wpex_overlay_bg( $style, $bg = '' ) {
	return totaltheme_get_overlay_bg_color( $bg );
}
function wpex_overlay_opacity( $style, $opacity = '' ) {
	return totaltheme_get_overlay_opacity( $opacity );
}
function wpex_has_woo_mods() {
	return (bool) totaltheme_call_static( 'Integration\WooCommerce', 'is_advanced_mode' );
}

/*-----------------------------------------------------------------------------------*/
/*  5.10.1
/*-----------------------------------------------------------------------------------*/
function wpex_get_mod( $id, $default = '', $not_empty = false ) {
	$value = get_theme_mod( $id, $default );
	return ( $not_empty && ! $value ) ? $default : $value;
}
function wpex_schema_markup() {}
function wpex_get_schema_markup() {}

/*-----------------------------------------------------------------------------------*/
/* 6.0
/*-----------------------------------------------------------------------------------*/

// Soft deprecated.
function wpex_get_first_term_name( $post = '', $taxonomy = '', $terms = 'deprecated' ) {
	if ( $primary_term = totaltheme_get_post_primary_term( $post, $taxonomy ) ) {
		return $primary_term->name ?? '';
	}
}
function wpex_blog_style() {
	return wpex_blog_entry_style();
}
function wpex_get_term_color_class( $term = '' ) {
	return totaltheme_get_term_color_classname( $term ?: totaltheme_get_post_primary_term() );
}
function wpex_get_term_background_color_class( $term = '' ) {
	return totaltheme_get_term_color_background_classname( $term ?: totaltheme_get_post_primary_term() );
}
function wpex_get_post_primary_term( $post = '', $taxonomy = '' ) {
	return totaltheme_get_post_primary_term( $post, $taxonomy, false );
}
function wpex_get_post_first_term( $post = '', $taxonomy = '' ) {
	return totaltheme_get_post_primary_term( $post, $taxonomy );
}

// Hard deprecated.
function wpex_blog_entry_quote_icon_class() {}
function wpex_parse_link_target() {}
function wpex_has_post_edit() {
	_deprecated_function( 'wpex_has_post_edit', 'Total 6.0' );
}
function wpex_header_menu_cart_style() {
	_deprecated_function( 'wpex_header_menu_cart_style', 'Total 6.0' );
}
function wpex_wcmenucart_menu_item() {
	_deprecated_function( 'wpex_wcmenucart_menu_item', 'Total 6.0' );
}
function wpex_get_mobile_menu_cart_link() {
	_deprecated_function( 'wpex_get_mobile_menu_cart_link', 'Total 6.0' );
}
function wpex_cart_overlay_html() {
	_deprecated_function( 'wpex_cart_overlay_html', 'Total 6.0' );
}
function wpex_add_cart_dropdown_html() {
	_deprecated_function( 'wpex_add_cart_dropdown_html', 'Total 6.0' );
}
function wpex_mobile_menu_cart_count() {
	_deprecated_function( 'wpex_mobile_menu_cart_count', 'Total 6.0' );
}
function wpex_elementor_location_exists() {
	_deprecated_function( 'wpex_elementor_location_exists', 'Total 6.0' );
}
function wpex_header_builder_id() {
	return totaltheme_call_static( 'Header\Core', 'get_template_id' );
}
function wpex_footer_builder_id() {
	return totaltheme_call_static( 'Footer\Core', 'get_template_id' );
}
function wpex_has_custom_footer() {
	return totaltheme_call_static( 'Footer\Core', 'is_custom' );
}
function wpex_convert_fa_to_ticon() {
	_deprecated_function( 'wpex_convert_fa_to_ticon', 'Total 6.0' );
}
function wpex_has_post_redirection() {
	_deprecated_function( 'wpex_has_post_redirection', 'Total 6.0' );
}
function wpex_has_site_frame_border() {
	_deprecated_function( 'wpex_has_site_frame_border', 'Total 6.0' );
}
function wpex_text_decorations() {
	_deprecated_function( 'wpex_text_decorations', 'Total 6.0' );
}
function wpex_font_styles() {
	_deprecated_function( 'wpex_font_styles', 'Total 6.0' );
}
function wpex_text_transforms() {
	_deprecated_function( 'wpex_text_transforms', 'Total 6.0' );
}
function wpex_border_styles() {
	_deprecated_function( 'wpex_border_styles', 'Total 6.0' );
}
function wpex_alignments() {
	_deprecated_function( 'wpex_alignments', 'Total 6.0' );
}
function wpex_css_animations() {
	_deprecated_function( 'wpex_css_animations', 'Total 6.0' );
}
function wpex_author_has_social() {
	_deprecated_function( 'wpex_author_has_social', 'Total 6.0' );
}
function wpex_attachment_exists() {
	_deprecated_function( 'wpex_attachment_exists', 'Total 6.0' );
}
function wpex_parse_text_align_class() {
	_deprecated_function( 'wpex_parse_text_align_class', 'Total 6.0' );
}
function wpex_parse_padding_class() {
	_deprecated_function( 'wpex_parse_padding_class', 'Total 6.0' );
}
function wpex_parse_border_width_class() {
	_deprecated_function( 'wpex_parse_border_width_class', 'Total 6.0' );
}
function wpex_parse_border_style_class() {
	_deprecated_function( 'wpex_parse_border_style_class', 'Total 6.0' );
}
function wpex_parse_margin_class() {
	_deprecated_function( 'wpex_parse_margin_class', 'Total 6.0' );
}
function wpex_parse_direction() {
	_deprecated_function( 'wpex_parse_direction', 'Total 6.0' );
}
function wpex_parse_border_radius_class() {
	_deprecated_function( 'wpex_parse_border_radius_class', 'Total 6.0' );
}
function wpex_add_sp_video_to_oembed() {
	_deprecated_function( 'wpex_add_sp_video_to_oembed', 'Total 6.0' );
}
function wpex_blog_slider_video() {
	_deprecated_function( 'wpex_blog_slider_video', 'Total 6.0' );
}
function wpex_get_staff_overlay() {
	_deprecated_function( 'wpex_get_staff_overlay', 'Total 6.0' );
}
function wpex_get_post_format_icon() {
	_deprecated_function( 'totaltheme_get_post_format_icon_name', 'Total 6.0' );
}
function wpex_post_format_icon() {
	_deprecated_function( 'totaltheme_get_post_format_icon_name', 'Total 6.0' );
}
function wpex_parse_list( $list ) {
	_deprecated_function( 'wpex_parse_list', 'Total 6.0', 'wp_parse_list' );
}
function wpex_get_custom_accent_color_hover() {
	_deprecated_function( 'wpex_get_custom_accent_color_hover', 'Total 6.0' );
}
function wpex_get_theme_color_palette() {
	_deprecated_function( 'wpex_get_theme_color_palette', 'Total 6.0', 'totaltheme_get_color_palette' );
}
function wpex_get_color_palette() {
	_deprecated_function( 'wpex_get_color_palette', 'Total 6.0', 'totaltheme_get_color_palette' );
}
function wpex_theme_color_meta_tag() {
	_deprecated_function( 'wpex_theme_color_meta_tag', 'Total 6.0' );
}
function wpex_x_ua_compatible_meta_tag() {
	_deprecated_function( 'wpex_x_ua_compatible_meta_tag', 'Total 6.0' );
}
function wpex_x_ua_compatible_headers() {
	_deprecated_function( 'wpex_x_ua_compatible_headers', 'Total 6.0' );
}
function wpex_get_accent_color() {
	_deprecated_function( 'wpex_get_accent_color', 'Total 6.0' );
}
function wpex_get_custom_accent_color() {
	_deprecated_function( 'wpex_get_custom_accent_color', 'Total 6.0' );
}
function wpex_get_first_term_id() {
	_deprecated_function( 'wpex_get_first_term_id', 'Total 6.0', 'totaltheme_get_post_primary_term' );
}
function wpex_get_first_term() {
	_deprecated_function( 'wpex_get_first_term', 'Total 6.0', 'totaltheme_get_post_primary_term' );
}
function wpex_sanitize_gallery_id() {
	_deprecated_function( 'wpex_sanitize_gallery_id', 'Total 6.0', 'wp_attachment_is_image' );
}
if ( ! function_exists( 'wpex_get_theme_icon_html' ) ) {
	function wpex_get_theme_icon_html( $icon = '', $extra_class = '' ) {
		return totaltheme_get_icon( $icon, $extra_class );
	}
}
function wpex_theme_icon_html( $icon = '', $extra_class = '' ) {
	echo totaltheme_get_icon( $icon, $extra_class );
}
function wpex_ticons_list( $return = 'deprecated', $default = 'none' ) {
	_deprecated_function( 'wpex_ticons_list', 'Total 6.0', 'Theme_Icons::get_icons_list' );
}
function wpex_visibility() {
	_deprecated_function( 'wpex_visibility', 'Total 6.0', 'totaltheme_get_visibility_choices' );
}
function wpex_visibility_class( $visibility ) {
	_deprecated_function( 'wpex_visibility_class', 'Total 6.0', 'totaltheme_get_visibility_class' );
}
function wpex_svg( $file = '', $size = 20 ) {
	echo totaltheme_get_svg( $file, $size );
}
function wpex_get_svg( $file = '', $size = 20 ) {
	$svg = totaltheme_get_svg( $file, $size );
	return (string) apply_filters( 'wpex_svg', $svg, $svg, $size );
}
function wpex_gallery_count( $post_id = '' ) {
	return count( (array) wpex_get_gallery_ids( $post_id ) );
}
function wpex_get_staff_member_by_user( $user_id  = '' ) {
	_deprecated_function( 'wpex_get_staff_member_by_user', 'Total 6.0', 'totaltheme_get_user_related_staff_member_id' );
	return totaltheme_get_user_related_staff_member_id( $user_id );
}
function wpex_process_user_identifier( $id_or_email = '' ) {
	_deprecated_function( 'wpex_process_user_identifier', 'Total 6.0' );
}
function wpex_has_sticky_header_menu() {
	_deprecated_function( 'wpex_has_sticky_header_menu', 'Total 6.0', 'TotalTheme\Header\Menu\Sticky::is_enabled' );
}
function wpex_header_menu_classes( $return = '' ) {
	if ( 'wrapper' === $return ) {
		return totaltheme_call_static( 'Header\Menu', 'get_wrapper_classes' );
	} else {
		return totaltheme_call_static( 'Header\Menu', 'get_inner_classes' );
	}
}
function wpex_header_menu_ul_classes() {
	return totaltheme_call_static( 'Header\Menu', 'get_menu_class' );
}
function wpex_get_mobile_menu_styles() {
	_deprecated_function( 'wpex_get_mobile_menu_styles', 'Total 6.0', 'TotalTheme\Mobile\Menu::style_choices' );
}
if ( ! class_exists( 'WPEX_Dropdown_Walker_Nav_Menu' ) && class_exists( 'TotalTheme\Walkers\Main_Nav_Menu' ) ) {
	class WPEX_Dropdown_Walker_Nav_Menu extends TotalTheme\Walkers\Main_Nav_Menu {
		public function __construct() {
			_deprecated_class( __CLASS__, 'Total 6.0', 'TotalTheme\Walkers\Main_Nav_Menu' );
			return totaltheme_init_class( 'Walkers\Main_Nav_Menu' );
		}
	}
}
function wpex_custom_wp_gallery_supported() {
	_deprecated_function( 'wpex_custom_wp_gallery_supported', 'Total Theme 6.0', 'TotalTheme\WP_Post_Gallery\is_enabled' );
}

function wpex_excerpt( $args ) {
	echo totaltheme_post_excerpt( $args );
}

function wpex_get_excerpt( $args = [] ) {
	return totaltheme_get_post_excerpt( $args );
}

function wpex_get_secondary_thumbnail( $post_id = '', $check_gallery = true ) {
	_deprecated_function( 'wpex_get_secondary_thumbnail', 'Total Theme 6.0', 'totaltheme_get_post_secondary_thumbnail_id' );
	return totaltheme_get_post_secondary_thumbnail_id( $post_id, $check_gallery );
}

function wpex_vc_is_inline() {
	_deprecated_function( 'wpex_vc_is_inline', 'Total Theme 6.0', 'totaltheme_is_wpb_frontend_editor' );
}
function wpex_search_results_style() {
	//_deprecated_function( 'wpex_search_results_style', 'Total Theme 6.0', 'Search\Archive::style' );
	return totaltheme_call_static( 'Search\Archive', 'style' );
}
function wpex_search_loop_top_class() {
	//_deprecated_function( 'wpex_search_loop_top_class', 'Total Theme 6.0', 'Search\Archive::wrapper_class' );
	totaltheme_call_static( 'Search\Archive', 'wrapper_class' );
}
function wpex_search_archive_columns() {
	//_deprecated_function( 'wpex_search_entry_class', 'Total Theme 6.0', 'Search\Archive::columns' );
	return totaltheme_call_static( 'Search\Archive', 'columns' );
}
function wpex_search_entry_class() {
	//_deprecated_function( 'wpex_search_entry_class', 'Total Theme 6.0', 'Search\Entry::wrapper_class' );
	totaltheme_call_static( 'Search\Entry', 'wrapper_class' );
}
function wpex_search_entry_inner_class() {
	//_deprecated_function( 'wpex_search_entry_inner_class', 'Total Theme 6.0', 'Search\Entry::inner_class' );
	totaltheme_call_static( 'Search\Entry', 'inner_class' );
}
function wpex_search_entry_card_style() {
	//_deprecated_function( 'wpex_search_entry_card_style', 'Total Theme 6.0', 'Search\Entry::card_style' );
	return totaltheme_call_static( 'Search\Entry', 'card_style' );
}
function wpex_search_entry_card() {
	//_deprecated_function( 'wpex_search_entry_card', 'Total Theme 6.0', 'Search\Entry::render_card' );
	return totaltheme_call_static( 'Search\Entry', 'render_card' );
}
function wpex_search_entry_content_class() {
	//_deprecated_function( 'wpex_search_entry_inner_class', 'Total Theme 6.0', 'Search\Entry::content_class' );
	totaltheme_call_static( 'Search\Entry', 'content_class' );
}
function wpex_search_entry_divider() {
	//_deprecated_function( 'wpex_search_entry_inner_class', 'Total Theme 6.0', 'Search\Entry::divider' );
	totaltheme_call_static( 'Search\Entry', 'divider' );
}
function wpex_search_entry_excerpt_length() {
	//_deprecated_function( 'wpex_search_entry_inner_class', 'Total Theme 6.0', 'Search\Entry::excerpt_length' );
	return totaltheme_call_static( 'Search\Entry', 'excerpt_length' );
}
function wpex_search_entry_excerpt_class() {
	//_deprecated_function( 'wpex_search_entry_inner_class', 'Total Theme 6.0', 'Search\Entry::excerpt_class' );
	totaltheme_call_static( 'Search\Entry', 'excerpt_class' );
}
function wpex_search_entry_header_class() {
	//_deprecated_function( 'wpex_search_entry_inner_class', 'Total Theme 6.0', 'Search\Entry::header_class' );
	totaltheme_call_static( 'Search\Entry', 'header_class' );
}
function wpex_search_entry_title_class() {
	//_deprecated_function( 'wpex_search_entry_inner_class', 'Total Theme 6.0', 'Search\Entry::title_class' );
	totaltheme_call_static( 'Search\Entry', 'title_class' );
}
function wpex_is_total_portfolio_enabled() {
	//_deprecated_function( 'wpex_is_total_portfolio_enabled', 'Total Theme 6.0', 'Portfolio\Post_Type::is_enabled' );
	return totaltheme_call_static( 'Portfolio\Post_Type', 'is_enabled' );
}
function wpex_is_total_staff_enabled() {
	//_deprecated_function( 'wpex_is_total_staff_enabled', 'Total Theme 6.0', 'Staff\Post_Type::is_enabled' );
	return totaltheme_call_static( 'Staff\Post_Type', 'is_enabled' );
}
function wpex_is_total_testimonials_enabled() {
	//_deprecated_function( 'wpex_is_total_testimonials_enabled', 'Total Theme 6.0', 'Testimonials\Post_Type::is_enabled' );
	return totaltheme_call_static( 'Testimonials\Post_Type', 'is_enabled' );
}
function wpex_is_portfolio_tax() {
	//_deprecated_function( 'wpex_is_portfolio_tax', 'Total Theme 6.0', 'is_tax' );
	return is_tax( [ 'portfolio_category', 'portfolio_tag' ] ) && ! is_search();
}
function wpex_is_staff_tax() {
	//_deprecated_function( 'wpex_is_staff_tax', 'Total Theme 6.0', 'is_tax' );
	return is_tax( [ 'staff_category', 'staff_tag' ] ) && ! is_search();
}
function wpex_is_testimonials_tax() {
	//_deprecated_function( 'wpex_is_testimonials_tax', 'Total Theme 6.0', 'is_tax' );
	return is_tax( [ 'testimonials_category', 'testimonials_tag' ] ) && ! is_search();
}
function wpex_elementor_is_preview_mode() {
	_deprecated_function( 'wpex_elementor_is_preview_mode', 'Total Theme 6.1', 'totaltheme_is_elementor_editor' );
}

/*-----------------------------------------------------------------------------------*/
/* 6.1
/*-----------------------------------------------------------------------------------*/
function wpex_is_post_in_series() {
	_deprecated_function( 'wpex_is_post_in_series', 'Total Theme 6.1' );
}
if ( ! function_exists( 'wpex_post_has_terms' ) ) {
	function wpex_post_has_terms() {
		_deprecated_function( 'wpex_post_has_terms', 'Total Theme 6.1' );
	}
}
function wpex_get_card_styles() {
	_deprecated_function( 'wpex_get_card_styles', 'Total Theme 6.1', 'WPEX_Card::get_card_styles' );
}
function wpex_get_default_card_styles() {
	_deprecated_function( 'wpex_get_default_card_styles', 'Total Theme 6.1', 'WPEX_Card::get_default_card_styles' );
}
function wpex_choices_card_styles() {
	_deprecated_function( 'wpex_choices_card_styles', 'Total Theme 6.1', 'WPEX_Card::get_grouped_card_style_options' );
}
function wpex_card_select() {
	_deprecated_function( 'wpex_card_select', 'Total Theme 6.1' );
}
function wpex_sanitize_template_content() {
	_deprecated_function( 'wpex_sanitize_template_content', 'Total Theme 6.1', 'totaltheme_render_content' );
}
function wpex_the_content( $content = '' ) {
//	_deprecated_function( 'wpex_the_content', 'Total Theme 6.1', 'totaltheme_render_content' );
	return totaltheme_render_content( $content );
}

/*-----------------------------------------------------------------------------------*/
/* 6.3
/*-----------------------------------------------------------------------------------*/
function wpex_mobile_menu_extras() {
	_deprecated_function( 'wpex_mobile_menu_extras', 'Total Theme 6.13' );
}
