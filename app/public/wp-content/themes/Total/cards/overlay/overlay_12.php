<?php

defined( 'ABSPATH' ) || exit;

$this->thumbnail_overlay_style = false;
$this->allowed_media = [ 'thumbnail' ];

$has_link = $this->has_link();
$bk = $this->get_breakpoint();

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
		'class' => 'wpex-flex wpex-h-100 wpex-rounded-sm wpex-overflow-hidden',
		'image_class' => 'wpex-h-100 wpex-w-100 wpex-object-cover',
	] );

	$html .= '<div class="wpex-card-details wpex-' . $bk . '-absolute wpex-bottom-0 wpex-' . $bk . '-inset-x-0 wpex-surface-1 wpex-text-1 wpex-mt-25 wpex-' . $bk . '-m-25 wpex-' . $bk . '-p-25 wpex-' . $bk . '-shadow-xl wpex-rounded-sm">';


		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-current wpex-text-2xl',
			'link' => false,
		] );

		$html .= $this->get_excerpt( [
			'link' => false,
			'class' => 'wpex-mt-20',
		] );

	$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
