<?php
namespace TotalTheme\Page;

\defined( 'ABSPATH' ) || exit;

/**
 * Page Single Blocks.
 */
class Single_Blocks {

	/**
	 * Returns the array of page single blocks.
	 */
	public static function choices() {
		$choices = [
			'title'    => \esc_html__( 'Title', 'total' ),
			'media'    => \esc_html__( 'Media', 'total' ),
			'content'  => \esc_html__( 'Content', 'total' ),
			'share'    => \esc_html__( 'Social Share Buttons', 'total' ),
			'comments' => \esc_html__( 'Comments', 'total' ),
		];
		$choices = \apply_filters( 'wpex_page_single_blocks_choices', $choices ); // @deprecated
		$choices = (array) \apply_filters( 'totaltheme/page/single_blocks/choices', $choices );
		return $choices;
	}

	/**
	 * Checks if a block is registered.
	 *
	 * This function checks deprecated filters to ensure custom blocks registered using old methods still work
	 * but ensures a block assigned in the Customizer is actually registered for security reasons.
	 */
	public static function is_block_registered( $block ) {
		if ( \array_key_exists( $block, self::choices() ) || \array_key_exists( $block, \apply_filters( 'wpex_page_single_blocks', [], 'customizer' ) ) ) {
			return true;
		}
		$single_blocks = (array) \apply_filters( 'wpex_page_single_blocks', [], 'page' );
		if ( \array_key_exists( $block, $single_blocks ) || in_array( $block, $single_blocks ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Returns blocks to display.
	 */
	public static function get() {
		$default_blocks = [
			'content'
		];

		$blocks = \get_theme_mod( 'page_composer', $default_blocks );

		if ( \is_string( $blocks ) ) {
			$blocks = $blocks ? \explode( ',', $blocks ) : [];
		}

		if ( $blocks ) {
			$blocks = \array_filter( $blocks, [ self::class, 'is_block_registered' ] );
		}

		// Set keys equal to values for easier filter removal.
		$blocks = \array_combine( $blocks, $blocks );

		// Toolset checks.
		if ( defined( 'TYPES_VERSION' ) && $blocks ) {
			foreach ( $blocks as $block ) {
				if ( ! get_theme_mod( "cpt_single_block_{$block}_enabled", true ) ) {
					unset( $blocks[$block] );
				}
			}
		}

		$blocks = \apply_filters( 'wpex_page_single_blocks', $blocks, 'page' ); // @deprecated
		$blocks = \apply_filters( 'wpex_single_blocks', $blocks, 'page' ); // @deprecated
		$blocks = (array) \apply_filters( 'totaltheme/page/single_blocks', $blocks );
		return $blocks;
	}

	/**
	 * Render Blocks.
	 */
	public static function render() {
		$blocks = self::get();

		if ( empty( $blocks ) || ! \is_array( $blocks ) ) {
			return;
		}

		foreach ( $blocks as $block ) {
			if ( 'media' === $block && \wpex_has_custom_post_media_position() ) {
				continue;
			}

			if ( 'the_content' !== $block && \is_callable( $block ) ) {
				\call_user_func( $block );
			} else {
				\get_template_part( 'partials/page-single-' . $block );
			}
		}
	}

	/**
	 * Echo class attribute for blocks wrapper element.
	 */
	public static function wrapper_class() {
		$class = [
			'single-page-article',
			'wpex-clr',
		];

		/**
		 * Filters the single page blocks class.
		 *
		 * @param array $class
		 */
		$class = (array) \apply_filters( 'totaltheme/page/single_blocks/wrapper_class', $class );

		/*** deprecated ***/
		$class = (array) \apply_filters( 'wpex_page_single_blocks_class', $class );

		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

}
