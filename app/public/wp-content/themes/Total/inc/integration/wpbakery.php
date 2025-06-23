<?php

namespace TotalTheme\Integration;

use TotalTheme\Integration\WPBakery\Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Integration.
 */
final class WPBakery {

	/**
	 * Init.
	 */
	public static function init() {
		if ( \class_exists( __CLASS__ . '\Helpers' ) ) {
			self::init_classes();
			self::init_hooks();
		}
	}

	/**
	 * Returns array of WPBakery theme features.
	 */
	private static function get_features(): array {
		$features = [
			'patterns'             => 'Patterns',
			'background_overlays'  => 'BG_Overlays',
			'video_backgrounds'    => 'Video_Backgrounds',
			'slider_backgrounds'   => 'Slider_Backgrounds',
			'parallax_backgrounds' => 'Advanced_Parallax',
			'shape_dividers'       => 'Shape_Dividers',
		];
		$parallax_support = (bool) \apply_filters_deprecated(
			'vcex_supports_advanced_parallax',
			[ true ],
			'6.1',
			'totaltheme/integration/wpbakery/features'
		);
		if ( ! $parallax_support ) {
			unset( $features['parallax_backgrounds'] );
		}
		$shape_dividers_support = (bool) \apply_filters_deprecated(
			'vcex_supports_shape_dividers',
			[ true ],
			'6.1',
			'totaltheme/integration/wpbakery/features'
		);
		if ( ! $shape_dividers_support ) {
			unset( $features['shape_dividers'] );
		}
		return (array) apply_filters( 'totaltheme/integration/wpbakery/features', $features );
	}

	/**
	 * Initiate classes.
	 */
	private static function init_classes(): void {
		if ( \totaltheme_call_static( __CLASS__ . '\Slim_Mode', 'is_enabled' ) ) {
			\totaltheme_init_class( __CLASS__ . '\Slim_Mode' );
		} else {
			\totaltheme_init_class( __CLASS__ . '\Elements\Vc_Tabs' );
			\totaltheme_init_class( __CLASS__ . '\Elements\Single_Image' );
			\totaltheme_init_class( __CLASS__ . '\Font_Container' );
		}

		//\totaltheme_init_class( __CLASS__ . '\Modify_Colorpicker' );
		\totaltheme_init_class( __CLASS__ . '\Remove_Elements' );
		\totaltheme_init_class( __CLASS__ . '\Inline_CSS' );
		\totaltheme_init_class( __CLASS__ . '\Full_Width' );
		\totaltheme_init_class( __CLASS__ . '\Shortcode_Theme_Styles' );
		\totaltheme_init_class( __CLASS__ . '\Layout_Templates' );

		if ( wp_validate_boolean( \get_theme_mod( 'wpb_optimize_js_enable', true ) ) ) {
			\totaltheme_init_class( __CLASS__ . '\Optimize_JS' );
		}

		\totaltheme_init_class( __CLASS__ . '\Preload_Styles' );

		\totaltheme_init_class( __CLASS__ . '\Elements\Section') ;
		\totaltheme_init_class( __CLASS__ . '\Elements\Row' );
		\totaltheme_init_class( __CLASS__ . '\Elements\Column' );
		\totaltheme_init_class( __CLASS__ . '\Elements\Text_Block' );

		foreach ( self::get_features() as $class ) {
			\totaltheme_init_class( __CLASS__ . '\\' . $class );
		}

		if ( Helpers::is_theme_mode_enabled() ) {
			\totaltheme_init_class( __CLASS__ . '\Theme_Mode' );
			\totaltheme_init_class( __CLASS__ . '\Disable_Updates' );
		}

		if ( ! \wp_validate_boolean( \get_theme_mod( 'wpb_param_desc_enabled', true ) ) ) {
			\totaltheme_init_class( __CLASS__ . '\Remove_Param_Descriptions' );
		}
	}

