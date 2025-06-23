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
	$flex_row_class = "wpex{$bk}-flex-row";
} else {
	$flex_row_class = "wpex{$bk}-flex-row-reverse";
}

$card_class = "wpex-card-inner {$flex_class} {$flex_row_class} wpex-gap-20 wpex{$bk}-gap-30";

// Begin card output.
$html .= '<div class="' . esc_attr( $card_class ) . '">';

	$html .= $this->get_media( [
		'class'          => "wpex-rounded-lg  wpex{$bk}-text-right wpex{$bk}-w-33 wpex{$bk}-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => [
			'class'       => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover wpex-rounded-lg',
		],
	] );

	$html .= '<div class="wpex-card-details wpex' . $bk . '-flex-grow">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-xl wpex-font-medium wpex-mb-10',
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-mb-15',
		] );

		$html .= $this->get_author( [
			'class'      => 'wpex-font-bold wpex-child-inherit-color',
			'link_class' => 'wpex-inherit-color wpex-hover-underline',
			'prefix'     => esc_html__( 'By', 'total' ) . ' ',
		] );

		$html .= '<div class="wpex-card-meta">';

			$html .= $this->get_date( [
				'class'    => 'wpex-tex-xs wpex-text-3',
				'html_tag' => 'span',
				'suffix'   => '. '
			] );

			$html .= $this->get_time( [
				'class'    => 'wpex-tex-xs wpex-text-3',
				'html_tag' => 'span',
			] );

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;
