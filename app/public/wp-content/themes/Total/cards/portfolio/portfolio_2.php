<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_media( [
	'class' => 'wpex-mb-15',
] );

$html .= $this->get_title( [
	'class' => totaltheme_has_classic_styles() ? 'wpex-heading wpex-text-base' : 'wpex-heading wpex-text-lg',
] );

$html .= $this->get_terms_list( [
	'class'      => 'wpex-text-2',
	'separator'  => ' &middot; ',
	'term_class' => 'wpex-inherit-color wpex-hover-underline',
] );

return $html;
