<?php

namespace TotalThemeCore\Vcex\Elementor\Widgets;

use TotalThemeCore\Vcex\Elementor;
use TotalThemeCore\Vcex\Elementor\Register_Controls;
use TotalThemeCore\Vcex\Carousel\Core as Vcex_Carousel;
use VCEX_Login_Form as Shortcode;
use Elementor\Widget_Base;

\defined( 'ABSPATH' ) || exit;

class Login_Form extends Widget_Base {

	public function get_name(): string {
		return 'vcex_login_form';
	}

	public function get_title(): string {
		return Shortcode::get_title() . ' - Total';
	}

	public function get_icon(): string {
		return 'eicon-lock-user';
	}

	public function get_custom_help_url() {
		// none yet.
	}

	public function get_categories(): array {
		return [ Elementor::CATEGORY_ID ];
	}

	public function get_keywords(): array {
		return [ 'login', 'form', 'login form' ];
	}

	public function get_script_depends(): array {
		return [];
	}

	public function get_style_depends(): array {
		return [];
	}

	protected function is_dynamic_content(): bool {
		return true;
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
