<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

final class Shape_Dividers {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Elements to add shape divider settings to.
	 */
	private $shortcodes = [
		'vc_row',
		'vc_section',
	];

	/**
	 * Create or retrieve the class instance.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		\add_action( 'vc_after_init', [ $this, 'add_params' ], 40 );
		\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ $this, 'add_classes' ], 10, 3 );

		foreach ( $this->shortcodes as $shortcode ) {
			\add_filter( $this->get_insert_hook( $shortcode ), [ $this, 'insert_dividers' ], 100, 2 );
		}
	}

	/**
	 * Returns the hook name for inserting the shape dividers.
	 */
	protected function get_insert_hook( $shortcode = '' ) {
		return "wpex_hook_{$shortcode}_top";
	}

	/**
	 * Add new params.
	 */
	public function add_params() {
		if ( ! \function_exists( 'vc_add_params' ) ) {
			return;
		}
		foreach ( $this->shortcodes as $shortcode ) {
			\vc_add_params( $shortcode, $this->get_attributes() );
		}
	}

	/**
	 * Returns vc_map params.
	 */
	private function get_attributes() {
		$choices = ( $total_dividers = totaltheme_get_instance_of( 'Shape_Dividers' ) ) ? $total_dividers->choices() : false;

		if ( ! $choices ) {
			return;
		}

		$divider_types = \array_flip( $choices );

		return [
			[
				'type' => 'vcex_notice',
				'param_name' => 'vcex_notice__dividers',
				'text' => \esc_html__( 'Insert a SVG shape above or below your row. Works best with stretched rows and you may want to add a padding to the row to offset your divider and prevent text from overlapping.', 'total' ),
				'group' => \esc_html__( 'Dividers', 'total' ),
			],
			[
				'type' => 'vcex_subheading',
				'text' => \esc_html__( 'Top Divider', 'total' ),
				'param_name' => 'vcex_subheading__divider',
				'group' => \esc_html__( 'Dividers', 'total' ),
			],
			[
				'type' => 'dropdown',
				'heading' => \esc_html__( 'Divider Type', 'total' ),
				'param_name' => 'wpex_shape_divider_top',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'value' => $divider_types,
			],
			[
				'type' => 'vcex_select',
				'choices' => 'visibility',
				'heading' => \esc_html__( 'Visibility', 'total' ),
				'group' => \esc_html__( 'Dividers', 'total' ),
				'param_name' => 'wpex_shape_divider_top_visibility',
				'dependency' => [ 'element' => 'wpex_shape_divider_top', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Invert', 'total' ),
				'param_name' => 'wpex_shape_divider_top_invert',
				'std' => 'false',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'dependency' => [
					'element' => 'wpex_shape_divider_top',
					'value' => [ 'triangle', 'triangle_asymmetrical', 'arrow', 'clouds', 'curve', 'waves' ],
				],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Bring to Front', 'total' ),
				'param_name' => 'wpex_shape_divider_top_infront',
				'description' => \esc_html__( 'Enable to place the divider on top of overflowing content. Important: This will only take affect on the live site and not in the frontend editor because it can make it impossible to access the edit buttons.', 'total' ),
				'std' => 'false',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'dependency' => [ 'element' => 'wpex_shape_divider_top', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Flip', 'total' ),
				'param_name' => 'wpex_shape_divider_top_flip',
				'std' => 'false',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'dependency' => [
					'element' => 'wpex_shape_divider_top',
					'value' => [ 'tilt', 'triangle_asymmetrical', 'clouds', 'waves', 'mountains', 'wave_brush' ],
				],
			],
			[
				'type' => 'vcex_colorpicker',
				'heading' => \esc_html__( 'Divider Color', 'total' ),
				'param_name' => 'wpex_shape_divider_top_color',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'description' => \esc_html__( 'Your color should equal the background color of the previous or next section.', 'total' ),
				'dependency' => [ 'element' => 'wpex_shape_divider_top', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_number',
				'heading' => \esc_html__( 'Divider Height', 'total' ),
				'param_name' => 'wpex_shape_divider_top_height',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'description' => \esc_html__( 'Enter your custom height in pixels.', 'total' ),
				'dependency' => [
					'element' => 'wpex_shape_divider_top',
					'value' => [ 'tilt', 'triangle', 'triangle_asymmetrical', 'arrow', 'clouds', 'curve', 'waves' ],
				],
				'min' => 1,
				'step' => 1,
				'max' => 500,
			],
			[
				'type' => 'vcex_number',
				'heading' => \esc_html__( 'Divider Width', 'total' ),
				'param_name' => 'wpex_shape_divider_top_width',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'description' => \esc_html__( 'Enter your custom percentage based width. For example to make your shape twice as big enter 200.', 'total' ),
				'dependency' => [
					'element' => 'wpex_shape_divider_top',
					'value' => [ 'triangle', 'triangle_asymmetrical', 'arrow', 'curve', 'waves' ],
				],
				'min' => 100,
				'step' => 1,
				'max' => 300,
			],
			[
				'type' => 'vcex_subheading',
				'text' => \esc_html__( 'Bottom Divider', 'total' ),
				'param_name' => 'vcex_subheading__divider--bottom',
				'group' => \esc_html__( 'Dividers', 'total' ),
			],
			[
				'type' => 'dropdown',
				'heading' => \esc_html__( 'Divider Type', 'total' ),
				'param_name' => 'wpex_shape_divider_bottom',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'value' => $divider_types,
			],
			[
				'type' => 'vcex_select',
				'choices' => 'visibility',
				'heading' => \esc_html__( 'Visibility', 'total' ),
				'group' => \esc_html__( 'Dividers', 'total' ),
				'param_name' => 'wpex_shape_divider_bottom_visibility',
				'dependency' => [ 'element' => 'wpex_shape_divider_bottom', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Invert', 'total' ),
				'param_name' => 'wpex_shape_divider_bottom_invert',
				'std' => 'false',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'dependency' => [
					'element' => 'wpex_shape_divider_bottom',
					'value' => [ 'triangle', 'triangle_asymmetrical', 'arrow', 'clouds', 'curve', 'waves' ],
				],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Bring to Front', 'total' ),
				'param_name' => 'wpex_shape_divider_bottom_infront',
				'description' => \esc_html__( 'Enable to place the divider on top of overflowing content. Important: This will only take affect on the live site and not in the frontend editor because it can make it impossible to access the edit buttons.', 'total' ),
				'std' => 'false',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'dependency' => [ 'element' => 'wpex_shape_divider_bottom', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Flip', 'total' ),
				'param_name' => 'wpex_shape_divider_bottom_flip',
				'std' => 'false',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'dependency' => [
					'element' => 'wpex_shape_divider_bottom',
					'value' => [ 'tilt', 'triangle_asymmetrical', 'clouds', 'waves', 'wave_brush', 'mountains' ]
				],
			],
			[
				'type' => 'vcex_colorpicker',
				'heading' => \esc_html__( 'Divider Color', 'total' ),
				'param_name' => 'wpex_shape_divider_bottom_color',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'dependency' => [ 'element' => 'wpex_shape_divider_bottom', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_number',
				'heading' => \esc_html__( 'Divider Height', 'total' ),
				'param_name' => 'wpex_shape_divider_bottom_height',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'description' => \esc_html__( 'Enter your custom height in pixels.', 'total' ),
				'dependency' => [
					'element' => 'wpex_shape_divider_bottom',
					'value' => [
						'tilt',
						'triangle',
						'triangle_asymmetrical',
						'arrow',
						'clouds',
						'curve',
						'waves'
					],
				],
				'min' => 1,
				'step' => 1,
				'max' => 500,
			],
			[
				'type' => 'vcex_number',
				'heading' => \esc_html__( 'Divider Width', 'total' ),
				'param_name' => 'wpex_shape_divider_bottom_width',
				'group' => \esc_html__( 'Dividers', 'total' ),
				'description' => \esc_html__( 'Enter your custom percentage based width. For example to make your shape twice as big enter 200.', 'total' ),
				'dependency' => [
					'element' => 'wpex_shape_divider_bottom',
					'value' => [ 'triangle', 'triangle_asymmetrical', 'arrow', 'curve', 'waves' ]
				],
				'min' => 100,
				'step' => 1,
				'max' => 300,
			],
		];
	}

	/**
	 * Adds classes to shortcodes that have dividers.
	 */
	public function add_classes( $class_string, $tag, $atts ) {
		if ( \in_array( $tag, $this->shortcodes ) ) {
			if ( ! empty( $atts['wpex_shape_divider_top'] ) ) {
				$class_string .= ' wpex-has-shape-divider-top';
			}
			if ( ! empty( $atts['wpex_shape_divider_bottom'] ) ) {
				$class_string .= ' wpex-has-shape-divider-bottom';
			}
		}

		return $class_string;
	}

	/**
	 * Inserts the divider HTML into the shortcodes.
	 */
	public function insert_dividers( $content, $atts ) {
		if ( ! empty( $atts['wpex_shape_divider_top'] ) && \is_string( $atts['wpex_shape_divider_top'] ) ) {
			$content .= $this->top_divider( $atts );
		}

		if ( ! empty( $atts['wpex_shape_divider_bottom'] ) && \is_string( $atts['wpex_shape_divider_bottom'] ) ) {
			$content .= $this->bottom_divider( $atts );
		}

		return $content;
	}

	/**
	 * Returns the top divider html.
	 */
	protected function top_divider( $atts ) {
		return totaltheme_call_static( 'Shape_Dividers', 'get_divider', 'top', $atts );
	}

	/**
	 * Returns the bottom divider html.
	 */
	protected function bottom_divider( $atts ) {
		return totaltheme_call_static( 'Shape_Dividers', 'get_divider', 'bottom', $atts );
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}


}
