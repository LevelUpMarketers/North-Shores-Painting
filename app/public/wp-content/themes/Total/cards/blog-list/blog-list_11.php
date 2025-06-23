<?php

defined( 'ABSPATH' ) || exit;

$html = '';

// Set flex row class.
if ( $this->has_flex_direction_reverse() ) {
	$flex_row_class = 'wpex-flex-row-reverse';
} else {
	$flex_row_class = 'wpex-flex-row';
}

// Begin card output.
$html .= '<div class="wpex-card-inner wpex-flex wpex-items-center ' . $flex_row_class . ' wpex-gap-20">';

	$html .= $this->get_media( [
		'class'          => 'wpex-w-33 wpex-flex-shrink-0 wpex-self-stretch',
		'thumbnail_args' => [
			'class'       => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		],
	] );

	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-last-mb-0">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-lg wpex-mb-5',
		] );

		$html .= $this->get_author( [
			'class'      => 'wpex-text-3 wpex-child-inherit-color',
			'link_class' => 'wpex-no-underline',
			'prefix'     => esc_html( 'By', 'total' ) . ' ',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
