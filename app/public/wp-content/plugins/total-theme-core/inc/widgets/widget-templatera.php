<?php

namespace TotalThemeCore\Widgets;

\defined( 'ABSPATH' ) || exit;

/**
 * Template widget.
 */
class Widget_Templatera extends \TotalThemeCore\WidgetBuilder {

	/**
	 * Widget args.
	 */
	private $args;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->args = [
			'id_base' => 'wpex_templatera',
			'name'    => $this->branding() . \esc_html__( 'Template', 'total-theme-core' ),
			'options' => [
				'customize_selective_refresh' => true,
			],
			'fields'  => [
				[
					'id'    => 'title',
					'label' => \esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				],
				[
					'id'    => 'template',
					'label' => \esc_html__( 'Template', 'total-theme-core' ),
					'type'  => 'select_template',
				],
			],
		];

		$this->create_widget( $this->args );
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		\extract( $this->parse_instance( $instance ) );

		echo \wp_kses_post( $args['before_widget'] );

		$this->widget_title( $args, $instance );

		$output = '';

		$temp_post = $template ? \get_post( $template ) : '';

		if ( $temp_post && ! empty( $temp_post->post_content ) ) {
			if ( \function_exists( '\totaltheme_get_post_builder_type' ) ) {
				$template_type = \totaltheme_get_post_builder_type( $template );
			} else {
				$template_type = '';
			}

			if ( \function_exists( 'totaltheme_call_non_static' ) ) {
				$output .= totaltheme_call_non_static( 'Integration\WPBakery\Shortcode_Inline_Style', 'get_style', [ $template ], false );
			}

			if ( 'elementor' === $template_type && \function_exists( '\wpex_get_elementor_content_for_display' ) ) {
				$template_content = \wpex_get_elementor_content_for_display( $template );
			} elseif ( \function_exists( 'totaltheme_render_content' ) ) {
				$template_content = \totaltheme_render_content( $temp_post->post_content );
			} else {
				$template_content = \do_shortcode( \wp_kses_post( $temp_post->post_content ) );
			}

			$output .= '<div class="wpex-templatera-widget-content wpex-clr">' . $template_content . '</div>';
		}

		// @codingStandardsIgnoreLine.
		echo $output;

		echo \wp_kses_post( $args['after_widget'] );
	}

}

register_widget( 'TotalThemeCore\Widgets\Widget_Templatera' );
