<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-items-center wpex-gap-15">';

	// Thumbnail
	$thumbnail_args = array(
		'class' => 'wpex-flex-shrink-0 wpex-w-20 wpex-p-3',
		'image_class' => 'wpex-w-100 wpex-rounded-full',
	);
	if ( empty( $this->args['media_width'] ) ) {
		$thumbnail_args['css'] = 'max-width:50px;';
	}
	$html .= $this->get_thumbnail( $thumbnail_args );

	// Details.
	$html .= '<div class="wpex-card-details wpex-flex-grow">';

		// Title.
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-lg wpex-font-medium wpex-m-0',
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;