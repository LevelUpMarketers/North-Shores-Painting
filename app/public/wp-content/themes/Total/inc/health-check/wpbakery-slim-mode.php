<?php

namespace TotalTheme\Health_Check;

\defined( 'ABSPATH' ) || exit;

final class WPBakery_Slim_Mode extends \TotalTheme\Health_Check {

	/**
	 * The name of the test.
	 */
	protected $test = 'total-wpbakery-slim-mode';

	/**
	 * Runs the test.
	 */
	public function run() {
		$slim_mode_enabled = totaltheme_call_static( 'TotalTheme\Integration\WPBakery\Slim_Mode', 'is_enabled' );

		if ( $slim_mode_enabled ) {
			return;
		}

		$this->label = \esc_html__( 'WPBakery Slim Mode Disabled', 'total' );
		$this->status = 'recommended';
		$this->badge['color'] = 'blue';
		$this->description = \esc_html__( 'Exclusive theme feature that removes redundant WPBakery elements and their CSS to greatly slim things down. Enabling this option will also hide elements intended for dynamic templates and custom cards from showing up when editing posts and pages. If enabling on an existing site you will need to double to ensure you are not using any of the elements removed by this function and if so, replace them.', 'total' );

		$panel_link = totaltheme_call_static( 'Admin\Theme_Panel', 'get_setting_link', 'wpb_slim_mode_enable' );
	
		if ( $panel_link ) {
			$this->actions = '<p><a href="' . \esc_url( $panel_link ) . '" target="_blank">' . \esc_html( 'Visit the Theme Panel to enable', 'total' )  . ' <span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>';
		}
	}

}
