<?php

namespace TotalTheme\CPT;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Post Type Meta Blocks.
 */
if ( \class_exists( '\TotalTheme\Meta' ) ) {
	class Meta_Blocks extends \TotalTheme\Meta {

		/**
		 * Static-only class.
		 */
		private function __construct() {}

		/**
		 * Returns the array meta block choices.
		 */
		public static function choices() {
			return parent::registered_blocks();
		}

		/**
		 * Returns array of default blocks.
		 */
		public static function default_blocks(): array {
			return [ 'date', 'author', 'categories', 'comments' ];
		}

		/**
		 * Returns custom post type meta blocks to display.
		 */
		public static function get( $singular = null ) {
			$blocks    = self::default_blocks();
			$post_type = \get_post_type();

			// Check for block options in the PTU plugin
			if ( \totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
				if ( ! $singular ) {
					$singular = \is_singular() && \is_main_query();
				}
				if ( $singular ) {
					if ( $ptu_blocks = \wpex_get_ptu_type_mod( $post_type, 'single_meta_blocks' ) ) {
						$blocks = $ptu_blocks;
					}
				} else {
					if ( $ptu_blocks = \wpex_get_ptu_type_mod( $post_type, 'entry_meta_blocks' ) ) {
						$blocks = $ptu_blocks;
					}
				}
			}
			
			// Check for block options in the customizer
			$blocks = \get_theme_mod( "{$post_type}_single_meta_blocks", $blocks );

			if ( \is_string( $blocks ) ) {
				$blocks = $blocks ? \explode( ',', $blocks ) : [];
			}

			if ( $blocks ) {
				// Make sure only allowed blocks are displayed
				$blocks = \array_intersect( $blocks, \array_keys( self::choices() ) );
			}

			$blocks = \apply_filters( 'wpex_meta_blocks', $blocks, $post_type );

			if ( $singular ) {
				$blocks = \apply_filters( 'totaltheme/cpt/meta_blocks/singular_blocks', $blocks, $post_type );
			}  else {
				$blocks = \apply_filters( 'totaltheme/cpt/meta_blocks/entry_blocks', $blocks, $post_type );
			}

			return (array) $blocks;
		}

		/**
		 * Render custom post type meta blocks.
		 */
		public static function render( $args = [] ) {
			if ( ! isset( $args['blocks'] ) ) {
				$args['blocks'] = self::get();
			}
			parent::render_blocks( $args );
		}

		/**
		 * Echo class attribute for the the custom post type meta blocks wrapper element.
		 */
		public static function wrapper_class( $is_custom = false ) {
			$classes = [
				'meta',
				'wpex-text-sm',
				'wpex-text-3',
			];

			// Don't add margins if displaying a "custom" meta (aka not part of default archive or template design).
			if ( ! $is_custom ) {
				$classes[] = 'wpex-mt-10';
				$singular = \is_singular( \get_post_type() );

				if ( $singular ) {
					$classes[] = 'wpex-mb-20';
				} else {
					$columns = (int) \wpex_get_grid_entry_columns();
					if ( 1 === $columns ) {
						$classes[] = 'wpex-mb-20';
					} else {
						$classes[] = 'wpex-mb-15';
					}
				}
			}

			// Remove margin on last li element.
			$classes[] = 'wpex-last-mr-0';

			if ( ! $is_custom && $singular ) {
				$classes = \apply_filters( 'wpex_cpt_single_meta_class', $classes ); // @deprecated
				$classes = (array) \apply_filters( 'totaltheme/cpt/meta_blocks/singular_wrapper_class', $classes );
			} else {
				$classes = \apply_filters( 'wpex_cpt_entry_meta_class', $classes ); // @deprecated
				$classes = (array) \apply_filters( 'totaltheme/cpt/meta_blocks/entry_wrapper_class', $classes );
			}

			if ( $classes ) {
				echo 'class="' . \esc_attr( \implode( ' ', $classes ) ) . '"';
			}
		}
	}
}
