<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

class Theme_Mode {

	/**
	 * Hidden modules list.
	 */
	private static $hidden_modules_list = [
		'ai',
		'typography',
		'color-picker',
	];

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( \function_exists( '\vc_set_as_theme' ) ) {
			\vc_set_as_theme();
		}

		\add_action( 'wp_enqueue_scripts', [ self::class, '_on_wp_enqueue_scripts' ] );
		\add_filter( 'vc_get_all_templates', [ self::class, '_filter_vc_get_all_templates' ], 99 );

		if ( \is_admin() ) {
			self::admin_hooks();
		}

		if ( ! self::design_options_enabled() && \get_option( 'wpb_js_use_custom' ) ) {
			\delete_option( 'wpb_js_use_custom' );
		}
	}

	/**
	 * Hooks into wp_enqueue_scripts.
	 */
	public static function _on_wp_enqueue_scripts() {
		if ( ! self::design_options_enabled() ) {
			\wp_deregister_style( 'js_composer_custom_css' );
		}
	}

	/**
	 * Admin hooks.
	 */
	public static function admin_hooks(): void {
		// Hooks intp admin_enqueue_scripts
		\add_action( 'admin_enqueue_scripts', [ self::class, '_on_admin_enqueue_scripts' ] );

		// Remove welcome about page
		\remove_action( 'vc_activation_hook', 'vc_page_welcome_set_redirect' );
		\remove_action( 'init', 'vc_page_welcome_redirect' );
		\remove_action( 'admin_init', 'vc_page_welcome_redirect' );
		\add_action( 'admin_menu', [ self::class, '_on_admin_menu' ], 1000 );

		// Remove settings page tabs
		\add_filter( 'vc_settings_tabs', [ self::class, '_filter_vc_settings_tabs' ], 1000 );

		// Design options
		if ( self::design_options_enabled() ) {
			\add_filter( 'vc_settings_page_show_design_tabs', '__return_true' );
		} else {
			\add_filter( 'vc_settings_page_show_design_tabs', '__return_false' );
			self::$hidden_modules_list[] = 'design-options';
		}
	}

	/**
	 * Runs on the "admin_enqueue_scripts" hook.
	 */
	public static function _on_admin_enqueue_scripts( $hook_suffix ): void {
		// Disable modules
		if ( 'wpbakery-page-builder_page_vc-modules' === $hook_suffix ) {
			\wp_enqueue_script(
				'totaltheme-admin-wpbakery-module-manager',
				\totaltheme_get_js_file( 'admin/wpbakery/module-manager' ),
				[],
				\WPEX_THEME_VERSION,
				true
			);
			\wp_localize_script(
				'totaltheme-admin-wpbakery-module-manager',
				'totaltheme_admin_wpbakery_module_manager_vars',
				[
					'hide_modules' => self::$hidden_modules_list,
				]
			);
		}
	}

	/**
	 * Remove vc templates.
	 */
	public static function _filter_vc_get_all_templates( $data ) {
		if ( $data && \is_array( $data ) ) {
			foreach ( $data as $key => $val ) {
				if ( isset( $val['category'] ) && 'shared_templates' === $val['category'] ) {
					unset( $data[ $key ] );
				}
			}
		}
		return $data;
	}

	/**
	 * Hooks into the admin_menu action.
	 */
	public static function _on_admin_menu() {
		if ( \defined( 'VC_PAGE_MAIN_SLUG' ) && 'vc-welcome' === \VC_PAGE_MAIN_SLUG ) {
			//remove_menu_page( 'vc-welcome' ); // hides the grid and template pages, can't use.
		} else {
			\remove_submenu_page( 'vc-general', 'vc-welcome' );
			\remove_submenu_page( 'admin', 'vc-welcome' );
		}
	}

	/**
	 * Remove settings tab.
	 */
	public static function _filter_vc_settings_tabs( $tabs ) {
		if ( \is_array( $tabs ) ) {
			unset( $tabs['vc-updater'] );
			unset( $tabs['vc-ai'] );
			unset( $tabs['vc-typography'] );
		}
		return $tabs;
	}

	/**
	 * Check if design options are enabled.
	 */
	private static function design_options_enabled(): bool {
		return \get_theme_mod( 'wpbakery_design_options_enable', false )
			&& ! \totaltheme_call_static( __NAMESPACE__ . '\Slim_Mode', 'is_enabled' );
	}

}
