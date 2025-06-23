<?php

namespace TotalThemeCore\Shortcodes;

\defined( 'ABSPATH' ) || exit;

/**
 * WP Login Link Shortcode.
 */
final class Shortcode_WP_Login_Link {

	public function __construct() {
		if ( ! \shortcode_exists( 'wp_login_url' ) ) {
			\add_shortcode( 'wp_login_url', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		if ( \is_admin() && ! \wp_doing_ajax() ) {
			return; // !important check because shortcode functions are only loaded on the front-end
		}

		\extract( \shortcode_atts( [
			'url'             => '',
			'login_url'       => '', // legacy
			'icon_logged_in'  => '',
			'text'            => \esc_html__( 'Login', 'total-theme-core' ),
			'logout_text'     => \esc_html__( 'Log Out', 'total-theme-core' ),
			'target'          => '',
			'logout_redirect' => '',
			'icon'            => '',
			'icon_logged_in'  => '',
		], $atts, 'wp_login_url' ) );

		if ( 'blank' === $target || '_blank' === $target ) {
			$target = ' target="_blank" rel="noopener"';
		} else {
			$target = '';
		}

		if ( $url ) {
			$login_url = $url;
		} elseif ( $login_url ) {
			$login_url = $login_url;
		} else {
			$login_url = \wp_login_url();
		}

		if ( ! $logout_redirect ) {
			$permalink = \get_permalink();
			if ( $permalink ) {
				$logout_redirect = $permalink;
			} else {
				$logout_redirect = \home_url( '/' );
			}
		}

		$user_logged_in = \is_user_logged_in();

		if ( $user_logged_in ) {
			$href  = \wp_logout_url( $logout_redirect );
			$class = 'wpex_logout';
			$text  = $logout_text;
		} else {
			$href  = $login_url;
			$class = 'login';
		}

		$output = '<a href="' . \esc_url( $href ) . '" class="' . \esc_attr( $class ) . '"' . $target . '>';
			if ( $icon ) {
				if ( $icon_logged_in && $user_logged_in ) {
					$icon = $icon_logged_in;
				}
				$icon_html = \function_exists( '\totaltheme_get_icon' ) ? \totaltheme_get_icon( $icon, 'wpex-mr-10' ) : '';
				if ( ! $icon_html ) {
					$icon_html = '<span class="' . \esc_attr( $icon ) . '" aria-hidden="true"></span>';
				}
				$output .= $icon_html;
			}
			$output .= \esc_html( $text );
		$output .= '</a>';

		return $output;
	}

}
