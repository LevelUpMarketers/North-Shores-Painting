<?php

namespace TotalTheme\Scripts;

\defined( 'ABSPATH' ) || exit;

/**
 * JS Scripts.
 */
class JS {

	/**
	 * Returns the JS extension for files.
	 */
	public static function js_extension() {
		\_deprecated_function( __METHOD__, 'Total Theme 6.0' );
		return '.min.js';
	}

	/**
	 * Register scripts on the init hook early enough for WPBakery.
	 */
	public static function register_early(): void {

		// Register core JS - needs to register before anything else
		\wp_register_script(
			WPEX_THEME_JS_HANDLE,
			\totaltheme_get_js_file( 'frontend/core' ),
			[],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);

		\wp_localize_script( \WPEX_THEME_JS_HANDLE, 'wpex_theme_params', self::l10n() );
		

		// Slider Pro
		\wp_deregister_script( 'slider-pro' ); // prevent issues since the version in Total has some tweaks
		\wp_register_script(
			'slider-pro',
			\totaltheme_get_js_file( 'vendor/jquery.sliderPro' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION,
			true
		);

		\wp_register_script(
			'wpex-slider-pro-custom-thumbs',
			\totaltheme_get_js_file( 'vendor/jquery.sliderProCustomThumbnails' ),
			[ 'jquery', 'slider-pro' ],
			\WPEX_THEME_VERSION,
			true
		);

		\wp_register_script(
			'wpex-slider-pro',
			\totaltheme_get_js_file( 'frontend/slider-pro' ),
			[ 'jquery', 'slider-pro' ],
			\WPEX_THEME_VERSION,
			true
		);

		$slider_prev_icon = \apply_filters( 'wpex_slider_prev_icon', 'material-arrow-back-ios' );
		$slider_next_icon = \apply_filters( 'wpex_slider_next_icon', 'material-arrow-forward-ios' );

		\wp_localize_script(
			'wpex-slider-pro',
			'wpex_slider_pro_params',
			[
				'i18n' => [
					'NEXT' => \esc_html__( 'next Slide', 'total' ),
					'PREV' => \esc_html__( 'previous Slide', 'total' ),
					'GOTO' => \esc_html__( 'go to slide', 'total' ),
				],
				'previousArrow' => '<div class="sp-arrow sp-previous-arrow" tabindex="0" role="button"><span class="screen-reader-text">' . esc_html__( 'previous slide', 'total' ) . '</span><span class="sp-arrow-inner wpex-flex wpex-items-center wpex-justify-center wpex-w-100 wpex-h-100">' . \totaltheme_call_static( 'Theme_Icons', 'get_icon', $slider_prev_icon ) . '</span></div>',
				'nextArrow' => '<div class="sp-arrow sp-next-arrow" tabindex="0" role="button"><span class="screen-reader-text">' . esc_html__( 'next slide', 'total' ) . '</span></span><span class="sp-arrow-inner wpex-flex wpex-items-center wpex-justify-center wpex-w-100 wpex-h-100">' . \totaltheme_call_static( 'Theme_Icons', 'get_icon', $slider_next_icon ) . '</div>',
			]
		);

		// Isotope
		\wp_register_script(
			'isotope',
			\totaltheme_get_js_file( 'vendor/isotope.pkgd' ),
			[ 'imagesloaded' ],
			'3.0.6',
			true
		);

		\wp_register_script(
			'wpex-isotope',
			\totaltheme_get_js_file( 'frontend/isotope' ),
			[ 'isotope', 'imagesloaded' ],
			\WPEX_THEME_VERSION,
			true
		);

		\wp_localize_script(
			'wpex-isotope',
			'wpex_isotope_params',
			\wpex_get_masonry_settings()
		);

		// Parallax Backgrounds
		\wp_register_script(
			'wpex-parallax-backgrounds',
			\totaltheme_get_js_file( 'frontend/parallax-backgrounds' ),
			[],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer'
			]
		);

		// Background Slider
		\wp_register_script(
			'wpex-background-slider',
			\totaltheme_get_js_file( 'frontend/background-slider' ),
			[],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer'
			]
		);

		// Off Canvas
		\wp_register_script(
			'wpex-off-canvas',
			\totaltheme_get_js_file( 'frontend/off-canvas' ),
			[ WPEX_THEME_JS_HANDLE ],
			\WPEX_THEME_VERSION,
			[
				'in_footer' => false,
				'strategy'  => 'defer',
			]
		);

