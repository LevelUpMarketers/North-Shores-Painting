<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Creates the admin panel for the customizer.
 */
class Import_Export {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( ! \get_theme_mod( 'import_export_enable', true ) ) {
			return;
		}

		\add_action( 'admin_menu', [ self::class, 'on_admin_menu' ], 9999 );
	}

	/**
	 * Runs on the "admin_menu" hook.
	 */
	public static function on_admin_menu() {
		$hook_suffix = \add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_attr__( 'Import/Export', 'total' ),
			\esc_attr__( 'Import/Export', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG . '-import-export',
			[ self::class, 'render_admin_page' ]
		);

		add_action( "admin_print_scripts-{$hook_suffix}", [ self::class, 'enqueue_scripts' ] );
	}

	/**
	 * Register scripts.
	 */
	public static function enqueue_scripts() {
		\wp_enqueue_script(
			'totaltheme-admin-import-export',
			\totaltheme_get_js_file( 'admin/import-export' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION,
			true
		);
		\wp_localize_script( 'totaltheme-admin-import-export', 'totaltheme_admin_import_export_vars', [
			'confirmReset'  => esc_html__( 'Confirm Reset', 'total' ),
			'importOptions' => esc_html__( 'Import Options', 'total' ),
		] );
	}

	/**
	 * Perform actions.
	 */
	private static function perform_actions() {
		if ( ! isset( $_POST['totaltheme-admin-import-export-nonce'] )
			|| ! wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-admin-import-export-nonce'] ) ), 'totaltheme-admin-import-export' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		$form = $_POST['totaltheme_import_export'] ?? [];

		if ( ! is_array( $form ) ) {
			return;
		}

		// Delete options if import set to -1
		if ( isset( $form['reset'] ) && '-1' == $form['reset'] ) {

			// Get menu locations
			$locations 	= \get_theme_mod( 'nav_menu_locations' );
			$save_menus	= [];

			if ( $locations ) {
				foreach ( $locations as $key => $val ) {
					$save_menus[ $key ] = $val;
				}
			}

			// Get sidebars
			$widget_areas = \get_theme_mod( 'widget_areas' );

			// Remove all mods
			$bk = self::backup_mods();

			if ( $bk ) {

				\remove_theme_mods();

				// WP fix. Logo doesn't get removed with \remove_theme_mods();
				\set_theme_mod( 'custom_logo', '' );
				\remove_theme_mod( 'custom_logo' );

				// Re-add the menus
				\set_theme_mod( 'nav_menu_locations', array_map( 'absint', $save_menus ) );
				\set_theme_mod( 'widget_areas', $widget_areas );

				// Error messages
				$error_msg	= \esc_attr__( 'All settings have been reset.', 'total' );
				$error_type	= 'updated';
			} else {
				$error_msg	= \esc_attr__( 'Error creating backup.', 'total' );
				$error_type	= 'error';
			}

		}

		// Set theme mods based on json data.
		elseif ( ! empty( $form['import'] ) ) {

			// Decode input data.
			$theme_mods = \json_decode( \stripslashes_deep( $form['import'] ), true );

			// Validate json file then set new theme options.
			if ( \function_exists( 'json_last_error' ) && \defined( 'JSON_ERROR_NONE' ) ) {
				if ( JSON_ERROR_NONE === \json_last_error() ) {
					// Loop through mods and add them.
					foreach ( $theme_mods as $theme_mod => $value ) {
						\set_theme_mod( $theme_mod, $value );
					}

					// Success message.
					$error_msg  = \esc_attr__( 'Settings imported successfully.', 'total' );
					$error_type = 'updated';
				}

				// Display invalid json data error.
				else {
					$error_msg  = \esc_attr__( 'Invalid Import Data.', 'total' );
					$error_type = 'error';
				}

			} else {
				$error_msg  = \esc_attr__( 'The version of PHP on your server is very outdated and can not support a proper import. Please make sure your server has been updated to the WordPress "supported" version of PHP.', 'total' );
				$error_type = 'error';
			}

		}

		// No json data entered.
		else {
			$error_msg = \esc_attr__( 'No import data found.', 'total' );
			$error_type = 'error';
		}

		if ( $error_msg ) {
			echo "<div class='notice {$error_type}'><p>{$error_msg}</p></div>";
		}
	}

	/**
	 * Settings page output.
	 */
	public static function render_admin_page() {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		if ( isset( $_GET['totaltheme_restore_mods'] ) && empty( $_POST['totaltheme-admin-import-export-nonce'] ) ) {
			if ( \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_GET['totaltheme_restore_mods'] ) ), 'totaltheme_restore_mods_nonce' ) ) {
				$mods_bk = \get_transient( 'totaltheme_mods_bk' );
				if ( is_array( $mods_bk ) ) {
					foreach ( $mods_bk as $mod => $val ) {
						\set_theme_mod( $mod, $val );
					}
					\delete_transient( 'totaltheme_mods_bk' );
					echo "<div class='notice updated'><p>" . \esc_attr__( 'Settings have been restored.', 'total' ) . "</p></div>";
				}
			}
		}

		if ( isset( $_POST['totaltheme_import_export'] ) ) {
			self::perform_actions();
		}

		?>

		<div class="wpex-theme-import-export wrap">

			<?php
			// Need to insert h1 for notices.
			echo '<h1 style="display:none;" aria-hidden="true"></h1>'; ?>

			<div class="notice notice-warning"><p><?php \esc_html_e( 'This will export, import, or delete all theme mods, meaning that if other plugins have added settings to the Customizer, those will also be exported, imported, or deleted.', 'total' ); ?></p></div>

			<form method="post">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php \esc_html_e( 'Export Settings', 'total' ); ?></th>
						<td>
							<?php if ( $theme_mods = (array) \get_theme_mods() ) {
								foreach ( $theme_mods as $k => $v ) {
									$theme_mods[ $k ] = \maybe_unserialize( $v );
								}
								$json = \json_encode( $theme_mods );
								$disabled = '';
							} else {
								$json     = \esc_attr__( 'No Settings Found', 'total' );
								$disabled = ' disabled';
							}
							echo '<textarea class="wpex-theme-import-export__settings" rows="10" cols="50" readonly style="width:100%;">' . $json . '</textarea>'; ?>
							<p class="submit">
								<a href="#" class="wpex-theme-import-export__highlight button-primary<?php echo \esc_attr( $disabled ); ?>"><?php \esc_html_e( 'Highlight Options', 'total' ); ?></a>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php \esc_html_e( 'Import Settings', 'total' ); ?></th>
						<td>
							<textarea name="totaltheme_import_export[import]" rows="10" cols="50" style="width:100%;"></textarea>
							<input class="wpex-theme-import-export__reset" name="totaltheme_import_export[reset]" type="hidden" value=""></input>
							<p class="submit">
								<input type="submit" class="wpex-theme-import-export__submit button-primary" value="<?php \esc_attr_e( 'Import Options', 'total' ) ?>">
								<a href="#" class="wpex-theme-import-export__delete button-secondary"><?php \esc_html_e( 'Reset Options', 'total' ); ?></a>
								<a href="#" class="wpex-theme-import-export__delete-cancel button-secondary" style="display:none;"><?php \esc_html_e( 'Cancel Reset', 'total' ); ?></a>
							</p>
							<div class="wpex-theme-import-export__warning error inline" style="display:none;">
								<p style="margin:.5em 0;"><?php \esc_attr_e( 'The theme will create a backup of your settings, which will be available for 24 hours. However, it\'s strongly recommended to ensure you always have a full server backup, just in case! Your menu locations and widget areas will remain intact and won\'t be reset. Only Customizer and addon settings will be reset.', 'total' ); ?></p>
							</div>
						</td>
					</tr>
				</table>
				<?php \wp_nonce_field( 'totaltheme-admin-import-export', 'totaltheme-admin-import-export-nonce' ); ?>
			</form>
		</div>

		<?php
		$bk = \get_transient( 'totaltheme_mods_bk' );
		if ( $bk ) {
			?>
			<div class="wpex-theme-import-export-backup">
				<h2><?php \esc_html_e( 'Backup', 'total' ); ?></h2>
				<p><?php \esc_html_e( 'Your theme settings were recently reset, and a backup has been saved in case you wish to revert. This backup will be available for one day from the reset time.', 'total' ); ?></p>
				<p><a href="<?php echo \esc_url( \add_query_arg( 'totaltheme_restore_mods', \wp_create_nonce( 'totaltheme_restore_mods_nonce' ) ) ); ?>" class="button button-secondary"><?php \esc_html_e( 'Restore', 'total' ); ?></a></p>
			</div>
			<?php
		}
	}
	
	/**
	 * Backup mods.
	 */
	private static function backup_mods(): bool {
		return \set_transient( 'totaltheme_mods_bk', \get_theme_mods(), DAY_IN_SECONDS );
	}

}
