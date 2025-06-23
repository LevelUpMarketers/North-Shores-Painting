<?php

namespace TotalTheme\Customizer\Controls;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Color Control.
 */
class Color extends \WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_color';

	/**
	 * The control type.
	 */
	public $allow_global = 1;

	/**
	 * Control Slug used for color palette colors to update live color palette.
	 */
	public $color_slug = '';

	/**
	 * Default Color.
	 */
	public $color_default = '';

	/**
	 * Is the color a dark color setting.
	 */
	public $color_is_dark = 0;

	/**
	 * Excluded colors from the color palette selector.
	 */
	public $exclude_colors = '';

	/**
	 * Included colors for the color palette selector.
	 */
	public $include_colors = '';

	/**
	 * Enque scripts.
	 */
	public function enqueue() {
		\wp_enqueue_script( 'totaltheme-components' );
		\wp_enqueue_style( 'totaltheme-components' );
	}

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$this->json['value']          = $this->value();
		$this->json['id']             = $this->id;
		$this->json['color_default']  = $this->color_default;
		$this->json['allow_global']   = (int) $this->allow_global;
		$this->json['color_scheme']   = $this->color_is_dark ? 'dark' : 'light';
		$this->json['exclude_colors'] = $this->get_excluded_colors();
		$this->json['include_colors'] = $this->include_colors;

		if ( $this->color_slug ) {
			$this->json['color_slug'] = $this->color_slug;
		}
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
			<label class="customize-control-title">{{ data.label }}</label>
		<# } #>
		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<?php totaltheme_component( 'color', [
			'id'           => '_customize-input-{{ data.id }}',
			'allow_global' => '{{ data.allow_global }}',
			'value'        => '{{ data.value }}',
			'default'      => '{{ data.color_default }}',
			'exclude'      => '{{ data.exclude_colors }}',
			'include'      => '{{ data.include_colors }}',
			'color_scheme' => '{{ data.color_scheme }}',
			'input_name'   => '{{ data.id }}',
		] );
	}

	/**
	 * Return string of excluded colors.
	 */
	public function get_excluded_colors(): string {
		$excluded = '';
		if ( $this->color_slug ) {
			$excluded .= ",$this->color_slug";
		}
		if ( $this->exclude_colors ) {
			$excluded .= ",$this->exclude_colors";
		}
		if ( $excluded ) {
			$excluded = \trim( $excluded, ',' );
		}
		return $excluded;
	}

}
