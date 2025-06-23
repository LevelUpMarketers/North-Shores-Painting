<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Number
$html .= $this->get_number( array(
	'class' => 'wpex-text-1 wpex-text-6xl wpex-font-light',
	'prepend_zero' => true,
) );

// Title
$html .= $this->get_title( array(
	'class' => 'wpex-heading wpex-text-lg',
) );

// Excerpt
$html .= $this->get_excerpt( array(
	'class' => 'wpex-mt-5 wpex-text-3',
) );

// More Link
$html .= $this->get_more_link( array(
	'class' => 'wpex-mt-15 wpex-font-semibold',
	'link_class' => 'wpex-hover-underline',
	'text' => esc_html__( 'Learn more', 'total' ),
	'suffix' => ' &rarr;',
) );

return $html;