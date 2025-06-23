<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

final class Remove_Param_Descriptions {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		// Removes descriptions from the mapper to slim down memory usage.
		\add_filter( 'vc_mapper_attribute', [ self::class, 'filter_vc_mapper_attribute' ] );

		// Need to remove from the editor as well because WPB doesn't check if it's empty only if it's set.
		if ( \function_exists( '\vc_post_param' ) && 'vc_edit_form' === \vc_post_param( 'action' ) ) {
			\add_filter( 'vc_single_param_edit', [ self::class, 'filter_vc_single_param_edit' ], 99 );
		}
	}

	/**
	 * Hooks into the "vc_single_param_edit" filter.
	 */
	public static function filter_vc_mapper_attribute( $attribute ) {
		if ( isset( $attribute['description'] ) ) {
			unset( $attribute['description'] );
		}
		return $attribute;
	}

	/**
	 * Hooks into the "vc_single_param_edit" filter.
	 */
	public static function filter_vc_single_param_edit( $param ) {
		if ( isset( $param['description'] ) ) {
			unset( $param['description'] );
		}
		return $param;
	}

}
