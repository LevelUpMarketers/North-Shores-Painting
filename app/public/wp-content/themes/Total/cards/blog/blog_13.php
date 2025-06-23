<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_media( [
	'class' => 'wpex-mb-15',
] );

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-xl wpex-font-medium',
] );

$html .= $this->get_date( [
	'type'  => 'published',
	'class' => 'wpex-mt-10 wpex-text-2xs wpex-uppercase wpex-font-medium wpex-text-3 wpex-tracking-widest',
] );

return $html;
