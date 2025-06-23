<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-border wpex-border-gray-200 wpex-border-solid wpex-p-30 wpex-text-center">';

	// Icon
	$html .= $this->get_icon( array(
		'size' => 'sm',
		'class' => 'wpex-text-accent wpex-mb-20',
	) );

	// Title
	$html .= $this->get_title( array(
		'class' => 'wpex-heading wpex-text-lg wpex-mb-15',
	) );

	// Excerpt
	$html .= $this->get_excerpt( array(
		'class' => 'wpex-mb-15',
	) );

	// More Link
	$html .= $this->get_more_link( array(
		'class' => 'wpex-font-semibold wpex-mt-auto',
		'link_class' => 'wpex-hover-underline',
		'text' => esc_html__( 'Learn more', 'total' ),
		'suffix' => ' &rarr;',
	) );

$html .= '</div>';

return $html;