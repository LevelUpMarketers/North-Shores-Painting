<?php

defined( 'ABSPATH' ) || exit;

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
$html = '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-10 wpex' . $bk . '-gap-25">';

	$html .= $this->get_media( [
		'class'          => "wpex{$bk}-w-50 wpex{$bk}-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => [
			'class'       => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		],
	] );

	$html .= '<div class="wpex-card-details wpex' . $bk . '-flex-grow">';

		$html .= '<div class="wpex-card-header wpex-flex wpex-flex-wrap wpex-mb-10 wpex-text-xs wpex-uppercase wpex-font-medium">';

			$html .= $this->get_primary_term( [
				'link'       => true,
				'class'      => 'wpex-inline-block',
				'after'      => '<span class="wpex-mx-5">&middot;</span>',
				'term_class' => 'wpex-inherit-color wpex-no-underline wpex-inline-block wpex-border-0 wpex-border-b-2 wpex-border-solid wpex-border-gray-400 wpex-hover-border-accent wpex-hover-text-accent',
			] );

			$html .= $this->get_date( [
				'class' => 'wpex-inline-block',
			] );

		$html .= '</div>';

		$html .= $this->get_title( [
			'link'  => true,
			'class' => 'wpex-heading wpex-text-xl wpex-font-bold wpex-mb-5',
		] );

		$html .= $this->get_excerpt();

	$html .= '</div>';

$html .= '</div>';

return $html;
