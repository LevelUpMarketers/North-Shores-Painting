<?php

namespace TotalTheme\Search;

\defined( 'ABSPATH' ) || exit;

/**
 * Search Archive.
 */
class Archive {

	/**
	 * Archive style.
	 */
	protected static $style = null;

	/**
	 * Card style.
	 */
	protected static $card_style = null;

	/**
	 * Archive columns.
	 */
	protected static $columns = null;

	/**
	 * Static class only.
	 */
	private function __construct() {}

	/**
	 * Returns the search archive style.
	 */
	public static function style(): string {
		if ( null !== self::$style ) {
			return self::$style;
		}

		$style = '';

		// @todo update to check for a custom archive template first and if one is defined display that.
		if ( ! self::card_style() ) {
			$style = get_theme_mod( 'search_style', 'default' );
			if ( get_theme_mod( 'search_results_cpt_loops', true ) ) {
				$post_type = $_GET['post_type'] ?? $_GET['post_types'] ?? '';
				if ( $post_type && 'product' !== $post_type ) {
					// @todo support PTU types?
					if ( in_array( $post_type, wpex_theme_post_types(), true ) ) {
						$style = sanitize_text_field( wp_unslash( $post_type ) );
					} elseif ( 'post' === $post_type ) {
						$style = 'blog';
					}
				}
			}
		}

		$style = $style ?: 'default'; // Important: Can't be empty or it will look like the blog
		$style = \apply_filters_deprecated( 'wpex_search_results_style', [ $style ], 'Total 6.0', 'totaltheme/search/archive/style' );
		self::$style = (string) \apply_filters( 'totaltheme/search/archive/style', $style );
		return self::$style;
	}

	/**
	 * Returns archive columns.
	 * 
	 * @return int|string|array
	 */
	public static function columns() {
		if ( null === self::$columns ) {
			$columns = self::card_style() ? get_theme_mod( 'search_entry_columns', 2 ) : 1;
			$columns = apply_filters_deprecated( 'wpex_search_archive_columns', [ $columns ], 'Total 6.0', 'totaltheme/search/archive/columns' );
			self::$columns = \apply_filters( 'totaltheme/search/archive/columns', $columns );
		}
		return self::$columns;
	}

	/**
	 * Output wrapper class.
	 */
	public static function wrapper_class(): void {
		$classes = [ 'wpex-clr' ];
		if ( self::card_style() ) {
			$classes[] = 'wpex-row';
			$grid_style = get_theme_mod( 'search_archive_grid_style', 'fit-rows' );
			if ( 'masonry' === $grid_style || 'no-margins' === $grid_style ) {
				$classes[] = 'wpex-masonry-grid';
				wpex_enqueue_isotope_scripts(); // This is a good spot to enqueue grid scripts
			}
			if ( $gap = get_theme_mod( 'search_archive_grid_gap' ) ) {
				$classes[] = wpex_gap_class( $gap );
			}
		}
		$classes = apply_filters_deprecated( 'wpex_search_loop_top_class', [ $classes ], 'Total 6.0', 'totaltheme/search/archive/wrapper_class' );
		$classes = (array) apply_filters( 'totaltheme/search/archive/wrapper_class', $classes );
		if ( $classes ) {
			echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
		}
	}

	/**
	 * Card style.
	 */
	public static function card_style(): string {
		if ( null === self::$card_style ) {
			$card_style = get_theme_mod( 'search_entry_card_style' );
			$card_style = apply_filters_deprecated( 'wpex_search_entry_card_style', [ $card_style ], 'Total 6.0', 'totaltheme/search/archive/card_style' );
			self::$card_style = (string) apply_filters( 'totaltheme/search/archive/card_style', $card_style );
		}
		return self::$card_style;
	}

}
