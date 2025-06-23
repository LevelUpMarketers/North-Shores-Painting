<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

final class Shortcode_Inline_Style {

	/**
	 * Instance.
	 */
	protected $parsed_ids = [];

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Shortcode_Inline_Style.
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
	private function __construct() {}

	/**
	 * Get style.
	 */
	public function get_style( $post_id, $add_to_parsed = false ) {
		if ( ! \WPEX_VC_ACTIVE ) {
			return '';
		}

		if ( \is_array( $post_id ) ) {
			foreach ( $post_id as $id ) {
				return self::get_style( $id, $add_to_parsed );
			}
		}

		if ( ! $post_id || ! \is_numeric( $post_id ) || \in_array( $post_id, $this->parsed_ids ) ) {
			return '';
		}

		$css = \get_post_meta( $post_id, '_wpb_shortcodes_custom_css', true );
		$css = (string) \apply_filters( 'vc_shortcodes_custom_css', $css, $post_id );

		if ( $css ) {
			if ( $add_to_parsed ) {
				$this->parsed_ids[] = $post_id;
			}
			return '<style>' . \wp_strip_all_tags( $css ) . '</style>';
		}
	}

	/**
	 * Render style.
	 */
	public function render_style( $post_id, $add_to_parsed = false ) {
		echo static::$instance->get_style( $post_id, $add_to_parsed );
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
