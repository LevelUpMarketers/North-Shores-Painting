<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-1 wpex-border wpex-border-solid wpex-border-main">';

	// Media
	$html .= $this->get_media();

	// Details
	$html .= '<div class="wpex-card-details wpex-m-25 wpex-last-mb-0">';

		$html .= $this->get_primary_term( [
			'class' => 'wpex-font-semibold wpex-leading-normal wpex-mb-15',
			'term_class' => 'wpex-inline-block wpex-bg-accent wpex-hover-bg-accent_alt wpex-no-underline wpex-px-10 wpex-py-5 wpex-text-xs',
			'has_term_background_color' => true,
		] );

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-xl wpex-font-bold wpex-my-15',
			'link_class' => 'wpex-inherit-color-important',
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-text-2 wpex-my-15',
		] );

	$html .= '</div>';

	// Footer
	$html .= '<div class="wpex-card-footer wpex-mt-auto wpex-mx-25 wpex-mb-25">';

		$html .= $this->get_date( [
			'class'      => 'wpex-text-3',
			'icon'       => 'clock-o',
			'icon_class' => 'wpex-mr-10',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
