<?php

defined( 'ABSPATH' ) || exit;

$has_link = $this->has_link_wrap();

$html = '';

if ( $has_link ) {
	$html .= $this->get_link_open( array(
		'class' => 'wpex-no-underline wpex-inherit-color',
		'attributes' => array(
			'aria-label' => get_the_title( $this->post_id ),
		),
	) );
}

// Get card breakpoint.
$bk = $this->get_breakpoint();

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

// Begin card output.
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-20 wpex' . $bk . '-gap-35">';

	// Media
	$html .= $this->get_media( array(
		'class' => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'link' => ! $has_link,
	) );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-last-mb-0">';

		// Date
		$html .= $this->get_date( array(
			'class' => 'wpex-mb-5 wpex-text-3 wpex-text-xs',
			'type' => 'published',
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-2xl',
			'link' => ! $has_link,
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mt-10',
		) );

	$html .= '</div>';

$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
}

return $html;
