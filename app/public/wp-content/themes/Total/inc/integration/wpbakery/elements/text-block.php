<?php

namespace TotalTheme\Integration\WPBakery\Elements;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Text Block Configuration.
 */
final class Text_Block {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Initialize the class.
	 */
	public static function init(): void {
		\add_action( 'init', [ self::class, 'add_params' ], 40 );
		\add_action( 'vc_after_init', [ self::class, 'on_vc_after_init' ], 40 );
		\add_action( 'vc_after_mapping', [ self::class, 'on_vc_after_mapping' ] );
		\add_filter( 'vc_shortcode_output', [ self::class, 'custom_output' ], 10, 4 );
	}

	/**
	 * Adds new params for the VC Rows.
	 *
	 * @todo update to use vc_add_params instead of vc_add_param.
	 */
	public static function add_params() {
		if ( ! function_exists( 'vc_add_params' ) ) {
			return;
		}

		$params = [
			[
				'type' => 'vcex_select',
				'heading' => \esc_html__( 'Visibility', 'total' ),
				'param_name' => 'visibility',
			],
		];

		// Styling options.
		if ( \function_exists( '\vcex_inline_style' ) ) {

			// Bottom margin
			$params[] = [
				'type' => 'vcex_select',
				'heading' => esc_html__( 'Bottom Margin', 'total' ),
				'param_name' => 'bottom_margin',
				'admin_label' => true,
				'group' => esc_html__( 'Style', 'total' ),
			];

			// Width
			$params[] = [
				'type' => 'textfield',
				'heading' => \esc_html__( 'Width', 'total' ),
				'param_name' => 'width',
				'description' => \esc_html__( 'Enter a custom width instead of using line breaks to slim down your content and keep it responsive. ', 'total' ),
				'group' => \esc_html__( 'Style', 'total' ),
			];

			// Align
			$params[] = [
				'type' => 'vcex_text_align',
				'heading' => \esc_html__( 'Align', 'total' ),
				'param_name' => 'align',
				'std' => 'center',
				'group' => \esc_html__( 'Style', 'total' ),
				'dependency' => array( 'element' => 'width', 'not_empty' => true ),
			];
			// Typography
			$params[] = [
				'type' => 'vcex_text_align',
				'heading' => \esc_html__( 'Text Align', 'total' ),
				'param_name' => 'text_align',
				'group' => \esc_html__( 'Typography', 'total' ),
			];
			$params[] = [
				'type' => 'vcex_colorpicker',
				'heading' => \esc_html__( 'Color', 'total' ),
				'param_name' => 'color',
				'group' => \esc_html__( 'Typography', 'total' ),
			];
			$params[] = [
				'type' => 'vcex_ofswitch',
				'std' => 'true',
				'heading' => \esc_html__( 'Apply Color To Everything?', 'total' ),
				'description' => \esc_html__( 'If enabled the custom color will be applied to all child elements of the text block including headings and links.', 'total' ),
				'param_name' => 'child_inherit_color',
				'group' => \esc_html__( 'Typography', 'total' ),
			//	'dependency' => array( 'element' => 'color', 'not_empty' => true ), // causes vc error
			];
			$params[] = [
				'type' => 'vcex_font_size',
				'heading' => \esc_html__( 'Font Size', 'total' ),
				'param_name' => 'font_size',
				'group' => \esc_html__( 'Typography', 'total' ),
			];
			$params[] = [
				'type' => 'vcex_font_family_select',
				'heading' => \esc_html__( 'Font Family', 'total' ),
				'param_name' => 'font_family',
				'group' => \esc_html__( 'Typography', 'total' ),
			];
			$params[] = [
				'type' => 'dropdown',
				'std' => 'false',
				'heading' => \esc_html__( 'Font Style', 'total' ),
				'param_name' => 'italic',
				'value' => [
					\esc_html__( 'Normal', 'total' ) => 'false',
					\esc_html__( 'Italic', 'total' ) => 'true',
				],
				'group' => \esc_html__( 'Typography', 'total' ),
			];
			$params[] = [
				'type' => 'vcex_preset_textfield',
				'heading' => \esc_html__( 'Line Height', 'total' ),
				'param_name' => 'line_height',
				'choices' => 'line_height',
				'group' => \esc_html__( 'Typography', 'total' ),
			];
			$params[] = [
				'type' => 'vcex_preset_textfield',
				'heading' => \esc_html__( 'Letter Spacing', 'total' ),
				'param_name' => 'letter_spacing',
				'choices' => 'letter_spacing',
				'group' => \esc_html__( 'Typography', 'total' ),
			];
			$params[] = [
				'type' => 'vcex_select',
				'heading' => \esc_html__( 'Font Weight', 'total' ),
				'param_name' => 'font_weight',
				'group' => \esc_html__( 'Typography', 'total' ),
			];
			$params[] = [
				'type' => 'vcex_select',
				'heading' => \esc_html__( 'Text Transform', 'total' ),
				'param_name' => 'text_transform',
				'group' => \esc_html__( 'Typography', 'total' ),
			];
			$params[] = [
				'type' => 'vcex_min_max',
				'heading' => \esc_html__( 'Min-Max Font Size', 'total' ),
				'param_name' => 'responsive_text_min_max',
				'unit' => 'px',
				'description' => \esc_html__( 'This setting allows you to define a minimum and maximum font size in pixels. Javascript will then be used to calculate an ideal font size for your text. Important: This setting works independently and will override any other predefined font size and is recommend only for very large banners/headings.', 'total' ),
				'group' => \esc_html__( 'Typography', 'total' ),
			];

			$params[] = [
				'type' => 'vcex_colorpicker',
				'heading' => esc_html__( 'Background Color', 'total' ),
				'group' => \esc_html__( 'Design Options', 'js_composer' ),
				'param_name' => 'wpex_bg_color',
				'weight' => -2,
			];
	
			$params[] = [
				'type' => 'vcex_colorpicker',
				'heading' => esc_html__( 'Border Color', 'total' ),
				'group' => \esc_html__( 'Design Options', 'js_composer' ),
				'param_name' => 'wpex_border_color',
				'weight' => -2,
			];
			
			// Deprecated
			$params[] = [ 'type' => 'hidden', 'param_name' => 'responsive_text', 'std' => '' ];
			$params[] = [ 'type' => 'hidden', 'param_name' => 'min_font_size', 'std' => '' ];
	
		}

		\vc_add_params( 'vc_column_text', $params );
	}

