<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= $this->get_thumbnail( [
	'class' => 'wpex-mx-auto',
] );

$html .= '<div class="wpex-card-details wpex-text-center wpex-p-20">';

	$html .= $this->get_title( [
		'class' => 'wpex-heading wpex-text-xl',
	] );

	$html .= $this->get_element( [
		'content' => wpex_get_staff_member_position(),
		'class' => 'wpex-card-staff-member-position wpex-my-15 wpex-text-sm',
	] );

	$html .= $this->get_excerpt( [
		'length' => '-1',
		'class' => 'wpex-mt-15',
	] );

$html .= '</div>';

return $html;
