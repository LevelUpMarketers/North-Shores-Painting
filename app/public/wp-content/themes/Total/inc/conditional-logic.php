<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Handles conditional logic checks.
 */
class Conditional_Logic {

	/**
	 * Result.
	 */
	public $result = false;

	/**
	 * Constructor.
	 */
	public function __construct( $conditions ) {
		if ( \is_string( $conditions ) ) {
			$conditions = \str_replace( ' ', '', $conditions );
			\parse_str( $conditions, $conditions );
		}

		if ( ! \is_array( $conditions ) ) {
			return;
		}

		foreach ( $conditions as $condition => $check ) {
			if ( $this->result ) {
				return; // once the logic has returned true there is no need to check anymore.
			}
			if ( 'is_global' === $condition ) {
				$this->result = true;
			} elseif ( $this->is_condition_allowed( $condition ) && \is_callable( $condition ) ) {
				if ( $check ) {
					$this->result = (bool) \call_user_func( $condition, $this->string_to_array( $check ) );
				} else {
					$this->result = (bool) \call_user_func( $condition );
				}
			}
		}
	}

	/**
	 * Get conditional tags.
	 */
	private function get_conditional_tags(): array {
		$tags = [
			// User
			'is_user_logged_in',
			'is_super_admin',

			// Core
			'is_main_site',

			// Loop
			'is_main_query',
			'is_paged',
			'not_paged',
			'in_the_loop',

			// Posts
			'is_page',
			'is_page_template',
			'is_singular',
			'is_single',
			'is_attachment',
			'is_sticky',
			'has_term',
			'has_tag',
			'has_post_thumbnail',

			// archives
			'is_tax',
			'is_search',
			'is_tag',
			'is_category',
			'is_archive',
			'is_post_type_archive',
			'is_author',
			'is_date',
			'is_year',
			'is_month',
			'is_day',
			'is_time',
			'is_new_day',
			'is_404',

			// Homepage
			'is_home',
			'is_front_page',

			// WooCommerce
			'is_shop',
			'is_product_category',
			'is_product_tag',
			'is_woocommerce',
			'is_wc_endpoint_url',
			'wpex_is_woo_shop',
			'wpex_is_woo_tax',
			'wpex_is_woo_single',

			// Tribe Events
			'tribe_is_event',
			'tribe_is_view',
			'tec_is_view',
			'tribe_is_list_view',
			'tribe_is_event_category',
			'tribe_is_in_main_loop',
			'tribe_is_day',
			'tribe_is_month',
		];

		/**
		 * Filters the list of conditional tags allowed.
		 *
		 * @param array $conditionals
		 */
		$tags = (array) \apply_filters( 'totaltheme/conditional_logic/conditional_tags', $tags );

		/*** deprecated ***/
		$tags = apply_filters( 'wpex_conditional_logic_callable_whitelist', $tags );

		return (array) $tags;
	}

	/**
	 * Check if condition is allowed (whitelisted).
	 */
	private function is_condition_allowed( $function = '' ): bool {
		$whitelist = (array) $this->get_conditional_tags();
		return ( $whitelist && \in_array( $function, $whitelist, true ) );
	}

	/**
	 * Converts strings to arrays.
	 */
	private function string_to_array( $input ): array {
		if ( \is_string( $input ) ) {
			$input = \explode( ',', \sanitize_text_field( $input ) );
		}
		return (array) $input;
	}

}
