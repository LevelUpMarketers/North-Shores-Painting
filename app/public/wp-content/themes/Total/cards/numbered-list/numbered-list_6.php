<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$has_link = $this->has_link();

$inner_class = 'wpex-card-inner wpex-flex wpex-flex-grow wpex-gap-10 wpex-p-20 wpex-rounded-sm wpex-surface-2 wpex-hover-bg-accent wpex-hover-text-white wpex-transition-colors wpex-duration-150';

if ( $has_link ) {
	$html .= $this->get_link_open( array(
		'class' => $inner_class . ' wpex-no-underline',
	) );
} else {
	$html .= '<div class="' . $inner_class . '">';
}

	// Title
	$html .= $this->get_title( array(
		'class' => 'wpex-heading wpex-flex-grow wpex-text-lg wpex-inherit-color-important',
		'link' => false,
		'show_count' => true,
	) );

if ( $has_link ) {
	$html .= '</a>';
} else {
	$html .= '</div>';
}

return $html;