	/**
	 * Hook into actions and filters.
	 */
	private static function init_hooks(): void {
		
		\add_action( 'init', [ self::class, 'init_hook' ], 20 );

		// Admin & Editor only hooks
		if ( is_admin() || totaltheme_is_wpb_frontend_editor() ) {
			// Remove the promo notices
			\wpex_remove_class_filter( 'admin_init', 'Vc_Notice_Controller', 'init', 10 );

			// Remove promo transient that isn't even used anywhere in the plugin; also fixes a debug error when updating
			\remove_action( 'upgrader_process_complete', 'vc_set_promo_editor_popup', 10, 2 );
			\add_action( 'admin_init', [ self::class, 'admin_init_hook' ], 20 );

			// Disable default templates
			\add_filter( 'vc_load_default_templates', '__return_empty_array' );

			// Editor Scripts
			\add_action( 'wpb_after_register_frontend_editor_js', [ self::class, '_register_editor_scripts' ] );
			\add_action( 'wpb_after_register_backend_editor_js', [ self::class, '_register_editor_scripts' ] );
			\add_action( 'vc_base_register_admin_css', [ self::class, 'register_admin_css' ] );
			\add_action( 'vc_backend_editor_enqueue_js_css', [ self::class, 'enqueue_be_editor_scripts' ] );
			\add_action( 'vc_frontend_editor_enqueue_js_css', [ self::class, 'enqueue_fe_editor_scripts' ] );

			// Remove WPbakery vc fonts
			\add_action( 'wpb_enqueue_backend_editor_css', [ self::class, '_filter_editor_css_dependencies' ] );
			\add_action( 'wpb_enqueue_frontend_editor_css', [ self::class, '_filter_editor_css_dependencies' ] );

			// Add new background styles - @todo is this still needed?
			\add_filter( 'vc_css_editor_background_style_options_data', [ self::class, 'background_styles' ] );

			// Editor scripts
			\add_action( 'vc_load_iframe_jscss', [ self::class, 'iframe_scripts' ] );
		}

		// Disable the WPBakery editor for certain post types (run on frontend to prevent edit button)
		\add_filter( 'vc_check_post_type_validation', [ self::class, 'disable_editor' ], 10, 2 );

		// Load js_composer early - if slim mode is disabled
		if ( ! \totaltheme_call_static( __CLASS__ . '\Slim_Mode', 'is_enabled' ) ) {
			\add_action( 'wp_enqueue_scripts', [ self::class, 'load_composer_front_css' ], 0 );
			
		}

		// Enqueue wpex-wpbakery CSS - when slim mode is not loaded as the code is added in there
		if ( ! \totaltheme_call_static( __CLASS__ . '\Slim_Mode', 'is_enabled' ) || \totaltheme_is_wpb_frontend_editor() ) {
			\add_action( 'wp_enqueue_scripts', [ self::class, '_enqueue_css' ], 11 );
		}

		// Modify core scripts
		\add_action( 'wp_enqueue_scripts', [ self::class, 'register_scripts' ] );
		\add_action( 'wp_footer', [ self::class, 'enqueue_dependent_scripts' ] );

		// Add customizer settings.
		\add_filter( 'wpex_customizer_panels', [ self::class, 'customizer_settings' ] );

		// Insert noscript tags - @todo remove CSS added to VC js stretched rows and remove this.
		if ( true === \apply_filters( 'wpex_noscript_tags', true ) ) {
			\add_action( 'wp_head', [ self::class, 'noscript' ], 60 );
		}

		// Fixes issues with multisite wp-activate.php template not displaying shortcodes.
		// @todo deprecate when no longer needed.
		if ( \is_multisite()
			&& ! empty( $GLOBALS['pagenow'] )
			&& \in_array( $GLOBALS['pagenow'], [ 'wp-activate.php', 'wp-signup.php' ] )
			&& \function_exists( 'wpbakery' )
		) {
			\add_action( 'activate_header', [ self::class, 'init_wpbakery' ] );
			\add_action( 'before_signup_header', [ self::class, 'init_wpbakery' ] );
		}

	}

