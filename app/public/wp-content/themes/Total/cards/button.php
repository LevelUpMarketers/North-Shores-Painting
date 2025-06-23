<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$class = 'wpex-card-title theme-button';

if ( get_queried_object_id() === $this->post_id ) {
	$class .= ' active';
}

$link_open = $this->get_link_open( [
	'class' => $class,
] );

if ( ! $link_open ) {
	return;
}

$html .= $link_open;

$html .= $this->get_the_title();

$html .= $this->get_link_close();

return $html;
