<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-border-2 wpex-border-solid wpex-border-gray-200">';

	$html .= $this->get_media( [
		'image_class' => 'wpex-w-100',
	] );

	$html .= '<div class="wpex-card-details wpex-p-20">';

		if ( totaltheme_has_classic_styles() ) {
			$title_class = 'wpex-heading wpex-text-md';
		} else {
			$title_class = 'wpex-heading wpex-text-lg';
		}

		$html .= $this->get_title( [
			'class' => $title_class,
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-mt-10',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
