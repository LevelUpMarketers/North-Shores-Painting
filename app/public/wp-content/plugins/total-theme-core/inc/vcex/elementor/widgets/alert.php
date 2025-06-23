<?php

namespace TotalThemeCore\Vcex\Elementor\Widgets;

use VCEX_Alert_Shortcode as Shortcode;
use TotalThemeCore\Vcex\Elementor;
use TotalThemeCore\Vcex\Elementor\Register_Controls;
use Elementor\Widget_Base;

\defined( 'ABSPATH' ) || exit;

class Alert extends Widget_Base {

	public function get_name() {
		return 'vcex_alert'; // we don't use Shortcode::TAG because this is used to check if the shortcode is already registered.
	}

	public function get_title() {
		return Shortcode::get_title() . ' - Total';
	}

	public function get_icon() {
		return 'eicon-alert';
	}

	public function get_custom_help_url() {
		// none yet.
	}

	public function get_categories() {
		return [ Elementor::CATEGORY_ID ];
	}

	public function get_keywords() {
		return [ 'alert', 'notice' ];
	}

	public function get_script_depends() {
		return [];
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
		$content = $atts['content'] ?? null;
		echo Shortcode::output( $atts, $content );
	}

}
