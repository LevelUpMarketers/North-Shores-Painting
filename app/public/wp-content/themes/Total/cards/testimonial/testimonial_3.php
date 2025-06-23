<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-p-30 wpex-surface-1 wpex-rounded-sm wpex-shadow-lg">';

	$html .= $this->get_star_rating( [
		'class' => 'wpex-mb-15',
	] );

	$html .= $this->get_excerpt( [
		'class'  => 'wpex-mb-20',
		'length' => '-1',
	] );

	$html .= '<div class="wpex-card-footer wpex-mt-auto wpex-flex wpex-items-center">';

		$html .= $this->get_thumbnail( [
			'link'        => false,
			'class'       => 'wpex-shrink-0 wpex-rounded-full wpex-mr-15 wpex-card-thumbnail-sm',
			'image_class' => 'wpex-rounded-full',
		] );

		$html .= '<div class="wpex-card-footer-aside wpex-flex-grow">';

			$html .= $this->get_element( [
				'content' => wpex_get_testimonial_author(),
				'class'   => totaltheme_has_classic_styles() ? 'wpex-card-testimonial-author wpex-heading wpex-text-md' : 'wpex-card-testimonial-author wpex-heading wpex-text-lg',
			] );

			$html .= $this->get_element( [
				'content'     => wpex_get_testimonial_company(),
				'link'        => wpex_get_testimonial_company_url(),
				'link_target' => wpex_get_testimonial_company_link_target(),
				'link_class'  => 'wpex-hover-underline',
				'class'       => 'wpex-card-testimonial-company wpex-text-3 wpex-child-inherit-color',
			] );

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;
