<?php

namespace TotalTheme\Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "http_request_args".
 */
final class Http_Request_Args {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $parsed_args, $url ) {
		if ( false === \strpos( $url, 'api.wordpress.org/themes/update-check' ) ) {
			return $parsed_args;
		}

		// Disable theme update checks since it's not needed.
		if ( isset( $parsed_args['body']['themes'] ) ) {
			$themes = \json_decode( $parsed_args['body']['themes'] );
			if ( $parent = \get_option( 'template' ) ) {
				unset( $themes->themes->$parent );
			} elseif ( $child = \get_option( 'stylesheet' ) ) {
				unset( $themes->themes->$child );
			}
			$parsed_args['body']['themes'] = \wp_json_encode( $themes );
		}

		return $parsed_args;
	}

}
