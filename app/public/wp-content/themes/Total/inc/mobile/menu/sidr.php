<?php

namespace TotalTheme\Mobile\Menu;

\defined( 'ABSPATH' ) || exit;

/**
 * Sidr mobile Menu.
 */
final class Sidr {

	/**
	 * JS handle.
	 */
	public const JS_HANDLE = 'wpex-mobile-menu-sidr';

	/**
	 * JS object.
	 */
	public const JS_OBJECT = 'wpex_mobile_menu_sidr_params';

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of our class.
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
	 * Check if the sidr mobile menu is enabled.
	 */
	public function is_enabled(): bool {
		return 'sidr' === totaltheme_call_static( 'Mobile\Menu', 'style' ) && totaltheme_call_static( 'Mobile\Menu', 'is_enabled' );
	}

	/**
	 * Enqueue Scripts.
	 */
	public function enqueue_js(): void {
		\wp_enqueue_script(
			self::JS_HANDLE,
			\totaltheme_get_js_file( 'frontend/mobile-menu/sidr' ),
			[ \WPEX_THEME_JS_HANDLE ],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);
		if ( $l10n = $this->get_l10n() ) {
			\wp_localize_script( self::JS_HANDLE, self::JS_OBJECT, $l10n );
		}
	}

	/**
	 * Returns l10n.
	 */
	private function get_l10n(): array {
		$l10n = [
			'source'           => $this->get_source(),
			'side'             => $this->get_position(),
			'dark_surface'     => \wp_validate_boolean( get_theme_mod( 'mobile_menu_sidr_dark_surface', true ) ),
			'displace'         => \wp_validate_boolean( get_theme_mod( 'mobile_menu_sidr_displace', false ) ),
			'aria_label'       => \esc_attr( \wpex_get_aria_label( 'mobile_menu' ) ),
			'aria_label_close' => \esc_attr( \wpex_get_aria_label( 'mobile_menu_close' ) ),
			'class'            => [ 'wpex-mobile-menu' ],
			'speed'            => 300,
		];
		if ( \get_theme_mod( 'mobile_menu_sidr_background' ) ) {
			$l10n['class'] = 'has-background';
		}
		$l10n = \array_merge( \totaltheme_call_static( 'Mobile\Menu', 'get_global_js_l10n' ), $l10n );
		return (array) apply_filters( 'totaltheme/mobile/menu/sidr/l10n', $l10n );
	}

	/**
	 * Returns source.
	 */
	private function get_source(): string {
		$items = [];
		if ( \wpex_has_mobile_menu_alt() ) {
			$items['nav'] = '#mobile-menu-alternative';
		} else {
			$items['nav'] = '#site-navigation';
		}
		if ( \get_theme_mod( 'mobile_menu_search', true ) ) {
			$items['search'] = '#mobile-menu-search';
		}
		$items = \apply_filters( 'wpex_mobile_menu_source', $items ); // @deprecrated
		$items = (array) \apply_filters( 'totaltheme/mobile/menu/sidr/source', $items );
		return \implode( ', ', $items );
	}

	/**
	 * Returns sidr position (left or right).
	 */
	private function get_position(): string {
		$position = ( $position = \get_theme_mod( 'mobile_menu_sidr_direction' ) ) ? sanitize_text_field( $position ) : '';
		if ( ! $position || ! in_array( $position, [ 'left', 'right' ], true ) ) {
			if ( totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
				$toggle_style = \wpex_header_menu_mobile_toggle_style();
				if ( 'centered_logo' === $toggle_style || 'next_to_logo' === $toggle_style ) {
					$position = 'left';
				} else {
					$position = 'right';
				}
			} else {
				$position = 'right';
			}
		}
		return $position;
	}

	/**
	 * Register Scripts.
	 */
	public function register_js(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 6.0' );
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
