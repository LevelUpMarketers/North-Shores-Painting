<?php

namespace TotalTheme\Health_Check;

\defined( 'ABSPATH' ) || exit;

final class jQuery_Easing extends \TotalTheme\Health_Check {

	/**
	 * The name of the test.
	 */
	protected $test = 'total-jquery-easing';

	/**
	 * Runs the test.
	 */
	public function run() {
		$check = (bool) \get_theme_mod( 'scroll_to_easing', false );

		if ( ! $check ) {
			return;
		}

		$this->label          = \esc_html__( 'jQuery easing is enabled', 'total' );
		$this->status         = 'recommended';
		$this->badge['color'] = 'blue';
		$this->description    = \esc_html__( 'jQuery Easing is used for smoother local scrolling links but can be disabled if your site doesn\'t have any local scroll links or if you rather use native browser scrolling methods. Disabling this feature will prevent added scripts from being loaded on the site.', 'total' );
		$this->actions = '<p><a href="' . \esc_url( \admin_url( '/customize.php?autofocus[section]=wpex_local_scroll' ) ) . '" target="_blank">' . \esc_html( 'Visit the customizer to update your settings', 'total' )  . '<span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>';
	}

}
