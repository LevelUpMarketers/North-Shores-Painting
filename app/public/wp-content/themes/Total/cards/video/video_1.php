<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_video( [
	'class' => 'wpex-mb-15',
] );

$html .= $this->get_title( [
	'class'      => totaltheme_has_classic_styles() ? 'wpex-heading wpex-text-md' : 'wpex-heading wpex-text-lg',
	'link_class' => 'wpex-inherit-color-important wpex-hover-underline',
] );

$html .= $this->get_terms_list( [
	'class'      => 'wpex-text-1',
	'separator'  => ' &middot; ',
	'term_class' => 'wpex-inherit-color',
] );

return $html;
