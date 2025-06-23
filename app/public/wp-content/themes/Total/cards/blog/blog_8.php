<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-1 wpex-border wpex-border-solid wpex-border-gray-200 wpex-rounded-sm wpex-overflow-hidden">';

	// Media
	$html .= $this->get_media();

	// Details
	$html .= '<div class="wpex-card-details wpex-m-25 wpex-last-mb-0">';

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-xl wpex-mb-15',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'length' => 30,
		) );

	$html .= '</div>';

	// Footer
	$html .= '<div class="wpex-card-footer wpex-mt-auto wpex-mx-25 wpex-mb-25 wpex-flex wpex-items-center">';

		// Avatar
		$html .= $this->get_avatar( array(
			'size' => 35,
			'class' => 'wpex-flex-shrink-0 wpex-mr-15',
			'image_class' => 'wpex-rounded-full wpex-align-middle',
		) );

		$html .= '<div class="wpex-card-meta wpex-flex-grow wpex-leading-snug wpex-text-sm">';

			// Author
			$html .= $this->get_author( array(
				'class' => 'wpex-font-medium wpex-text-1 wpex-capitalize',
				'link' => false,
			) );

			// Date
			$html .= $this->get_date( array(
				'type' => 'published',
			) );

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;