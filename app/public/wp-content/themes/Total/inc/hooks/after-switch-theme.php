<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "after_switch_theme".
 */
final class After_Switch_Theme {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback() {
		// Flush rewrite rules.
		\flush_rewrite_rules();

		// Delete tgma plugin activation script user meta data to make sure notices display correctly.
		\delete_metadata( 'user', null, 'tgmpa_dismissed_notice_totaltheme', null, true );
	}

}
