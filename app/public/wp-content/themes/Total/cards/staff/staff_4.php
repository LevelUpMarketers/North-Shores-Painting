<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-text-center">';

	$html .= $this->get_thumbnail( [
		'class'       => 'wpex-mx-auto wpex-mb-15 wpex-rounded-full-tl wpex-rounded-full-bl wpex-rounded-full-br',
		'image_class' => 'wpex-rounded-full-tl wpex-rounded-full-bl wpex-rounded-full-br'
	] );

	$html .= $this->get_title( [
		'class' => totaltheme_has_classic_styles() ? 'wpex-heading wpex-text-md wpex-font-bold' : 'wpex-heading wpex-text-lg wpex-font-bold',
	] );

	$html .= $this->get_element( [
		'content' => wpex_get_staff_member_position(),
		'class'   => 'wpex-card-staff-member-position wpex-text-3',
	] );

$html .= '</div>';

return $html;
