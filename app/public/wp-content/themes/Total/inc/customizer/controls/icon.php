<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;
use \TotalTheme\Theme_Icons;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Icon Control.
 */
class Icon extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_icon';

	/**
	 * Fallback icon.
	 */
	public $fallback = true;

	/**
	 * Enque scripts.
	 */
	public function enqueue() {
		\totaltheme_call_static( 'Helpers\Icon_Select', 'enqueue_scripts' );
	}

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$value = $this->value();

		if ( ! $value && $this->fallback ) {
			$value = (string) $this->fallback;
		}
		
		$this->json['value']         = $value;
		$this->json['choices']       = $this->choices ? esc_attr( wp_json_encode( (array) $this->choices ) ) : '';
		$this->json['selected_icon'] = $this->get_selected_icon( $value );
		$this->json['id']            = $this->id;
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
			<span class="customize-control-title">{{{ data.label }}}</span>
		<# } #>
		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<div class="totaltheme-icon-select" data-totaltheme-choices="{{{ data.choices }}}">
			<div class="totaltheme-icon-select__form">
				<div class="totaltheme-icon-select__preview-wrap">
					<div class="totaltheme-icon-select__preview <# if ( ! data.selected_icon ) { #>totaltheme-icon-select__preview--empty<# } #>">
						<div class="totaltheme-icon-select__svg">{{{ data.selected_icon }}}</div>
						<div class="totaltheme-icon-select__preview-loading" hidden><?php echo \totaltheme_get_svg( 'spinner' ); ?></div>
						<a href="#" class="totaltheme-icon-select__remove" role="button"><?php echo \totaltheme_get_svg( 'xmark' ); ?><span class="screen-reader-text"><?php \esc_html_e( 'remove selected icon', 'total' ); ?></span></a>
					</div>
				</div>
				<input class="totaltheme-icon-select__input" type="hidden" value="{{{ data.value }}}">
				<div class="totaltheme-icon-select__actions">
					<button type="button" class="totaltheme-icon-select__button totaltheme-icon-select__actions-library button button-primary"><?php \esc_html_e( 'Select Icon', 'total' ); ?></button>
					<button type="button" class="totaltheme-icon-select__button totaltheme-icon-select__actions-media button button-secondary"><?php \esc_html_e( 'Custom', 'total' ); ?></button>
				</div>
			</div>
		</div>
	<?php }

	/**
	 * Returns the selected icon in svg format.
	 */
	protected function get_selected_icon( $value = '' ) {
		return $value ? \totaltheme_get_icon( (string) \str_replace( 'ticons ticons-', '', $value ) ) : '';
	}

}
