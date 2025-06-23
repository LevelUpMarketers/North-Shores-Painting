<?php

namespace TotalTheme\Customizer;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Manager.
 */
if ( \class_exists( '\TotalTheme\Customizer' ) ) {
	class Manager extends \TotalTheme\Customizer {

		/**
		 * Start things up.
		 */
		public function __construct() {
			\add_action( 'admin_menu', [ $this, 'on_admin_menu' ], 40 );
		}

		/**
		 * Add sub menu page for the custom CSS input.
		 */
		public function on_admin_menu() {
			$hook_suffix = add_submenu_page(
				WPEX_THEME_PANEL_SLUG,
				esc_html__( 'Customizer Manager', 'total' ),
				esc_html__( 'Customizer Manager', 'total' ),
				'administrator',
				WPEX_THEME_PANEL_SLUG . '-customizer',
				array( $this, 'render_admin_page' )
			);

			\add_action( "load-{$hook_suffix}", [ $this, 'admin_help_tab' ] );
		}

		/**
		 * Add admin help tab.
		 */
		public function admin_help_tab() {
			$screen = \get_current_screen();

			if ( ! $screen ) {
				return;
			}

			$screen->add_help_tab(
				[
					'id'      => 'totaltheme_customizer_manager',
					'title'   => \esc_html__( 'Overview', 'total' ),
					'content' => '<p>' . esc_html__( 'Disable sections in the Customizer that you no longer need. It will NOT alter any options already set in the Customizer or disable sections visible on the front-end of your site.', 'total' ) . '</p>'
				]
			);
		}

		/**
		 * Save disbled panels.
		 */
		protected function save_disabled_panels_list( $option ) {
			$excluded_panels = [];
			$panels = $this->panels();
			foreach ( $panels as $id => $val ) {
				if ( ! isset( $option[$id] ) ) {
					$excluded_panels[] = $id;
				}
			}
			if ( $excluded_panels ) {
				update_option( 'wpex_disabled_customizer_panels', $excluded_panels, false );
			} else {
				delete_option( 'wpex_disabled_customizer_panels' );
			}
		}

		/**
		 * Settings page output.
		 *
		 */
		public function render_admin_page() {
			if ( ! \current_user_can( 'administrator' ) ) {
				return;
			}

			\wp_enqueue_style( 'wpex-admin-pages' );
			\wp_enqueue_script( 'wpex-admin-pages' );

			// This is where we save our options.
			if ( isset( $_POST['totaltheme-customizer-manager-nonce'] )
				&& \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-customizer-manager-nonce'] ) ), 'totaltheme-customizer-manager' ) ) {
				$this->save_disabled_panels_list( $_POST['wpex_disabled_customizer_panels'] ?? [] );
			}

			?>

			<div id="wpex-customizer-manager-admin-page" class="wrap">

				<h2 class="nav-tab-wrapper">
					<a href="#" class="nav-tab nav-tab-active"><?php esc_html_e( 'Panels', 'total' ); ?></a>
					<a href="<?php echo \esc_url( \admin_url( 'customize.php' ) ); ?>" class="nav-tab"><?php \esc_html_e( 'Customizer', 'total' ); ?> <span class="dashicons dashicons-external"></span></a>
				</h2>

				<div class="wpex-check-uncheck">
					<a href="#" class="wpex-customizer-check-all"><?php \esc_html_e( 'Check all', 'total' ); ?></a> | <a href="#" class="wpex-customizer-uncheck-all"><?php \esc_html_e( 'Uncheck all', 'total' ); ?></a>
				</div>

				<form method="post">
					<table class="form-table wpex-customizer-editor-table">
						<?php
						// Get panels.
						$panels = $this->panels();

						// Get disabled panels.
						$disabled_panels = \get_option( 'wpex_disabled_customizer_panels' ) ?: array();

						// Loop through panels and add checkbox
						foreach ( $panels as $id => $val ) {
							$title     = $val['title'] ?? $val;
							$condition = $val['condition'] ?? true;

							// Check if option should be hidden
							$is_hidden = isset( $val['condition'] ) && ! call_user_func( $val['condition'] ) ? true : false;

							// Check if a given section is enabled
							$is_enabled = \wpex_has_customizer_panel( $id ) ? 'on' : '';
							?>

							<tr valign="top"<?php if ( $is_hidden ) echo ' style="display:none;"'; ?>>
								<th scope="row"><label for="wpex_disabled_customizer_panels[<?php echo \esc_attr( $id ); ?>]"><?php echo \esc_html( $title ); ?></label></th>
									<td>
									<?php
									// Condition isn't met so add setting as a hidden item
									if ( $is_hidden ) { ?>
										<input type="hidden" id="wpex_disabled_customizer_panels[<?php echo \sc_attr( $id ); ?>]" name="wpex_disabled_customizer_panels[<?php echo \esc_attr( $id ); ?>]"<?php \checked( $is_enabled, 'on' ); ?>>
									<?php }
									// Display setting
									else { ?>
										<input class="wpex-customizer-editor-checkbox" type="checkbox" id="wpex_disabled_customizer_panels[<?php echo \esc_attr( $id ); ?>]" name="wpex_disabled_customizer_panels[<?php echo \esc_attr( $id ); ?>]"<?php \checked( $is_enabled, 'on' ); ?>>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</table>
					<?php \wp_nonce_field( 'totaltheme-customizer-manager', 'totaltheme-customizer-manager-nonce' ); ?>
					<?php \submit_button(); ?>
				</form>
			</div>
		<?php }

	}
}
