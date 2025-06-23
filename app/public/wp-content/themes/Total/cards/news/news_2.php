<?php
defined( 'ABSPATH' ) || exit;

$html = '';

if ( empty( $this->args['breakpoint'] ) ) {
	$this->args['breakpoint'] = 'sm';
}

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
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-30">';

	// Thumbnail
	$html .= $this->get_thumbnail( array(
		'class' => 'wpex' . $bk . '-w-25 wpex-flex-shrink-0',
		'image_class' => 'wpex-w-100 wpex-rounded-sm'
	) );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex-grow">';

		// Author wrap
		$html .= '<div class="wpex-card-author-wrap wpex-flex wpex-items-center wpex-gap-10 wpex-mb-15">';

			// Avatar
			$html .= $this->get_avatar( array(
				'size' => 30,
				'class' => 'wpex-flex-shrink-0',
				'image_class' => 'wpex-rounded-full wpex-align-middle',
			) );

			// Author
			$html .= $this->get_author( array(
				'class' => 'wpex-text-sm',
				'link_class' => 'wpex-inherit-color wpex-hover-underline',
			) );

		$html .= '</div>';

		// Title
		$html .= $this->get_title( array(
			'link' => true,
			'class' => 'wpex-heading wpex-text-xl wpex-font-bold',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-my-15',
		) );

		// Date
		$html .= $this->get_date( array(
			'format' => 'Y.m.d ',
			'class' => 'wpex-opacity-60',
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;