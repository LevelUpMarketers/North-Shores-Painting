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
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-20 wpex' . $bk . '-gap-35">';

	// Media
	$html .= $this->get_media( array(
		'class' => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
	) );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-last-mb-0">';

		// First Term
		$html .= $this->get_primary_term( array(
			'class' => 'wpex-text-accent wpex-mb-5 wpex-text-xs wpex-uppercase wpex-tracking-wide',
			'term_class' => 'wpex-inherit-color wpex-no-underline',
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-xl wpex-mb-15',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'length' => 30,
		) );

		// Footer
		$html .= '<div class="wpex-card-footer wpex-mt-20 wpex-flex wpex-items-center">';

			// Avatar
			$html .= $this->get_avatar( array(
				'size' => 35,
				'class' => 'wpex-flex-shrink-0 wpex-mr-15',
				'image_class' => 'wpex-rounded-full wpex-align-middle',
			) );

			$html .= '<div class="wpex-card-meta wpex-flex-grow wpex-leading-snug wpex-text-sm">';

				// Author
				$html .= $this->get_author( array(
					'class' => 'wpex-font-semibold wpex-text-1 wpex-capitalize',
					'link' => false,
				) );

				// Post meta byline
				$html .= '<div class="wpex-flex wpex-flex-wrap wpex-gap-5 wpex-text-3">';

					// Date
					$html .= $this->get_date( array(
						'type' => 'published',
					) );

					// Read Time
					$html .= $this->get_estimated_read_time( array(
						'before' => '<span> &bull; </span>',
						'minute_text' => esc_html__( '%s min read', 'total' ),
						'second_text' => esc_html__( '%s sec read', 'total' ),
					) );

				$html .= '</div>';

			$html .= '</div>';

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;