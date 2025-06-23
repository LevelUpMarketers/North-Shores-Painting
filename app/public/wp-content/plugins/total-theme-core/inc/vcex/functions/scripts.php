<?php

defined( 'ABSPATH' ) || exit;

/**
 * Returns a CSS file URL.
 */
function vcex_get_css_file( string $file ): string {
	return TTC_PLUGIN_DIR_URL . "assets/css/{$file}.min.css";
}

/**
 * Returns a JS file URL.
 */
function vcex_get_js_file( string $file ): string {
	return TTC_PLUGIN_DIR_URL . "assets/js/{$file}.min.js";
}

/**
 * Enqueue lightbox scripts.
 */
function vcex_enqueue_lightbox_scripts() {
	if ( function_exists( 'wpex_enqueue_lightbox_scripts' ) ) {
		wpex_enqueue_lightbox_scripts();
	}
}

/**
 * Enqueue slider scripts.
 */
function vcex_enqueue_slider_scripts( $noCarouselThumbnails = 'deprecated' ) {
	totalthemecore_call_static( 'Vcex\Slider\Core', 'enqueue_scripts' );
}

/**
 * Enqueue carousel scripts.
 */
function vcex_enqueue_carousel_scripts() {
	totalthemecore_call_static( 'Vcex\Carousel\Core', 'enqueue_scripts' );
}

/**
 * Enqueue isotope scripts.
 */
function vcex_enqueue_isotope_scripts() {
	wp_enqueue_script( 'vcex-isotope-grids' );
}

/**
 * Enqueue Google Fonts.
 */
function vcex_enqueue_google_font( $font_family = '' ) {
	if ( $font_family && function_exists( 'wpex_enqueue_google_font' ) ) {
		wpex_enqueue_google_font( $font_family );
	}
}

/**
 * Enqueue Fonts.
 */
function vcex_enqueue_font( $font_family = '' ) {
	if ( $font_family && function_exists( 'wpex_enqueue_font' ) ) {
		wpex_enqueue_font( $font_family );
	}
}

/**
 * Enqueue justified gallery scripts.
 */
function vcex_enqueue_justified_gallery_scripts() {
	wp_enqueue_script( 'justifiedGallery' );
	wp_enqueue_script( 'vcex-justified-gallery' );
	wp_enqueue_style( 'vcex-justified-gallery' );
}
