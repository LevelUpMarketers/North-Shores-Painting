<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-items-center wpex-surface-1 wpex-p-25 wpex-rounded-md wpex-shadow">';

	$html .= '<div class="wpex-card-details wpex-flex-grow">';

		$html .= $this->get_icon( [
			'icon'  => 'quote-left',
			'class' => 'wpex-text-accent wpex-mb-10',
			'bidi'  => true,
		] );

		$html .= $this->get_excerpt( [
			'length' => '-1',
			'class'  => 'wpex-font-500',
		] );

		$html .= $this->get_element( [
			'content' => wpex_get_testimonial_author(),
			'class'   => totaltheme_has_classic_styles() ? 'wpex-card-testimonial-author wpex-mt-10 wpex-heading wpex-text-md wpex-text-1 wpex-font-bold wpex-uppercase' : 'wpex-card-testimonial-author wpex-mt-10 wpex-heading wpex-text-lg wpex-text-1 wpex-font-bold wpex-uppercase',
		] );

	$html .= '</div>';

	$html .= $this->get_thumbnail( [
		'link'        => false,
		'class'       => 'wpex-flex-shrink-0 wpex-ml-20 wpex-rounded-full',
		'image_class' => 'wpex-rounded-full',
	] );

$html .= '</div>';

return $html;
