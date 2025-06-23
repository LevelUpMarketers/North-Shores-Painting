<?php

namespace TotalTheme\Integration\Gutenberg;

\defined( 'ABSPATH' ) || exit;

/**
 * Dequeue Gutenberg Styles.
 */
class Dequeue_Styles {

	/**
	 * Init.
	 */
	public static function init() {
		\add_action( 'wp_enqueue_scripts', [ self::class, 'on_wp_enqueue_scripts' ], 100 );
		self::remove_actions();
	}

	/**
	 * Hooks into wp_enqueue_scripts to remove scripts.
	 */
	public static function on_wp_enqueue_scripts() {
		$styles_list = self::get_styles_list();

		foreach ( $styles_list as $style_handle ) {
			\wp_dequeue_style( $style_handle );
		}
	}

	/**
	 * Return list of styles to remove.
	 */
	protected static function get_styles_list(): array {
		$list = [];

		if ( self::check_for_blocks() ) {
			return $list;
		}

		$list[] = 'global-styles';
		$list[] = 'wp-block-library';
		$list[] = 'wp-block-library-theme';

		return (array) apply_filters( 'totaltheme/integration/gutenberg/dequeue_styles/styles_list', $list );
	}

	/**
	 * Checks if the current post has blocks.
	 */
	protected static function check_for_blocks(): bool {
		return ( \is_singular() && \has_blocks() );
	}

	/**
	 * Removes actions that insert extra junk to the site.
	 *
	 * @todo update code once WP updates to add proper filter.
	 */
	protected static function remove_actions() {

		// remove SVG and global styles
		\remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
		\remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );

		// remove wp_footer actions which add's global inline styles
		\remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

		// remove render_block filters which add unnecessary stuff.
		\remove_filter( 'render_block', 'wp_render_duotone_support' );
		\remove_filter( 'render_block', 'wp_restore_group_inner_container' );
		\remove_filter( 'render_block', 'wp_render_layout_support_flag' );
	}

}
