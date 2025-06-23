<?php

use function Just_Events\get_event_date;
use function Just_Events\is_same_day_event;
use function Just_Events\get_event_timezone;
use function Just_Events\get_current_date_time;

/**
 * Overlay: Just Events Date.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.3
 */

defined( 'ABSPATH' ) || exit;

// Get overlay position.
$position = $args['position'] ?? $position;

// Only used for inside position.
if ( 'inside_link' !== $position ) {
	return;
}

// Just Events Plugin not Active.
if ( ! class_exists( '\Just_Events\Plugin', false ) ) {
	return;
}

// Get event ID.
$post_id = get_the_ID();

if ( 'just_event' !== get_post_type( $post_id ) ) {
	$post_id = get_queried_object_id(); // likely a dynamic template or single image element
}

// Get event data.
$start_date = get_event_date( $post_id, 'start', false );
$end_date   = get_event_date( $post_id, 'end', false );

// Display a dummy date in builder-mode.
if ( ! $start_date && in_array( get_post_type( get_queried_object() ), [ 'wpex_card', 'elementor_library', 'wpex_templates' ] ) ) {
	$start_date = get_current_date_time();
}

// Start date required.
if ( ! $start_date ) {
	return;
}

// Format dates.
$timezone = get_event_timezone( $post_id );
$start_date = new DateTime( $start_date, $timezone );
$start_date = $start_date->format( 'U' );

if ( $end_date ) {
	$end_date = new DateTime( $end_date, $timezone );
	$end_date = $end_date->format( 'U' );
}

// Wrap class.
$wrap_class = 'overlay-just-event-date theme-overlay wpex-absolute wpex-m-10 wpex-surface-1 wpex-text-1 wpex-m-5 wpex-nowrap wpex-leading-none wpex-uppercase wpex-tracking-tight';

if ( ! empty( $args['color_scheme'] ) ) {
	$wrap_class .= ' ' . $args['color_scheme'];
}

$align = $args['align'] ?? 'top_right';

switch( $align ) {
	case 'top_left':
		$wrap_class .= ' wpex-top-0 wpex-left-0';
		break;
	case 'top_right':
		$wrap_class .= ' wpex-top-0 wpex-right-0';
		break;
	case 'bottom_left':
		$wrap_class .= ' wpex-bottom-0 wpex-left-0';
		break;
	case 'bottom_right':
		$wrap_class .= ' wpex-bottom-0 wpex-right-0';
		break;
}

?>

<div class="<?php echo \esc_attr( $wrap_class ); ?>" style="min-width:60px;">
	<div class="overlay-just-event-date__inner wpex-flex wpex-flex-col wpex-items-center wpex-gap-5 wpex-m-5">
		<div class="overlay-just-event-date__month wpex-text-xs"><?php
			echo esc_html( wp_date( 'M', $start_date ) );
		?></div>
		<div class="overlay-just-event-date__day wpex-text-2xl"><?php
			echo esc_html( wp_date( 'j', $start_date ) );
		?></div>
		<?php if ( $end_date
			&& $start_date !== $end_date
			&& ! is_same_day_event( $post_id )
			&& $end_date_formatted = wp_date( 'M j', $end_date )
		) { ?>
			<div class="overlay-just-event-date__end wpex-text-xs wpex-border-t wpex-border-solid wpex-border-surface-4 wpex-pt-5"><?php
				printf(
					esc_html_x( 'To %s', 'Adverb: event date overlay end date prefix', 'total' ),
					esc_html( $end_date_formatted )
				);
			?></div>
		<?php } ?>
	</div>
</div>
