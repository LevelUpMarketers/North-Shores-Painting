<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-p-25 wpex-border wpex-border-main wpex-border-solid wpex-last-mb-0">';

	$html .= $this->get_date( [
		'class' => 'wpex-mb-5 wpex-text-3',
	] );

	$html .= $this->get_title( [
		'class' => 'wpex-heading wpex-text-lg wpex-mb-15',
	] );

	$html .= $this->get_excerpt();

$html .= '</div>';

return $html;
