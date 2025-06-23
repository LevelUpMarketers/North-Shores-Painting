<?php

namespace TotalTheme\Integration\WPBakery\Elements;

use TotalTheme\Integration\WPBakery\Helpers as WPB_Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Section Tweaks.
 */
final class Section {

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
		\add_filter( 'shortcode_atts_vc_section', [ self::class, 'parse_attributes' ], 99 );
		\add_filter( 'wpex_vc_section_wrap_atts', [ self::class, 'wrap_attributes' ], 10, 2 );
		\add_filter( 'vc_shortcode_output', [ self::class, 'custom_output' ], 10, 4 );

		if ( \defined( '\VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
			\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ self::class, 'shortcode_classes' ], 10, 3 );
		}

		if ( \function_exists( '\vc_post_param' ) && 'vc_edit_form' === \vc_post_param( 'action' ) ) {
			\add_filter( 'vc_edit_form_fields_attributes_vc_section', [ self::class, 'edit_form_fields'] );
		}
	}

	/**
	 * Add custom params.
	 */
	public static function add_params() {
		if ( ! \function_exists( '\vc_add_params' ) ) {
			return;
		}

		$params = [
			[
				'type' => 'dropdown',
				'heading' => \esc_html__( 'Access', 'total' ),
				'param_name' => 'vcex_user_access',
				'weight' => 99,
				'value' => WPB_Helpers::get_user_access_choices(),
			],
			[
				'type' => 'dropdown',
				'heading' => \esc_html__( 'Custom Access', 'total' ),
				'param_name' => 'vcex_user_access_callback',
				'value' => WPB_Helpers::get_user_access_custom_choices(),
				'description' => sprintf( \esc_html__( 'Custom Access functions must be %swhitelisted%s for security reasons.', 'total' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' ),
				'weight' => 99,
				'dependency' => [ 'element' => 'vcex_user_access', 'value' => 'custom' ],
			],
			[
				'type' => 'vcex_select',
				'heading' => \esc_html__( 'Visibility', 'total' ),
				'param_name' => 'visibility',
				'weight' => 99,
			],
			[
				'type' => 'textfield',
				'heading' => \esc_html__( 'Local Scroll ID', 'total' ),
				'param_name' => 'local_scroll_id',
				'description' => \esc_html__( 'Unique identifier for local scrolling links.', 'total' ),
				'weight' => 99,
			],
			[
				'type' => 'textfield',
				'heading' => \esc_html__( 'Minimum Height', 'total' ),
				'description' => \esc_html__( 'Adds a minimum height to the row so you can have a row without any content but still display it at a certain height. Such as a background with a video or image background but without any content.', 'total' ),
				'param_name' => 'min_height',
			],
			// Design Options.
			[
				'type' => 'vcex_colorpicker',
				'heading' => esc_html__( 'Background Color', 'total' ),
				'group' => \esc_html__( 'Design Options', 'js_composer' ),
				'param_name' => 'wpex_bg_color',
				'weight' => -2,
			],
			[
				'type' => 'vcex_colorpicker',
				'heading' => esc_html__( 'Border Color', 'total' ),
				'group' => \esc_html__( 'Design Options', 'js_composer' ),
				'param_name' => 'wpex_border_color',
				'weight' => -2,
			],
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Custom Background Image Source', 'total' ),
				'group' => \esc_html__( 'Design Options', 'js_composer' ),
				'param_name' => 'wpex_bg_image_source',
				'value' => WPB_Helpers::get_background_image_source_choices(),
				'weight' => -2,
			],
			[
				'type' => 'vcex_custom_field',
				'choices' => 'image',
				'heading' => esc_html__( 'Background Image Custom Field', 'total-theme-core' ),
				'group' => \esc_html__( 'Design Options', 'js_composer' ),
				'param_name' => 'wpex_bg_image_custom_field',
				'dependency' => [ 'element' => 'wpex_bg_image_source', 'value' => 'custom_field' ],
				'weight' => -2,
			],
			[
				'type' => 'textfield',
				'heading' => esc_html__( 'Background Image Position', 'total' ),
				'group' => \esc_html__( 'Design Options', 'js_composer' ),
				'param_name' => 'wpex_bg_position',
				'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
				'weight' => -2,
			],
			[
				'type' => 'dropdown',
				'heading' => \esc_html__( 'Fixed Background Style', 'total' ),
				'param_name' => 'wpex_fixed_bg',
				'group' => \esc_html__( 'Design Options', 'js_composer' ),
				'value' => [
					\esc_html__( 'None', 'total' ) => '',
					\esc_html__( 'Fixed', 'total' ) => 'fixed',
					\esc_html__( 'Fixed top', 'total' ) => 'fixed-top',
					\esc_html__( 'Fixed bottom', 'total' ) => 'fixed-bottom',
				],
				'description' => \esc_html__( 'Note: Fixed backgrounds are disabled on devices under 1080px to prevent issues with mobile devices that don\'t properly support them', 'total' ),
				'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
				'weight' => -2,
			],
			[
				'type' => 'textfield',
				'heading' => esc_html__( 'Background Image Size', 'total' ),
				'group' => \esc_html__( 'Design Options', 'js_composer' ),
				'param_name' => 'wpex_bg_size',
				'description' => \esc_html__( 'Specify the size of the background image.', 'total' ) . ' (<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-size" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'see mozilla docs', 'total' ) . ' &#8599;</a>)',
				'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
				'weight' => -2,
			],
			[
				'type' => 'textfield',
				'heading' => \esc_html__( 'Z-Index', 'total' ),
				'param_name' => 'wpex_zindex',
				'group' => \esc_html__( 'Design Options', 'total' ),
				'description' => \esc_html__( 'Note: Adding z-index values on rows containing negative top/bottom margins will allow you to overlay the rows, however, this can make it hard to access the page builder tools in the frontend editor and you may need to use the backend editor to modify the overlapped rows.', 'total' ),
				'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
				'weight' => -2,
			],
			// Deprecated
			[ 'type' => 'hidden', 'param_name' => 'wpex_post_thumbnail_bg', 'value' => '' ], // @since 5.17
		];

		\vc_add_params( 'vc_section', $params );
	}

	/**
	 * Modify core params.
	 */
	public static function modify_params() {
		if ( ! \function_exists( '\vc_update_shortcode_param' ) ) {
			return;
		}

		// Move el_id.
		$param = \WPBMap::getParam( 'vc_section', 'el_id' );
		if ( $param ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_section', $param );
		}

		// Move el_class.
		$param = \WPBMap::getParam( 'vc_section', 'el_class' );
		if ( $param ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_section', $param );
		}

		// Move css_animation.
		$param = \WPBMap::getParam( 'vc_section', 'css_animation' );
		if ( $param ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_section', $param );
		}

		// Move full_width.
		$param = \WPBMap::getParam( 'vc_section', 'full_width' );
		if ( $param ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_section', $param );
		}

		// Move css.
		$param = \WPBMap::getParam( 'vc_section', 'css' );
		if ( $param ) {
			$param['group'] = \esc_html__( 'Design Options', 'total' );
			$param['weight'] = -1;
			\vc_update_shortcode_param( 'vc_section', $param );
		}
	}

	/**
	 * Tweaks section attributes on edit.
	 */
	public static function edit_form_fields( $atts ) {
		if ( ! empty( $atts['wpex_post_thumbnail_bg'] ) && 'true' === $atts['wpex_post_thumbnail_bg'] ) {
			$atts['wpex_bg_image_source'] = 'featured';
			unset( $atts['wpex_post_thumbnail_bg'] );
		}
		return $atts;
	}

	/**
	 * Parse VC section attributes on front-end.
	 */
	public static function parse_attributes( $atts ) {
		if ( ! empty( $atts['full_width'] )
			&& \apply_filters( 'wpex_boxed_layout_vc_stretched_rows_reset', true )
			&& 'boxed' === \wpex_site_layout()
		) {
			$atts['full_width'] = '';
			$atts['full_width_boxed_layout'] = 'true';
		}

		// Custom background image.
		if ( ! empty( $atts['wpex_post_thumbnail_bg'] ) && 'true' === $atts['wpex_post_thumbnail_bg'] ) {
			$atts['wpex_bg_image_source'] = 'featured';
			unset( $atts['wpex_post_thumbnail_bg'] );
		}
		
		if ( ! empty( $atts['wpex_bg_image_source'] )
			&& \class_exists( 'TotalThemeCore\Vcex\Helpers\Get_Image_From_Source' )
			&& $bg_image = (new \TotalThemeCore\Vcex\Helpers\Get_Image_From_Source( $atts['wpex_bg_image_source'], $atts ))->get()
		) {
			if ( ! \is_numeric( $bg_image ) ) {
				$bg_image = \attachment_url_to_postid( $bg_image );
			}
			$atts['background_image_id'] = $bg_image;
		}

		return $atts;
	}

	/**
	 * Add custom attributes to the row wrapper.
	 */
	public static function wrap_attributes( $wrapper_attributes, $atts ) {
		$inline_style = '';

		// Local scroll ID
		if ( ! empty( $atts['local_scroll_id'] ) ) {
			$wrapper_attributes[] = 'data-ls_id="#' . \esc_attr( $atts['local_scroll_id'] ) . '"';
			$wrapper_attributes[] = 'tabindex="-1"';
		}

		// Min Height
		if ( ! empty( $atts['min_height'] ) && $min_height_safe = \sanitize_text_field( $atts['min_height'] ) ) {
			if ( \is_numeric( $min_height_safe ) ) {
				$min_height_safe = \intval( $min_height_safe ) . 'px';
			}
			$inline_style .= "min-height:{$min_height_safe};";
		}

		// Z-Index
		if ( ! empty( $atts['wpex_zindex'] ) && $z_index_safe = \sanitize_text_field( $atts['wpex_zindex'] ) ) {
			$inline_style .= "z-index:{$z_index_safe}!important;";
		}

		// Background color
		if ( ! empty( $atts['wpex_bg_color'] ) && $bg_color_parsed = wpex_parse_color( $atts['wpex_bg_color'] ) ) {
			$inline_style .= 'background-color:' . \esc_attr( $bg_color_parsed ) . '!important;';
		}

		// Border color
		if ( ! empty( $atts['wpex_border_color'] ) && $border_color_parsed = wpex_parse_color( $atts['wpex_border_color'] ) ) {
			$inline_style .= 'border-color:' . \esc_attr( $border_color_parsed ) . '!important;';
		}
		
		// Custom background image
		if ( ! empty( $atts['background_image_id'] )
			&& $background_image_url = \wp_get_attachment_image_url( $atts['background_image_id'], 'full' )
		) {
			$inline_style .= 'background-image:url(' . \esc_url( $background_image_url ) . ')!important;';
		}

		// Settings that should only get added if parallax is disabled.
		if ( empty( $atts['parallax'] ) ) {

			// Background position.
			if ( ! empty( $atts['wpex_bg_position'] ) && $bg_position_safe = \sanitize_text_field( $atts['wpex_bg_position'] ) ) {
				$inline_style .= "background-position:{$bg_position_safe}!important;";
			}

			// Background size.
			if ( ! empty( $atts['wpex_bg_size'] ) && $bg_size_safe = \sanitize_text_field( $atts['wpex_bg_size'] ) ) {
				$inline_style .= "background-size:{$bg_size_safe}!important;";
			}

		}

		// Add inline style to wrapper attributes.
		if ( $inline_style ) {
			$wrapper_attributes[] = 'style="' . \esc_attr( $inline_style ) . '"';
		}

		return $wrapper_attributes;
	}

	/**
	 * Tweak shortcode classes.
	 */
	public static function shortcode_classes( $class_string, $tag, $atts ) {
		if ( 'vc_section' !== $tag ) {
			return $class_string;
		}

		$add_classes = [];

		// Relative classname
		if ( ! \str_contains( $class_string, 'wpex-relative' ) && ! \str_contains( $class_string, 'wpex-sticky' ) ) {
			$add_classes[] ='wpex-relative';
		}

		if ( \str_contains( $class_string, 'vc_section-has-fill' ) ) {
			$class_string = \str_replace( 'vc_section-has-fill', '', $class_string );
			$add_classes['wpex-vc_section-has-fill'] = 'wpex-vc_section-has-fill';
		} elseif ( ! empty( $atts['vcex_parallax'] )
			|| ! empty( $atts['wpex_self_hosted_video_bg'] )
			|| ! empty( $atts['background_image_id'] )
			|| ! empty( $atts['wpex_bg_color'] )
			|| ! empty( $atts['wpex_border_color'] )
			|| ( ! empty( $atts['el_class'] ) && is_string( $atts['el_class'] )
				&& ( \str_contains( $atts['el_class'], 'wpex-surface-' ) || \str_contains( $atts['el_class'], 'wpex-bg-' ) )
			)
		) {
			$add_classes['wpex-vc_section-has-fill'] = 'wpex-vc_section-has-fill';
		}

		if ( ! empty( $atts['visibility'] ) ) {
			$add_classes[] = \totaltheme_get_visibility_class( $atts['visibility'] );
		}

		if ( ! empty( $atts['full_width'] ) ) {
			$add_classes[] = 'wpex-vc-row-stretched';
		}

		if ( ! empty( $atts['full_width_boxed_layout'] ) ) {
			$add_classes[] = 'wpex-vc-section-boxed-layout-stretched';
		}

		if ( empty( $atts['full_width'] ) && isset( $add_classes['wpex-vc_section-has-fill'] ) ) {
			$add_classes[] = totaltheme_has_classic_styles() ? 'wpex-vc-reset-negative-margin' : 'wpex-vc_section-mx-0';
		}

		if ( ! empty( $atts['wpex_fixed_bg'] ) ) {
			$add_classes[] = WPB_Helpers::get_fixed_background_class( $atts['wpex_fixed_bg'] );
		}

		if ( $add_classes ) {
			$add_classes = \array_map( 'esc_attr', $add_classes ); // @note can't use sanitize_html_class.
			$add_classes = \array_filter( $add_classes );
			if ( $add_classes ) {
				$class_string .= ' ' . \implode( ' ', $add_classes );
			}
		}

		$class_string = \totaltheme_replace_vars( $class_string );

		return $class_string;
	}

	/**
	 * Custom HTML output.
	 */
	public static function custom_output( $output, $obj, $atts, $shortcode ) {
		if ( 'vc_section' !== $shortcode ) {
			return $output;
		}
		if ( ! WPB_Helpers::shortcode_has_access( $atts ) ) {
			return;
		}

		return \totaltheme_replace_vars( $output );
	}

}
