<?php

namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * JetPack Integration.
 */
final class Jetpack {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Jetpack.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		$this->sharedaddy_support();
		$this->carousel_support();
	}

	/**
	 * Adds support for sharedaddy.
	 */
	public function sharedaddy_support() {
		if ( ! \Jetpack::is_module_active( 'sharedaddy' ) ) {
			return;
		}
		if ( wpex_is_request( 'frontend' ) ) {
			add_action( 'loop_start', array( $this, 'remove_share' ) );
			add_filter( 'sharing_show', '__return_true' );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
			add_filter( 'wpex_custom_social_share', array( $this, 'alter_share' ) );
		}
		add_filter( 'wpex_customizer_sections', array( $this, 'remove_customizer_settings' ), 40 );
	}

	/**
	 * Adds support for carousels.
	 */
	public function carousel_support() {
		if ( \Jetpack::is_module_active( 'carousel' ) || \Jetpack::is_module_active( 'tiled-gallery' ) ) {
			add_filter( 'wpex_custom_wp_gallery', '__return_false' );
		}
	}

	/**
	 * Removes jetpack default loop filters.
	 */
	public function remove_share() {
		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_excerpt', 'sharing_display', 19 );
	}

	/**
	 * Enqueue scripts if social share is enabled.
	 */
	public function load_scripts() {
		if ( wpex_has_social_share() ) {
			add_filter( 'sharing_enqueue_scripts', '__return_true' );
		}
	}

	/**
	 * Replace Total social share with sharedaddy.
	 */
	public function alter_share() {
		if ( function_exists( 'sharing_display' ) ) {
			return sharing_display( '', false ); // text, echo
		}
	}

	/**
	 * Remove Customizer settings.
	 */
	public function remove_customizer_settings( $array ) {
		unset( $array['wpex_social_sharing'] );
		return $array;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
