<?php

namespace TotalThemeCore\Vcex\Elementor\Widgets;

use VCEX_Countdown_Shortcode as Shortcode;
use TotalThemeCore\Vcex\Elementor;
use TotalThemeCore\Vcex\Elementor\Register_Controls;
use Elementor\Widget_Base;

\defined( 'ABSPATH' ) || exit;

class Countdown extends Widget_Base {

	public function get_name() {
		return 'vcex_countdown';
	}

	public function get_title() {
		return Shortcode::get_title() . ' - Total';
	}

	public function get_icon() {
		return 'eicon-countdown';
	}

	public function get_custom_help_url() {
		// none yet.
	}

	public function get_categories() {
		return [ Elementor::CATEGORY_ID ];
	}

	public function get_keywords() {
		return [ 'date', 'countdown' ];
	}

	public function get_script_depends() {
		return Shortcode::get_script_depends();
	}

	public function get_style_depends() {
		return [];
	}

	protected function register_controls(): void {
		new Register_Controls( $this, Shortcode::get_params() );
	}

	protected function render(): void {
		$atts = $this->get_settings_for_display();
		$atts['is_elementor_widget'] = true;
		echo Shortcode::output( $atts );
	}

}
