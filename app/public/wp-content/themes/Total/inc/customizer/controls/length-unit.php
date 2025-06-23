<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

/**
 * Customizer Pixel Control
 */
class Length_Unit extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_length_unit';

	/**
	 * Units to select from.
	 */
	public $units = [];

	/**
	 * Excluded units
	 */
	public $exclude_units = [];

	/**
	 * Default unit.
	 */
	public $default_unit = null;

	/**
	 * Placeholder Text.
	 */
	public $placeholder = '';

	/**
	 * Allow numeric.
	 */
	public $allow_numeric = true;

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$parsed_value = $this->get_parsed_value();
		$this->units  = $this->parse_units();
		$value_type   = $this->get_value_type( $parsed_value );

		$this->json['value']        = $parsed_value;
		$this->json['value_type']   = $value_type;
		$this->json['input_value']  = $this->get_input_value( $parsed_value, $value_type );
		$this->json['unit_choices'] = $this->get_unit_choices();
		$this->json['placeholder']  = $this->placeholder;
		$this->json['id']           = $this->id;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * Render the content
	 */
	public function content_template() { ?>
		<#
		var inputTypeAttr = _.contains( [ 'int', 'px', 'em', 'rem', 'vw', 'vh', 'vmin', 'vmax', '%' ], data.value_type ) ? 'number' : 'text';
		#>

		<# if ( data.label ) { #>
			<label for="_customize-input-{{ data.id }}" class="customize-control-title">{{ data.label }}</label>
		<# } #>

		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<div class="totaltheme-customize-length-unit totaltheme-customize-length-unit--{{ data.value_type }}">
			<input id="_customize-input-{{ data.id }}" type="{{ inputTypeAttr }}" class="totaltheme-customize-length-unit__input" value="{{ data.input_value }}" placeholder="{{ data.placeholder }}" data-placeholder="{{ data.placeholder }}"<# if ( "number" === inputTypeAttr ) { #> inputmode="numeric"<# } #>>
			<span class="totaltheme-customize-length-unit__select-wrap">
				<select class="totaltheme-customize-length-unit__select" data-unit="{{ data.value_type }}"<# if ( 1 === _.size( data.unit_choices ) ) { #> disabled<# } #>>
				<# _.each( data.unit_choices, function( choice ) { #>
				<option value="{{ choice }}"<# if ( choice == data.value_type ) { #> selected<# } #>>{{ choice }}</option>
				<# }); #>
				</select>
			</span>
			<input type="hidden" value="{{ data.value }}">
		</div>

		<?php
	}

	/**
	 * Return value.
	 */
	protected function get_parsed_value(): string {
		$value = \trim( (string) $this->value() );

		// 0s not allowed only 0{unit} allowed.
		// Check if numerical values aren't allowed and the saved value is numerical.
		if ( ! $value || 0 === $value || '0' === $value || ( ! $this->allow_numeric && \is_numeric( $value ) ) ) {
			return '';
		}

		return $value;
	}

	/**
	 * Return input value.
	 */
	protected function get_input_value( $value, $type ): string {
		if ( ! $value ) {
			return '';
		}
		$func_types    = [ 'var', 'calc', 'clamp' ];
		$numeric_types = [ 'int', 'px', 'em', 'rem', 'vw', 'vh', 'vmin', 'vmax', '%' ];
		if ( \in_array( $type, $func_types, true ) ) {
			$value = \rtrim( \str_replace( "{$type}(", "", $value ), ")" );
		} elseif ( \in_array( $type, $numeric_types, true ) ) {
			$value = \floatval( $value );
		}
		return $value;
	}

	/**
	 * Return default units.
	 */
	protected function get_default_units(): array {
		return [ 'px', 'em', 'rem', 'vw', 'vh', 'vmin', 'vmax', '%', 'var', 'func' ];
	}

	/**
	 * Parses units.
	 */
	protected function parse_units(): array {
		if ( ! $this->units ) {
			$this->units = $this->get_default_units();
		}
		if ( $this->exclude_units ) {
			$this->units = \array_diff( $this->units, $this->exclude_units );
		}
		return $this->units;
	}

	/**
	 * Returns the default unit.
	 */
	protected function get_default_unit() {
		if ( null === $this->default_unit ) {
			if ( 1 === \count( $this->units ) ) {
				$this->default_unit = $this->units[0];
			} else {
				$this->default_unit = 'px';
			}
		}
		return $this->default_unit;
	}

	/**
	 * Get type of value.
	 */
	protected function get_value_type( $value ): string {
		if ( ! $value ) {
			return $this->get_unit_choices()[0] ?? $this->get_default_units()[0] ?? 'std';
		}

		if ( \is_numeric( $value ) && \in_array( 'int', $this->units, true ) ) {
			return 'int';
		} elseif ( \str_starts_with( $value, 'var(' ) ) {
			$type = 'var';
		} elseif ( \str_starts_with( $value, 'clamp(' ) ) {
			$type = 'func';
		} elseif ( \str_starts_with( $value, 'calc(' ) ) {
			$type = 'func';
		} else {
			$type = $this->get_value_unit( $value ) ?: $this->get_default_unit();
		}

		if ( ! \in_array( $type, $this->units ) && \in_array( 'func', $this->units, true ) ) {
			$type = 'func';
		}

		return $type ?: 'std';
	}

	/**
	 * Return unit from value.
	 */
	protected function get_value_unit( $input ) {
		if ( $input && \is_string( $input ) && ! \is_numeric( $input ) ) {
			$non_numeric_string = \preg_replace( '/[^0-9.]/', '', $input );
			$unit = \str_replace( $non_numeric_string, '', $input );
			return \trim( $unit );
		}
	}

	/**
	 * Return css unit from input.
	 */
	protected function get_unit_choices(): array {
		if ( $this->default_unit ) {
			\array_unshift( $this->units, $this->default_unit );
			$this->units = \array_unique( $this->units );
		}
		return $this->units;
	}

}
