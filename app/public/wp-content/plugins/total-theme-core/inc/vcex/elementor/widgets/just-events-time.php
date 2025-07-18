<?php

namespace TotalThemeCore\Vcex\Elementor\Widgets;

use VCEX_Just_Events_Time_Shortcode as Shortcode;
use TotalThemeCore\Vcex\Elementor;
use TotalThemeCore\Vcex\Elementor\Register_Controls;
use Elementor\Widget_Base;

\defined( 'ABSPATH' ) || exit;

class Just_Events_Time extends Widget_Base {

	public function get_name() {
		return 'vcex_just_events_time';
	}

	public function get_title() {
		return Shortcode::get_title();
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_custom_help_url() {
		// none yet.
	}

	public function get_categories() {
		return [ Elementor::DYNAMIC_CATEGORY_ID ];
	}

	public function get_keywords() {
		return [ 'event', 'date', 'just events' ];
	}

	public function get_script_depends() {
		return [];
	}

	public function get_style_depends() {
		return [];
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
