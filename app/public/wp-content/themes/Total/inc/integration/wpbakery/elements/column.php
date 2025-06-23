<?php

namespace TotalTheme\Integration\WPBakery\Elements;

use TotalTheme\Integration\WPBakery\Deprecated_CSS_Params_Style;
use TotalTheme\Integration\WPBakery\Helpers as WPB_Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Column Tweaks.
 */
final class Column {

	/**
	 * Init.
	 */
	public static function init() {
		// Preload required classes.
		if ( ! \class_exists( '\TotalTheme\Integration\WPBakery\Helpers' ) ) {
			return;
		}

		\add_action( 'vc_after_init', [ self::class, 'add_params' ], 40 ); // add params first
		\add_action( 'vc_after_init', [ self::class, 'modify_params' ], 40 ); // priority is crucial.
		\add_filter( 'shortcode_atts_vc_column', [ self::class, 'shortcode_atts' ] );
		\add_filter( 'vc_shortcode_output', [ self::class, 'custom_output' ], 10, 4 );

		if ( \defined( '\VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
			\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ self::class, 'shortcode_classes' ], 100, 3 );
		}

		if ( \function_exists( '\vc_post_param' ) && 'vc_edit_form' === \vc_post_param( 'action' ) ) {
			\add_filter( 'vc_edit_form_fields_attributes_vc_column', [ self::class, 'edit_form_fields_vc_column' ] );
			\add_filter( 'vc_edit_form_fields_attributes_vc_column_inner', [ self::class, 'edit_form_fields_vc_column_inner' ] );
		}
	}

	/**
	 * Used to update default parms.
	 */
	public static function modify_params() {
		if ( ! \function_exists( '\vc_update_shortcode_param' ) ) {
			return;
		}

		// Modify el_id.
		if ( $param = \WPBMap::getParam( 'vc_column', 'el_id' ) ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify el_class.
		if ( $param = \WPBMap::getParam( 'vc_column', 'el_class' ) ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify css_animation.
		if ( $param = \WPBMap::getParam( 'vc_column', 'css_animation' ) ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify video_bg.
		if ( $param = \WPBMap::getParam( 'vc_column', 'video_bg' ) ) {
			$param['group'] = \esc_html__( 'Video', 'total' );
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify video_bg_parallax.
		if ( $param = \WPBMap::getParam( 'vc_column', 'video_bg_parallax' ) ) {
			$param['group'] = \esc_html__( 'Video', 'total' );
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify video_bg_url.
		if ( $param = \WPBMap::getParam( 'vc_column', 'video_bg_url' ) ) {
			$param['group'] = \esc_html__( 'Video', 'total' );
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify parallax_speed_video.
		if ( $param = \WPBMap::getParam( 'vc_column', 'parallax_speed_video' ) ) {
			$param['group'] = \esc_html__( 'Video', 'total' );
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify parallax.
		if ( $param = \WPBMap::getParam( 'vc_column', 'parallax' ) ) {
			$param['group'] = \esc_html__( 'Parallax', 'total' );
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify parallax_image.
		if ( $param = \WPBMap::getParam( 'vc_column', 'parallax_image' ) ) {
			$param['group'] = \esc_html__( 'Parallax', 'total' );
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify parallax_speed_bg.
		if ( $param = \WPBMap::getParam( 'vc_column', 'parallax_speed_bg' ) ) {
			$param['group'] = \esc_html__( 'Parallax', 'total' );
			$param['dependency'] = array(
				'element' => 'parallax',
				'value' => array( 'content-moving', 'content-moving-fade' ),
			);
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Move responsive settings.
		if ( $param = \WPBMap::getParam( 'vc_column', 'offset' ) ) {
			$param['weight'] = -1;
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Modify width (this moves the responsive tab to the end).
		if ( $param = \WPBMap::getParam( 'vc_column', 'width' ) ) {
			$param['weight'] = -1;
			\vc_update_shortcode_param( 'vc_column', $param );
		}

		// Move css.
		if ( $param = \WPBMap::getParam( 'vc_column', 'css' ) ) {
			$param['weight'] = -1;
			\vc_update_shortcode_param( 'vc_column', $param );
		}
	}

	/**
	 * Adds new params for the VC Rows.
	 */
	public static function add_params() {
		if ( ! \function_exists( '\vc_add_params' ) ) {
			return;
		}

		/*-----------------------------------------------------------------------------------*/
		/*  - Columns
		/*-----------------------------------------------------------------------------------*/

		// Array of params to add
		$column_params = [];

		$column_params[] = [
			'type'       => 'vcex_select',
			'heading'    => \esc_html__( 'Visibility', 'total' ),
			'param_name' => 'visibility',
			'weight'     => 99,
		];

		$column_params[] = [
			'type'        => 'textfield',
			'heading'     => \esc_html__( 'Animation Duration', 'total'),
			'param_name'  => 'animation_duration',
			'description' => \esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
		];

		$column_params[] = [
			'type'        => 'textfield',
			'heading'     => \esc_html__( 'CSS Animation Delay', 'total'),
			'param_name'  => 'css_animation_delay', // @todo rename to just animation_delay
			'description' => \esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
		];

		$column_params[] = [
			'type'        => 'textfield',
			'heading'     => \esc_html__( 'Minimum Height', 'total' ),
			'param_name'  => 'min_height',
			'description' => \esc_html__( 'You can enter a minimum height for this row.', 'total' ),
		];

		$column_params[] = [
			'type'       => 'dropdown',
			'heading'    => \esc_html__( 'Shadow', 'total' ),
			'param_name' => 'wpex_shadow',
			'value'      => \array_flip( (array) \wpex_utl_shadows() ),
		];

		$column_params[] = [
			'type'        => 'dropdown',
			'heading'     => \esc_html__( 'Typography Style', 'total' ),
			'param_name'  => 'typography_style',
			'value'       => \array_flip( \wpex_typography_styles() ),
			'description' => \esc_html__( 'Will alter the font colors of all child elements. This is an older setting that is somewhat deprecated.', 'total' ),
		];

		/* Design Options */
		$column_params[] = [
			'type' => 'vcex_colorpicker',
			'heading' => esc_html__( 'Background Color', 'total' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_color',
			'weight' => -2,
		];

		$column_params[] = [
			'type' => 'vcex_colorpicker',
			'heading' => esc_html__( 'Border Color', 'total' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_border_color',
			'weight' => -2,
		];

		$column_params[] = [
			'type' => 'dropdown',
			'heading' => esc_html__( 'Custom Background Image Source', 'total' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_image_source',
			'value' => WPB_Helpers::get_background_image_source_choices(),
			'weight' => -2,
		];

		$column_params[] = [
			'type' => 'vcex_custom_field',
			'choices' => 'image',
			'heading' => esc_html__( 'Background Image Custom Field', 'total-theme-core' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_image_custom_field',
			'dependency' => [ 'element' => 'wpex_bg_image_source', 'value' => 'custom_field' ],
			'weight' => -2,
		];

		$column_params[] = [
			'type' => 'textfield',
			'heading' => esc_html__( 'Background Image Position', 'total' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_position',
			'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
			'weight' => -2,
		];
	
		$column_params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Fixed Background Style', 'total' ),
			'param_name' => 'wpex_fixed_bg',
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'weight' => -2,
			'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
			'value' => [
				\esc_html__( 'None', 'total' ) => '',
				\esc_html__( 'Fixed', 'total' ) => 'fixed',
				\esc_html__( 'Fixed top', 'total' ) => 'fixed-top',
				\esc_html__( 'Fixed bottom', 'total' ) => 'fixed-bottom',
			],
			'description' => \esc_html__( 'Note: Fixed backgrounds are disabled on devices under 1080px to prevent issues with mobile devices that don\'t properly support them', 'total' ),
		];

		$column_params[] = [
			'type' => 'textfield',
			'heading' => esc_html__( 'Background Image Size', 'total' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_size',
			'description' => \esc_html__( 'Specify the size of the background image.', 'total' ) . ' (<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-size" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'see mozilla docs', 'total' ) . ' &#8599;</a>)',
			'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
			'weight' => -2,
		];

		$column_params[] = [
			'type'        => 'textfield',
			'heading'     => \esc_html__( 'Z-Index', 'total' ),
			'param_name'  => 'wpex_zindex',
			'group'       => \esc_html__( 'Design Options', 'js_composer' ),
			'description' => \esc_html__( 'Note: Adding z-index values on rows containing negative top/bottom margins will allow you to overlay the rows, however, this can make it hard to access the page builder tools in the frontend editor and you may need to use the backend editor to modify the overlapped rows.', 'total' ),
			'weight'      => -2,
			'dependency'  => [ 'element' => 'parallax', 'is_empty' => true ],
		];

		// Hidden fields = Deprecated params, these should be removed on save.
		$deprecated_column_params = [
			'id',
			'style',
			'typo_style',
			'bg_style',
			'drop_shadow',
			'wpex_featured_bg_image',
		];

		if ( WPB_Helpers::parse_deprecated_css_check( 'vc_column' ) ) {
			$deprecated_column_params = \array_merge( $deprecated_column_params, [
				'bg_color',
				'bg_image',
				'border_style',
				'border_color',
				'border_width',
				'margin_top',
				'margin_bottom',
				'margin_left',
				'margin_right',
				'padding_top',
				'padding_bottom',
				'padding_left',
				'padding_right',
			] );
		}

		foreach ( $deprecated_column_params as $param ) {
			$column_params[] = [
				'type'       => 'hidden',
				'param_name' => $param,
				'value'      => '',
			];
		}

		\vc_add_params( 'vc_column', $column_params );

		/*-----------------------------------------------------------------------------------*/
		/*  - Inner Columns
		/*-----------------------------------------------------------------------------------*/
		$inner_column_params = [];

		// Hidden fields = Deprecated params, these should be removed on save
		$deprecated_params = [
			'id',
			'style',
			'bg_style',
			'typo_style',
		];

		if ( WPB_Helpers::parse_deprecated_css_check( 'vc_column_inner' ) ) {
			$deprecated_params = \array_merge( $deprecated_params, [
				'bg_color',
				'bg_image',
				'border_style',
				'border_color',
				'border_width',
				'margin_top',
				'margin_bottom',
				'margin_left',
				'padding_top',
				'padding_bottom',
				'padding_left',
				'padding_right',
			] );
		}

		foreach ( $deprecated_params as $param ) {
			$inner_column_params[] = [
				'type'       => 'hidden',
				'param_name' => $param,
				'value'      => '',
			];
		}

		\vc_add_params( 'vc_column_inner', $inner_column_params );
	}

	/**
	 * Tweaks attributes on edit.
	 */
	public static function edit_form_fields( $atts ) {
		if ( ! is_array( $atts ) || empty( $atts ) ) {
			return $atts;
		}

		// Parse ID
		if ( empty( $atts['el_id'] ) && ! empty( $atts['id'] ) ) {
			$atts['el_id'] = $atts['id'];
			unset( $atts['id'] );
		}

		// Parse $atts['typo_style'] into $atts['typography_style']
		if ( empty( $atts['typography_style'] ) && ! empty( $atts['typo_style'] ) ) {
			if ( \in_array( $atts['typo_style'], \array_flip( \wpex_typography_styles() ) ) ) {
				$atts['typography_style'] = $atts['typo_style'];
				unset( $atts['typo_style'] );
			}
		}

		// Remove old style param and add it to the classes field
		$style = $atts['style'] ?? '';
		if ( 'bordered' === $style || 'boxed' === $style ) {
			if ( ! empty( $atts['el_class'] ) ) {
				$atts['el_class'] .= " {$style}-column";
			} else {
				$atts['el_class'] = "{$style}-column";
			}
			unset( $atts['style'] );
		}

		// Parse css
		if ( empty( $atts['css'] ) && WPB_Helpers::parse_deprecated_css_check( 'vc_column' ) ) {

			// Convert deprecated fields to css field.
			if ( \class_exists( '\TotalTheme\Integration\WPBakery\Deprecated_CSS_Params_Style' ) ) {
				$atts['css'] = Deprecated_CSS_Params_Style::generate_css( $atts );
			}

			// Unset deprecated vars.
			unset( $atts['bg_image'] );
			unset( $atts['bg_color'] );

			unset( $atts['margin_top'] );
			unset( $atts['margin_bottom'] );
			unset( $atts['margin_right'] );
			unset( $atts['margin_left'] );

			unset( $atts['padding_top'] );
			unset( $atts['padding_bottom'] );
			unset( $atts['padding_right'] );
			unset( $atts['padding_left'] );

			unset( $atts['border_width'] );
			unset( $atts['border_style'] );
			unset( $atts['border_color'] );

		}

		return $atts;
	}

	/**
	 * Tweaks attributes on edit.
	 */
	public static function edit_form_fields_vc_column( $atts ) {
		if ( ! empty( $atts['wpex_featured_bg_image'] ) && 'true' === $atts['wpex_featured_bg_image'] ) {
			$atts['wpex_bg_image_source'] = 'featured';
			unset( $atts['wpex_featured_bg_image'] );
		}
		return self::edit_form_fields( $atts, 'vc_column' );
	}

	/**
	 * Tweaks attributes on edit.
	 */
	public static function edit_form_fields_vc_column_inner( $atts ) {
		return self::edit_form_fields( $atts );
	}

	/**
	 * Tweak shortcode classes.
	 */
	public static function shortcode_classes( $class_string, $tag, $atts ) {
		if ( ! in_array( $tag, [ 'vc_column', 'vc_column_inner' ], true ) ) {
			return $class_string;
		}

		// Move 'wpb_column' to the front.
		$class_string = \str_replace( 'wpb_column', '', $class_string );
		$class_string = 'wpb_column ' . \trim( $class_string );

		// Remove colorfill class which VC adds extra margins to.
		$class_string = \str_replace( 'vc_col-has-fill', 'wpex-vc_col-has-fill', $class_string );

		if ( ! empty( $atts['wpex_bg_color'] ) && ! empty( $atts['wpex_border_color'] ) && ! str_contains( $class_string, 'wpex-vc_col-has-fill' ) ) {
			$class_string .= ' wpex-vc_col-has-fill';
		}

		// Visibility.
		if ( ! empty( $atts['visibility'] ) && $visibility_class = \totaltheme_get_visibility_class( $atts['visibility'] ) ) {
			$class_string .= " {$visibility_class}";
		}

		// Style => deprecated fallback.
		if ( ! empty( $atts['style'] ) && 'default' !== $atts['style'] && $col_style_safe = \sanitize_html_class( $atts['style'] ) ) {
			$class_string .= " {$col_style_safe}-column";
		}

		// Typography Style => deprecated fallback.
		if ( ! empty( $atts['typo_style'] ) && empty( $atts['typography_style'] ) ) {
			if ( $typo_class = \wpex_typography_style_class( $atts['typo_style'] ) ) {
				$class_string .= " {$typo_class}";
			}
		} elseif ( empty( $atts['typo_style'] ) && ! empty( $atts['typography_style'] ) ) {
			if ( $typo_class = \wpex_typography_style_class( $atts['typography_style'] ) ) {
				$class_string .= " {$typo_class}";
			}
		}

		return $class_string;
	}

	/**
	 * Customize the column HTML output.
	 */
	public static function custom_output( $output, $obj, $atts, $shortcode ) {
		if ( 'vc_column' !== $shortcode ) {
			return $output;
		}

		/* Outer Column Edits */

			// Add outer css.
			$outer_css = self::get_outer_css( $atts );
			if ( $outer_css ) {
				$output = self::insert_outer_css( $output, $outer_css );
			}

		/* Inner Column Edits */
			$add_inner_class = [];

			// Fix empty space after vc_column-inner classname - @todo Remove when WPBakery fixes.
			$output = \str_replace( 'class="vc_column-inner "', 'class="vc_column-inner"', $output );

			// Add inner css.
			$inner_css = self::get_inner_css( $atts );
			if ( $inner_css ) {
				$output = self::insert_inner_css( $output, $inner_css );
			}

			// Add Fixed background classname.
			if ( ! empty( $atts['wpex_fixed_bg'] ) ) {
				$fixed_bg_class = WPB_Helpers::get_fixed_background_class( $atts['wpex_fixed_bg'] );
				if ( $fixed_bg_class ) {
					$add_inner_class[] = $fixed_bg_class;
				}
			}

			// Custom Shadow classname
			if ( ! empty( $atts['wpex_shadow'] ) ) {
				$shadow_safe = \sanitize_html_class( (string) $atts['wpex_shadow'] );
				if ( $shadow_safe ) {
					$add_inner_class[] = "wpex-{$shadow_safe}";
				}
			}

			// Insert extra classes to the column.
			if ( $add_inner_class ) {
				$output = self::insert_column_class( $output, $add_inner_class );
			}

		return $output;
	}

	/**
	 * Parse column atts on the frontend (vc_column only).
	 */
	public static function shortcode_atts( $atts ) {
		if ( ! empty( $atts['wpex_featured_bg_image'] ) && 'true' === $atts['wpex_featured_bg_image'] ) {
			$atts['wpex_bg_image_source'] = 'featured';
			unset( $atts['wpex_featured_bg_image'] );
		}
		if ( $bg_image_id = self::get_background_image_id( $atts ) ) {
			$atts['background_image_id'] = $bg_image_id;
		}
		return $atts;
	}

	/**
	 * Get outer css.
	 */
	protected static function get_outer_css( $atts = [] ) {
		$outer_css = '';

		// Z-Index.
		if ( ! empty( $atts['wpex_zindex'] ) && $z_index_safe = \sanitize_text_field( $atts['wpex_zindex'] ) ) {
			$outer_css .= "z-index:{$z_index_safe}!important;";
		}

		// Animation duration.
		if ( ! empty( $atts['animation_duration'] ) && $animation_duration_safe = \floatval( $atts['animation_duration'] ) ) {
			$outer_css .= "animation-duration:{$animation_duration_safe}s;";
		}

		// Animation delay.
		if ( ! empty( $atts['css_animation_delay'] ) && $css_delay_safe = \floatval( $atts['css_animation_delay'] ) ) {
			$outer_css .= "animation-delay:{$css_delay_safe}s;";
		}

		return $outer_css;
	}

	/**
	 * Insert outer CSS.
	 */
	protected static function insert_outer_css( $output, $css ) {
		if ( ! $css ) {
			return $output;
		}
		$needle = 'class="wpb_column';
		$pos = strpos( $output, $needle );
		if ( $pos !== false ) {
			$replace = 'style="' . \esc_attr( $css ) . '" class="wpb_column';
			$output = \substr_replace( $output, $replace, $pos, \strlen( $needle ) );
		}
		return $output;
	}

	/**
	 * Get inner css.
	 */
	protected static function get_inner_css( $atts = [] ) {
		$inner_css = '';

		// Min Height.
		if ( ! empty( $atts['min_height'] ) ) {
			if ( \is_numeric( $atts['min_height'] ) ) {
				$atts['min_height'] = \intval( $atts['min_height'] ) . 'px';
			}
			$min_height_safe = \esc_attr( $atts['min_height'] );
			$inner_css .= "min-height:{$min_height_safe};";
		}

		// Inline css styles => Fallback For OLD Total Params - @deprecated in 4.9.
		if ( empty( $atts['css'] )
			&& WPB_Helpers::parse_deprecated_css_check( 'vc_column' )
			&& \class_exists( 'TotalTheme\Integration\WPBakery\Deprecated_CSS_Params_Style' )
		) {
			$inner_css .= Deprecated_CSS_Params_Style::generate_css( $atts, 'inline_css' );
		}

		// Background color
		if ( ! empty( $atts['wpex_bg_color'] ) && $bg_color_parsed = wpex_parse_color( $atts['wpex_bg_color'] ) ) {
			$inner_css .= 'background-color:' . \esc_attr( $bg_color_parsed ) . '!important;';
		}

		// Border color
		if ( ! empty( $atts['wpex_border_color'] ) && $border_color_parsed = wpex_parse_color( $atts['wpex_border_color'] ) ) {
			$inner_css .= 'border-color:' . \esc_attr( $border_color_parsed ) . '!important;';
		}

		// Settings that should only get added if parallax is disabled.
		if ( empty( $atts['parallax'] ) ) {

			// Need to re-check deprecated param here because we can't access the parsed $atts hre.
			if ( ! empty( $atts['wpex_featured_bg_image'] ) && 'true' === $atts['wpex_featured_bg_image'] ) {
				$atts['wpex_bg_image_source'] = 'featured';
				unset( $atts['wpex_featured_bg_image'] );
			}

			// Custom background image. @note the bg image was never added when parallax was enabled for columns.
			$atts['background_image_id'] = self::get_background_image_id( $atts );
			if ( ! empty( $atts['background_image_id'] )
				&& $background_image_url = \wp_get_attachment_image_url( $atts['background_image_id'], 'full' )
			) {
				$inner_css .= 'background-image:url(' . \esc_url( $background_image_url ) . ')';
				if ( ! isset( $atts['wpex_bg_image_source'] )
					|| 'featured' !== $atts['wpex_bg_image_source']
					|| \apply_filters( 'wpex_vc_column_featured_bg_image_has_important', true )
				) {
					$inner_css .= '!important';
				}
				$inner_css .= ';';
			}

			// Background position.
			if ( ! empty( $atts['wpex_bg_position'] ) && $bg_position_safe = \sanitize_text_field( $atts['wpex_bg_position'] ) ) {
				$inner_css .= "background-position:{$bg_position_safe}!important;";
			}

			// Background size.
			if ( ! empty( $atts['wpex_bg_size'] ) && $bg_size_safe = \sanitize_text_field( $atts['wpex_bg_size'] ) ) {
				$inner_css .= "background-size:{$bg_size_safe}!important;";
			}
		}

		return $inner_css;
	}

	/**
	 * Insert inner CSS.
	 */
	protected static function insert_inner_css( $output, $css ) {
		if ( ! $css ) {
			return $output;
		}
		$needle = 'class="vc_column-inner';
		$pos = \strpos( $output, $needle );
		if ( false !== $pos ) {
			$replace = 'style="' . \esc_attr( $css ) . '" class="vc_column-inner';
			$output = \substr_replace( $output, $replace, $pos, \strlen( $needle ) );
		}
		return $output;
	}

	/**
	 * Insert column classes helper function.
	 */
	protected static function insert_column_class( $output, $class = '' ) {
		if ( ! $class ) {
			return $output;
		}
		$needle = 'class="vc_column-inner';
		$pos = \strpos( $output, $needle );
		if ( false !== $pos ) {
			if ( \is_array( $class ) ) {
				$class = \array_filter( $class );
				$class = \array_unique( $class );
				$class = \implode( ' ', $class );
			}
			$replace = 'class="vc_column-inner ' . \esc_attr( $class );
			$output = \substr_replace( $output, $replace, $pos, \strlen( $needle ) );
		}
		return $output;
	}

	/**
	 * Returns background image ID.
	 */
	private static function get_background_image_id( $atts ) {
		if ( ! empty( $atts['wpex_bg_image_source'] )
			&& \class_exists( 'TotalThemeCore\Vcex\Helpers\Get_Image_From_Source' )
			&& $bg_image = (new \TotalThemeCore\Vcex\Helpers\Get_Image_From_Source( $atts['wpex_bg_image_source'], $atts ))->get()
		) {
			if ( ! \is_numeric( $bg_image ) ) {
				$bg_image = \attachment_url_to_postid( $bg_image );
			}
			return $bg_image;
		}
	}

}

