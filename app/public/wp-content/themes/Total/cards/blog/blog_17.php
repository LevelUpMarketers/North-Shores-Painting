<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow">';

	// Media
	$html .= $this->get_media( array(
		'class' => 'wpex-mb-20',
	) );

	// Details
	$html .= '<div class="wpex-card-details wpex-last-mb-0">';

		// First Term
		$html .= $this->get_primary_term( array(
			'class' => 'wpex-text-accent wpex-mb-5 wpex-text-xs wpex-uppercase wpex-tracking-wide',
			'term_class' => 'wpex-inherit-color wpex-no-underline',
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-xl wpex-mb-10',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'length' => 30,
		) );

		// Footer
		$html .= '<div class="wpex-card-footer wpex-mt-15 wpex-flex wpex-items-center">';

			// Avatar
			$html .= $this->get_avatar( array(
				'size' => 35,
				'class' => 'wpex-flex-shrink-0 wpex-mr-15',
				'image_class' => 'wpex-rounded-full wpex-align-middle',
			) );

			$html .= '<div class="wpex-card-meta wpex-flex-grow wpex-leading-snug wpex-text-sm">';

				// Author
				$html .= $this->get_author( array(
					'class' => 'wpex-font-semibold wpex-text-1 wpex-capitalize',
					'link' => false,
				) );

				// Post meta byline
				$html .= '<div class="wpex-flex wpex-flex-wrap wpex-gap-5 wpex-text-3">';

					// Date
					$html .= $this->get_date( array(
						'type' => 'published',
					) );

					// Read Time
					$html .= $this->get_estimated_read_time( array(
						'before' => '<span> &bull; </span>',
						'minute_text' => esc_html__( '%s min read', 'total' ),
						'second_text' => esc_html__( '%s sec read', 'total' ),
					) );

				$html .= '</div>';

			$html .= '</div>';

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;