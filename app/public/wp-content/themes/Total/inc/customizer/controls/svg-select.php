<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

/**
 * Customizer SVG Select Control.
 */
class SVG_Select extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_svg_select';

	/**
	 * The default svg.
	 */
	public $default = '';

	/**
	 * Render Control Content.
	 */
	public function render_content() {
		$this_val = $this->value();
		$choices  = $this->choices ?? [];

		if ( \is_string( $choices ) && \is_callable( $choices ) ) {
			$choices = \call_user_func( $choices );
		}

		if ( ! \is_array( $choices ) ) {
			return;
		}

		?>

		<label class="customize-control-title"><?php echo \esc_html( $this->label ); ?></label>

		<?php if ( ! empty( $this->description ) ) : ?>
			<span id="<?php echo \esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo \esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<div class="totaltheme-customize-svg-select">

			<select <?php $this->link(); ?>>
				<?php foreach ( $choices as $svg_file => $svg_name ) { ?>
					<option value="<?php echo \esc_attr( $svg_file ); ?>" <?php selected( $svg_file, $this_val, true ); ?>><?php echo \esc_html( $svg_name ); ?></option>
				<?php } ?>
			</select>

			<div class="totaltheme-customize-svg-select__preview">
				<span><?php esc_html_e( 'Preview', 'total' ); ?></span>
				<?php foreach ( $choices as $svg_file => $svg_name ) {
					if ( $svg = totaltheme_get_loading_icon( $svg_file ) ) {
						$this_val = $this_val ?: $this->default;
						$svg_class = 'totaltheme-customize-svg-select-icon';
						if ( $svg_file === $this->default ) {
							$svg_class .= ' totaltheme-customize-svg-select-icon--default';
						}
						if ( $svg_file !== $this_val ) {
							$svg_class .= ' totaltheme-customize-svg-select-icon--hidden';
						}
						echo '<div class="'. \esc_attr( $svg_class ) . '" data-wpex-value="' . \esc_attr( $svg_file ) . '">' . $svg . '</div>';
					}
				} ?>
			</div>

		</div>

		<?php
	}

}
