<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Font_Size Customizer Control.
 */
class Font_Size extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_font_size';

	/**
	 * Send data to content_template.
	 */
	public function to_json() {
		parent::to_json();

		$this->json['value'] = $this->get_parsed_value();
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

		<# if ( data.label ) { #>
			<label class="customize-control-title">{{ data.label }}</label>
		<# } #>

		<# if ( data.description ) { #>
			<span id="_customize-description-{{ data.id }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<div class="totaltheme-customize-font-size">
			<div class="totaltheme-customize-responsive-field">
				<?php foreach ( $this->get_media_queries() as $key => $val ) : ?>
				<div class="totaltheme-customize-responsive-field__item">
					<label for="{{ data.id }}_<?php echo \esc_attr( $key ); ?>" class="screen-reader-text"><?php echo \esc_attr( $val['label'] ); ?></label>
					<input class="totaltheme-customize-responsive-field__input" id="{{ data.id }}_<?php echo \esc_attr( $key ); ?>" data-name="<?php echo \esc_attr( $key ); ?>" value="{{ data.value.<?php echo esc_attr( $key );?> }}" type="text" placeholder="-">
					<?php if ( isset( $val['icon'] ) ) {
						$icon_classes = 'totaltheme-customize-responsive-field__icon';
						if ( 'pl' === $key || 'tl' === $key ) {
							$icon_classes .= ' totaltheme-customize-responsive-field__icon--flip';
						} ?>
					<span class="<?php echo \esc_attr( $icon_classes ); ?>" aria-hidden="true"><span class="<?php echo \esc_attr( $val['icon'] ); ?>"></span></span>
					<?php } ?>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="total-theme-customize-font-size__actions" aria-hidden="true"><?php
				$util_size_links = '';
				foreach ( \wpex_utl_font_sizes() as $k => $v ) {
					if ( $k ) {
						$util_size_links .= '<a href="# title="' . \esc_attr( $v ) . '">' . \esc_html( $k ) . '</a>, ';
					}
				}
				\printf( \esc_html__( '%sPreset:%s', 'total-theme-core' ), '<strong>', '</strong> ' . \rtrim( \trim( $util_size_links ), ',' ) );
			?></div>
		</div>

	<?php }

	/**
	 * Returns the field value.
	 */
	protected function get_parsed_value() {
		$field_val = $this->value();

		// Setup default values.
		$defaults = [];
		foreach ( $this->get_media_queries() as $key => $val ) {
			$defaults[ $key ] = '';
		}

		// Font size should always be an array.
		if ( ! \is_array( $field_val ) ) {
			$field_val = [
				'd' => $field_val,
			];
		}

		return \wp_parse_args( $field_val, $defaults );
	}

	/**
	 * Returns the media queries.
	 */
	protected function get_media_queries(): array {
		return [
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
	}

}
