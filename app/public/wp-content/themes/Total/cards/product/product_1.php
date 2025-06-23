<?php

defined( 'ABSPATH' ) || exit;

$has_link = $this->has_link();
$html = '';

$inner_class = 'wpex-card-inner wpex-relative';

if ( $has_link ) {

	if ( ! empty( $this->args['thumbnail_overlay_style'] )
		&& in_array( $this->args['thumbnail_overlay_style'], [ 'thumb-swap', 'thumb-swap-title' ], true )
	) {
		$inner_class .= ' overlay-parent';
	}

	$html .= $this->get_link_open( [
		'class'      => "{$inner_class} wpex-no-underline wpex-inherit-color",
		'attributes' => [
			'aria-label' => get_the_title( $this->post_id ),
		],
	] );
} else {
	$html .= '<div class="' . esc_attr( $inner_class ) . '">';
}

	$sale_flash = $this->get_sale_flash( [
		'class' => 'wpex-absolute wpex-z-5 wpex-left-0 wpex-top-0 wpex-ml-15 wpex-mt-15 wpex-inline-block wpex-py-5 wpex-px-10 wpex-mb-20 wpex-bg-accent wpex-leading-normal wpex-rounded-sm',
	] );

	$html .= $this->get_media( [
		'class'  => 'wpex-mb-15',
		'before' => $sale_flash,
		'link'   => false,
	] );

	$html .= '<div class="wpex-card-details wpex-last-mb-0">';

		if ( totaltheme_has_classic_styles() ) {
			$title_class = 'wpex-heading wpex-text-md wpex-mb-5';
		} else {
			$title_class = 'wpex-heading wpex-text-lg wpex-mb-5';
		}

		$html .= $this->get_title( [
			'class' => $title_class,
			'link'  => false,
		] );

		$html .= $this->get_price( [
			'link' => false,
		] );

	$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
