<?php

namespace TotalTheme\Customizer\Controls;

use WP_Query;
use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Templates Select Control.
 */
class Template_Select extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_template_select';

	/**
	 * Template type.
	 */
	public $template_type = 'all';

	/**
	 * Render the content
	 */
	public function render_content() {
		$selected_template = $this->value();
		$input_id          = "_customize-input-{$this->id}";
		$description_id    = "_customize-description-{$this->id}";
		$describedby       = ! empty( $this->description ) ? $description_id : '';
		?>

		<?php if ( ! empty( $this->label ) ) : ?>
			<label for="<?php echo \esc_attr( $input_id ); ?>" class="customize-control-title"><?php echo \esc_html( $this->label ); ?></label>
		<?php endif; ?>

		<?php if ( ! empty( $this->description ) ) : ?>
			<span id="<?php echo \esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo \wp_kses_post( $this->description ); ?></span>
		<?php endif; ?>

		<div class="total-customize-chosen-wrap">

		<?php
		$dropdown = totaltheme_call_non_static( 'Theme_Builder', 'template_select', [
			'id'            => $input_id,
			'selected'      => $selected_template,
			'template_type' => $this->template_type,
			'describedby'   => $describedby,
			'echo'          => false,
		] );

		// Hackily add in the data link parameter.
		if ( $dropdown ) {
			$dropdown = \str_replace( '<select', '<select ' . $this->get_link(), $dropdown );
		}

		 // @codingStandardsIgnoreLine
		echo $dropdown;

		// Display the ajaxed create template buttons.
		if ( \post_type_exists( 'wpex_templates' )
			&& \current_user_can( 'publish_pages' )
			&& \current_user_can( 'edit_theme_options' ) )
		{
			?>
			<div class="totaltheme-customize-create-template"<?php echo ( $selected_template ) ? ' style="display:none"' : ''; ?>>
				<div class="totaltheme-customize-create-template__form" data-wpex-template-type="<?php echo \esc_attr( $this->template_type ); ?>">
					<label for="totaltheme-customize-create-template-input-<?php echo \esc_attr( $this->id ); ?>" class="screen-reader-text"><?php \esc_html_e( 'New template name', 'total' ); ?></label>
					<input type="text" id="totaltheme-customize-create-template-input-<?php echo esc_attr( $this->id ); ?>" class="totaltheme-customize-create-template__name" placeholder="<?php \esc_html_e( 'Template name', 'total' ); ?>">
					<button class="totaltheme-customize-create-template__save button"><?php \esc_html_e( 'Save', 'total' ); ?></button>
					<span class="totaltheme-customize-create-template__spinner"><?php echo \totaltheme_get_loading_icon( 'wordpress' ); ?></span>
					<button class="totaltheme-customize-create-template__cancel button"><span class="screen-reader-text"><?php \esc_html_e( 'Save', 'total' ); ?></span><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
				</div>
				<button type="button" class="totaltheme-customize-create-template__add-item button button-primary"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="currentColor"><path d="M2 12C2 6.44444 6.44444 2 12 2C17.5556 2 22 6.44444 22 12C22 17.5556 17.5556 22 12 22C6.44444 22 2 17.5556 2 12ZM13 11V7H11V11H7V13H11V17H13V13H17V11H13Z"></path></svg><?php \esc_html_e( 'New Template', 'total' ); ?></button>
			</div>
			<?php
			}
		?>
		</div>
	<?php }

}
