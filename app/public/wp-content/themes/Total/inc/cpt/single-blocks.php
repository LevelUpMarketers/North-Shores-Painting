<?php

namespace TotalTheme\CPT;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Post Type Single Blocks.
 */
class Single_Blocks {

	/**
	 * Returns array of default blocks.
	 */
	public static function default_blocks(): array {
		return [
			'media',
			'title',
			'meta',
			'post-series',
			'content',
			'page-links',
			'share',
			'comments',
		];
	}

	/**
	 * Returns the array of custom post type single blocks.
	 */
	public static function choices() {
		$choices = [
			'media'       => \esc_html__( 'Media', 'total' ),
			'title'       => \esc_html__( 'Title', 'total' ),
			'meta'        => \esc_html__( 'Meta', 'total' ),
			'post-series' => \esc_html__( 'Post Series', 'total' ),
			'content'     => \esc_html__( 'Content', 'total' ),
			'page-links'  => \esc_html__( 'Page Links', 'total' ),
			'share'       => \esc_html__( 'Social Share', 'total' ),
			'author-bio'  => \esc_html__( 'Author Bio', 'total' ),
			'related'     => \esc_html__( 'Related', 'total' ),
			'comments'    => \esc_html__( 'Comments', 'total' ),
		];
		$choices = \apply_filters( 'wpex_cpt_single_blocks_choices', $choices );
		return (array) \apply_filters( 'totaltheme/cpt/single_blocks/choices', $choices );
	}

	/**
	 * Returns custom post type post blocks to display.
	 */
	public static function get( $post_type = '' ) {
		if ( ! $post_type ) {
			$post_type = \get_post_type();
		}

		$blocks = \get_theme_mod( "{$post_type}_single_blocks", self::default_blocks() );

		if ( $ptu_blocks = \wpex_get_ptu_type_mod( $post_type, 'single_blocks' ) ) {
			$blocks = $ptu_blocks;
		}

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

		// Set keys equal to vars for easier unsetting via hooks.
		$blocks = \array_combine( $blocks, $blocks );

		if ( \defined( 'TYPES_VERSION' ) && $blocks ) {
			foreach ( $blocks as $block ) {
				if ( ! \get_theme_mod( "cpt_single_block_{$block}_enabled", true ) ) {
					unset( $blocks[ $block ] );
				}
			}
		}

		$blocks = \apply_filters( "wpex_{$post_type}_single_blocks", $blocks, $post_type );
		$blocks = \apply_filters( 'wpex_single_blocks', $blocks, $post_type );
		return (array) \apply_filters( 'totaltheme/cpt/single_blocks', $blocks, $post_type );
	}

	/**
	 * Render CPT post blocks.
	 */
	public static function render() {
		$blocks = self::get();
		if ( empty( $blocks ) || ! \is_array( $blocks ) ) {
			return;
		}
		foreach ( $blocks as $block ) {
			if ( 'media' === $block && \wpex_has_custom_post_media_position() ) {
				continue; // Media not needed for this position.
			}
			if ( 'the_content' !== $block && \is_callable( $block ) ) {
				\call_user_func( $block );
			} else {
				\get_template_part( "partials/cpt/cpt-single-{$block}", \get_post_type() );
			}
		}
	}

	/**
	 * Echo class attribute for the single custom post type blocks wrapper element.
	 */
	public static function wrapper_class() {
		$class = [
			'wpex-first-mt-0',
			'wpex-clr',
		];
		$class = \apply_filters( 'wpex_cpt_single_blocks_class', $class );
		$class = (array) \apply_filters( 'totaltheme/cpt/single_blocks/wrapper_class', $class );
		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

}
