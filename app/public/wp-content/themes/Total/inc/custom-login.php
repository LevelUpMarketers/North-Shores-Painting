<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Login Page Design.
 */
class Custom_Login {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( is_admin() ) {
			self::admin_actions();
		}

		self::frontend_actions();
	}

	/**
	 * Admin hooks.
	 */
	public static function admin_actions() {
		\add_action( 'admin_menu', [ self::class, 'add_submenu_page' ] );
	}

	/**
	 * Frontend hooks.
	 */
	public static function frontend_actions() {
		if ( ! self::is_interim_login() ) {
			\add_action( 'login_head', [ self::class, 'on_login_head' ] );
			\add_action( 'login_header', [ self::class, 'on_login_header' ], 0 );
			\add_action( 'login_footer', [ self::class, 'on_login_footer' ], PHP_INT_MAX );
			\add_action( 'login_headerurl', [ self::class, 'filter_login_headerurl' ] );
			\add_filter( 'login_headertext',[ self::class, 'filter_login_headertext' ] );
			
			if ( 'off' === self::get_option( 'language_switcher', 'on' ) ) {
				\add_filter( 'login_display_language_dropdown', '__return_false' );
			}
		}
	}

	/**
	 * Add sub menu page.
	 */
	public static function add_submenu_page() {
		\add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_html__( 'Custom Login', 'total' ),
			\esc_html__( 'Custom Login', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG . '-admin-login',
			[ self::class, 'render_admin_page' ]
		);
	}

	/**
	 * Enque admin page scripts.
	 */
	public static function enqueue_admin_scripts(): void {
		\wp_enqueue_media();

		\wp_enqueue_style( 'totaltheme-components' );
		\wp_enqueue_script( 'totaltheme-components' );

		\wp_enqueue_style( 'totaltheme-admin-pages' );
		\wp_enqueue_script( 'totaltheme-admin-pages' );
	}

	/**
	 * Save options.
	 */
	private static function maybe_save_options(): void {
		if ( ! isset( $_POST['totaltheme_custom_login'] )
			|| ! isset( $_POST['totaltheme-custom-login-admin-nonce'] )
			|| ! wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-custom-login-admin-nonce'] ) ), 'totaltheme-custom-login-admin' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		$options = (array) \wp_unslash( $_POST['totaltheme_custom_login'] );

		foreach ( $options as $key => $val ) {
			if ( empty( $val ) ) {
				unset( $options[ $key ] );
				// Remove related settings that aren't needed.
				if ( 'background_img' === $key ) {
					unset( $options['background_style'] );
				} elseif ( 'logo' === $key ) {
					unset( $options['logo_height'] );
				}
			}
		}

		if ( ! isset( $options['language_switcher'] ) ) {
			$options['language_switcher'] = 'off';
		}

		// Sanitize settings before saving.
		$options_safe = array_map( 'sanitize_text_field', $options );

		\set_theme_mod( 'login_page_design', $options_safe );
	}

	/**
	 * Settings page output.
	 */
	public static function render_admin_page(): void {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		if ( ! function_exists( 'is_login' ) ) {
			echo '<div class="notice notice-error" style="margin-top:20px;"><p>' . \esc_html__( 'The Custom Login functionality requires WordPress 6.1 or greater.', 'total' ) . '</p></div>';
			return;
		}

		self::maybe_save_options();
		self::enqueue_admin_scripts();
		?>

		<div class="wrap">

			<h2 class="nav-tab-wrapper wpex-panel-js-tabs">
				<a href="#main" class="nav-tab nav-tab-active"><?php \esc_html_e( 'Main', 'total' ); ?></a>
				<a href="#logo" class="nav-tab"><?php \esc_html_e( 'Logo', 'total' ); ?></a>
				<a href="#background" class="nav-tab"><?php \esc_html_e( 'Background', 'total' ); ?></a>
				<a href="#form" class="nav-tab"><?php \esc_html_e( 'Form', 'total' ); ?></a>
				<a href="#button" class="nav-tab"><?php \esc_html_e( 'Button', 'total' ); ?></a>
				<a href="#bottom-links" class="nav-tab"><?php \esc_html_e( 'Bottom Links', 'total' ); ?></a>
			</h2>

			<?php $theme_mod = self::get_options_list(); ?>

			<form method="post" action="">

				<table class="form-table wpex-tabs-wrapper">

					<tr valign="top" class="wpex-tab-content wpex-main">
						<th scope="row"><label for="totaltheme_custom_login-enabled"><?php \esc_html_e( 'Enable', 'total' ); ?></label></th>
						<td>
							<?php $enabled = $theme_mod['enabled'] ?? ''; ?>
							<span class="totaltheme-admin-checkbox">
								<input id="totaltheme_custom_login-enabled" type="checkbox" name="totaltheme_custom_login[enabled]" <?php \checked( $enabled, 'on' ); ?>>
								<span class="totaltheme-admin-checkbox__track"></span>
								<span class="totaltheme-admin-checkbox__thumb"></span>
							</span>
						</td>
					</tr>

					<?php if ( ! empty( get_available_languages() ) ) { ?>
						<tr valign="top" class="wpex-tab-content wpex-main">
							<th scope="row"><label for="totaltheme_custom_login-language_switcher"><?php \esc_html_e( 'Language Switcher', 'total' ); ?></label></th>
							<td>
								<?php $enabled = $theme_mod['language_switcher'] ?? 'on'; ?>
								<span class="totaltheme-admin-checkbox">
									<input id="totaltheme_custom_login-language_switcher" type="checkbox" name="totaltheme_custom_login[language_switcher]" <?php \checked( $enabled, 'on' ); ?>>
									<span class="totaltheme-admin-checkbox__track"></span>
									<span class="totaltheme-admin-checkbox__thumb"></span>
								</span>
								<p class="description"><?php \esc_html_e( 'Can be used to disable the WordPress Language switcher.', 'total' ); ?></p>
							</td>
						</tr>
					<?php } ?>

					<tr valign="top" class="wpex-tab-content wpex-main">
						<th scope="row"><label for="totaltheme_custom_login-center"><?php \esc_html_e( 'Center', 'total' ); ?></label></th>
						<td>
							<?php $enabled = $theme_mod['center'] ?? ''; ?>
							<span class="totaltheme-admin-checkbox">
								<input id="totaltheme_custom_login-center" type="checkbox" name="totaltheme_custom_login[center]" <?php \checked( $enabled, 'on' ); ?>>
								<span class="totaltheme-admin-checkbox__track"></span>
								<span class="totaltheme-admin-checkbox__thumb"></span>
							</span>
							<p class="description"><?php \esc_html_e( 'Will vertically center the login form on screens greater then 768px.', 'total' ); ?></p>
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-main">
						<th scope="row"><label for="totaltheme_custom_login-width"><?php \esc_html_e( 'Max Width', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['width'] ?? ''; ?>
							<input id="totaltheme_custom_login-width" type="text" name="totaltheme_custom_login[width]" value="<?php echo \esc_attr( $option ); ?>" placeholder="320px">
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-main">
						<th scope="row"><label for="totaltheme_custom_login-form_top"><?php \esc_html_e( 'Top Margin', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['form_top'] ?? ''; ?>
							<input id="totaltheme_custom_login-form_top" type="text" name="totaltheme_custom_login[form_top]" value="<?php echo \esc_attr( $option ); ?>" placeholder="5%">
							<p class="description"><?php \esc_html_e( 'It\'s recommended to use a percentage or viewport based unit to ensure it looks good on small screens.', 'total' ); ?></p>
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-logo">
						<th scope="row"><label for="totaltheme_custom_login-logo"><?php \esc_html_e( 'Logo', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['logo'] ?? ''; ?>
							<input id="totaltheme_custom_login-logo" class="wpex-media-input" type="text" name="totaltheme_custom_login[logo]" value="<?php echo \esc_attr( $option ); ?>">
							<button class="wpex-media-upload-button button-primary" type="button"><?php \esc_attr_e( 'Select', 'total' ); ?></button>
							<button class="wpex-media-remove button-secondary" type="button"><?php \esc_html_e( 'Remove', 'total' ); ?></button>
							<?php $preview = wpex_get_image_url( $option ); ?>
							<div class="wpex-media-live-preview" style="width:320px">
								<?php if ( $preview ) { ?>
									<img src="<?php echo \esc_url( $preview ); ?>" alt="<?php \esc_html_e( 'Preview Image', 'total' ); ?>">
								<?php } ?>
							</div>
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-logo">
						<th scope="row"><label for="totaltheme_custom_login-logo_height"><?php \esc_html_e( 'Logo Height', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['logo_height'] ?? ''; ?>
							<input id="totaltheme_custom_login-logo_height" type="number" name="totaltheme_custom_login[logo_height]" value="<?php echo \esc_attr( $option ); ?>" placeholder="84px">
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-logo">
						<th scope="row"><label for="totaltheme_custom_login-logo_url"><?php \esc_html_e( 'Custom Logo URL', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['logo_url'] ?? ''; ?>
							<input id="totaltheme_custom_login-logo_url" type="text" name="totaltheme_custom_login[logo_url]" value="<?php echo \esc_attr( $option ); ?>">
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-logo">
						<th scope="row"><label for="totaltheme_custom_login-logo_url_title"><?php \esc_html_e( 'Logo Screen Reader Text', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['logo_url_title'] ?? ''; ?>
							<input id="totaltheme_custom_login-logo_url_title" type="text" name="totaltheme_custom_login[logo_url_title]" value="<?php echo \esc_attr( $option ); ?>">
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-background">
						<th scope="row"><label for="totaltheme_custom_login-background_color"><?php \esc_html_e( 'Background Color', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[background_color]',
							'input_name' => 'totaltheme_custom_login[background_color]',
							'value'      => sanitize_text_field( $theme_mod['background_color'] ?? '' ),
						] );
						?></td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-background">
						<th scope="row"><label for="totaltheme_custom_login-background_img"><?php \esc_html_e( 'Background Image', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['background_img'] ?? ''; ?>
							<div class="uploader">
								<input id="totaltheme_custom_login-background_img" class="wpex-media-input" type="text" name="totaltheme_custom_login[background_img]" value="<?php echo \esc_attr( $option ); ?>">
								<button class="wpex-media-upload-button button-primary" type="button"><?php \esc_attr_e( 'Select', 'total' ); ?></button>
								<button class="wpex-media-remove button-secondary" type="button"><?php \esc_html_e( 'Remove', 'total' ); ?></button>
								<?php $preview = \wpex_get_image_url( $option ); ?>
								<div class="wpex-media-live-preview">
									<?php if ( $preview ) { ?>
										<img src="<?php echo \esc_url( $preview ); ?>" alt="<?php \esc_html_e( 'Preview Image', 'total' ); ?>">
									<?php } ?>
								</div>
							</div>
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-background">
						<th scope="row"><label for="totaltheme_custom_login-background_style"><?php \esc_html_e( 'Background Image Style', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['background_style'] ?? ''; ?>
							<select id="totaltheme_custom_login-background_style" name="totaltheme_custom_login[background_style]">
								<?php
								$bg_styles = [
									'stretched' => \esc_html__( 'Cover', 'total' ),
									'repeat'    => \esc_html__( 'Repeat', 'total' ),
									'fixed'     => \esc_html__( 'Center Fixed', 'total' ),
								];
								foreach ( $bg_styles as $key => $val ) { ?>
									<option value="<?php echo \esc_attr( $key ); ?>" <?php \selected( $option, $key, true ); ?>>
										<?php echo \strip_tags( $val ); ?>
									</option>
								<?php } ?>
							</select>
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-form">
						<th scope="row"><label for="totaltheme_custom_login-form_background_color"><?php \esc_html_e( 'Form Background Color', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[form_background_color]',
							'input_name' => 'totaltheme_custom_login[form_background_color]',
							'value'      => sanitize_text_field( $theme_mod['form_background_color'] ?? '' ),
						] );
						?></td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-form">
						<th scope="row"><label for="totaltheme_custom_login-form_styles_inner_check"><?php \esc_html_e( 'Apply Background to Inner Form Only?', 'total' ); ?></label></th>
						<td>
							<?php $enabled = $theme_mod['form_styles_inner_check'] ?? ''; ?>
							<span class="totaltheme-admin-checkbox">
								<input id="totaltheme_custom_login-form_styles_inner_check" type="checkbox" name="totaltheme_custom_login[form_styles_inner_check]" <?php \checked( $enabled, 'on' ); ?>>
								<span class="totaltheme-admin-checkbox__track"></span>
								<span class="totaltheme-admin-checkbox__thumb"></span>
							</span>
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-form">
						<th scope="row"><label for="totaltheme_custom_login-form_text_color"><?php \esc_html_e( 'Form Text Color', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[form_text_color]',
							'input_name' => 'totaltheme_custom_login[form_text_color]',
							'value'      => sanitize_text_field( $theme_mod['form_text_color'] ?? '' ),
						] );
						?></td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-form">
						<th scope="row"><label for="totaltheme_custom_login-form_input_bg"><?php \esc_html_e( 'Form Input Background', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[form_input_bg]',
							'input_name' => 'totaltheme_custom_login[form_input_bg]',
							'value'      => sanitize_text_field( $theme_mod['form_input_bg'] ?? '' ),
						] );
						?></td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-form">
						<th scope="row"><label for="totaltheme_custom_login-form_input_color"><?php \esc_html_e( 'Form Input Color', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[form_input_color]',
							'input_name' => 'totaltheme_custom_login[form_input_color]',
							'value'      => sanitize_text_field( $theme_mod['form_input_color'] ?? '' ),
						] );
						?></td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-form">
						<th scope="row"><label for="totaltheme_custom_login-form_border_radius"><?php \esc_html_e( 'Form Border Radius', 'total' ); ?> (px)</label></th>
						<td>
							<?php $option = $theme_mod['form_border_radius'] ?? ''; ?>
							<input id="totaltheme_custom_login-form_border_radius" type="text" name="totaltheme_custom_login[form_border_radius]" value="<?php echo \esc_attr( $option ); ?>">
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-form">
						<th scope="row"><label for="totaltheme_custom_login-form_box_shadow"><?php \esc_html_e( 'Form Box Shadow', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['form_box_shadow'] ?? ''; ?>
							<input id="totaltheme_custom_login-form_box_shadow" type="text" name="totaltheme_custom_login[form_box_shadow]" value="<?php echo \esc_attr( $option ); ?>">
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-form">
						<th scope="row"><label for="totaltheme_custom_login-form_border"><?php \esc_html_e( 'Form Border', 'total' ); ?></label></th>
						<td>
							<?php $option = $theme_mod['form_border'] ?? ''; ?>
							<input id="totaltheme_custom_login-form_border" type="text" name="totaltheme_custom_login[form_border]" value="<?php echo \esc_attr( $option ); ?>">
							<p class="description"><?php \esc_html_e( 'Please use the shorthand format: width style color. Enter 0px or "none" to disable border.', 'total' ); ?></p>
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-form">
						<th scope="row"><label for="totaltheme_custom_login-form_background_opacity"><?php \esc_html_e( '(Deprecated) Form Opacity', 'total' ); ?></label></th>
						<td>
							<?php $option = ! empty( $theme_mod['form_background_opacity'] ) ? floatval( $theme_mod['form_background_opacity'] ) : ''; ?>
							<input id="totaltheme_custom_login-form_background_opacity" type="number" name="totaltheme_custom_login[form_background_opacity]" value="<?php echo \esc_attr( $option ); ?>" min="0" max="1" step="0.1">
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-button">
						<th scope="row"><label for="totaltheme_custom_login-form_button_bg"><?php \esc_html_e( 'Form Button Background', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[form_button_bg]',
							'input_name' => 'totaltheme_custom_login[form_button_bg]',
							'value'      => sanitize_text_field( $theme_mod['form_button_bg'] ?? '' ),
						] );
						?></td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-button">
						<th scope="row"><label for="totaltheme_custom_login-form_button_color"><?php \esc_html_e( 'Form Button Color', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[form_button_color]',
							'input_name' => 'totaltheme_custom_login[form_button_color]',
							'value'      => sanitize_text_field( $theme_mod['form_button_color'] ?? '' ),
						] );
						?></td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-button">
						<th scope="row"><label for="totaltheme_custom_login-form_button_bg_hover"><?php \esc_html_e( 'Form Button Background: Hover', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[form_button_bg_hover]',
							'input_name' => 'totaltheme_custom_login[form_button_bg_hover]',
							'value'      => sanitize_text_field( $theme_mod['form_button_bg_hover'] ?? '' ),
						] );
						?></td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-bottom-links">
						<th scope="row"><label for="totaltheme_custom_login-bottom_links_color"><?php \esc_html_e( 'Bottom Links Color', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[bottom_links_color]',
							'input_name' => 'totaltheme_custom_login[bottom_links_color]',
							'value'      => sanitize_text_field( $theme_mod['bottom_links_color'] ?? '' ),
						] );
						?></td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-bottom-links">
						<th scope="row"><label for="totaltheme_custom_login-bottom_links_hover_color"><?php \esc_html_e( 'Bottom Links Hover Color', 'total' ); ?></label></th>
						<td><?php totaltheme_component( 'color', [
							'id'         => 'totaltheme_custom_login[bottom_links_hover_color]',
							'input_name' => 'totaltheme_custom_login[bottom_links_hover_color]',
							'value'      => sanitize_text_field( $theme_mod['bottom_links_hover_color'] ?? '' ),
						] );
						?></td>
					</tr>

				</table>

				<?php \wp_nonce_field( 'totaltheme-custom-login-admin', 'totaltheme-custom-login-admin-nonce' ); ?>

				<?php \submit_button(); ?>

			</form>

		</div>

	<?php }

	/**
	 * Hooks into "login_healanguage_switcherd".
	 */
	public static function on_login_head(): void {
		$options = self::get_options_list();

		// Do nothing if disabled.
		if ( empty( $options['enabled'] ) ) {
			return;
		}

		// Set options.
		$center                    = wp_validate_boolean( self::get_option( 'center' ) );
		$form_styles_inner_check   = wp_validate_boolean( self::get_option( 'form_styles_inner_check' ) );
		$width                     = self::get_option( 'width' );
		$logo                      = self::get_option( 'logo' );
		$logo_height               = self::get_option( 'logo_height', '84px' );
		$background_img            = self::get_option( 'background_img' );
		$background_style          = self::get_option( 'background_style' );
		$background_color          = self::get_option( 'background_color' );
		$form_bg                   = self::get_option( 'form_background_color' );
		$form_bg_opacity           = self::get_option( 'form_background_opacity' );
		$form_text_color           = self::get_option( 'form_text_color' );
		$form_top                  = self::get_option( 'form_top' );
		$form_input_bg             = self::get_option( 'form_input_bg' );
		$form_input_color          = self::get_option( 'form_input_color' );
		$form_border_radius        = self::get_option( 'form_border_radius' );
		$form_border               = self::get_option( 'form_border' );
		$form_box_shadow           = self::get_option( 'form_box_shadow' );
		$form_button_bg            = self::get_option( 'form_button_bg' );
		$form_button_bg_hover      = self::get_option( 'form_button_bg_hover' );
		$form_button_color         = self::get_option( 'form_button_color' );
		$bottom_links_color        = self::get_option( 'bottom_links_color' );
		$bottom_links_hover_color  = self::get_option( 'bottom_links_hover_color' );

		// Output Styles.
		$output = '';

			// Width.
			if ( $width_safe = \esc_attr( $width ) ) {
				if ( is_numeric( $width_safe ) ) {
					$width_safe = "{$width_safe}px";
				}
				$output .= "body.login #login{width:auto;max-width:{$width_safe};}";
			}

			// Logo.
			if ( $logo && $logo_safe = \esc_url( wpex_get_image_url( $logo ) ) ) {
				$output .= 'body.login div#login h1 a{';
					$output .= "background: url(\"{$logo_safe}\") center center no-repeat;";
					$output .= 'background-size: contain;';
					$output .= 'width: 100%;';
					$output .= 'display:block;';
					$output .= 'margin:0 auto 30px;';
					if ( $logo_height_safe = \esc_attr( $logo_height ) ) {
						if ( is_numeric( $logo_height_safe ) ) {
							$logo_height_safe = "{$logo_height_safe}px";
						}
						$output .= "height:{$logo_height_safe};";
					}
				$output .='}';
			}

			// Background image.
			if ( $background_img && $background_img_safe = \esc_url( wpex_get_image_url( $background_img ) ) ) {
				if ( 'stretched' === $background_style ) {
					$output .= "body.login{background: url(\"{$background_img_safe}\") no-repeat center center fixed; background-size: cover;}";
				} elseif ( 'repeat' === $background_style ) {
					$output .= "body.login{background: url(\"{$background_img_safe}\") repeat;}";
				} elseif ( 'fixed' === $background_style ) {
					$output .= "body.login background: url(\"{$background_img_safe}\") center top fixed no-repeat;}";
				}
			}

			// Background color.
			if ( $background_color && $background_color_safe = \esc_attr( wpex_parse_color( $background_color ) ) ) {
				$output .= "body.login{background-color:{$background_color_safe};}";
			}

			// Form top.
			if ( $form_top ) {
				if ( \is_numeric( $form_top ) ) {
					$form_top = "{$form_top}px";
				}
				if ( $form_top_safe = \esc_attr( $form_top ) ) {
					if ( ! $form_bg || $form_styles_inner_check ) {
						$output .= "body.login div#login{padding-top:{$form_top_safe};}";
					} else {
						$output .= ".wpex-login-spacer{padding-top:{$form_top_safe};}";
					}
				}
			}

			// Center - must go here to override!
			if ( $center ) {
				$form_top_safe = $form_top_safe ?? '5%';
				$output .= '@media (min-width: 769px) {';
					$output .= '.wpex-login-spacer{display:none;}';
					$output .= ".wpex-login-wrapper{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding-block:{$form_top_safe};}";
					$output .= 'body.login div#login{margin:0;}'; // the default auto margin causes issues inside the flex wrapper.
				$output .= '}';
			}

			// Form Background Color.
			if ( $form_bg ) {
				if ( $form_bg_safe = wpex_parse_color( $form_bg ) ) {
					if ( $form_bg_opacity && ! \str_starts_with( $form_bg_safe, 'var' ) && strlen( $form_bg_safe ) < 8 ) {
						$form_bg_safe = self::hex2rgba( $form_bg_safe, $form_bg_opacity );
					}
					if ( $form_styles_inner_check ) {
						$output .= "body.login #loginform{background-color:{$form_bg_safe};}";
					} else {
						if ( ! $form_top ) {
							$output .= '.wpex-login-spacer{padding-top:5%;}';
						}
						$output .= 'body.login #loginform{background:none;box-shadow:none;padding:0 0 20px;border:0;outline:0;}#backtoblog{ text-align:center;}.login #nav{text-align:center;}';
						$output .= "body.login div#login{background:{$form_bg_safe};height:auto;border-radius:5px;box-sizing:border-box;padding:40px;width:auto;}";
						if ( ! $width ) {
							$output .= 'body.login div#login{max-width:400px;}';
						}
					}
				}
			} elseif ( $form_bg_opacity && $form_bg_rgba_safe = \esc_attr( self::hex2rgba( '#fff', $form_bg_opacity ) ) ) {
				$output .= "body.login #loginform{background-color:{$form_bg_rgba_safe};}";
			}

			// Form box shadow.
			if ( $form_box_shadow && $form_box_shadow_safe = \esc_attr( $form_box_shadow ) ) {
				if ( ! $form_bg || $form_styles_inner_check ) {
					$output .= "body.login #loginform{box-shadow:{$form_box_shadow_safe};}";
				} else {
					$output .= "body.login div#login{box-shadow:{$form_box_shadow_safe};}";
				}
			}

			// Form border.
			if ( $form_border && $form_border_safe = \esc_attr( $form_border ) ) {
				if ( ! $form_bg || $form_styles_inner_check ) {
					$output .= "body.login #loginform{border:{$form_border_safe};}";
				} else {
					$output .= "body.login div#login{border:{$form_border_safe};}";
				}
			}

			// Form border radius.
			if ( $form_border_radius ) {
				if ( is_numeric( $form_border_radius ) ) {
					$form_border_radius = "{$form_border_radius}px";
				}
				$form_border_radius_safe = \esc_attr( $form_border_radius );
				if ( $form_border_radius_safe ) {
					if ( ! $form_bg || $form_styles_inner_check ) {
						$output .= "body.login #loginform{border-radius:{$form_border_radius_safe};}";
					} else {
						$output .= "body.login div#login{border-radius:{$form_border_radius_safe};}";
					}
				}
			}

			// Form input.
			if ( $form_input_bg && $form_input_bg_safe = \esc_attr( wpex_parse_color( $form_input_bg ) ) ) {
				$output .= "body.login div#login input.input{background:{$form_input_bg_safe};border:0;box-shadow:none;}";
			}

			if ( $form_input_color && $form_input_color_safe = \esc_attr( wpex_parse_color( $form_input_color ) ) ) {
				$output .= "body.login form .input,body.login button.wp-hide-pw{color:{$form_input_color_safe};}";
				$output .= "body.login button.wp-hide-pw:hover{color:{$form_input_color_safe};}";
			}

			// Text Color.
			if ( $form_text_color && $form_text_color_safe = \esc_attr( wpex_parse_color( $form_text_color ) ) ) {
				$output .= ".login label,.login #nav a,.login #backtoblog a,.login #nav{color:{$form_text_color_safe};}";
			}

			// Button background.
			if ( $form_button_bg && $form_button_bg_safe = \esc_attr( wpex_parse_color( $form_button_bg ) ) ) {
				$output .= "body.login div#login .button:not(.wp-hide-pw){background:{$form_button_bg_safe};border:0;outline:0;}";
			}

			// Button background.
			if ( $form_button_color && $form_button_color_safe = \esc_attr( wpex_parse_color( $form_button_color ) ) ) {
				$output .= "body.login div#login .button:not(.wp-hide-pw){color:{$form_button_color_safe};}";
			}

			// Button background Hover.
			if ( $form_button_bg_hover && $form_button_bg_hover_safe = \esc_attr( wpex_parse_color( $form_button_bg_hover ) ) ) {
				$output .= "body.login div#login .button:not(.wp-hide-pw):hover{background:{$form_button_bg_hover_safe};border:0;outline:0;}";
			}

			// Remove box-shadow.
			if ( $form_button_bg || $form_button_bg_hover ) {
				$output .= 'body.login div#login .button:not(.wp-hide-pw){box-shadow:none!important;}';
			}

			// Remove text-shadow.
			if ( $form_button_color || $form_button_bg ) {
				$output .= 'body.login div#login .button:not(.wp-hide-pw){text-shadow:none;}';
			}

			// Bottom Links.
			if ( $bottom_links_color && $bottom_links_color_safe = \esc_attr( wpex_parse_color( $bottom_links_color ) ) ) {
				$output .= ".login :is(#nav,#backtoblog,.privacy-policy-page-link) a:is(:any-link,:hover,:focus,:active){color:{$bottom_links_color_safe};}";
			}

			if ( $bottom_links_hover_color && $bottom_links_hover_color_safe = \esc_attr( wpex_parse_color( $bottom_links_hover_color ) ) ) {
				$output .= ".login :is(#nav,#backtoblog,.privacy-policy-page-link) a:is(:hover,:focus,:active){color:{$bottom_links_hover_color_safe};}";
			}

		if ( $output_safe = wp_strip_all_tags( $output ) ) {
			echo "<style>{$output_safe}</style>";
		}
	}

	/**
	 * Hooks into "login_header".
	 */
	public static function on_login_header(): void {
		if ( self::get_option( 'form_background_color' )
			&& ! (bool) self::get_option( 'form_styles_inner_check' )
		) {
			echo '<div class="wpex-login-spacer"></div>';
		}
		if ( wp_validate_boolean( self::get_option( 'center' ) ) ) {
			echo '<div class="wpex-login-wrapper">';
		}
	}

	/**
	 * Hooks into "login_footer".
	 */
	public static function on_login_footer(): void {
		if ( wp_validate_boolean( self::get_option( 'center' ) ) ) {
			echo '</div>';
		}
	}

	/**
	 * Custom login page logo URL.
	 */
	public static function filter_login_headerurl( $url ) {
		$options = self::get_options_list();
		if ( ! empty( $options['logo_url'] ) ) {
			$url = \esc_url( $options['logo_url'] );
		} else {
			$url = \esc_url( home_url( '/' ) );
		}
		return $url;
	}

	/**
	 * Custom login page logo URL title attribute.
	 */
	public static function filter_login_headertext( $title ) {
		$options = self::get_options_list();
		$title = $options['logo_url_title'] ?? $title;
		return \esc_attr( $title );
	}

	/**
	 * Returns custom login page settings.
	 */
	protected static function get_options_list(): array {
		$defaults = [
			'enabled'                 => true,
			'language_switcher'       => true,
			'center'                  => null,
			'width'                   => null,
			'logo'                    => null,
			'logo_url_home'           => true,
			'logo_url'                => null,
			'logo_url_title'          => null,
			'logo_height'             => null,
			'background_color'        => null,
			'background_img'          => null,
			'background_style'        => null,
			'form_background_color'   => null,
			'form_background_opacity' => null,
			'form_text_color'         => null,
			'form_top'                => null,
			'form_border_radius'      => null,
			'form_border'             => null,
			'form_box_shadow'         => null,
		];

		$list = \get_theme_mod( 'login_page_design') ?: $defaults;

		return (array) $list;
	}

	/**
	 * RGBA to HEX conversions.
	 */
	protected static function hex2rgba( $color, $opacity = false ) {
		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if( empty( $color ) ) {
			return $default;
		}

		// Sanitize $color if "#" is provided
		if ( $color[0] == '#' ) {
			$color = \substr( $color, 1 );
		}

		// Check if color has 6 or 3 characters and get values
		if ( \strlen( $color ) == 6) {
			$hex = [ $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] ];
		} elseif ( \strlen( $color ) == 3 ) {
			$hex = [ $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] ];
		} else {
			return $default;
		}

		// Convert hexadec to rgb
		$rgb = \array_map( 'hexdec', $hex );

		//Check if opacity is set(rgba or rgb)
		if ( $opacity ) {
			if ( \abs( $opacity ) > 1 )
				$opacity = 1.0;
			$output = 'rgba(' . \implode( ",", $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . \implode( ",", $rgb ) . ')';
		}

		return $output;
	}

	/**
	 * Get option value.
	 */
	protected static function get_option( $option_id, $default = '' ) {
		$options = self::get_options_list();
		return ! empty( $options[ $option_id ] ) ? $options[ $option_id ] : $default;
	}

	/**
	 * Check if currently viewing the interim login screen.
	 */
	protected static function is_interim_login(): bool {
		return (bool) isset( $_REQUEST['interim-login'] );
	}

	/**
	 * Register a setting and its sanitization callback.
	 */
	public static function register_settings(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 5.16' );
	}

	/**
	 * Save options.
	 */
	public static function save_options(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 5.16' );
	}

	/**
	 * Outputs the CSS for the custom login page.
	 */
	public static function output_css(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 5.16' );
	}

}
