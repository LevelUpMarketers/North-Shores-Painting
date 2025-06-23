<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_thumbnail( [
	'class' => 'wpex-mb-10',
] );

$html .= $this->get_title( [
	'class' => totaltheme_has_classic_styles() ? 'wpex-heading wpex-text-md' : 'wpex-heading wpex-text-lg',
] );

$html .= $this->get_element( [
	'content' => wpex_get_staff_member_position(),
	'class'   => 'wpex-card-staff-member-position wpex-text-3',
] );

return $html;
