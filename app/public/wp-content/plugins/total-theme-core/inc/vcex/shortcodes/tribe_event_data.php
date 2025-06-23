<?php

defined( 'ABSPATH' ) || exit;

/**
 * Tribe Event Data Shortcode.
 */
if ( ! class_exists( 'Vcex_Tribe_Event_Data_Shortcode' ) ) {

	class Vcex_Tribe_Event_Data_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_tribe_event_data';

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
			return esc_html__( 'Tribe Event Data', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display data from the Events Calendar plugin', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Data', 'total-theme-core' ),
					'param_name' => 'return',
					'admin_label' => true,
					'choices' => [
						'' => esc_html( '— Select —', 'total-theme-core' ),
						'schedule_details' => esc_html__( 'Formatted Date', 'total-theme-core' ),
						'start_date' => esc_html__( 'Start Date', 'total-theme-core' ),
						'end_date' => esc_html__( 'End Date', 'total-theme-core' ),
						'cost' => esc_html__( 'Cost', 'total-theme-core' ),
						'website' => esc_html__( 'Event Website URL', 'total-theme-core' ),
						'website_link' => esc_html__( 'Event Website Link', 'total-theme-core' ),
						'venue' => esc_html__( 'Venue', 'total-theme-core' ),
						'venu_website' => esc_html__( 'Venue Website', 'total-theme-core' ),
						'venu_website_link' => esc_html__( 'Venue Website Link', 'total-theme-core' ),
						'venue_region' => esc_html__( 'Venue Region (State or Province)', 'total-theme-core' ),
						'venue_city' => esc_html__( 'Venue City', 'total-theme-core' ),
						'address' => esc_html__( 'Venue Address', 'total-theme-core' ),
						'map' => esc_html__( 'Venue Map', 'total-theme-core' ),
						'phone' => esc_html__( 'Venue Phone Number', 'total-theme-core' ),
						'phone_link' => esc_html__( 'Venue Phone Number with Link', 'total-theme-core' ),
						'category' => esc_html__( 'Category Name', 'total-theme-core' ),
						'category_link' => esc_html__( 'Category Link', 'total-theme-core' ),
						'organizer' => esc_html__( 'Organizer Name', 'total-theme-core' ),
						'organizer_link' => esc_html__( 'Organizer Website Link', 'total-theme-core' ),
						'organizer_phone' => esc_html__( 'Organizer Phone Number', 'total-theme-core' ),
						'organizer_phone_link' => esc_html__( 'Organizer Phone Number with Link', 'total-theme-core' ),
						'organizer_email' => esc_html__( 'Organizer Email', 'total-theme-core' ),
					],
					'description' => esc_html__( 'Select the data you wish to display.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
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
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
					'editors' => [ 'wpbakery' ],
				],
				// Label
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Label', 'total-theme-core' ),
					'param_name' => 'label',
					'group' => esc_html__( 'Label', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'label_margin',
					'std' => '5px',
					'choices' => [
						'0px' => '0px',
						'5px' => '5px',
						'10px' => '10px',
						'15px' => '15px',
						'20px' => '20px',
						'25px' => '25px',
						'30px' => '30px',
						'40px' => '40px',
						'50px' => '50px',
					],
					'description' => esc_html__( 'Select the margin your label and the event data.', 'total-theme-core' ),
					'group' => esc_html__( 'Label', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'label_color',
					'css' => [
						'property' => 'color',
						'selector' => '.vcex-tribe-event-data__label',
					],
					'group' => esc_html__( 'Label', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'label_font_size',
					'css' => [
						'property' => 'font-size',
						'selector' => '.vcex-tribe-event-data__label',
					],
					'group' => esc_html__( 'Label', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'label_font_weight',
					'choices' => 'font_weight',
					'css' => [
						'property' => 'font-weight',
						'selector' => '.vcex-tribe-event-data__label',
					],
					'group' => esc_html__( 'Label', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Typography
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'italic',
					'value' => [
						esc_html__( 'Normal', 'total-theme-core' ) => '',
						esc_html__( 'Italic', 'total-theme-core' ) => 'true',
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Hidden fields
				[ 'type' => 'hidden', 'param_name' => 'data_only' ],
			];
		}

	}

}

new Vcex_Tribe_Event_Data_Shortcode;

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Tribe_Events_Data' ) ) {
	class WPBakeryShortCode_Vcex_Tribe_Events_Data extends WPBakeryShortCodesContainer {}
}
