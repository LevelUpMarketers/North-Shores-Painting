<?php

defined( 'ABSPATH' ) || exit;

$bk = $this->get_breakpoint();

$html = '';

if ( $bk ) {
	$bk = "-{$bk}";
	$flex_class = 'wpex-flex wpex-flex-col wpex-flex-grow';
} else {
	$flex_class = 'wpex-flex';
}

// Set flex row class.
if ( $this->has_flex_direction_reverse() ) {
	$flex_row_class = "wpex{$bk}-flex-row-reverse";
} else {
	$flex_row_class = "wpex{$bk}-flex-row";
}

// Begin output
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-overflow-hidden wpex-gap-20 wpex' . $bk . '-gap-30">';

	// Image
	$html .= $this->get_media( [
		'class' => "wpex{$bk}-w-40 wpex-flex-shrink-0 wpex-self-stretch",
		'link' => true,
	] );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow">';

		// Title
		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-child-inherit-color wpex-text-xl wpex-mb-5',
			'link' => true,
		] );

		// Date
		if ( str_starts_with( $this->get_var( 'thumbnail_overlay_style' ), 'just-events-date' ) ) {
			if ( function_exists( 'Just_Events\get_event_formatted_time' ) ) {
				$html .= $this->get_element( [
					'class'     => 'wpex-card-time wpex-mb-20',
					'content'   => Just_Events\get_event_formatted_time( $this->post_id ),
				] );
			}
		} else {
			$html .= $this->get_date( [
				'class'     => 'wpex-mb-20',
				'start_end' => 'both',
				'show_time' => false,
				'format'    => 'M j, Y',
			] );
		}

		// Excerpt
		$html .= $this->get_excerpt( [
			'class' => 'wpex-mb-25',
		] );

		// More Button
		$html .= $this->get_more_link( [
			'link_class' => 'theme-button',
			'text' => esc_html__( 'View Event', 'total' ),
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
