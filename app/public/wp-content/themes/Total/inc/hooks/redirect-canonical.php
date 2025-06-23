<?php

namespace TotalTheme\Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "redirect_canonical".
 */
final class Redirect_Canonical {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $redirect_url ) {
		if ( \is_singular() && \is_paged() && self::post_has_pagination() ) {
			$redirect_url = false; // fix for pagination not working on singular posts.
		}

		return $redirect_url;
	}

	/**
	 * Checks if the current post has paginated elements.
	 */
	private static function post_has_pagination(): bool {
		if ( self::content_has_paginated_elements( (string) get_post_field( 'post_content' ) ) ) {
			return true;
		} elseif ( $template_content = totaltheme_call_static( 'Theme_Builder\Post_Template', 'get_template_content' ) ) {
			return self::content_has_paginated_elements( (string) $template_content );
		}
		return false;
	}

	/**
	 * Checks if the post content has paginated elements.
	 */
	private static function content_has_paginated_elements( string $content ): bool {
		return str_contains( $content, 'pagination="numbered"' ) || str_contains( $content, 'pagination="true"' );
	}

}
