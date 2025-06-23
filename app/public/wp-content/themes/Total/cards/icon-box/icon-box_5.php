<?php
defined( 'ABSPATH' ) || exit;

$html = '';

if ( $this->has_link() ) {
	$html .= $this->get_link_open( array(
		'class' => 'wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-justify-center wpex-surface-1 wpex-p-40 wpex-border-2 wpex-border-gray-200 wpex-border-solid wpex-text-center wpex-text-3 wpex-no-underline wpex-transition-all wpex-duration-300 wpex-hover-border-accent wpex-hover-text-accent',
	) );
} else {
	$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-justify-center wpex-surface-1 wpex-p-40 wpex-border-2 wpex-border-gray-200 wpex-border-solid wpex-text-center wpex-no-underline wpex-text-3">';
}

	// Icon
	$html .= $this->get_icon( array(
		'link' => false,
		'size' => 'sm',
		'class' => 'wpex-text-accent wpex-mb-10',
	) );

	// Title
	$html .= $this->get_title( array(
		'link' => false,
		'class' => 'wpex-heading wpex-font-medium wpex-text-lg wpex-inherit-color',
	) );

if ( $this->has_link() ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;