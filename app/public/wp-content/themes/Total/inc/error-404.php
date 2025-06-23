<?php

namespace TotalTheme;

use WP_Query;

\defined( 'ABSPATH' ) || exit;

/**
 * Error 404 Class.
 */
final class Error_404 {

	/**
	 * Class instance.
	 */
	private static $instance = null;

	/**
	 * Holds the 404 page template ID.
	 */
	protected static $template_id;

	/**
	 * Create or retrieve the instance of Error_404.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		if ( ! $this->is_custom_enabled() ) {
			return;
		}
		if ( \wpex_is_request( 'admin' ) ) {
			$this->admin_init();
		}
		if ( \wpex_is_request( 'frontend' ) ) {
			\add_action( 'init', [ $this, 'on_init' ] );
		}
	}

	/**
	 * Checks if the custom 404 page is enabled.
	 */
	public function is_custom_enabled(): bool {
		return \wp_validate_boolean( \get_theme_mod( 'custom_404_enable', true ) );
	}

	/**
	 * Checks if the 404 has the page header title enabled.
	 */
	public function is_page_header_enabled(): bool {
		return $this->is_custom_enabled() ? \wp_validate_boolean( \get_theme_mod( 'error_page_has_page_header', true ) ) : true;
	}

	/**
	 * Admin hooks.
	 */
	public function admin_init() {
		\add_action( 'admin_menu', [ $this, 'add_submenu_page' ] );
		\add_action( 'admin_init', [ $this, 'register_page_options' ] );

		if ( \current_user_can( 'edit_posts' ) ) {
			\add_action( 'wp_ajax_wpex_error_404_edit_links', array( $this, 'ajax_edit_links' ) );
		}
	}

	/**
	 * Front-end hooks.
	 */
	public function on_init() {
		if ( $template_id = $this->get_template_id() ) {
			if ( 'page' === get_post_type( $template_id ) ) {
				\add_filter( 'wpex_current_post_id', [ $this, 'post_id' ] );
				\add_filter( 'wp_robots', [ $this, 'filter_wp_robots' ] );
				if ( \did_action( 'wpseo_loaded' ) ) {
					totaltheme_init_class( 'Integration\Yoast_SEO\Helpers\Exclude_From_Sitemap', $template_id );
				}
			} else {
				\add_filter( 'wpex_has_primary_bottom_spacing', [ $this, 'remove_primary_spacing' ] );
			}
		}
	}

	/**
	 * Add sub menu page for the custom CSS input.
	 */
	public function add_submenu_page() {
		\add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_html__( 'Custom 404', 'total' ),
			\esc_html__( 'Custom 404', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG . '-404',
			[ $this, 'render_admin_page' ]
		);
	}

	/**
	 * Function that will register admin page options.
	 */
	public function register_page_options() {
		\register_setting(
			'wpex_error_page',
			'error_page',
			array(
				'sanitize_callback' => [ $this, 'save_options' ],
				'default' => null,
			)
		);

		\add_settings_section(
			'wpex_error_page_main',
			false,
			[ $this, 'section_main_callback' ],
			'wpex-custom-error-page-admin'
		);

		\add_settings_field(
			'error_page_id',
			\esc_html__( 'Template', 'total' ),
			[ $this, 'content_id_field_callback' ],
			'wpex-custom-error-page-admin',
			'wpex_error_page_main',
			[
				'label_for' => 'wpex-field-page_id',
			]
		);

		\add_settings_field(
			'error_page_layout',
			\esc_html__( 'Page Layout', 'total' ),
			[ $this, 'layout_field_callback' ],
			'wpex-custom-error-page-admin',
			'wpex_error_page_main',
			[
				'label_for' => 'wpex-field-layout',
			]
		);

		\add_settings_field(
			'error_page_use_blank_template',
			\esc_html__( 'Use Blank Template', 'total' ),
			[ $this, 'use_blank_template_field_callback' ],
			'wpex-custom-error-page-admin',
			'wpex_error_page_main',
			[
				'label_for' => 'wpex-field-use_blank_template',
			]
		);

		\add_settings_field(
			'error_page_has_page_header',
			\esc_html__( 'Page Header Title', 'total' ),
			[ $this, 'has_page_header_field_callback' ],
			'wpex-custom-error-page-admin',
			'wpex_error_page_main',
			[
				'label_for' => 'wpex-field-has_page_header',
				'class' => 'form-table__tr--has_page_header',
			]
		);

		\add_settings_field(
			'error_page_title',
			\esc_html__( 'Custom Title', 'total' ),
			[ $this, 'title_field_callback' ],
			'wpex-custom-error-page-admin',
			'wpex_error_page_main',
			[
				'label_for' => 'wpex-field-page_title',
				'class' => 'form-table__tr--custom_title',
			]
		);

		\add_settings_field(
			'error_page_text',
			\esc_html__( 'Custom Content', 'total' ),
			[ $this, 'content_field_callback' ],
			'wpex-custom-error-page-admin',
			'wpex_error_page_main',
			[
				'label_for' => 'wpex-field-page_text',
				'class' => 'form-table__tr--custom_content',
			]
		);
	}

