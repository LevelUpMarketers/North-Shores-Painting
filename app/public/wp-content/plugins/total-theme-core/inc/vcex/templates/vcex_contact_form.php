<?php

/**
 * Contact Form shortcode template.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$output = '';
$unique_id = uniqid( 'vcex-contact-form__' );
$use_placeholders = vcex_validate_att_boolean( 'enable_placeholders', $atts );
$label_fw = ! empty( $atts['labels_font_weight'] ) ? $atts['labels_font_weight'] : 'semibold';
$label_fw_class = vcex_parse_font_weight_class( $label_fw );
$items_spacing = ! empty( $atts['items_spacing'] ) ? absint( $atts['items_spacing'] ) : 15;

// Shortcode classes.
$wrap_class = [
	'vcex-contact-form',
];

if ( ! empty( $atts['style'] ) ) {
	if ( 'white' === $atts['style'] ) {
		$wrap_class[] = 'light-form';
	} else {
		$wrap_class[] = "wpex-form-{$atts['style']}";
	}
}

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-mx-auto';
}

if ( ! empty( $atts['shadow'] ) && empty( $atts['padding'] ) ) {
	$wrap_class[] = 'wpex-p-20';
}

if ( ! empty( $atts['text_align'] ) ) {
	$wrap_class[] = "wpex-text-{$atts['text_align']}";
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_contact_form' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

// Parse shortcode classes.
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_contact_form', $atts );

// Shortcode data.
$shortcode_data = 'data-ajaxurl="' . esc_attr( set_url_scheme( admin_url( 'admin-ajax.php' ) ) ) . '"';

// Notices
$notice_success = ! empty( $atts['notice_success'] ) ? sanitize_text_field( $atts['notice_success'] ) : esc_html__( 'Thank you for the message. We will respond as soon as possible.', 'total-theme-core' );
$shortcode_data .= ' data-notice-success="' . esc_attr( vcex_parse_text_safe( $notice_success ) ) . '"';

$notice_error = ! empty( $atts['notice_error'] ) ? sanitize_text_field( $atts['notice_error'] ) : esc_html__( 'Some errors occurred.', 'total-theme-core' );
$shortcode_data .= ' data-notice-error="' . esc_attr( vcex_parse_text_safe( $notice_error ) ) . '"';

// Custom to subject.
if ( ! empty( $atts['email_subject'] ) ) {
	$shortcode_data .= ' data-subject="' . esc_attr( vcex_parse_text_safe( $atts['email_subject'] ) ) . '"';
}

// Check if reCAPTCHA is enabled.
if ( 'true' === $atts['enable_recaptcha'] && function_exists( 'wpex_get_recaptcha_keys' ) ) {
	$site_key = wpex_get_recaptcha_keys( 'site' );
	$shortcode_data .= ' data-recaptcha="' . esc_attr( $site_key ) . '"';
}

// Used to send the post ID to the form ajax request.
if ( is_singular() ) {
	$shortcode_data .= ' data-post_id="' . esc_attr( get_the_ID() ) . '"';
}

// Security Nonce.
$shortcode_data .= ' data-nonce="' . esc_attr( wp_create_nonce( 'vcex-contact-form-nonce' ) ) . '"';

// Required labels.
$show_required_label = vcex_validate_boolean( $atts['enable_required_label'] );
$placeholder_required = ! empty( $atts['label_required'] ) ? ' ' . sanitize_text_field( trim( $atts['label_required'] ) ) : '*';

// Begin output.
$output .= '<div class="' . esc_attr( $wrap_class ) . '" ' . $shortcode_data . '>';

	// Start form output.
	$output .= '<form class="vcex-contact-form__form">';

		// Display fields.
		$fields_class = "vcex-contact-form__fields wpex-mb-{$items_spacing}";

		if ( vcex_validate_boolean( $atts['stack_fields'] ) ) {
			$fields_class .= " wpex-flex wpex-flex-col wpex-gap-{$items_spacing}";
		} else {
			$gap = ! empty( $atts['items_spacing'] ) ? $items_spacing : 20;
			$fields_class .= " wpex-flex wpex-gap-{$gap} wpex-flex-wrap";
		}

		$output .= '<div class="' . esc_attr( $fields_class ) . '">';

			// Store label classname.
			if ( $use_placeholders ) {
				$label_class = 'screen-reader-text';
			} else {
				if ( ! empty( $atts['labels_bottom_margin'] ) ) {
					$label_bottom_margin_class = vcex_parse_margin_class( $atts['labels_bottom_margin'], 'bottom' );
				} else {
					$label_bottom_margin_class = 'wpex-mb-5';
				}
				$label_class = "vcex-contact-form__label wpex-block {$label_bottom_margin_class} {$label_fw_class}";
			}

			// Name.
			$field_id = "{$unique_id}-name";
			$label = ! empty( $atts['label_name'] ) ? $atts['label_name'] : esc_html__( 'Your Name', 'total-theme-core' );
			$placeholder = $show_required_label ? $label . $placeholder_required : $label;
			$placeholder = $use_placeholders ? ' placeholder="' . esc_attr( $placeholder ) . '"' : '';

			$output .= '<div class="vcex-contact-form__name wpex-flex-grow">';
				$output .= '<label class="' . esc_attr( $label_class ) . '" for="' . esc_attr( $field_id ) . '">' . esc_html( $label );
					if ( $show_required_label ) {
						if ( ! empty( $atts['label_required'] ) ) {
							$output .= ' <span class="vcex-contact-form__required">' . esc_html( trim( $atts['label_required'] ) ) . '</span>';
						} else {
							$output .= '<sup class="vcex-contact-form__required">*</sup>';
						}
					}
				$output .= '</label>';
				$output .= '<input class="vcex-contact-form__input wpex-w-100" type="text" id="' . esc_attr( $field_id ) . '" name="vcex_cf_name" required' . $placeholder . '>';
			$output .= '</div>';

			// Email.
			$field_id = "{$unique_id}-email";
			$label = ! empty( $atts['label_email'] ) ? $atts['label_email'] : esc_html__( 'Your Email', 'total-theme-core' );
			$placeholder = $show_required_label ? $label . $placeholder_required : $label;
			$placeholder = $use_placeholders ? ' placeholder="' . esc_attr( $placeholder ) . '"' : '';

			$output .= '<div class="vcex-contact-form__email wpex-flex-grow">';
				$output .= '<label class="' . esc_attr( $label_class ) . '" for="' . esc_attr( $field_id ) . '">' . esc_html( $label );
					if ( $show_required_label ) {
						if ( ! empty( $atts['label_required'] ) ) {
							$output .= ' <span class="vcex-contact-form__required">' . esc_html( trim( $atts['label_required'] ) ) . '</span>';
						} else {
							$output .= '<sup class="vcex-contact-form__required">*</sup>';
						}
					}
				$output .= '</label>';
				$output .= '<input class="vcex-contact-form__input wpex-w-100" type="email" id="' . esc_attr( $field_id ) . '" name="vcex_cf_email" required' . $placeholder . '>';
			$output .= '</div>';

		$output .= '</div>';

		// Message.
		$field_id = "{$unique_id}-message";
		$label = ! empty( $atts['label_message'] ) ? $atts['label_message'] : esc_html__( 'Message', 'total-theme-core' );
		$placeholder = $use_placeholders ? ' placeholder="' . esc_attr( $label ) . '"' : '';
		$rows = is_numeric( $atts['message_rows'] ) ? absint( $atts['message_rows'] ) : 8;
		$minlength = is_numeric( $atts['message_minlength'] ) ? ' minlength="' . esc_attr( absint( $atts['message_minlength'] ) ) . '"' : '';
		$maxlength = is_numeric( $atts['message_maxlength'] ) ? ' maxlength="' . esc_attr( absint( $atts['message_maxlength'] ) ) . '"' : '';

		$output .= '<div class="vcex-contact-form__message wpex-mb-' . $items_spacing . '">';
			$output .= '<label class="' . esc_attr( $label_class ) . '" for="' . esc_attr( $field_id ) . '">' . esc_html( $label ) . '</label>';
			$output .= '<textarea rows="' . esc_attr( $rows ) . '" class="vcex-contact-form__textarea wpex-align-top wpex-w-100" id="' . esc_attr( $field_id ) . '" name="vcex_cf_message" required' . $placeholder . $minlength . $maxlength . '></textarea>';
		$output .= '</div>';

		// Privacy policy.
		if ( vcex_validate_boolean( $atts['enable_privacy_check'] ) ) {
			$privacy_policy_page = get_option( 'wp_page_for_privacy_policy' );
			$privacy_policy_url = $privacy_policy_page ? get_permalink( $privacy_policy_page ) : '#';
			$field_id = $unique_id . '-privacy';
			if ( ! empty( $atts['label_privacy'] ) ) {
				$label = $atts['label_privacy'];
				$label = str_replace( '{{', '<a href="' . esc_url( $privacy_policy_url ) . '" target="_blank" rel="noopener noreferrer">', $label );
				$label = str_replace( '}}', '</a>', $label );
			} else {
				$label = sprintf( esc_html__( 'I agree with the %sPrivacy Policy%s.', 'total-theme-core' ), '<a href="' . esc_url( $privacy_policy_url ) . '" target="_blank" rel="noopener noreferrer">', '</a>' );
			}

			$output .= '<div class="vcex-contact-form__privacy wpex-flex wpex-items-center wpex-gap-5 wpex-mb-' . $items_spacing . '"><input type="checkbox" class="vcex-contact-form__checkbox" id="' . esc_attr( $field_id ) . '" name="vcex_cf_privacy" required>';

			$label_class = 'vcex-contact-form__label wpex-block';
			if ( ! $use_placeholders ) {
				$label_class .= ' ' . $label_fw_class;
			}
			$output .= '<label class="' . esc_attr( $label_class ) . '" for="' . esc_attr( $field_id ) . '">' . wp_kses_post( do_shortcode( $label ) ) . '</label></div>';
		}

		// Button.
		$button_text = ! empty( $atts['button_text'] ) ? $atts['button_text'] : esc_html__( 'Submit', 'total-theme-core' );
		$button_fullwidth = ( 'true' === $atts['button_fullwidth'] ) ? ' vcex-contact-form__submit--full' : '';
		$output .= '<button class="vcex-contact-form__submit' . esc_attr( $button_fullwidth ) . ' theme-button">' . esc_html( $button_text ) . '</button>';

		// reCAPTCHA branding.
		if ( vcex_validate_boolean( $atts['enable_recaptcha'] )
			&& vcex_validate_boolean( $atts['enable_recaptcha_notice'] )
			&& function_exists( 'wpex_get_recaptcha_keys' )
		) {

			$recaptcha_keys = wpex_get_recaptcha_keys();

			if ( ! empty( $recaptcha_keys['site'] ) && ! empty( $recaptcha_keys['secret'] ) ) {

				$output .= '<style>.grecaptcha-badge { visibility: hidden; }</style>';

				$recaptcha_notice = 'This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy">Privacy Policy</a> and <a href="https://policies.google.com/terms">Terms of Service</a> apply.';

				/**
				 * Filters the reCAPTCHA notice text.
				 *
				 * @link https://developers.google.com/recaptcha/docs/faq
				 * @param string $recaptcha_notice
				 */
				$recaptcha_notice = apply_filters( 'vcex_contact_form_recaptcha_notice', $recaptcha_notice );

				$output .= '<div class="vcex-contact-form__recaptcha wpex-mt-' . $items_spacing . ' wpex-text-sm">' . wp_kses_post( $recaptcha_notice ) . '</div>';

			}
		}

		// Spinner.
		$output .= '<div class="vcex-contact-form__spinner wpex-mt-' . $items_spacing . ' wpex-hidden">';
			if ( function_exists( 'totaltheme_get_loading_icon' ) ) {
				$loader_icon = ! empty( $atts['loader_icon'] ) ? sanitize_text_field( $atts['loader_icon'] ) : 'wordpress';
				$output .= totaltheme_get_loading_icon( $loader_icon, 20 );
			}
		$output .= '</div>';

		// Notices.
		$output .= '<div class="vcex-contact-form__notice wpex-hidden wpex-alert wpex-mt-' . $items_spacing . ' wpex-mb-0"></div>';

	$output .= '</form>';

	$output .= '<div class="vcex-contact-form__overlay wpex-hidden"></div>';

$output .= '</div>';

echo $output; // @codingStandardsIgnoreLine
