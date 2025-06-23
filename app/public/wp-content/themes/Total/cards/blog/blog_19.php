<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-2">';

	// Media
	$html .= $this->get_media();

	// Details
	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow wpex-p-30 wpex-last-mb-0">';

		// Date
		$html .= $this->get_date( array(
			'class' => 'wpex-mb-10 wpex-text-3',
			'type' => 'published',
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-child-inherit-color wpex-text-2xl wpex-hover-underline',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mt-10 wpex-mb-20',
		) );

		// Terms
		$html .= $this->get_terms_list( array(
			'class' => 'wpex-flex wpex-flex-wrap wpex-gap-10 wpex-mt-auto',
			'term_class' => 'wpex-text-current wpex-hover-text-1 wpex-underline wpex-decoration-current',
			'term_prefix' => '#',
			'has_term_background_color' => false,
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;