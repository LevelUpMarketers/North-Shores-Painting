<?php
namespace TotalTheme\Footer\Bottom;

\defined( 'ABSPATH' ) || exit;

/**
 * Footer Copyright.
 */
class Copyright {

	/**
	 * Return default content.
	 */
	public static function get_default_content(): string {
		return 'Copyright <a href="[site_url]">[site_name]</a> [current_year] - All Rights Reserved';
	}

	/**
	 * Return copyright content.
	 */
	public static function get_content(): string {
		$content = \wpex_get_translated_theme_mod( 'footer_copyright_text', self::get_default_content() );
		return (string) \apply_filters( 'totaltheme/footer/bottom/copyright/content', $content );
	}

}
