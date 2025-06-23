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
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex' . $bk . '-items-center wpex-surface-1 wpex-border wpex-border-solid wpex-border-main wpex-surface-1">';

	// Media
	$html .= $this->get_media( array(
		'class' => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => array(
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		),
	) );

	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-p-30 wpex-last-mb-0">';

		// Terms
		$html .= $this->get_terms_list( array(
			'class' => 'wpex-text-accent wpex-child-inherit-color wpex-mb-10 wpex-font-bold wpex-text-xs wpex-uppercase wpex-tracking-wide',
			'term_class' => 'wpex-inline-block wpex-hover-underline',
			'separator' => '<span class="wpex-card-terms-list-sep wpex-inline-block wpex-mx-5">|</span>',
		//	'has_term_color' => true,
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-2xl wpex-font-light wpex-mb-10',
			'link_class' => 'wpex-inherit-color-important',
		) );

		// Author
		$html .= $this->get_author( array(
			'class' => 'wpex-mb-20 wpex-font-semibold wpex-text-3 wpex-child-inherit-color',
			'link_class' => 'wpex-hover-underline',
			'prefix' => esc_html__( 'By', 'total' ) . ' ',
		) );

		// Excerpt
		$html .= $this->get_excerpt();

	$html .= '</div>';

$html .= '</div>';

return $html;