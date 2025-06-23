<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * bbPress Integration.
 */
final class BuddyPress {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of BuddyPress.
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
		\define( 'WPEX_BUDDYPRESS_DIR', \WPEX_INC_DIR . 'integration/buddypress/' );
		\add_action( 'wp_enqueue_scripts', [ $this, 'scripts'], 20 );
		\add_filter( 'totaltheme/page/header/is_enabled', [ $this, 'maybe_disable_page_header'] );
		\add_filter( 'wpex_post_layout_class', [ $this, 'layouts'], 11 ); // on 11 due to buddyPress issues
		\add_filter( 'wpex_customizer_panels', [ $this, 'add_customizer_panel'] );
	}

	/**
	 * Load custom CSS.
	 */
	public function scripts(): void {
		if ( ! get_theme_mod( 'bp_enqueue_theme_styles', true ) ) {
			return;
		}

		$deps = [];

		if ( \wp_style_is( 'bp-nouveau' ) ) {
			$deps[] = 'bp-nouveau';
		}

		\wp_enqueue_style(
			'wpex-buddypress',
			\totaltheme_get_css_file( 'frontend/buddypress' ),
			$deps,
			WPEX_THEME_VERSION
		);
	}

	/**
	 * Potentially disable the page hader.
	 */
	public function maybe_disable_page_header( $check ): bool {
		if ( $check ) {
			if ( ! \is_buddypress() ) {
				return $check;
			}
			if ( \bp_is_directory() ) {
				if ( ! \get_theme_mod( 'bp_directory_page_title', true ) ) {
					$check = false;
				}
			} elseif ( bp_is_user() ) {
				if ( ! \get_theme_mod( 'bp_user_singular_page_title', true ) ) {
					$check = false;
				}
			}
		}
		return $check;
	}

	/**
	 * Set layouts.
	 */
	public function layouts( $layout ): string {
		if ( \is_buddypress() ) {
			$layout = \get_theme_mod( 'bp_layout' ) ?: \wpex_get_default_content_area_layout();
			if ( \bp_is_directory() ) {
				$layout = \get_theme_mod( 'bp_directory_layout' ) ?: $layout;
			} elseif ( \bp_is_user() ) {
				$layout = \get_theme_mod( 'bp_user_layout' ) ?: $layout;
			}
		}
		return $layout;
	}

	/**
	 * Adds new Customizer Panel for bbPress.
	 */
	public function add_customizer_panel( array $panels ): array {
		$panels['buddypress'] = [
			'title'    => \esc_html__( 'BuddyPress (Total)', 'total' ),
			'settings' => WPEX_BUDDYPRESS_DIR . 'customizer-settings.php',
			'icon'       => '\f448',
		];
		return $panels;
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
