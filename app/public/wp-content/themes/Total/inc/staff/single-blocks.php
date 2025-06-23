<?php

namespace TotalTheme\Staff;

\defined( 'ABSPATH' ) || exit;

/**
 * Staff Single Blocks.
 */
class Single_Blocks {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns the array of staff single blocks.
	 */
	public static function choices() {
		$choices = [
			'title'    => \esc_html__( 'Post Title', 'total' ),
			'meta'     => \esc_html__( 'Meta', 'total' ),
			'media'    => \esc_html__( 'Media', 'total' ),
			'content'  => \esc_html__( 'Content', 'total' ),
			'social'   => \esc_html__( 'Social Links', 'total' ),
			'share'    => \esc_html__( 'Social Share Buttons', 'total' ),
			'comments' => \esc_html__( 'Comments', 'total' ),
			'related'  => \esc_html__( 'Related Posts', 'total' ),
		];

		/**
		 * Filters the list of single blocks allowed for staff posts.
		 *
		 * @param array $choices
		 */
		$choices = \apply_filters( 'totaltheme/staff/single_blocks/choices', $choices );

		/*** deprecated ***/
		$choices = \apply_filters( 'wpex_staff_single_blocks_choices', $choices );

		return (array) $choices;
	}

	/**
	 * Checks if a block is registered.
	 *
	 * This function checks deprecated filters to ensure custom blocks registered using old methods still work
	 * but ensures a block assigned in the Customizer is actually registered for security reasons.
	 */
	public static function is_block_registered( $block ) {
		if ( \array_key_exists( $block, self::choices() )
			|| \array_key_exists( $block, \apply_filters( 'wpex_staff_single_blocks', [], 'customizer' ) )
		) {
			return true;
		}
		$single_blocks = (array) \apply_filters( 'wpex_staff_single_blocks', [], 'front-end' );
		if ( \array_key_exists( $block, $single_blocks ) || \in_array( $block, $single_blocks ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Returns staff post blocks to display.
	 */
	public static function get() {
		$default_blocks = [
			'content',
			'related',
		];

		$blocks = \get_theme_mod( 'staff_post_composer' ) ?: $default_blocks;

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

		/**
		 * Filters the single staff blocks.
		 *
		 * @param array $blocks
		 */
		$blocks = \apply_filters( 'totaltheme/staff/single_blocks', $blocks );

		/*** deprecated ***/
		$blocks = \apply_filters( 'wpex_staff_single_blocks', $blocks, 'front-end' );

		return (array) $blocks;
	}

	/**
	 * Render Staff post blocks.
	 */
	public static function render() {
		$blocks = self::get();

		if ( empty( $blocks ) || ! is_array( $blocks ) ) {
			return;
		}

		foreach ( $blocks as $block ) {
			if ( 'the_content' !== $block && \is_callable( $block ) ) {
				\call_user_func( $block );
			} else {
				\get_template_part( "partials/staff/staff-single-{$block}" );
			}
		}
	}

	/**
	 * Echo class attribute for the single staff blocks wrapper element.
	 */
	public static function wrapper_class() {
		$class = [
			'wpex-first-mt-0',
			'wpex-clr',
		];

		/**
		 * Filters the staff single blocks element class.
		 *
		 * @param array $class
		 */
		$class = \apply_filters( 'totaltheme/staff/single_blocks/wrapper_class', $class );

		/*** deprecated ***/
		$class = \apply_filters( 'wpex_staff_single_blocks_class', $class );

		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', \array_unique( (array) $class ) ) ) . '"';
		}
	}

}
