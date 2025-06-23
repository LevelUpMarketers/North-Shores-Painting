<?php

namespace TotalTheme\Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * Add a new template.
 */
class Add_Template {

	/**
	 * The nonce field name.
	 */
	public const NONCE = 'totaltheme_add_template_nonce';

	/**
	 * The template ID for the created template.
	 */
	public $template_id = 0;

	/**
	 * Constructor.
	 */
	public function __construct( string $title = '', string $type = '' ) {
		if ( ! \current_user_can( 'publish_pages' ) || ! \is_admin() ) {
			return;
		}

		$this->template_id = \wp_insert_post( [
			'post_title'  => \sanitize_text_field( $title ),
			'post_status' => 'publish',
			'post_type'   => 'wpex_templates',
		] );

		if ( $this->template_id && $type && 'null' !== $type && 'none' !== $type ) {
			\update_post_meta( $this->template_id, 'wpex_template_type', \sanitize_text_field( $type ) );
		}
	}

	/**
	 * Helper function renders the add template form.
	 */
	public static function render_form( string $type = '', bool $hidden = false, string $nonce = '' ): void {
		if ( ! \post_type_exists( 'wpex_templates' ) || ! \current_user_can( 'publish_pages' ) ) {
			return;
		}

		if ( ! $nonce ) {
			$nonce = \wp_create_nonce( self::NONCE );
		}

		\wp_enqueue_script(
			'totaltheme-module-add-template',
			\totaltheme_get_js_file( 'module/add-template' ),
			[],
			\WPEX_THEME_VERSION,
			true
		);

		\wp_localize_script(
			'totaltheme-module-add-template',
			'totaltheme_module_add_template_vars',
			[
				'confirm' => \esc_html__( 'Template created. Click ok to refresh your page.', 'total' ),
				'error' => \esc_html__( 'Something wen\'t wrong, please try again or create your template via the WP dashboard.', 'total' ),
			]
		);

		?>

		<div class="totaltheme-add-template<?php echo $hidden ? ' hidden' : ''; ?>">
			<div class="totaltheme-add-template__form" data-nonce="<?php echo \esc_attr( $nonce ); ?>" data-type="<?php echo \esc_attr( $type ); ?>">
				<label for="totaltheme-add-template-input" class="screen-reader-text"><?php \esc_html_e( 'New template name', 'total' ); ?></label>
				<input type="text" id="totaltheme-add-template-input" class="totaltheme-add-template__name" placeholder="<?php \esc_html_e( 'Template name', 'total' ); ?>">
				<button class="totaltheme-add-template__save button"><?php \esc_html_e( 'Save', 'total' ); ?></button>
				<span class="totaltheme-add-template__spinner"><?php echo \totaltheme_get_loading_icon( 'wordpress' ); ?></span>
				<button class="totaltheme-add-template__cancel button"><span class="screen-reader-text"><?php \esc_html_e( 'Save', 'total' ); ?></span><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
			</div>
			<button class="totaltheme-add-template__toggle button button-secondary"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="currentColor"><path d="M2 12C2 6.44444 6.44444 2 12 2C17.5556 2 22 6.44444 22 12C22 17.5556 17.5556 22 12 22C6.44444 22 2 17.5556 2 12ZM13 11V7H11V11H7V13H11V17H13V13H17V11H13Z"></path></svg><?php \esc_html_e( 'New Template', 'total' ); ?></button>
		</div>

		<?php
	}

}
