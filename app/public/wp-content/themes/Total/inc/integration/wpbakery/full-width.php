<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

/**
 * CSS Based Full Width Elements for WPBakery.
 */
final class Full_Width {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Shortcodes to add overlay settings to.
	 */
	private $shortcodes = [];

	/**
	 * Checks if the functionality is hard disabled.
	 */
	private static $is_disabled = false;

	/**
	 * Create or retrieve the class instance.
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
		if ( ! \wp_validate_boolean( \get_theme_mod( 'wpb_full_width_css_enable', true ) ) || ! wpex_is_request( 'frontend' ) ) {
			return;
		}

		\add_filter( 'shortcode_atts_vc_section',  [ $this, 'parse_attributes' ], PHP_INT_MAX );
		\add_filter( 'shortcode_atts_vc_row',  [ $this, 'parse_attributes' ], PHP_INT_MAX );
		\add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ $this, 'modify_shortcode_classes' ], 10, 3 );
		\add_action( 'wpex_hook_content_top', [ $this, 'wpex_hook_content_top' ] );
		\add_action( 'wpex_hook_content_bottom', [ $this, 'wpex_hook_content_bottom' ] );
	}

	/**
	 * Hooks into wpex_hook_content_top.
	 */
	public function wpex_hook_content_top(): void {
		if ( \wpex_has_sidebar() ) {
			self::$is_disabled = true;
		}
	}

	/**
	 * Hooks into wpex_hook_content_bottom.
	 */
	public function wpex_hook_content_bottom(): void {
		self::$is_disabled = false;
	}

	/**
	 * Check if this functionality is enabled.
	 */
	private function is_enabled(): bool {
		if ( self::$is_disabled ) {
			return false;
		}

		// Only used for the non-boxed layout by default.
		$check = 'boxed' !== \wpex_site_layout();

		/*** deprecated ***/
		$check = \apply_filters( 'wpex_wpb_full_width_css', $check );

		return (bool) \apply_filters( 'totaltheme/integration/wpbakery/full_width/is_enabled', $check );
	}

	/**
	 * Parse VC section attributes on front-end.
	 */
	public function parse_attributes( $atts ) {
		if ( $this->shortcode_is_full_width( $atts ) && $this->is_enabled() && $this->shortcode_supports_css_full_width( $atts ) ) {
			$atts['wpex_full_width'] = $this->get_full_width_style( $atts );
			$atts['full_width'] = '';
		}
		return $atts;
	}

	/**
	 * Parse VC section attributes on front-end.
	 */
	public function modify_shortcode_classes( $class_string, $tag, $atts ) {
		if ( ! \in_array( $tag, [ 'vc_section', 'vc_row' ], true ) || empty( $atts['wpex_full_width'] ) || ! $this->is_enabled() ) {
			return $class_string;
		}
		$styles_map = [
			'stretch_row' => 'centered',
			'stretch_row_content_no_spaces' => 'no-padding',
		];
		$style = $styles_map[ $atts['wpex_full_width'] ] ?? '';
		$tag_parsed = \str_replace( 'vc_', '', $tag );
		$class_string .= " wpex-vc-full-width-{$tag_parsed}";
		if ( $style  ) {
			$class_string .= " wpex-vc-full-width-{$tag_parsed}--{$style}";
		}
		return $class_string;
	}

	/**
	 * Returns full-width style.
	 */
	private function get_full_width_style( $atts ) {
		return $atts['full_width'] ?? null;
	}

	/**
	 * Check if the element is set to full width.
	 */
	private function shortcode_is_full_width( $atts ): bool {
		return (bool) $this->get_full_width_style( $atts );
	}

	/**
	 * Make sure the row can be set to stretch with pure CSS.
	 */
	private function shortcode_supports_css_full_width( $atts ): bool {
		$check = true;
		if ( ! empty( $atts['css'] )
			&& \preg_match( '/margin-left|margin-right|padding-left|padding-right/i', $atts['css'] )
		) {
			$check = false;
		}
		return (bool) \apply_filters( 'wpex_wpb_shortcode_supports_css_full_width', $check, $atts );
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
