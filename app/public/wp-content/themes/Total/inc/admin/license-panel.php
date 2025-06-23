<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Theme License Activation and De-activation.
 */
final class License_Panel {

	/**
	 * Stores the current theme license.
	 */
	private $theme_license = null;

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of License_Panel.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Private Constructor.
	 */
	private function __construct() {
		if ( \apply_filters( 'wpex_show_license_panel', true ) ) {
			$this->init_hooks();
		}
	}

	/**
	 * Hook into actions and filters.
	 */
	public function init_hooks() {
		if ( ! get_option( 'total_dismiss_license_notice', false ) && ! $this->get_theme_license() ) {
			\add_action( 'admin_notices', [ $this, 'admin_notice' ] );
		}

		if ( self::is_enabled() ) {
			if ( $this->is_network_admin() ) {
				\add_action( 'network_admin_menu', [ $this, 'add_network_admin_page' ] );
			} else {
				\add_action( 'admin_menu', [ $this, 'add_admin_submenu_page' ], 1 );
			}
			\add_action( 'wp_ajax_wpex_theme_license_form', [ $this, 'license_form_ajax' ] );
		}
	}

	/**
	 * Get the active theme license.
	 */
	public function get_theme_license() {
		if ( null === $this->theme_license ) {
			$this->theme_license = totaltheme_get_license();
		}
		return $this->theme_license;
	}

	/**
	 * Check if we should display the admin page.
	 */
	public static function is_enabled(): bool {
		$check = true;
		$check = \apply_filters( 'wpex_show_license_panel', $check ); // @deprecated
		if ( $check && \is_multisite() && ! \is_main_site() && \totaltheme_get_network_license() ) {
			$check = false;
		}
		return (bool) \apply_filters( 'totaltheme/admin/license_panel/is_enabled', $check );
	}

	/**
	 * Check if we are on a multisite network admin screen.
	 */
	private function is_network_admin(): bool {
		return \is_multisite() && \is_network_admin();
	}

	/**
	 * Return current site URL.
	 */
	private function get_site_url() {
		$url = $this->is_network_admin() ? \network_site_url() : \site_url();
		if ( $url && is_string( $url ) ) {
			return trim( $url );
		}
	}

	/**
	 * Return current site URL encoded.
	 */
	private function get_site_url_encoded() {
		if ( $url = $this->get_site_url() ) {
			return \rawurlencode( trim( $url ) );
		}
	}

	/**
	 * Add network admin page.
	 */
	public function add_network_admin_page() {
		$hook_suffix = \add_submenu_page(
			'themes.php',
			\esc_html__( 'Total Theme License', 'total' ),
			\esc_html__( 'Total Theme License', 'total' ),
			'manage_network_options',
			'total-theme-license',
			[ $this, 'render_admin_page' ]
		);

		\add_action( "admin_print_styles-{$hook_suffix}", [ $this, 'enqueue_styles' ] );
		\add_action( "admin_print_scripts-{$hook_suffix}", [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Add sub menu page.
	 */
	public function add_admin_submenu_page() {
		$hook_suffix = \add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_html__( 'Theme License', 'total' ),
			\esc_html__( 'Theme License', 'total' ),
			'administrator', // admin only!
			\WPEX_THEME_PANEL_SLUG . '-theme-license',
			[ $this, 'render_admin_page' ]
		);

		\add_action( "load-{$hook_suffix}", [ $this, 'admin_help_tab' ] );
		\add_action( "admin_print_styles-{$hook_suffix}", [ $this, 'enqueue_styles' ] );
		\add_action( "admin_print_scripts-{$hook_suffix}", [ $this, 'enqueue_scripts' ] );
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
				'id'      => 'totaltheme_panel',
				'title'   => \esc_html__( 'Overview', 'total' ),
				'content' => '<p><strong>Please Read !!!</strong><p>A valid theme license is required for each domain using the Total WordPress theme. If you don\'t own a license please purchase one from <a href="https://1.envato.market/EPJRe" target="_blank" rel="noopener noreferrer">ThemeForest.com &#8599;</a></p>This theme is exclusively sold at ThemeForest. Anywhere else providing a download for theme is doing so illegally and most likely includes malicous code.</p><p>A lot of effort, time and money is put into the development and maintance of this product which was created and developed by AJ Clarke from <a href="https://www.wpexplorer.com/" target="_blank" rel="noopener noreferrer">WPExplorer.com &#8599;</a> and he kindly requests that you abide by the product\'s licensing terms.</p>',
			]
		);
	}

