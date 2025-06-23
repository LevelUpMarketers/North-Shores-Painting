<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

class Remove_Elements {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		foreach ( self::get_blacklist() as $element ) {
			\vc_remove_element( $element );
		}
	}

	/**
	 * Returns a list of elements to remove.
	 */
	protected static function get_blacklist(): array {
		$list = [
			'vc_teaser_grid',
			'vc_posts_grid',
			'vc_posts_slider',
			'vc_gallery',
			'vc_wp_text',
			'vc_wp_pages',
			'vc_wp_links',
			'vc_wp_meta',
			'vc_carousel',
			'vc_images_carousel',
			'vc_googleplus',
			'vc_copyright',
		];
		return (array) \apply_filters( 'totaltheme/integration/wpbakery/remove_elements/blacklist', $list );
	}

}
