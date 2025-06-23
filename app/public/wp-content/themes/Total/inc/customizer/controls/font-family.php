<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Font Family Control.
 */
class Font_Family extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_font_family';

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$this->json['value'] = $this->value();
		$this->json['id'] = $this->id;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * Render the content
	 */
	public function content_template() { ?>
		<# var valueExists = false; #>

		<# if ( data.label ) { #>
			<label for="_customize-input-{{ data.id }}" class="customize-control-title">{{ data.label }}</label>
		<# } #>

		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<div class="total-customize-chosen-wrap">
			<select id="_customize-input-{{ data.id }}" data-customize-setting-link="{{ data.id }}">
				<option value="" <# if ( ! data.value ) { #> selected<# } #>><?php \esc_html_e( 'Default', 'total' ); ?></option>

				<?php
				$user_fonts = \wpex_get_registered_fonts();
				if ( ! empty( $user_fonts ) ) { ?>
				<optgroup label="<?php \esc_html_e( 'My Fonts', 'total' ); ?>">
					<?php foreach ( $user_fonts as $font_name => $font_settings ) { ?>
						<option value="<?php echo \esc_attr( $font_name ); ?>" <# if ( "<?php echo esc_attr( $font_name ); ?>" === data.value ) { valueExists = true; #> selected<# } #>><?php echo \ucfirst( \esc_html( $font_name ) ); ?></option>
					<?php } ?>
				</optgroup>
				<?php } ?>

				<?php
				$custom_fonts = \wpex_add_custom_fonts();
				if ( $custom_fonts && \is_array( $custom_fonts ) ) { ?>
				<optgroup label="<?php \esc_html_e( 'Custom Fonts', 'total' ); ?>">
					<?php foreach ( $custom_fonts as $font ) { ?>
						<option value="<?php echo \esc_attr( $font ); ?>"<# if ( "<?php echo esc_attr( $font ); ?>" === data.value ) { valueExists = true; #> selected<# } #>><?php echo \ucfirst( \esc_html( $font ) ); ?></option>
					<?php } ?>
				</optgroup>
				<?php } ?>

				<?php
				// Add Standard font options.
				if ( $std_fonts = \wpex_standard_fonts() ) { ?>
				<optgroup label="<?php \esc_html_e( 'Standard Fonts', 'total' ); ?>">
					<?php foreach ( $std_fonts as $font ) {  ?>
						<option value="<?php echo \esc_attr( $font ); ?>"<# if ( "<?php echo esc_attr( $font ); ?>" === data.value ) { valueExists = true; #> selected<# } #>><?php echo \esc_html( $font ); ?></option>
					<?php } ?>
				</optgroup>
				<?php } ?>

				<?php
				// Show Google font options if the user hasn't added any custom fonts.
				if ( ! $user_fonts && $google_fonts = \wpex_google_fonts_array() ) { ?>
				<optgroup label="<?php \esc_html_e( 'Google Fonts', 'total' ); ?>">
					<?php foreach ( $google_fonts as $font ) { ?>
						<option value="<?php echo \esc_attr( $font ); ?>"<# if ( "<?php echo esc_attr( $font ); ?>" === data.value ) { valueExists = true; #> selected<# } #>><?php echo \esc_html( $font ); ?></option>
					<?php } ?>
				</optgroup>
				<?php } ?>

				<# if ( data.value && ! valueExists ) { #>
				<optgroup label="<?php \esc_html_e( 'Non Registered Fonts', 'total' ); ?>">
					<option value="{{ data.value }}" selected="selected">{{ data.value }}</option>
				</optgroup>
				<# } #>
			</select>
		</div>
	<?php }
}
