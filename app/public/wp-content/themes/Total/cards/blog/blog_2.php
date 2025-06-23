<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow">';

	// Media
	$html .= $this->get_media( array(
		'class' => 'wpex-mb-25 wpex-rounded',
		'image_class' => 'wpex-rounded',
	) );

	$html .= '<div class="wpex-card-details wpex-mb-25 wpex-last-mb-0">';

		// Terms
		$html .= $this->get_terms_list( array(
			'class' => 'wpex-mb-10 wpex-text-sm wpex-font-semibold',
			'term_class' => 'wpex-no-underline',
			'separator' => ' &middot; ',
			'has_term_color' => true,
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-xl wpex-mb-15',
			'link_class' => 'wpex-inherit-color-important',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mb-25',
			'length' => 30,
		) );

	$html .= '</div>';

	$html .= '<div class="wpex-card-footer wpex-flex wpex-items-center">';

		// Avatar
		$html .= $this->get_avatar( array(
			'size' => 40,
			'class' => 'wpex-flex-shrink-0 wpex-mr-15',
			'image_class' => 'wpex-rounded-full wpex-align-middle',
		) );

		$html .= '<div class="wpex-card-meta wpex-flex-grow wpex-leading-snug wpex-text-sm">';

			// Author
			$html .= $this->get_author( array(
				'class' => 'wpex-text-1 wpex-font-bold wpex-capitalize',
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