<?php
namespace TotalTheme\CPT;

\defined( 'ABSPATH' ) || exit;

/**
 * CPT Entry Blocks.
 */
class Entry_Blocks {

	/**
	 * Returns the array of custom post type entry blocks.
	 */
	public static function choices() {
		$choices = [
			'media'    => \esc_html__( 'Media (Thumbnail, Slider, Video)', 'total' ),
			'title'    => \esc_html__( 'Title', 'total' ),
			'meta'     => \esc_html__( 'Meta', 'total' ),
			'content'  => \esc_html__( 'Content', 'total' ),
			'readmore' => \esc_html__( 'Readmore', 'total' ),
		];
		$choices = \apply_filters( 'wpex_cpt_entry_blocks_choices', $choices );
		return (array) \apply_filters( 'totaltheme/cpt/entry_blocks/choices', $choices );
	}

	/**
	 * Returns custom post type blocks to display.
	 */
	public static function get() {
		$blocks = [
			'media'    => 'media',
			'title'    => 'title',
			'meta'     => 'meta',
			'content'  => 'content',
			'readmore' => 'readmore',
		];

		// Get post type.
		$post_type = \get_post_type();

		// Get post type based options.
		if ( $post_type ) {
			$blocks = \get_theme_mod( "{$post_type}_entry_blocks", $blocks );
			if ( $ptu_blocks = \wpex_get_ptu_type_mod( $post_type, 'entry_blocks' ) ) {
				$blocks = $ptu_blocks;
			}
		}

		// Make sure blocks are an array.
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
		$blocks = \apply_filters( "wpex_{$post_type}_entry_blocks", $blocks, $post_type );
		$blocks = \apply_filters( 'wpex_entry_blocks', $blocks, $post_type );
		return (array) \apply_filters( 'totaltheme/cpt/entry_blocks', $blocks, $post_type );
	}

	/**
	 * Render custom post type blocks.
	 */
	public static function render() {
		$blocks = self::get();

		if ( empty( $blocks ) || ! \is_array( $blocks ) ) {
			return;
		}

		foreach ( $blocks as $block ) {
			if ( 'the_content' !== $block && \is_callable( $block ) ) {
				\call_user_func( $block );
			} else {
				\get_template_part( "partials/cpt/cpt-entry-{$block}", \get_post_type() );
			}
		}

	}

}
