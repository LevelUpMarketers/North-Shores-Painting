<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "the_password_form".
 */
final class The_Password_Form {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $form ) {
		\ob_start();
			include \locate_template( 'partials/password-protection-form.php' );
		$form = \ob_get_clean();
		return $form;
	}

}
