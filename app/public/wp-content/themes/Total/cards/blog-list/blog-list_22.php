<?php

defined( 'ABSPATH' ) || exit;

$has_link = $this->has_link_wrap();
$bk       = $this->get_breakpoint();

$html = '';

if ( $has_link ) {
	$html .= $this->get_link_open( [
		'class' => 'wpex-no-underline wpex-inherit-color',
		'attributes' => [
			'aria-label' => get_the_title( $this->post_id ),
		],
	] );
}

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
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-overflow-hidden wpex-rounded-md wpex-border wpex-border-solid wpex-border-main wpex-surface-1 wpex-no-underline wpex-inherit-color">';

	$html .= $this->get_media( [
		'class' => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'link' => ! $has_link,
		'thumbnail_args' => array(
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		),
	] );

	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-p-30 wpex-last-mb-0">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-child-inherit-color wpex-text-xl',
			'link' => ! $has_link,
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-mt-15',
		] );

	$html .= '</div>';

$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
}

return $html;
