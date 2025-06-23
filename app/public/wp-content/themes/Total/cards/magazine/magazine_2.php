<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= $this->get_media( [
	'class'       => 'wpex-mb-20 wpex-rounded-lg',
	'image_class' => 'wpex-rounded-lg',
] );

$html .= '<div class="wpex-card-details">';

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

return $html;
