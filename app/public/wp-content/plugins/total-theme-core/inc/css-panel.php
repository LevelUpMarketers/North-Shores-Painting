<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom CSS Admin Panel.
 */
final class CSS_Panel {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		\add_action( 'admin_menu', [ self::class, 'on_admin_menu' ], 20 );
		\add_action( 'admin_bar_menu', [ self::class, 'on_admin_bar_menu' ], 1000 );
	}

	/**
	 * Add sub menu page for the custom CSS input.
	 */
	public static function on_admin_menu() {
		$slug = defined( 'WPEX_THEME_PANEL_SLUG' ) ? \WPEX_THEME_PANEL_SLUG : 'themes.php';
		$hook_suffix = \add_submenu_page(
			$slug,
			\esc_html__( 'Custom CSS', 'total-theme-core' ),
			\esc_html__( 'Custom CSS', 'total-theme-core' ),
			'edit_theme_options',
			"{$slug}-custom-css",
			[ self::class, 'render_admin_page' ]
		);
	}

	/**
	 * Add custom CSS to the adminbar since it will be used frequently.
	 */
	public static function on_admin_bar_menu( $wp_admin_bar ) {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$slug = \defined( 'WPEX_THEME_PANEL_SLUG' ) ? \WPEX_THEME_PANEL_SLUG : 'themes.php';

		$wp_admin_bar->add_node( [
			'id'    => 'totaltheme_css_panel',
			'title' => \esc_html__( 'Custom CSS', 'total-theme-core' ),
			'href'  => \esc_url( \admin_url( "admin.php?page={$slug}-custom-css" ) ),
			'meta'  => [
				'class' => 'wpex-custom-css',
			]
		] );
	}

	/**
	 * Maybe save the custom CSS.
	 */
	private static function maybe_save_css(): void {
		if ( ! isset( $_POST['totaltheme_css_panel'] )
			|| ! isset( $_POST['totalthemecore-css-panel-nonce'] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totalthemecore-css-panel-nonce'] ) ), 'totalthemecore-css-panel' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		$option  = \wp_unslash( $_POST['totaltheme_css_panel'] );
		$old_css = \get_theme_mod( 'custom_css', null );

		// Sanitize and save theme mod.
		if ( ! empty( $option ) ) {
			if ( \function_exists( 'wp_update_custom_css_post' ) && ! $old_css ) {
				$updated = \wp_update_custom_css_post( $option );
			} else {
				$updated = \set_theme_mod( 'custom_css', \wp_strip_all_tags( $option ) );
			}
		} else {
			if ( \function_exists( 'wp_update_custom_css_post' ) && ! $old_css ) {
				\wp_update_custom_css_post( '' );
			} else {
				\remove_theme_mod( 'custom_css' );
			}
		}

		// CSS couldn't be updated.
		if ( isset( $updated ) && ( \is_wp_error( $updated ) || false === $updated ) ) {
			echo '<div class="notice notice-error"><p>' . \esc_html__( 'Error: Your CSS couldn\'t be saved, please try again.', 'total' ) . '</p></div>';
			\update_option( 'totaltheme_css_panel_backup', \wp_strip_all_tags( $option ), false );
		} else {
			\delete_option( 'totaltheme_css_panel_backup' );
		}
	}

	/**
	 * Settings page output.
	 */
	public static function render_admin_page() {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		self::maybe_save_css();

		$old_css = \get_theme_mod( 'custom_css', null );

		if ( \function_exists( 'wp_get_custom_css' ) && ! $old_css ) {
			$custom_css = \get_option( 'totaltheme_css_panel_backup' ) ?: \wp_get_custom_css();
		} else {
			$custom_css = \get_theme_mod( 'custom_css', null );
		}

		self::enqueue_scripts();
		?>

		<div class="wrap wpex-custom-css-panel-wrap">
			<div class="wpex-remember-to-save">
				<p><?php echo \wp_kses_post( \__( 'Don\'t forget to <a href="#">save your changes</a>', 'total-theme-core' ) ); ?></p>
			</div>
			<div style="margin-block-start:20px;">
				<form method="post" action="">
					<table class="form-table">
						<tr valign="top">
							<td style="padding:0;">
								<textarea cols="70" rows="30" id="totaltheme_css_panel" name="totaltheme_css_panel"><?php echo \wp_strip_all_tags( $custom_css ); ?></textarea>
							</td>
						</tr>
					</table>
					<?php \wp_nonce_field( 'totalthemecore-css-panel', 'totalthemecore-css-panel-nonce' ); ?>
					<?php \submit_button(); ?>
				</form>
			</div>
		</div>
	<?php }

	/**
	 * CSS Panel scripts.
	 */
	private static function enqueue_scripts() {
		\wp_enqueue_style( 'totaltheme-admin-pages' );
		\wp_enqueue_script( 'totaltheme-admin-pages' );

		$settings = \wp_enqueue_code_editor( [
			'type' => 'text/css'
		] );

		if ( false === $settings ) {
			return; // code mirror is disabled.
		}

		\wp_add_inline_script(
			'code-editor',
			\sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "totaltheme_css_panel", %s ); } );',
				\wp_json_encode( $settings )
			)
		);
	}

}
