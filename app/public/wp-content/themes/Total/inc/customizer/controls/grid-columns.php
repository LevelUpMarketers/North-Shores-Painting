<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Columns Control.
 *
 * @todo update to use json template.
 */
class Grid_Columns extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'wpex-columns';

	/**
	 * Render the content
	 */
	public function render_content() {
		$input_id       = "_customize-input-{$this->id}";
		$description_id = "_customize-description-{$this->id}";
		$field_val = $this->value();

		$medias = [
			'd'  => [
				'label' => \esc_html__( 'Desktop', 'total' ),
				'icon'  => 'dashicons dashicons-desktop',
			],
			'tl' => [
				'label' => \esc_html__( 'Tablet Landscape (max-width: 1024px)', 'total' ),
				'icon'  => 'dashicons dashicons-tablet',
			],
			'tp' => [
				'label' => \esc_html__( 'Tablet Portrait (max-width: 959px)', 'total' ),
				'icon'  => 'dashicons dashicons-tablet',
			],
			'pl' => [
				'label' => \esc_html__( 'Phone Landscape (max-width: 767px)', 'total' ),
				'icon'  => 'dashicons dashicons-smartphone',
			],
			'pp' => [
				'label' => \esc_html__( 'Phone Portrait (max-width: 479px)', 'total' ),
				'icon'  => 'dashicons dashicons-smartphone',
			],
		];

		// Setup default values
		$defaults = [];
		foreach ( $medias as $key => $val ) {
			$defaults[ $key ] = '';
		}

		// If field val isn't an array then it's a single desktop column setting
		if ( ! \is_array( $field_val ) ) {
			$field_val = [
				'd' => $field_val,
			];
		}

		// Parse field
		$field_val = \wp_parse_args( $field_val, $defaults ); ?>

		<label for="<?php echo \esc_attr( $input_id ); ?>"><span class="customize-control-title"><?php echo \esc_html( $this->label ); ?></span></label>

		<?php if ( ! empty( $this->description ) ) : ?>
			<span id="<?php echo \esc_attr( $description_id ); ?>" class="description customize-control-description">
				<?php echo \wp_strip_all_tags( $this->description ); ?>
			</span>
		<?php endif; ?>

		<div class="totaltheme-customize-columns">

			<?php
			// Loop through medias and display fields
			foreach ( $medias as $key => $val ) : ?>

				<?php if ( 'd' === $key ) { ?>
					<div class="totaltheme-customize-columns__primary">
					<select id="<?php echo \esc_attr( $input_id ); ?>" class="totaltheme-customize-columns__select" data-name="<?php echo \esc_attr( $key ); ?>"><?php $this->show_options( $field_val[ $key ], false ); ?></select>
				<?php } else { ?>
					<div class="totaltheme-customize-columns__extra totaltheme-customize-columns__hidden">
					<select id="<?php echo \esc_attr( $this->id ); ?>_<?php echo \esc_attr( $key ); ?>" class="totaltheme-customize-columns__select" data-name="<?php echo \esc_attr( $key ); ?>"><?php $this->show_options( $field_val[ $key ], true ); ?></select>
					<label for="<?php echo \esc_attr( $this->id ); ?>_<?php echo \esc_attr( $key ); ?>">
						<?php if ( isset( $val['icon'] ) ) {
						$icon_classes = 'wpex-crf-icon';
						if ( 'pl' === $key || 'tl' === $key ) {
							$icon_classes .= ' wpex-crf-icon-flip';
						}
						echo '<span class="' . \esc_attr( $icon_classes ) . '" aria-hidden="true"><span class="' . \esc_attr( $val['icon'] ) . '"></span></span>';
						}
						echo \esc_attr( $val['label'] );
					?></label>
				<?php } ?>

				</div>

			<?php endforeach; ?>

		</div>

		<a href="#" class="totaltheme-customize-columns__toggle" role="button" aria-expanded="false"><?php \esc_html_e( 'Toggle responsive options', 'total' ); ?></a>

	<?php }

	/**
	 * Displays select field.
	 */
	public function show_options( $selected, $is_extra = false ) {
		if ( ! empty( $this->choices ) && ! $is_extra ) {
			$columns = $this->choices;
		} else {
			$columns = \wpex_grid_columns();
			$columns = \array_combine( $columns, $columns );
		}
		if ( $is_extra ) {
			echo '<option value ' . \selected( $selected, '', false ) . '>' . \esc_html__( 'Inherit' , 'total' ) . '</option>';
		}
		foreach ( $columns as $column => $label ) {
			echo '<option value="' . \esc_attr( $column ) . '" ' . \selected( $selected, $column, false ) . '>' . \esc_html( $label ) . '</option>';
		}

	}

}
