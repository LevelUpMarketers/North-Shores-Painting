<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_media( [
	'class' => 'wpex-mb-20',
] );

if ( totaltheme_has_classic_styles() ) {
	$title_class = 'wpex-heading wpex-text-md';
} else {
	$title_class = 'wpex-heading wpex-text-lg';
}

$html .= $this->get_title( [
	'class' => $title_class,
] );

$html .= $this->get_excerpt( [
	'class' => 'wpex-mt-10',
] );

return $html;
