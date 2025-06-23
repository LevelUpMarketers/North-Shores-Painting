<?php
namespace TotalThemeCore\Vcex;

defined( 'ABSPATH' ) || exit;

/**
 * Class used for user callback functions.
 */
final class User_Callback_Functions {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Scripts.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Returns whitelisted functions.
	 */
	public function get_whitelist() {
		$whitelist = [
			// Core functions
			'get_the_title',
			'get_the_excerpt',
			'get_the_ID',
			'get_permalink',
			'get_post_thumbnail_id',
			'wp_get_attachment_image',
			// Theme functions
			'wpex_title',
		];
		if ( \defined( 'VCEX_CALLBACK_FUNCTION_WHITELIST' ) && is_array( \VCEX_CALLBACK_FUNCTION_WHITELIST ) ) {
			$whitelist = \array_merge( $whitelist, \VCEX_CALLBACK_FUNCTION_WHITELIST );
		}
		return $whitelist;
	}

	/**
	 * Check if a function is whitelisted.
	 */
	public function is_whitelisted( $function_name = '' ) {
		return \in_array( $function_name, $this->get_whitelist(), true );
	}

}
