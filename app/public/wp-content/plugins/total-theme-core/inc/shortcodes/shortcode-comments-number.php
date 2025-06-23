<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Comments_Number {

	public function __construct() {
		if ( ! shortcode_exists( 'comments_number' ) ) {
			add_shortcode( 'comments_number', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		$atts = shortcode_atts( [
			'format' => true,
		], $atts, 'comments_number' );
		if ( ! empty( $atts['format'] ) && wp_validate_boolean( $atts['format'] ) ) {
			$text = get_comments_number_text();
		} else {
			$text = get_comments_number();
		}
		return '<span class="comments-count-shortcode">' . wp_strip_all_tags( $text ) . '</span>';
	}

}
