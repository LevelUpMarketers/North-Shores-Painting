<?php

namespace TotalTheme;

use WP_Query;

\defined( 'ABSPATH' ) || exit;

/**
 * Header Builder.
 */
final class HeaderBuilder {

	/**
	 * Hook to insert the header into.
	 */
	protected $insert_hook = 'wpex_hook_header_inner';

	/**
	 * Hook priority.
	 */
	protected $insert_priority = 0;

	/**
	 * Start things up.
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 */
	public function init_hooks(): void {
		if ( \wpex_is_request( 'admin' ) ) {
			\add_action( 'admin_menu', [ $this, 'add_admin_submenu_page' ], 20 );
			\add_action( 'admin_init', [ $this, 'register_page_options' ] );
		}
		if ( totaltheme_call_static( 'Header\Core', 'is_custom' ) ) {
			if ( \wpex_is_request( 'frontend' ) ) {
				\add_action( 'wp', [ $this, 'alter_header' ] );
			}
			\add_filter( 'wpex_head_css', [ $this, 'custom_css' ], 99 );
		}
	}

	/**
	 * Add sub menu page.
	 */
	public function add_admin_submenu_page(): void {
		$hook_suffix = \add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_html__( 'Header Builder', 'total' ),
			\esc_html__( 'Header Builder', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG .'-header-builder',
			[ $this, 'render_admin_page' ]
		);

		\add_action( "load-{$hook_suffix}", [ $this, 'admin_help_tab' ] );
	}

