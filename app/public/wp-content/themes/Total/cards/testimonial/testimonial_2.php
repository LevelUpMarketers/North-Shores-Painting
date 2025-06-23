<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-header wpex-flex wpex-items-center">';

	$html .= $this->get_thumbnail( [
		'link'        => false,
		'class'       => 'wpex-card-thumbnail-sm wpex-shrink-0 wpex-rounded-full wpex-mr-15',
		'image_class' => 'wpex-rounded-full',
	] );

	$html .= '<div class="wpex-card-header-aside">';

		$html .= $this->get_title( [
			'link'  => false,
			'class' => totaltheme_has_classic_styles() ? 'wpex-heading wpex-text-md wpex-mb-5' : 'wpex-heading wpex-text-lg wpex-mb-5',
		] );

		$html .= $this->get_star_rating( [
			'class' => 'wpex-text-sm',
		] );

	$html .= '</div>';

$html .= '</div>';

$html .= $this->get_excerpt( [
	'class' => 'wpex-mt-15',
] );

return $html;
