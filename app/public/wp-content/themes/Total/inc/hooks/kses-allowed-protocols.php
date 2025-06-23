<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "kses_allowed_protocols".
 */
final class Kses_Allowed_Protocols {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $protocols ) {
		$protocols[] = 'skype';
		$protocols[] = 'whatsapp';
		$protocols[] = 'callto';
		return $protocols;
	}

}
