<?php declare(strict_types=1);

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * VCEX Shortcodes.
 *
 * The original Visual Composer Extension Plugin by WPExplorer built for Total.
 */
final class Vcex {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Create or retrieve the instance of Init.
	 */
	public static function init(): void {
		self::include_functions();
		self::init_classes();
		self::integrations();

		if ( self::maybe_register_shortcodes() && \class_exists( '\TotalThemeCore\Vcex\Shortcode_Abstract' ) ) {
			self::register_shortcodes();
		}
	}

	/**
	 * Run global classes.
	 */
	private static function init_classes(): void {
		\totalthemecore_init_class( 'Vcex\Scripts' );
		\totalthemecore_init_class( 'Vcex\Ajax' );
	}

	/**
	 * Include helper functions.
	 */
	private static function include_functions(): void {
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/deprecated.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/core.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/shortcode-atts.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/arrays.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/grid-filter.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/loadmore.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/entry-classes.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/onclick.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/scripts.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/vcex/functions/parsers.php';
	}

	/**
	 * Check if we should register shortcodes or not.
	 *
	 * Prevent shortcode rendering via $_REQUEST - security patch for WP core issue.
	 */
	protected static function maybe_register_shortcodes(): bool {
		return ! isset( $_REQUEST['action'] ) || 'parse-media-shortcode' !== $_REQUEST['action'];
	}

	/**
	 * Register shortcodes.
	 */
	private static function register_shortcodes(): void {
		totalthemecore_call_non_static( 'Vcex\Shortcodes_Registry', 'register_all' );
	}

	/**
	 * Integrations.
	 */
	private static function integrations(): void {
		if ( \apply_filters( 'vcex_gutenberg_integration', true ) ) {
			\totalthemecore_init_class( 'Vcex\Gutenberg' );
		}
		if ( \did_action( 'elementor/loaded' ) && (bool) \apply_filters( 'vcex_elementor_integration', true ) ) {
			\totalthemecore_init_class( 'Vcex\Elementor' );
		}
		if ( \class_exists( '\Vc_Manager', false ) ) {
			\totalthemecore_init_class( 'Vcex\WPBakery' );
		}

		// WPML
		if ( \class_exists( 'SitePress', false ) ) {
			\totalthemecore_init_class( 'Vcex\WPML' );
		}
	}

}
