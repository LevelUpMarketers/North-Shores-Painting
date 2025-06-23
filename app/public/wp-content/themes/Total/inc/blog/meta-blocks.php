<?php

namespace TotalTheme\Blog;

\defined( 'ABSPATH' ) || exit;

/**
 * Blog Meta Blocks.
 */
if ( class_exists( '\TotalTheme\Meta' ) ) {
	class Meta_Blocks extends \TotalTheme\Meta {

		/**
		 * Returns the array meta block choices.
		 */
		public static function choices() {
			return parent::registered_blocks();
		}

		/**
		 * Returns blog meta blocks to display.
		 */
		public static function get( $singular = true ) {
			if ( $singular ) {
				return self::singular_blocks();
			}
			return self::entry_blocks();
		}

		/**
		 * Returns blog singular meta blocks to display.
		 */
		public static function singular_blocks() {
			$blocks = [
				'date',
				'author',
				'categories',
				'comments',
			];

			$blocks = \get_theme_mod( 'blog_post_meta_sections', $blocks );

			if ( \is_string( $blocks ) ) {
				$blocks = $blocks ? \explode( ',', $blocks ) : [];
			}

			if ( $blocks && is_array( $blocks ) ) {

				// Only allowed sections.
				$blocks = \array_intersect( $blocks, \array_keys( self::choices() ) );

				// Set keys equal to values for easier modification.
				$blocks = \array_combine( $blocks, $blocks );
			}

			$blocks = (array) \apply_filters( 'wpex_blog_single_meta_sections', $blocks ); // @deprecated

			return (array) \apply_filters( 'totaltheme/blog/meta_blocks/singular_blocks', $blocks );
		}

		/**
		 * Returns blog entry meta blocks to display.
		 */
		public static function entry_blocks() {
			$default_blocks = [
				'date',
				'author',
				'categories',
				'comments',
			];

			// Get Sections from Customizer.
			$blocks = \get_theme_mod( 'blog_entry_meta_sections', $default_blocks );

			if ( \is_string( $blocks ) ) {
				$blocks = $blocks ? \explode( ',', $blocks ) : [];
			}

			if ( $blocks && \is_array( $blocks ) ) {

				// Only allowed sections are allowed.
				$blocks = \array_intersect( $blocks, \array_keys( self::choices() ) );

				// Set keys equal to values for easier modification.
				$blocks = \array_combine( $blocks, $blocks );

				// Remove comments for link format.
				if ( isset( $blocks['comments'] ) && 'link' === \get_post_format() ) {
					unset( $blocks['comments'] );
				}

			}

			$blocks = (array) \apply_filters( 'wpex_blog_entry_meta_sections', $blocks ); // @deprecated

			return (array) \apply_filters( 'totaltheme/blog/meta_blocks/entry_blocks', $blocks );
		}

		/**
		 * Render blog meta blocks.
		 */
		public static function render( $args = [] ) {
			if ( ! isset( $args['blocks'] ) ) {
				$args['blocks'] = self::get();
			}
			parent::render_blocks( $args );
		}

		/**
		 * Echo class attribute for the the blog meta blocks wrapper element.
		 */
		public static function wrapper_class( $singular = false ) {
			if ( $singular ) {
				self::singular_wrapper_class();
			} else {
				self::entry_wrapper_class();
			}
		}

		/**
		 * Echo class attribute for the the singular blog meta blocks wrapper element.
		 */
		public static function singular_wrapper_class() {
			$classes = [
				'meta',
				'wpex-text-sm',
				'wpex-text-3',
				'wpex-mb-20',
				'wpex-last-mr-0',
			];

			$classes = \apply_filters( 'wpex_blog_single_meta_class', $classes ); // @deprecated
			$classes = (array) \apply_filters( 'totaltheme/blog/meta_blocks/singular_wrapper_class', $classes );

			if ( $classes ) {
				echo 'class="' . \esc_attr( \implode( ' ', $classes ) ) . '"';
			}
		}

		/**
		 * Echo class attribute for the the entry blog meta blocks wrapper element.
		 */
		public static function entry_wrapper_class() {
			$classes = [
				'blog-entry-meta',
				'entry-meta',
				'meta',
				'wpex-text-sm',
				'wpex-text-3',
				'wpex-last-mr-0',
			];

			$entry_style = \wpex_blog_entry_style();

			switch ( $entry_style ) {
				case 'grid-entry-style':
					$classes[] = 'wpex-mb-15';
					break;
				default:
					$classes[] = 'wpex-mb-20';
					break;
			}

			$classes = \apply_filters( 'wpex_blog_entry_meta_class', $classes ); // @deprecated
			$classes = (array) \apply_filters( 'totaltheme/blog/meta_blocks/entry_wrapper_class', $classes );

			if ( $classes ) {
				echo 'class="' . \esc_attr( \implode( ' ', \array_unique( $classes ) ) ) . '"';
			}
		}

	}
}
