<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-1 wpex-border-2 wpex-border-solid wpex-border-gray-200">';

	$html .= $this->get_media();

	$html .= '<div class="wpex-card-details wpex-m-25 wpex-last-mb-0">';

		$html .= $this->get_title( [
			'link'  => true,
			'class' => 'wpex-heading wpex-text-lg wpex-font-bold wpex-mb-15',
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-mb-15',
		] );

	$html .= '</div>';

	$html .= '<div class="wpex-card-footer wpex-mt-auto wpex-mb-25 wpex-mx-25">';

		$html .= $this->get_more_link( [
			'class'      => 'wpex-font-semibold',
			'link_class' => 'wpex-hover-underline',
			'text'       => esc_html__( 'Continue reading', 'total' ),
			'suffix'     => ' &rarr;',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
