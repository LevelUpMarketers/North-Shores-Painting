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

// Begin card output.
$html .= '<div class="wpex-card-inner wpex-surface-2 ' . $flex_class . ' ' . $flex_row_class . '">';

	// Media
	$html .= $this->get_media( [
		'class' => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => [
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		],
	] );

	// Details
	$html .= '<div class="wpex-card-details wpex-p-30 wpex-flex wpex-flex-col wpex-flex-grow wpex-last-mb-0">';

		// Date
		$html .= $this->get_date( [
			'class' => 'wpex-mb-10 wpex-text-3',
			'type' => 'published',
			] );

		// Title
		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-child-inherit-color wpex-text-2xl wpex-hover-underline',
			] );

		// Excerpt
		$html .= $this->get_excerpt( [
			'class' => 'wpex-mt-10 wpex-mb-20',
		] );

		// Terms
		$html .= $this->get_terms_list( [
			'class' => 'wpex-flex wpex-flex-wrap wpex-gap-10 wpex-mt-auto',
			'term_class' => 'wpex-text-current wpex-hover-text-1 wpex-underline wpex-decoration-current',
			'term_prefix' => '#',
			'has_term_background_color' => false,
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
