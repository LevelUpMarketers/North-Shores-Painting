<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Inner
$html .= '<div class="wpex-card-inner wpex-pt-15 wpex-pb-30 wpex-border-t-2 wpex-border-main wpex-border-solid wpex-last-mb-0">';

	// Primary Term
	$html .= $this->get_primary_term( array(
		'class' => 'wpex-mb-15 wpex-text-xs wpex-font-bold',
		'term_class' => 'wpex-text-accent wpex-hover-underline',
		'has_term_color' => true,
	) );

	// Title
	$html .= $this->get_title( array(
		'class' => 'wpex-heading wpex-text-2xl wpex-mb-20',
	) );

	// Excerpt
	$html .= $this->get_excerpt( array(
		'excerpt_length' => 30,
	) );

$html .= '</div>';

return $html;