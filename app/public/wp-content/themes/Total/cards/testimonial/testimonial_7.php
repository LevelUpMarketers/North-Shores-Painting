<?php

defined( 'ABSPATH' ) || exit;

$legacy_fs = totaltheme_has_classic_styles();

$html = $this->get_star_rating( [
	'class' => $legacy_fs ? 'wpex-text-md wpex-text-accent' : 'wpex-text-lg wpex-text-accent',
] );

$html .= $this->get_title( [
	'link'  => false,
	'class' => $legacy_fs ? 'wpex-heading wpex-text-md wpex-font-semibold wpex-my-5' : 'wpex-heading wpex-text-lg wpex-font-semibold wpex-my-5',
] );

$html .= $this->get_excerpt( [
	'length' => '-1',
	'class'  => 'wpex-italic',
] );

return $html;
