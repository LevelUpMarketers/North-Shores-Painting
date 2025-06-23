<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "dynamic_sidebar_params".
 */
final class Dynamic_Sidebar_Params {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback.
	 */
	public static function callback( $params ) {
		if ( ! \is_array( $params ) || empty( $params ) ) {
			return $params;
		}

		global $wp_registered_widgets;
		$widget_id      = $params[0]['widget_id'] ?? '';
		$widget_id_base = $wp_registered_widgets[ $widget_id ]['callback'][0]->id_base ?? '';

		$widgets_with_borders = [
			// core widgets
			'categories',
			'archives',
			'recent-posts',
			'recent-comments',
			'meta',
			'pages',

			// woocommerce widgets
			'layered_nav',
			'woocommerce_product_categories',
			'woocommerce_layered_nav',
		];

		$has_border = \in_array( $widget_id_base, $widgets_with_borders );

		if ( 'nav_menu' === $widget_id_base && \str_contains( $params[0]['id'], 'footer' ) ) {
			$has_border = true;
		}

		if ( apply_filters( 'wpex_widget_has_bordered_list', $has_border, $widget_id_base, $params ) ) {
			$params[0]['before_widget'] = \str_replace( 'class="', 'class="wpex-bordered-list ', $params[0]['before_widget'] );
		}

		return $params;
	}

}
