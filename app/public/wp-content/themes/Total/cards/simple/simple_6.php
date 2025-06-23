<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-lg wpex-mb-10',
] );

$html .= $this->get_excerpt( [
	'class' => 'wpex-mb-20',
] );

$html .= $this->get_more_link( [
	'text' => esc_html__( 'Learn more', 'total' ),
	'link_class' => 'wpex-hover-underline',
	'suffix' => ' &rarr;',
] );

return $html;
