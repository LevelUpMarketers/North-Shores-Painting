<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

class Optimize_JS {

	/**
	 * Checks if we should enqueue the wpb_composer_front_js script.
	 */
	private $enqueue_wpb_composer_front_js = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( \is_admin()
			|| ! \defined( 'WPB_VC_VERSION' )
			|| \version_compare( \WPB_VC_VERSION, '6.9.0', '<' )
			|| \totaltheme_is_wpb_frontend_editor()
		) {
			return;
		}

		// Hook into shortcodes to see if the current shortcode requires the wpb_composer_front_js script.
		\add_filter( 'vc_shortcode_content_filter_after', [ $this, 'vc_shortcode_content_filter_after' ], 10, 4 );

		// Hooks into the footer to check if we should dequeue unnedded scripts.
		\add_action( 'wp_footer', [ $this, 'maybe_dequeue_script' ], 11 );
	}

	/**
	 * Hooks into vc_shortcode_content_filter_after to check if the curent shortcode needs js_composer.
	 */
	public function vc_shortcode_content_filter_after( $output, $shortcode, $atts, $content ) {
		if ( $this->enqueue_wpb_composer_front_js ) {
			return $output; // no need to make additional checks since we know the script needs to load.
		}
		if ( \in_array( $shortcode, $this->required_shortcodes() ) ) {
			$this->enqueue_wpb_composer_front_js = true;
		} elseif ( in_array( $shortcode, $this->shortcodes_to_check(), true ) ) {
			if ( ! empty( $atts['full_width'] )
				|| ! empty( $atts['parallax'] )
				|| ( ! empty( $atts['video_bg'] ) && 'self_hosted' !== $atts['video_bg'] )
				|| ( ! empty( $atts['full_height'] ) && \wpex_validate_boolean( $atts['full_height'] ) )
			) {
				$this->enqueue_wpb_composer_front_js = true;
			}
		}
		return $output;
	}

	/**
	 * Shortcodes that require the script always.
	 */
	public function required_shortcodes() {
		return [
			'vc_tour',
			'vc_tab',
			'vc_accordion',
			'vc_tta_tabs',
			'vc_toggle',
			'vc_progress_bar',
			'vc_basic_grid',
			'vc_masonry_grid',
			'vc_masonry_media_grid',
			'vc_hoverbox',
			'vc_pinterest',
			//'vc_gmaps', // @note there is a vc_googleMapsPointer function but all it does is alter the pointer events, seems useless.

			// Elements not enabled in the theme by default.
			'vc_carousel',
			'vc_images_carousel',
			'vc_teaser_grid',
			'vc_posts_grid',
			'vc_posts_slider',
			'vc_gallery',
		];
	}

	/**
	 * Shortcodes to check.
	 */
	public function shortcodes_to_check() {
		return [
			'vc_section',
			'vc_row',
			'vc_column',
		];
	}

	/**
	 * Hooks into wp_footer to dequeue the wpb_composer_front_js if it's not actually needed.
	 */
	public function maybe_dequeue_script() {

		// Fallback checks, these scripts require wpb_composer_front_js
		if ( ! $this->enqueue_wpb_composer_front_js ) {
			$scripts = [
				'vc_pageable_owl',
				'vc_masonry',
				'vc_grid',
				'vc_youtube_iframe_api_js',
				'vc_jquery_skrollr_js',
				'vc_accordion_script',
				'vc_tta_autoplay_script',
				'lightbox2', // seems to work without though...@todo revise
			];
			foreach ( $scripts as $script ) {
				if ( \wp_script_is( $script ) ) {
					return;
				}
			}
		}

		// Waypoints JS.
		if ( \wp_script_is( 'vc_waypoints' ) && ! \wp_script_is( 'wpex-vc_waypoints' ) ) {
			return;
		}

		// Dequeue script.
		if ( false === $this->enqueue_wpb_composer_front_js ) {
			\wp_dequeue_script( 'wpb_composer_front_js' );
		}
	}

}
