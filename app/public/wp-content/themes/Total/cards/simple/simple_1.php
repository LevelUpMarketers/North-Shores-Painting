<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_date( [
	'class' => 'wpex-mb-5 wpex-text-3',
] );

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-lg wpex-mb-15',
] );

$html .= $this->get_excerpt();

return $html;
