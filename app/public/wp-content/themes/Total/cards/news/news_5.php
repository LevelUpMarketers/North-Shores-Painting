<?php

defined( 'ABSPATH' ) || exit;

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
$html = '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-20">';

	$html .= $this->get_thumbnail( [
		'class'       => "wpex{$bk}-w-20 wpex-flex-shrink-0",
		'image_class' => 'wpex-w-100',
	] );

	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow">';

		$html .= $this->get_primary_term( [
			'class'          => 'wpex-text-xs wpex-font-bold wpex-uppercase wpex-mb-5',
			'term_class'     => 'wpex-hover-underline',
			'has_term_color' => true,
		] );

		$html .= $this->get_title( [
			'link'       => true,
			'class'      => 'wpex-heading wpex-text-lg wpex-font-bold',
			'link_class' => 'wpex-inherit-color-important',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
