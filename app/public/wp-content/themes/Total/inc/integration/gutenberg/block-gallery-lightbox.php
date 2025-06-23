<?php

namespace TotalTheme\Integration\Gutenberg;

\defined( 'ABSPATH' ) || exit;

/**
 * Gutenberg Block Gallery Lightbox.
 */
class Block_Gallery_Lightbox {

	/**
	 * Name for our custom script tag.
	 */
	protected const SCRIPT_TAG = 'wpex-block-gallery-lightbox';

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		\add_action( 'wp_enqueue_scripts', [ self::class, 'register_scripts' ] );
		\add_filter( 'render_block', [ self::class, 'on_render_block'], 10, 2 );
	}

	/**
	 * Register scripts.
	 */
	public static function register_scripts(): void {
		\wp_register_script(
			self::SCRIPT_TAG,
			\totaltheme_get_js_file( 'frontend/blocks/gallery-lightbox' ),
			[],
			WPEX_THEME_VERSION,
			true
		);
	}

	/**
	 * Equeue script if needed.
	 */
	public static function on_render_block( $block_content, $block ) {
		if ( isset( $block['blockName'] )
			&& 'core/gallery' === $block['blockName']
			&& isset( $block['attrs']['linkTo'] )
			&& 'media' === $block['attrs']['linkTo']
		) {
			\wpex_enqueue_lightbox_scripts();
			\wp_enqueue_script( self::SCRIPT_TAG );
		}
		return $block_content;
	}

}
