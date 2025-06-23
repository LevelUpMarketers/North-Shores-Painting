<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-surface-1 wpex-rounded-md wpex-border wpex-border-main wpex-border-solid">';

	$html .= '<div class="wpex-card-details wpex-px-25 wpex-pt-25">';

		$html .= $this->get_title( [
			'class'      => 'wpex-heading wpex-text-lg wpex-font-normal wpex-mb-15 wpex-text-accent',
			'link_class' => 'wpex-inherit-color-important wpex-hover-underline',
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-my-15'
		] );

	$html .= '</div>';

	$html .= $this->get_element( [
		'link'       => get_permalink(),
		'content'    => get_permalink(),
		'class'      => 'wpex-card-permalink wpex-py-15 wpex-px-25 wpex-border-t wpex-border-main wpex-border-solid wpex-overflow-hidden wpex-text-ellipsis',
		'css'        => 'color:#006627',
		'link_class' => 'wpex-inherit-color-important wpex-hover-underline',
	] );

$html .= '</div>';

return $html;
