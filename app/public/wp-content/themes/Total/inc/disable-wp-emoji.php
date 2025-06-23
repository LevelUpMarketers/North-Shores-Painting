<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Disable WP Emoji scripts.
 */
class Disable_WP_Emoji {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! \get_theme_mod( 'remove_emoji_scripts_enable', true ) ) {
			return;
		}

		// Remove hooks.
		\remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		\remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		\remove_action( 'wp_print_styles', 'print_emoji_styles' );
		\remove_action( 'admin_print_styles', 'print_emoji_styles' );
		\remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		\remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		\remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		// Add hooks.
		\add_filter( 'tiny_mce_plugins', [ $this, 'disable_emojis_tinymce' ] );
	}

	/**
	 * Disable's wpemoji scripts in the tinymce.
	 */
	public function disable_emojis_tinymce( $plugins ) {
		if ( \is_array( $plugins ) ) {
			$key = \array_search( 'wpemoji', $plugins );
			if ( $key ) {
				unset( $plugins[ $key ] );
			}
		}
		return $plugins;
	}

}
