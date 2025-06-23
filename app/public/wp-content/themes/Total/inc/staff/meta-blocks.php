<?php

namespace TotalTheme\Staff;

\defined( 'ABSPATH' ) || exit;

/**
 * Staff Meta Blocks.
 */
if ( \class_exists( '\TotalTheme\Meta' ) ) {
	class Meta_Blocks extends \TotalTheme\Meta {

		/**
		 * Returns portfolio meta blocks to display.
		 */
		public static function get( $singular = true ) {
			$blocks = [
				'date',
				'categories'
			];

			/**
			 * Filters the staff single meta sections.
			 *
			 * @param array|string $blocks
			 */
			$blocks = \apply_filters( 'totaltheme/staff/meta_blocks/singular_blocks', $blocks );

			/*** deprecated ***/
			$blocks = \apply_filters( 'wpex_staff_single_meta_sections', $blocks );

			if ( $blocks && ! \is_array( $blocks ) ) {
				$blocks = \explode( ',', $blocks );
			}

			return $blocks;
		}

		/**
		 * Render portfolio meta blocks.
		 */
		public static function render( $args = array() ) {
			if ( ! isset( $args['blocks'] ) ) {
				$args['blocks'] = self::get();
			}

			parent::render_blocks( $args );
		}

		/**
		 * Echo class attribute for the the staff meta blocks wrapper element.
		 */
		public static function wrapper_class( $singular = true ) {
			$class = [
				'meta',
				'wpex-text-sm',
				'wpex-text-3',
				'wpex-mb-20',
				'wpex-last-mr-0',
			];
			$class = (array) \apply_filters( 'wpex_staff_single_meta_class', $class ); // @deprecated
			$class = (array) \apply_filters( 'totaltheme/staff/meta_blocks/singular_wrapper_class', $class );
			if ( $class ) {
				echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
			}
		}
	}
}
