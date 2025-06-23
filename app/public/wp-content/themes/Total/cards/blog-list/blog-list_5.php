<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Get card breakpoint.
$bk = $this->get_breakpoint();

// Set flex row class.
if ( $this->has_flex_direction_reverse() ) {
	$flex_row_class = 'wpex-flex-row-reverse';
} else {
	$flex_row_class = 'wpex-flex-row';
}

// Begin card output.
$html .= '<div class="wpex-card-inner wpex-flex ' . $flex_row_class . ' wpex-gap-20 wpex-items-center">';

	// Media
	$html .= $this->get_media( array(
		'class' => 'wpex-w-33 wpex-flex-shrink-0 wpex-self-stretch',
		'thumbnail_args' => array(
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		),
	) );

	// Title
	$html .= $this->get_title( array(
		'class' => 'wpex-heading wpex-text-lg wpex-flex-grow',
	) );

$html .= '</div>';

return $html;