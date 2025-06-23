<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_title( [
	'class' => 'wpex-heading wpex-text-2xl wpex-mb-10',
] );

$html .= $this->get_date( [
    'class' => 'wpex-text-3 wpex-text-sm wpex-uppercase wpex-tracking-wider',
] );

return $html;
