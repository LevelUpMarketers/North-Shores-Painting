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
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-surface-1 wpex-border wpex-border-solid wpex-border-gray-200 wpex-rounded-sm wpex-overflow-hidden">';

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
			'class' => 'wpex-heading wpex-text-xl wpex-mb-15',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mb-25',
			'length' => 30,
		) );

		$html .= '<div class="wpex-card-footer wpex-flex wpex-items-center wpex-mt-25">';

			// Avatar
			$html .= $this->get_avatar( array(
				'size' => 35,
				'class' => 'wpex-flex-shrink-0 wpex-mr-15',
				'image_class' => 'wpex-rounded-full wpex-align-middle',
			) );

			$html .= '<div class="wpex-card-meta wpex-flex-grow wpex-leading-snug wpex-text-sm">';

				// Author
				$html .= $this->get_author( array(
					'class' => 'wpex-font-medium wpex-text-1 wpex-capitalize',
					'link' => false,
				) );

				// Date
				$html .= $this->get_date( array(
					'type' => 'published',
				) );

			$html .= '</div>';

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;