<?php

namespace TotalThemeCore\Vcex\Elementor\Widgets;

use VCEX_Widget_Modern_Menu_Shortcode as Shortcode;
use TotalThemeCore\Vcex\Elementor;
use TotalThemeCore\Vcex\Elementor\Register_Controls;
use Elementor\Widget_Base;

\defined( 'ABSPATH' ) || exit;

class Widget_Modern_Menu extends Widget_Base {

	public function get_name() {
		return 'vcex_widget_modern_menu';
	}

	public function get_title() {
		return Shortcode::get_title() . ' - Total';
	}

	public function get_icon() {
		return 'eicon-sidebar';
	}

	public function get_custom_help_url() {
		// none yet.
	}

	public function get_categories() {
		return [ Elementor::DYNAMIC_CATEGORY_ID ];
	}

	public function get_keywords() {
		return [ 'menu', 'sidebar', 'widget' ];
	}

	public function get_script_depends() {
		return [];
	}

	public function get_style_depends() {
		return [];
	}

	protected function register_controls() {
		new Register_Controls( $this, Shortcode::get_params() );
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		$atts['is_elementor_widget'] = true;
		echo Shortcode::output( $atts );
	}

}
