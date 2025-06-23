<?php

namespace TotalTheme\Search;

\defined( 'ABSPATH' ) || exit;

/**
 * Search Entry.
 */
class Entry extends Archive {

	/**
	 * Static class only.
	 */
	private function __construct() {}

	/**
	 * Excerpt length
	 */
	public static function excerpt_length(): int {
		$length = get_theme_mod( 'search_entry_excerpt_length', 30 );
		$length = apply_filters_deprecated( 'wpex_search_entry_excerpt_length', [ $length ], 'Total 6.0', 'totaltheme/search/entry/excerpt_length' );
		return (int) \apply_filters( 'totaltheme/search/entry/excerpt_length', $length );
	}

	/**
	 * Renders the entry card.
	 */
	public static function render_card(): bool {
		$card_style = self::card_style();

		if ( ! $card_style ) {
			return false;
		}

		$args = [
			'style'          => $card_style,
			'post_id'        => get_the_ID(),
			'thumbnail_size' => 'search_results',
			'excerpt_length' => self::excerpt_length(),
		];

		if ( $overlay = totaltheme_call_static( 'Overlays', 'get_entry_image_overlay_style' ) ) {
			$args['thumbnail_overlay_style'] = $overlay;
		}

		$args = apply_filters_deprecated( 'wpex_search_entry_card_args', [ $args ] , 'Total 6.0', 'totaltheme/search/entry/card_args' );
		$args = (array) \apply_filters( 'totaltheme/search/entry/card_args', $args );

		wpex_card( $args );

		return true;
	}

	/**
	 * Output wrapper class.
	 */
	public static function wrapper_class(): void {
		$columns = self::columns();

		$class = [
			'search-entry',
		];

		if ( self::card_style() || ( is_numeric( $columns ) && (int) $columns > 1 ) ) {
			$col_class = wpex_row_column_width_class( $columns );
			if ( $col_class ) {
				$class[] = 'col';
				$class[] = $col_class;
			}
			if ( $loop_counter = wpex_get_loop_counter() ) {
				$class[] = 'col-' . absint( $loop_counter );
			}
			$grid_style = get_theme_mod( 'search_archive_grid_style', 'fit-rows' );
			if ( 'masonry' === $grid_style || 'no-margins' === $grid_style ) {
				$class[] = 'wpex-masonry-col';
			}
		}

		$class = apply_filters_deprecated( 'wpex_search_entry_class', [ $class ], 'Total 6.0', 'totaltheme/search/entry/wrapper_class' );
		$class = (array) apply_filters( 'totaltheme/search/entry/wrapper_class', $class );

		post_class( $class );
	}

	/**
	 * Output inner class.
	 */
	public static function inner_class(): void {
		$class = [
			'search-entry-inner',
			'wpex-flex',
			'wpex-last-mb-0',
		];
		$class = apply_filters_deprecated( 'wpex_search_entry_inner_class', [ $class ], 'Total 6.0', 'totaltheme/search/entry/inner_class' );
		$class = (array) apply_filters( 'totaltheme/search/entry/inner_class', $class );
		if ( $class ) {
			echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Ouput content class.
	 */
	public static function content_class(): void {
		$class = [
			'search-entry-text',
			'wpex-flex-grow',
			'wpex-last-mb-0',
		];
		$class = apply_filters_deprecated( 'wpex_search_entry_content_class', [ $class ], 'Total 6.0', 'totaltheme/search/entry/content_class' );
		$class = (array) apply_filters( 'totaltheme/search/entry/content_class', $class );
		if ( $class ) {
			echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Excerpt class.
	 */
	public static function excerpt_class(): void {
		$class = [
			'search-entry-excerpt',
			'wpex-my-15',
			'wpex-last-mb-0',
			'wpex-clr',
		];
		$class = apply_filters_deprecated( 'wpex_search_entry_excerpt_class', [ $class ], 'Total 6.0', 'totaltheme/search/entry/excerpt_class' );
		$class = (array) apply_filters( 'totaltheme/search/entry/excerpt_class', $class );
		if ( $class ) {
			echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Header class.
	 */
	public static function header_class(): void {
		$class = [
			'search-entry-header',
		];
		$class = apply_filters_deprecated( 'wpex_search_entry_header_class', [ $class ], 'Total 6.0', 'totaltheme/search/entry/header_class' );
		$class = (array) apply_filters( 'totaltheme/search/entry/header_class', $class );
		if ( $class ) {
			echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Title class.
	 */
	public static function title_class(): void {
		$class = [
			'search-entry-header-title',
			'entry-title',
			'wpex-text-lg',
		];
		$class = apply_filters_deprecated( 'wpex_search_entry_title_class', [ $class ], 'Total 6.0', 'totaltheme/search/entry/title_class' );
		$class = (array) apply_filters( 'totaltheme/search/entry/title_class', $class );
		if ( $class ) {
			echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Output divider.
	 */
	public static function divider(): void {
		echo apply_filters( 'wpex_search_entry_divider', '<div class="search-entry-divider wpex-divider wpex-my-25"></div>' );
	}

}