	/**
	 * Functions that run on init.
	 */
	public static function init_hook(): void {
		if ( \function_exists( 'visual_composer' ) ) {
			\remove_action( 'wp_head', [ \visual_composer(), 'addMetaData' ] );
		}

		if ( \function_exists( 'vc_set_default_editor_post_types' ) ) {
			\vc_set_default_editor_post_types( [ 'page', 'portfolio', 'staff', 'wpex_templates', 'wpex_card' ] );
		}
	}

	/**
	 * Functions that run on admin_init.
	 */
	public static function admin_init_hook(): void {
		// Remove editor logo
		\add_filter( 'vc_nav_front_logo', [ self::class, 'editor_nav_logo' ] );
		
		// Remove purchase notice
		\wpex_remove_class_filter( 'admin_notices', 'Vc_License', 'adminNoticeLicenseActivation', 10 );
	}

	/**
	 * Override editor logo.
	 */
	public static function editor_nav_logo(): string {
		return '<div id="vc_logo" class="vc_navbar-brand" aria-hidden="true"></div>';
	}

	/**
	 * Loads wpbakery css (non slim mode).
	 */
	public static function _enqueue_css(): void {
		$deps = [ \WPEX_THEME_STYLE_HANDLE ];
		if ( \wp_style_is( 'js_composer_front', 'registered' ) ) {
			$deps[] = 'js_composer_front';
		}
		\wp_enqueue_style(
			'wpex-wpbakery',
			\totaltheme_get_css_file( 'frontend/wpbakery' ),
			$deps,
			\WPEX_THEME_VERSION
		);
	}

	/**
	 * Load js_composer_front CSS early on for easier modification.
	 */
	public static function load_composer_front_css(): void {
		\wp_enqueue_style( 'js_composer_front' );
	}

	/**
	 * Register scripts for later loading.
	 */
	public static function register_scripts(): void {
		\wp_register_script(
			'wpex-vc_accordion-events',
			\totaltheme_get_js_file( 'frontend/wpbakery/vc_accordion-events' ),
			[ 'vc_accordion_script', 'jquery' ],
			\WPEX_THEME_VERSION,
			true
		);

		\wp_register_script(
			'wpex-vc_waypoints',
			\totaltheme_get_js_file( 'frontend/wpbakery/vc_waypoints' ),
			[ 'jquery-core', 'vc_waypoints' ],
			\WPEX_THEME_VERSION,
			true
		);

		/**
		 * Filters the vc_waypoints script settings.
		 *
		 * @param array $settings
		 */
		$waypoints_settings = [
			'delay'  => 300,
			'offset' => '85%'
		];
		$waypoints_settings = (array) \apply_filters( 'wpex_vc_waypoints_settings', $waypoints_settings );

		\wp_localize_script(
			'wpex-vc_waypoints',
			'wpex_vc_waypoints_params',
			$waypoints_settings
		);
	}

	/**
	 * Enqueues scripts dependent on other WPBakery scripts.
	 */
	public static function enqueue_dependent_scripts(): void {
		if ( \wp_script_is( 'vc_accordion_script' ) ) {
			\wp_enqueue_script( 'wpex-vc_accordion-events' );
		}
		if ( \wp_script_is( 'vc_waypoints' ) ) {
			\wp_enqueue_script( 'wpex-vc_waypoints' );
		}
	}

