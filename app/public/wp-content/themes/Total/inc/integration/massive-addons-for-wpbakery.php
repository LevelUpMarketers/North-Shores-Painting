<?php

namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * Massive Addons Tweaks.
 *
 * @package TotalTheme
 * @subpackage Integration
 * @version 5.8.1
 */
class Massive_Addons_For_WPBakery {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'vcex_supports_advanced_parallax', '__return_false' );
	}

}
