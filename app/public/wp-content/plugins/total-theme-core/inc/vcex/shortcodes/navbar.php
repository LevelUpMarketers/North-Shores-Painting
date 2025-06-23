<?php

defined( 'ABSPATH' ) || exit;

/**
 * Navbar Shortcode.
 */
if ( ! class_exists( 'Vcex_Navbar_Shortcode' ) ) {

	class Vcex_Navbar_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_navbar';

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
			return esc_html__( 'Navigation Bar', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Custom menu navigation bar', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// General
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Aria Label', 'total-theme-core' ),
					'param_name' => 'aria_label',
					'description' => esc_html__( 'Optional menu description for screen readers.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Show Select Field on Mobile?', 'total-theme-core' ),
					'param_name' => 'mobile_select',
					'description' => esc_html__( 'When enabled the menu buttons will be converted into a singular select dropdown on mobile devices.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Mobile Select Empty Option Text', 'total-theme-core' ),
					'param_name' => 'mobile_select_browse_txt',
					'description' => esc_html__( 'This option is used as the first option in the mobile select field. For example if you have a standard link based menu you may use "Browse" as the empty option text. If you have setup a filter style menu you may want to keep this empty and add an "All" link at the start of your menu which is done by adding a link with a # symbol as the url value.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'mobile_select', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				// Menu
				[
					'type' => 'vcex_select',
					'choices_callback' => 'Vcex_Navbar_Shortcode::get_menu_choices',
					'admin_label' => true,
					'heading' => esc_html__( 'Menu', 'total-theme-core' ),
					'param_name' => 'menu',
					'save_always' => true,
					'elementor' => [ 'type' => 'select_group' ],
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Parent Page ID', 'total-theme-core' ),
					'param_name' => 'parent_id',
					'description' => esc_html__( 'Leave empty to display child pages of the current page.', 'total-theme-core' ),
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'dependency' => [ 'element' => 'menu', 'value' => 'post_children' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'std' => 'ASC',
					'choices' => [
						'ASC' => esc_html__( 'ASC', 'total-theme-core' ),
						'DESC' => esc_html__( 'DESC', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'dependency' => [ 'element' => 'menu', 'value' => 'post_children' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'std' => 'menu_order',
					'choices' => [
						'menu_order' => esc_html__( 'Menu Order', 'total-theme-core' ),
						'date' => esc_html__( 'Date', 'total-theme-core' ),
						'title' => esc_html__( 'Title', 'total-theme-core' ),
						'name' => esc_html__( 'Name (post slug)', 'total-theme-core' ),
						'modified' => esc_html__( 'Modified', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'dependency' => [ 'element' => 'menu', 'value' => 'post_children' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Taxonomy', 'total-theme-core' ),
					'param_name' => 'taxonomy',
					'choices' => 'taxonomy',
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'dependency' => [ 'element' => 'menu', 'value' => [ 'dynamic_terms', 'post_terms' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Parent Terms Only', 'total-theme-core' ),
					'param_name' => 'parent_terms_only',
					'choices' => 'taxonomy',
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'dependency' => [ 'element' => 'menu', 'value' => [ 'dynamic_terms', 'post_terms' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Child Of', 'total-theme-core' ),
					'param_name' => 'child_of',
					'settings' => [
						'multiple' => false,
						'min_length' => 1,
						'groups' => true,
						'display_inline' => true,
					//	'delay' => 0,
						'auto_focus' => true,
					],
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'dependency' => [ 'element' => 'menu', 'value' => [ 'dynamic_terms', 'post_terms' ] ],
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a parent term ID.', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Exclude Terms', 'total-theme-core' ),
					'param_name' => 'terms_not_in',
					'settings' => [
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						'display_inline' => true,
					//	'delay' => 0,
					],
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a comma separated list of term ID\'s.', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'dependency' => [ 'element' => 'menu', 'value' => [ 'dynamic_terms', 'post_terms' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Post Filter Grid ID', 'total-theme-core' ),
					'param_name' => 'filter_menu',
					'description' => esc_html__( 'Enter the "Element ID" of the post grid module you wish to filter. This will only work on the theme specific grids. Make sure the filter on the grid module is disabled to prevent conflicts. View theme docs for more info.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'menu', 'value_not_equal_to' => 'post_children' ],
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Local Scroll Menu', 'total-theme-core'),
					'param_name' => 'local_scroll',
					'group' => esc_html__( 'Menu', 'total-theme-core' ),
					'dependency' => [ 'element' => 'menu', 'value_not_equal_to' => [ 'post_children', 'dynamic_terms', 'current_tax_terms', 'post_terms' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Filter
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Filter Mode', 'total-theme-core' ),
					'param_name' => 'filter_layout_mode',
					'std' => 'masonry',
					'choices' => [
						'masonry' => esc_html__( 'Isotope: Masonry (sorts visible items)', 'total-theme-core' ),
						'fitRows' => esc_html__( 'Isotope: Fit Rows (sorts visible items)', 'total-theme-core' ),
						'hide' => esc_html__( 'Simple Show/Hide (sorts visible items)', 'total-theme-core' ),
						'ajax' => esc_html__( 'Ajaxed (loads selected items - Post Cards only)', 'total-theme-core' ),
					],
					'description' => esc_html__( 'Note: The Isotope layouts will only work with the Post Cards "Grid" display type and and the "Default" or "Masonry" grid style.', 'total-theme-core' ),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter_menu', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Filter Query Type', 'total-theme-core' ),
					'param_name' => 'filter_relation',
					'choices' => [
						'' => esc_html__( 'Single Selection', 'total-theme-core' ),
						'AND' => esc_html__( 'Multiple AND Selections', 'total-theme-core' ),
						'OR' => esc_html__( 'Multiple OR Selections', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter_layout_mode', 'value' => 'ajax' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Active Term', 'total-theme-core' ),
					'param_name' => 'filter_active_item',
					'settings' => [
						'multiple' => false,
						'min_length' => 1,
						'groups' => true,
						'display_inline' => true,
					//	'delay' => 0,
						'auto_focus' => true,
					],
					'description' => esc_html__( 'Select the term you wish to have active by default. If using the AJAX filter this value should match the default term selected in the targeted Post Cards element via the "Include Terms" field.', 'total-theme-core' ),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter_menu', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Show Term Count', 'total-theme-core' ),
					'param_name' => 'term_count',
					'description' => esc_html__( 'Enable to display the number of posts assigned to a given taxonomy term item. Note: Since this element exists independently from the target grid, this functionality must use AJAX to calculate how many posts are inside each term so it may take a second for the terms to show up on page load and it will only display on the live site.', 'total-theme-core' ),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter_layout_mode', 'value' => 'ajax' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom All Text', 'total-theme-core' ),
					'param_name' => 'all_text',
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'description' => esc_html__( 'The "All" button is added when displaying a taxonomy based menu.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter_menu', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Filter Speed', 'total-theme-core' ),
					'param_name' => 'filter_transition_duration',
					'description' => esc_html__( 'Default is "0.4" seconds. Enter "0.0" to disable.', 'total-theme-core' ),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter_layout_mode', 'value' => [ 'masonry', 'fitRows' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Style
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Preset', 'total-theme-core' ),
					'param_name' => 'preset_design',
					'std' => 'none',
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => 'none',
						esc_html__( 'Dark', 'total-theme-core' ) => 'dark',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Alignment', 'total-theme-core' ),
					'param_name' => 'align',
					'dependency' => [
						'element' => 'button_layout',
						'value_not_equal_to' => [ 'spaced_out', 'expanded' ]
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Layout', 'total-theme-core' ),
					'param_name' => 'button_layout',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'spaced_out' => esc_html__( 'Spaced Out', 'total-theme-core' ),
						'expanded' => esc_html__( 'Expanded', 'total-theme-core' ),
						'list' => esc_html__( 'List', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Expand Links', 'total-theme-core' ),
					'param_name' => 'expand_links',
					'std' => 'false',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button_layout', 'value' => 'spaced_out' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Full-Screen Center', 'total-theme-core'),
					'param_name' => 'full_screen_center',
					'description' => esc_html__( 'Center the navigation when used inside a stretched row or full-screen page layout.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Menu Background', 'total-theme-core' ),
					'param_name' => 'wrap_background',
					'css' => [ 'selector' => '{{WRAPPER}}', 'property' => 'background' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_subheading',
					'param_name' => 'vcex_subheading__links',
					'text' => esc_html__( 'Link Styles', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Button Style', 'total-theme-core' ),
					'description' => esc_html__( 'Select the "Plain Text" option to display simple links and then you can customize them to look exactly how you want.', 'total-theme-core' ),
					'param_name' => 'button_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'std' => 'minimal-border',
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'transition_duration',
					'heading' => esc_html__( 'Link Animation Duration', 'total-theme-core' ),
					'param_name' => 'link_transition_duration',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => esc_html__( 'Controls the animation speed of your links which includes changes in backgrounds, colors, opacity, link decoration, etc.', 'total-theme-core' ),
					'css' => [ 'selector' => 'a.vcex-navbar-link', 'property' => 'transition-duration' ],
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Button Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Link Side Margin', 'total-theme-core' ),
					'param_name' => 'link_margin_side',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Link Bottom Margin', 'total-theme-core' ),
					'param_name' => 'link_margin_bottom',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Link Padding', 'total-theme-core' ),
					'param_name' => 'link_padding',
					'css' => [ 'selector' => 'a.vcex-navbar-link', 'property' => 'padding' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
				//	'edit_field_class' => 'vc_col-sm-4',
					'css' => [ 'selector' => 'a.vcex-navbar-link' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'hover_color',
				//	'edit_field_class' => 'vc_col-sm-4',
					'css' => [ 'selector' => 'a.vcex-navbar-link:is(:hover,.active)', 'property' => 'color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Active', 'total-theme-core' ),
					'param_name' => 'active_color',
				//	'edit_field_class' => 'vc_col-sm-4',
					'css' => [ 'selector' => 'a.vcex-navbar-link:is(:active,.active)', 'property' => 'color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background',
				//	'edit_field_class' => 'vc_col-sm-4',
					'css' => [ 'selector' => 'a.vcex-navbar-link' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'hover_bg',
				//	'edit_field_class' => 'vc_col-sm-4',
					'css' => [ 'selector' => 'a.vcex-navbar-link:is(:hover,.active)', 'property' => 'background' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Active', 'total-theme-core' ),
					'param_name' => 'active_bg',
				//	'edit_field_class' => 'vc_col-sm-4',
					'css' => [ 'selector' => 'a.vcex-navbar-link:is(:active,.active)', 'property' => 'background' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Underlines and decorations - MUST be after colors to prevent issues with active colors because hovers also target actives.
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Link Underline', 'total-theme-core' ),
					'param_name' => 'link_underline',
					'edit_field_class' => 'vc_col-sm-4',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'underline' => esc_html__( 'Underline', 'total-theme-core' ),
						'none' => esc_html__( 'No underline', 'total-theme-core' ),
					],
					'css' => [ 'selector' => 'a.vcex-navbar-link', 'property' => 'text-decoration-line' ],
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Link Underline: Hover', 'total-theme-core' ),
					'param_name' => 'link_underline_hover',
					'edit_field_class' => 'vc_col-sm-4',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'underline' => esc_html__( 'Underline', 'total-theme-core' ),
						'line-through' => esc_html__( 'Line Through', 'total-theme-core' ),
						'none' => esc_html__( 'No underline', 'total-theme-core' ),
					],
					'css' => [ 'selector' => 'a.vcex-navbar-link:hover', 'property' => 'text-decoration-line' ],
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Link Underline: Active', 'total-theme-core' ),
					'param_name' => 'link_underline_active',
					'edit_field_class' => 'vc_col-sm-4',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'underline' => esc_html__( 'Underline', 'total-theme-core' ),
						'line-through' => esc_html__( 'Line Through', 'total-theme-core' ),
						'none' => esc_html__( 'No underline', 'total-theme-core' ),
					],
					'css' => [ 'selector' => 'a.vcex-navbar-link:is(:active,.active)', 'property' => 'text-decoration-line' ],
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Link Underline Offset', 'total-theme-core' ),
					'edit_field_class' => 'vc_col-sm-4',
					'param_name' => 'link_underline_offset',
					'css' => [ 'selector' => 'a.vcex-navbar-link', 'property' => 'text-underline-offset' ],
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Link Underline Offset: Hover', 'total-theme-core' ),
					'edit_field_class' => 'vc_col-sm-4',
					'param_name' => 'hover_link_underline_offset',
					'css' => [ 'selector' => 'a.vcex-navbar-link:hover', 'property' => 'text-underline-offset' ],
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Link Underline Offset: Active', 'total-theme-core' ),
					'edit_field_class' => 'vc_col-sm-4',
					'param_name' => 'active_link_underline_offset',
					'css' => [ 'selector' => 'a.vcex-navbar-link:is(:active,.active)', 'property' => 'text-underline-offset' ],
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Link Underline Thickness', 'total-theme-core' ),
					'param_name' => 'link_decoration_thickness',
					'edit_field_class' => 'vc_col-sm-4',
					'css' => [ 'selector' => 'a.vcex-navbar-link', 'property' => 'text-decoration-thickness' ],
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Link Underline Thickness: Hover', 'total-theme-core' ),
					'edit_field_class' => 'vc_col-sm-4',
					'param_name' => 'hover_link_decoration_thickness',
					'css' => [ 'selector' => 'a.vcex-navbar-link:hover', 'property' => 'text-decoration-thickness' ],
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Link Underline Thickness: Active', 'total-theme-core' ),
					'edit_field_class' => 'vc_col-sm-4',
					'param_name' => 'active_link_decoration_thickness',
					'css' => [ 'selector' => 'a.vcex-navbar-link:is(:active,.active)', 'property' => 'text-decoration-thickness' ],
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'description' => esc_html__( 'This is an older option that will load a 3rd party CSS library of hover animations.', 'total-theme-core'),
					'param_name' => 'hover_animation',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Typography
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => [ 'selector' => 'a.vcex-navbar-link' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					// note: adds utility class instead of css
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => [ 'selector' => 'a.vcex-navbar-link' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => [ 'selector' => 'a.vcex-navbar-link' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'text_transform',
					'css' => [ 'selector' => 'a.vcex-navbar-link' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => 'a.vcex-navbar-link',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Sticky.
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Sticky', 'total-theme-core'),
					'param_name' => 'sticky',
					'group' => esc_html__( 'Sticky', 'total-theme-core' ),
					'description' => esc_html__( 'Note: Sticky is disabled in the front-end editor.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Sticky Background', 'total-theme-core'),
					'param_name' => 'sticky_background',
					'css' => [
						'selector' => '.is-sticky > {{WRAPPER}}',
						'property' => 'background-color',
						'important' => true,
					],
					'dependency' => [ 'element' => 'sticky', 'value' => 'true' ],
					'group' => esc_html__( 'Sticky', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Sticky Endpoint', 'total-theme-core'),
					'param_name' => 'sticky_endpoint',
					'group' => esc_html__( 'Sticky', 'total-theme-core' ),
					'description' => esc_html__( 'Enter the ID or classname of an element that when reached will disable the stickiness. Example: #footer', 'total-theme-core' ),
					'dependency' => [ 'element' => 'sticky', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Offset Navbar Height', 'total-theme-core'),
					'param_name' => 'sticky_offset_nav_height',
					'group' => esc_html__( 'Sticky', 'total-theme-core' ),
					'dependency' => [ 'element' => 'sticky', 'value' => 'true' ],
					'description' => esc_html__( 'Whether the navigation menu height should be included in the offset when calculating local scroll position. Generally you would enable for horizontal menus and disable for vertical menus (for example if the menu is placed in a sidebar).', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Link CSS', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'dependency' => [ 'element' => 'preset_design', 'value' => 'none' ],
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Wrap CSS', 'total-theme-core' ),
					'param_name' => 'wrap_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Deprecated params
				[ 'type' => 'hidden', 'param_name' => 'style' ],
			];
		}

		/**
		 * Enqueue scripts.
		 */
		public static function enqueue_scripts( $atts = [] ): void {
			if ( ! empty( $atts['filter_menu'] ) ) {
				$filter_mode = ! empty( $atts['filter_layout_mode'] ) ? $atts['filter_layout_mode'] : 'masonry';
				switch ( $filter_mode ) {
					case 'ajax':
						totalthemecore_call_non_static( 'Vcex\Ajax', 'enqueue_scripts', self::class, $atts );
						break;
					case 'hide':
						wp_enqueue_script(
							'vcex-navbar-filter',
							vcex_get_js_file( 'frontend/navbar-filter' ),
							[],
							TTC_VERSION,
							true
						);
						break;
					case 'masonry':
					case 'fitRows':
					default:
					//	vcex_enqueue_isotope_scripts(); // @deprecated 2.0 - shouldn't be needed here.
						wp_enqueue_script(
							'vcex-navbar_filter-links',
							vcex_get_js_file( 'frontend/navbar-filter-isotope' ),
							[ 'jquery', 'imagesloaded', 'isotope' ],
							TTC_VERSION,
							true
						);
						break;
				}
			}
			if ( vcex_validate_att_boolean( 'sticky', $atts, false ) ) {
				wp_enqueue_script(
					'vcex-navbar_sticky',
					vcex_get_js_file( 'frontend/navbar-sticky' ),
					[],
					TTC_VERSION,
					true
				);
			}
			if ( vcex_validate_att_boolean( 'mobile_select', $atts, false ) ) {
				wp_enqueue_script(
					'vcex-navbar_mobile-select',
					vcex_get_js_file( 'frontend/navbar-mobile-select' ),
					[],
					TTC_VERSION,
					true
				);
			}
		}

		/**
		 * Returns menu items from the current page children.
		 */
		public static function get_post_children_menu_items( array $atts ): array {
			$menu_items = [];

			if ( empty( $atts['parent_id'] ) && vcex_is_template_edit_mode() ) {
				for ($i = 0; $i < 3; $i++){
					$menu_item             = new stdClass();
					$menu_item->url        = '#';
					$menu_item->title      = esc_html__( 'Sample Page', 'total-theme-core' ) . ' ' . ( $i + 1 );
					$menu_item->type       = 'post_type';
					$menu_item->object     = 'page';
					$menu_item->object_id  = 0;
					$menu_items[]          = $menu_item;
				}
				return $menu_items;
			}

			$parent_id = ! empty( $atts['parent_id'] ) ? absint( $atts['parent_id'] ) : get_the_ID();
			$post_type = get_post_type( $parent_id );

			$args = [
				'posts_per_page' => 100,
				'post_type'      => $post_type,
				'post_parent'    => $parent_id,
				'order'          => ! empty( $atts['orderby'] ) ? sanitize_text_field( $atts['order'] ) : 'ASC',
				'orderby'        => ! empty( $atts['orderby'] ) ? sanitize_sql_orderby( $atts['orderby'] ) : 'menu_order',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			];

			$children = new WP_Query( $args );

			if ( $children->have_posts() ) {
				foreach ( $children->posts as $child ) {
					$menu_item             = new stdClass();
					$menu_item->url        = get_permalink( $child );
					$menu_item->title      = get_the_title( $child );
					$menu_item->type       = 'post_type';
					$menu_item->object     = $post_type;
					$menu_item->object_id  = $child;
					$menu_items[]          = $menu_item;
				}
			}

			return $menu_items;
		}

		/**
		 * Loops through the result of get_terms to return menu items.
		 */
		public static function get_menu_items_from_terms( array $args, array $atts ) {
			$args = (array) apply_filters( 'vcex_navbar_get_terms_args', $args, $atts );
			if ( isset( $atts['menu'] ) && 'post_terms' === $atts['menu'] ) {
				if ( vcex_is_template_edit_mode() ) {
					$menu_items = [];
					for ($i = 0; $i < 3; $i++){
						$menu_item             = new stdClass();
						$menu_item->url        = '#';
						$menu_item->title      = esc_html__( 'Sample Term', 'total-theme-core' ) . ' ' . ( $i + 1 );
						$menu_item->type       = 'taxonomy';
						$menu_item->object     = $args['taxonomy'];
						$menu_item->object_id  = 0;
						$menu_item->term_count = 0;
						$menu_items[]          = $menu_item;
					}
					return $menu_items;
				} else {
					$terms = wp_get_post_terms( get_the_ID(), $args['taxonomy'], $args );
				}
			} else {
				$terms = get_terms( $args );
			}

			if ( ! is_array( $terms ) || ! count( $terms ) || is_wp_error( $terms ) ) {
				return;
			}

			$menu_items = [];

			if ( ! empty( $atts['filter_menu'] ) && empty( $atts['filter_active_item'] ) ) {
				$menu_item          = new stdClass();
				$menu_item->url     = '#';
				$menu_item->title   = ! empty( $atts['all_text'] ) ? sanitize_text_field( $atts['all_text'] ) : esc_html__( 'All', 'total-theme-core' );
				$menu_item->classes = [ 'vcex-navbar__all-link' ];
				$menu_items[]       = $menu_item;
			}

			foreach ( $terms as $term ) {
				$menu_item             = new stdClass();
				$menu_item->url        = get_term_link( $term, $args['taxonomy'] );
				$menu_item->title      = $term->name;
				$menu_item->type       = 'taxonomy';
				$menu_item->object     = $args['taxonomy'];
				$menu_item->object_id  = $term->term_id;
				$menu_item->term_count = $term->count ?? 0;
				$menu_items[]          = $menu_item;
			}

			return $menu_items;
		}

		/**
		 * Parses deprecated attributes.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}
			if ( isset( $atts['style'] ) && 'simple' === $atts['style'] ) {
				$atts['button_style'] = 'plain-text';
				unset( $atts['style'] );
			}
			return $atts;
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			add_filter(
				'vc_autocomplete_vcex_navbar_filter_active_item_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_navbar_filter_active_item_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::render'
			);
			add_filter(
				'vc_autocomplete_vcex_navbar_terms_not_in_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_navbar_terms_not_in_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::render'
			);
			add_filter(
				'vc_autocomplete_vcex_navbar_child_of_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Parent_Terms::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_navbar_child_of_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Parent_Terms::render'
			);
		}

		/**
		 * Returns array for the menu select field.
		 */
		public static function get_menu_choices( $editor = 'wpbakery' ): array {
			$choices = [
				'' => esc_html( '— Select —', 'total-theme-core' ),
				'wp_nav_menus' => [
					'label'    => esc_html__( 'WP Nav Menus', 'total-theme-core' ),
				],
			];

			// Get WP Menus.
			$wp_menus = get_terms( 'nav_menu', [
				'hide_empty' => true,
			] );

			if ( $wp_menus && ! is_wp_error( $wp_menus ) ) {
				$choices['wp_nav_menus']['options'] = [];
				foreach ( (array) $wp_menus as $wp_menu ) {
					$choices['wp_nav_menus']['options'][ $wp_menu->term_id ] = $wp_menu->name;
				}
			}
			
			// Dynamic Menus.
			$choices['dynamic'] = [
				'label'   => esc_html__( 'Dynamic Menus', 'total-theme-core' ),
				'options' => [
					'post_children'     => esc_html__( 'Child Pages', 'total-theme-core' ),
					'post_terms'        => esc_html__( 'Post Terms', 'total-theme-core' ),
					'dynamic_terms'     => esc_html__( 'Taxonomy Terms', 'total-theme-core' ),
					'current_tax_terms' => esc_html__( 'Current Taxonomy Child Terms', 'total-theme-core' ),
				],
			];

			return $choices;
		}

	}

}

new Vcex_Navbar_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Navbar' ) ) {
	class WPBakeryShortCode_Vcex_Navbar extends WPBakeryShortCode {}
}
