<?php

defined( 'ABSPATH' ) || exit;

$this->thumbnail_overlay_style = false;
$this->allowed_media = [ 'thumbnail' ];

$has_link = $this->has_link();

$html = '';

if ( $has_link ) {
	$html .= $this->get_link_open( [
		'class' => 'wpex-card-inner wpex-relative wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-no-underline wpex-image-hover-parent wpex-rounded-sm wpex-card-touch-support',
		'attributes' => array(
			'aria-label' => get_the_title( $this->post_id ),
		),
	] );
} else {
	$html .= '<div class="wpex-card-inner wpex-relative wpex-flex wpex-flex-col wpex-flex-grow wpex-image-hover-parent wpex-rounded-sm">';
}

	$html .= $this->get_thumbnail( [
		'link' => false,
		'class' => 'wpex-flex wpex-h-100',
		'image_class' => 'wpex-h-100 wpex-w-100 wpex-object-cover',
	] );

	$html .= '<span class="wpex-card-backdrop wpex-absolute wpex-inset-0 wpex-bg-black wpex-opacity-30"></span>';

	$html .= '<div class="wpex-card-details wpex-absolute wpex-bottom-0 wpex-inset-0 wpex-p-20 wpex-flex wpex-items-center wpex-justify-center wpex-text-center">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-lg wpex-text-white wpex-card-hover-reveal wpex-card-hover-reveal--up',
			'link' => false,
		] );

	$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
