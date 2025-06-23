<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-2 wpex-text-2 wpex-p-30">';

	$html .= $this->get_thumbnail( [
		'class'       => 'wpex-mx-auto',
		'image_class' => 'wpex-rounded-full',
		'link'        => false,
	] );

	$html .= $this->get_excerpt( [
		'length' => '-1',
		'class'  => 'wpex-my-20',
	] );

	if ( $author = wpex_get_testimonial_author() ) {
		$author_class = 'wpex-card-testimonial-author wpex-text-1 wpex-font-semibold wpex-mt-auto';
		$author_class .= totaltheme_has_classic_styles() ? ' wpex-text-md' : ' wpex-text-lg';
		$html .= $this->get_element( [
			'content' => $author,
			'class'   => $author_class,
		] );
	}

	if ( $company = wpex_get_testimonial_company() ) {
		$html .= $this->get_element( [
			'content' => $company,
			'class'   => 'wpex-card-testimonial-company wpex-text-sm wpex-opacity-70',
		] );
	}

$html .= '</div>';

return $html;
