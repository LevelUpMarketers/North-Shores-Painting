<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex-col wpex-flex-grow wpex-surface-1 wpex-border wpex-border wpex-border-gray-200 wpex-border-solid wpex-rounded wpex-mt-50 wpex-px-25 wpex-pb-25 wpex-text-center">';

	$html .= $this->get_thumbnail( [
		'link'        => false,
		'class'       => 'wpex-mx-auto',
		'image_class' => 'wpex-surface-1 wpex-rounded-full wpex-m-5 wpex-border wpex-border wpex-border-gray-200 wpex-border-solid',
	] );

	$html .= $this->get_excerpt( [
		'length' => '-1',
		'class'  => 'wpex-italic wpex-mt-15',
	] );

	if ( $author = wpex_get_testimonial_author() ) {
		if ( $company = wpex_get_testimonial_company() ) {
			$author = "{$author} - {$company}";
		}
		$html .= $this->get_element( [
			'content' => $author,
			'class'   => 'wpex-card-testimonial-author wpex-mt-10 wpex-heading wpex-text-sm wpex-text-accent wpex-font-bold',
		] );
	}

	$html .= $this->get_star_rating( [
		'class' => totaltheme_has_classic_styles() ? 'wpex-mt-5 wpex-text-md' : 'wpex-mt-5 wpex-text-lg',
	] );

$html .= '</div>';

return $html;
