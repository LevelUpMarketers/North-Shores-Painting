<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_media( [
	'class' => 'wpex-mb-15',
] );

$html .= '<div class="wpex-card-details wpex-flex wpex-items-center">';

	$html .= $this->get_avatar( [
		'size'        => 32,
		'class'       => 'wpex-flex-shrink-0 wpex-mr-15',
		'image_class' => 'wpex-rounded-full wpex-align-middle',
	] );

	$html .= '<div class="wpex-card-details wpex-flex-grow">';

		if ( totaltheme_has_classic_styles() ) {
			$title_class = 'wpex-heading wpex-text-md';
		} else {
			$title_class = 'wpex-heading wpex-text-lg';
		}

		$html .= $this->get_title( [
			'class' => $title_class,
		] );

		$html .= $this->get_terms_list( [
			'class'      => 'wpex-text-3',
			'separator'  => ' &middot; ',
			'term_class' => 'wpex-inherit-color wpex-hover-underline',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
