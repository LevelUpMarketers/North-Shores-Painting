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
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-48">';

	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex' . $bk . '-justify-between wpex-gap-15">';

		$html .= '<div class="wpex-card-meta">';

			$html .= $this->get_element( [
				'content' => wpex_get_testimonial_author(),
				'class'   => 'wpex-card-testimonial-author wpex-font-semibold',
			] );

		$html .= '</div>';

		$html .= $this->get_excerpt( [
			'length' => '-1',
			'class'  => 'wpex-text-1 wpex-font-semibold wpex-text-2xl',
		] );

		$html .= $this->get_element( [
			'content' => wpex_get_testimonial_company(),
			'class'   => 'wpex-card-testimonial-company wpex-text-sm wpex-text-3',
		] );

	$html .= '</div>';

	$html .= $this->get_thumbnail( [
		'class' => "wpex-flex-shrink-0 wpex{$bk}-w-50",
		'link'  => false,
	] );

$html .= '</div>';

return $html;
