<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Get card breakpoint.
$bk = $this->get_breakpoint();

if ( $bk ) {
	$bk = "-{$bk}";
	$flex_class = 'wpex-flex wpex-flex-col wpex-flex-grow ';
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
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-surface-1 wpex-shadow wpex-rounded-sm">';

	// Media
	$html .= $this->get_media( array(
		'class' => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => array(
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		),
	) );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-p-25 wpex-last-mb-0">';

		// Terms
		$html .= $this->get_terms_list( array(
			'class' => 'wpex-font-semibold wpex-leading-normal wpex-mb-15 wpex-last-mr-0',
			'term_class' => 'wpex-inline-block wpex-bg-accent wpex-hover-opacity-80 wpex-no-underline wpex-mr-10 wpex-mb-10 wpex-px-10 wpex-py-5 wpex-rounded-full wpex-text-xs',
			'has_term_background_color' => true,
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-lg wpex-font-bold wpex-mb-15',
			'link_class' => 'wpex-inherit-color-important',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mb-30',
		) );

		// Date
		$html .= $this->get_date( array(
			'type' => 'published',
			'class' => 'wpex-text-xs wpex-uppercase wpex-font-semibold',
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;