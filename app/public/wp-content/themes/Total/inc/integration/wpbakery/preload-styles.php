<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

/**
 * Preload WPBakery styles to render elements nicely.
 */
class Preload_Styles {

	/**
	 * Array of styles to enqueue earily.
	 */
	protected $queue = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( \is_admin() || ! \function_exists( 'vc_asset_url' ) || ! $this->shortcodes_to_check() ) {
			return;
		}
		\add_action( 'get_header', [ $this, 'generate_queue' ] );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 100 );
	}

	/**
	 * Generate styles queueue.
	 */
	public function generate_queue() {
		if ( \is_singular() ) {
			$this->single_check();
		}
	}

	/**
	 * Hooks into wp_head to enqueue_css early to prevent FOUC.
	 */
	public function enqueue_styles() {
		if ( ! $this->queue || ! \function_exists( '\vc_asset_url' ) ) {
			return;
		}
		foreach ( $this->queue as $style => $filename ) {
			\wp_enqueue_style(
				$style,
				\vc_asset_url( "css/{$filename}.min.css" ),
				[],
				WPB_VC_VERSION
			);
		}
	}

	/**
	 * Singular checks.
	 */
	protected function single_check() {
		if ( $dynamic_template = totaltheme_call_static( 'Theme_Builder\Post_Template', 'get_template_content' ) ) {
			$this->maybe_enqueue_styles( $dynamic_template ); // @Todo can this be optimized?
		} else {
			$this->maybe_enqueue_styles( get_post_field( 'post_content', get_the_ID() ) );
		}
	}

	/**
	 * Maybe enqueue styles.
	 */
	protected function maybe_enqueue_styles( $content = '' ) {
		$shortcodes_to_check = $this->shortcodes_to_check();
		if ( ! $shortcodes_to_check ) {
			return;
		}
		foreach ( $shortcodes_to_check as $shortcode => $styles ) {
			if ( $this->content_has_shortcode( $content, $shortcode ) ) {
				foreach ( $styles as $style => $filename ) {
					if ( ! \array_key_exists( $style, $this->queue ) ) {
						$this->queue[ $style ] = $filename;
					}
				}
			}
		}
	}

	/**
	 * Arrays of shortcodes to check and enqueue CSS for.
	 */
	protected function shortcodes_to_check() {
		$shortcodes = [
			'vc_tta_tabs' => [
				'vc_tta_style' => 'js_composer_tta',
			],
			'vc_tta_accordion' => [
				'vc_tta_style' => 'js_composer_tta',
			],
		];
		return (array) \apply_filters( 'wpex_vc_early_css_shortcodes_list', $shortcodes );
	}

	/**
	 * Checks if a the content has a specific shortcode.
	 */
	protected function content_has_shortcode( $content = '', $shortcode = '' ) {
		if ( $content && \str_contains( $content, $shortcode ) ) {
			return true;
		}
	}

}
