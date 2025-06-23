<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-p-30 wpex-surface-1 wpex-shadow-xl wpex-last-mb-0">';

	// Number
	$html .= $this->get_number( array(
		'class' => 'wpex-text-1 wpex-text-6xl wpex-font-light',
		'prepend_zero' => true,
	) );

	// Title
	$html .= $this->get_title( array(
		'class' => 'wpex-heading wpex-text-lg wpex-mb-10',
	) );

	// Excerpt
	$html .= $this->get_excerpt( array(
		'class' => 'wpex-mb-15 wpex-text-3',
	) );

	// More Link
	$html .= $this->get_more_link( array(
		'class' => 'wpex-mt-auto wpex-font-semibold',
		'link_class' => 'wpex-hover-underline',
		'text' => esc_html__( 'Learn more', 'total' ),
		'suffix' => ' &rarr;',
	) );

$html .= '</div>';

return $html;