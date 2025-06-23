<?php

/**
 * vcex_searchbar shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Define main vars.
$output       = '';
$placeholder  = ! empty( $atts['placeholder'] ) ? sanitize_text_field( $atts['placeholder'] ) : esc_html__( 'Keywords...', 'total-theme-core' );
$button_text  = ! empty( $atts['button_text'] ) ? sanitize_text_field( $atts['button_text'] ) : esc_html__( 'Search', 'total-theme-core' );
$breakpoint   = ! empty( $atts['breakpoint'] ) ? sanitize_text_field( $atts['breakpoint'] ) : 'sm';
$gap          = ! empty( $atts['gap'] ) ? sanitize_text_field( $atts['gap']  ) : '15';
$input_name   = ! empty( $atts['input_name'] ) ? sanitize_text_field( $atts['input_name'] ) : 's';
$input_width  = ! empty( $atts['input_width'] ) ? sanitize_text_field( $atts['input_width'] ) : '';
$button_width = ! empty( $atts['button_width'] ) ? sanitize_text_field( $atts['button_width'] ) : '';
$has_button   = vcex_validate_att_boolean( 'has_button', $atts, true );
$has_clear    = vcex_validate_att_boolean( 'has_clear', $atts );
$mobile_stack = vcex_validate_att_boolean( 'fullwidth_mobile', $atts );
$auto_fill    = vcex_validate_att_boolean( 'auto_fill', $atts );
$legacy_typo  = vcex_has_classic_styles();

// Set gap to 0 if input with is 100% - this is a fallback for pre 1.4.5
if ( $has_button && '100%' === $input_width && empty( $atts['gap'] ) ) {
	$gap = '0';
}

// Autofocus.
$autofocus = vcex_validate_att_boolean( 'autofocus', $atts ) ? ' autofocus' : '';

// Inline Styles.
$widths_css = [];
if ( $input_width ) {
	$input_css = vcex_inline_style( [
		'width' => $input_width,
	], false );
	$widths_css[] = '.vcex-searchbar-input-wrap{' . $input_css . '}';
}

if ( $button_width ) {
	$button_css = vcex_inline_style( [
		'width' => $button_width,
	], false );
	$widths_css[] = '.vcex-searchbar-button{' . $button_css . '}';
}

if ( $widths_css ) {
	$unique_classname = vcex_element_unique_classname();
	foreach ( $widths_css as $css_k => $css_v ) {
		$widths_css[$css_k] = ".$unique_classname $css_v";
	}
	$output .= '<style>';
		if ( $mobile_stack ) {
			switch ( $breakpoint ) {
				case 'sm';
					$breakpoint_px = '640';
					break;
				case 'md';
					$breakpoint_px = '768';
					break;
				case 'lg';
					$breakpoint_px = '1024';
					break;
				case 'xl';
					$breakpoint_px = '1280';
					break;
			}
			$output .= "@media only screen and (min-width: {$breakpoint_px}px) {";
		}
		$output .= esc_attr( implode( '', $widths_css ) );
		if ( $mobile_stack ) {
			$output .= '}';
		}
	$output .= '</style>';
}

// Wrap Classes.
$wrap_classes = [
	'vcex-searchbar',
	'vcex-module',
];

if ( $has_button && $mobile_stack ) {
	$wrap_classes[] = 'vcex-fullwidth-mobile';
}

if ( $has_clear ) {
	$wrap_classes[] = 'vcex-searchbar--has-clear';
}

if ( $legacy_typo ) {
	$wrap_classes[] = 'wpex-text-lg';
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_classes[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_classes[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['wrap_width'] ) ) {
	$wrap_classes[] = 'wpex-max-w-100';
	if ( ! empty( $atts['wrap_float'] ) ) {
		$wrap_classes[] = vcex_parse_align_class( $atts['wrap_float'] );
	}
}

if ( ! empty( $atts['classes'] ) ) {
	$wrap_classes[] = vcex_get_extra_class( $atts['classes'] );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_classes[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( isset( $unique_classname ) ) {
	$wrap_classes[] = $unique_classname;
}

$wrap_attrs = [
	'id'    => $atts['unique_id'] ?? null,
	'class' => vcex_parse_shortcode_classes( $wrap_classes, 'vcex_searchbar', $atts ),
];

// Begin output.
$wrap_attrs_string = vcex_parse_html_attributes( $wrap_attrs );
$output .= "<div{$wrap_attrs_string}>";

	$form_class = [
		'vcex-searchbar-form',
	];

	if ( '100%' === $input_width && '100%' === $button_width ) {
		$form_class[] = 'wpex-flex wpex-flex-col';
	} else {
		if ( $has_button && $mobile_stack ) {
			$form_class[] = 'wpex-flex wpex-flex-col wpex-' . $breakpoint . '-flex-row wpex-' . $breakpoint . '-justify-between';
		} else {
			$form_class[] = $has_button ? 'wpex-flex wpex-justify-between' : 'wpex-flex';
		}
	}

	if ( $has_button && $gap ) {
		$form_class[] = vcex_parse_gap_class( $gap );
	}

	$form_attrs = [
		'class'  => $form_class,
		'action' => esc_url( ! empty( $atts['action'] ) ? vcex_parse_text( $atts['action'] ) : home_url( '/' ) ),
		'method' => 'get',
	];

	if ( ! empty( $atts['aria_label'] ) ) {
		$form_attrs['aria-label'] = $atts['aria_label'];
	}

	if ( vcex_validate_att_boolean( 'role_landmark', $atts ) ) {
		$form_attrs['role'] = 'search';
	}

	$form_attrs_string = vcex_parse_html_attributes( $form_attrs );

	$output .= "<form{$form_attrs_string}>";

		$input_wrap_class = [
			'vcex-searchbar-input-wrap',
		];

		if ( ! $input_width ) {
			$input_wrap_class[] = 'wpex-flex-grow';
		}

		if ( $has_clear ) {
			$input_wrap_class[] = 'wpex-relative';
		}

		$output .= '<div class="' . esc_attr( implode( ' ', $input_wrap_class ) ) . '">';

			$input_id = uniqid( 'vcex-searchbar-input-' );

			$output .= '<label for="' . esc_attr( $input_id  ) . '" class="screen-reader-text">' . esc_html( $placeholder ) . '</label>';

			// Input classes.
			$input_class = [
				'vcex-searchbar-input',
				'wpex-h-100',
				'wpex-w-100',
				'wpex-inherit-tracking',
				'wpex-inherit-text-transform',
			];

			if ( ! empty( $atts['placeholder_color'] ) ) {
				$input_class[] = 'wpex-placeholder-opacity-100';
			}

			if ( $legacy_typo ) {
				$input_class[] = 'wpex-text-1em';
				$input_class[] = 'wpex-p-10';
			}

			if ( $has_clear ) {
				$input_class[] = 'wpex-pr-30';
			}

			if ( ! empty( $atts['css'] ) ) {
				$input_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
			}

			// Input output.
			$input_val = '';
			if ( $auto_fill && ( is_search() || isset( $_GET[ $input_name ] ) ) ) {
				$input_val = ( 's' === $input_name ) ? get_search_query( false ) : $_GET[ $input_name ];
			}
			$output .= '<input id="' . esc_attr( $input_id ) . '" value="' . esc_attr( \strip_shortcodes( \sanitize_text_field( $input_val ) ) ) . '" type="search" class="' . esc_attr( implode( ' ', $input_class ) ) . '" name="' . esc_attr( $input_name ) . '" placeholder="' . esc_attr( $placeholder ) . '" required' . $autofocus . '>';

			if ( ! empty( $atts['advanced_query'] ) ) :

				// Sanitize.
				$advanced_query = trim( $atts['advanced_query'] );
				$advanced_query = html_entity_decode( $advanced_query );

				// Convert to array.
				parse_str( $advanced_query, $advanced_query_array );

				// If array is valid loop through params.
				if ( $advanced_query_array ) :

					foreach ( $advanced_query_array as $key => $val ) :

						switch ( $val ) {
							case 'current_term':
								if ( is_tax() ) {
									$tax_obj = get_queried_object();
									if ( is_object( $tax_obj ) && ! empty( $tax_obj->taxonomy ) ) {
										$val = $tax_obj->slug;
									}
								}
								break;
							case 'current_author':
								if ( is_author() ) {
									$val = get_the_author_meta( 'ID' );
								}
								break;
						}

						if ( 'current_term' === $val || 'current_author' === $val ) {
							continue;
						}

						$output .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';

					endforeach;

				endif;

			endif;

			// Clear (x) button
			if ( $has_clear ) {
				wp_enqueue_script( 'vcex-searchbar-clear' );
				$clear_reload = vcex_validate_att_boolean( 'clear_reload', $atts ) ? ' data-vcex-reload="1"' : '';
				$output .= '<button class="vcex-searchbar-clear vcex-searchbar-clear--hidden wpex-unstyled-button wpex-invisible wpex-opacity-0 wpex-absolute wpex-right-0 wpex-top-50 -wpex-translate-y-50 wpex-flex wpex-items-center wpex-justify-center wpex-mr-15" aria-label="' . esc_html__( 'Clear', 'total-theme-core' ) . '" type="button"' . $clear_reload . '><svg class="wpex-focus-not-visible-outline-0" focusable="false" xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 24 24" width="20" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg></button>';
			}

		$output .= '</div>';

		/*
		 * Button
		 */
		if ( $has_button ) {

			$button_class = [
				'vcex-searchbar-button',
				'theme-button',
			];

			if ( ! $button_width ) {
				if ( $mobile_stack ) {
					$button_class[] = "wpex-{$breakpoint}-w-25";
				} else {
					$button_class[] = 'wpex-w-25';
				}
			}

			if ( $legacy_typo ) {
				if ( '100%' === $input_width && '100%' === $button_width ) {
					$button_class[] = 'wpex-py-15';
				} elseif ( $mobile_stack ) {
					$button_class[] = "wpex-py-15 wpex-{$breakpoint}-py-0";
				} else {
					$button_class[] = 'wpex-py-0';
				}
			}

			$output .= '<button class="' . esc_attr( implode( ' ', $button_class ) ) . '" type="submit">';
				$output .= vcex_parse_text_safe( $button_text );
			$output .= '</button>';

		} // end has_button check.

	$output .= '</form>';

$output .= '</div>';

echo $output; // @codingStandardsIgnoreLine
