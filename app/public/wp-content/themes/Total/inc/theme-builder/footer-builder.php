<?php

namespace TotalTheme;

use WP_Query;

\defined( 'ABSPATH' ) || exit;

/**
 * Footer Builder.
 */
final class FooterBuilder {

	/**
	 * Hook to insert the footer into.
	 */
	protected $insert_hook = 'wpex_hook_footer_before';

	/**
	 * Hook priority.
	 */
	protected $insert_priority = 40;

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
		if ( totaltheme_call_static( 'Footer\Core', 'is_custom' ) ) {
			if ( ! \get_theme_mod( 'footer_builder_footer_widgets', false ) ) {
				\add_filter( 'wpex_register_footer_sidebars', '__return_false' );
			}
			if ( \wpex_is_request( 'frontend' ) ) {
				\add_action( 'wp', [ $this, 'alter_footer' ] );
				\add_filter( 'wpex_head_css', [ $this, 'filter_wpex_head_css' ], 99 );

			}
		}
	}

	/**
	 * Add sub menu page.
	 */
	public function add_admin_submenu_page(): void {
		$hook_suffix = \add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_html__( 'Footer Builder', 'total' ),
			\esc_html__( 'Footer Builder', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG . '-footer-builder',
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
				'id'      => 'totaltheme_footer_builder',
				'title'   => \esc_html__( 'Overview', 'total' ),
				'content' => '<p>' .  \esc_html__( 'By default the footer consists of a simple widgetized area which you can control via the WordPress Customizer. For more complex layouts you can use the option below to select a template and create your own custom footer layout from scratch.', 'total' ) . '</p>'
			]
		);
	}

	/**
	 * Function that will register admin page options.
	 */
	public function register_page_options(): void {
		\register_setting(
			'wpex_footer_builder',
			'footer_builder',
			array(
				'sanitize_callback' => array( $this, 'save_options' ),
				'default' => null
			)
		);

		\add_settings_section(
			'wpex_footer_builder_main',
			false,
			array( $this, 'section_main_callback' ),
			'wpex-footer-builder-admin'
		);

		\add_settings_field(
			'footer_builder_page_id',
			\esc_html__( 'Footer Template', 'total' ),
			array( $this, 'content_id_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main',
			array(
				'label_for' => 'wpex-footer-builder-select',
			)
		);

		\add_settings_field(
			'footer_builder_color_scheme',
			\esc_html__( 'Color Scheme', 'total' ),
			array( $this, 'footer_builder_color_scheme_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main',
			array(
				'label_for' => 'wpex-footer-builder-color-scheme',
			)
		);

		\add_settings_field(
			'footer_builder_footer_bottom',
			\esc_html__( 'Footer Bottom', 'total' ),
			array( $this, 'footer_builder_footer_bottom_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main',
			array(
				'label_for' => 'wpex-footer-builder-footer-bottom',
			)
		);

		\add_settings_field(
			'footer_builder_footer_widgets',
			\esc_html__( 'Footer Widgets', 'total' ),
			array( $this, 'footer_widgets_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main',
			array(
				'label_for' => 'wpex-footer-builder-widgets',
			)
		);

		\add_settings_field(
			'fixed_footer',
			\esc_html__( 'Fixed Footer', 'total' ),
			array( $this, 'fixed_footer_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main',
			array(
				'label_for' => 'wpex-footer-builder-fixed',
			)
		);

		\add_settings_field(
			'footer_reveal',
			\esc_html__( 'Footer Reveal', 'total' ),
			array( $this, 'footer_reveal_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main',
			array(
				'label_for' => 'wpex-footer-builder-reveal',
			)
		);

		\add_settings_field(
			'bg',
			\esc_html__( 'Background Color', 'total' ),
			array( $this, 'bg_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main',
			array(
				'label_for' => 'wpex-footer-builder-bg',
			)
		);

		\add_settings_field(
			'bg_img',
			\esc_html__( 'Background Image', 'total' ),
			array( $this, 'bg_img_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main',
			array(
				'label_for' => 'wpex-footer-builder-bg-img',
			)
		);

		\add_settings_field(
			'bg_img_style',
			\esc_html__( 'Background Image Style', 'total' ),
			array( $this, 'bg_img_style_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main',
			array(
				'label_for' => 'wpex-footer-builder-bg-style',
			)
		);
	}

	/**
	 * Save options.
	 */
	public function save_options( $options ): void {
		if ( ! isset( $_POST['totaltheme-footer-builder-admin-nonce'] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-footer-builder-admin-nonce'] ) ), 'totaltheme-footer-builder-admin' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		// Update footer builder page ID
		if ( ! empty( $options['content_id'] ) ) {
			\set_theme_mod( 'footer_builder_page_id', $options['content_id'] );
		} else {
			\remove_theme_mod( 'footer_builder_page_id' );
		}

		// Color scheme.
		if ( ! empty( $options['color_scheme'] ) ) {
			\set_theme_mod( 'footer_builder_color_scheme', $options['color_scheme'] );
		} else {
			\remove_theme_mod( 'footer_builder_color_scheme' );
		}

		// Footer Bottom - Disabled by default
		if ( empty( $options['footer_builder_footer_bottom'] ) ) {
			\remove_theme_mod( 'footer_builder_footer_bottom' );
		} else {
			\set_theme_mod( 'footer_builder_footer_bottom', 1 );
		}

		// Footer Widgets - Disabled by default
		if ( empty( $options['footer_builder_footer_widgets'] ) ) {
			\remove_theme_mod( 'footer_builder_footer_widgets' );
		} else {
			\set_theme_mod( 'footer_builder_footer_widgets', 1 );
		}

		// Update fixed footer - Disabled by default
		if ( empty( $options['fixed_footer'] ) ) {
			\remove_theme_mod( 'fixed_footer' );
		} else {
			\set_theme_mod( 'fixed_footer', 1 );
		}

		// Update footer Reveal - Disabled by default
		if ( empty( $options['footer_reveal'] ) ) {
			\remove_theme_mod( 'footer_reveal' );
		} else {
			\set_theme_mod( 'footer_reveal', true );
		}

		// Update bg
		if ( empty( $options['bg'] ) ) {
			\remove_theme_mod( 'footer_builder_bg' );
		} else {
			\set_theme_mod( 'footer_builder_bg', \sanitize_text_field( $options['bg'] ) );
		}

		// Update bg img
		if ( empty( $options['bg_img'] ) ) {
			\remove_theme_mod( 'footer_builder_bg_img' );
		} else {
			\set_theme_mod( 'footer_builder_bg_img', \sanitize_text_field( $options['bg_img'] ) );
		}

		// Update bg img style
		if ( empty( $options['bg_img_style'] ) ) {
			\remove_theme_mod( 'footer_builder_bg_img_style' );
		} else {
			\set_theme_mod( 'footer_builder_bg_img_style', \sanitize_text_field( $options['bg_img_style'] ) );
		}

	}

	/**
	 * Main Settings section callback.
	 */
	public function section_main_callback( $options ): void {}

	/**
	 * Fields callback functions.
	 */

	// Footer Builder Page ID
	public function content_id_field_callback(): void {
		$selected_template = \get_theme_mod( 'footer_builder_page_id' );
		$template_exists   = ( $selected_template && \get_post_status( $selected_template ) );

		totaltheme_call_non_static( 'Theme_Builder', 'template_select', [
			'id'            => 'wpex-footer-builder-select',
			'name'          => 'footer_builder[content_id]',
			'selected'      => $selected_template,
			'template_type' => 'footer',
		] );
		?>
		<br><br>
		<?php totaltheme_call_static( 'Helpers\Add_Template', 'render_form', 'footer', $template_exists ); ?>
		<div class="wpex-edit-template-links-ajax totaltheme-admin-button-group<?php echo ( ! $template_exists ) ? ' hidden' : ''; ?>" data-hide-rows="true"><?php $this->edit_links( $selected_template ); ?></div>
	<?php }

	/**
	 * Color sceheme field callback.
	 */
	public function footer_builder_color_scheme_field_callback(): void {
		$color_scheme = \get_theme_mod( 'footer_builder_color_scheme' );
		?>
		<select id="wpex-footer-builder-color-scheme" name="footer_builder[color_scheme]">
			<option value="" <?php \selected( $color_scheme, '' ); ?>><?php \esc_html_e( 'Default', 'total' );?></option>
			<?php foreach ( \totaltheme_get_color_schemes() as $scheme ) { ?>
				<option value="<?php echo \esc_attr( $scheme['id'] ); ?>" <?php \selected( $color_scheme, $scheme['id'] ); ?>><?php
					echo \esc_html( $scheme['name'] );
				?></option>
			<?php } ?>
		</select>
	<?php }

	/**
	 * Footer Bottom Callback.
	 */
	public function footer_builder_footer_bottom_field_callback(): void {
		$val = \get_theme_mod( 'footer_builder_footer_bottom', false ) ? 'on' : false;
		?>
		<input type="checkbox" name="footer_builder[footer_builder_footer_bottom]" id="wpex-footer-builder-footer-bottom" <?php \checked( $val, 'on' ); ?>>
		<?php
	}

	/**
	 * Fixed Footer Callback.
	 */
	public function fixed_footer_field_callback(): void {
		$val = \get_theme_mod( 'fixed_footer', false ) ? 'on' : false;
		?>
		<input type="checkbox" name="footer_builder[fixed_footer]" id="wpex-footer-builder-fixed" <?php \checked( $val, 'on' ); ?>>
		<?php
	}

	/**
	 * Footer Reveal Callback.
	 */
	public function footer_reveal_field_callback(): void {
		$val = \get_theme_mod( 'footer_reveal' ) ? 'on' : false;
		?>
		<input type="checkbox" name="footer_builder[footer_reveal]" id="wpex-footer-builder-reveal" <?php \checked( $val, 'on' ); ?>>
		<?php
	}

	/**
	 * Footer Widgets Callback.
	 */
	public function footer_widgets_field_callback(): void {
		$val = \get_theme_mod( 'footer_builder_footer_widgets', false ) ? 'on' : false;
		?>
		<input type="checkbox" name="footer_builder[footer_builder_footer_widgets]" id="wpex-footer-builder-widgets" <?php \checked( $val, 'on' ); ?>>
		<?php
	}

	/**
	 * Background Setting.
	 */
	public function bg_field_callback(): void {
		totaltheme_component( 'color', [
			'id'                 => 'wpex-footer-builder-bg',
			'value'              => ( $value = \get_theme_mod( 'footer_builder_bg' ) ) ? sanitize_text_field( $value ) : '',
			'input_name'         => 'footer_builder[bg]',
			'dropdown_placement' => 'right',
			'include'            => 'transparent,surface-1,surface-2,surface-3,surface-4',
		] );
	}

	/**
	 * Background Image Setting.
	 */
	public function bg_img_field_callback(): void {
		$bg = \get_theme_mod( 'footer_builder_bg_img' );
		?>
		<div class="uploader">
			<input id="wpex-footer-builder-bg-img" class="wpex-media-input" type="text" name="footer_builder[bg_img]" value="<?php echo \esc_attr( $bg ); ?>">
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
		$style = \get_theme_mod( 'footer_builder_bg_img_style' );
		?>
		<select id="wpex-footer-builder-bg-style" name="footer_builder[bg_img_style]">
			<?php foreach ( \wpex_get_bg_img_styles() as $key => $val ) { ?>
				<option value="<?php echo \esc_attr( $key ); ?>" <?php \selected( $style, $key, true ); ?>><?php echo \esc_html( $val ); ?></option>
			<?php } ?>
		</select>
	<?php }

	/**
	 * Settings page output.
	 */
	public function render_admin_page(): void {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
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
			$template_id = \get_theme_mod( 'footer_builder_page_id' );

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
			
			// Deleted template notice
			if ( $template_id && ( false === \get_post_status( $template_id ) || 'trash' === \get_post_status( $template_id ) ) ) {
				echo '<div class="notice notice-warning"><p>' . \esc_html__( 'It appears the page you had selected has been deleted, please re-save your settings to prevent issues.', 'total' ) . '</p></div>';
			} ?>

			<form method="post" action="options.php">
				<?php \settings_fields( 'wpex_footer_builder' ); ?>
				<?php \do_settings_sections( 'wpex-footer-builder-admin' ); ?>
				<?php \wp_nonce_field( 'totaltheme-footer-builder-admin', 'totaltheme-footer-builder-admin-nonce' ); ?>
				<?php \submit_button(); ?>
			</form>
		</div>
	<?php }

	/**
	 * Alter the footer.
	 */
	public function alter_footer(): void {
		$this->insert_hook     = \apply_filters( 'wpex_footer_builder_insert_hook', $this->insert_hook );
		$this->insert_priority = \apply_filters( 'wpex_footer_builder_insert_priority', $this->insert_priority );

		\add_action( $this->insert_hook, [ $this, 'get_part' ], $this->insert_priority );
	}

	/**
	 * Gets the footer builder template part if the footer is enabled.
	 */
	public function get_part(): void {
		if ( totaltheme_call_static( 'Footer\Core', 'is_enabled' ) ) {
			\get_template_part( 'partials/footer/footer-builder' );
		}
	}

	/**
	 * Custom CSS for footer builder.
	 */
	public function filter_wpex_head_css( string $css ): string {
		$add_css = '';

		// Custom background color.
		$bg = \get_theme_mod( 'footer_builder_bg' );
		if ( $bg && $bg_safe = \wpex_parse_color( $bg ) ) {
			$add_css .= "background-color:{$bg_safe};";
		}

		// Custom background image.
		$bg_img = \get_theme_mod( 'footer_builder_bg_img' );
		if ( $bg_img && $bg_img_safe = \esc_url( \wpex_get_image_url( $bg_img ) ) ) {
			$add_css .= "background-image:url({$bg_img_safe});";
		}

		// Custom background image style.
		// @todo update to use utility classes so that the fixed backgrounds don't get messed up on mobile.
		if ( $bg_img && $bg_img_style = \get_theme_mod( 'footer_builder_bg_img_style' ) ) {
			$add_css .= \wpex_sanitize_data( $bg_img_style, 'background_style_css' );
		}

		if ( $add_css ) {
			$css .= "/*FOOTER BUILDER*/#footer-builder{{$add_css}}";
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
	 * Returns footer template ID.
	 */
	public static function get_template_id(): int {
		return (int) totaltheme_call_static( 'Footer\Core', 'get_template_id' );
	}

	/**
	 * Custom CSS for footer builder.
	 */
	public function wpex_head_css() {
		\_deprecated_function( __METHOD__, 'Total Theme 6.0' );
	}

}

new FooterBuilder();
