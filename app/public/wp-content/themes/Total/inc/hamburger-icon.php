<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Hamburger Icon.
 */
class Hamburger_Icon {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Renders the hamburger icon.
	 */
	public static function render( $args = [] ): string {
		$args = self::parse_args( $args );
		$icon = '<span class="' . \esc_attr( self::get_class( $args ) ) . '" aria-hidden="true"><span></span></span>';
		return (string) \apply_filters( 'wpex_hamburger_icon', $icon, $args );
	}

	/**
	 * Parses the hamburger icon args.
	 */
	protected static function parse_args( $args ): array {
		$args = \wp_parse_args( $args, [
			'toggle_state' => true,
			'rounded'      => false,
			'class'        => '',
		] );
		return (array) \apply_filters( 'wpex_hamburger_icon_args', $args );
	}

	/**
	 * Returns class for the hamburger icon.
	 */
	protected static function get_class( $args ): string {
		if ( $args['class'] ) {
			$class = "{$args['class']} wpex-hamburger-icon";
		} else {
			$class = 'wpex-hamburger-icon';
		}

		if ( \wp_validate_boolean( $args['rounded'] ) ) {
			$class .= ' wpex-hamburger-icon--rounded';
		}

		if ( \wp_validate_boolean( $args['toggle_state'] ) ) {
			$class .= ' wpex-hamburger-icon--inactive';
			$animate = $args['animate'] ?? true;
			if ( $animate ) {
				$class .= ' wpex-hamburger-icon--animate';
			}
		}

		return $class;
	}

}
