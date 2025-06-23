<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Visibility Select Control.
 */
class Visibility_Select extends WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'totaltheme_visibility_select';

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$this->json['value'] = $this->value();
		$this->json['id']    = $this->id;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * Render the content
	 */
	public function content_template() {
		?>
		<# if ( data.label ) { #>
			<label for="_customize-input-{{ data.id }}" class="customize-control-title">{{ data.label }}</label>
		<# } #>

		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<select id="_customize-input-{{ data.id }}" data-customize-setting-link="{{ data.id }}">
		<?php foreach ( \totaltheme_get_visibility_choices() as $category => $category_args ) {
			if ( ! empty ( $category_args['choices'] ) ) {
				echo ' <optgroup label="' . \esc_attr( $category_args['label'] ?? '' ) . '">';
				foreach ( $category_args['choices'] as $choice_id => $choice_label ) {
					echo '<option value="' . \esc_attr( $choice_id ) . '"<# if ( "<?php echo esc_attr( $key ); ?>" === data.value ) { #> selected<# } #>>' . \esc_html( $choice_label ) . '</option>';
				}
				echo '</optgroup>';
			}
		} ?>
		</select>
		<?php
	}
}
