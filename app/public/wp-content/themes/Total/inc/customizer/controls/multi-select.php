<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Multi-Check Control.
 */
class Multi_Select extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_multi_select';

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$this->json['value']   = $this->get_parsed_value();
		$this->json['choices'] = $this->choices ?? [];
		$this->json['id']      = $this->id;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * Render the content
	 */
	public function content_template() { ?>
		<# if ( data.label ) { #>
			<div id="_customize-input-{{ data.id }}" class="customize-control-title">{{ data.label }}</div>
		<# } #>

		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<div role="group" class="totaltheme-customize-multi-select" aria-labelledby="_customize-input-{{ data.id }}">
			<# _.each( data.choices, function( label, key ) { #>
			<label for="{{ data.id }}_{{ key }}">
				<input class="totaltheme-customize-multi-select__input" id="{{ data.id }}_{{ key }}" type="checkbox" value="{{ key }}"<# if ( _.contains( data.value, key ) ) { #> checked<# } #>> {{ label }}</label><br>
			<# }); #>
		</div>

		<?php
	}

	/**
	 * Returns the parsed value.
	 */
	protected function get_parsed_value(): array {
		$value = $this->value();
		if ( ! \is_array( $value ) ) {
			$value = \explode( ',', $value );
		}
		return (array) $value;
	}

}
