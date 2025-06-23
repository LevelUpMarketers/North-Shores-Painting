<?php

defined( 'ABSPATH' ) || exit;

$has_link = $this->has_link();
$html = '';

$inner_class = 'wpex-card-inner';

if ( $has_link ) {
	if ( ! empty( $this->args['thumbnail_overlay_style'] )
		&& in_array( $this->args['thumbnail_overlay_style'], [ 'thumb-swap', 'thumb-swap-title' ], true )
	) {
		$inner_class .= ' overlay-parent';
	}
	$html .= $this->get_link_open( [
		'class' => $inner_class . ' wpex-no-underline wpex-inherit-color',
		'attributes' => [
			'aria-label' => get_the_title( $this->post_id ),
		],
	] );
} else {
	$html .= '<div class="' . esc_attr( $inner_class ) . '">';
}

$html .= '<div class="wpex-card-inner wpex-relative wpex-text-center wpex-p-15 wpex-surface-1 wpex-border wpex-border-solid wpex-border-gray-200">';

	$html .= $this->get_media( [
		'class' => 'wpex-mb-15 wpex-mx-auto',
		'link' => false,
	] );

	$html .= '<div class="wpex-card-details wpex-last-mb-0">';

		if ( totaltheme_has_classic_styles() ) {
			$title_class = 'wpex-heading wpex-text-md wpex-mb-5';
		} else {
			$title_class = 'wpex-heading wpex-text-lg wpex-mb-5';
		}

		$html .= $this->get_title( [
			'class' => $title_class,
			'link_class' => 'wpex-inherit-color-important',
			'link' => false,
		] );

		$html .= $this->get_price( [
			'class' => 'wpex-text-accent wpex-font-semibold',
			'link' => false,
		] );

	$html .= '</div>';

$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
