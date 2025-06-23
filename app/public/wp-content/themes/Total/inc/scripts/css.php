<?php

namespace TotalTheme\Scripts;

\defined( 'ABSPATH' ) || exit;

/**
 * CSS Scripts.
 */
final class CSS {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Get theme handle.
	 */
	public static function get_theme_handle() {
		$theme_handle = \WPEX_THEME_STYLE_HANDLE;
		if ( \is_child_theme() ) {
			$parent_handle = (string) \apply_filters( 'wpex_parent_stylesheet_handle', 'parent-style' );
			if ( \wp_style_is( $parent_handle ) ) {
				$theme_handle = $parent_handle; // only alter the handle if the script is actually loaded.
			}
		}
		return $theme_handle;
	}

	/**
	 * Register scripts on the init hook early enough for WPBakery.
	 */
	public static function register_early(): void {
		\wp_register_style(
			'wpex-hover-animations',
			\totaltheme_get_css_file( 'vendor/hover-css' ),
			[],
			'2.0.1'
		);

		\wp_register_style(
			'slider-pro',
			\totaltheme_get_css_file( 'vendor/jquery.sliderPro' ),
			[],
			'1.3'
		);

		\totaltheme_call_static( 'Lightbox', 'register_css' );
	}

	/**
	 * Register styles.
	 */
	public static function register(): void {
		// Nothing needs to be registered here anymore.
	}

	/**
	 * Enqueue Theme styles.
	 */
	public static function enqueue(): void {
		$theme_handle = self::get_theme_handle();

		// Main style.css File.
		\wp_enqueue_style(
			\WPEX_THEME_STYLE_HANDLE, // !! must be hardset here !!
			\get_stylesheet_uri(),
			[],
			\WPEX_THEME_VERSION
		);

		// Mobile menu breakpoint CSS.
		if ( ! \totaltheme_call_static( 'Header\Core', 'is_custom' ) ) {
			$mm_breakpoint = \totaltheme_call_static( 'Mobile\Menu', 'breakpoint' );
			$max_media     = false;
			$min_media     = false;

			if ( $mm_breakpoint < 9999 && \wpex_is_layout_responsive() ) {
				$max_media = "only screen and (max-width:{$mm_breakpoint}px)";
				$mm_breakpoint_plus_one = $mm_breakpoint + 1;
				$min_media = "only screen and (min-width:{$mm_breakpoint_plus_one}px)";
			}

			\wp_enqueue_style(
				'wpex-mobile-menu-breakpoint-max',
				\totaltheme_get_css_file( 'frontend/breakpoints/max' ),
				$theme_handle ? [ $theme_handle ] : [],
				\WPEX_THEME_VERSION,
				$max_media
			);

			if ( $min_media ) {
				\wp_enqueue_style(
					'wpex-mobile-menu-breakpoint-min',
					\totaltheme_get_css_file( 'frontend/breakpoints/min' ),
					$theme_handle ? [ $theme_handle ] : [],
					\WPEX_THEME_VERSION,
					$min_media
				);

				// Vertical header CSS - @todo allow vertical header with header builder.
				\totaltheme_call_static( 'Header\Vertical', 'maybe_enqueue_stylesheet' );
			}

		}

		// Dark mode.
		if ( \totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
			\wp_enqueue_style(
				'wpex-dark-mode',
				\totaltheme_get_css_file( 'frontend/dark-mode' ),
				$theme_handle ? [ $theme_handle ] : [],
				\WPEX_THEME_VERSION
			);
		}

		// Overlay/Transparent header.
		\totaltheme_call_static( 'Header\Overlay', 'maybe_enqueue_stylesheet' );

		// Load theme icons.
		if ( \totaltheme_call_static( 'Theme_Icons', 'is_enabled' ) && 'font' === \totaltheme_call_static( 'Theme_Icons', 'get_format' ) ) {
			\totaltheme_call_static( 'Theme_Icons', 'enqueue_font_style' );
		}

		// Total Shortcodes.
		if ( \get_theme_mod( 'extend_visual_composer', true ) ) {
			\wp_enqueue_style(
				'vcex-shortcodes',
				\totaltheme_get_css_file( 'frontend/vcex-shortcodes' ),
				[],
				\WPEX_THEME_VERSION
			);
		}

		// Customizer CSS.
		if ( \is_customize_preview() ) {
			\wp_enqueue_style(
				'totaltheme-customize-shortcuts',
				\totaltheme_get_css_file( 'customize/shortcuts' ),
				[],
				\WPEX_THEME_VERSION
			);
		}

		// Login template.
		if ( \is_page_template( 'templates/login.php' ) ) {
			\wp_enqueue_style(
				'totaltheme-templates-login',
				\totaltheme_get_css_file( 'frontend/templates/login' ),
				[ $theme_handle ],
				\WPEX_THEME_VERSION
			);
		}

		// Post Edit.
		if ( \wp_validate_boolean( \get_theme_mod( 'edit_post_link_enable', true ) )
			&& \is_singular()
			&& \is_user_logged_in()
			&& current_user_can( 'edit_post', get_the_ID() )
		) {
			\wp_enqueue_style(
				'totaltheme-post-edit',
				\totaltheme_get_css_file( 'frontend/post-edit' ),
				[ $theme_handle ],
				\WPEX_THEME_VERSION
			);
		}

	}

}
