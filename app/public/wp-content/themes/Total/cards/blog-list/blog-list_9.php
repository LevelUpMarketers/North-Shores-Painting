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
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-surface-1 wpex-border-2 wpex-border-solid wpex-border-gray-200">';

	// Media
	$html .= $this->get_media( array(
		'class' => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => array(
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		),
	) );

	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-p-25 wpex-last-mb-0">';

		// Title
		$html .= $this->get_title( array(
			'link' => true,
			'class' => 'wpex-heading wpex-text-lg wpex-font-bold wpex-mb-15',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mb-15',
		) );

		// More Link
		$html .= $this->get_more_link( array(
			'class' => 'wpex-font-semibold',
			'text' => esc_html__( 'Continue reading', 'total' ),
			'link_class' => 'wpex-hover-underline',
			'suffix' => ' &rarr;',
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;