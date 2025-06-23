<?php

defined( 'ABSPATH' ) || exit;

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
$html = '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-20 wpex' . $bk . '-gap-40 wpex-items-center">';

	// Media
	$html .= $this->get_media( array(
		'class' => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => array(
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		),
	) );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-last-mb-0">';

		// Position
		$html .= $this->get_element( array(
			'content' => wpex_get_staff_member_position(),
			'class' => 'wpex-card-staff-member-position wpex-text-sm wpex-uppercase wpex-mb-10',
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-2xl',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mt-20',
			//'length' => '-1',
		) );

		// Social Links
		$html .= $this->get_element( array(
			'class' => 'wpex-card-staff-social wpex-mt-20 wpex-child-inherit-color',
			'content' => wpex_get_staff_social( array(
				'format'      => 'flex',
				'labels_only' => true,
				'margin_top'  => 0,
				'separator'   => '&#8212;',
				'spacing'     => '10',
			) ),
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;
