<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Restrict Content Class.
 */
class Restrict_Content {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Singleton.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Returns all restrictions.
	 */
	protected function get_restrictions(): array {
		$restrictions = [
			// User
			'is_user_logged_in',
			'is_super_admin',
			'logged_in',
			'logged_out',

			// Core
			'is_main_site',

			// Loop
			'is_main_query',
			'is_paged',
			'in_the_loop',
			'not_paged',

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

		if ( $custom_restrictions = $this->get_custom_restrictions( false ) ) {
			$restrictions = \array_merge( $restrictions, $custom_restrictions );
		}

		return $this->restrictions_apply_filters( $restrictions );
	}

	/**
	 * Returns user restrictions only.
	 */
	public function get_custom_restrictions( bool $apply_filters = true ): array {
		$restrictions = [];
		if ( \defined( 'VCEX_CALLBACK_FUNCTION_WHITELIST' ) && is_array( \VCEX_CALLBACK_FUNCTION_WHITELIST ) ) {
			$restrictions = \VCEX_CALLBACK_FUNCTION_WHITELIST;
		}

		return $this->restrictions_apply_filters( $restrictions );
	}

	/**
	 * Apply filters to the restrictions array.
	 */
	protected function restrictions_apply_filters( array $restrictions ): array {
		$restrictions = (array) \apply_filters( 'totaltheme/restrict_content/restrictions', $restrictions );

		/*** deprecated ***/
		$restrictions = (array) \apply_filters( 'wpex_user_access_callable_whitelist', $restrictions );

		return $restrictions;
	}

	/**
	 * Checks is a restriction is valid.
	 */
	protected function is_restriction_valid( $restriction = '' ): bool {
		return in_array( $restriction, $this->get_restrictions() );
	}

	/**
	 * Check restriction.
	 */
	public function check_restriction( $restriction = '' ): bool {
		if ( ! \is_string( $restriction ) || ! $this->is_restriction_valid( $restriction ) ) {
			return true; /*** !!! important security check !!! ***/
		}

		$callback = $this->get_restriction_callback( $restriction );

		if ( ! \is_callable( $callback ) ) {
			return true;
		}

		return (bool) \call_user_func( $callback );
	}

	/**
	 * Returns restriction callback.
	 */
	protected function get_restriction_callback( $restriction = '' ) {
		$callback = $restriction;
		switch ( $restriction ) {
			case 'logged_in':
				$callback = 'is_user_logged_in';
				break;
			case 'logged_out':
				$callback = [ $this, 'is_user_logged_out' ];
				break;
			case 'not_paged':
				$callback = [ $this, 'not_paged' ];
				break;
		}
		return $callback;
	}

	/**
	 * Check if user is logged out.
	 */
	protected function is_user_logged_out(): bool {
		return ! \is_user_logged_in();
	}

	/**
	 * Check if not paged.
	 */
	protected function not_paged(): bool {
		return ! \is_paged();
	}

}