	/**
	 * Hooks into vc_after_init.
	 */
	public static function on_vc_after_init() {
		if ( \function_exists( '\vc_update_shortcode_param' ) ) {
			if ( $param = \WPBMap::getParam( 'vc_column_text', 'css' ) ) {
				$param['weight'] = -1;
				\vc_update_shortcode_param( 'vc_column_text', $param );
			}
		}
	}

	/**
	 * Hooks into vc_after_mapping.
	 */
	public static function on_vc_after_mapping() {
		if ( \function_exists( '\vc_post_param' ) && '\vc_edit_form' === \vc_post_param( 'action' ) ) {
			\add_filter( 'vc_edit_form_fields_attributes_vc_column_text', [ self::class, 'edit_fields' ] );
		}
	}

	/**
	 * Edit form fields.
	 */
	public static function edit_fields( $atts ) {
		if ( isset( $atts['responsive_text'] )
			&& 'true' == $atts['responsive_text']
			&& ! empty( $atts['font_size'] )
			&& ! empty( $atts['min_font_size'] )
			&& \function_exists( '\vcex_parse_min_max_text_font_size' )
		) {
			$min = \vcex_parse_min_max_text_font_size( $atts['min_font_size'] );
			$max = \vcex_parse_min_max_text_font_size( $atts['font_size'] );
			if ( $min && $max ) {
				$atts['responsive_text_min_max'] = \wp_strip_all_tags( "{$min}|{$max}" );
				$atts['min_font_size'] = '';
				$atts['font_size'] = '';
				$atts['responsive_text'] = '';
			}
		}

		return $atts;
	}

