<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Inner
$html .= '<div class="wpex-card-inner wpex-flex">';

	// Number
	$html .= $this->get_number( array(
		'class' => 'wpex-flex-shrink-0 wpex-mr-20 wpex-text-accent wpex-text-3xl wpex-font-bold',
		'suffix' => '.',
	) );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex-grow">';

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-lg',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mt-5 wpex-text-3',
			'length' => 10,
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;