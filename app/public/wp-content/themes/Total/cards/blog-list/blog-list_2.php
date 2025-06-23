<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Get card breakpoint.
$bk = $this->get_breakpoint();

if ( $bk ) {
	$bk = "-{$bk}";
	$flex_class = 'wpex-flex wpex-flex-col';
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
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-20 wpex' . $bk . '-gap-30">';

	// Media
	$html .= $this->get_media( array(
		'class' => 'wpex' . $bk . '-w-33 wpex-flex-shrink-0 wpex-rounded-sm wpex-self-stretch',
		'thumbnail_args' => array(
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover wpex-rounded-sm',
		),
	) );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-last-mb-0">';

		// Terms
		$html .= $this->get_terms_list( array(
			'class' => 'wpex-mb-10 wpex-text-sm wpex-font-semibold',
			'term_class' => 'wpex-hover-underline',
			'separator' => ' &middot; ',
			'has_term_color' => true,
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-xl wpex-mb-15',
			'link_class' => 'wpex-inherit-color-important',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mb-15',
		) );

		// Footer
		$html .= '<div class="wpex-card-footer wpex-flex wpex-items-center">';

			// Avatar
			$html .= $this->get_avatar( array(
				'size'        => 40,
				'class'       => 'wpex-flex-shrink-0 wpex-mr-15',
				'image_class' => 'wpex-rounded-full wpex-align-middle',
			) );

			// Footer aside
			$html .= '<div class="wpex-card-meta wpex-flex-grow wpex-leading-snug wpex-text-sm">';

				// Author
				$html .= $this->get_author( array(
					'class' => 'wpex-text-1 wpex-font-bold wpex-capitalize',
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