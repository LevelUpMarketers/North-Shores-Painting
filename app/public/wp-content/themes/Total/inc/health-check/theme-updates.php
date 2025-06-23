<?php

namespace TotalTheme\Health_Check;

\defined( 'ABSPATH' ) || exit;

final class Theme_Updates extends \TotalTheme\Health_Check {

	/**
	 * The name of the test.
	 */
	protected $test = 'total-theme-updates';

	/**
	 * Runs the test.
	 */
	public function run() {
		$passed_checks = true;
		$description = '';
		$actions = '';
		
		if ( ! \totaltheme_get_license() && ! \class_exists( 'Envato_Market', false ) ) {
			$passed_checks = false;
			$description .= '<p>' . \sprintf( \esc_html__( 'Your theme license is not active so your site will not receive update notifications. If you are working on a development site, you can install the %sEnvato Market plugin%s to receive updates without having to buy a new license. But please purchase a new license for each live site. Thank you!', 'total' ), '<a href="https://www.envato.com/lp/market-plugin/" target="_blank" rel="noreferrer">', '  <span aria-hidden="true" class="dashicons dashicons-external"></span></a>' ). '</p>';
			$actions .= '<p><a href="' . \esc_url( \admin_url( 'admin.php?page=wpex-panel-theme-license' ) ) . '" target="_blank">' . \esc_html( 'Activate Your License', 'total' )  . ' <span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>';
		}

		if ( 1 === \count( (array) \wp_get_themes() ) ) {
			$passed_checks = false;
			$description .= '<p> * ' . \esc_html__( 'It appears you only have one theme installed. In order for theme updates to display in your dashboard you must have at least one of the default WP themes active to serve as a backup incase an update fails. This is a WordPress requirement.', 'total' ) . '</p>';
			$actions .= '<p><a href="' . \esc_url( \admin_url( 'themes.php' ) ) . '" target="_blank">' . \esc_html( 'Manage your themes.', 'total' )  . ' <span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>';
		}

		if ( $passed_checks ) {
			return;
		}

		$this->label          = \esc_html__( 'Theme updates are disabled', 'total' );
		$this->status         = 'critical';
		$this->badge['color'] = 'red';
		$this->description    = $description;
		$this->actions        = $actions;
	}

}
