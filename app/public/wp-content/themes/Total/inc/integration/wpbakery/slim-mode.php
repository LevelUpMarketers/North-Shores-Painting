<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

class Slim_Mode {

	/**
	 * Used to prevent extra lookups when using is_enabled().
	 */
	protected static $is_enabled;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Check if this functionality is enabled.
	 *
	 * Note: This method is static so we can call it without initializing our class.
	 */
	public static function is_enabled(): bool {
		if ( \is_null( self::$is_enabled ) ) {
			self::$is_enabled = (bool) \apply_filters( 'totaltheme/integration/wpbakery/slim_mode/is_enabled', \get_theme_mod( 'wpb_slim_mode_enable', false ) );
		}
		return self::$is_enabled;
	}

	/**
	 * Init.
	 *
	 * Note: We only switch the CSS on the front-end because for some reason
	 * WPBakery adds CSS for the editor in the js_composer_front css file.
	 */
	public static function init(): void {
		if ( ! \totaltheme_is_wpb_frontend_editor() ) {
			\add_action( 'wp_enqueue_scripts', [ self::class, '_enqueue_css' ], 0 ); // load before parent css
			\add_action( 'wp_enqueue_scripts', [ self::class, 'remove_vc_css' ], 40 );
			\add_action( 'wp_footer', [ self::class, 'remove_vc_css' ] ); // vc loads their CSS for every shortcode (uff).
			\add_action( 'vc_load_iframe_jscss', [ self::class, 'remove_vc_css' ] );
		}

		\add_filter( 'vc_after_init', [ self::class, 'deprecate_elements' ] );
		\add_filter( 'totaltheme/integration/wpbakery/remove_elements/blacklist', [ self::class, 'remove_vc_elements' ] );
		\add_filter( 'vcex_shortcodes_list', [ self::class, 'remove_vcex_elements' ] );

		self::remove_grid_builder();

		// Remove element map hooks.
		\remove_action( 'init', 'vc_gutenberg_map' );
	}

	/**
	 * Loads wpbakery slim mode CSS.
	 */
	public static function _enqueue_css(): void {
		\wp_enqueue_style(
			'wpex-wpbakery-slim',
			\totaltheme_get_css_file( 'frontend/wpbakery-slim' ),
			[],
			\WPEX_THEME_VERSION
		);
	}

	/**
	 * Remove the the vc CSS.
	 */
	public static function remove_vc_css(): void {
		\wp_dequeue_style( 'js_composer_front' );
		\wp_dequeue_style( 'js_composer_custom_css' );
	}

	/**
	 * Remove elements.
	 */
	public static function remove_vc_elements( array $list ): array {
		$new_items = [
			'vc_gutenberg',
			'vc_pie',
			'vc_empty_space',
			'vc_hoverbox',
			'vc_pinterest',
			'vc_tweetmeme',
			'vc_facebook',
			'vc_btn',
			'vc_flickr',
			'vc_progress_bar',
			'vc_cta',
			'vc_basic_grid',
			'vc_media_grid',
			'vc_masonry_grid',
			'vc_masonry_media_grid',
			'vc_separator',
			'vc_single_image',
			'vc_custom_heading',
			'vc_icon',
			'vc_wp_search',
			'vc_wp_recentcomments',
			'vc_wp_calendar',
			'vc_wp_tagcloud',
			'vc_wp_custommenu',
			'vc_wp_posts',
			'vc_wp_categories',
			'vc_wp_archives',
			'vc_wp_rss',
			'vc_toggle',
			'vc_tabs',
			'vc_tour',
			'vc_accordion',
			'vc_text_separator',
			'vc_message',
			'vc_zigzag',
			'vc_acf',
			'vc_pricing_table',
			'vc_tta_toggle',
			'vc_tta_pageable', // disabled because it requires a massive amount of CSS.

			// @note re-enabled in 5.10 - these don't require added CSS and are useful.
			//	'vc_round_chart',
			//	'vc_line_chart',
		];

		if ( ! apply_filters( 'totaltheme/integration/wpbakery/slim_mode/deprecate_elements', true ) ) {
			$new_items = array_merge( $new_items, self::deprecated_elements_list() );
		}

		if ( \totaltheme_version_check( 'initial', '6.2', '>=' ) ) {
			$new_items[] = 'vc_gmaps'; // WPB released a new version of the Google Maps element in 8.3, included in Total 6.2
		}

		return array_merge( $list, $new_items );
	}

	/**
	 * Remove elements.
	 */
	public static function remove_vcex_elements( array $elements ): array {
		$elements_to_remove = [
			'post_type_grid',
			'blog_grid',
			'blog_carousel',
			'portfolio_carousel',
			'portfolio_grid',
			'post_type_carousel',
			'post_type_slider',
			'post_type_archive',
			'staff_carousel',
			'staff_grid',
			'testimonials_carousel',
			'testimonials_grid',
		//	'testimonials_slider',
			'woocommerce_carousel',
			'woocommerce_loop_carousel',
			'image_galleryslider',
			'form_shortcode',
			'grid_item-post_excerpt',
			'grid_item-post_meta',
			'grid_item-post_terms',
			'grid_item-post_video',
		];

		return array_diff( $elements, $elements_to_remove );
	}

	/**
	 * Remove the the vc grid builder.
	 */
	public static function remove_grid_builder(): void {
		\remove_action( 'init', 'vc_grid_item_editor_create_post_type' );
		\remove_action( 'vc_after_init', 'vc_grid_item_editor_shortcodes' );
		\remove_action( 'wp_ajax_vc_gitem_preview', 'vc_grid_item_render_preview', 5 );
		if ( \is_admin() ) {
			\remove_action( 'admin_init', 'vc_grid_item_editor_init' );
			\remove_action( 'vc_ui-pointers-vc_grid_item', 'vc_grid_item_register_pointer' );
			\remove_action( 'admin_head', 'vc_gitem_menu_highlight' );
			\remove_action( 'wp_ajax_vc_edit_form', 'vc_gitem_set_mapper_check_access' );
		}
	}

	/**
	 * Deprecate elements.
	 */
	public static function deprecate_elements() {
		foreach ( self::deprecated_elements_list() as $element ) {
			vc_map_update( $element, [ 'deprecated' => true ] );
		}
	}

	/**
	 * Returns array of elements that will be "deprecated" rather then removed.
	 */
	protected static function deprecated_elements_list(): array {
		return [
			'vc_video',
		];
	}

}
