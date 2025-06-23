<?php

namespace TotalTheme\Integration\Gutenberg;

\defined( 'ABSPATH' ) || exit;

/**
 * Registers block styles for core blocks.
 */
final class Register_Block_Styles {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		if ( ! \function_exists( '\register_block_style' ) ) {
			return;
		}

		// Full Height Post Template
		\register_block_style( 'core/post-template', [
			'name'  => 'full-height',
			'label' => esc_html__( 'Full Height', 'total' ),
		] );

		// Spaced Out List
		\register_block_style( 'core/list', [
			'name'  => 'list-spaced-out',
			'label' => esc_html__( 'Spaced Out', 'total' ),
		] );

	}

}
