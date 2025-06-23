<?php

defined( 'ABSPATH' ) || exit;

/**
 * Button Shortcode.
 */
if ( ! class_exists( 'VCEX_Button_Shortcode' ) ) {

	class VCEX_Button_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_button';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title(): string {
			return esc_html__( 'Theme Button', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Customizable button', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				// General
				array(
					'type' => 'vcex_select_buttons',
					'std' => 'custom_text',
					'heading' => esc_html__( 'Text Source', 'total-theme-core' ),
					'param_name' => 'text_source',
					'choices' => array(
						'custom_text' => esc_html__( 'Custom Text', 'total-theme-core' ),
						'custom_field' => esc_html__( 'Custom Field', 'total-theme-core' ),
						'callback_function' => esc_html__( 'Callback Function', 'total-theme-core' ),
					),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Text', 'total-theme-core' ),
					'param_name' => 'content',
					'admin_label' => true,
					'std' => 'Button Text',
					'description' => self::param_description( 'text' ) . '<br>' . esc_html__( 'You can use {{post_title}} to display the current post title when creating custom cards.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'text_source', 'value' => 'custom_text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'text',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'text_custom_field',
					'dependency' => array( 'element' => 'text_source', 'value' => 'custom_field' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_callback_function',
					'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
					'param_name' => 'text_callback_function',
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'dependency' => array( 'element' => 'text_source', 'value' => 'callback_function' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Aria Label', 'total-theme-core' ),
					'param_name' => 'aria_label',
					'description' => sprintf( esc_html__( 'Provides descriptive text for screen readers. %sLearn more about the aria-label tag%s. Shortcodes are allowed.', 'total-theme-core' ), '<a href="https://www.w3.org/WAI/WCAG21/Techniques/aria/ARIA8" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'param_name' => 'hover_animation',
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				// Link
				array(
					'type' => 'vcex_select',
					'std' => 'custom_link',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'onclick',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Link', 'total-theme-core' ),
					'param_name' => 'onclick_url',
				//	'std' => '#', // this breaks the migration from url to onclick_url.
					'dependency' => array(
						'element' => 'onclick',
						'value' => array(
							'custom_link',
							'local_scroll',
							'popup',
							'lightbox_image',
							'lightbox_video',
							'toggle_element',
						),
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => self::param_description( 'link' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Active Toggle Text', 'total-theme-core' ),
					'param_name' => 'toggle_element_active_text',
					'description' => esc_html__( 'Custom text to display when the toggle element is currently active. Leave empty to display the default button text.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'toggle_element' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vc_link',
					'heading' => esc_html__( 'Internal Link', 'total-theme-core' ),
					'param_name' => 'onclick_internal_link',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => esc_html__( 'This setting is used only if you want to link to an internal page to make it easier to find and select it. Any extra settings in the popup (title, target, nofollow) are ignored.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'internal_link' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'link',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'onclick_custom_field',
					'dependency' => array( 'element' => 'onclick', 'value' => 'custom_field' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_callback_function',
					'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
					'param_name' => 'onclick_callback_function',
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'callback_function' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'attach_image',
					'heading' => esc_html__( 'Lightbox Image', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_image',
					'dependency' => array( 'element' => 'onclick', 'value' => 'lightbox_image' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'attach_images',
					'heading' => esc_html__( 'Lightbox Gallery', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_gallery',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'lightbox_gallery' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Title Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_title',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value_not_equal_to' => 'toggle_element' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'onclick_target',
					'choices' => array(
						'' => esc_html__( 'Self', 'total-theme-core' ),
						'blank' => esc_html__( 'Blank', 'total-theme-core' ),
					),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array(
							'custom_link',
							'internal_link',
							'custom_field',
							'callback_function',
							'post_permalink',
							'just_event_link',
							'home',
						),
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Rel', 'total-theme-core' ),
					'param_name' => 'onclick_rel',
					'std' => '',
					'choices' => array(
						'' => esc_html__( 'None', 'total-theme-core' ),
						'nofollow' => esc_html__( 'Nofollow', 'total-theme-core' ),
						'sponsored' => esc_html__( 'Sponsored', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'custom_link', 'internal_link', 'custom_field', 'callback_function' ),
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Use Download Attribute?', 'total-theme-core' ),
					'param_name' => 'download_attribute',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => array( 'custom_link', 'custom_field', 'callback_function' ) ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Dimensions (optional)', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_dims',
					'description' => esc_html__( 'Enter a custom width and height for your lightbox pop-up window. Use format widthxheight. Example: 1920x1080.', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => array( 'lightbox_video', 'popup' ) ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Title', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_title',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'lightbox_image', 'lightbox_video', 'popup' )
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textarea',
					'heading' => esc_html__( 'Lightbox Caption', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_caption',
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'lightbox_image', 'lightbox_video', 'popup' )
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Custom Data Attributes', 'total-theme-core' ),
					'param_name' => 'onclick_data_attributes',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => esc_html__( 'Enter your custom data attributes in the format of data|value. Hit enter after each set of data attributes.', 'total-theme-core' ),
				),
				// Style
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'State', 'total-theme-core' ),
					'param_name' => 'state',
					'choices' => array(
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'active' => esc_html__( 'Active', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Size', 'total-theme-core' ),
					'param_name' => 'size',
					'std' => '',
					'choices' => 'button_size',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => array( 'element' => 'font_size', 'is_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Layout', 'total-theme-core' ),
					'param_name' => 'layout',
					'choices' => [
						'inline'   => \esc_html__( 'Inline', 'total-theme-core' ),
						'block'    => \esc_html__( 'Block', 'total-theme-core' ),
						'expanded' => \esc_html__( 'Expanded', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'std' => 'inline',
					'description' => esc_html__( 'Note: If you add any custom settings in the container design tab the button can no longer render inline since the added elements are added as a wrapper.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Float', 'total-theme-core' ),
					'param_name' => 'align',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => esc_html__( 'Note: Any alignment besides "Default" will add a wrapper around the button to position it so it will no longer be inline.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_notice',
					'param_name' => 'styling_notice',
					'text' => esc_html__( 'You can set custom styles for your this specific button below but you can also customize the design of all your buttons via the Customizer.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Preset Color', 'total-theme-core' ),
					'param_name' => 'color',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => array( 'element' => 'hover_animation', 'is_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'custom_background',
					'css' => [ 'property' => 'background' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'custom_hover_background',
					'css' => [ 
						'selector' => '{{WRAPPER}}:hover',
						'property' => 'background',
						'important' => true, // overrides preset colors.
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'custom_color',
					'css' => [
						'property' => 'color',
						'important' => true, // overrides preset colors.
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'custom_hover_color',
					'css' => [ 
						'selector' => '{{WRAPPER}}:hover',
						'property' => 'color',
						'important' => true, // overrides preset colors.
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => true,
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'font_padding', // @todo rename
					'css' => [ 'property' => 'padding' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'margin',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'param_name' => 'border',
					'css' => true,
					'description' => self::param_description( 'border' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				// Typography
				array(
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'text_transform',
					'css' => true, // it's always been added inline so best to keep.
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-button',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				),
				//Icons
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Icon Color', 'total-theme-core' ),
					'param_name' => 'icon_color',
					'css' => [ 'selector' => '.vcex-button-icon', 'property' => 'color' ],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Icon library', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'description' => esc_html__( 'For optimal site speed, it\'s strongly recommended to use the theme\'s built-in icon library or upload a custom icon.', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Theme Icons', 'total-theme-core' ) => 'ticons',
						esc_html__( 'Font Awesome', 'total-theme-core' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'total-theme-core' ) => 'openiconic',
						esc_html__( 'Typicons', 'total-theme-core' ) => 'typicons',
						esc_html__( 'Entypo', 'total-theme-core' ) => 'entypo',
						esc_html__( 'Linecons', 'total-theme-core' ) => 'linecons',
						esc_html__( 'Material', 'total-theme-core' ) => 'material',
						esc_html__( 'Pixel', 'total-theme-core' ) => 'pixelicons',
					),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Left Icon', 'total-theme-core' ),
					'param_name' => 'icon_left',
					'dependency' => array( 'element' => 'icon_type', 'value' => 'ticons' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Left Icon', 'total-theme-core' ),
					'param_name' => 'icon_left_fontawesome',
					'settings' => array( 'emptyIcon' => true, 'type' => 'fontawesome', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'fontawesome' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Left Icon', 'total-theme-core' ),
					'param_name' => 'icon_left_openiconic',
					'settings' => array( 'emptyIcon' => true, 'type' => 'openiconic', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'openiconic' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Left Icon', 'total-theme-core' ),
					'param_name' => 'icon_left_typicons',
					'settings' => array( 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'typicons' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Left Icon', 'total-theme-core' ),
					'param_name' => 'icon_left_entypo',
					'settings' => array( 'emptyIcon' => true, 'type' => 'entypo', 'iconsPerPage' => 300 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'entypo' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Left Icon', 'total-theme-core' ),
					'param_name' => 'icon_left_linecons',
					'settings' => array( 'emptyIcon' => true, 'type' => 'linecons', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'linecons' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Left Icon', 'total-theme-core' ),
					'param_name' => 'icon_left_material',
					'settings' => array( 'emptyIcon' => true, 'type' => 'material', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'material' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Left Icon', 'total-theme-core' ),
					'param_name' => 'icon_left_pixelicons',
					'settings' => array( 'emptyIcon' => true, 'type' => 'pixelicons', 'source' => vcex_pixel_icons() ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'pixelicons' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Left Icon Alternative Character', 'total-theme-core' ),
					'param_name' => 'icon_left_alt',
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Left Icon: Right Padding', 'total-theme-core' ),
					'param_name' => 'icon_left_padding',
					'choices' => array(
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'0px' => '0px',
						'5px' => '5px',
						'10px' => '10px',
						'15px' => '15px',
					),
					'css' => [
						'selector' => '.theme-button-icon-left',
						'property' => 'padding-inline-end',
					],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Left Icon: Hover Transform x', 'total-theme-core' ),
					'param_name' => 'icon_left_transform',
					'css' => [ 'selector' => '.theme-button-icon-left', 'property' => '--wpex-btn-icon-animate-h' ],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
					'description' => esc_html__( 'Enter a value to move the icon horizontally on hover. You can enter a px or em value. Use negative values to go left and positive values to go right. Example: 10px would move the icon 10 pixels to the right while -10px would move the icon 10 pixels to the left.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Left Icon: Size', 'total-theme-core' ),
					'param_name' => 'icon_left_size',
					'css' => [ 'selector' => '.theme-button-icon-left', 'property' => 'font-size' ],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Right Icon', 'total-theme-core' ),
					'param_name' => 'icon_right',
					'dependency' => array( 'element' => 'icon_type', 'value' => 'ticons' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Right Icon', 'total-theme-core' ),
					'param_name' => 'icon_right_fontawesome',
					'settings' => array( 'emptyIcon' => true, 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'fontawesome' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Right Icon', 'total-theme-core' ),
					'param_name' => 'icon_right_openiconic',
					'settings' => array( 'emptyIcon' => true, 'type' => 'openiconic', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'openiconic' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Right Icon', 'total-theme-core' ),
					'param_name' => 'icon_right_typicons',
					'settings' => array( 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'typicons' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Right Icon', 'total-theme-core' ),
					'param_name' => 'icon_right_entypo',
					'settings' => array( 'emptyIcon' => true, 'type' => 'entypo', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'entypo' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Right Icon', 'total-theme-core' ),
					'param_name' => 'icon_right_linecons',
					'settings' => array( 'emptyIcon' => true, 'type' => 'linecons', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'linecons' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Right Icon', 'total-theme-core' ),
					'param_name' => 'icon_right_material',
					'settings' => array( 'emptyIcon' => true, 'type' => 'material', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'material' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Right Icon', 'total-theme-core' ),
					'param_name' => 'icon_right_pixelicons',
					'settings' => array( 'emptyIcon' => true, 'type' => 'pixelicons', 'source' => vcex_pixel_icons() ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'pixelicons' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Right Icon Alternative Character', 'total-theme-core' ),
					'param_name' => 'icon_right_alt',
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Right Icon: Left Padding', 'total-theme-core' ),
					'param_name' => 'icon_right_padding',
					'choices' => array(
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'0px' => '0px',
						'5px' => '5px',
						'10px' => '10px',
						'15px' => '15px',
					),
					'css' => [
						'selector' => '.theme-button-icon-right',
						'property' => 'padding-inline-start',
					],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Right Icon: Hover Transform x', 'total-theme-core' ),
					'param_name' => 'icon_right_transform',
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
					'css' => [
						'selector' => '.theme-button-icon-right',
						'property' => '--wpex-btn-icon-animate-h',
					],
					'description' => esc_html__( 'Enter a value to move the icon horizontally on hover. You can enter a px or em value. Use negative values to go left and positive values to go right. Example: 10px would move the icon 10 pixels to the right while -10px would move the icon 10 pixels to the left.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Right Icon: Size', 'total-theme-core' ),
					'param_name' => 'icon_right_size',
					'css' => [ 'selector' => '.theme-button-icon-right', 'property' => 'font-size' ],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Design options
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css_wrap',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
				// Gutenberg.
				[ 'param_name' => 'text_align', 'editors' => [ 'gutenberg' ] ],
				// Deprecated
				[ 'type' => 'hidden', 'param_name' => 'classes' ],
				[ 'type' => 'hidden', 'param_name' => 'internal_link' ],
				[ 'type' => 'hidden', 'param_name' => 'url_custom_field' ],
				[ 'type' => 'hidden', 'param_name' => 'url' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_image' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_poster_image' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_type' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_gallery' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_post_gallery' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_title' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_dimensions' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_video_html5_webm' ],
				[ 'type' => 'hidden', 'param_name' => 'image_attachment' ],
				[ 'type' => 'hidden', 'param_name' => 'data_attributes' ],
				[ 'type' => 'hidden', 'param_name' => 'rel' ],
				[ 'type' => 'hidden', 'param_name' => 'target' ],
				[ 'type' => 'hidden', 'param_name' => 'title' ],
				[ 'type' => 'hidden', 'param_name' => 'url_callback_function' ],
			);
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			return self::parse_deprecated_attributes( $atts );
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( ! empty( $atts['class'] ) && empty( $atts['classes'] ) ) {
				$atts['el_class'] = $atts['class'];
			}

			if ( isset( $atts['lightbox'] ) && 'true' == $atts['lightbox'] ) {
				$atts['onclick'] = 'lightbox';
				unset( $atts['lightbox'] );
			}

			if ( ! empty( $atts['lightbox_image'] ) ) {
				$atts['onclick_lightbox_image'] = $atts['lightbox_image'];
				unset( $atts['lightbox_image'] );
			}

			if ( ( empty( $atts['onclick'] ) || 'custom_link' === $atts['onclick'] )
				&& isset( $atts['target'] )
				&& 'local' === $atts['target']
			) {
				$atts['onclick'] = 'local_scroll';
				unset( $atts['target'] );
			}

			/**
			 * Migrate old lightbox_type att.
			 *
			 * @note "image" was also used for a lightbox onclick action.
			 */
			if ( isset( $atts['onclick'] ) && in_array( $atts['onclick'], [ 'lightbox', 'image' ] ) ) {

				if ( ! empty( $atts['lightbox_type'] ) ) {
					switch ( $atts['lightbox_type'] ) {
						case 'iframe':
						case 'url':
						case 'inline':
							$atts['onclick'] = 'popup';
							break;
						case 'html5':
							$atts['onclick'] = 'lightbox_video';
							if ( ! empty( $atts['lightbox_video_html5_webm'] ) ) {
								$atts['url'] = $atts['lightbox_video_html5_webm'];
							}
							break;
						case 'video':
						case 'video_embed':
							$atts['onclick'] = 'lightbox_video';
							break;
						case 'image':
						default:
							$atts['onclick'] = 'lightbox_image';
							break;
					}
				} else {
					$atts['onclick'] = 'lightbox_image';
				}
				if ( isset( $atts['lightbox_post_gallery'] ) && 'true' === $atts['lightbox_post_gallery'] ) {
					$atts['onclick'] = 'lightbox_post_gallery';
					unset( $atts['lightbox_post_gallery'] );
				} elseif ( ! empty( $atts['lightbox_gallery'] ) ) {
					$atts['onclick'] = 'lightbox_gallery';
				} elseif ( ! empty( $atts['image_attachment'] ) ) {
					$atts['onclick'] = 'lightbox_image';
				}
			}

			if ( isset( $atts['lightbox_title'] ) ) {
				unset( $atts['lightbox_title'] );
			}

			// @note this needs to be added last.
			$migrate_atts = [
				'classes'               => 'el_class',
				'url'                   => 'onclick_url',
				'internal_link'         => 'onclick_internal_link',
				'url_custom_field'      => 'onclick_custom_field',
				'lightbox_gallery'      => 'onclick_lightbox_gallery',
				'image_attachment'      => 'onclick_lightbox_image',
				'lightbox_dimensions'   => 'onclick_lightbox_dims',
				'data_attributes'       => 'onclick_data_attributes',
				'rel'                   => 'onclick_rel',
				'target'                => 'onclick_target',
				'title'                 => 'onclick_title',
				'url_callback_function' => 'onclick_callback_function',
			];

			foreach ( $migrate_atts as $old_att => $new_att ) {
				if ( isset( $atts[ $old_att ] ) && empty( $atts[ $new_att ] ) ) {
					$atts[ $new_att ] = $atts[ $old_att ];
					unset( $atts[ $old_att ] );
				}
			}

			return $atts;
		}

	}

}

new VCEX_Button_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Button' ) ) {
	class WPBakeryShortCode_Vcex_Button extends WPBakeryShortCode {}
}