		// Lightbox.
		\totaltheme_call_static( 'Lightbox', 'register_js' );
	}

	/**
	 * Register scripts that aren't enqueued right away.
	 */
	public static function register(): void {

		// Social share
		\wp_register_script(
			'wpex-social-share',
			\totaltheme_get_js_file( 'frontend/social-share' ),
			[],
			\WPEX_THEME_VERSION,
			[
				'in_footer' => false,
				'strategy'  => 'defer',
			]
		);

		// Edit Links
		if ( is_user_logged_in() ) {
			\wp_register_script(
				'wpex-hide-edit-links',
				\totaltheme_get_js_file( 'frontend/hide-edit-links' ),
				[],
				\WPEX_THEME_VERSION,
				[
					'in_footer' => false,
					'strategy'  => 'defer',
				]
			);
		}

		// Menu Widget Accordion
		\wp_register_script(
			'wpex-widget-nav-menu',
			\totaltheme_get_js_file( 'frontend/wp/widget-nav-menu' ),
			[ \WPEX_THEME_JS_HANDLE ],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);

	}

	/**
	 * Enqueue Theme scripts.
	 */
	public static function enqueue() {

		// Core js
		\wp_enqueue_script( WPEX_THEME_JS_HANDLE );

		// Inline Script
		\wp_register_script( 'wpex-inline', false );
		\wp_add_inline_script(
			'wpex-inline',
			'!function(){const e=document.querySelector("html"),t=()=>{const t=window.innerWidth-document.documentElement.clientWidth;t&&e.style.setProperty("--wpex-scrollbar-width",`${t}px`)};t(),window.addEventListener("resize",(()=>{t()}))}();'
		);
		\wp_enqueue_script( 'wpex-inline' );

		// Dark Mode
		if ( true === \totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
			\totaltheme_call_static( 'Dark_Mode', 'enqueue_js' );
		}

		// Header search js
		if ( \totaltheme_call_static( 'Header\Menu\Search', 'is_enabled' ) ) {
			\totaltheme_call_static( 'Header\Menu\Search', 'enqueue_js' );
		}

		// Sticky elements - must be added in order (topbar,nav,header) !
		// We can enqueue these right away to prevent extra checks
		if ( \totaltheme_call_static( 'Topbar\Sticky', 'is_enabled' ) ) {
			\totaltheme_call_static( 'Topbar\Sticky', 'enqueue_js' );
		}
	
		if ( \totaltheme_call_static( 'Header\Menu\Sticky', 'is_enabled' ) ) {
			\totaltheme_call_static( 'Header\Menu\Sticky', 'enqueue_js' );
		}

		if ( \totaltheme_call_static( 'Header\Sticky', 'is_enabled' ) ) {
			\totaltheme_call_static( 'Header\Sticky', 'enqueue_js' );
		}

		// Header Menu scripts
		if ( \totaltheme_call_static( 'Header\Menu', 'is_enabled' ) ) {

			// Superfish dropdowns
			if ( 'sfhover' === \totaltheme_call_static( 'Header\Menu', 'get_dropdown_method' ) ) {
				\wp_enqueue_script(
					'wpex-superfish',
					\totaltheme_get_js_file( 'vendor/jquery.superfish' ),
					[ 'jquery', 'hoverIntent' ],
					\WPEX_THEME_VERSION,
					[
						'in_footer' => false,
						'strategy'  => 'defer',
					]
				);
				$superfish_params = (array) \apply_filters( 'wpex_superfish_params', [
					'delay'    => 600,
					'speed'    => 'fast',
					'speedOut' => 'fast',
				] );
				\wp_localize_script(
					'wpex-superfish',
					'wpex_superfish_params',
					$superfish_params
				);
			}

		}

		// Mobile Menu
		if ( \totaltheme_call_static( 'Header\Menu', 'is_enabled' ) ) {
			\totaltheme_call_static( 'Mobile\Menu', 'enqueue_js' );
		}

		// Toggle Bar
		if ( \wpex_has_togglebar() ) {
			\wp_enqueue_script(
				'wpex-toggle-bar',
				\totaltheme_get_js_file( 'frontend/toggle-bar' ),
				[],
				\WPEX_THEME_VERSION,
				[
					'in_footer' => false,
					'strategy'  => 'defer',
				]
			);
		}

		// Comment reply
		if ( \is_singular() && \comments_open() && \get_option( 'thread_comments' ) && ! \wp_script_is ( 'comment-reply' ) ) {
			\wp_enqueue_script(
				'wpex-comment-reply',
				\totaltheme_get_js_file( 'frontend/wp/comment-reply' ),
				[],
				'2.7.0',
				[
					'in_footer' => false,
					'strategy'  => 'defer',
				]
			);
		}

		// Global lightbox
		\totaltheme_call_static( 'Lightbox', 'enqueue_global_scripts' );
	}

	/**
	 * Main js l10n.
	 */
	private static function l10n(): array {
		$l10n = [];

		// Custom selects
		if ( \apply_filters( 'wpex_custom_selects_js', true ) ) {
			$l10n['selectArrowIcon'] = \esc_html( \totaltheme_call_static( 'Forms\Select_Wrap', 'get_arrow_icon_html' ) );
			$l10n['customSelects'] = '.widget_categories form,.widget_archive select,.vcex-form-shortcode select';

			if ( \totaltheme_is_integration_active( 'woocommerce' )
				&& \totaltheme_call_static( 'Integration\WooCommerce', 'is_advanced_mode' )
			) {
				$l10n['customSelects'] .= ',.woocommerce-ordering .orderby,#dropdown_product_cat,.single-product .variations_form .variations select';
				if ( class_exists( 'WC_Product_Addons' ) ) {
					$l10n['customSelects'] .= ',.wc-pao-addon .wc-pao-addon-wrap select';
				}
			}

			if ( \totaltheme_is_integration_active( 'bbpress' ) ) {
				$l10n['customSelects'] .= ',#bbp_stick_topic_select,#bbp_topic_status_select';
			}

			if ( \totaltheme_is_integration_active( 'lifterlms' ) ) {
				$l10n['customSelects'] .= ',#llms-quiz-attempt-select';
			}
		}

		/**
		 * Local Scroll args.
		 */
		if ( $local_scroll_l10n = \totaltheme_call_non_static( 'Local_Scroll', 'get_l10n' ) ) {
			$l10n = \array_merge( $l10n, $local_scroll_l10n );
		}

		/**
		 * WPBakery.
		 */
		if ( \totaltheme_is_integration_active( 'wpbakery' ) ) {
			if ( ! \wp_validate_boolean( \get_theme_mod( 'vc_tta_animation_enable' ) ) ) {
				$l10n['disable_vc_tta_animation'] = 1;
			}
		}

		$l10n = \apply_filters( 'wpex_localize_array', $l10n ); // soft deprecated @since 5.7.0

		return (array) $l10n;
	}

}
