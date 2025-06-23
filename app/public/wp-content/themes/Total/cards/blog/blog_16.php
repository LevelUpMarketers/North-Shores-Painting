<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Media
$html .= $this->get_media( array(
	'class' => 'wpex-mb-15',
) );

// Title
$html .= $this->get_title( array(
	'class' => 'wpex-heading wpex-text-2xl',
) );

// Excerpt
$html .= $this->get_excerpt( array(
	'class'  => 'wpex-mt-15',
	'length' => 30,
) );

// Date
$html .= $this->get_date( array(
	'class' => 'wpex-mt-15',
	'type'  => 'published',
) );

return $html;