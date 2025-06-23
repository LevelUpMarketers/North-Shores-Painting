<?php

namespace TotalThemeCore;

use TotalThemeCore\WPBakery\Helpers as WPB_Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * Templatera integration.
 */
final class Templatera {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		// Enables the Templatera frontend editor.
		self::enable_frontend_editor();
	}

	/**
	 * Enables the frontend editor for Templatera.
	 */
	private static function enable_frontend_editor(): void {
		if ( \is_admin() ) {
			\add_action( 'admin_print_footer_scripts', [ self::class, 'add_editor_button' ], 1000 );
			\add_filter( 'vc_show_button_fe', [ self::class, 'vc_show_button_fe' ], 10, 3 );
		}

		// We only have to register templatera when dealing with the front-end editor.
		// Needs to run on init and admin_init so the single-templatera.php template works.
		if ( \class_exists( '\TotalThemeCore\WPBakery\Helpers' ) && WPB_Helpers::is_frontend_edit_mode() ) {
			\add_filter( 'register_post_type_args', [ self::class, 'filter_post_type_args' ], 10, 2 );
			\add_action( 'init', [ self::class, 'enable_editor' ], 8 ); // note: must use same priority as templatera.
		}
	}

	/**
	 * Adds the front-end editor button.
	 */
	public static function add_editor_button() {
		if ( ! \function_exists( 'vc_frontend_editor' ) || ! \function_exists( 'templatera_init' ) ) {
			return;
		}
		global $pagenow;
		if ( 'post.php' !== $pagenow
			|| ! isset( $_GET['post'] )
			|| 'templatera' !== \get_post_type( \sanitize_text_field( \wp_unslash( $_GET['post'] ) ) )
		) {
			return;
		}
		$front_end_url = \vc_frontend_editor()->getInlineUrl();
		?>
		<script>
			( function ( $ ) {
				if ( typeof vc !== 'undefined' ) {
					vc.events.on( 'vc:access:backend:ready', function ( access ) {
						var vcSwitch = $( '.composer-inner-switch' );
						if ( vcSwitch.length ) {
							vcSwitch.append( '<a class="wpb_switch-to-front-composer" href="<?php echo esc_url( $front_end_url ); ?>">' + window.i18nLocale.main_button_title_frontend_editor + '</a>' );
						}
					} );
				}
			} ) ( window.jQuery );
		</script>
		<?php
	}

	/**
	 * Enable front-end editor.
	 */
	public static function enable_editor() {
		if ( 'templatera' === WPB_Helpers::get_admin_post_type() ) {
			\add_filter( 'vc_role_access_with_frontend_editor_get_state', '__return_true' );
		}
	}

	/**
	 * Templatera post type args.
	 */
	public static function filter_post_type_args( $args, $post_type ) {
		if ( 'templatera' === $post_type ) {
			$args['public']             = true;
			$args['publicly_queryable'] = true;
			$args['map_meta_cap']       = true;
		}
		return $args;
	}

	/**
	 * Enable hover edit with WPBakery link in wp admin.
	 */
	public static function vc_show_button_fe( $check, $post_id, $post_type ) {
		if ( 'templatera' === $post_type ) {
			$check = true;
		}
		return $check;
	}

}
