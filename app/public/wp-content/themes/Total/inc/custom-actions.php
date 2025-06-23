<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom user actions panel.
 */
final class Custom_Actions {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( \wpex_is_request( 'admin' ) ) {
			\add_action( 'admin_menu', [ self::class, 'on_admin_menu' ], 40 );
			\add_action( 'admin_init', [ self::class, 'on_admin_init' ] );
		}
		if ( \wpex_is_request( 'frontend' ) ) {
			\add_action( 'init', [ self::class, 'render_custom_actions' ] );
		}
	}

	/**
	 * Add sub menu page.
	 */
	public static function on_admin_menu() {
		$hook_suffix = \add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_html__( 'Custom Actions', 'total' ),
			\esc_html__( 'Custom Actions', 'total' ),
			self::get_user_capability(),
			\WPEX_THEME_PANEL_SLUG . '-user-actions',
			[ self::class, 'render_admin_page' ]
		);

		\add_action( "load-{$hook_suffix}", [ self::class, 'admin_help_tab' ] );
		\add_action( "admin_print_styles-{$hook_suffix}", [ self::class, 'enqueue_styles' ] );
		\add_action( "admin_print_scripts-{$hook_suffix}", [ self::class, 'enqueue_scripts' ] );
	}

	/**
	 * Add admin help tab.
	 */
	public static function admin_help_tab() {
		$screen = \get_current_screen();

		if ( ! $screen ) {
			return;
		}

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_custom_actions',
				'title'   => \esc_html__( 'Overview', 'total' ),
				'content' => '<p>' . esc_html__( 'Here you can insert HTML code into any section of the theme. PHP code is not allowed for security reasons. If you wish to insert PHP code into a theme action you will want to use a child theme or shortcodes in the fields below.', 'total' ) . '</p>'
			]
		);
	}

	/**
	 * Register a setting and its sanitization callback.
	 */
	public static function on_admin_init() {
		\register_setting( 'wpex_custom_actions', 'wpex_custom_actions', [ self::class, 'save_custom_actions' ] );
	}

	/**
	 * Save custom actions.
	 */
	public static function save_custom_actions( $actions ) {
		if ( ! \is_array( $actions )
			|| ! isset( $_POST['totaltheme-custom-actions-admin-nonce'] )
			|| ! wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-custom-actions-admin-nonce'] ) ), 'totaltheme-custom-actions-admin' )
			|| ! \current_user_can( self::get_user_capability() )
		) {
			return;
		}

		foreach ( $actions as $key => $val ) {
			if ( empty( $val['action'] ) || \ctype_space( $val['action'] ) ) {
				unset( $actions[ $key ] );
			} else {
				// Sanitize action @todo don't allow javascript anymore?
				//$actions[ $key ]['action'] = wp_kses_post( $val['action'] );
				// Priority must be a number.
				if ( ! empty( $val['priority'] ) ) {
					$actions[ $key ]['priority'] = \intval( $val['priority'] );
				}
			}
		}
		return $actions;
	}

	/**
	 * Panel scripts.
	 */
	public static function enqueue_scripts(): void {
		\wp_enqueue_script(
			'totaltheme-admin-custom-actions',
			\totaltheme_get_js_file( 'admin/custom-actions' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);
	}

	/**
	 * Panel styles.
	 */
	public static function enqueue_styles(): void {
		\wp_enqueue_style(
			'totaltheme-admin-custom-actions',
			\totaltheme_get_css_file( 'admin/custom-actions' ),
			[],
			\WPEX_THEME_VERSION,
			'all'
		);
	}

	/**
	 * Settings page.
	 */
	public static function render_admin_page(): void {
		if ( ! \current_user_can( self::get_user_capability() ) ) {
			return;
		}

		?>

		<div class="wrap totaltheme-custom-actions">
			<form method="post" action="options.php">
				<?php \settings_fields( 'wpex_custom_actions' ); ?>
				<div class="totaltheme-custom-actions__inner">
					<div class="totaltheme-custom-actions__list">
						<?php
						// Get hooks.
						$wp_hooks = [
							'wp_hooks' => [
								'label' => 'WordPress',
								'hooks' => [
									'wp_head',
									'wp_body_open',
									'wp_footer',
								],
							],
							'html' => [
								'label' => 'HTML',
								'hooks' => [ 'wpex_hook_after_body_tag' ]
							]
						];

						// Theme hooks.
						$theme_hooks = \wpex_theme_hooks();

						// Combine hooks.
						$hooks = ( $wp_hooks + $theme_hooks );

						// Loop through sections.
						foreach ( $hooks as $section ) :
							
							?>

							<div class="totaltheme-custom-actions__group">

								<h2><?php echo \esc_html( $section['label'] ); ?></h2>

								<?php foreach ( $section['hooks'] as $hook ) :

									// These are hooks that need deprecating.
									if ( \in_array( $hook, [ 'wpex_outer_wrap_before', 'wpex_outer_wrap_after' ] ) ) {
										continue;
									}

									// Get hook settings.
									$action = self::get_hook_action( $hook );
									$priority = isset( $options[ $hook ]['priority'] ) ? \intval( $options[ $hook ]['priority'] ) : 10;
									$not_empty = ( $action && ! \ctype_space( $action ) ) ? true : false;

									?>

										<div class="totaltheme-custom-actions-item" data-state="closed" data-has-content="<?php echo $not_empty ? 'true' : 'false'; ?>">
											<div class="totaltheme-custom-actions-item__heading">
												<h3><?php
													$hook_name = $hook;
													if ( 'wpex_mobile_menu_top' === $hook_name || 'wpex_mobile_menu_bottom' === $hook_name ) {
														$hook_name = $hook_name . ' (' . \esc_html( 'deprecated', 'total' ) . ')';
													}
													echo \wp_strip_all_tags( $hook_name );
												?></span></h3>
												<div class="hide-if-no-js">
													<button class="totaltheme-custom-actions-item__toggle" aria-expanded="false">
														<span class="screen-reader-text"><?php \esc_html_e( 'Toggle fields for action hook:', 'total' ); ?> <?php echo \wp_strip_all_tags( $hook ); ?></span>
														<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path></svg></span>
													</button>
												</div>
											</div>

											<div class="totaltheme-custom-actions-item__fields">
												<p>
													<label for="wpex_custom_actions[<?php echo \esc_attr( $hook ); ?>][action]"><?php \esc_html_e( 'Code', 'total' ); ?></label>
													<textarea id="wpex_custom_actions[<?php echo \esc_attr( $hook ); ?>][action]" placeholder="<?php esc_attr_e( 'Enter your custom action here&hellip;', 'total' ); ?>" name="wpex_custom_actions[<?php echo \esc_attr( $hook ); ?>][action]" rows="10" cols="50" style="width:100%;"><?php echo \esc_textarea( $action ); ?></textarea>
												</p>
												<p class="wpex-clr">
													<label for="wpex_custom_actions[<?php echo \esc_attr( $hook ); ?>][priority]"><?php \esc_html_e( 'Priority', 'total' ); ?></label>
													<input id="wpex_custom_actions[<?php echo \esc_attr( $hook ); ?>][priority]" name="wpex_custom_actions[<?php echo \esc_attr( $hook ); ?>][priority]" type="number" value="<?php echo \esc_attr( $priority ); ?>">
												</p>
											</div>
										</div>

								<?php endforeach; ?>

							</div>

						<?php endforeach; ?>

					</div>
					<div class="totaltheme-custom-actions__sidebar">
						<div class="totaltheme-custom-actions-widget">
							<h3><?php \esc_html_e( 'Save Your Actions', 'total' ); ?></h3>
							<div class="totaltheme-custom-actions-widget__content">
								<p><?php \esc_html_e( 'Click the button below to save your custom actions.', 'total' ); ?></p>
								<?php \wp_nonce_field( 'totaltheme-custom-actions-admin', 'totaltheme-custom-actions-admin-nonce' ); ?>
								<?php \submit_button(); ?>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>

	<?php }

	/**
	 * Returns the custom actions.
	 */
	protected static function get_custom_actions(): array {
		return (array) \get_option( 'wpex_custom_actions', [] );
	}

	/**
	 * Renders the custom actions on the frontend.
	 */
	public static function render_custom_actions(): void {
		foreach ( self::get_custom_actions() as $key => $val ) {
			if ( ! empty( $val['action'] ) ) {
				$priority = isset( $val['priority'] ) ? \intval( $val['priority'] ) : 10;
				\add_action( $key, [ self::class, 'execute_action' ], $priority );
			}
		}
	}

	/**
	 * Used to execute an action.
	 */
	public static function execute_action(): void {
		$hook    = \current_filter();
		$actions = self::get_custom_actions();
		$output  = $actions[ $hook ]['action'] ?? '';
		if ( $output && is_string( $output ) && empty( $actions[ $hook ]['php'] ) ) {
			// @todo can we add some sanitization but still allow scripts?
			//$output = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $output ); // remove script tags
			//$output = wp_kses_post( $output );
			echo totaltheme_replace_vars( \do_shortcode( \do_blocks( $output ) ) );
		}
	}

	/**
	 * Returns user capability for this admin page.
	 */
	private static function get_user_capability(): string {
		return (string) \apply_filters( 'totaltheme/custom_actions/user_capability', 'edit_theme_options' );
	}

	/**
	 * Hook action.
	 */
	protected static function get_hook_action( string $hook = '' ): string {
		$action = self::get_custom_actions()[ $hook ]['action'] ?? '';
		if ( ! $action && ( 'wpex_hook_outer_wrap_before' === $hook || 'wpex_hook_outer_wrap_after' === $hook ) ) {
			$hook = \str_replace( 'wpex_hook_outer', 'wpex_outer', $hook );
			$action = self::get_custom_actions()[ $hook ]['action'] ?? '';
		}
		return (string) $action;
	}

}
