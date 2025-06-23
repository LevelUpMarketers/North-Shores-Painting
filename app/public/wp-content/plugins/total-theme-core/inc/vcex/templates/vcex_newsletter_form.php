<?php
/**
 * vcex_newsletter_form shortcode output
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.9.1
 */

defined( 'ABSPATH' ) || exit;

// Main variables.
$output           = '';
$stack_fields     = vcex_validate_att_boolean( 'stack_fields', $atts, false );
$placeholder_text = ! empty( $atts['placeholder_text'] ) ? $atts['placeholder_text'] : esc_html__( 'Enter your email address', 'total-theme-core' );

// Wrapper classes.
$wrap_class = [
	'vcex-newsletter-form',
];

if ( $stack_fields ) {
	$wrap_class[] = 'vcex-newsletter-form--stacked';
}

$wrap_class[] = 'vcex-module';
$wrap_class[] = 'wpex-flex';
$wrap_class[] = 'wpex-max-w-100'; // prevent issues with flex wraps when using the flex container.

if ( ! empty( $atts['input_width'] ) && ! empty( $atts['input_align'] ) ) {
	$wrap_class[] = vcex_parse_justify_content_class( $atts['input_align'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( vcex_validate_att_boolean( 'fullwidth_mobile', $atts, false ) && ! $stack_fields ) {
	$wrap_class[] = 'vcex-fullwidth-mobile';
}

if ( ! empty( $atts['classes'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['classes'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_newsletter_form', $atts );

// Begin output.
$output .= '<div class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $atts ) . '>';

	$form_class = 'vcex-newsletter-form-wrap';

	if ( empty( $atts['input_width'] ) ) {
		$form_class .= ' wpex-flex-grow';
	}

	$output .= '<div class="' . esc_attr( $form_class ) . '">';

		$form_class = 'wpex-flex';

		if ( $stack_fields ) {
			$form_class .= ' wpex-flex-col';
			if ( empty( $atts['gap'] ) ) {
				$atts['gap'] = '10';
			}
		}

		if ( ! empty( $atts['gap'] ) ) {
			$form_class .= ' wpex-gap-' . sanitize_html_class( absint( $atts['gap'] ) );
		}

		/**
		 * Filters the vcex_newsletter_form action url.
		 *
		 * @param string $form_action
		 * @param array $shortcode_attributes
		 */
		$form_action = (string) apply_filters( 'vcex_newsletter_form_action_url', $atts['form_action'], $atts );

		$output .= '<form action="' . esc_url( $form_action ) . '" method="post" class="' . esc_attr( $form_class ) . '">';

			if ( ! empty( $atts['input_label'] ) ) {
				//@todo Add support for input labels.
			} else {
				$output .= '<label class="vcex-newsletter-form-label wpex-text-current wpex-flex-grow">';
				$output .= '<span class="screen-reader-text">' . esc_html( $placeholder_text ) . '</span>';
			}

				$input_name = ! empty( $atts['input_name'] ) ? $atts['input_name'] : 'EMAIL';

				$output .= '<input class="vcex-newsletter-form-input" type="email" name="' . esc_attr( $input_name ) . '" placeholder="' . esc_attr( $placeholder_text ) . '" autocomplete="off" required>';

			if ( empty( $atts['input_label'] ) ) {
				$output .= '</label>';
			}

			/** Hidden Fields **/
			if ( ! empty( $atts['hidden_fields'] ) ) {
				$hidden_fields = $atts['hidden_fields'];
				$hidden_fields = explode( ',', $hidden_fields );
				if ( is_array( $hidden_fields ) ) {
					foreach ( $hidden_fields as $field ) {
						$field_attrs = explode( '|', $field );
						if ( isset( $field_attrs[0] ) && isset( $field_attrs[1] ) ) {
							$output .= '<input type="hidden" name="' . esc_attr( $field_attrs[0] ) . '" value="' . esc_attr( $field_attrs[1] ) . '">';
						}
					}
				}
			}

			ob_start();
				do_action( 'vcex_newsletter_form_extras' );
			$output .= ob_get_clean();

			/** Submit Button ***/
			$submit_text = ! empty( $atts['submit_text'] ) ? $atts['submit_text'] : esc_html__( 'Sign Up', 'total-theme-core' );

			if ( $submit_text ) {
				$submit_class = 'vcex-newsletter-form-button';
				if ( $stack_fields ) {
					$submit_class .= ' vcex-newsletter-form-button--min-height';
				}
				$submit_class .= ' wpex-flex-shrink-0';
				$submit_class .= ' theme-button';
				$output .= '<button type="submit" value="" class="' . esc_attr( trim( $submit_class) ) . '">';
					$output .= do_shortcode( wp_kses_post( $submit_text ) );
				$output .= '</button>';
			}

		$output .= '</form>';

	$output .= '</div>';

$output .= '</div>';

echo $output; // @codingStandardsIgnoreLine
