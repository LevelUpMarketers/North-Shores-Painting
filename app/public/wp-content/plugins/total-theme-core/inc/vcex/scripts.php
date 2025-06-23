<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Register scripts for use with vcex elements and enqueues global js.
 */
class Scripts {

	/**
	 * Holds array of scripts to register later.
	 */
	protected $scripts = [];

	/**
	 * Holds array of styles to register later.
	 */
	protected $styles = [];

	/**
	 * Instance.
	 */
	protected static $instance;

	/**
	 * Create or retrieve the instance of Scripts.
	 */
	public static function instance() {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init_hooks();
		}
		return self::$instance;
	}

	/**
	 * Add hooks.
	 *
	 * IMPORTANT: For WPBakery front-end editor to work scripts must be registered early
	 * before wp_enqueue_scripts with template_redirect being the latest hook that can be used.
	 */
	public function init_hooks() {
		\add_action( 'init', [ $this, 'register_scripts' ] );
	}

	/**
	 * Returns JS extension.
	 */
	public function get_js_extension() {
		\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
		return '.min.js';
	}

	/**
	 * Register scripts.
	 */
	public function register_scripts() {

		/* Justified Grid */
		\wp_register_script(
			'justifiedGallery',
			\vcex_get_js_file( 'vendor/jquery.justifiedGallery' ),
			[ 'jquery' ],
			'3.8.1',
			true
		);

		\wp_register_script(
			'vcex-justified-gallery',
			\vcex_get_js_file( 'frontend/justified-gallery' ),
			[ 'jquery', 'justifiedGallery' ],
			TTC_VERSION,
			true
		);

		\wp_register_style(
			'vcex-justified-gallery',
			\vcex_get_css_file( 'frontend/justified-gallery' ),
			[],
			TTC_VERSION
		);

		/* Isotope Scripts */
		\wp_register_script(
			'vcex-isotope-grids',
			\vcex_get_js_file( 'frontend/isotope-grids' ),
			[ 'jquery', 'isotope', 'imagesloaded' ],
			TTC_VERSION,
			true
		);

		\wp_localize_script(
			'vcex-isotope-grids',
			'vcex_isotope_params',
			\function_exists( '\wpex_get_masonry_settings' ) ? \wpex_get_masonry_settings() : []
		);

		/* Carousel Scripts */
		\totalthemecore_call_static( 'TotalThemeCore\Vcex\Carousel\Core', 'register_scripts' );

		/* Responsive Text */
		\wp_register_script(
			'vcex-responsive-text',
			\vcex_get_js_file( 'frontend/responsive-text' ),
			[],
			TTC_VERSION,
			true
		);

		/**
		 * Responsive CSS.
		 *
		 * @deprecated Soft deprecated in v1.3 in exchange for inline style tags, kept as fallback.
		 */
		\wp_register_script(
			'vcex-responsive-css',
			\vcex_get_js_file( 'frontend/responsive-css' ),
			[ 'jquery' ],
			TTC_VERSION,
			true
		);

		/* AJAX */
		totalthemecore_call_non_static( 'Vcex\Ajax', 'register_scripts' );

		/** Register scripts/styles added later */
		if ( $this->scripts ) {
			foreach ( $this->scripts as $args ) {
				\wp_register_script( ...$args );
			}
		}

		if ( $this->styles ) {
			foreach ( $this->styles as $args ) {
				\wp_register_style( ...$args );
			}
		}

		// Remove from memory after registration.
		$this->styles  = [];
		$this->scripts = [];
	}

	/**
	 * Add script to the registration array.
	 */
	public function register_script( array $args ) {
		$this->scripts[] = $args;
	}

	/**
	 * Add style to the registration array.
	 */
	public function register_style( array $args ) {
		$this->styles[] = $args;
	}

}
