<?php

use function Just_Events\get_default_time_format;
use function Just_Events\get_event_formatted_time;

/**
 * vcex_just_events_date shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.8
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Just_Events\Plugin' ) ) {
	return;
}

$time_args = [];

if ( ! empty( $atts['format'] ) ) {
	$time_args['format'] = $atts['format'];
}

if ( ! empty( $atts['prefix'] ) ) {
	$time_args['prefix'] = '<span class="wpex-bold">' . vcex_parse_text( $atts['prefix'] ) . '</span>';
}

if ( ! empty( $atts['start_end'] ) ) {
	$time_args['start_end'] = $atts['start_end'];
}

if ( 'just_event' !== get_post_type() && (bool) vcex_get_template_edit_mode() ) {
	$date = (string) \wp_date( $time_args['format'] ?? get_default_time_format(), current_time( 'timestamp' ) );
	if ( isset( $time_args['prefix'] ) ) {
		$date = $time_args['prefix'] . ' ' . $date;
	}
} else {
	$date = get_event_formatted_time( get_the_ID(), $time_args );
}

if ( ! $date ) {
	$date = \esc_html__( 'Event date undefined', 'just-events' );
}

// Wrap class.
$wrap_class = [
	'vcex-just-events-time',
	'vcex-module',
];

if ( ! empty( $atts['align'] ) ) {
	$wrap_class[] = vcex_parse_align_class( $atts['align'] );
}

// Unset params to prevent extra classes from being added.
unset( $atts['padding'] );

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_just_events_date' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_just_events_date', $atts );

$output = '<div class="' . esc_attr( trim( $wrap_class ) ) . '"' . vcex_get_unique_id( $atts ) . '>';
	$output .= wp_kses_post( $date );
$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
