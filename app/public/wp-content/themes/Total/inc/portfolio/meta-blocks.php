<?php

namespace TotalTheme\Portfolio;

\defined( 'ABSPATH' ) || exit;

/**
 * Portfolio Meta Blocks.
 */
if ( \class_exists( '\TotalTheme\Meta' ) ) {
	class Meta_Blocks extends \TotalTheme\Meta {

		/**
		 * Static-only class.
		 */
		private function __construct() {}

		/**
		 * Returns portfolio meta blocks to display.
		 */
		public static function get( $singular = true ) {
			$blocks = [
				'date',
				'author',
				'categories',
				'comments',
			];

			/**
			 * Filter the single portfolio meta sections.
			 *
			 * @param array|string $blocks
			 */
			$blocks = \apply_filters( 'totaltheme/portfolio/meta_blocks/singular_blocks', $blocks );

			/*** deprecated ***/
			$blocks = \apply_filters( 'wpex_portfolio_single_meta_sections', $blocks );

			if ( is_string( $blocks ) ) {
				$blocks = $blocks ? explode( ',', $blocks ) : [];
			}

			return $blocks;
		}

		/**
		 * Render portfolio meta blocks.
		 */
		public static function render( $args = [] ) {
			if ( ! isset( $args['blocks'] ) ) {
				$args['blocks'] = self::get();
			}

			parent::render_blocks( $args );
		}

		/**
		 * Echo class attribute for the the portfolio meta blocks wrapper element.
		 */
		public static function wrapper_class( $singular = true ) {
			$class = [
				'meta',
				'wpex-text-sm',
				'wpex-text-3',
				'wpex-mb-20',
				'wpex-last-mr-0',
			];

			/**
			 * Filters the portfolio post meta element class.
			 *
			 * @param array $class
			 */
			$class = (array) \apply_filters( 'totaltheme/portfolio/meta_blocks/singular_wrapper_class', $class );

			/*** deprecated ***/
			$class = (array) \apply_filters( 'wpex_portfolio_single_meta_class', $class );

			if ( $class ) {
				echo 'class="' . \esc_attr( \implode( ' ', \array_unique( $class ) ) ) . '"';
			}
		}
	}
}
