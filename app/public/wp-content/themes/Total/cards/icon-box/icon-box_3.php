<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-2 wpex-text-1 wpex-p-30 wpex-text-center">';

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
		'class' => 'wpex-mb-15 wpex-text-2',
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