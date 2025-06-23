<?php
/**
 * vcex_login_form shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

$wrap_class = [
	'vcex-login-form',
	'vcex-module',
	'wpex-clr',
];

$style = ! empty( $atts['style'] ) ? sanitize_text_field( $atts['style'] ) : 'bordered';

if ( 'boxed' === $style || 'bordered' === $style ) {
	$wrap_class[] = "wpex-{$style}";
}

if ( ! empty( $atts['form_style'] ) ) {
	$wrap_class[] = 'wpex-form-' . sanitize_html_class( $atts['form_style'] );
}

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$float = ! empty( $atts['float'] ) ? $atts['float'] : 'center';
	$wrap_class[] = vcex_parse_align_class( $float );
}

$is_user_logged_in = ( is_user_logged_in() && ! vcex_is_frontend_edit_mode() ) ? true : false;

if ( $is_user_logged_in ) {
	$wrap_class[] = 'logged-in';
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_login_form' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_login_form', $atts );

// Begin output.
$output = '<div class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $atts['unique_id'] ) . '>';

	// Check if user is logged in and not in front-end editor.
	if ( $is_user_logged_in ) :

		$output .= vcex_parse_text_safe( $content );

	// If user is not logged in display login form.
	else :

		$output .= wp_login_form( [
			'echo' => false,
			'redirect' => $atts['redirect'] ? esc_url( $atts['redirect'] ) : esc_url( wpex_get_current_url() ),
			'form_id' => 'vcex-loginform',
			'label_username' => ! empty( $atts['label_username'] ) ? sanitize_text_field( $atts['label_username'] ) : esc_html__( 'Username', 'total-theme-core' ),
			'label_password' => ! empty( $atts['label_password'] ) ? sanitize_text_field( $atts['label_password'] ) : esc_html__( 'Password', 'total-theme-core' ),
			'label_remember' => ! empty( $atts['label_remember'] ) ? sanitize_text_field( $atts['label_remember'] ) : esc_html__( 'Remember Me', 'total-theme-core' ),
			'label_log_in' => ! empty( $atts['label_log_in'] ) ? sanitize_text_field( $atts['label_log_in'] ) : esc_html__( 'Log In', 'total-theme-core' ),
			'remember' => vcex_validate_att_boolean( 'remember', $atts, true ),
			'value_username' => NULL,
			'value_remember' => false,
		] );

		if ( 'true' == $atts['register'] || 'true' == $atts['lost_password'] ) {

			$output .= '<div class="vcex-login-form-nav wpex-clr">';

				if ( 'true' == $atts['register'] ) {

					$register_label = $atts['register_label'] ?: esc_html__( 'Register', 'total-theme-core' );
					$register_url = $atts['register_url'] ?: wp_registration_url();

					$output .= '<a href="' . esc_url( $register_url ) . '" class="vcex-login-form-register">' . esc_html( $register_label ) . '</a>';

				}

				if ( 'true' == $atts['register'] && 'true' == $atts['lost_password'] ) {
					$output .= '<span class="pipe">|</span>';
				}

				if ( 'true' == $atts['lost_password'] ) {

					$lost_password_label = $atts['lost_password_label'] ?:  esc_html__( 'Lost Password?', 'total-theme-core' );

					$output .= '<a href="' . esc_url( wp_lostpassword_url( get_permalink() ) ) . '" class="vcex-login-form-lost">' . esc_html( $lost_password_label ) . '</a>';
				}

			$output .= '</div>';

		}

	endif;

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
