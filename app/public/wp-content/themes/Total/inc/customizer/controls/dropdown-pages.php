<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;
use WP_Query;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Pages Select Control.
 *
 * @todo update to a js template (currently only used for a single setting "Main Blog Page").
 */
class Dropdown_Pages extends WP_Customize_Control {

	/**
	 * Define control type.
	 */
	public $type = 'wpex-dropdown-pages';

	/**
	 * Render the content.
	 */
	public function render_content() {
		$input_id       = "_customize-input-{$this->id}";
		$description_id = "_customize-description-{$this->id}";
		?>

		<?php if ( ! empty( $this->label ) ) : ?>
			<label for="<?php echo \esc_attr( $input_id ); ?>" class="customize-control-title"><?php echo \esc_html( $this->label ); ?></label>
		<?php endif; ?>

		<?php if ( ! empty( $this->description ) ) : ?>
			<span id="<?php echo \esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo \esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<div class="total-customize-chosen-wrap"><?php
			$dropdown = \wp_dropdown_pages( [
				'id'                => $input_id,
				'echo'              => 0,
				'show_option_none'  => \esc_html__( '— Select —', 'total' ),
				'option_none_value' => '0',
				'selected'          => $this->value() ?: '0',
			] );
			// Hackily add in the data link parameter.
			echo \str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

		?></div>

	<?php }
}
