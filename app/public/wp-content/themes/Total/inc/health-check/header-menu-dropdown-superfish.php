<?php

namespace TotalTheme\Health_Check;

\defined( 'ABSPATH' ) || exit;

final class Header_Menu_Dropdown_SuperFish extends \TotalTheme\Health_Check {

	/**
	 * The name of the test.
	 */
	protected $test = 'total-header-menu-dropdown-superfish';

	/**
	 * Runs the test.
	 */
	public function run() {
		$method = \get_theme_mod( 'menu_dropdown_method' );

		if ( 'sfhover' !== $method ) {
			return;
		}

		$this->label          = \esc_html__( 'SuperFish scripts are being used for the header menu', 'total' );
		$this->status         = 'recommended';
		$this->badge['color'] = 'blue';
		$this->description    = \esc_html__( 'Using one of the newer header menu dropdown methods "CSS Hover" or "On Click" will slim down the scripts loaded on the site.', 'total' );
		$this->actions = '<p><a href="' . \esc_url( \admin_url( '/customize.php?autofocus[section]=wpex_header_menu_dropdowns' ) ) . '" target="_blank">' . \esc_html( 'Visit the customizer to update your settings', 'total' )  . '<span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>';
	}

}
