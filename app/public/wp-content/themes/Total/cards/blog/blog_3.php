<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-rounded wpex-surface-1 wpex-shadow-lg wpex-overflow-hidden">';

	// Media
	$html .= $this->get_media();

	$html .= '<div class="wpex-card-details wpex-p-30 wpex-last-mb-0">';

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-lg wpex-mb-5',
			'link_class' => 'wpex-inherit-color-important',
			'has_term_color' => true,
		) );

		// Terms
		$html .= $this->get_terms_list( array(
			'class' => 'wpex-mb-15 wpex-text-xs wpex-font-semibold wpex-uppercase',
			'term_class' => 'wpex-hover-underline',
			'separator' => ' &middot; ',
			'has_term_color' => true,
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => '',
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;