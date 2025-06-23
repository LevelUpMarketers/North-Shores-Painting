<?php

namespace TotalTheme\Integration\WPBakery\Elements;

use VCEX_Parse_Row_Atts;
use TotalTheme\Integration\WPBakery\Deprecated_CSS_Params_Style;
use TotalTheme\Integration\WPBakery\Helpers as WPB_Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Row Tweaks.
 */
final class Row {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Initialize the class.
	 */
	public static function init(): void {
		if ( ! \class_exists( '\TotalTheme\Integration\WPBakery\Helpers' ) ) {
			return;
		}
		
		// Register hooks.
		\add_action( 'vc_after_init', [ self::class, 'add_params' ], 40 ); // add params first
		\add_action( 'vc_after_init', [ self::class, 'modify_params'], 40 ); // priority is crucial.
		\add_filter( 'shortcode_atts_vc_row', [ self::class, 'parse_row_atts'], 99 );
		\add_filter( 'wpex_vc_row_wrap_atts', [ self::class, 'wrap_attributes' ], 10, 2 );
		\add_filter( 'wpex_hook_vc_row_top', [ self::class, 'maybe_add_header_overlay_offset' ], 1, 2 );
		\add_filter( 'vc_shortcode_output', [ self::class, 'custom_output' ], 10, 4 );

		if ( \defined( '\VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
			\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ self::class, 'shortcode_classes' ], 10, 3 );
		}

		// Deprecated center row - we use latest and earliest hooks to prevent conflicts with
		// parallax bgs, overlays, etc.
		\add_filter( 'wpex_hook_vc_row_top', [ self::class, 'center_row_open' ], 100, 2 );
		\add_filter( 'wpex_hook_vc_row_bottom', [ self::class, 'center_row_close' ], 1, 2 );

