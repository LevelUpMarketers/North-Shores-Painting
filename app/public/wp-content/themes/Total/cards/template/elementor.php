<?php

defined( 'ABSPATH' ) || exit;

if ( empty( $content ) ) {
	return;
}

$html    = '';
$content = wpex_get_elementor_content_for_display( $template_id );

if ( $this->has_link() ) {
	// Replace all links with spans.
	$content = str_replace( '<a', '<span', $content );
	$content = str_replace( '</a>', '</span>', $content );

	// Open card link.
	$html .= $this->get_link_open( [
		'class' => 'wpex-card-inner wpex-no-underline wpex-inherit-color',
		'attributes' => [
			'aria-label' => get_the_title( $this->post_id ),
		],
	] );

	// Card content.
	$html .= $content;

	// Close card.
	$html .= $this->get_link_close();
} else {
	$html = $html .= '<div class="wpex-card-inner">' . $content . '</div>';
}

return $html;
