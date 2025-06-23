<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Dark Mode.
 */
class Dark_mode {

	/**
	 * Dark Mode is enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Checks if dark mode is enabled.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}
		self::$is_enabled = (bool) \wp_validate_boolean( \get_theme_mod( 'dark_mode_enable', false ) );
		return self::$is_enabled;
	}

	/**
	 * Enqueue dark mode JS.
	 */
	public static function enqueue_js(): void {
		\wp_enqueue_script(
			'wpex-dark-mode',
			\totaltheme_get_js_file( 'frontend/dark-mode' ),
			[],
			\WPEX_THEME_VERSION
		);
		\wp_localize_script( 'wpex-dark-mode', 'wpex_dark_mode_params', [
			'check_system_pref' => \get_theme_mod( 'dark_mode_check_system_pref', true ) && ! \is_customize_preview(),
		] );
	}

	/**
	 * Enqueue dark mode JS.
	 */
	public static function get_header_logo(): string {
		$logo = (string) apply_filters( 'totaltheme/dark_mode/header_logo', get_theme_mod( 'custom_logo_dark' ) );
		return \wpex_get_image_url( $logo );
	}

	/**
	 * Returns the header logo retina image url.
	 */
	public static function get_header_logo_retina(): string {
		$logo = (string) apply_filters( 'totaltheme/dark_mode/header_logo_retina', get_theme_mod( 'retina_logo_dark' ) );
		return \wpex_get_image_url( $logo );
	}

	/**
	 * Returns the sticky header logo.
	 */
	public static function get_sticky_header_logo(): string {
		$logo = (string) apply_filters( 'totaltheme/dark_mode/sticky_header_logo', get_theme_mod( 'fixed_header_logo_dark' ) );
		return \wpex_get_image_url( $logo );
	}

	/**
	 * Returns the sticky header logo retina.
	 */
	public static function get_sticky_header_logo_retina(): string {
		$logo = (string) apply_filters( 'totaltheme/dark_mode/sticky_header_logo', get_theme_mod( 'fixed_header_logo_retina_dark' ) );
		return \wpex_get_image_url( $logo );
	}

	/**
	 * Get Icon Name.
	 */
	public static function get_icon_name( $theme ): string {
		if ( 'dark' === $theme ) {
			$name = ( $name = get_theme_mod( 'dark_mode_icon_dark' ) ) ? \sanitize_text_field( $name ) : 'circle-half-stroke';
		} else {
			$name = ( $name = get_theme_mod( 'dark_mode_icon_light' ) ) ? \sanitize_text_field( $name ) : 'circle-half-stroke';
		}
		return (string) $name;
	}

	/**
	 * Returns labels.
	 */
	public static function get_label( $theme ): string {
		if ( 'dark' === $theme ) {
			return \esc_html__( 'Dark Mode', 'total' );
		} else {
			return \esc_html__( 'Light Mode', 'total' );
		}
	}

	/**
	 * Check if the dark mode icon should be automatically inserted into the menu.
	 */
	public static function auto_insert_menu_icon( $menu_location ): bool {
		return self::is_enabled()
			&& ( 'main_menu' === $menu_location || 'mobile_menu_alt' === $menu_location )
			&& get_theme_mod( 'dark_mode_menu_icon', true );
	}

	/**
	 * Hooks into "wp_nav_menu_items" to insert the dark mode icon into the main menu.
	 */
	public static function filter_wp_nav_menu_items( $items, $args ) {
		$theme_location = $args->theme_location ?? '';

		if ( ! self::auto_insert_menu_icon( $theme_location ) ) {
			return $items;
		}

		$li_class = 'theme-toggle-li menu-item menu-item-theme-toggle';
		$a_class  = '';

		// 3rd party plugin integrations
		if ( 'mobile_menu_alt' !== $theme_location ) {

			// Ubermenu
			if ( \class_exists( 'UberMenu' ) ) {
				$li_class .= ' ubermenu-item-level-0 ubermenu-item';
				$a_class  .= ' ubermenu-target ubermenu-item-layout-default ubermenu-item-layout-text_only';
			}

			// Max Mega Menu
			if ( \function_exists( 'max_mega_menu_is_enabled' ) && \max_mega_menu_is_enabled( $theme_location ) ) {
				$li_class .= ' mega-menu-item';
				$a_class  .= ' mega-menu-link';
			}

		}

		$dark_mode_icon = '<li class="' . \esc_attr( $li_class ) . '"><a href="#" data-role="button" data-wpex-toggle="theme" aria-label="' . \esc_attr( wpex_get_aria_label( 'dark_mode_toggle' ) ) . '"';
			if ( $a_class ) {
				$dark_mode_icon .= 'class="' . \esc_attr( $a_class ) . '"';
			}
		$dark_mode_icon .= '><span class="link-inner">';
			$dark_mode_icon .= '<span class="hidden-dark-mode wpex-no-renaming wpex-flex wpex-items-center wpex-gap-10">' . \totaltheme_get_icon( self::get_icon_name( 'dark' ), 'menu-item-theme-toggle__icon wpex-icon--w' ) . '<span class="menu-item-theme-toggle__label wpex-hidden">' . self::get_label( 'dark' ) . '</span></span>';
			$dark_mode_icon .= '<span class="visible-dark-mode wpex-no-renaming wpex-flex wpex-items-center wpex-gap-10">' . \totaltheme_get_icon( self::get_icon_name( 'light' ), 'menu-item-theme-toggle__icon wpex-icon--w' ) . '<span class="menu-item-theme-toggle__label wpex-hidden">' . self::get_label( 'light' ) . '</span></span>';
		$dark_mode_icon .= '</span></a></li>';

		$items .= $dark_mode_icon;

		return $items;
	}

}
