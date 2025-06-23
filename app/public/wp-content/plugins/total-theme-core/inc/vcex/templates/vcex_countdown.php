<?php

/**
 * VCEX Countdown.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$output = '';
$source = $atts['source'] ?? '';
$style = ! empty( $atts['style'] ) ? sanitize_text_field( $atts['style'] ) : 'inline';
$items = ! empty( $atts['items'] ) ? $atts['items'] : [ 'days', 'hours', 'minutes' , 'seconds' ];

if ( is_string( $items ) ) {
	$items = explode( ',', $items );
}

// Get end date data.
switch ( $source ) {
	case 'custom_field':
		if ( ! empty( $atts['custom_field'] ) ) {
			$date = vcex_get_meta_value( $atts['custom_field'], false, false );
		}
		break;
	case 'just_events_start':
	case 'just_events_end':
		$date = get_post_meta( vcex_get_the_ID(), "_{$source}_date", true );
		if ( function_exists( 'Just_Events\get_event_timezone_string' ) ) {
			$atts['timezone'] = Just_Events\get_event_timezone_string();
		}
		break;
	default:
		$end_year  = ! empty( $atts['end_year'] ) ? (int) do_shortcode( $atts['end_year'] ) : date( 'Y' );
		$end_month = ! empty( $atts['end_month'] ) ? (int) $atts['end_month'] : '';
		$end_day   = ! empty( $atts['end_day'] ) ? (int) $atts['end_day'] : '';
		if ( $end_year && $end_month && $end_day ) {
			// Make sure input is not crazy.
			if ( $end_month > 12 ) {
				$end_month = '';
			}
			if ( $end_day > 31 ) {
				$end_day = '';
			}
			$date = "{$end_year}-{$end_month}-{$end_day}";
			$end_time = ! empty( $atts['end_time'] ) ? sanitize_text_field( $atts['end_time'] ) : '00:00';
			if ( $end_time ) {
				$date = " {$date} {$end_time}";
			}
		}
		break;
}

if ( isset( $date ) ) {
	$end_date = date( 'Y-m-d H:i', strtotime( $date ) );
} else {
	$end_year = (int) date( 'Y' ) + 1;
	$end_date = "{$end_year}-12-15 00:00";
}

// Countdown data.
$data = [
	'data-countdown' => esc_attr( $end_date ),
];

if ( in_array( 'days', $items ) ) {
	$data['data-days'] = $atts['days'] ?: esc_attr__( 'Days', 'total-theme-core' );
}
if ( in_array( 'hours', $items ) ) {
	$data['data-hours'] = $atts['hours'] ?: esc_attr__( 'Hours', 'total-theme-core' );
}
if ( in_array( 'minutes', $items ) ) {
	$data['data-minutes'] = $atts['minutes'] ?: esc_attr__( 'Minutes', 'total-theme-core' );
}
if ( in_array( 'seconds', $items ) ) {
	$data['data-seconds'] = $atts['seconds'] ?: esc_attr__( 'Seconds', 'total-theme-core' );
}

if ( ! empty( $atts['timezone'] ) ) {
	$data['data-timezone'] = esc_attr( $atts['timezone'] );
}

// Main classes.
$shortcode_class = [
	'vcex-countdown-wrap',
	'vcex-module',
];

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_countdown' );

if ( $extra_classes ) {
	$shortcode_class = array_merge( $shortcode_class, $extra_classes );
}

// Add to attributes.
$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_countdown', $atts );

// Output.
$output .= '<div class="' . esc_attr( $shortcode_class ) . '">';

	$inner_class = [
		'vcex-countdown',
		"vcex-countdown--{$style}",
	];

	$inner_class_utl = [];

	if ( 'outline' === $style || 'boxed' === $style ) {
		$item_class = [];
		$stack_bk = $atts['stack_breakpoint'] ?? '';
		$inner_class_utl[] = 'wpex-flex';
		if ( $stack_bk ) {
			$inner_class_utl[] = 'wpex-flex-col';
			$inner_class_utl[] = 'wpex-items-center';
			$inner_class_utl[] = 'wpex-' . sanitize_html_class( $stack_bk ) .'-flex-row';
			$inner_class_utl[] = 'wpex-' . sanitize_html_class( $stack_bk ) .'-items-stretch';
		}
		$inner_class_utl[] = 'wpex-flex-wrap';
		$inner_class_utl[] = 'wpex-justify-center';
		$inner_class_utl[] = 'wpex-gap-20';
		if ( empty( $atts['text_align'] ) ) {
			$inner_class_utl[] = 'wpex-text-center';
		}

		$labels_inline = vcex_validate_att_boolean( 'labels_inline', $atts );

		if ( 'outline' === $style || 'boxed' === $style ) {
			$item_class[] = 'wpex-flex';
			if ( $labels_inline ) {
				$item_class[] = 'wpex-flex-row';
				$item_class[] = 'wpex-items-center';
				$item_class[] = 'wpex-gap-5';
			} else {
				$item_class[] = 'wpex-flex-col';
			}
			$item_class[] = 'wpex-justify-center';
		}

		switch ( $style ) {
			case 'outline':
			case 'bordered':
				$item_class[] = 'wpex-bordered';
				break;
			case 'boxed':
				$item_class[] = 'wpex-boxed';
				break;
		}

		if ( vcex_validate_att_boolean( 'stretch_items', $atts ) ) {
			$item_class[] = 'wpex-flex-grow';
			$inner_class[] = 'vcex-countdown--stretch';
		}

		if ( ! empty( $atts['item_shadow'] ) ) {
			$item_class[] = vcex_parse_shadow_class( $atts['item_shadow'] );
		}

		if ( ! empty( $atts['item_width'] ) ) {
			$item_class[] = 'wpex-max-w-100';
		}

		if ( $item_class ) {
			$data['data-item-class'] = implode(' ', array_filter( $item_class ) );
		}
	}

	if ( $inner_class_utl ) {
		$inner_class = array_merge( $inner_class, $inner_class_utl );
	}

	$output .= '<div class="' . esc_attr( implode( ' ', $inner_class ) ) . '"';

		/**
		 * Filters the countdown data attributes.
		 *
		 * @param array $data_attributes
		 */
		$data = (array) apply_filters( 'vcex_countdown_data', $data, $atts );

		foreach ( $data as $name => $value ) {
			$output .= ' ' . $name . '=' . '"' . esc_attr( $value ) . '"';
		}

	$output .= '>';

	$output .='</div>';

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
