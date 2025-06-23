<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Calculate svg size based on title font size.
$svg_font_size = 'wpex-text-lg';

if ( ! empty( $this->args['title_font_size'] ) ) {
	$svg_font_size = wpex_sanitize_utl_font_size( $this->args['title_font_size'] );
}

$html .= '<div class="wpex-card-inner wpex-flex wpex-gap-15">';

	// Icon.
	$html .= '<div class="wpex-card-svg ' . esc_attr( $svg_font_size ) . ' wpex-text-accent"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 24 24" width="1em" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/><circle cx="12" cy="12" r="5"/></svg></div>';

	// Details.
	$html .= '<div class="wpex-card-details">';

		// Title.
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-lg wpex-m-0',
		) );

		// Permalink
		$permalink = esc_url( get_permalink( $this->post_id ) );
		if ( $permalink ) {
			$html .= $this->get_element( array(
				'content' => $permalink,
				'class' => 'wpex-card-permalink',
				'css' => 'color:#006627;',
				'link_class' => 'wpex-inherit-color-important wpex-hover-underline',
				'link' => $permalink,
			) );
		}

		// Excerpt.
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mt-5'
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;