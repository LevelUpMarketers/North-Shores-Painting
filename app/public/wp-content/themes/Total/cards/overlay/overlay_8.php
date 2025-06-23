<?php

defined( 'ABSPATH' ) || exit;

$this->thumbnail_overlay_style = false;
$this->allowed_media = [ 'thumbnail' ];

$has_link = $this->has_link();

$html = '';

if ( $has_link ) {
	$html .= $this->get_link_open( [
		'class' => 'wpex-card-inner wpex-relative wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-no-underline wpex-image-hover-parent',
		'attributes' => array(
			'aria-label' => get_the_title( $this->post_id ),
		),
	] );
} else {
	$html .= '<div class="wpex-card-inner wpex-relative wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-image-hover-parent">';
}

	$html .= $this->get_thumbnail( [
		'link' => false,
		'class' => 'wpex-flex wpex-h-100',
		'image_class' => 'wpex-h-100 wpex-w-100 wpex-object-cover',
	] );

	$html .= '<span class="wpex-card-backdrop wpex-absolute wpex-inset-0" style="background-image:linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,.15) 60%, rgba(0,0,0,.85) 100%);"></span>';

	$html .= '<div class="wpex-card-details wpex-absolute wpex-bottom-0 wpex-inset-x-0 wpex-text-white wpex-p-25">';

		$html .= $this->get_primary_term( [
			'class' => 'wpex-text-sm wpex-bold',
			'link' => false,
		] );

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-current wpex-text-lg wpex-bold wpex-mt-10 wpex-mb-20',
			'link' => false,
		] );

		$html .= $this->get_more_link( [
			'class' => 'theme-button wpex-text-xs',
			'link' => false,
		] );

	$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
