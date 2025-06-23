<?php

defined( 'ABSPATH' ) || exit;

$this->thumbnail_overlay_style = false;
$this->allowed_media = [ 'thumbnail' ];

$has_link = $this->has_link();

$html = '';

if ( $has_link ) {
	$html .= $this->get_link_open( [
		'class' => 'wpex-card-inner wpex-relative wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-no-underline wpex-image-hover-parent wpex-rounded-sm',
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

	$html .= $this->get_title( [
		'class' => 'wpex-heading wpex-absolute wpex-top-0 wpex-left-0 wpex-m-25 wpex-text-lg wpex-text-white',
		'link' => false,
	] );

	$html .= $this->get_more_link( [
		'class' => 'wpex-absolute wpex-right-0 wpex-bottom-0 wpex-mx-25 wpex-mb-20 wpex-inline-flex wpex-items-center wpex-text-white wpex-card-hover-reveal',
		'link' => false,
		'suffix' => '<span class="wpex-card-svg wpex-flex wpex-items-center wpex-ml-5"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 0 24 24" width="20px" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg></span>',
	] );

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
