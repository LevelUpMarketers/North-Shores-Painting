<?php
namespace TotalTheme\Widgets;

\defined( 'ABSPATH' ) || exit;

/**
 * The Wiget Block Editor Tweaks.
 */
class Block_Editor {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Disables the block editor.
	 */
	public static function disable_block_editor() {

		// Disables the block editor from managing widgets in the Gutenberg plugin.
		\add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );

		// Disables the block editor from managing widgets.
		\add_filter( 'use_widgets_block_editor', '__return_false' );

	}

}
