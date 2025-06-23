<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Accessibility admin panel.
 */
final class Accessibility_Panel {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( ! \apply_filters( 'wpex_accessibility_panel', true ) ) {
			return;
		}

		\add_action( 'admin_menu', [ self::class, 'on_admin_menu' ], 50 );
		\add_action( 'admin_init', [ self::class, 'on_admin_init' ] );
	}

	/**
	 * Add sub menu page.
	 */
	public static function on_admin_menu() {
		\add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_attr__( 'Accessibility', 'total' ),
			\esc_attr__( 'Accessibility', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG . '-accessibility',
			[ self::class, 'render_admin_page' ]
		);
	}

	/**
	 * Register a setting and its sanitization callback.
	 */
	public static function on_admin_init() {
		\register_setting(
			'wpex_accessibility_settings',
			'wpex_accessibility_settings',
			[
				'sanitize_callback' => [ self::class, 'save_options' ],
				'default' => null,
			]
		);
	}

	/**
	 * Sanitization callback.
	 */
	public static function save_options( $options ) {
		if ( ! isset( $_POST['totaltheme-admin-accessibility-panel-nonce'] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-admin-accessibility-panel-nonce'] ) ), 'totaltheme-admin-accessibility-panel' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		foreach ( self::get_settings() as $k => $v ) {
			$type    = $v['type'] ?? 'input';
			$default = $v['default'] ?? null;
			switch ( $type ) {
				case 'checkbox':
					if ( isset( $options[ $k ] ) ) {
						if ( ! $default ) {
							\set_theme_mod( $k, true );
						} else {
							\remove_theme_mod( $k );
						}
					} else {
						if ( $default ) {
							\set_theme_mod( $k, false );
						} else {
							\remove_theme_mod( $k );
						}
					}
					break;
				case 'aria_label':
					$aria_labels = (array) \get_theme_mod( 'aria_labels', [] );
					if ( empty( $options[ $k ] ) ) {
						unset( $aria_labels[ $k ] );
					} else {
						$defaults = \wpex_aria_label_defaults();
						if ( ! isset( $defaults[ $k ] ) || ( $defaults[ $k ] !== $options[ $k ] ) ) {
							$aria_labels[ $k ] = $options[ $k ];
						}
					}
					if ( ! empty( $aria_labels ) ) {
						\set_theme_mod( 'aria_labels', $aria_labels );
					} else {
						\remove_theme_mod( 'aria_labels' );
					}
					break;
				default:
					if ( ! empty( $options[ $k ] ) && $default != $options[ $k ] ) {
						\set_theme_mod( $k, wp_strip_all_tags( $options[ $k ] ) );
					} else {
						\remove_theme_mod( $k );
					}
					break;
			}
		}
	}

	/**
	 * Settings page output.
	 */
	public static function render_admin_page() {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		\wp_enqueue_style( 'totaltheme-admin-pages' );
		\wp_enqueue_script( 'totaltheme-admin-pages' );

		?>

		<div class="wrap">
			<?php self::nav_tabs(); ?>
			<form method="post" action="options.php">
				<?php
				\settings_fields( 'wpex_accessibility_settings' );
				$tabs = [];
				foreach ( self::get_settings() as $setting_id => $setting ) {
					$tab = ( 'aria_label' === $setting['type'] ) ? 'aria_labels' : 'general';
					$tabs[ $tab ][ $setting_id ] = $setting;
				}

				foreach ( $tabs as $tab => $settings ) {

					// Note array_key_first was added in PHP 7.3
					if ( \function_exists( 'array_key_first' ) && $tab === \array_key_first( $tabs ) ) {
						$active = ' wpex-admin-tabs__panel--active';
					} elseif( $tab === 'general' ) {
						$active = ' wpex-admin-tabs__panel--active';
					} else {
						$active = '';
					} ?>
					<table id="wpex-admin-tabpanel--<?php echo \esc_attr( $tab ); ?>" class="form-table wpex-admin-tabs__panel<?php echo \esc_attr( $active ); ?>" role="tabpanel" tabindex="0" aria-labelledby="wpex-admin-tab--<?php echo \esc_attr( $tab ); ?>">
						<?php foreach ( $settings as $setting_id => $setting ) {
							$type = $setting['type'] ?? 'input';
							?>
								<tr valign="top">
									<th scope="row">
										<?php if ( 'checkbox' === $type ) {
											echo \esc_html( $setting['name'] );
										} else { ?>
											<label for="wpex_accessibility_settings[<?php echo \esc_attr( $setting_id ); ?>]"><?php echo \esc_html( $setting['name'] ); ?></label>
										<?php } ?>
									</th>
									<td><?php self::setting_field( $setting_id, $setting ); ?></td>
								</tr>
							<?php } ?>
						</table>
					<?php } ?>
				<?php \wp_nonce_field( 'totaltheme-admin-accessibility-panel', 'totaltheme-admin-accessibility-panel-nonce' ); ?>
				<?php \submit_button(); ?>
			</form>
		</div>

	<?php }

	/**
	 * Return array of settings.
	 */
	private static function get_settings(): array {
		$array = [
			// General options.
			'skip_to_content' => [
				'name' => \esc_html__( 'Skip to content link', 'total' ),
				'default' => true,
				'type' => 'checkbox',
				'description' => \esc_html__( 'Enables the skip to content link when clicking tab as soon as your site loads.', 'total' ),
			],
			'skip_to_content_id' => [
				'name' => \esc_html__( 'Skip to content ID', 'total' ),
				'default' => '#content',
				'type' => 'text',
			],
			'remove_menu_ids' => [
				'name' => \esc_html__( 'Remove Menu ID attributes', 'total' ),
				'default' => false,
				'type' => 'checkbox',
				'description' => \esc_html__( 'Removes the ID attributes added by default in WordPress to each item in your menu.', 'total' ),
			],
			// Aria labels.
			'toggle_bar_open' => [
				'name' => \esc_html__( 'Toggle Bar: Open', 'total' ),
				'type' => 'aria_label',
			],
			'toggle_bar_close' => [
				'name' => \esc_html__( 'Toggle Bar: Close', 'total' ),
				'type' => 'aria_label',
			],
			'site_navigation' => [
				'name' => \esc_html__( 'Main Menu', 'total' ),
				'type' => 'aria_label',
			],
			'search' => [
				'name' => \esc_html__( 'Search', 'total' ),
				'type' => 'aria_label',
			],
			'submit_search' => [
				'name' => \esc_html__( 'Submit Search', 'total' ),
				'type' => 'aria_label',
			],
			'mobile_menu' => [
				'name' => \esc_html__( 'Mobile Menu', 'total' ),
				'type' => 'aria_label',
			],
			'mobile_menu_open' => [
				'name' => \esc_html__( 'Mobile Menu: Open', 'total' ),
				'type' => 'aria_label',
			],
			'mobile_menu_close' => [
				'name' => \esc_html__( 'Mobile Menu: Close', 'total' ),
				'type' => 'aria_label',
			],
			'breadcrumbs' => [
				'name' => \esc_html__( 'Breadcrumbs', 'total' ),
				'type' => 'aria_label',
			],
			'footer_callout' => [
				'name' => \esc_html__( 'Footer Callout', 'total' ),
				'type' => 'aria_label',
			],
			'footer_bottom_menu' => [
				'name' => \esc_html__( 'Footer Menu', 'total' ),
				'type' => 'aria_label',
			],
		];

		if ( \totaltheme_is_integration_active( 'woocommerce' ) ) {
			$array['cart_open'] = [
				'name' => \esc_html__( 'Shopping Cart: Open', 'total' ),
				'type' => 'aria_label',
			];
			$array['cart_close'] = [
				'name' => \esc_html__( 'Shopping Cart: Close', 'total' ),
				'type' => 'aria_label',
			];
		}

		if ( \totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
			$array['dark_mode_toggle'] = [
				'name' => \esc_html__( 'Dark Mode Toggle', 'total' ),
				'type' => 'aria_label',
			];
		}

		return (array) $array;
	}

	/**
	 * Return setting field.
	 */
	private static function setting_field( $key, $setting ) {
		$type        = $setting[ 'type' ] ?? 'input';
		$default     = $setting[ 'default' ] ?? null;
		$description = $setting[ 'description' ] ?? null;
		switch ( $type ) {
			case 'checkbox':
				$theme_mod = \get_theme_mod( $key, $default );
				?>
				<?php if ( $description ) { ?>
					<label for="wpex_accessibility_settings[<?php echo \esc_attr( $key ); ?>]">
				<?php } ?>
				<input id="wpex_accessibility_settings[<?php echo \esc_attr( $key ); ?>]" type="checkbox" name="wpex_accessibility_settings[<?php echo \esc_attr( $key ); ?>]" value="<?php echo \esc_attr( $theme_mod ); ?>" <?php checked( $theme_mod, true ); ?>>

				<?php if ( $description ) { ?>
					<?php echo \esc_html( $description ); ?>
					</label>
				<?php } ?>
				<?php break;
			case 'aria_label':
				$aria_label = \wpex_get_aria_label( $key );
				?>
				<input type="text" id="wpex_accessibility_settings[<?php echo \esc_attr( $key ); ?>]" name="wpex_accessibility_settings[<?php echo \esc_attr( $key ); ?>]" value="<?php echo \esc_attr( $aria_label ); ?>">
				<?php if ( $description ) { ?>
					<p class="description"><?php echo \esc_html( $description ); ?></p>
				<?php } ?>
				<?php break;
			default:
				$theme_mod = \get_theme_mod( $key, $default );
				?>
				<input type="text" id="wpex_accessibility_settings[<?php echo \esc_attr( $key ); ?>]" name="wpex_accessibility_settings[<?php echo \esc_attr( $key ); ?>]" value="<?php echo \esc_attr( $theme_mod ); ?>">
				<?php if ( $description ) { ?>
					<p class="description"><?php echo \esc_html( $description ); ?></p>
				<?php } ?>
				<?php break;
		}
	}

	/**
	 * Panel tabs.
	 */
	private static function nav_tabs() {
		?>
		<h2 class="nav-tab-wrapper wpex-admin-tabs__list" role="tablist">
			<a id="wpex-admin-tab--general" href="#" class="nav-tab nav-tab-active wpex-admin-tabs__tab" aria-controls="wpex-admin-tabpanel--general" aria-selected="true" role="tab" tabindex="0"><?php \esc_html_e( 'General', 'total' ); ?></a>
			<a id="wpex-admin-tab--aria_labels" href="#" class="nav-tab wpex-admin-tabs__tab" aria-controls="wpex-admin-tabpanel--aria_labels" aria-selected="false" role="tab" tabindex="-1"><?php \esc_html_e( 'Aria Labels', 'total' ); ?></a>
		</h2>
		<?php
	}

}
