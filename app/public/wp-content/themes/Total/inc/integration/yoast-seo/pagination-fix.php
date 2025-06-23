<?php

namespace TotalTheme\Integration\Yoast_SEO;

defined( 'ABSPATH' ) || exit;

/**
 * Yoast SEO replacement changes.
 */
class Pagination_Fix {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		add_filter( 'wpseo_canonical', [ self::class, 'canonical' ], 10, 2 );
		add_filter( 'wpseo_replacements', [ self::class, 'wpseo_replacements' ], 10, 2 );
	}

	/**
	 * Returns the correct canonical when a page is paginated.
	 *
	 * @param string                      $canonical    The current canonical.
	 * @param Indexable_Presentation|null $presentation The indexable presentation.
	 *
	 * @return string The correct canonical.
	 */
	public static function canonical( $canonical, $presentation = null ) {
		if ( self::is_enabled() && $page = get_query_var( 'paged' ) ) {
			if ( str_contains( $canonical, trailingslashit( $page ) ) ) {
				return $canonical;
			}
			global $wp_rewrite;
			if ( is_a( $wp_rewrite, 'WP_Rewrite' )
				&& is_callable( [ $wp_rewrite, 'using_permalinks' ] )
				&& $wp_rewrite->using_permalinks()
			) {
				$canonical = trailingslashit( $canonical );
				if ( ! empty( $wp_rewrite->pagination_base ) ) {
					$canonical .= trailingslashit( $wp_rewrite->pagination_base );
				}
				$canonical = user_trailingslashit( $canonical . $page );
			}
		}

		return $canonical;
	}

	/**
	 * Re-adds the pagination var to the Yoast seo replacements to fix bugs with single pagination.
	 *
	 * @api     array   $replacements The replacements.
	 *
	 * @param array $args The object some of the replacement values might come from,
	 *                    could be a post, taxonomy or term.
	 */
	public static function wpseo_replacements( $replacements, $args = [] ) {
		if ( self::is_enabled() && $page = get_query_var( 'paged' ) ) {
			$sep = '-';
			if ( function_exists( 'YoastSEO' )
				&& ! empty( YoastSEO()->helpers )
				&& ! empty( YoastSEO()->helpers->options )
				&& is_callable( [ YoastSEO()->helpers->options, 'get_title_separator' ] )
			) {
				$sep = YoastSEO()->helpers->options->get_title_separator();
			}
			$replacements['%%page%%'] = sprintf( $sep . ' ' . esc_html__( 'Page %s' ), $page );
		}
		return $replacements;
	}

	/**
	 * Checks if the current page needs a pagination fix.
	 *
	 * @return boolean Whether the current page should apply pagination fixes.
	 */
	protected static function is_enabled(): bool {
		global $wp_query;
		return ( is_singular() && is_paged() && empty( $wp_query->max_num_pages ) );
	}

}