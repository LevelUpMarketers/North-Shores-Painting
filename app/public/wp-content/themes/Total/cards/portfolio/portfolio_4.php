<?php

defined( 'ABSPATH' ) || exit;

$has_link = $this->has_link_wrap();
$html     = '';

if ( $has_link ) {
	$html .= $this->get_link_open( [
		'class' => 'wpex-card-inner wpex-text-center wpex-text-3 wpex-no-underline wpex-last-mb-0',
	] );
} else {
	$html .= '<div class="wpex-card-inner wpex-text-center wpex-last-mb-0">';
}

	$html .= $this->get_media( [
		'class' => 'wpex-mx-auto wpex-mb-15',
		'link' => $has_link ? false : true,
	] );

	$title_class = 'wpex-heading wpex-text-lg wpex-font-normal';
	if ( $has_link ) {
		$title_class .= ' wpex-hover-text-accent';
	}
	$html .= $this->get_title( [
		'class' => $title_class,
		'link' => $has_link ? false : true,
	] );

if ( $has_link ) {
	$html .= '</a>';
} else {
	$html .= '</div>';
}

return $html;
