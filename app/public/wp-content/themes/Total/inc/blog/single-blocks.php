<?php
namespace TotalTheme\Blog;

\defined( 'ABSPATH' ) || exit;

/**
 * Blog Single Blocks.
 */
class Single_Blocks {

	/**
	 * Returns the array of standard post single blocks.
	 */
	public static function choices() {
		$choices = [
			'featured_media' => \esc_html__( 'Media','total' ),
			'title'          => \esc_html__( 'Title', 'total' ),
			'meta'           => \esc_html__( 'Meta', 'total' ),
			'post_series'    => \esc_html__( 'Post Series','total' ),
			'the_content'    => \esc_html__( 'Content','total' ),
			'post_tags'      => \esc_html__( 'Post Tags','total' ),
			'social_share'   => \esc_html__( 'Social Share Buttons','total' ),
			'author_bio'     => \esc_html__( 'Author Box','total' ),
			'related_posts'  => \esc_html__( 'Related Posts','total' ),
			'comments'       => \esc_html__( 'Comments','total' ),
		];

		$choices = (array) \apply_filters( 'wpex_blog_single_blocks', $choices, 'customizer' ); // old filter.

		/**
		 * Filters the list of single blocks allowed for standard posts.
		 *
		 * @param array $choices
		 */
		$choices = (array) \apply_filters( 'totaltheme/blog/single_blocks/choices', $choices );

		/*** deprecated ***/
		$choices = (array) \apply_filters( 'wpex_post_single_blocks_choices', $choices );

		return $choices;
	}

	/**
	 * Checks if a block is registered.
	 */
	public static function is_block_registered( $block ) {
		return \array_key_exists( $block, self::choices() );
	}

	/**
	 * Returns standard post blocks to display.
	 */
	public static function get() {
		$default_blocks = [
			'featured_media',
			'title',
			'meta',
			'post_series',
			'the_content',
			'post_tags',
			'social_share',
			'author_bio',
			'related_posts',
			'comments',
		];

		$blocks = \get_theme_mod( 'blog_single_composer' ) ?: $default_blocks;

		if ( \is_string( $blocks ) ) {
			$blocks = $blocks ? \explode( ',', $blocks ) : [];
		}

		if ( $blocks ) {

			/*
			 * Make sure only blocks registered to be selected in the customizer can render when
			 * pulled from the theme_mod.
			 */
			$blocks = \array_filter( $blocks, [ self::class, 'is_block_registered' ] );
		}

		// Set keys equal to vars for easier unsetting via hooks.
		$blocks = \array_combine( $blocks, $blocks );

		// Remove items if post is password protected.
		if ( \post_password_required() ) {
			unset( $blocks['featured_media'] );
			unset( $blocks['post_tags'] );
			unset( $blocks['social_share'] );
			unset( $blocks['author_bio'] );
			unset( $blocks['author_bio'] );
		}

		$blocks = (array) \apply_filters( 'wpex_blog_single_layout_blocks', $blocks, 'front-end' );

		/**
		 * Filters the blog post layout blocks for the frontend.
		 *
		 * @param array $blocks
		 * @param string $context
		 */
		$blocks = (array) \apply_filters( 'totaltheme/blog/single_blocks', $blocks );

		/*** deprecated ***/
		$blocks = (array) \apply_filters( 'wpex_post_single_blocks', $blocks, 'front-end' );

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

		foreach ( $blocks as $block ) {

			if ( 'the_content' !== $block && \is_callable( $block ) ) {
				\call_user_func( $block );
			} elseif ( 'title' === $block ) {
				\wpex_get_template_part( 'blog_single_title' );
			} elseif ( 'meta' === $block ) {
				\wpex_get_template_part( 'blog_single_meta' );
			} elseif ( 'featured_media' === $block ) {
				if ( \wpex_get_custom_post_media_position() ) {
					continue;
				}
				\wpex_get_template_part( 'blog_single_media' );
			} elseif ( 'post_series' === $block ) {
				\wpex_get_template_part( 'post_series' );
			} elseif ( 'the_content' === $block ) {
				\wpex_get_template_part( 'blog_single_content' );
			} elseif ( 'post_tags' === $block ) {
				\wpex_get_template_part( 'blog_single_tags' );
			} elseif ( 'social_share' === $block ) {
				\wpex_social_share();
			} elseif ( 'author_bio' === $block ) {
				if ( 'hide' !== \get_post_meta( \get_the_ID(), 'wpex_post_author', true ) ) {
					\wpex_get_template_part( 'author_bio' );
				}
			} elseif ( 'related_posts' === $block ) {
				\wpex_get_template_part( 'blog_single_related' );
			} elseif ( 'comments' === $block ) {
				\comments_template();
			} else {
				\get_template_part( 'partials/blog/blog-single', $block );
			}

		}

	}

	/**
	 * Echo class attribute for the single standard post blocks wrapper element.
	 */
	public static function wrapper_class() {
		$classes = [
			'single-blog-article',
			'wpex-first-mt-0',
		];
		$classes[] = 'wpex-clr';
		$classes = (array) \apply_filters( 'wpex_blog_single_blocks_class', $classes ); // @deprecated
		$classes = (array) \apply_filters( 'totaltheme/blog/single_blocks/wrapper_class', $classes );
		if ( $classes ) {
			echo 'class="' . \esc_attr( \implode( ' ', $classes ) ) . '"';
		}
	}

}
