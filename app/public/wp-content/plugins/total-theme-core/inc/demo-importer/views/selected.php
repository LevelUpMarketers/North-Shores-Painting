<?php

use TotalTheme\Demo_Importer\Helpers;

defined( 'ABSPATH' ) || exit;

if ( empty( $demo_data['name'] ) ) {
	return;
}

$can_install_plugins = current_user_can( 'install_plugins' );

?>

<div class="totaltheme-demo-importer-selected">

	<div class="totaltheme-demo-importer-selected__screenshot"><?php Helpers::render_demo_screenshot( $demo, $demo_data['name'] ); ?></div>
		
	<?php
	// If the current demo requires some plugins
	if ( ! empty( $demo_data['plugins'] ) ) {

		// Contains the HTML output for the plugins that need to be installed or activated
		$plugins_output = '';

		// Get the data of all the plugins that might be required for the demo
		$plugins_data = $this->plugin_installer->get_plugins_data();

		// Iterate through the list of plugin data and display plugins that are required.
		foreach ( $plugins_data as $plugin_data ) {
			$plugin_name = isset( $plugin_data['name'] ) ? sanitize_text_field( wp_unslash( (string) $plugin_data['name'] ) ) : '';

			if ( 'gutenberg' === strtolower( $plugin_name ) || ! in_array( $plugin_name, $demo_data['plugins'], true ) ) {
				continue; // plugin not in allowed list.
			}

			$plugin_slug = sanitize_text_field( wp_unslash( $plugin_data['slug'] ) );
			$user_action_link = '';

			// If the plugin is not installed/activated provide the possibility to install/activate it
			if ( ! $this->plugin_installer->is_plugin_installed( $plugin_slug ) ) {
				$user_action_link = '<a href="' . esc_url( admin_url( 'update.php' ) . '?action=install-plugin&plugin=' . $plugin_slug . '&_wpnonce=' . wp_create_nonce( 'totaltheme_demo_importer_install_plugin_' . $plugin_slug ) ) . '" class="totaltheme-demo-importer-install-plugin-btn button button-secondary">' . esc_html__( 'Install', 'total-theme-core' ) . '</a>';
			} elseif ( ! $this->plugin_installer->is_plugin_activated( $plugin_slug ) ) {
				$user_action_link = '<a href="' . esc_url( admin_url( 'plugins.php' ) . '?action=activate&plugin=' . $plugin_data['file_path'] . '&_wpnonce=' . wp_create_nonce( 'totaltheme_demo_importer_activate_plugin_' . $plugin_data['file_path'] ) ) . '" class="totaltheme-demo-importer-activate-plugin-btn button secondary-button">' . esc_html__( 'Activate', 'total-theme-core' ) . '</a>';
			}

			if ( $user_action_link ) {
				$plugins_output .= '<tr class="totaltheme-demo-importer-selected-plugins__item"><td>' . esc_html( $plugin_data['name'] ) . '</td><td class="totaltheme-plugin-action-result">' . $user_action_link . '</td></tr>';
			}
		}

		if ( ! empty( trim( $plugins_output ) ) && $can_install_plugins ) {
			?>
			<div class="totaltheme-demo-importer-selected-data">
				<div class="totaltheme-demo-importer-selected-plugins">
					<div class="totaltheme-demo-importer-selected__heading"><?php esc_html_e( 'Recommended Plugins', 'total-theme-core' ); ?></div>
					<div class="totaltheme-demo-importer-selected__desc"><?php esc_html_e( 'This demo uses the following plugins. We recommend activating the plugins so you get the complete demo content but it is completely optional.', 'total-theme-core' ); ?></div>
					<?php echo '<table class="totaltheme-demo-importer-selected-plugins__list"><tbody>' . $plugins_output . '</tbody></table>'; ?>
				</div>
				<div class="totaltheme-demo-importer-selected__buttons">
					<a href="#" class="button button-primary totaltheme-demo-importer-selected-next"><?php esc_html_e( 'Select Import Data', 'total-theme-core' ); ?><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><rect fill="none" height="24" width="24"/><path d="M15,5l-1.41,1.41L18.17,11H2V13h16.17l-4.59,4.59L15,19l7-7L15,5z"/></svg></a>
				</div>
			</div>
			<?php
		}
	}
	?>

	<form method="post" class="totaltheme-demo-importer-selected-form<?php echo $plugins_output ? ' hidden' : ''; ?>">
		<input type="hidden" name="demo" value="<?php echo esc_attr( $demo ); ?>">

		<?php if ( current_user_can( 'delete_posts' ) && Helpers::get_imported_data_list() ) { ?>
			<div class="totaltheme-demo-importer-selected__warning"><?php echo wp_kses_post( __( '<strong>Warning:</strong> Before importing a new demo, perhaps you will want to delete the previously imported data first? If so, please close this window and use the tool found at the top of the page.', 'total-theme-core' ) ); ?></div>
		<?php } ?>

		<div class="totaltheme-demo-importer-selected__heading"><?php esc_html_e( 'Data Selection', 'total-theme-core' ); ?></div>
		<div class="totaltheme-demo-importer-selected__desc"><?php esc_html_e( 'Please select what content you want to import:', 'total-theme-core' ); ?></div>

		<ul>
			<li>
				<label for="totaltheme_demo_importer_xml_check">
					<input id="totaltheme_demo_importer_xml_check" type="checkbox" name="totaltheme_demo_importer_xml_check" checked="checked">
					<strong><?php esc_html_e( 'Import XML Data', 'total-theme-core' ); ?></strong> (<?php esc_html_e( 'pages, posts, meta data, terms, menus, custom fonts, color palette, etc.', 'total-theme-core' ); ?>)
				</label>
			</li>

			<li>
				<label for="totaltheme_demo_importer_xml_attachments_check">
					<input id="totaltheme_demo_importer_xml_attachments_check" type="checkbox" name="totaltheme_demo_importer_xml_attachments_check" checked="checked">
					<strong><?php esc_html_e( 'Import Images', 'total-theme-core' ); ?></strong> <span class="totaltheme-red">(<?php esc_html_e( 'will be  much slower', 'total-theme-core' ); ?>)
				</label>
			</li>

			<li>
				<label for="totaltheme_demo_importer_mods_check">
					<input id="totaltheme_demo_importer_mods_check" type="checkbox" name="totaltheme_demo_importer_mods_check" checked="checked">
					<strong><?php esc_html_e( 'Import Customizer Settings', 'total-theme-core' ); ?></strong> <span class="totaltheme-red">(<?php esc_html_e( 'will reset all current settings', 'total-theme-core' ); ?>)</span>
				</label>
			</li>

			<li>
				<label for="totaltheme_demo_importer_widgets_check">
					<input id="totaltheme_demo_importer_widgets_check" type="checkbox" name="totaltheme_demo_importer_widgets_check" checked="checked">
					<strong><?php esc_html_e( 'Import Widgets', 'total-theme-core' ); ?></strong>
				</label>
			</li>

			<?php
			// Revslider.
			if ( isset( $demo_data['plugins'] ) && in_array( 'Slider Revolution', $demo_data['plugins'] ) ) :
				$mimes = get_allowed_mime_types();
				$allows_zip_uploads = ( is_array( $mimes ) && array_key_exists( 'zip', $mimes ) ) ?  true : false;
				?>

				<li>
					<label for="totaltheme_demo_importer_sliders_check">
						<input id="totaltheme_demo_importer_sliders_check" type="checkbox" name="totaltheme_demo_importer_sliders_check" <?php checked( $allows_zip_uploads, true ); ?> <?php if ( ! $allows_zip_uploads ) echo ' disabled="disabled"'; ?>>
						<strong><?php esc_html_e( 'Import Sliders', 'total-theme-core' ); ?></strong><?php if ( ! $allows_zip_uploads ) { echo ' <span class="totaltheme-red">(' . esc_html__( 'You must enable zip uploads for your WordPress install.', 'total-theme-core' ) . ')</span>'; } ?>
					</label>
				</li>

			<?php endif; ?>

		</ul>

		<?php wp_nonce_field( "totaltheme_demo_importer_import_{$demo}", "totaltheme_demo_importer_import_{$demo}_nonce" ); ?>

		<div class="totaltheme-demo-importer-selected__buttons">
			<button type="submit" name="submit" class="button button-primary totaltheme-demo-importer-selected-confirm" data-confirm="<?php esc_attr_e( 'Please confirm that you want to import the selected demo content.', 'total-theme-core' ); ?>"><?php esc_html_e( 'Start Import', 'total-theme-core' ); ?></button>
		</div>

	</form>

	<div class="totaltheme-demo-importer-selected-loading hidden">
		<div class="totaltheme-demo-importer-selected__warning"><?php esc_html_e( 'The Import process could take some time, so please be patient.', 'total-theme-core' ); ?></div>
		<div class="totaltheme-demo-importer-selected-status"></div>
		<div class="totaltheme-demo-importer-selected__buttons totaltheme-demo-importer-retry hidden">
			<button type="submit" name="submit" class="button button-secondary totaltheme-demo-importer-abort"><?php esc_html_e( 'Abort', 'total-theme-core' ); ?></button>
			<button type="submit" name="submit" class="button button-primary totaltheme-demo-importer-retry-btn"><?php esc_html_e( 'Try Finishing Import', 'total-theme-core' ); ?></button>
		</div>
	</div>

	<div class="totaltheme-demo-importer-selected-complete hidden">
		<div class="totaltheme-demo-importer-selected-complete__message"><?php esc_html_e( 'Import Complete', 'total-theme-core' ); ?> <span class="dashicons dashicons-yes-alt" aria-hidden="true"></span></div>
		<div class="totaltheme-demo-importer-selected__buttons">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'View Site', 'total-theme-core' ); ?><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><rect fill="none" height="24" width="24"/><path d="M9,5v2h6.59L4,18.59L5.41,20L17,8.41V15h2V5H9z"/></svg></a>
		</div>
	</div>

</div>