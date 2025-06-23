<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "term_link".
 */
final class Term_Link {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback.
	 */
	public static function callback( $termlink, $term, $taxonomy ) {
		$redirect_id = \get_term_meta( $term->term_id, 'wpex_redirect', true );
		if ( \is_numeric( $redirect_id ) && $redirect_url = \get_permalink( $redirect_id ) ) {
			$termlink = \esc_url( $redirect_url );
		}
		return $termlink;
	}

}
