<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

/**
 * Customizer TRBL Control.
 */
class Top_Right_Bottom_Left extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_trbl';

	/**
	 * Check if the output should be in shorthand format.
	 */
	public $shorthand = false;

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$this->json['value']       = $this->get_parsed_value();
		$this->json['shorthand']   = $this->shorthand ? 'true' : 'false';
		$this->json['description'] = $this->get_description();
		$this->json['id']          = $this->id;
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
			<div class="customize-control-title">{{ data.label }}</div>
		<# } #>

		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<div class="totaltheme-customize-trbl" data-wpex-shorthand="{{ data.shorthand }}">
			<?php
			$directions = [
				'top'    => '&uarr; ' . \esc_html__( 'Top', 'total' ),
				'right'  => \esc_html__( 'Right', 'total' ) . ' &rarr;',
				'bottom' => '&darr; ' .\esc_html__( 'Bottom', 'total' ),
				'left'   => '&larr; ' . \esc_html__( 'Left', 'total' ),
			];
			foreach ( $directions as $key => $label ) : ?>
			<div class="totaltheme-customize-trbl__item">
				<div>
				<input id="{{ data.id }}_<?php echo \esc_attr( $key ); ?>" class="totaltheme-customize-trbl__input" data-name="<?php echo \esc_attr( $key ); ?>" value="{{ data.value.<?php echo esc_attr( $key ); ?> }}" type="text" placeholder="-">
				</div>
				<label class="totaltheme-customize-trbl__label" for="{{ data.id }}_<?php echo \esc_attr( $key ); ?>"><?php echo \esc_attr( $label ); ?></label>
			</div>
			<?php endforeach; ?>
		</div>
		<input type="hidden" value="{{ data.value }}">
		<?php
	}

	/**
	 * Returns control description.
	 */
	protected function get_description() {
		if ( ! empty( $this->description ) ) {
			return $this->description;
		} elseif ( $this->shorthand ) {
			return \esc_html__( 'This field uses a shorthand format so any empty field will be treated as 0px, however, if all fields are empty it will use the default theme styles.', 'total' );
		} else {
			return \esc_html__( 'The theme will use a px unit if a unit is not provided.', 'total' );
		}
	}

	/**
	 * Parses the value to convert into array of top/right/bottom/left.
	 */
	protected function get_parsed_value(): array {
		$val = $this->value();
		if ( ! $this->shorthand && \str_contains( $val, ':' ) ) {
			return $this->parse_multi_prop_val( $val );
		}
		return $this->parse_single_prop_val( $val );
	}

	/**
	 * Parses a multi-property attribute value.
	 */
	protected function parse_multi_prop_val( $val = '' ): array {
		$new_val = [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		];
		$params_pairs = \explode( '|', $val );
		if ( ! empty( $params_pairs ) ) {
			foreach ( $params_pairs as $pair ) {
				$param = \preg_split( '/\:/', $pair );
				if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
					$new_val[ $param[0] ] = $param[1];
				}
			}
		}
		return $new_val;
	}

	/**
	 * Parses a single-property attribute.
	 */
	protected function parse_single_prop_val( $val = '' ): array {
		$val = \trim( $val );

		$new_val = [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		];

		if ( ! $val ) {
			return $new_val;
		}

		$array = \explode( ' ', $val );

		if ( ! $array ) {
			return $new_val;
		}

		$count = \count( $array );

		if ( 1 === $count ) {
			foreach ( $new_val as $key => $val ) {
				$new_val[$key] = $array[0];
			}
		} else {
			if ( 2 === $count ) {
				$new_val['top']    = $array[0];
				$new_val['bottom'] = $array[0];
				$new_val['right']  = $array[1] ?? '';
				$new_val['left']   = $array[1] ?? '';
			} else {
				$new_val['top']    = $array[0];
				$new_val['right']  = $array[1] ?? '';
				$new_val['bottom'] = $array[2];
				$new_val['left']   = $array[3] ?? '';
			}
		}

		return $new_val;
	}

	/**
	 * Return css unit (text) from input.
	 */
	protected function get_unit( $input = '' ) {
		if ( $input && \is_string( $input ) && ! \is_numeric( $input ) ) {
			$non_numeric_string = \preg_replace( '/[^0-9.]/', '', $input );
			$unit = \str_replace( $non_numeric_string, '', $input );
			return \trim( $unit );
		}
	}

}
