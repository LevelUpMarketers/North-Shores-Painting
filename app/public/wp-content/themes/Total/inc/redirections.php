<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Redirections Class.
 */
final class Redirections {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		\add_action( 'template_redirect', [ self::class, 'maybe_redirect' ] );
	}

	/**
	 * Potentially redirect the current page.
	 */
	public static function maybe_redirect(): void {
		$redirect = self::get_redirect();
		if ( $redirect && ! \totaltheme_is_wpb_frontend_editor() ) {
			if ( \is_numeric( $redirect ) ) {
				$redirect = \get_permalink( $redirect );
				if ( $redirect ) {
					self::safe_redirect( $redirect );
				}
			} else {
				self::redirect( $redirect );
			}
		}
	}

	/**
	 * Performs a standard redirection.
	 */
	protected static function redirect( $redirect = '' ): void {
		\wp_redirect( \esc_url( $redirect ), self::get_status_code( $redirect ), 'Total WordPress Theme' );
		exit;
	}

	/**
	 * Performs a safe redirection.
	 */
	protected static function safe_redirect( $redirect = '' ): void {
		\wp_safe_redirect( \esc_url( $redirect ), self::get_status_code( $redirect ), 'Total WordPress Theme' );
		exit;
	}

	/**
	 * Return status code for the redirection.
	 */
	protected static function get_status_code( $redirect = '' ): int {
		return (int) \apply_filters( 'wpex_redirect_status_code', 301, $redirect );
	}

	/**
	 * Check if the current page has a redirection.
	 */
	protected static function get_redirect(): string {
		$redirect = '';
		if ( \is_singular() ) {
			if ( 'link' === \get_post_format() && ! \apply_filters( 'wpex_redirect_link_format_posts', false ) ) {
				$redirect = '';
			} elseif ( $custom_redirect = \wpex_get_custom_permalink() ) {
				$redirect = $custom_redirect;
			}
		} elseif ( \is_tax() || \is_category() || \is_tag() ) {
			$redirect = ( $redirect = \get_term_meta( \get_queried_object_id(), 'wpex_redirect', true ) ) ? \sanitize_text_field( $redirect ) : '';
		}
		return $redirect;
	}

}
