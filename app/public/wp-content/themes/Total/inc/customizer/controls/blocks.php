<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Blocks Control.
 *
 * @todo convert to js template.
 */
class Blocks extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_blocks';

	/**
	 * Enque scripts.
	 */
	public function enqueue() {
		\wp_enqueue_script( 'jquery-ui-core' );
		\wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$this->json['value']   = $this->get_parsed_value();
		$this->json['choices'] = $this->parse_choices( $this->choices ?? [] );
		$this->json['id']      = $this->id;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * The control template.
	 */
	public function content_template() {

		?>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{{data.label}}}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<div class="totaltheme-customize-blocks<# if ( data.value ) { #> totaltheme-customize-blocks--has-blocks<# } #>">

			<div class="totaltheme-customize-blocks__add-new">
				<select class="totaltheme-customize-blocks__select">
					<option value=""><?php \esc_html_e( 'Select Item', 'total' ); ?></option>
					<# _.each( data.choices, function( label, key ) { #>
						<option value="{{ key }}">{{{ label }}}</option>
					<# }) #>
				</select>
				<button type="button" class="totaltheme-customize-blocks__add-button button button-secondary">+ <?php \esc_html_e( 'Add', 'total' ); ?></button>
			</div>

			<div class="totaltheme-customize-blocks__template">
				<div class="totaltheme-customize-blocks__item-name"></div>
				<button class="totaltheme-customize-blocks__delete-item"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span><span class="screen-reader-text"><?php \esc_html_e( 'delete item', 'total' ); ?></span></button>
			</div>

			<div class="totaltheme-customize-blocks__list">
				<# _.each( data.value, function( key ) {
					var label = data.choices[key] || key;
				#>
					<div class="totaltheme-customize-blocks__item" data-wpex-key="{{{ key }}}">
						<div class="totaltheme-customize-blocks__item-name">{{{ label }}}</div>
						<button class="totaltheme-customize-blocks__delete-item"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span><span class="screen-reader-text"><?php \esc_html_e( 'delete item', 'total' ); ?></span></button>
					</div>
				<# }) #>
			</div>

		</div>

	<?php }

	/**
	 * Returns select choices
	 */
	protected function parse_choices( array $choices ): array {
		if ( \is_string( $choices ) && \is_callable( $choices ) ) {
			$choices = \call_user_func( $choices );
		}

		return $choices;
	}

	/**
	 * Return setting value.
	 */
	protected function get_parsed_value(): array {
		$value = $this->value();
		if ( empty( $value ) ) {
			return [];
		}
		if ( \is_string( $value ) ) {
			$value = \explode( ',', $value );
		}
		return (array) $value;
	}

}
