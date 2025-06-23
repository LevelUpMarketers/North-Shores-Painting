<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= $this->get_thumbnail( [
	'class' => 'wpex-rounded-t',
	'image_class' => 'wpex-rounded-t',
] );

$html .= '<div class="wpex-card-details wpex-surface-1 wpex-flex-col wpex-flex-grow wpex-p-25 wpex-last-mb-0">';

	$html .= $this->get_title( [
		'class' => 'wpex-heading wpex-text-lg wpex-mb-5',
	] );

	$html .= $this->get_element( [
		'content' => wpex_get_staff_member_position(),
		'class' => 'wpex-card-staff-member-position wpex-text-3 wpex-font-bold',
	] );

	$html .= $this->get_excerpt( [
		'length' => 15,
		'class'  => 'wpex-my-10 wpex-text-3',
	] );

	$html .= $this->get_element( [
		'content' => wpex_get_staff_social( [
			'show_icons' => false,
			'spacing'    => '10',
		] ),
		'class' => 'wpex-mt-15',
	] );

$html .= '</div>';

return $html;
