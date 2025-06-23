<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-p-15 wpex-surface-1 wpex-border wpex-border-solid wpex-border-main">';

	$html .= $this->get_thumbnail( [
		'class' => 'wpex-mb-15',
	] );

	$html .= $this->get_title( [
		'class' => totaltheme_has_classic_styles() ? 'wpex-heading wpex-text-md' : 'wpex-heading wpex-text-lg',
	] );

	$html .= $this->get_element( [
		'content' => wpex_get_staff_member_position(),
		'class'   => 'wpex-card-staff-member-position wpex-text-3',
	] );

$html .= '</div>';

return $html;