	/**
	 * Save options.
	 */
	public function save_options( $options ) {
		if ( ! isset( $_POST['totaltheme-error-404-admin-nonce'] )
			|| ! wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-error-404-admin-nonce'] ) ), 'totaltheme-error-404-admin' )
			|| ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		if ( ! empty( $options['layout'] ) ) {
			\set_theme_mod( 'error_page_layout', \sanitize_text_field( $options['layout'] ) );
		} else {
			\remove_theme_mod( 'error_page_layout' );
		}

		if ( ! empty( $options['use_blank_template'] ) ) {
			\set_theme_mod( 'error_page_use_blank_template', true );
		} else {
			\remove_theme_mod( 'error_page_use_blank_template' );
		}

		if ( empty( $options['has_page_header'] ) ) {
			\set_theme_mod( 'error_page_has_page_header', false );
		} else {
			\remove_theme_mod( 'error_page_has_page_header' );
		}

		if ( ! empty( $options['title'] ) ) {
			\set_theme_mod( 'error_page_title', \sanitize_text_field( $options['title'] ) );
		} else {
			\remove_theme_mod( 'error_page_title' );
		}

		if ( ! empty( $options['text'] ) ) {
			\set_theme_mod( 'error_page_text', \wp_kses_post( $options['text'] ) );
		} else {
			\remove_theme_mod( 'error_page_text' );
		}

		if ( ! empty( $options['content_id'] ) ) {
			\set_theme_mod( 'error_page_content_id', \absint( $options['content_id'] ) );
		} else {
			\remove_theme_mod( 'error_page_content_id' );
		}

		return; // Don't actually save as an option since we are using mods.
	}

	/**
	 * Main Settings section callback.
	 */
	public function section_main_callback( $options ) {
		// Leave blank
	}

	/**
	 * Fields callback functions.
	 */

	// Custom Error Page ID.
	public function content_id_field_callback() {
		$selected_template = \get_theme_mod( 'error_page_content_id' );
		$template_exists   = ( $selected_template && \get_post_status( $selected_template ) );

		if ( $theme_builder = totaltheme_get_instance_of( 'Theme_Builder' ) ) {
			$theme_builder->template_select( [
				'id'            => 'wpex-field-page_id',
				'name'          => 'error_page[content_id]',
				'selected'      => $selected_template,
				'template_type' => 'error_404',
			] );
		}
		
		?>

		<br><br>

		<?php totaltheme_call_static( 'Helpers\Add_Template', 'render_form', 'error_404', $template_exists ); ?>
		<span class="wpex-edit-template-links-spinner hidden"><?php echo \totaltheme_get_loading_icon( 'wordpress' ); ?></span>
		<div class="wpex-edit-template-links-ajax totaltheme-admin-button-group<?php echo ( ! $template_exists ) ? ' hidden' : ''; ?>" data-nonce="<?php echo \wp_create_nonce( 'wpex_error_404_edit_links_nonce' ); ?>" data-action="wpex_error_404_edit_links"><?php $this->edit_links( $selected_template ); ?></div>

	<?php }

	/**
	 *  Layout Field.
	 */
	public function layout_field_callback() {
		$layout = \get_theme_mod( 'error_page_layout' );
		?>
		<select type="text" name="error_page[layout]" id="wpex-field-page_layout">
			<?php foreach ( \wpex_get_post_layouts() as $k => $v ) { ?>
				<option value="<?php echo \esc_attr( $k ); ?>" <?php \selected( $k, $layout, true ); ?>><?php echo \esc_html( $v ); ?></option>
			<?php } ?>
		</select>
	<?php }

	/**
	 *  Use Blank Template field.
	 */
	public function use_blank_template_field_callback() {
		$check = \wp_validate_boolean( \get_theme_mod( 'error_page_use_blank_template', false ) );

		?>
		<span class="totaltheme-admin-checkbox">
			<input type="checkbox" name="error_page[use_blank_template]" id="wpex-field-use_blank_template" <?php \checked( $check, true, true ); ?>>
			<span class="totaltheme-admin-checkbox__track"></span>
			<span class="totaltheme-admin-checkbox__thumb"></span>
		</span>
		<p class="description"><?php \esc_html_e( 'This will remove all parts of the site (top bar, header, callout, footer) exept your template content.', 'total' ) ?></p>
	<?php }

	/**
	 *  Has Page Header Field.
	 */
	public function has_page_header_field_callback() {
		$check = \wp_validate_boolean( \get_theme_mod( 'error_page_has_page_header', true ) );

		?>
		<span class="totaltheme-admin-checkbox">
			<input type="checkbox" name="error_page[has_page_header]" id="wpex-field-has_page_header" <?php \checked( $check, true, true ); ?>>
			<span class="totaltheme-admin-checkbox__track"></span>
			<span class="totaltheme-admin-checkbox__thumb"></span>
		</span>
		<p class="description"><?php \esc_html_e( 'Note: If the page header title is disabled globally this setting will be ignored.', 'total' ) ?></p>
	<?php }

	/**
	 *  Title Field.
	 */
	public function title_field_callback() { ?>
		<input type="text" name="error_page[title]" id="wpex-field-page_title" value="<?php echo \get_theme_mod( 'error_page_title' ); ?>">
		<p class="description"><?php \esc_html_e( 'Enter a custom title for your 404 page.', 'total' ) ?></p>
	<?php }

	/**
	 *  Content Field.
	 */
	public function content_field_callback() {
		$text = \get_theme_mod( 'error_page_text' );
		$text_safe = \wp_kses_post( $text );
		\wp_editor( $text_safe, 'wpex-field-page_text', [
			'textarea_name' => 'error_page[text]'
		] );
	}

	/**
	 * Settings page output.
	 */
	public function render_admin_page() {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		\wp_enqueue_style( 'totaltheme-admin-pages' );

		\wp_enqueue_script(
			'totaltheme-admin-custom-404',
			\totaltheme_get_js_file( 'admin/custom-404' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION
		);

		?>

		<div class="wrap totaltheme-admin-wrap">
			<form method="post" action="options.php">
				<?php \settings_fields( 'wpex_error_page' ); ?>
				<?php \do_settings_sections( 'wpex-custom-error-page-admin' ); ?>
				<?php \wp_nonce_field( 'totaltheme-error-404-admin', 'totaltheme-error-404-admin-nonce' ); ?>
				<?php \submit_button(); ?>
			</form>
		</div>
	<?php }

	/**
	 * Hooks into "wp_robots" to add the noindex tag to the custom 404 page.
	 */
	public function filter_wp_robots( $robots ) {
		$template_id = $this->get_template_id();
		if ( $template_id && \is_page( $template_id ) && $template_id === \get_queried_object_id() ) {
			$robots['noindex'] = true;
		}
		return $robots;
	}

	/**
	 * Remove the spacing on the #primary element.
	 */
	public function remove_primary_spacing( $check ): bool {
		if ( \is_404() ) {
			$check = false;
		}
		return $check;
	}

	/**
	 * Custom VC CSS for 404 custom page design.
	 */
	public function post_id( $post_id ) {
		if ( \is_404() && $error_page_id = $this->get_template_id() ) {
			$post_id = $error_page_id;
		}
		return $post_id;
	}

	/**
	 * Returns inline CSS for the 404 page.
	 */
	public function get_template_id() {
		if ( ! \is_null( self::$template_id ) ) {
			return self::$template_id;
		}
		$template_id = \absint( \get_theme_mod( 'error_page_content_id' ) );
		if ( ! $template_id ) {
			self::$template_id = 0;
			return self::$template_id;
		}
		$template_id   = \wpex_parse_obj_id( $template_id, 'page' );
		$template_post = \get_post( $template_id );
		if ( \is_a( $template_post, 'WP_Post' ) && 'publish' === \get_post_status( $template_post ) ) {
			self::$template_id = \absint( $template_id );
			return $template_id;
		}
	}

	/**
	 * Returns inline CSS for the 404 page.
	 */
	protected function get_template_content(): ?string {
		$template_id = $this->get_template_id();

		if ( ! $template_id ) {
			return null;
		}

		if ( $translated_id = \wpex_parse_obj_id( $template_id, 'page' ) ) {
			$post = \get_post( $translated_id );
		}

		$post = $post ?? \get_post( $template_id );

		if ( $post && 'publish' === \get_post_status( $post ) && ! empty( $post->post_content ) ) {
			return (string) $post->post_content;
		}

		return null;
	}

	/**
	 * Returns inline CSS for the 404 page.
	 */
	protected function get_template_css() {
		$css         = '';
		$template_id = $this->get_template_id();

		if ( \WPEX_VC_ACTIVE ) {

			// The CSS added to the page.
			$post_css = \get_post_meta( $template_id, '_wpb_post_custom_css', true );
			$post_css = (string) \apply_filters( 'vc_post_custom_css', $post_css, $template_id );

			if ( $post_css ) {
				$css .= $post_css;
			}

			// The CSS generated by shortcodes.
			$shortcode_css = \get_post_meta( $template_id, '_wpb_shortcodes_custom_css', true );
			$shortcode_css = (string) \apply_filters( 'vc_shortcodes_custom_css', $shortcode_css, $template_id );

			if ( $shortcode_css ) {
				$css .= $shortcode_css;
			}

		}

		if ( $css && $css_safe = \wp_strip_all_tags( $css ) ) {
			return "<style>{$css_safe}</style>";
		}
	}

	/**
	 * Gets the content for the custom 404 page.
	 */
	protected function get_the_content() {
		if ( $template_id = $this->get_template_id() ) {
			if ( 'elementor' === \totaltheme_get_post_builder_type( $template_id ) ) {
				return \wpex_get_elementor_content_for_display( $template_id );
			} elseif ( $template_content = $this->get_template_content() ) {
				$content = \totaltheme_render_content( $template_content );
				if ( $template_css = $this->get_template_css() ) {
					$content = $template_css . $content;
				}
				return $content;
			}
		}

		// Custom Text.
		$error_text = (string) \wpex_get_translated_theme_mod( 'error_page_text' );

		if ( $error_text ) {
			return '<div class="custom-error404-content wpex-clr">' . \totaltheme_render_content( trim( $error_text ), 'error404' ) .'</div>';
		}

		// Default text.
		return '<div class="error404-content wpex-text-center wpex-py-30 wpex-clr"><h1 class="error404-content-heading wpex-m-0 wpex-mb-10 wpex-text-3xl">' . \esc_html__( 'Sorry, this page could not be found.', 'total' ) . '</h1><div class="error404-content-text wpex-text-md wpex-last-mb-0">' . \esc_html__( 'The page you are looking for doesn\'t exist, no longer exists or has been moved.', 'total' ) . '</div></div>';
	}

	/**
	 * Renders the custom 404 page.
	 *
	 * @return string
	 */
	public function render() {
		// @codingStandardsIgnoreLine
		echo $this->get_the_content();
	}

	/**
	 * Returns custom 404 page content.
	 *
	 * @deprecated 5.6.1
	 */
	public static function get_content() {
		\ob_start();
			self::instance()->render();
		return \ob_get_clean();
	}

	/**
	 * Get edit links.
	 */
	public function edit_links( $template_id = '' ) {
		if ( ! $template_id ) {
			return;
		}
		$template_type = \get_post_type( $template_id );
		$edit_link = \get_edit_post_link( $template_id );
		if ( ! $edit_link ) {
			return;
		}
		?>
		<a href="<?php echo \esc_url( $edit_link ); ?>" target="_blank" rel="noopener noreferrer" class="button"><?php \esc_html_e( 'Backend Edit', 'total' ); ?> &#8599;</a>
		<?php if ( \defined( 'WPEX_VC_ACTIVE' ) && \WPEX_VC_ACTIVE && \in_array( $template_type, [ 'templatera', 'wpex_templates' ], true ) ) { ?>
			<a href="<?php echo \esc_url( \admin_url( 'post.php?vc_action=vc_inline&post_id=' . \absint( $template_id ) . '&post_type=' . \get_post_type( $template_id ) ) ); ?>" target="_blank" rel="noopener noreferrer" class="button"><?php \esc_html_e( 'Frontend Edit', 'total' ); ?> &#8599;</a>
		<?php } ?>
	<?php }

	/**
	 * Return correct edit links.
	 */
	public function ajax_edit_links() {
		check_ajax_referer( 'wpex_error_404_edit_links_nonce', 'nonce' );

		$this->edit_links( \absint( $_POST['template_id'] ) );

		\wp_die();
	}

}