	/**
	 * iFrame Scripts.
	 *
	 * These scripts load in the front-end editor iframe which renders the site.
	 */
	public static function iframe_scripts(): void {
		\wp_enqueue_style(
			'totaltheme-admin-wpbakery-iframe',
			\totaltheme_get_css_file( 'admin/wpbakery/iframe' ),
			[],
			\WPEX_THEME_VERSION
		);

		if ( is_rtl() ) {
			\wp_enqueue_style(
				'totaltheme-admin-wpbakery-iframe-rtl',
				\totaltheme_get_css_file( 'admin/wpbakery/iframe-rtl' ),
				[],
				\WPEX_THEME_VERSION
			);
		}

		\wp_enqueue_script(
			'totaltheme-admin-wpbakery-vc_reload',
			\totaltheme_get_js_file( 'admin/wpbakery/vc_reload' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION,
			true
		);
	}

	/**
	 * Register editor scripts.
	 */
	public static function _register_editor_scripts(): void {
		\wp_register_script(
			'totaltheme-admin-wpbakery-edit-form',
			\totaltheme_get_js_file( 'admin/wpbakery/edit-form' ),
			[],
			\WPEX_THEME_VERSION
		);

	}

	/**
	 * Register editor css.
	 */
	public static function register_admin_css(): void {
		\wp_register_style(
			'totaltheme-admin-wpbakery-backend-editor',
			\totaltheme_get_css_file( 'admin/wpbakery/backend-editor' ),
			[ 'js_composer' ],
			\WPEX_THEME_VERSION
		);

		if ( is_rtl() ) {
			\wp_register_style(
				'totaltheme-admin-wpbakery-backend-editor-rtl',
				\totaltheme_get_css_file( 'admin/wpbakery/backend-editor-rtl' ),
				[ 'js_composer' ],
				\WPEX_THEME_VERSION
			);
		}

		\wp_register_style(
			'totaltheme-admin-wpbakery-frontend-editor',
			\totaltheme_get_css_file( 'admin/wpbakery/frontend-editor' ),
			[ 'vc_inline_css' ],
			\WPEX_THEME_VERSION
		);

		if ( is_rtl() ) {
			\wp_register_style(
				'totaltheme-admin-wpbakery-frontend-editor-rtl',
				\totaltheme_get_css_file( 'admin/wpbakery/frontend-editor-rtl' ),
				[ 'vc_inline_css' ],
				\WPEX_THEME_VERSION
			);
		}

		// @todo remove following scripts if WPBakery ever adds PHP hooks.
		\wp_register_style(
			'totaltheme-admin-wpbakery-slim-mode',
			\totaltheme_get_css_file( 'admin/wpbakery/slim-mode' ),
			[],
			\WPEX_THEME_VERSION
		);

		\wp_register_style(
			'totaltheme-admin-wpbakery-theme-mode',
			\totaltheme_get_css_file( 'admin/wpbakery/theme-mode' ),
			[],
			\WPEX_THEME_VERSION
		);
	}

	/**
	 * Enqueue backend editor scripts.
	 */
	public static function enqueue_be_editor_scripts(): void {
		\wp_enqueue_script( 'totaltheme-admin-wpbakery-edit-form' );
		\wp_enqueue_style( 'totaltheme-admin-wpbakery-backend-editor' );

		if ( \is_rtl() ) {
			\wp_enqueue_style( 'totaltheme-admin-wpbakery-backend-editor-rtl' );
		}

		if ( \totaltheme_call_static( __CLASS__ . '\Slim_Mode', 'is_enabled' ) ) {
			\wp_enqueue_style( 'totaltheme-admin-wpbakery-slim-mode' );
		}

		if ( Helpers::is_theme_mode_enabled() ) {
			\wp_enqueue_style( 'totaltheme-admin-wpbakery-theme-mode' );
		}
	}

	/**
	 * Enqueue frontend editor scripts.
	 */
	public static function enqueue_fe_editor_scripts(): void {
		\wp_enqueue_script( 'totaltheme-admin-wpbakery-edit-form' );
		\wp_enqueue_style( 'totaltheme-admin-wpbakery-frontend-editor' );

		if ( \is_rtl() ) {
			\wp_enqueue_style( 'totaltheme-admin-wpbakery-frontend-editor-rtl' );
		}

		if ( \totaltheme_call_static( __CLASS__ . '\Slim_Mode', 'is_enabled' ) ) {
			\wp_enqueue_style( 'totaltheme-admin-wpbakery-slim-mode' );
		}

		if ( Helpers::is_theme_mode_enabled() ) {
			\wp_enqueue_style( 'totaltheme-admin-wpbakery-theme-mode' );
		}
	}

	/**
	 * Adds Customizer settings for VC.
	 */
	public static function customizer_settings( array $panels ): array {
		$branding = ( $branding = \wpex_get_theme_branding() ) ? ' (' . $branding . ')' : '';
		$panels['visual_composer'] = [
			'title'      => "WPBakery Builder{$branding}",
			'settings'   => \WPEX_INC_DIR . 'integration/wpbakery/customizer-settings.php',
			'is_section' => true,
			'icon'       => "data:image/svg+xml,%3Csvg fill='%230473aa' height='20' width='20' viewBox='0.0004968540742993355 -0.00035214610397815704 65.50897979736328 49.80835723876953' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M51.345 9.041c-4.484-.359-6.371.747-8.509 1.039 2.169 1.135 5.125 2.099 8.708 1.89-3.3 1.296-8.657.853-12.355-1.406-.963-.589-1.975-1.519-2.733-2.262C33.459 5.583 31.247.401 21.683.018 9.687-.457.465 8.347.016 19.645s8.91 20.843 20.907 21.318c.158.008.316.006.472.006 3.137-.184 7.27-1.436 10.383-3.355-1.635 2.32-7.746 4.775-10.927 5.553.319 2.527 1.671 3.702 2.78 4.497 2.459 1.76 5.378-.73 12.11-.606 3.746.069 7.61 1.001 10.734 2.75l3.306-11.54c8.402.13 15.4-6.063 15.716-14.018.321-8.088-5.586-14.527-14.152-16.209h0z'%3E%3C/path%3E%3C/svg%3E",
		];
		return $panels;
	}

	/**
	 * Add noscript tag for stretched rows.
	 */
	public static function noscript(): void {
		echo '<noscript><style>body:not(.content-full-screen) .wpex-vc-row-stretched[data-vc-full-width-init="false"]{visibility:visible;}</style></noscript>';
	}

	/**
	 * Add new background style options.
	 */
	public static function background_styles( array $styles ): array {
		$styles[ \esc_html__( 'Repeat-x', 'total' ) ] = 'repeat-x';
		$styles[ \esc_html__( 'Repeat-y', 'total' ) ] = 'repeat-y';
		return $styles;
	}

	/**
	 * Disable builder completely on admin post types.
	 */
	public static function disable_editor( $check, $type ) {
		$excluded_types = [
			'attachment',
			'wpex_font',
			'wpex_sidebars',
			'wpex_color_palette',
			'acf',
			'acf-field-group',
			'acf-ui-options-page',
			'elementor_library',
		];
		if ( \in_array( $type, $excluded_types, true ) || \wpex_get_ptu_type_mod( $type, 'disable_wpbakery' ) ) {
			$check = false;
		}
		return $check;
	}

	/**
	 * Fixes issues with multisite wp-activate.php and wp-signup.php template not displaying shortcodes.
	 *
	 * @todo is this still needed?
	 */
	public static function init_wpbakery(): void {
		$methods = [
			[ \wpbakery(), 'frontCss' ],
			[ \wpbakery(), 'frontJsRegister'],
			[ 'WPBMap', 'addAllMappedShortcodes' ],
		];
		foreach ( $methods as $method ) {
			if ( \is_callable( $method ) ) {
				\call_user_func( $method );
			}
		}
	}

	/**
	 * Removes the custom fonts loaded by the WPBakery editor.
	 */
	public static function _filter_editor_css_dependencies( $dependencies ) {
		if ( \is_array( $dependencies ) ) {
			foreach ( $dependencies as $key => $val ) {
				// @todo should we remove 'wp-color-picker', 'farbtastic' as well?
				if ( \in_array( $val, [ 'vc_google_fonts' ], true ) ) {
					unset( $dependencies[ $key ] );
				}
			}
		}
		return $dependencies;
	}

	/**
	 * Remove scripts hooked in too late for me to remove on wp_enqueue_scripts.
	 */
	public static function remove_footer_scripts(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 5.18' );
	}

}