		if ( \function_exists( '\vc_post_param' ) && 'vc_edit_form' === \vc_post_param( 'action' ) ) {
			\add_filter( 'vc_edit_form_fields_attributes_vc_row', [ self::class, 'edit_form_fields'] );
		}
	}

	/**
	 * Adds new params to vc_map.
	 */
	public static function add_params(): void {
		if ( \function_exists( '\vc_add_params' ) ) {
			\vc_add_params( 'vc_row', self::get_custom_params() );
			\vc_add_params( 'vc_row_inner', self::get_inner_custom_params() );
		}
	}

	/**
	 * Get custom params for rows.
	 */
	protected static function get_custom_params(): array {
		$params = [];

		$params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Access', 'total' ),
			'param_name' => 'vcex_user_access',
			'weight' => 99,
			'value' => WPB_Helpers::get_user_access_choices(),
		];
	
		$params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Custom Access', 'total' ),
			'param_name' => 'vcex_user_access_callback',
			'value' => WPB_Helpers::get_user_access_custom_choices(),
			'description' => sprintf( \esc_html__( 'Custom Access functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' ),
			'weight' => 99,
			'dependency' => [ 'element' => 'vcex_user_access', 'value' => 'custom' ],
		];
		
		$params[] = [
			'type' => 'vcex_select',
			'heading' => \esc_html__( 'Visibility', 'total' ),
			'param_name' => 'visibility',
			'weight' => 99,
		];
	
		$params[] = [
			'type' => 'textfield',
			'heading' => \esc_html__( 'Local Scroll ID', 'total' ),
			'param_name' => 'local_scroll_id',
			'description' => \esc_html__( 'Unique identifier for local scrolling links.', 'total' ),
			'weight' => 99,
		];
	
		$params[] = [
			'type' => 'textfield',
			'heading' => \esc_html__( 'Minimum Height', 'total' ),
			'description' => \esc_html__( 'Adds a minimum height to the row so you can have a row without any content but still display it at a certain height. Such as a background with a video or image background but without any content.', 'total' ),
			'param_name' => 'min_height',
		];

		if ( 'boxed' !== get_theme_mod( 'main_layout_style' ) && \wp_validate_boolean( \get_theme_mod( 'wpb_full_width_css_enable', true ) ) ) {
			$params[] = [
				'type' => 'textfield',
				'heading' => \esc_html__( 'Row Stretch Side Margin', 'total' ),
				'description' => \esc_html__( 'Important: This setting will only work with CSS based stretched rows, so if this row has any side margin or padding added in the "Design Options" tab it won\'t work. It will also be ignored for the "boxed" site layout.', 'total' ),
				'dependency' => [ 'element' => 'full_width', 'not_empty' => true ],
				'param_name' => 'full_width_margin',
			];
		}
	
		$params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Max Width', 'total' ),
			'param_name' => 'max_width',
			'value' => [
				\esc_html__( 'None', 'total' ) => '',
				'10%' => '10',
				'20%' => '20',
				'30%' => '30',
				'40%' => '40',
				'50%' => '50',
				'60%' => '60',
				'70%' => '70',
				'80%' => '80',
			],
			'description' => \esc_html__( 'The max width is done by setting a percentage margin on the left and right of your row. You can visit the Design Options tab to enter custom percentage margins yourself if you prefer. Or use the "Custom Max Width" option below to enter a custom max-width value.', 'total' ),
			'dependency' => [ 'element' => 'full_width', 'is_empty' => true ],
		];
	
		$params[] = [
			'type' => 'textfield',
			'heading' => \sprintf( \esc_html__( 'Max Width %s', 'total' ), '(px)' ),
			'param_name' => 'max_width_custom',
			'dependency' => [ 'element' => 'full_width', 'is_empty' => true ],
		];
	
		$params[] = [
			'type' => 'vcex_text_align',
			'std' => 'center',
			'heading' => \esc_html__( 'Aligment', 'total' ),
			'param_name' => 'max_width_align',
			'dependency' => [ 'element' => 'max_width_custom', 'not_empty' => true ],
		];
	
		$params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Use Content/Sidebar Layout', 'total' ),
			'param_name' => 'has_sidebar',
			'value' => [
				\esc_html__( 'Disabled', 'total' ) => '',
				\esc_html__( 'Right column is sidebar', 'total' ) => 'right',
				\esc_html__( 'Left column is sidebar', 'total' ) => 'left',
			],
			'description' => \esc_html__( 'When enabled the theme will make use of the default content and sidebar widths defined under Customize > Layout. This is useful for dynamic templates when you want to insert your sidebar to a row and keep consistency with the rest of the site. Make sure you select a 2 column layout.', 'total' ),
		];
	
		$params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Inner Column Gap', 'total' ),
			'param_name' => 'column_spacing',
			'value' => self::get_column_spacing_preset_choices(),
			'description' => \esc_html__( 'Alter the inner column spacing. This setting will also apply to inner rows. It is recommended to set the value of this setting to 0px when creating custom cards.', 'total' ),
			'weight' => 40,
		];
	
		$params[] = [
			'type' => 'vcex_ofswitch',
			'std' => 'false',
			'heading' => \esc_html__( 'Remove Bottom Column Margin', 'total' ),
			'param_name' => 'remove_bottom_col_margin',
			'description' => \esc_html__( 'Enable to remove the default bottom margin on all the columns inside this row.', 'total' ),
		];
	
		$params[] = [
			'type' => 'vcex_ofswitch',
			'std' => 'no',
			'heading' => \esc_html__( 'Float Columns Right', 'total' ),
			'param_name' => 'columns_right',
			'vcex' => [ 'off' => 'no', 'on' => 'yes', ],
			'description' => \esc_html__( 'Most useful when you want to alternate content such as an image to the right and content to the left but display the image at the top on mobile.', 'total' ),
		];

		$params[] = [
			'type' => 'vcex_ofswitch',
			'std' => 'false',
			'heading' => \esc_html__( 'Hide if Empty', 'total' ),
			'param_name' => 'hide_if_empty',
			'description' => \esc_html__( 'The row will not display on the page if the inner contents are empty. To determine if a row is empty all HTML tags are removed except images and plain text, if the end result is an empty string the row is considered empty.', 'total' ),
		];
	
		$params[] = [
			'type' => 'vcex_ofswitch',
			'heading' => \esc_html__( 'Transparent Header Offset', 'total' ),
			'param_name' => 'offset_overlay_header',
			'vcex' => [ 'off' => 'no', 'on' => 'yes', ],
			'std' => 'no',
			'description' => \esc_html__( 'Check this box to add an space before this row equal to the height of your header to prevent the content from going behind your Transparent Header. This option will use javascript on page load. For a better result, we recommend using an empty row with a spacing element to manually offset the height of your header.', 'total' ),
		];
	
		$params[] = [
			'type' => 'vcex_ofswitch',
			'heading' => \esc_html__( 'Full-Width Columns On Tablets', 'total' ),
			'param_name' => 'tablet_fullwidth_cols',
			'vcex' => [ 'off' => 'no', 'on' => 'yes' ],
			'std' => 'no',
			'description' => \esc_html__( 'Enable to make all columns inside this row full-width for tablets', 'total' ) . ' (min-width: 768px) and (max-width: 959px). This is a legacy setting added prior to the introduction of responsive column settings.',
		];
	
		// Design options.
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

		$params[] = [
			'type' => 'dropdown',
			'heading' => esc_html__( 'Custom Background Image Source', 'total' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_image_source',
			'value' => WPB_Helpers::get_background_image_source_choices(),
			'weight' => -2,
		];

		$params[] = [
			'type' => 'dropdown',
			'heading' => esc_html__( 'Custom Background Image Source', 'total' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_image_source',
			'value' => WPB_Helpers::get_background_image_source_choices(),
			'weight' => -2,
		];

		$params[] = [
			'type' => 'vcex_custom_field',
			'choices' => 'image',
			'heading' => esc_html__( 'Background Image Custom Field', 'total-theme-core' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_image_custom_field',
			'dependency' => [ 'element' => 'wpex_bg_image_source', 'value' => 'custom_field' ],
			'weight' => -2,
		];

		$params[] = [
			'type' => 'textfield',
			'heading' => esc_html__( 'Background Image Position', 'total' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_position',
			'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
			'weight' => -2,
		];
	
		$params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Fixed Background Style', 'total' ),
			'param_name' => 'wpex_fixed_bg',
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'weight' => -2,
			'value' => [
				\esc_html__( 'None', 'total' ) => '',
				\esc_html__( 'Fixed', 'total' ) => 'fixed',
				\esc_html__( 'Fixed top', 'total' ) => 'fixed-top',
				\esc_html__( 'Fixed bottom', 'total' ) => 'fixed-bottom',
			],
			'description' => \esc_html__( 'Note: Fixed backgrounds are disabled on devices under 1080px to prevent issues with mobile devices that don\'t properly support them', 'total' ),
			'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
		];

		$params[] = [
			'type' => 'textfield',
			'heading' => esc_html__( 'Background Image Size', 'total' ),
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'param_name' => 'wpex_bg_size',
			'description' => \esc_html__( 'Specify the size of the background image.', 'total' ) . ' (<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-size" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'see mozilla docs', 'total' ) . ' &#8599;</a>)',
			'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
			'weight' => -2,
		];
		
		$params[] = [
			'type' => 'textfield',
			'heading' => \esc_html__( 'Z-Index', 'total' ),
			'param_name' => 'wpex_zindex',
			'group' => \esc_html__( 'Design Options', 'js_composer' ),
			'description' => \esc_html__( 'Note: Adding z-index values on rows containing negative top/bottom margins will allow you to overlay the rows, however, this can make it hard to access the page builder tools in the frontend editor and you may need to use the backend editor to modify the overlapped rows.', 'total' ),
			'dependency' => [ 'element' => 'parallax', 'is_empty' => true ],
			'weight' => -2,
		];
		
		$params[] = [
			'type' => 'vcex_ofswitch',
			'heading' => \esc_html__( 'Center Row Content', 'total' ),
			'param_name' => 'center_row',
			'vcex' => [ 'off' => 'no', 'on' => 'yes', ],
			'std' => 'no',
			'dependency' => [ 'element' => 'full_width', 'is_empty' => true ],
			'description' => \esc_html__( 'Enable to center your row content while using a "Full Screen" page layout. This is a legacy setting added prior to the introduction of the "Row stretch" setting.', 'total' ),
		];

		$params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Typography Style (soft deprecated)', 'total' ),
			'param_name' => 'typography_style',
			'value' => \array_flip( \wpex_typography_styles() ),
			'description' => \esc_html__( 'Will alter the font colors of all child elements. This is an older setting that is somewhat deprecated.', 'total' ),
		];

		// Deprecated params.
		$params[] = [ 'type' => 'hidden', 'param_name' => 'id', 'value' => '' ];
		$params[] = [ 'type' => 'hidden', 'param_name' => 'style', 'value' => '' ];
		$params[] = [ 'type' => 'hidden', 'param_name' => 'bg_style', 'value' => '' ];
		$params[] = [ 'type' => 'hidden', 'param_name' => 'no_margins', 'value' => '' ];
		$params[] = [ 'type' => 'hidden', 'param_name' => 'video_bg_overlay', 'value' => '' ];
		$params[] = [ 'type' => 'hidden', 'param_name' => 'match_column_height', 'value' => '' ];
		$params[] = [ 'type' => 'hidden', 'param_name' => 'wpex_post_thumbnail_bg', 'value' => '' ]; // @since 5.17

		if ( WPB_Helpers::parse_deprecated_css_check( 'vc_row' ) ) {
			$css_options = [
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
			];
			foreach ( $css_options as $param ) {
				$params[] = [
					'type'       => 'hidden',
					'param_name' => $param,
					'value'      => '',
				];
			}
		}

		return $params;
	}

	/**
	 * Get custom params for inner rows.
	 */
	public static function get_inner_custom_params(): array {
		$params = [];

		$params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Access', 'total' ),
			'param_name' => 'vcex_user_access',
			'weight' => 99,
			'value' => WPB_Helpers::get_user_access_choices(),
		];

		$params[] = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Custom Access', 'total' ),
			'param_name' => 'vcex_user_access_callback',
			'value' => WPB_Helpers::get_user_access_custom_choices(),
			'description' => sprintf( \esc_html__( 'Custom Access functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' ),
			'weight' => 99,
			'dependency' => [ 'element' => 'vcex_user_access', 'value' => 'custom' ],
		];

		$params[] =	[
			'type' => 'vcex_select',
			'heading' => \esc_html__( 'Visibility', 'total' ),
			'param_name' => 'visibility',
			'weight' => 99,
		];

		if ( ! totaltheme_has_classic_styles() ) {
			$params[] = [
				'type' => 'dropdown',
				'heading' => \esc_html__( 'Inner Column Gap', 'total' ),
				'param_name' => 'column_spacing',
				'value' => self::get_column_spacing_preset_choices(),
				'description' => \esc_html__( 'Alter the inner column spacing. This setting will also apply to inner rows. It is recommended to set the value of this setting to 0px when creating custom cards.', 'total' ),
				'weight' => 40,
			];
		}

		$params[] =	[
			'type'        => 'vcex_ofswitch',
			'heading'     => \esc_html__( 'Remove Bottom Column Margin', 'total' ),
			'param_name'  => 'remove_bottom_col_margin',
			'std'         => 'false',
			'description' => \esc_html__( 'Enable to remove the default bottom margin on all the columns inside this row.', 'total' ),
		];

		$params[] =	[
			'type'        => 'vcex_ofswitch',
			'heading'     => \esc_html__( 'Float Columns Right', 'total' ),
			'param_name'  => 'columns_right',
			'vcex'        => [ 'off' => 'no', 'on' => 'yes', ],
			'std'         => 'no',
			'description' => \esc_html__( 'Most useful when you want to alternate content such as an image to the right and content to the left but display the image at the top on mobile.', 'total' ),
		];

		return $params;
	}

	/**
	 * Modify default params.
	 */
	public static function modify_params(): void {
		if ( ! \function_exists( '\vc_update_shortcode_param' ) ) {
			return;
		}

		// Move row title to top
		if ( $param = \WPBMap::getParam( 'vc_row', 'row_title' ) ) {
			$param['weight'] = 1000;
			\vc_update_shortcode_param( 'vc_row', $param );
		}

		// Move el_id
		if ( $param = \WPBMap::getParam( 'vc_row', 'el_id' ) ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_row', $param );
		}

		// Move el_class
		if ( $param = \WPBMap::getParam( 'vc_row', 'el_class' ) ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_row', $param );
		}

		// Move css_animation
		if ( $param = \WPBMap::getParam( 'vc_row', 'css_animation' ) ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_row', $param );
		}

		// Move full_width
		if ( $param = \WPBMap::getParam( 'vc_row', 'full_width' ) ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_row', $param );
		}

		// Move content_placement
		if ( $param = \WPBMap::getParam( 'vc_row', 'content_placement' ) ) {
			$param['weight'] = 99;
			\vc_update_shortcode_param( 'vc_row', $param );
		}

		// Modify the VC gap setting
		$elements = [ 'vc_row' ];
		if ( ! totaltheme_has_classic_styles() ) {
			$elements[] = 'vc_row_inner';
		}
		foreach ( $elements as $element ) {
			if ( $param = \WPBMap::getParam( $element, 'gap' ) ) {
				$param['heading'] = \esc_html__( 'Outer Column Gap', 'total' );
				$param['description'] = \esc_html__( 'Alters the outer column gap to be used when adding backgrounds to your inner columns. To increase the default space between the columns without backgrounds use the "Inner Column Gap" setting instead.', 'total' );
				$param['weight'] = 40;
				\vc_update_shortcode_param( $element, $param );
			}
		}

		// Move css
		if ( $param = \WPBMap::getParam( 'vc_row', 'css' ) ) {
			$param['weight'] = -1;
			\vc_update_shortcode_param( 'vc_row', $param );
		}
	}

	/**
	 * Tweaks row attributes on edit.
	 */
	public static function edit_form_fields( $atts ) {
		// Featured image bg.
		if ( ! empty( $atts['wpex_post_thumbnail_bg'] ) && 'true' === $atts['wpex_post_thumbnail_bg'] ) {
			$atts['wpex_bg_image_source'] = 'featured';
			unset( $atts['wpex_post_thumbnail_bg'] );
		}

		// Parse ID.
		if ( empty( $atts['el_id'] ) && ! empty( $atts['id'] ) ) {
			$atts['el_id'] = $atts['id'];
			unset( $atts['id'] );
		}

		// Convert match_column_height to equal_height.
		if ( ! empty( $atts['match_column_height'] ) ) {
			$atts['equal_height'] = 'yes';
			unset( $atts['match_column_height'] );
		}

		// Parse $style into $typography_style.
		if ( empty( $atts['typography_style'] ) && ! empty( $atts['style'] ) ) {
			if ( \in_array( $atts['style'], \array_flip( \wpex_typography_styles() ) ) ) {
				$atts['typography_style'] = $atts['style'];
				unset( $atts['style'] );
			}
		}

		// Convert 'no-margins' to '0px' column_spacing.
		if ( empty( $atts['column_spacing'] ) && ! empty( $atts['no_margins'] ) && 'true' === $atts['no_margins'] ) {
			$atts['column_spacing'] = '0px';
			unset( $atts['no_margins'] );
		}

		// Parse css.
		if ( empty( $atts['css'] ) && WPB_Helpers::parse_deprecated_css_check( 'vc_row' ) ) {

			// Convert deprecated fields to css field.
			if ( \class_exists( 'TotalTheme\Integration\WPBakery\Deprecated_CSS_Params_Style' ) ) {
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
	 * Parses row attributes on the frontend.
	 */
	public static function parse_row_atts( $atts ) {
		// Remove full width for the boxed layout.
		if ( ! empty( $atts['full_width'] )
			&& \apply_filters( 'wpex_boxed_layout_vc_stretched_rows_reset', true )
			&& 'boxed' === \wpex_site_layout()
		) {
			$atts['full_width_style'] = $atts['full_width'];
			$atts['full_width_boxed_layout'] = 'true';
			$atts['full_width'] = ''; // unset full-width
		}

		// Migrate old id param to el_id.
		if ( ! empty( $atts['id'] ) && empty( $atts['el_id'] ) ) {
			$atts['el_id'] = $atts['id'];
			unset( $atts['id'] );
		}

		// Migrate old match height option.
		if ( ! empty( $atts['match_column_height'] ) ) {
			$atts['equal_height'] = 'yes';
			unset( $atts['match_column_height'] );
		}

		// Check center_row param.
		if ( empty( $atts['full_width'] )
			&& isset( $atts['center_row'] )
			&& 'yes' === $atts['center_row']
			&& 'full-screen' === \wpex_content_area_layout()
		) {
			$atts['center_row'] = true;
		} else {
			$atts['center_row'] = false; // !!! important !!!
		}
		
		// Check deprecated no_margins param.
		if ( isset($atts['no_margins'] ) && 'true' == $atts['no_margins'] ) {
			$atts['column_spacing'] = '0px';
		}

		// Migrate old style param.
		if ( ! empty( $atts['style'] ) && empty( $atts['typography_style'] ) ) {
			$atts['typography_style'] = $atts['style'];
		}

		// Get custom background image url.
		if ( ! empty( $atts['wpex_post_thumbnail_bg'] ) && 'true' === $atts['wpex_post_thumbnail_bg'] ) {
			$atts['wpex_bg_image_source'] = 'featured';
			unset( $atts['wpex_post_thumbnail_bg'] );
		}
		if ( ! empty( $atts['wpex_bg_image_source'] ) ) {
			// @note we need to use custom code for the featured image to apply the old filter for Rows.
			if ( 'featured' === $atts['wpex_bg_image_source'] ) {
				$background_image = (int) \apply_filters( 'wpex_vc_row_post_thumbnail_bg_id', WPB_Helpers::get_post_thumbnail_id(), $atts );
			} elseif ( \class_exists( 'TotalThemeCore\Vcex\Helpers\Get_Image_From_Source' ) ) {
				$background_image = (new \TotalThemeCore\Vcex\Helpers\Get_Image_From_Source( $atts['wpex_bg_image_source'], $atts ))->get();
			}
			if ( ! empty( $background_image ) ) {
				if ( ! \is_numeric( $background_image ) ) {
					$background_image = \attachment_url_to_postid( $background_image );
				}
				$atts['background_image_id'] = $background_image;
			}
		}

		return $atts;
	}

	/**
	 * Tweak shortcode classes.
	 */
	public static function shortcode_classes( $class_string, $tag, $atts ) {
		if ( ! \in_array( $tag, [ 'vc_row', 'vc_row_inner' ], true ) ) {
			return $class_string;
		}

		$add_classes = [];

		// Relative classname.
		if ( ! \str_contains( $class_string, 'wpex-relative' ) && ! \str_contains( $class_string, 'wpex-sticky' ) ) {
			$add_classes[] = 'wpex-relative';
		}

		// Custom column spacing.
		if ( ! empty( $atts['column_spacing'] )
			&& $column_spacing_safe = \sanitize_html_class( $atts['column_spacing'] )
		) {
			if ( totaltheme_has_classic_styles() ) {
				$add_classes[] = 'wpex-vc-has-custom-column-spacing';
				if ( \in_array( $atts['column_spacing'], self::get_column_spacing_preset_choices(), true ) ) {
					$add_classes[] = "wpex-vc-column-spacing-{$column_spacing_safe}";
				}
			} else {
				if ( \in_array( $atts['column_spacing'], self::get_column_spacing_preset_choices(), true ) ) {
					$column_spacing_safe = absint( $column_spacing_safe ); // we use 0 not 0px for the new class.
					$add_classes[] = "wpex-vc_row-gap-{$column_spacing_safe}";
				}
			}
		}

		if ( ! empty( $atts['has_sidebar'] ) && \in_array( $atts['has_sidebar'], [ 'left', 'right' ] ) ) {
			$sidebar_class_escaped = \sanitize_html_class( "wpex-vc_row-has-sidebar--{$atts['has_sidebar']}" );
			$add_classes['wpex-vc_row-has-sidebar'] = 'wpex-vc_row-has-sidebar';
			$add_classes[ $sidebar_class_escaped ] = $sidebar_class_escaped;
		}

		// Swap vc classes.
		if ( \str_contains( $class_string, 'vc_row-has-fill' ) ) {
			$class_string = \str_replace( 'vc_row-has-fill', '', $class_string );
			$add_classes['wpex-vc_row-has-fill'] = 'wpex-vc_row-has-fill';
		} elseif ( ! empty( $atts['vcex_parallax'] )
			|| ! empty( $atts['wpex_self_hosted_video_bg'] )
			|| ! empty( $atts['background_image_id'] )
			|| ! empty( $atts['wpex_bg_color'] )
			|| ! empty( $atts['wpex_border_color'] )
			|| ( ! empty( $atts['el_class'] ) && is_string( $atts['el_class'] )
				&& ( \str_contains( $atts['el_class'], 'wpex-surface-' ) || \str_contains( $atts['el_class'], 'wpex-bg-' ) )
			)
		) {
			$add_classes['wpex-vc_row-has-fill'] = 'wpex-vc_row-has-fill';
		}

		if ( ! empty( $atts['visibility'] ) ) {
			$add_classes[] = \totaltheme_get_visibility_class( $atts['visibility'] );
		}

		if ( ! empty( $atts['full_width'] ) ) {
			$add_classes[] = 'wpex-vc-row-stretched';
		}

		if ( ! empty( $atts['full_width_boxed_layout'] ) ) {
			$add_classes[] = 'wpex-vc-row-boxed-layout-stretched';
			if ( isset( $atts['full_width_style'] ) && 'stretch_row_content_no_spaces' == $atts['full_width_style'] ) {
				$add_classes[] = 'vc_row-no-padding';
			}
		}

		$supports_max_width = self::row_supports_max_width( $atts );

		if ( $supports_max_width && ! empty( $atts['max_width'] ) ) {
			$has_margin_x = true;
			$add_classes[] = 'vc-has-max-width vc-max-width-' . \sanitize_html_class( absint( $atts['max_width'] ) );
		}

		if ( $supports_max_width && ! empty( $atts['max_width_custom'] ) ) {
			$has_margin_x = true;
			$align = ! empty( $atts['max_width_align'] ) ? $atts['max_width_align'] : 'center';
			if ( 'none' !== $align ) {
				switch ( $align ) {
					case 'left':
						$add_classes[] = 'wpex-vc_row-mr-auto';
						break;
					case 'right':
						$add_classes[] = 'wpex-vc_row-ml-auto';
						break;
					case 'center':
						$add_classes[] = 'wpex-vc_row-mx-auto';
						break;
				}
			}
		}

		if ( \wpex_validate_boolean( $atts['center_row'] ?? false ) ) {
			$add_classes[] = 'wpex-vc-row-centered';
		}

		if ( \wpex_validate_boolean( $atts['remove_bottom_col_margin'] ?? false ) ) {
			$add_classes[] = \totaltheme_has_classic_styles() ? 'no-bottom-margins' : 'wpex-vc_row-col-mb-0';
		}

		if ( \wpex_validate_boolean( $atts['tablet_fullwidth_cols'] ?? false ) ) {
			$add_classes[] = 'tablet-fullwidth-columns';
		}

		if ( isset( $atts['columns_right'] ) && 'yes' === $atts['columns_right'] ) {
			$add_classes[] = 'wpex-cols-right';
		}

		if ( ! empty( $atts['wpex_fixed_bg'] ) ) {
			$add_classes[] = WPB_Helpers::get_fixed_background_class( $atts['wpex_fixed_bg'] );
		}

		if ( isset( $atts['offset_overlay_header'] ) && \wpex_validate_boolean( $atts['offset_overlay_header'] ) ) {
			$add_classes[] = 'add-overlay-header-offset';
		}

		if ( empty( $atts['full_width'] ) && isset( $add_classes['wpex-vc_row-has-fill'] ) && empty( $has_margin_x ) ) {
			$add_classes[] = totaltheme_has_classic_styles() ? 'wpex-vc-reset-negative-margin' : 'wpex-vc_row-mx-0';
		}

		if ( ! empty( $atts['typography_style'] ) ) {
			$add_classes[] = wpex_typography_style_class( $atts['typography_style'] );
		}

		if ( $add_classes && $add_classes = array_filter( $add_classes ) ) {
			$class_string .= ' ' . implode( ' ', $add_classes );
		}

		return $class_string;
	}

	/**
	 * Add custom attributes to the row wrapper.
	 */
	public static function wrap_attributes( $wrap_attributes, $atts ) {
		$inline_style = '';

		// Local scroll ID
		if ( ! empty( $atts['local_scroll_id'] ) ) {
			$wrap_attributes[] = 'data-ls_id="#' . \esc_attr( $atts['local_scroll_id'] ) . '"';
			$wrap_attributes[] = 'tabindex="-1"';
		}

		// Z-Index - @todo rename to just z_index
		if ( ! empty( $atts['wpex_zindex'] ) && $z_index_safe = \sanitize_text_field( $atts['wpex_zindex'] ) ) {
			$inline_style .= "z-index:{$z_index_safe}!important;";
		}

		// Custom background image
		if ( ! empty( $atts['background_image_id'] )
			&& $background_image_url = \wp_get_attachment_image_url( $atts['background_image_id'], 'full' )
		) {
			$inline_style .= 'background-image:url(' . \esc_url( $background_image_url ) . ')';
			if ( ! isset( $atts['wpex_bg_image_source'] )
				|| 'featured' !== $atts['wpex_bg_image_source']
				|| \apply_filters( 'wpex_vc_row_post_thumbnail_bg_has_important', true )
			) {
				$inline_style .= '!important';
			}
			$inline_style .= ';';
		}

		// Min Height
		if ( ! empty( $atts['min_height'] ) && $min_height_safe = \sanitize_text_field( $atts['min_height'] ) ) {
			if ( \is_numeric( $min_height_safe ) ) {
				$min_height_safe = \intval( $min_height_safe ) . 'px';
			}
			$inline_style .= "min-height:{$min_height_safe};";
		}

		// Max Width
		if ( self::row_supports_max_width( $atts ) && ! empty( $atts['max_width_custom'] ) ) {
			$inline_style .= 'max-width:' . \intval( $atts['max_width_custom'] ) . 'px;';
		}

		// Background color
		if ( ! empty( $atts['wpex_bg_color'] ) && $bg_color_parsed = wpex_parse_color( $atts['wpex_bg_color'] ) ) {
			$inline_style .= 'background-color:' . \esc_attr( $bg_color_parsed ) . '!important;';
		}

		// Border color
		if ( ! empty( $atts['wpex_border_color'] ) && $border_color_parsed = wpex_parse_color( $atts['wpex_border_color'] ) ) {
			$inline_style .= 'border-color:' . \esc_attr( $border_color_parsed ) . '!important;';
		}

		// Settings that should only get added if parallax is disabled
		if ( empty( $atts['parallax'] ) ) {

			// Background position
			if ( ! empty( $atts['wpex_bg_position'] ) && $bg_position_safe = \sanitize_text_field( $atts['wpex_bg_position'] ) ) {
				$inline_style .= "background-position:{$bg_position_safe}!important;";
			}

			// Background size
			if ( ! empty( $atts['wpex_bg_size'] ) && $bg_size_safe = \sanitize_text_field( $atts['wpex_bg_size'] ) ) {
				$inline_style .= "background-size:{$bg_size_safe}!important;";
			}

		}

		// Full Width margin
		if ( ! empty( $atts['full_width_margin'] ) && $fw_margin_safe = \sanitize_text_field( $atts['full_width_margin'] ) ) {
			$inline_style .= "--wpex-vc-full-width-offset:{$fw_margin_safe};";
		}

		// Inline css styles
		// Fallback For OLD Total Params
		if ( empty( $atts['css'] )
			&& WPB_Helpers::parse_deprecated_css_check( 'vc_row' )
			&& \class_exists( 'TotalTheme\Integration\WPBakery\Deprecated_CSS_Params_Style' )
		) {
			$inline_style .= Deprecated_CSS_Params_Style::generate_css( $atts, 'inline_css' );
		}

		// Add inline style to wrapper attributes
		if ( $inline_style ) {
			$wrap_attributes[] = 'style="' . \esc_attr( $inline_style ) . '"';
		}

		return $wrap_attributes;
	}

	/**
	 * Open center row.
	 *
	 * Priority: 1
	 */
	public static function maybe_add_header_overlay_offset( $content, $atts ) {
		if ( isset( $atts['offset_overlay_header'] )
			&& \wpex_validate_boolean( $atts['offset_overlay_header'] )
			&& totaltheme_call_static( 'Header\Overlay', 'is_enabled' )
		) {
			$style = totaltheme_call_static( 'Header\Core', 'has_fixed_height' ) ? ' style="height:var(--wpex-site-header-height, 100px);"' : '';
			$content .= '<div class="overlay-header-offset-div wpex-w-100" ' . $style . '></div>';
		}
		return $content;
	}

	/**
	 * Open center row.
	 */
	public static function center_row_open( $content, $atts ) {
		if ( empty( $atts['center_row'] ) || ! \wpex_validate_boolean( $atts['center_row'] ) ) {
			return $content;
		}

		$is_flex = false;

		if ( ! empty( $atts['equal_height'] ) || ! empty( $atts['full_height'] ) || ! empty( $atts['content_placement'] ) ) {
			$is_flex = true;
		}

		$wrap_class = 'center-row container';
		$inner_class = 'center-row-inner';

		if ( $is_flex ) {
			$inner_class .= ' wpex-flex wpex-flex-wrap';
			if ( isset( $atts['columns_right'] ) && 'yes' === $atts['columns_right'] ) {
				$inner_class .= ' wpex-flex-row-reverse';
			}
			if ( ! empty( $atts['content_placement'] ) ) {
				switch ( $atts['content_placement'] ) {
					case 'top':
						$inner_class .= ' wpex-items-start';
						break;
					case 'middle':
						$inner_class .= ' wpex-items-center';
						break;
					case 'bottom':
						$inner_class .= ' wpex-items-end';
						break;
				}
			}
		} else {
			$inner_class .= ' wpex-clr';
		}

		$content .= '<div class="' . esc_attr( $wrap_class ) . '">';
			$content .= '<div class="' . esc_attr( $inner_class ) . '">';
		return $content;
	}

	/**
	 * Close center row.
	 */
	public static function center_row_close( $content, $atts ) {
		if ( ! empty( $atts['center_row'] ) && \wpex_validate_boolean( $atts['center_row'] ) ) {
			$content .= '</div></div>';
		}
		return $content;
	}

	/**
	 * Custom HTML output.
	 */
	public static function custom_output( $output, $obj, $atts, $shortcode ) {
		if ( 'vc_row' === $shortcode || 'vc_row_inner' === $shortcode ) {
			if ( ! WPB_Helpers::shortcode_has_access( $atts ) ) {
				return;
			}
			$output = \totaltheme_replace_vars( $output );
		}
		return $output;
	}

	/**
	 * Checks if the current row supports max_width.
	 */
	protected static function row_supports_max_width( $atts = [] ): bool {
		return ( empty( $atts['full_width'] ) && empty( $atts['wpex_full_width'] ) );
	}

	/**
	 * Returns array of preset column spacing choices.
	 */
	protected static function get_column_spacing_preset_choices(): array {
		return [
			\esc_html__( 'Default', 'total' ) => '',
			'0px' => '0px',
			'1px' => '1',
			'5px' => '5',
			'10px' => '10',
			'20px' => '20',
			'30px' => '30',
			'40px' => '40',
			'50px' => '50',
			'60px' => '60',
		];
	}

}
