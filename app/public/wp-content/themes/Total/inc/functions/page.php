<?php
defined( 'ABSPATH' ) || exit;

/**
 * Page content class.
 */
function wpex_page_single_content_class() {
	$class = [
		'single-page-content',
		'single-content',
		'entry',
		'wpex-clr',
	];

	/**
	 * Filters the single page content class.
	 *
	 * @param array $class
	 */
	$class = (array) apply_filters( 'wpex_page_single_content_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Get Page single supported media types.
 */
function wpex_page_single_supported_media() {
	$supported_media = [
		'video',
		'audio',
		'thumbnail',
	];

	/**
	 * Filters the supported media for the standard page media block.
	 *
	 * @param array $supported_media
	 */
	$supported_media = (array) apply_filters( 'wpex_page_single_supported_media', $supported_media );

	return $supported_media;
}

/**
 * Get Post type single format.
 */
function wpex_page_single_media_type() {
	$supported_media = wpex_page_single_supported_media();

	if ( in_array( 'video', $supported_media ) && wpex_has_post_video() ) {
		$type = 'video';
	} elseif ( in_array( 'audio', $supported_media ) && wpex_has_post_audio() ) {
		$type = 'audio';
	} elseif ( in_array( 'gallery', $supported_media ) && wpex_has_post_gallery() ) {
		$type = 'gallery';
	} elseif ( in_array( 'thumbnail', $supported_media ) && has_post_thumbnail() ) {
		$type = 'thumbnail';
	} else {
		$type = ''; //important
	}

	/**
	 * Filters the single page media type.
	 *
	 * @param array $class
	 */
	$type = (string) apply_filters( 'wpex_page_single_media_type', $type );

	return $type;
}

/**
 * Page single media class.
 */
function wpex_page_single_media_class() {
	$class = [
		'single-media',
		'wpex-relative',
		'wpex-mb-30',
	];

	/**
	 * Filters the single page media class.
	 *
	 * @param array $class
	 */
	$class = (array) apply_filters( 'wpex_page_single_media_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Page single header class.
 */
function wpex_page_single_header_class() {
	$class = [
		'single-page-header',
	];

	if ( 'full-screen' === wpex_content_area_layout() ) {
		$class[] = 'container';
	}

	/**
	 * Filters the single page header class.
	 *
	 * @param array $class
	 */
	$class = (array) apply_filters( 'wpex_page_single_header_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Page single title class.
 */
function wpex_page_single_title_class() {
	$class = [
		'single-page-title',
		'entry-title',
		'wpex-mb-20',
		'wpex-text-3xl',
	];

	/**
	 * Filters the single page title class.
	 *
	 * @param array $class
	 */
	$class = (array) apply_filters( 'wpex_page_single_title_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}