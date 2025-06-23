<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery.
 */
final class WPBakery {

	/**
	 * Init.
	 */
	public static function init() {
		\add_action( 'vc_load_default_params', [ self::class, 'register_params' ] );

		if ( \is_admin() || \totalthemecore_call_static( 'WPBakery\Helpers', 'is_frontend_edit_mode' ) ) {
			\add_action( 'vc_base_register_admin_css', [ self::class, 'register_editor_css' ] );
			\add_action( 'vc_backend_editor_enqueue_js_css', [ self::class, 'enqueue_editor_scripts' ] );
			\add_action( 'vc_frontend_editor_enqueue_js_css', [ self::class, 'enqueue_editor_scripts' ] );
			\add_action( 'admin_footer', [ self::class, '_insert_icon_modal' ] );
			\add_action( 'wp_ajax_vcex_params', [ self::class, '_vcex_params_ajax_callback' ] );
		}
	}

	/**
	 * Registers custom params.
	 */
	public static function register_params(): void {
		if ( \function_exists( '\vc_add_shortcode_param' ) ) {
			require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/add-params.php';
		}
	}

	/**
	 * Register CSS editor scripts.
	 */
	public static function register_editor_css(): void {
		\wp_register_style(
			'totalthemecore-admin-wpbakery-params',
			\totalthemecore_get_css_file( 'admin/wpbakery/params' ),
			[],
			TTC_VERSION
		);
	}

	/**
	 * Enqueue editor scripts.
	 */
	public static function enqueue_editor_scripts(): void {
		\wp_enqueue_style( 'wpex-chosen' );
		\wp_enqueue_script( 'wpex-chosen' );

		if ( \function_exists( '\totaltheme_call_static' ) ) {
			\totaltheme_call_static( 'Helpers\Icon_Select', 'enqueue_scripts' );
		}

		\wp_enqueue_style( 'totaltheme-components' );
		\wp_enqueue_script( 'totaltheme-components' );

		// Must load after chosen for overrides to work.
		\wp_enqueue_style( 'totalthemecore-admin-wpbakery-params' );

		\wp_enqueue_script(
			'totalthemecore-admin-wpbakery-vc-atts',
			\totalthemecore_get_js_file( 'admin/wpbakery/vc-atts' ),
			[ 'jquery' ],
			TTC_VERSION,
			true
		);
	}

	/**
	 * Inserts the icon select modal into the page for use with the vcex_select_icon param type.
	 */
	public static function _insert_icon_modal(): void {
		if ( ( did_action( 'vc_backend_editor_render' ) || did_action( 'vc_frontend_editor_render' ) )
			&& \function_exists( 'totaltheme_call_static' )
		) {
			totaltheme_call_static( 'Helpers\Icon_Select', 'render_modal' );
		}
	}

	/**
	 * Ajax callback for vcex params.
	 */
	public static function _vcex_params_ajax_callback() {
		\check_ajax_referer( 'vcex_params_ajax', 'nonce' );

		if ( empty( $_POST['action'] ) ) {
			return;
		}
		
		$task = \sanitize_text_field( \wp_unslash( $_POST['task'] ) );
		$data = [];

		if ( 'refresh_choices' === $task ) {
			$choices = $_POST['choices'] ?? '';
			if ( $choices ) {
				if ( \class_exists( '\TotalThemeCore\Vcex\Setting_Choices' ) ) {
					$get_choices = (new \TotalThemeCore\Vcex\Setting_Choices( \sanitize_text_field( \wp_unslash( $choices ) )))->get_choices();
					foreach ( $get_choices as $key => $value ) {
						$data[] = [ $key => $value ]; // maintain order for js.
					}
				}
			}
		}

		\wp_send_json( $data );
	}

	/**
	 * Add editor form scripts.
	 */
	public static function add_editor_form_scripts(): void {
		\_deprecated_function( __METHOD__, 'Total Theme Core 1.8.7' );
	}

}
