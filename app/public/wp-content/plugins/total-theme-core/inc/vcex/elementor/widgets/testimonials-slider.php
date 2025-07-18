<?php

namespace TotalThemeCore\Vcex\Elementor\Widgets;

use VCEX_Testimonials_Slider_Shortcode as Shortcode;
use TotalThemeCore\Vcex\Elementor;
use TotalThemeCore\Vcex\Elementor\Register_Controls;
use Elementor\Widget_Base;

\defined( 'ABSPATH' ) || exit;

class Testimonials_Slider extends Widget_Base {

	public function get_name() {
		return 'vcex_testimonials_slider';
	}

	public function get_title() {
		return Shortcode::get_title() . ' - Total';
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_custom_help_url() {
		// none yet.
	}

	public function get_categories() {
		return [ Elementor::CATEGORY_ID ];
	}

	public function get_keywords() {
		return [ 'image', 'gallery', 'slider' ];
	}

	public function get_script_depends() {
		return Shortcode::get_script_depends();
	}

	public function get_style_depends() {
		return Shortcode::get_style_depends();
	}

	protected function is_dynamic_content(): bool {
		return true;
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
