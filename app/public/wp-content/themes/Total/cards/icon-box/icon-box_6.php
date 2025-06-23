<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-1 wpex-border wpex-border-solid wpex-border-gray-300 wpex-border-t-4 wpex-border-t-gray-800">';

	$html .= '<div class="wpex-card-details wpex-p-20">';

		// Icon
		$html .= $this->get_icon( array(
			'size' => 'sm',
			'class' => 'wpex-text-accent wpex-mb-15',
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-lg wpex-mb-10',
		) );

		// Excerpt
		$html .= $this->get_excerpt();

	$html .= '</div>';

	// More Link
	$html .= $this->get_more_link( array(
		'class' => 'wpex-mt-auto wpex-border-t wpex-border-solid wpex-border-gray-300 wpex-py-15 wpex-px-20 wpex-text-sm wpex-font-medium wpex-uppercase',
		'link_class' => 'wpex-hover-underline',
		'text' => esc_html__( 'Learn more', 'total' ),
		'suffix' => ' &raquo;',
	) );

$html .= '</div>';

return $html;