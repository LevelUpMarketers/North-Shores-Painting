<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-lg',
] );

$html .= '<div class="wpex-card-meta wpex-mt-5 wpex-text-3 wpex-child-inherit-color wpex-last-mr-0">';

	$html .= $this->get_author( [
		'html_tag' => 'span',
		'prefix' => esc_html( 'by', 'total' ) . ' ',
		'class' => 'wpex-inline-block wpex-mr-5',
		'link_class' => 'wpex-underline wpex-decoration-current',
	] );

	$html .= $this->get_date( [
		'html_tag' => 'span',
		'prefix' => esc_html( 'on', 'total' ) . ' ',
		'class' => 'wpex-inline-block wpex-mr-5',
	] );

$html .= '</div>';

return $html;
