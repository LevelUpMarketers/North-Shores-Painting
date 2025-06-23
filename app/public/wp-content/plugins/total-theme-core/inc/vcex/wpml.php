<?php

namespace TotalThemeCore\Vcex;

defined( 'ABSPATH' ) || exit;

/**
 * WPML Integration for vcex shortcodes.
 */
class WPML {

	/**
	 * Static only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		\add_filter( 'wpml_pb_shortcode_encode', [ self::class, '_shortcode_encode' ], 10, 3 );
		\add_filter( 'wpml_pb_shortcode_decode', [ self::class, '_shortcode_decode' ], 10, 3 );
	}

	/**
	 * Shortcode encode.
	 */
	public static function _shortcode_encode( $string, $encoding, $original_string ) {
		if ( 'vcex_urlencoded_json' === $encoding && is_array( $original_string ) ) {
			$output = [];
			foreach ( $original_string as $combined_key => $value ) {
				$parts = explode( '_', $combined_key );
				$i     = array_pop( $parts );
				$key   = implode( '_', $parts );
				$output[ $i ][ $key ] = $value;
			}
			$string = \urlencode( \json_encode( $output ) );
		}
		return $string;
	}

	/**
	 * Shortcode decode.
	 */
	public static function _shortcode_decode( $string, $encoding, $original_string ) {
		if ( 'vcex_urlencoded_json' === $encoding ) {
			$decoded = \urldecode( $original_string );
			if ( $decoded ) {
				$rows = \json_decode( $decoded, true );
				if ( \is_array( $rows ) ) {
					$string = [];
					foreach ( $rows as $i => $row ) {
						foreach ( $row as $key => $value ) {
							if ( \in_array( $key, [ 'heading', 'text', 'link', 'label', 'value' ], true ) ) {
								$string[ $key . '_' . $i ] = [ 'value' => $value, 'translate' => true ];
							} else {
								$string[ $key . '_' . $i ] = [ 'value' => $value, 'translate' => false ];
							}
						}
					}
				}
			}
		}
		return $string;
	}

}
