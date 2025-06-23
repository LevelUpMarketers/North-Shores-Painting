<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-1 wpex-shadow wpex-rounded-sm">';

	// Media
	$html .= $this->get_media();

	// Details
	$html .= '<div class="wpex-card-details wpex-m-25 wpex-last-mb-0">';

		// Terms
		$html .= $this->get_terms_list( array(
			'class' => 'wpex-font-semibold wpex-leading-normal wpex-mb-15 wpex-last-mr-0',
			'term_class' => 'wpex-inline-block wpex-bg-accent wpex-hover-opacity-80 wpex-no-underline wpex-mr-5 wpex-mb-5 wpex-px-10 wpex-py-5 wpex-rounded-full wpex-text-xs',
			'has_term_background_color' => true,
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-lg wpex-font-bold wpex-mb-15',
			'link_class' => 'wpex-inherit-color-important',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mb-30',
		) );

	$html .= '</div>';

	// Footer
	$html .= '<div class="wpex-card-footer wpex-mt-auto wpex-mb-25 wpex-mx-25">';

		// Date
		$html .= $this->get_date( array(
			'type' => 'published',
			'class' => 'wpex-text-xs wpex-uppercase wpex-font-semibold',
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;