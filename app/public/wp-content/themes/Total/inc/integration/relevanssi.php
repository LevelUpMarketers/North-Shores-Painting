<?php

namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * Relevanssi Integration.
 */
final class Relevanssi {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Revslider.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		if ( ! has_action( 'init', 'relevanssi_init' ) ) {
			return;
		}

		\add_filter( 'wpex_excerpt_output', [ self::class, 'parse_excerpts' ] );
		\add_filter( 'the_title', [ self::class, 'parse_title' ], 10, 2 );
		\add_filter( 'wpex_card_title', [ self::class, 'parse_card_title' ], 10, 3 );
	}

	/**
	 * Check if doing a search query.
	 */
	private static function is_search_query(): bool {
		return ( \is_search() && \wpex_is_request( 'frontend' ) && \is_main_query() );
	}

	/**
	 * Parses post excerpts.
	 */
	public static function parse_excerpts( $excerpt ) {
		if ( self::is_search_query() && \function_exists( 'relevanssi_highlight_terms' ) ) {
			$excerpt = \relevanssi_highlight_terms( $excerpt, \strip_shortcodes( \sanitize_text_field( \get_search_query( false ) ) ), true );
		}
		return $excerpt;
	}

	/**
	 * Parses the wpex_title.
	 */
	public static function parse_title( $title, $id ) {
		if ( self::is_search_query() ) {
			$post = \relevanssi_get_post( $id );
			if ( $post && ! empty( $post->post_highlighted_title ) ) {
				return $post->post_highlighted_title;
			}
		}
		return $title;
	}

	/**
	 * Parses card titles.
	 */
	public static function parse_card_title( $title, $card_obj, $args ) {
		if ( self::is_search_query() && empty( $card_obj->args['title'] ) && empty( $args['content'] ) ) {
			$post = \relevanssi_get_post( $card_obj->post_id ?? 0 );
			if ( $post && ! empty( $post->post_highlighted_title ) ) {
				return $post->post_highlighted_title;
			}
		}
		return $title;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
