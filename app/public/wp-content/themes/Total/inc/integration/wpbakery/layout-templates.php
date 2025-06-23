<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

class Layout_Templates {

	/**
	 * The layout key used for the WPBakery meta layout field.
	 */
	protected const LAYOUT_META_KEY = '_wpb_post_custom_layout';

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		\add_filter( 'vc_post_custom_layout_template', [ self::class, 'modify_template' ], 10, 2 );
		\add_filter( 'vc_post_custom_layout_name', [ self::class, 'set_default_layout' ], 20 );
	}

	/**
	 * Modify the blank template.
	 */
	public static function modify_template( $template, $layout_name ) {
		if ( 'blank' === $layout_name ) {
			$template = \WPEX_INC_DIR . 'integration/wpbakery/templates/blank.php';
		}
		return $template;
	}

	/**
	 * Set the default layout to "default" which removes the useless template select landing page
	 * when creating new blank pages.
	 */
	public static function set_default_layout( $layout = '' ) {
		if ( ! $layout || totaltheme_call_static( 'Integration\WPBakery\Slim_Mode', 'is_enabled' ) ) {
			$layout = 'default';
		}
		return $layout;
	}

}
