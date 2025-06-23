<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-2 wpex-p-30 wpex-text-center wpex-last-mb-0">';

	$html .= $this->get_excerpt( [
		'length' => '-1',
		'class'  => 'wpex-text-2xl wpex-font-500 wpex-text-2 wpex-mb-20',
	] );

	$html .= '<div class="wpex-card-footer wpex-mt-auto wpex-flex wpex-items-center wpex-justify-center wpex-text-left">';

		$html .= $this->get_thumbnail( [
			'link'        => false,
			'class'       => 'wpex-rounded-full wpex-mr-20',
			'image_class' => 'wpex-rounded-full',
		] );

		$html .= '<div class="wpex-card-meta">';

			$html .= $this->get_element( [
				'content' => wpex_get_testimonial_author(),
				'class'   => totaltheme_has_classic_styles() ? 'wpex-card-testimonial-author wpex-heading wpex-text-md wpex-text-1 wpex-font-bold' : 'wpex-card-testimonial-author wpex-heading wpex-text-lg wpex-text-1 wpex-font-bold',
			] );

			$html .= $this->get_element( [
				'content'     => wpex_get_testimonial_company(),
				'link'        => wpex_get_testimonial_company_url(),
				'link_target' => wpex_get_testimonial_company_link_target(),
				'link_class'  => 'wpex-no-underline',
				'class'       => 'wpex-card-testimonial-company wpex-text-3 wpex-child-inherit-color',
			] );

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;
