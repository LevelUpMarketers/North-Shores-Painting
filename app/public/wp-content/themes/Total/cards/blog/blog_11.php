<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_media( [
	'class' => 'wpex-mb-10',
] );

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-lg wpex-mb-5',
] );

$html .= $this->get_author( [
	'class'      => 'wpex-text-3 wpex-child-inherit-color',
	'link_class' => 'wpex-no-underline',
	'prefix'     => esc_html( 'By', 'total' ) . ' ',
] );

return $html;
