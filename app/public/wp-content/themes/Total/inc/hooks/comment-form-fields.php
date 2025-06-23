<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "comment_form_fields".
 */
final class Comment_Form_Fields {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $fields ) {
		if ( \get_theme_mod( 'comment_form_classic', true ) && ! \is_singular( 'product' ) ) {
			$comment_field = $fields['comment'];
			unset( $fields['comment'] );
			$fields['comment'] = $comment_field;
		}

		return $fields;
	}

}
