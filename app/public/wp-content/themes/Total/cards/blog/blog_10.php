<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_media( [
	'class' => 'wpex-mb-25',
] );

$html .= $this->get_primary_term( [
	'class' => 'wpex-uppercase wpex-text-3 wpex-text-xs wpex-font-semibold wpex-tracking-wide wpex-mb-5',
	'term_class' => 'wpex-inherit-color wpex-hover-underline',
] );

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-xl wpex-mb-15',
] );

$html .= $this->get_excerpt();

return $html;