	/**
	 * Add custom HTML to ouput.
	 */
	public static function custom_output( $output, $obj, $atts, $shortcode ) {
		if ( 'vc_column_text' !== $shortcode ) {
			return $output;
		}

		$add_attrs = '';
		$add_classes = [];
		$inline_css = '';

		// Min-Max font size.
		if ( ! empty( $atts['responsive_text_min_max'] )
			|| ( isset( $atts['responsive_text'] ) && 'true' == $atts['responsive_text'] ) // old setting
		) {

			$min_max = $atts['responsive_text_min_max'] ?? '';
			if ( $min_max && \is_string( $min_max ) ) {
				$min_max = \explode( '|', $min_max );
			}
			$font_size = $min_max[1] ?? $atts['font_size'] ?? null;
			$min_font_size = $min_max[0] ?? $atts['min_font_size'] ?? null;

			if ( $font_size && $min_font_size && \function_exists( 'vcex_parse_min_max_text_font_size' ) ) {

				// Parse font sizes.
				$font_size     = \vcex_parse_min_max_text_font_size( $font_size );
				$min_font_size = \vcex_parse_min_max_text_font_size( $min_font_size );

				// Add wrap classes and data.
				if ( $font_size && $min_font_size ) {
					$min_max_attrs = ' data-min-font-size="' . \absint( $min_font_size ) . '"';
					$min_max_attrs .= 'data-max-font-size="' . \absint( $font_size ) . '"';
					\wp_enqueue_script( 'vcex-responsive-text' );
					$atts['font_size'] = $min_font_size;
					$pos = \strpos( $output, '<div class="wpb_wrapper' );
					if ( false !== $pos ) {
						$output = \substr_replace( $output, '<div ' . $min_max_attrs . ' class="wpb_wrapper wpex-responsive-txt', $pos, \strlen( '<div class="wpb_wrapper' ) );
					}
				}

			}

		}

		if ( function_exists( 'vcex_inline_style' ) ) {
			$inline_style = vcex_inline_style( [
				'color'            => $atts['color'] ?? null,
				'font_family'      => $atts['font_family'] ?? null,
				'font_size'        => $atts['font_size'] ?? null,
				'letter_spacing'   => $atts['letter_spacing'] ?? null,
				'font_weight'      => $atts['font_weight'] ?? null,
				'text_align'       => $atts['text_align'] ?? null,
				'line_height'      => $atts['line_height'] ?? null,
				'width'            => $atts['width'] ?? null,
				'font_style'       => ( isset( $atts['italic'] ) && 'true' == $atts['italic'] ) ? 'italic' : '',
				'text_transform'   => $atts['text_transform'] ?? null,
				'background_color' => $atts['wpex_bg_color'] ?? null,
				'border_color'     => $atts['wpex_border_color'] ?? null,
				'margin_bottom'    => $atts['bottom_margin'] ?? null,
			], false );
			if ( $inline_style ) {
				$add_attrs .= ' style="' . \esc_attr( $inline_style ) . '"';
			}
		}

		if ( ! empty( $atts['font_size'] )
			&& \str_contains( $atts['font_size'], '|' )
			&& \function_exists( '\vcex_element_responsive_css' )
			&& \function_exists( '\vcex_element_unique_classname' )
		) {

			$el_unique_class = \vcex_element_unique_classname();

			$el_responsive_styles = [
				'font_size' => $atts['font_size'],
			];

			if ( $responsive_css = \vcex_element_responsive_css( $el_responsive_styles, $el_unique_class ) ) {
				$inline_css .= $responsive_css;
				$add_classes[] = $el_unique_class;
			}
		}

		if ( ! empty( $atts['color'] ) ) {
			$child_inherit_color = \wp_validate_boolean( $atts['child_inherit_color'] ?? true );
			if ( $child_inherit_color ) {
				$custom_color_classes = 'has-custom-color wpex-child-inherit-color';
				$custom_color_classes = \apply_filters( 'wpex_vc_column_text_custom_color_classes', $custom_color_classes );
				if ( $custom_color_classes && \is_string( $custom_color_classes ) ) {
					$add_classes[] = $custom_color_classes;
				}
			}
		}

		if ( ! empty( $atts['visibility'] ) ) {
			$add_classes[] = \totaltheme_get_visibility_class( $atts['visibility'] );
		}

		if ( ! empty( $atts['width'] ) ) {
			$add_classes[] = 'wpex-max-w-100';
			$align = ! empty( $atts['align'] ) ? $atts['align'] : 'center';
			switch ( $align ) {
				case 'left':
					$add_classes[] = 'wpex-mr-auto';
					break;
				case 'right':
					$add_classes[] = 'wpex-ml-auto';
					break;
				case 'center':
					$add_classes[] = 'wpex-mx-auto';
					break;
			}
		}

		if ( $add_classes && $add_classes = array_filter( $add_classes ) ) {
			$output = \str_replace( 'wpb_text_column', 'wpb_text_column ' . \esc_attr( \implode( ' ', $add_classes ) ), $output );
		}

		if ( $add_attrs ) {
			$pos = \strpos( $output, '<div' );
			if ( $pos !== false ) {
				$output = \substr_replace( $output, '<div ' . \trim( $add_attrs ), $pos, \strlen( '<div' ) );
			}
		}

		if ( $inline_css ) {
			$inline_css = '<style>' . \esc_attr( $inline_css ) . '</style>';
			$output = $inline_css . $output;
		}

		$output = \totaltheme_replace_vars( $output );

		return $output;
	}

}
