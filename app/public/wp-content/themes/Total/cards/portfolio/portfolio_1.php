<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_media( [
	'class' => 'wpex-mb-15',
] );

$html .= '<div class="wpex-card-details wpex-flex wpex-flex-wrap wpex-justify-between">';

	$html .= $this->get_title( [
		'class' => 'wpex-heading wpex-uppercase wpex-font-bold',
	] );

	$html .= $this->get_date( [
		'type'  => 'time_ago',
		'class' => 'wpex-text-3 wpex-text-right',
	] );

$html .= '</div>';

return $html;
