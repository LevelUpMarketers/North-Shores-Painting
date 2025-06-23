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
$html = '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-25">';

	$html .= $this->get_thumbnail( [
		'class'       => "wpex{$bk}-w-40 wpex-flex-shrink-0",
		'image_class' => 'wpex-w-100',
	] );

	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow">';

		$html .= $this->get_primary_term( [
			'class'          => 'wpex-text-xs wpex-font-bold wpex-uppercase',
			'term_class'     => 'wpex-inherit-color wpex-hover-underline',
			'has_term_color' => true,
		] );

		$html .= $this->get_title( [
			'link'       => true,
			'class'      => 'wpex-heading wpex-text-2xl wpex-font-bold',
			'link_class' => 'wpex-inherit-color-important',
		] );

		$html .= $this->get_excerpt( [
			'class'  => 'wpex-my-15',
			'length' => 20,
		] );

		$html .= $this->get_author( [
			'class'      => 'wpex-text-xs wpex-uppercase wpex-tracking-sm',
			'link_class' => 'wpex-inherit-color wpex-hover-underline',
			'prefix'     => esc_html__( 'By', 'total' ) . ' ',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
