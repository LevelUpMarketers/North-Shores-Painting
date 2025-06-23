<?php

namespace TotalThemeCore\Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Advertisement widget.
 */
class Widget_Advertisement extends \TotalThemeCore\WidgetBuilder {

	/**
	 * Widget args.
	 */
	private $args;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->args = array(
			'id_base' => 'wpex_advertisement',
			'name'    => $this->branding() . esc_html__( 'Advertisement', 'total-theme-core' ),
			'options' => array(
				'customize_selective_refresh' => true,
			),
			'fields'  => array(
				array(
					'id'    => 'title',
					'label' => esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'      => 'columns',
					'label'   => esc_html__( 'Columns', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => array(
						'1' => '1',
						'2' => '2',
					),
				),
				array(
					'id'      => 'breakpoint',
					'label'   => esc_html__( 'Stacking Breakpoint', 'total-theme-core' ),
					'type'    => 'select',
					'default' => 'sm',
					'choices' => array(
						'sm'   => esc_html__( 'sm - 640px', 'total-theme-core' ),
						'md'   => esc_html__( 'md - 768px', 'total-theme-core' ),
						'lg'   => esc_html__( 'lg - 1024px', 'total-theme-core' ),
						'xl'   => esc_html__( 'xl - 1280px', 'total-theme-core' ),
						'none' => esc_html__( 'None (no stacking)', 'total-theme-core' ),
					),
				),
				array(
					'id'      => 'columns_gap',
					'label'   => esc_html__( 'Column Gap', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'grid_gaps',
				),
				array(
					'id'    => 'stretch_img',
					'label' => esc_html__( 'Stretch Images?', 'total-theme-core' ),
					'type'  => 'checkbox',
					'description' => esc_html__( 'Force a 100% width on the advertisement images so they fill up the parent container.', 'total-theme-core' ),
				),
				array(
					'id'    => 'nofollow',
					'label' => esc_html__( 'Add "nofollow" to links?', 'total-theme-core' ),
					'type'  => 'checkbox',
				),
				array(
					'id'    => 'target_blank',
					'label' => esc_html__( 'Open links in a new tab?', 'total-theme-core' ),
					'type'  => 'checkbox',
				),
				array(
					'id'    => 'ads',
					'label' => esc_html__( 'Ads', 'total-theme-core' ),
					'type'  => 'repeater',
					'fields' => array(
						array(
							'id' => 'url',
							'label' => esc_html__( 'Link URL', 'total-theme-core' ),
							'type'  => 'text',
						),
						array(
							'id' => 'image',
							'label' => esc_html__( 'Image', 'total-theme-core' ),
							'type'  => 'media_upload',
						),
					),
				),
			),
		);

		$this->create_widget( $this->args );
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		extract( $this->parse_instance( $instance ) );

		// Before widget hook
		echo wp_kses_post( $args['before_widget'] );

		// Display widget title
		$this->widget_title( $args, $instance );

		// Define widget output
		$output = '';

		if ( $ads && is_array( $ads ) ) {

			$row_class = [
				'wpex-ads-widget',
				'wpex-grid',
			];

			$columns_class = sanitize_html_class( $columns );

			if ( 'none' === $breakpoint || ! function_exists( 'wpex_grid_columns_class' ) ) {
				$row_class[] = "wpex-grid-cols-{$columns_class}";
			} else {
				$bk_safe = (string) sanitize_html_class( $breakpoint );
				$row_class[] = "wpex-{$bk_safe}-grid-cols-{$columns_class}";
			}

			$columns_gap = $columns_gap ?: 10;

			if ( 'none' !== $columns_gap ) {
				$row_class[] = 'wpex-gap-' . sanitize_html_class( $columns_gap );
			}

			$output .= '<div class="' . esc_attr( implode( ' ', $row_class ) ) . '">';

			foreach ( $ads as $ad ) :
				$ad_url = ! empty( $ad['url'] ) ? (string) $ad['url'] : '#';
				$ad_image = (string) $ad['image'] ?? '';

				if ( ! $ad_url && ! $ad_image ) {
					continue;
				}

				$output .= '<div class="wpex-ads-widget__item">';

				$image_alt = '';

				if ( is_numeric( $ad_image ) ) {
					$image_alt = get_post_meta( $ad_image, '_wp_attachment_image_alt', TRUE );
					$ad_image  = wp_get_attachment_url( $ad_image );
				}

				if ( $ad_image ) {

					// Add link tag
					$output .= '<a href="' . esc_url( $ad_url ) . '"';

						if ( wp_validate_boolean( $target_blank ) ) {
							$output .= ' target="_blank"';
						}

						if ( wp_validate_boolean( $nofollow ) ) {
							$output .= ' rel="nofollow"';
						}

					$output .= '>';

					// Display Image
					$image_class = 'wpex-align-bottom';

					if ( wp_validate_boolean( $stretch_img ) ) {
						$image_class .= ' stretch-image';
					}

					$output .= '<img src="' . esc_url( $ad_image ) . '" class="' . esc_attr( $image_class ) . '" alt="' . esc_attr( $image_alt ) . '">';

					$output .= '</a>';

				}

				$output .= '</div>';

			endforeach;

			$output .= '</div>';

		}

		// @codingStandardsIgnoreLine
		echo $output;

		// After widget hook
		echo wp_kses_post( $args['after_widget'] );
	}

}
register_widget( 'TotalThemeCore\\Widgets\\Widget_Advertisement' );
