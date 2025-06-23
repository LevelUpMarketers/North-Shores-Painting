<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "wp_get_attachment_url".
 */
final class WP_Get_Attachment_Url {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $url ) {
		/**
		 * The wp_get_attachment_url() function doesn't distinguish whether a page request arrives via HTTP or HTTPS.
		 * Using wp_get_attachment_url filter, we can fix this to avoid the dreaded mixed content browser warning.
		 *
		 * @todo is this still needed?
		 */
		if ( \is_ssl() ) {
			$url = str_replace( 'http://', 'https://', $url );
		}

		return $url;
	}

}
