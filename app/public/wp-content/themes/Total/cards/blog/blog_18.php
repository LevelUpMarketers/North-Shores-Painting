<?php
defined( 'ABSPATH' ) || exit;

$has_link = $this->has_link_wrap();
$html     = '';

if ( $has_link ) {
	$html .= $this->get_link_open( array(
		'class' => 'wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-no-underline wpex-inherit-color',
		'attributes' => array(
			'aria-label' => get_the_title( $this->post_id ),
		),
	) );
} else {
	$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow">';
}

	// Media
	$html .= $this->get_media( array(
		'class' => 'wpex-mb-15',
		'link' => ! $has_link,
	) );

	// Date
	$html .= $this->get_date( array(
		'class' => 'wpex-mb-5 wpex-text-3 wpex-text-xs',
		'type' => 'published',
	) );

	// Title
	$html .= $this->get_title( array(
		'class' => 'wpex-heading wpex-text-2xl',
		'link' => ! $has_link,
	) );

	// Excerpt
	$html .= $this->get_excerpt( array(
		'class' => 'wpex-mt-10',
	) );

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
