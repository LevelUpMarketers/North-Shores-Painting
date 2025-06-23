<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "kses_allowed_protocols".
 */
final class Embed_Oembed_Html {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $cache, $url, $attr, $post_ID ) {
		if ( $cache && \is_string( $cache ) ) {
			$cache  = \str_replace( 'frameborder="0"', '', $cache );
			$domain = \parse_url( $url, PHP_URL_HOST );
			if ( self::is_responsive_url( $domain ) ) {
				$class = '';
				$cache = '<span class="wpex-responsive-media">' . $cache . '</span>';
			}
		}
		return $cache;
	}

	/**
	 * Check the current oembed URL should be made responsive.
	 */
	protected static function is_responsive_url( $domain ): bool {
		$check = false;

		if ( ! $domain ) {
			return $check;
		}

		$hosts = [
			'youtube.com',
			'youtu.be',
			'youtube-nocookie.com',
			'vimeo.com',
			'blip.tv',
			'money.cnn.com',
			'dailymotion.com',
			'flickr.com',
			'hulu.com',
			'kickstarter.com',
			'wistia.net',
			'soundcloud.com',
		];

		/*** deprecated ***/
		$hosts = (array) \apply_filters( 'wpex_oembed_responsive_hosts', $hosts );

		foreach ( $hosts as $host ) {
			if ( \str_ends_with( $domain, $host ) ) {
				$check = true;
				break;
			}
		}

		/**
		 * Filters whether the theme should add a responsive wrapper to your oEmbed or not.
		 *
		 * @param bool $check
		 * @todo rename filter
		 */
		$check = \apply_filters( 'wpex_responsive_video_wrap', $check, $domain );

		return (bool) $check;
	}

}
