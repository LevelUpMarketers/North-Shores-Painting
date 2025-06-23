<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Favicons panel.
 */
final class Favicons {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( \wpex_is_request( 'admin' ) ) {
			\add_action( 'admin_menu', [ self::class, 'add_submenu_page' ] );
			\add_action( 'admin_init', [ self::class, 'register_page_options' ] );
			\add_action( 'admin_head', [ self::class, 'render_favicon_html' ] );
			if ( \get_theme_mod( 'favicon' ) ) {
				\remove_action( 'login_head', 'wp_site_icon', 99 );
				\add_action( 'admin_init', [ self::class, 'remove_admin_wp_site_icon' ], 99 );
			}
		}
		if ( \wpex_is_request( 'frontend' ) ) {
			\add_action( 'wp_head', [ self::class, 'render_favicon_html' ] );
			if ( \get_theme_mod( 'favicon' ) ) {
				\remove_action( 'wp_head', 'wp_site_icon', 99 );
			}
		}
	}

	/**
	 * Add sub menu page.
	 */
	public static function add_submenu_page() {
		$hook_suffix = \add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_html__( 'Favicons', 'total' ),
			\esc_html__( 'Favicons', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG . '-favicons',
			array( self::class, 'render_admin_page' )
		);

		\add_action( "load-{$hook_suffix}", [ self::class, 'admin_help_tab' ] );
	}

	/**
	 * Add admin help tab.
	 */
	public static function admin_help_tab() {
		$screen = \get_current_screen();

		if ( ! $screen ) {
			return;
		}

		$allowed_html = [
			'a' => [
				'href' => [],
			],
		];

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_favicons',
				'title'   => \esc_html__( 'Overview', 'total' ),
				'content' => '<p>' . \wp_kses( sprintf( __( 'This panel will allow you to set a custom favicon for each device. If you prefer to define a single site icon and have it crop automatically you can go to <a href="%s">Appearance > Customize > Site Identity</a> and set your Site Icon via the core WordPress function.', 'total' ), \esc_url( \admin_url( '/customize.php?autofocus[section]=title_tagline' ) ) ), $allowed_html ) . '</p>'
			]
		);
	}

	/**
	 * Function that will register admin page options.
	 */
	public static function register_page_options() {
		\register_setting(
			'wpex_favicons',
			'wpex_favicons',
			[
				'sanitize_callback' => [ self::class, 'save_options' ],
				'default' => null,
				]
		);

		\add_settings_section(
			'wpex_favicons_main',
			false,
			[ self::class, 'admin_settings_main_callback' ],
			'wpex-favicons'
		);

		\add_settings_field(
			'wpex_favicon',
			\esc_html__( 'Favicon', 'total' ),
			[ self::class, 'admin_favicon_setting_callback' ],
			'wpex-favicons',
			'wpex_favicons_main'
		);

		\add_settings_field(
			'wpex_iphone_icon',
			\esc_html__( 'Apple iPhone Icon ', 'total' ),
			[ self::class, 'admin_iphone_icon_setting_callback' ],
			'wpex-favicons',
			'wpex_favicons_main'
		);

		\add_settings_field(
			'wpex_ipad_icon',
			\esc_html__( 'Apple iPad Icon ', 'total' ),
			[ self::class, 'admin_ipad_icon_setting_callback' ],
			'wpex-favicons',
			'wpex_favicons_main'
		);

		\add_settings_field(
			'wpex_iphone_icon_retina',
			\esc_html__( 'Apple iPhone Retina Icon ', 'total' ),
			[ self::class, 'admin_iphone_icon_retina_setting_callback' ],
			'wpex-favicons',
			'wpex_favicons_main'
		);

		\add_settings_field(
			'wpex_ipad_icon_retina',
			\esc_html__( 'Apple iPad Retina Icon ', 'total' ),
			[ self::class, 'admin_ipad_icon_retina_setting_callback' ],
			'wpex-favicons',
			'wpex_favicons_main'
		);
	}

	/**
	 * Save options.
	 */
	public static function save_options( $options ): void {
		if ( ! \is_array( $options )
			|| ! isset( $_POST['totaltheme-favicons-admin-nonce'] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-favicons-admin-nonce'] ) ), 'totaltheme-favicons-admin' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		foreach ( $options as $key => $value ) {
			if ( ! empty( $value ) ) {
				\set_theme_mod( $key, \sanitize_text_field( \wp_unslash( $value ) ) );
			} else {
				\remove_theme_mod( $key );
			}
		}
	}

	/**
	 * Main Settings section callback.
	 */
	public static function admin_settings_main_callback(): void {
		// Leave blank
	}

	/**
	 * Returns correct value for preview.
	 */
	private static function sanitize_val( $val, $instance = 'mod' ) {
		if ( 'image' === $instance && \is_numeric( $val ) ) {
			$val = \wp_get_attachment_image_src( $val, 'full' );
			if ( ! empty( $val ) && \is_array( $val ) ) {
				$val = $val[0];
			}
		} elseif( \is_numeric( $val ) ) {
			$val = \absint( $val );
		}
		return $val;
	}

	/**
	 * Fields callback functions.
	 */

	// Favicon
	public static function admin_favicon_setting_callback(): void {
		$val     = \get_theme_mod( 'favicon' );
		$val     = self::sanitize_val( $val );
		$preview = self::sanitize_val( $val, 'image' );

		?>

		<input type="text" name="wpex_favicons[favicon]" value="<?php echo \esc_attr( $val ); ?>" class="wpex-media-input">
		<button class="wpex-media-upload-button button-primary" type="button"><?php \esc_attr_e( 'Select', 'total' ); ?></button>
		<button class="wpex-media-remove button-secondary" type="button"><?php \esc_html_e( 'Remove', 'total' ); ?></button>
		<p class="description">32x32</p>
		<div class="wpex-media-live-preview" data-image-size="32">
			<?php if ( $preview ) { ?>
				<img src="<?php echo \esc_url( $preview ); ?>" alt="<?php \esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:32px;height:32px;">
			<?php } ?>
		</div>
	<?php }

	// iPhone
	public static function admin_iphone_icon_setting_callback(): void {
		$val	 = \get_theme_mod( 'iphone_icon' );
		$val     = self::sanitize_val( $val );
		$preview = self::sanitize_val( $val, 'image' );

		?>

		<input type="text" name="wpex_favicons[iphone_icon]" value="<?php echo \esc_attr( $val ); ?>" class="wpex-media-input">
		<button class="wpex-media-upload-button button-primary" type="button"><?php \esc_attr_e( 'Select', 'total' ); ?></button>
		<button class="wpex-media-remove button-secondary" type="button"><?php \esc_html_e( 'Remove', 'total' ); ?></button>
		<p class="description">57x57</p>
		<div class="wpex-media-live-preview" data-image-size="57">
			<?php if ( $preview ) { ?>
				<img src="<?php echo \esc_url( $preview ); ?>" alt="<?php \esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:57px;height:57px;">
			<?php } ?>
		</div>
	<?php }

	// iPad
	public static function admin_ipad_icon_setting_callback(): void {
		$val	 = \get_theme_mod( 'ipad_icon' );
		$val     = self::sanitize_val( $val );
		$preview = self::sanitize_val( $val, 'image' );

		?>

		<input type="text" name="wpex_favicons[ipad_icon]" value="<?php echo \esc_attr( $val ); ?>" class="wpex-media-input">
		<button class="wpex-media-upload-button button-primary" type="button"><?php \esc_attr_e( 'Select', 'total' ); ?></button>
		<button class="wpex-media-remove button-secondary" type="button"><?php \esc_html_e( 'Remove', 'total' ); ?></button>
		<p class="description">76x76</p>
		<div class="wpex-media-live-preview" data-image-size="76">
			<?php if ( $preview ) { ?>
				<img src="<?php echo \esc_url( $preview ); ?>" alt="<?php \esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:76px;height:76px;">
			<?php } ?>
		</div>
	<?php }

	// iPhone Retina
	public static function admin_iphone_icon_retina_setting_callback(): void {
		$val	 = \get_theme_mod( 'iphone_icon_retina' );
		$val     = self::sanitize_val( $val );
		$preview = self::sanitize_val( $val, 'image' );

		?>

		<input type="text" name="wpex_favicons[iphone_icon_retina]" value="<?php echo \esc_attr( $val ); ?>" class="wpex-media-input">
		<button class="wpex-media-upload-button button-primary" type="button"><?php \esc_attr_e( 'Select', 'total' ); ?></button>
		<button class="wpex-media-remove button-secondary" type="button"><?php \esc_html_e( 'Remove', 'total' ); ?></button>
		<p class="description">120x120</p>
		<div class="wpex-media-live-preview" data-image-size="120">
			<?php if ( $preview ) { ?>
				<img src="<?php echo \esc_url( $preview ); ?>" alt="<?php \esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:120px;height:120px;">
			<?php } ?>
		</div>
	<?php }

	// iPad Retina
	public static function admin_ipad_icon_retina_setting_callback(): void {
		$val	 = \get_theme_mod( 'ipad_icon_retina' );
		$val     = self::sanitize_val( $val );
		$preview = self::sanitize_val( $val, 'image' );

		?>

		<input type="text" name="wpex_favicons[ipad_icon_retina]" value="<?php echo \esc_attr( $val ); ?>" class="wpex-media-input">
		<button class="wpex-media-upload-button button-primary" type="button"><?php \esc_attr_e( 'Select', 'total' ); ?></button>
		<button class="wpex-media-remove button-secondary" type="button"><?php \esc_html_e( 'Remove', 'total' ); ?></button>
		<p class="description">152x152</p>
		<div class="wpex-media-live-preview" data-image-size="152">
			<?php if ( $preview ) { ?>
				<img src="<?php echo \esc_url( $preview ); ?>" alt="<?php \esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:152px;height:152px;">
			<?php } ?>
		</div>
	<?php }

	/**
	 * Settings page output.
	 */
	public static function render_admin_page(): void {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		\wp_enqueue_media();

		\wp_enqueue_style( 'totaltheme-admin-pages' );
		\wp_enqueue_script( 'totaltheme-admin-pages' );

		\delete_option( 'wpex_favicons' );

		?>
		<div class="wrap">
			<form method="post" action="options.php">
				<?php \settings_fields( 'wpex_favicons' ); ?>
				<?php \do_settings_sections( 'wpex-favicons' ); ?>
				<?php \wp_nonce_field( 'totaltheme-favicons-admin', 'totaltheme-favicons-admin-nonce' ); ?>
				<?php \submit_button(); ?>
			</form>
		</div>
	<?php }

	/**
	 * Settings page output.
	 */
	public static function render_favicon_html(): void {

		// Favicon - Standard.
		$favicon = \get_theme_mod( 'favicon' );
		if ( $favicon && $favicon_url = self::sanitize_val( $favicon, 'image' ) ) {
			echo '<link rel="icon" href="' . \esc_url( $favicon_url ) . '" sizes="32x32">';
			echo '<link rel="shortcut icon" href="' . \esc_url( $favicon_url ) . '">'; // For older IE
		}

		// Apple iPhone Icon - 57px.
		$favicon = \get_theme_mod( 'iphone_icon' );
		if ( $favicon && $favicon_url = self::sanitize_val( $favicon, 'image' ) ) {
			echo '<link rel="apple-touch-icon" href="' . \esc_url( $favicon_url ) . '" sizes="57x57" >';
		}

		// Apple iPad Icon - 76px.
		$favicon = \get_theme_mod( 'ipad_icon' );
		if ( $favicon && $favicon_url = self::sanitize_val( $favicon, 'image' ) ) {
			echo '<link rel="apple-touch-icon" href="' . \esc_url( $favicon_url ) . '" sizes="76x76" >';
		}

		// Apple iPhone Retina Icon - 120px.
		$favicon = \get_theme_mod( 'iphone_icon_retina' );
		if ( $favicon && $favicon_url = self::sanitize_val( $favicon, 'image' ) ) {
			echo '<link rel="apple-touch-icon" href="' . \esc_url( $favicon_url ) . '" sizes="120x120">';
		}

		// Apple iPad Retina Icon - 114px.
		$favicon = \get_theme_mod( 'ipad_icon_retina' );
		if ( $favicon && $favicon_url = self::sanitize_val( $favicon, 'image' ) ) {
			echo '<link rel="apple-touch-icon" href="' . \esc_url( $favicon_url ) . '" sizes="114x114">';
		}

	}

	/**
	 * Remove the WP site icon in the admin.
	 */
	public static function remove_admin_wp_site_icon(): void {
		\remove_action( 'admin_head', 'wp_site_icon', 10 );
	}

}
