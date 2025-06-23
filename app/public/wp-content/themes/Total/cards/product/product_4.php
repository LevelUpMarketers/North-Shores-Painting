<?php

defined( 'ABSPATH' ) || exit;

$has_link = $this->has_link();
$html     = '';

$inner_class = 'wpex-card-inner wpex-relative wpex-shadow-lg';

if ( $has_link ) {

	if ( ! empty( $this->args['thumbnail_overlay_style'] )
		&& in_array( $this->args['thumbnail_overlay_style'], [ 'thumb-swap', 'thumb-swap-title' ], true )
	) {
		$inner_class .= ' overlay-parent';
	}

	$html .= $this->get_link_open( [
		'class' => "{$inner_class} wpex-no-underline wpex-inherit-color",
		'attributes' => [
			'aria-label' => get_the_title( $this->post_id ),
		],
	] );
} else {
	$html .= '<div class="' . esc_attr( $inner_class ) . '">';
}

	$sale_flash = $this->get_sale_flash( [
		'class' => 'wpex-absolute wpex-z-5 wpex-left-0 wpex-top-0 wpex-inline-block wpex-py-5 wpex-px-10 wpex-mb-20 wpex-bg-accent wpex-leading-normal wpex-text-xs wpex-uppercase wpex-font-semibold',
	] );

	$html .= $this->get_thumbnail( [
		'before'              => $sale_flash,
		'link'                => false,
		'strip_overlay_links' => true,
	] );

	$html .= '<div class="wpex-card-details wpex-p-15 wpex-last-mb-0">';

		$html .= $this->get_terms_list( [
			'class'     => 'wpex-last-mr-0 wpex-text-3 wpex-child-inherit-color',
			'separator' => ' &middot; ',
			'link'      => false,
		] );

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-lg wpex-mb-10 wpex-font-semibold',
			'link'  => false,
		] );

		if ( totaltheme_has_classic_styles() ) {
			$price_class = 'wpex-text-1 wpex-text-md wpex-font-semibold';
		} else {
			$price_class = 'wpex-text-1 wpex-text-lg wpex-font-semibold';
		}

		$html .= $this->get_price([
			'class'     => $price_class,
			'show_sale' => true,
			'min_max'   => true,
			'link'      => false,
		] );

	$html .= '</div>';


if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
