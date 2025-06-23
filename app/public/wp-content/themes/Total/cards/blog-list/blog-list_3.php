<?php
defined( 'ABSPATH' ) || exit;

$html = '';

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
$html .= '<div class="wpex-card-inner wpex-surface-1 ' . $flex_class . ' ' . $flex_row_class . ' wpex' . $bk . '-items-center wpex-rounded wpex-shadow-lg wpex-overflow-hidden">';

	// Media
	$html .= $this->get_media( array(
		'class' => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => array(
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		),
	) );

	// Details.
	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-p-30 wpex-last-mb-0">';

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-lg wpex-mb-5',
			'link_class' => 'wpex-inherit-color-important',
		) );

		// Terms
		$html .= $this->get_terms_list( array(
			'class' => 'wpex-mb-15 wpex-text-xs wpex-font-semibold wpex-uppercase',
			'term_class' => 'wpex-hover-underline',
			'separator' => ' &middot; ',
			'has_term_color' => true,
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => '',
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;