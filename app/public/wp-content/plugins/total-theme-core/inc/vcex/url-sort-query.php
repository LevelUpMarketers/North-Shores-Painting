<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Class used for query sorting based on URL params.
 */
class Url_Sort_Query {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Stores the sort query.
	 */
	protected static $query;

	/**
	 * Returns url query sorting prefix.
	 */
	public static function get_prefix( ) {
		return (string) \apply_filters( 'totalthemecore/vcex/url_query_sort/prefix', '_sort_' );
	}

	/**
	 * Checks if the current page has a custom sort query.
	 */
	public static function has_query( ) {
		return (bool) self::get_query();
	}

	/**
	 * Returns the current page query.
	 */
	public static function get_query() {
		if ( ! \is_null( self::$query ) ) {
			return self::$query;
		}
		self::$query = [];
		if ( ! empty( $_GET ) ) {
			$sort_prefix = self::get_prefix();
			foreach ( $_GET as $get_k => $get_v ) {
				if ( \str_starts_with( $get_k, $sort_prefix ) ) {
					$key = \str_replace( $sort_prefix, '', $get_k );
					if ( $key ) {
						self::$query[ $key ] = \sanitize_text_field( \urldecode( $get_v ) );
					}
				}
			}
		}
		return self::$query;
	}

	/**
	 * Returns the value of a specific query param.
	 */
	public static function get_query_param_value( $param = '' ) {
		return self::get_query()[$param] ?? '';
	}

	/**
	 * Checks if a given sorting element is active on page load.
	 */
	public static function is_trigger_active( $args ): bool {
		if ( empty( $args['type'] ) || empty( $args['value'] ) ) {
			return false;
		}
		$val_type     = $args['type'];
		$val_to_check = \strval( $args['value'] );
		$param_value  = self::get_query_param_value( $val_type );
		if ( $param_value ) {
			if ( $param_value === $val_to_check ) {
				return true;
			} elseif ( \str_contains( $param_value, '|' ) ) {
				// @todo add support for custom fields.
			} elseif ( \str_contains( $param_value, ',' ) ) {
				if ( \in_array( $val_to_check, \explode( ',', $param_value ) ) ) {
					return true;
				}
			} elseif ( is_numeric( $val_to_check ) ) {
				if ( \taxonomy_exists( $val_type ) ) {
					$term = get_term_by( 'ID', $val_to_check, $val_type );
					if ( isset( $term->slug ) && $term->slug === $param_value ) {
						return true;
					}
				}
			}
		}
		return false;
	}

}
