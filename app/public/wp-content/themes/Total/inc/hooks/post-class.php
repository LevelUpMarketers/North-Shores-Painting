<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "post_class".
 */
final class Post_Class {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $classes, $class = '', $post_id = '' ) {
		if ( ! $post_id ) {
			return $classes;
		}

		$type = \get_post_type( $post_id );

		if ( 'forum' === $type || 'topic' === $type ) {
			return $classes;
		}

		$classes[] = 'entry';

		if ( \wpex_post_has_media( $post_id ) ) {
			$classes[] = 'has-media';
		} else {
			$classes[] = 'no-media';
		}

		if ( \wpex_get_post_redirect_link( $post_id ) ) {
			$classes[] = 'has-redirect';
		}

		if ( \is_sticky( $post_id ) ) {
			$classes[] = 'sticky';
		}

		return $classes;
	}

}
