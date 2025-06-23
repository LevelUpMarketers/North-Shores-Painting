<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

final class Inline_CSS {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init Hooks.
	 */
	public static function init(): void {
		\add_filter( 'wpex_head_css', [ self::class, 'add_css' ] );
	}

	/**
	 * Hook into wpex_head_css to add inline CSS for WPBakery.
	 */
	public static function add_css( string $css ): string {
		if ( $wpbakery_css = self::generate_css() ) {
			$css .= $wpbakery_css;
		}
		return $css;
	}

	/**
	 * Get template ID's that we need to load CSS for.
	 *
	 * @todo add needed style tag inline instead.
	 */
	protected static function get_template_ids(): array {
		$ids = [];
		if ( \wpex_is_woo_shop() && $shop_id = \totaltheme_wc_get_page_id( 'shop' ) ) {
			$ids[] = $shop_id;
		}
		$ids = (array) \apply_filters( 'wpex_vc_css_ids', $ids );
		$ids = \array_map( 'intval', $ids ); // sanitize
		if ( $ids ) {
			$ids = \array_unique( \array_filter( $ids ) );
		}
		return $ids;
	}

	/**
	 * Generate CSS.
	 */
	protected static function generate_css(): string {
		$css          = '';
		$template_ids = (array) self::get_template_ids();

		foreach ( $template_ids as $id ) {

			// Conditional checks, some CSS isn't necessarily needed globally.
			if ( \function_exists( 'is_shop' ) && \is_shop() ) {
				$condition = true; // Always return true for the shop
			} else {
				$condition = ( $id == \wpex_get_current_post_id() ) ? false : true;
			}

			if ( $condition && $vc_css = \get_post_meta( $id, '_wpb_shortcodes_custom_css', true ) ) {
				$css .= "/*VC META CSS*/{$vc_css}";
			}
		}

		return $css;
	}

}
