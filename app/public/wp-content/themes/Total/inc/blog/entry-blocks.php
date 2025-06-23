<?php

namespace TotalTheme\Blog;

\defined( 'ABSPATH' ) || exit;

/**
 * Blog Entry Blocks.
 */
class Entry_Blocks {

	/**
	 * Returns the array of standard post entry blocks.
	 */
	public static function choices(): array {
		$choices = [
			'featured_media'  => \esc_html__( 'Media', 'total' ),
			'title'           => \esc_html__( 'Title', 'total' ),
			'meta'            => \esc_html__( 'Meta', 'total' ),
			'excerpt_content' => \esc_html__( 'Excerpt', 'total' ),
			'readmore'        => \esc_html__( 'Read More', 'total' ),
		];

		/**
		 * Filters the list of entry blocks allowed for standard posts.
		 *
		 * @param array $choices
		 * @param string $instance | @todo deprecate
		 */
		$choices = (array) \apply_filters( 'totaltheme/blog/entry_blocks/choices', $choices );

		/*** deprecated ***/
		$choices = (array) \apply_filters( 'wpex_blog_entry_blocks', $choices, 'customizer' );

		return $choices;
	}

	/**
	 * Returns standard post blocks to display.
	 */
	public static function get() {
		$default_blocks = [
			'featured_media',
			'title',
			'meta',
			'excerpt_content',
			'readmore',
		];

		$blocks = \get_theme_mod( 'blog_entry_composer' ) ?: $default_blocks;

		if ( \is_string( $blocks ) ) {
			$blocks = $blocks ? \explode( ',', $blocks ) : [];
		}

		if ( $blocks ) {

			/*
			 * Make sure only blocks registered to be selected in the customizer can render when
			 * pulled from the theme_mod.
			 */
			$blocks = \array_intersect( $blocks, \array_keys( self::choices() ) );
		}

		$blocks = \array_combine( $blocks, $blocks );

		/**
		 * Filters the blog entry blocks.
		 *
		 * @param array $blocks
		 * @param string $instance | @deprecate as it's not needed.
		 */
		$blocks = (array) \apply_filters( 'totaltheme/blog/entry_blocks', $blocks, 'front-end' );

		/*** deprecated ***/
		$blocks = (array) \apply_filters( 'wpex_blog_entry_layout_blocks', $blocks, 'front-end' );

		return $blocks;
	}

	/**
	 * Render standard post blocks.
	 */
	public static function render() {
		$blocks = self::get();

		if ( empty( $blocks ) || ! \is_array( $blocks ) ) {
			return;
		}

		// Thumbnail entry style uses different layout.
		if ( 'thumbnail-entry-style' === \wpex_blog_entry_style() ) {

			\get_template_part( 'partials/blog/blog-entry-media' );

			?>

			<div <?php \wpex_blog_entry_content_class(); ?>><?php
				foreach ( $blocks as $block ) {
					if ( \is_callable( $block ) ) {
						\call_user_func( $block );
					} elseif ( 'title' === $block ) {
						\get_template_part( 'partials/blog/blog-entry-title' );
					} elseif ( 'meta' === $block ) {
						\get_template_part( 'partials/blog/blog-entry-meta' );
					} elseif ( 'excerpt_content' === $block ) {
						\get_template_part( 'partials/blog/blog-entry-content' );
					} elseif ( 'readmore' === $block ) {
						if ( \wpex_has_readmore() ) {
							\get_template_part( 'partials/blog/blog-entry-readmore' );
						}
					} else {
						\get_template_part( "partials/blog/blog-entry-{$block}" );
					}
				}
			?></div>

		<?php

		// Other entry styles.
		} else {

			foreach ( $blocks as $block ) {
				if ( \is_callable( $block ) ) {
					\call_user_func( $block );
				} elseif ( 'featured_media' === $block ) {
					\get_template_part( 'partials/blog/blog-entry-media' );
				} elseif ( 'title' === $block ) {
					\get_template_part( 'partials/blog/blog-entry-title' );
				} elseif ( 'meta' === $block ) {
					\get_template_part( 'partials/blog/blog-entry-meta' );
				} elseif ( 'excerpt_content' === $block ) {
					\get_template_part( 'partials/blog/blog-entry-content' );
				} elseif ( 'readmore' === $block ) {
					if ( \wpex_has_readmore() ) {
						\get_template_part( 'partials/blog/blog-entry-readmore' );
					}
				} else {
					\get_template_part( "partials/blog/blog-entry-{$block}" );
				}
			}

		}

	}

}
