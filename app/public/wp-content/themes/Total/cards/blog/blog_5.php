<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Media
$html .= $this->get_media( array(
	'class' => 'wpex-mb-15',
) );

// Title
$html .= $this->get_title( array(
	'class' => 'wpex-heading wpex-text-lg',
) );

return $html;