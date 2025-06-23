<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-text-center">';

	// Thumbnail
	$html .= $this->get_thumbnail( array(
		'link' => false,
		'class' => 'wpex-mx-auto wpex-rounded-full wpex-mb-15',
		'image_class' => 'wpex-rounded-full wpex-p-3 wpex-shadow-xs',
	) );

	// Rating
	$html .= $this->get_star_rating( array(
		'class' => 'wpex-mb-5 wpex-text-sm',
	) );

	// Author
	$html .= $this->get_element( array(
		'content' => wpex_get_testimonial_author(),
		'class' => 'wpex-card-testimonial-author wpex-heading wpex-text-lg wpex-mb-15',
	) );

	// Excerpt
	$html .= $this->get_excerpt( array(
		'length' => '-1',
		'class' => 'wpex-mt-15',
	) );

$html .= '</div>';

return $html;