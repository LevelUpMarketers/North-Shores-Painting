<?php

namespace TotalTheme\Search;

\defined( 'ABSPATH' ) || exit;

/**
 * Ajax Search.
 */
final class Ajax {

	/**
	 * Static class only.
	 */
	private function __construct() {}

    /**
	 * Init.
	 */
	public static function init(): void {
		\add_action( 'wp_ajax_wpex_ajax_search', [ self::class, '_ajax_callback' ] );
		\add_action( 'wp_ajax_nopriv_wpex_ajax_search', [ self::class, '_ajax_callback' ] );
	}

	/**
	 * Returns search results.
	 */
	public static function _ajax_callback(): void {
		\check_ajax_referer( 'wpex_ajax_search', 'nonce' );

		if ( empty( $_POST['search_string'] ) || ! \is_string( $_POST['search_string'] ) ) {
			\wp_die();
		}

		if ( \is_callable( [ 'WPBMap', 'addAllMappedShortcodes' ] ) ) {
			\WPBMap::addAllMappedShortcodes(); // Fix for WPBakery not working in ajax - for stripping shortcodes
		}

		$results            = [];
		$search_string_safe = \sanitize_text_field( \wp_unslash( $_POST['search_string'] ) );

		$excerpt_args = (array) apply_filters( 'totaltheme/search/ajax/excerpt_args', [
			'length' => 20,
		], $search_string_safe );

		global $wp_query; // use global query so it can work with $query->is_search checks
		$og_query = $wp_query;
		$wp_query->is_search = true;
		$wp_query->is_posts_page = false;
		$wp_query->query_vars['s'] = $search_string_safe;
	
		// Instantiate the query
		$query = new \WP_Query( (array) apply_filters( 'totaltheme/search/ajax/query_args', [
			's'              => $search_string_safe,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
		] ) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$tag = \get_post_type() ?: 'post';
				$tag = ( 'post' === $tag ) ? \esc_html__( 'Blog', 'total' ) : ( \get_post_type_object( $tag )->labels->name ?? $tag );
				if ( $permalink = \get_permalink() ) {
					$results[] = (array) \apply_filters( 'totaltheme/search/ajax/post_data', [
						'title'     => \wp_strip_all_tags( \get_the_title() ),
						'permalink' => \esc_url( $permalink ),
						'tag'       => \sanitize_text_field( $tag ),
						'excerpt'   => ( $excerpt = \totaltheme_get_post_excerpt( $excerpt_args ) ) ? \wp_strip_all_tags( $excerpt ) : '',
					], get_post() );
				}
			}
			$wp_query = $og_query;
			wp_reset_postdata();
		}

		\wp_send_json( $results );
	}

	/**
	 * Search title only.
	 */
	public static function _search_by_title( $search, $wp_query ) {
		global $wpdb;
		if ( ! empty( $wp_query->query_vars['s'] ) ) {
			$search_terms = $wp_query->query_vars['s'];
			$search = $wpdb->prepare(" AND ( {$wpdb->posts}.post_title LIKE %s )", '%' . $wpdb->esc_like( $search_terms ) . '%');
		}
		return $search;
	}

	/**
	 * Returns l10n for script localization.
	 */
	public static function get_l10n(): array {
		return [
			'nonce'               => \wp_create_nonce( 'wpex_ajax_search' ),
			'ajax_url'            => \set_url_scheme( \admin_url( 'admin-ajax.php' ) ),
			'character_threshold' => (int) \apply_filters( 'totaltheme/search/ajax/character_threshold', 3 ),
			'throttle_delay'      => (int) \apply_filters( 'totaltheme/search/ajax/throttle', 500 ),
			'highlight'           => ( true === \apply_filters( 'totaltheme/search/ajax/highlight', true ) ) ? 1 : 0,
		];
	}

}
