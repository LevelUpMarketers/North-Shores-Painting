<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

/**
 * Customizer Toggle Control.
 */
class Toggle extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_toggle';

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$this->json['value'] = (bool) $this->value();
		$this->json['id']    = $this->id;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * Render the content
	 */
	public function content_template() { ?>
		<div class="totaltheme-customize-toggle-control<# if ( data.value ) { #> totaltheme-customize-toggle-control--checked<# } #>">
			<# if ( data.label ) { #>
				<label for="_customize-input-{{ data.id }}" class="customize-control-title">{{ data.label }}</label>
			<# } #>
			<span class="totaltheme-customize-toggle-control__btn">
				<input id="_customize-input-{{ data.id }}" data-customize-setting-link="{{ data.id }}" type="checkbox"<# if ( data.value ) { #> checked<# } #>>
				<span class="totaltheme-customize-toggle-control__track"></span>
				<span class="totaltheme-customize-toggle-control__thumb"></span>
			</span>
		</div>
		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<?php
	}
}
