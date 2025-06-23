<?php

defined( 'ABSPATH' ) || exit;

/**
 * Sanitize data via the TotalTheme\SanitizeData class.
 */
function wpex_sanitize_data( $data = '', string $type = '' ) {
	if ( $data || '0' === $data ) {
		return totaltheme_call_non_static( 'Sanitize_Data', 'parse_data', $data, $type );
	}
}

/**
 * Validate Boolean.
 */
function wpex_validate_boolean( $var ): bool {
	if ( is_bool( $var ) ) {
		return $var;
	}
	if ( is_string( $var ) ) {
		$var = strtolower( $var );
		if ( 'false' === $var || 'off' === $var || 'disabled' === $var || 'no' === $var ) {
			return false;
		}
		if ( 'true' === $var || 'on' === $var || 'enabled' === $var || 'yes' === $var ) {
			return true;
		}
	}
	return (bool) $var;
}

/**
 * Echo escaped post title.
 */
function wpex_esc_title( $post = '' ) {
	echo wpex_get_esc_title( $post );
}

/**
 * Return escaped post title.
 */
function wpex_get_esc_title( $post = '' ) {
	return the_title_attribute( [
		'echo' => false,
		'post' => $post,
	] );
}

/**
 * Sanitize font-family for the frontend.
 */
function wpex_sanitize_font_family( $input ) {
	return wpex_sanitize_data( $input, 'font_family' );
}

/**
 * Sanitize visibility.
 */
function wpex_sanitize_visibility( string $input ): string {
	if ( empty( $input ) || ! is_string( $input ) ) {
		return '';
	}
	$input = str_replace( '-portrait', '', $input );
	$input = str_replace( '-landscape', '', $input );
	return sanitize_html_class( $input );
}

/**
 * Sanitize font-size for frontend.
 */
function wpex_sanitize_font_size( string $input ): string {
	return wpex_sanitize_data( $input, 'font_size' );
}

/**
 * Sanitize letter spacing.
 */
function wpex_sanitize_letter_spacing( $input ) {
	return wpex_sanitize_data( $input, 'letter_spacing' );
}

/**
 * Removes empty p tags from a string.
 */
function totaltheme_remove_empty_p_tags( $content ) {
	if ( $content && is_string( $content ) ) {
		$content = str_replace( '<p></p>', '', $content );
	}
	return $content;
}

/**
 * Clean up shortcodes.
 *
 * @note The core shortcode_unautop() function doesn't work properly with threaded shortcodes.
 */
function totaltheme_shortcode_unautop( $content = '' ) {
	if ( $content ) {
		return strtr( $content, [
			'<p>['    => '[',
			']</p>'   => ']',
			']<br />' => ']',
		] );
	}
}
