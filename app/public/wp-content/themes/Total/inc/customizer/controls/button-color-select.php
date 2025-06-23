<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Select Control.
 */
class Button_Color_Select extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_button_color';

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
	 * The control template.
	 */
	public function content_template() { ?>
		<# if ( data.label ) { #>
			<label for="_customize-input-{{ data.id }}" class="customize-control-title">{{ data.label }}</label>
		<# } #>

		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<select id="_customize-input-{{ data.id }}" data-customize-setting-link="{{ data.id }}">
		<?php foreach ( \wpex_get_accent_colors() as $key => $settings ) {
			if ( empty( $settings['label'] ) ) {
				continue;
			}
			if ( 'default' === $key ) { ?>
			<option value=""<# if ( ! data.value ) { #> selected<# } #>><?php echo \esc_attr( $settings['label'] ); ?></option>
			<?php } else { ?>
			<option value="<?php echo \esc_attr( $key ); ?>"<# if ( '<?php echo esc_attr( $key ); ?>' === data.value ) { #> selected<# } #>><?php echo \esc_html( $settings['label'] ); ?></option>
			<?php }
		} ?>
		</select>
	<?php }

}