	/**
	 * Enqueue styles.
	 */
	public function enqueue_styles() {
		\wp_enqueue_style(
			'totaltheme-admin-license-activation',
			\totaltheme_get_css_file( 'admin/license-activation' ),
			[],
			\WPEX_THEME_VERSION
		);
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		\wp_enqueue_script(
			'totaltheme-admin-license-activation',
			\totaltheme_get_js_file( 'admin/license-activation' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION,
			true
		);
	}

	/**
	 * Returns user capability for the admin page.
	 */
	protected function get_user_capability() {
		return $this->is_network_admin() ? 'manage_network_options' : 'administrator';
	}

	/**
	 * Settings page output.
	 */
	public function render_admin_page() {
		if ( ! \current_user_can( $this->get_user_capability() ) ) {
			return;
		}

		$this->enqueue_scripts();

		if ( isset( $_GET['troubleshoot'] ) ) {
			$this->troubleshoot();
			return;
		}

		$license         = $this->verify_license() ? $this->get_theme_license() : '';
		$license_cleared = ! empty( $_GET['license-cleared'] );
		?>

		<div class="wrap wpex-license-activation">
			<?php
			// Show h1 for network page.
			if ( $this->is_network_admin() || ( \is_multisite() && \is_main_site() ) ) {
				echo '<h1>' . \esc_html__( 'Total Theme Network License', 'total' ) . '</h1>';
			} else {
				// Need to insert h1 for notices.
				echo '<h1 style="display:none;" aria-hidden="true"></h1>';
			} ?>

			<?php if ( $license || $license_cleared ) {

				$notice_type = 'updated';

				if ( $license_cleared ) {
					$notice_type = 'notice-warning';
				}

				?>

				<div class="wpex-license-activation__notice notice <?php echo \esc_attr( $notice_type ); ?>">
					<?php if ( $license_cleared ) { ?>
						<p><?php echo \wp_kses_post( __( 'The current URL did not match the URL of the registered license. Your license has been removed from this site but remains active on the original URL. You can now enter a new license for this site.', 'total' ) ); ?>
					<?php } else { ?>
						<p><?php \esc_html_e( 'Congratulations. Your theme license is active.', 'total' ); ?></p>
					<?php } ?>
				</div>

			<?php } else { ?>

				<div class="wpex-license-activation__notice notice"></div>

			<?php } ?>

			<div class="wpex-license-activation__card">

				<h2 class="wpex-license-activation__heading"><?php \esc_html_e( 'Verify your License', 'total' ); ?></h2>

				<div class="wpex-license-activation__card-inner">

					<p class="wpex-license-activation__info"><?php echo \sprintf( \esc_html__( 'Enter your purchase code below. Learn how to %sfind your purchase code%s.', 'total' ), '<a target="_blank" rel="noopener noreferrer" href="https://totalwptheme.com/docs/how-to-find-your-total-theme-license/">', ' &#8599;</a>' ); ?></p>

					<form method="post" class="wpex-license-activation__form">

						<?php if ( $license ) { ?>
							<input type="text" class="wpex-license-activation__input" name="license" placeholder="<?php echo \esc_attr( $license ); ?>" value="<?php echo \esc_attr( $license ); ?>" readonly="readonly" autocomplete="off" onclick="select()">
						<?php } else { ?>
							<input type="text" class="wpex-license-activation__input" name="license" placeholder="<?php esc_html_e( 'Enter your purchase code here.', 'total' ); ?>" autocomplete="off">
						<?php } ?>

						<?php \wp_nonce_field( 'wpex_theme_license_form_nonce', 'wpex_theme_license_form_nonce' ); ?>

						<div class="wpex-license-activation__submit">
							<?php
							$submit_classes = 'wpex-license-activation__button primary button-hero ';
							$submit_classes .= $license ? 'deactivate' : 'activate';
							$activate_txt   = \esc_html__( 'Activate your license', 'total' );
							$deactivate_txt = \esc_html__( 'Deactivate your license', 'total' );
							submit_button(
								$license ? $deactivate_txt : $activate_txt,
								$submit_classes,
								'submit',
								false,
								[
									'data-activate'   => $activate_txt,
									'data-deactivate' => $deactivate_txt,
								]
							); ?>
							<div class="wpex-license-activation__spinner"><?php echo \totaltheme_get_loading_icon( 'wordpress' ); ?></div>
						</div>

					</form>

					<p class="wpex-license-activation__description"><?php echo \wp_kses_post( __( 'A purchase code (license) is only valid for <strong>One WordPress Installation</strong> (single or multisite). Are you already using this theme on another installation? Purchase <a target="_blank" rel="noopener noreferrer" href="https://themeforest.net/item/total-responsive-multipurpose-wordpress-theme/6339019?ref=WPExplorer&license=regular&open_purchase_for_item_id=6339019">new license here &#8599;</a> to get your new purchase code. If you are running a multisite network you only need to activate your license on the main site.', 'total' ) ); ?></p>

				</div>

			</div>

			<div class="wpex-license-activation__links">
				<a class="button button-secondary" href="<?php echo \esc_url( \admin_url( 'admin.php?page=wpex-panel-theme-license&troubleshoot=1' ) ); ?>"><?php \esc_html_e( 'Troubleshoot', 'total' ); ?></a><a class="button button-primary" href="https://my.totalwptheme.com" target="_blank" rel="noopener noreferrer"><?php \esc_html_e( 'Manage your licenses', 'total' ); ?> &#8599;</a>
			</div>

		</div>
	<?php }

	/**
	 * Troubleshoot.
	 */
	public function troubleshoot() {
		echo '<div class="wrap wpex-license-activation">';

			echo '<h1>License API Troubleshooting</h1>';

			if ( ! \function_exists( 'wp_remote_retrieve_response_code' ) ) {
				echo 'Looks like the wp_remote_retrieve_response_code function doesnt exist, make sure you update WordPress';
				return;
			}

			$remote_response = \wp_remote_get( 'https://my.totalwptheme.com/wp-json/twpt-license-manager/' );

			$response_code = \intval( \wp_remote_retrieve_response_code( $remote_response ) );

			echo '<div class="wpex-license-activation__response wpex-license-activation__response--' . \sanitize_html_class( $response_code ) . '">';
				echo '<p data-status="code-' . esc_attr( $response_code ) . '">';
				switch ( $response_code ) {
					case 200:
						esc_html_e( 'Server access ok.', 'total' );
						break;
					case 301:
						esc_html_e( 'Error 301: Firewall blocking access.', 'total' );
						break;
					case 403:
						esc_html_e( 'Error 403: Your server has been blocked by our firewall for security reasons.', 'total' );
						break;
					case 404:
						esc_html_e( 'Error 404: Please contact the theme developer for assistance.', 'total' );
						break;
					default:
						if ( isset( $remote_response->errors ) && is_array( $remote_response->errors ) ) {
							foreach ( $remote_response->errors as $k => $v ) {
								if ( empty( $v[0] ) ) {
									continue;
								}
								echo '<div><strong>' . $k . '</strong>: ' . $v[0] . '</div>';
							}

						}
						break;
				}
				echo '</p>';
			echo '</div>';

			echo '<div class="wpex-license-activation__links"><a href="' . \esc_url( \admin_url( 'admin.php?page=wpex-panel-theme-license' ) ) . '" class="button button-secondary">&larr; ' . \esc_html( 'Go back', 'total' ) . '</a></div>';

		echo '</div>';
	}

	/**
	 * Updates the active license option.
	 *
	 * The is_network_admin check doesn't work with ajax so we use update_options
	 * instead of using update_site_option, which seems to work just fine anyway.
	 */
	private function update_license_option( $license, $dev = false ) {
		\update_option( 'totaltheme_license', $license );
		if ( $dev ) {
			\update_option( 'totaltheme_license_dev', true );
		}
	}

	/**
	 * Deletes the active license option.
	 */
	private function delete_license_option() {
		\delete_option( 'totaltheme_license' );
		\delete_option( 'totaltheme_license_dev' );

		// Remove renamed options.
		\delete_option( 'active_theme_license' );
		\delete_option( 'active_theme_license_dev' );
	}

	/**
	 * Activate License.
	 */
	private function activate_license( $license, $response ) {
		$remote_response = \wp_remote_post( 'https://my.totalwptheme.com/wp-json/twpt-license-manager/activate/', [
			'body' => [
				'key'    => $license,
				'domain' => $this->get_site_url_encoded(),
			],
		] );
		if ( \is_wp_error( $remote_response ) ) {
			$response['message'] = $remote_response->get_error_message();
		} else {
			$remote_response_code = \wp_remote_retrieve_response_code( $remote_response );
			$response['response_code'] = $remote_response_code;
			$response['messageClass'] = 'notice-error';

			if ( 200 === (int) $remote_response_code ) {
				$body = \json_decode( \wp_remote_retrieve_body( $remote_response ) );
				if ( ! empty( $body->activated ) ) {
					$response['success'] = true;
					$response['message'] = \esc_html__( 'Congratulations. Your theme license is active.', 'total' );
					$response['messageClass'] = 'updated';
					$this->update_license_option( $license );
				} elseif ( ! empty( $body->error ) ) {
					switch ( $body->error ) {
						case 'api_error':
							$response['message'] = \esc_html__( 'The license code is not properly formated or couldn\'t be validated by the Envato API.', 'total' );
							break;
						case 'wrong_product':
							$response['message'] = \esc_html__( 'This license code is for a different product.', 'total' );
							break;
						case 'invalid':
							$response['message'] = \esc_html__( 'This license code is not valid.', 'total' );
							break;
						case 'duplicate':
							$response['message'] = \esc_html__( 'This license is already in use. Click the "manage licenses" link below to log in with your Envato ID and manage your licenses.', 'total' );
							break;
						default:
							$response['message'] = \esc_html( $body->error );
							break;
					}
				} else {
					$response['message'] = \esc_html( 'Something wen\'t wrong, please try again.', 'total' );
				}
			} else {
				$response['message'] = \esc_html( 'Can not connect to the verification server at this time. Please make sure outgoing connections are enabled on your server and try again. If it still does not work please wait a few minutes and try again.', 'total' );
			}
		}
		return $response;
	}

	/**
	 * Deactivate License.
	 */
	private function deactivate_license( $license, $response ) {
		$remote_response = \wp_remote_post( 'https://my.totalwptheme.com/wp-json/twpt-license-manager/deactivate/', [
			'body' => [
				'key'    => $license,
				'domain' => $this->get_site_url_encoded(),
			],
		] );

		if ( \is_wp_error( $remote_response ) ) {
			$response['message'] = $remote_response->get_error_message();
		} else {
			$remote_response_code = \wp_remote_retrieve_response_code( $remote_response );
			$response['response_code'] = $remote_response_code;
			if ( 200 === (int) $remote_response_code ) {
				$body = \json_decode( \wp_remote_retrieve_body( $remote_response ) );
				if ( ! empty( $body->deactivated ) ) {
					$this->delete_license_option();
					$response['message'] = esc_html__( 'The license has been deactivated successfully.', 'total' );
					$response['messageClass'] = 'notice-warning';
					$response['success'] = true;
				} elseif ( ! empty( $body->error ) ) {
					$response['message'] = \esc_html( $body->error );
				} else {
					$response['message'] = \esc_html( 'Something wen\'t wrong, please try again.', 'total' );
				}
			} else {
				$response['message'] = \esc_html( 'Can not connect to the verification server at this time, please try again in a few minutes.', 'total' );
			}
		}
		return $response;
	}

	/**
	 * License form ajax.
	 */
	public function license_form_ajax() {
		if ( ! isset( $_POST['nonce'] )
			|| ! isset( $_POST['process'] )
			|| ! wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['nonce'] ) ), 'wpex_theme_license_form_nonce' )
		) {
			wp_die();
		}

		$response = [
			'message'       => '',
			'messageClass'  => 'notice-error',
			'success'       => false,
			'response_code' => '',
		];

		if ( empty( $_POST['license'] ) ) {
			$response['message'] = \esc_html__( 'Please enter a license.', 'total' );
			$response['messageClass'] = 'notice-warning';
		} else {

			$license = \sanitize_text_field( \wp_unslash( $_POST['license'] ) );

			if ( ! $this->is_license_valid( $license ) ) {
				$response['message'] = \esc_html__( 'License not properly formatted.', 'total' );
				$response['messageClass'] = 'notice-error';
			} else {
				$process = \sanitize_text_field( \wp_unslash( $_POST['process'] ) );

				switch ( $process ) {
					case 'deactivate':
						$response = $this->deactivate_license( $license, $response );
						break;
					case 'activate':
						$response = $this->activate_license( $license, $response );
						break;
				}
			}
		}

		\wp_send_json( $response );

		\wp_die();
	}

	/**
	 * Admin Notice.
	 */
	public function admin_notice(): void {
		if ( isset( $_GET['total-dismiss'] )
			&& 'license-nag' === \sanitize_text_field( \wp_unslash( $_GET['total-dismiss'] ) )
			&& isset( $_GET[ 'total_dismiss_license_nag_nonce' ] )
			&& \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_GET['total_dismiss_license_nag_nonce'] ) ), 'total_dismiss_license_nag' )
		) {
			\update_option( 'total_dismiss_license_notice', true );
			return;
		}
		$screen = \get_current_screen();
		if ( ! isset( $screen->id ) || ! \in_array( $screen->id, [ 'dashboard', 'themes', 'plugins' ], true ) ) {
			return;
		}
		?>
		<div class="notice notice-warning is-dismissible">
			<p><strong><?php \esc_html_e( 'Activate Theme License', 'total' ); ?></strong>: <?php echo \esc_html_e( 'Don\'t forget to activate your theme license to receive updates and support.', 'total' ); ?></p>
			<p><strong><a href="<?php echo \esc_url( \admin_url( 'admin.php?page=wpex-panel-theme-license' ) ); ?>"><?php esc_html_e( 'Activate your license', 'total' ); ?></a> | <a href="<?php echo \esc_url( \wp_nonce_url( \add_query_arg( 'total-dismiss', 'license-nag' ), 'total_dismiss_license_nag', 'total_dismiss_license_nag_nonce' ) ); ?>"><?php \esc_html_e( 'Dismiss notice', 'total' ); ?></a></strong></p>
		</div>
		<?php
	}

	/**
	 * Checks if a license is valid.
	 * 
	 * license is version 4 UUID with the format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
	 * where x is any hexadecimal digit and y is one of 8, 9, A, or B.
	 */
	public function is_license_valid( string $license ): bool {
		$pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
		return (bool) preg_match( $pattern, $license );
	}

	/**
	 * Verify that the site license is active.
	 */
	public function verify_license(): bool {
		$license = $this->get_theme_license();
		
		if ( ! $license ) {
			return false;
		}

		$response = wp_remote_get( "https://my.totalwptheme.com/wp-json/twpt-license-manager/check/{$license}" );

		if ( \is_wp_error( $response ) ) {
			return true;
		}

		$body = \json_decode( \wp_remote_retrieve_body( $response ) );
		$status = $body->status ?? '';
		$domain = $body->domain ?? '';

		if ( 'inactive' === $status || $domain !== $this->get_site_url() ) {
			$this->delete_license_option();
			return false;
		}
		
		return true;
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
