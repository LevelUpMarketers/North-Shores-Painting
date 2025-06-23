<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$has_link = $this->has_link();

if ( $has_link ) {
	$html .= $this->get_link_open( array(
		'class' => 'wpex-card-header wpex-no-underline',
	) );
} else {
	$html .= '<div class="wpex-card-header">';
}

	// Permalink
	$html .= $this->get_element( array(
		'content' => get_permalink(),
		'class' => 'wpex-card-permalink',
		'css' => 'color:#006627;',
	) );

	// Title.
	$title_class = 'wpex-font-normal wpex-text-lg wpex-m-0';
	if ( $has_link ) {
		$title_class .= ' wpex-inherit-color wpex-hover-underline';
	}
	$html .= $this->get_title( array(
		'class' => $title_class,
		'link' => false,
	) );

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

// Excerpt.
$html .= $this->get_excerpt( array(
	'class' => 'wpex-mt-5'
) );

return $html;