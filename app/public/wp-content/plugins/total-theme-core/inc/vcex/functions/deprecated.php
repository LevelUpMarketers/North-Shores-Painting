<?php

defined( 'ABSPATH' ) || exit;

function vcex_ilightbox_skins() {}
function vcex_dummy_image_url() {}
function vcex_dummy_image() {}
function vcex_image_rendering() {}
function vcex_inline_js() {}
function vcex_parse_old_design_js() {}
function vcex_function_needed_notice() {}
function vcex_enqueue_navbar_filter_scripts() {}
function vcex_sanitize_data() {}
function vcex_get_button_custom_color_css() {}

function vcex_get_border_radius_class( $val ) {
	return vcex_parse_border_radius_class( $val );
}

function vcex_vc_map_get_attributes( $shortcode = '', $atts = '', $class = '' ) {
	return vcex_shortcode_atts( $shortcode, $atts, $class );
}

/* v1.4.3 */
function vcex_shortcodes_list() {
	return totalthemecore_call_non_static( 'Vcex\Shortcodes_Registry', 'get_all_registered' );
}

/* v1.8.1 */
function vcex_get_schema_markup() {}

/* 2.0 */
function vcex_asset_dir_path() {
	_deprecated_function( 'vcex_asset_dir_path', 'Total Theme Core 2.0' );
}
function vcex_asset_url() {
	_deprecated_function( 'vcex_asset_url', 'Total Theme Core 2.0' );
}
function vcex_enqueue_icon_font() {
	_deprecated_function( 'vcex_enqueue_icon_font', 'Total Theme Core 2.0' );
}
function vcex_get_icon_class() {
	_deprecated_function( 'vcex_get_icon_class', 'Total Theme Core 2.0' );
}
function vcex_get_icon_font_families() {
	_deprecated_function( 'vcex_get_icon_font_families', 'Total Theme Core 2.0' );
}
function vcex_sanitize_margin_class( $margin = '', $prefix = '' ) {
	return vcex_parse_margin_class( $margin, $prefix );
}
function vcex_enque_style( $type = '', $value = '' ) {
	if ( 'ilightbox' === $type || 'lightbox' === $type ) {
		vcex_enqueue_lightbox_scripts();
	}
}
function vcex_acf_utils() {
	_deprecated_function( 'vcex_acf_utils', 'Total Theme Core 2.0' );
}

/* 2.1 */
function vcex_get_elementor_global_color( $id ) {
	_deprecated_function( 'vcex_get_elementor_global_color', 'Total Theme Core 2.1', 'Elementor\Helpers::get_global_color' );
}