	/**
	 * Add admin help tab.
	 */
	public function admin_help_tab(): void {
		$screen = \get_current_screen();

		if ( ! $screen ) {
			return;
		}

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_header_builder',
				'title'   => \esc_html__( 'Overview', 'total' ),
				'content' => '<p>' . \wp_kses( sprintf( __( 'Use this setting to replace the default theme header with content created with WPBakery or other page builder. Alternatively you can modify the default header via <a href="%s" target="_blank" rel="noopener noreferrer">Customize > Header &#8599;</a>', 'total' ), \esc_url( \admin_url( 'customize.php?autofocus[panel]=wpex_header' ) ) ), $this->get_kses_allowed_html() ) . '</p>'
			],
		);

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_header_builder_patterns',
				'title'   => \esc_html__( 'Patterns', 'total' ),
				'content' => '<p>' . \wp_kses( sprintf( __( 'If you are using the WPBakery page builder you can access various pre-built header templates for quickly creating your custom header. Learn more about <a href="https://totalwptheme.com/docs/section-templates/" target="_blank" rel="noopener noreferrer">WPBakery Patterns &#8599;</a>', 'total' ), '' ), $this->get_kses_allowed_html() )  . '</p>'
			]
		);
	}

	/**
	 * Returns settings array.
	 */
	public function settings(): array {
		return [
			'page_id' => \esc_html__( 'Header Template', 'total' ),
			'bg' => \esc_html__( 'Background Color', 'total' ),
			'bg_img' => \esc_html__( 'Background Image', 'total' ),
			'bg_img_style' => \esc_html__( 'Background Image Style', 'total' ),
			'page_header_hidden_main_top_padding' => \esc_html__( 'Hidden Page Header Title Spacing', 'total' ),
			'top_bar' => \esc_html__( 'Top Bar', 'total' ),
			'sticky' => \esc_html__( 'Sticky Header', 'total' ),
			'sticky_type' => \esc_html__( 'Sticky Type', 'total' ),
			'overlay_page_id' => \esc_html__( 'Transparent Header Template', 'total' ),
		];
	}

	/**
	 * Function that will register admin page options.
	 */
	public function register_page_options(): void {
		\register_setting(
			'wpex_header_builder',
			'header_builder',
			[
				'sanitize_callback' => [ $this, 'save_options' ],
				'default' => null,
			]
		);

		\add_settings_section(
			'wpex_header_builder_main',
			false,
			[ $this, 'section_main_callback' ],
			'wpex-header-builder-admin'
		);

		$settings = $this->settings();
		foreach ( $settings as $key => $val ) {
			\add_settings_field(
				$key,
				$val,
				[ $this, "{$key}_field_callback" ],
				'wpex-header-builder-admin',
				'wpex_header_builder_main',
				[
					'label_for' => 'wpex-header-builder-field--' . \sanitize_html_class( $key ),
				]
			);
		}
	}

	/**
	 * Save options.
	 */
	public function save_options( $options ): void {
		if ( ! isset( $_POST['totaltheme-header-builder-admin-nonce'] ) 
			|| ! wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-header-builder-admin-nonce'] ) ), 'totaltheme-header-builder-admin' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		foreach ( $this->settings() as $key => $val ) {
			switch ( $key ) {
				case 'page_header_hidden_main_top_padding':
					if ( empty( $options[ $key ] ) ) {
						\remove_theme_mod( $key );
					} else {
						$value = is_numeric( $options[ $key ] ) ? "{$options[ $key ]}px" : $options[ $key ];
						\set_theme_mod( $key, sanitize_text_field( $value ) );
					}
					break;
				case 'top_bar':
					if ( empty( $options['top_bar'] ) ) {
						\set_theme_mod( 'top_bar', false );
					} else {
						\remove_theme_mod( 'top_bar' );
					}
					break;
				case 'sticky':
					if ( ! empty( $options['sticky'] ) ) {
						\set_theme_mod( 'header_builder_sticky', true );
					} else {
						\remove_theme_mod( 'header_builder_sticky' );
					}
					break;
				default:
					if ( empty( $options[ $key ] ) ) {
						\remove_theme_mod( "header_builder_{$key}" );
					} else {
						\set_theme_mod( "header_builder_{$key}", sanitize_text_field( $options[ $key ] ) );
					}
					break;
			}
		}
	}

	/**
	 * Main Settings section callback.
	 */
	public function section_main_callback( $options ) {
		// not needed
	}

	/**
	 * Header Builder Page ID.
	 */
	public function page_id_field_callback(): void {
		$selected_template = \get_theme_mod( 'header_builder_page_id' );
		$template_exists   = ( $selected_template && \get_post_status( $selected_template ) );
		\totaltheme_call_non_static( 'Theme_Builder', 'template_select', [
			'id'            => 'wpex-header-builder-field--page_id',
			'name'          => 'header_builder[page_id]',
			'selected'      => $selected_template,
			'template_type' => 'header',
		] );
		?>
		<br><br>
		<?php \totaltheme_call_static( 'Helpers\Add_Template', 'render_form', 'header', $template_exists ); ?>
		<div class="wpex-edit-template-links-ajax totaltheme-admin-button-group<?php echo ( ! $template_exists ) ? ' hidden' : ''; ?>" data-hide-rows="true"><?php $this->edit_links( $selected_template ); ?></div>
	<?php }

	/**
	 * Background Setting.
	 */
	public function bg_field_callback(): void {
		\totaltheme_component( 'color', [
			'id'                 => 'wpex-header-builder-field--bg',
			'value'              => ( $value = \get_theme_mod( 'header_builder_bg' ) ) ? sanitize_text_field( $value ) : '',
			'input_name'         => 'header_builder[bg]',
			'dropdown_placement' => 'right',
			'include'            => 'transparent,surface-1,surface-2,surface-3,surface-4',
		] );
	}

	/**
	 * Background Image Setting.
	 */
	public function bg_img_field_callback(): void {
		$bg = \get_theme_mod( 'header_builder_bg_img' );
		?>
		<div class="uploader">
			<input id="wpex-header-builder-field--bg_img" class="wpex-media-input" type="text" name="header_builder[bg_img]" value="<?php echo \esc_attr( $bg ); ?>">
			<button class="wpex-media-upload-button button-primary"><?php \esc_attr_e( 'Select', 'total' ); ?></button>
			<button class="wpex-media-remove button-secondary"><?php \esc_html_e( 'Remove', 'total' ); ?></button>
			<div class="wpex-media-live-preview">
				<?php if ( $preview = \wpex_get_image_url( $bg ) ) { ?>
					<img src="<?php echo \esc_url( $preview ); ?>" alt="<?php \esc_html_e( 'Preview Image', 'total' ); ?>">
				<?php } ?>
			</div>
		</div>
	<?php }

	/**
	 * Background Image Style Setting.
	 */
	public function bg_img_style_field_callback(): void {
		$style = \get_theme_mod( 'header_builder_bg_img_style' );
		?>
			<select id="wpex-header-builder-field--bg_img_style" name="header_builder[bg_img_style]">
			<?php foreach ( \wpex_get_bg_img_styles() as $key => $val ) { ?>
				<option value="<?php echo \esc_attr( $key ); ?>" <?php \selected( $style, $key, true ); ?>><?php echo \esc_html( $val ); ?></option>
			<?php } ?>
		</select>
	<?php }

	/**
	 * Hidden page header margin.
	 */
	public function page_header_hidden_main_top_padding_field_callback(): void {
		$val = \get_theme_mod( 'page_header_hidden_main_top_padding' );
		?>
		<input type="text" name="header_builder[page_header_hidden_main_top_padding]" value="<?php echo \esc_attr( $val ); ?>" placeholder="0px">
		<p class="description"><?php \esc_html_e( 'When the page header title is set to hidden there won\'t be any space between the header and the main content. You can enter a default spacing here.', 'total' ); ?><br><?php \wp_kses( \printf( \__( 'Disable or further customize the page header title area via <a href="%s" target="_blank" rel="noopener noreferrer">Customize > Page Header Title &#8599;</a>.', 'total' ), \esc_url( \admin_url( '/customize.php?autofocus[section]=wpex_page_header' ) ) ), $this->get_kses_allowed_html() ); ?></p>
		<?php
	}

	/**
	 * Top bar setting callback.
	 */
	public function top_bar_field_callback(): void {
		$val = \get_theme_mod( 'top_bar', true ) ? 'on' : false;
		?>
		<input type="checkbox" name="header_builder[top_bar]" id="wpex-header-builder-field--top_bar" <?php \checked( $val, 'on' ); ?>>
		<p class="description"><?php esc_html_e( 'It\'s advisable to keep this setting disabled. The option is provided if you need to display a static section above a sticky header.', 'total' ); ?></p>
	<?php }

	/**
	 * Sticky setting callback.
	 */
	public function sticky_field_callback(): void {
		$val = \get_theme_mod( 'header_builder_sticky' ) ? 'on' : false;
		?>
		<input type="checkbox" name="header_builder[sticky]" id="wpex-header-builder-field--sticky" <?php \checked( $val, 'on' ); ?>>
	<?php }

	/**
	 * Sticky type setting callback.
	 */
	public function sticky_type_field_callback(): void {
		$val = \get_theme_mod( 'header_builder_sticky_type', 'js' );
		?>
		<select type="checkbox" name="header_builder[sticky_type]" id="wpex-header-builder-field--sticky-type">
			<option value="js" <?php \selected( $val, 'js' ); ?>><?php esc_html_e( 'JavaScript', 'total' ); ?></option>
			<option value="css" <?php \selected( $val, 'css' ); ?>><?php esc_html_e( 'CSS', 'total' ); ?></option>
		</select>
		<p class="description"><?php echo wp_kses( __( '<strong>Important</strong>: The theme utilizes JavaScript for the sticky function so it can work with the transparent header and provide alternative styles when sticky. If you don\'t require these functionalities, it\'s advisable to switch to the CSS-based sticky header for a faster and smoother experience.', 'total' ), [ 'strong' => [] ] ); ?></p>
		<p class="description"><?php esc_html_e( 'If you need to show, hide or re-style elements when the header becomes sticky you will need to use the JavaScript type. Then you can target the "is-sticky" class for CSS modifications and use the classnames "hidden-stuck" and "visible-stuck" to control element visibility.', 'total' ); ?></p>
	<?php }

	/**
	 * Transparent header alternative.
	 */
	public function overlay_page_id_field_callback(): void {
		\totaltheme_call_non_static( 'Theme_Builder', 'template_select', [
			'id'            => 'wpex-header-builder-field--overlay_page_id',
			'name'          => 'header_builder[overlay_page_id]',
			'selected'      => \get_theme_mod( 'header_builder_overlay_page_id' ),
			'template_type' => 'header',
		] );
		echo '<p class="description">' . \wp_kses( \sprintf( \__( 'Define an alternative header template for use with the transparent header. This is useful if you need to provide an alternative logo or styling (colors) when using the transparent header for select pages only. Enable and customize the Transparent header via <a href="%s" target="_blank" rel="noopener noreferrer">Customize > Header &#8599;</a>.', 'total' ), \esc_url( \admin_url( '/customize.php?autofocus[section]=wpex_header_overlay' ) ) ), $this->get_kses_allowed_html() ) . '</p>';
	}

	/**
	 * Settings page output.
	 */
	public function render_admin_page(): void {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		\wp_enqueue_media();

		\wp_enqueue_style( 'totaltheme-admin-pages' );
		\wp_enqueue_script( 'totaltheme-admin-pages' );

		\wp_enqueue_style( 'totaltheme-components' );
		\wp_enqueue_script( 'totaltheme-components' );

		?>

		<div id="wpex-admin-page" class="wrap totaltheme-admin-wrap">

			<?php
			// Get defined template ID
			$template_id = \get_theme_mod( 'header_builder_page_id' );

			// Current language check
			if ( ! $template_id ) {
				$show_lang_warning = false;
				if ( \totaltheme_is_integration_active( 'polylang' ) && \function_exists( 'pll_default_language' ) && \function_exists( 'pll_current_language' ) ) {
					$current_lang = \pll_current_language();
					if ( $current_lang !== \pll_default_language() && $current_lang !== false ) {
						$show_lang_warning = true;
					}
				}
				if ( \totaltheme_is_integration_active( 'wpml' ) ) {
					$current_lang = \apply_filters( 'wpml_current_language', null );
					if ( $current_lang !== \apply_filters( 'wpml_default_language', null ) && $current_lang !== 'all' ) {
						$show_lang_warning = true;
					}
				}
				if ( $show_lang_warning ) {
					echo '<div class="notice notice-warning"><p>' . \esc_html__( 'Please switch to the default site language or "all" languages.', 'your-text-domain' ) . '</p></div>';
					return;
				}
			}

			// Warning if builder page has been deleted
			if ( $template_id && FALSE === \get_post_status( $template_id ) ) {
				echo '<div class="notice notice-warning"><p>' . \esc_html__( 'It appears the page you had selected has been deleted, please re-save your settings to prevent issues.', 'total' ) . '</p></div>';
			} ?>

			<form method="post" action="options.php">
				<?php \settings_fields( 'wpex_header_builder' ); ?>
				<?php \do_settings_sections( 'wpex-header-builder-admin' ); ?>
				<?php \wp_nonce_field( 'totaltheme-header-builder-admin', 'totaltheme-header-builder-admin-nonce' ); ?>
				<?php \submit_button(); ?>
			</form>

		</div>
	<?php }

	/**
	 * Remove the header and add custom header if enabled.
	 */
	public function alter_header(): void {
		$hooks = \wpex_theme_hooks();

		if ( isset( $hooks['header']['hooks'] ) ) {
			foreach ( $hooks['header']['hooks'] as $hook ) {
				if ( ! \in_array( $hook, [ 'wpex_hook_header_before', 'wpex_hook_header_after' ], true ) ) {
					\remove_all_actions( $hook, false );
				}
			}
		}

		$this->insert_hook     = \apply_filters( 'wpex_header_builder_insert_hook', $this->insert_hook );
		$this->insert_priority = \apply_filters( 'wpex_header_builder_insert_priority', $this->insert_priority );

		\add_action( $this->insert_hook, [ $this, 'get_part' ], $this->insert_priority );
	}

	/**
	 * Gets the header builder template part if the header is enabled.
	 */
	public function get_part(): void {
		if ( totaltheme_call_static( 'Header\Core', 'is_enabled' ) ) {
			\get_template_part( 'partials/header/header-builder' );
		}
	}

	/**
	 * Custom CSS for header builder.
	 */
	public function custom_css( string $css ): string {
		$header_css = '';

		// Custom background color.
		$bg = \get_theme_mod( 'header_builder_bg' );
		if ( $bg && $bg_safe = \wpex_parse_color( $bg ) ) {
			$header_css .= "--wpex-site-header-bg-color:{$bg_safe};";
		}

		// Custom background image.
		$bg_img = \get_theme_mod( 'header_builder_bg_img' );
		if ( $bg_img && $bg_img_safe = \esc_url( \wpex_get_image_url( \sanitize_text_field( $bg_img ) ) ) ) {
			$header_css .= "background-image:url(\"{$bg_img_safe}\");";
		}

		// @todo use utility classes instead so that fixed backgrounds aren't messed up on mobile.
		if ( $bg_img && $bg_img_style = \get_theme_mod( 'header_builder_bg_img_style' ) ) {
			$header_css .= \wpex_sanitize_data( $bg_img_style, 'background_style_css' );
		}

		if ( $header_css ) {
			$css .= "/*HEADER BUILDER*/#site-header.header-builder{{$header_css}}";
		}

		return $css;
	}

	/**
	 * Get edit links.
	 */
	public function edit_links( $template_id = '' ): void {
		if ( ! $template_id || ! \current_user_can( 'edit_post', $template_id ) ) {
			return;
		}
		$edit_link = \get_edit_post_link( $template_id );
		if ( ! $edit_link ) {
			return;
		}
		$template_type = \get_post_type( $template_id );
		?>
		<a href="<?php echo \esc_url( $edit_link ); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php \esc_html_e( 'Backend Edit', 'total' ); ?> &#8599;</a>
		<?php if ( \WPEX_VC_ACTIVE && \in_array( $template_type, [ 'templatera', 'wpex_templates' ], true ) ) {
			$post_type = \get_post_type( $template_id );
			?>
			<a href="<?php echo \esc_url( \admin_url( "post.php?vc_action=vc_inline&post_id={$template_id}&post_type={$post_type}" ) ); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php \esc_html_e( 'Edit with WPBakery', 'total' ); ?> &#8599;</a>
		<?php } elseif ( \class_exists( '\Elementor\Plugin' ) && \in_array( $template_type, [ 'elementor_library', 'wpex_templates' ], true ) ) { ?>
			<a href="<?php echo \esc_url( \admin_url( "post.php?post={$template_id}&action=elementor" ) ); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php \esc_html_e( 'Edit with Elementor', 'total' ); ?> &#8599;</a>
		<?php } ?>
	<?php }

	/**
	 * Returns header template ID.
	 */
	public static function get_template_id(): int {
		return (int) totaltheme_call_static( 'Header\Core', 'get_template_id' );
	}

	/**
	 * Return wp_kses allowed html.
	 */
	protected function get_kses_allowed_html(): array {
		return [
			'a' => [
				'href'   => [],
				'rel'    => [],
				'target' => [],
			],
		];
	}

}

new HeaderBuilder();
