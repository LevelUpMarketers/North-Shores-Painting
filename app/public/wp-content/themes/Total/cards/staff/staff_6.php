<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-1 wpex-text-center">';

	$html .= $this->get_thumbnail( [
		'class' => 'wpex-mx-auto',
	] );

	$html .= '<div class="wpex-card-details wpex-p-25">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-lg wpex-text-1 wpex-font-normal wpex-leading-snug wpex-child-inherit-color',
		] );

		$divider = $this->get_empty_element( [
			'html_tag' => 'span',
			'class'    => 'wpex-card-divider wpex-inline-block wpex-bg-accent wpex-my-5',
			'css'      => 'width:30px;height:2px;',
		] );

		$html .= $this->get_element( [
			'content' => wpex_get_staff_member_position(),
			'class' => 'wpex-card-staff-member-position wpex-text-xs wpex-uppercase wpex-leading-snug wpex-tracking-wide wpex-text-3',
			'before'  => $divider, // divider should only display if a position exists.
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
