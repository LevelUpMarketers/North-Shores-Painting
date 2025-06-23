<?php

defined( 'ABSPATH' ) || exit;

$this->thumbnail_overlay_style = false;
$this->allowed_media = [ 'thumbnail' ];

$has_link = $this->has_link();
$bk = $this->get_breakpoint();

$html = '';

if ( $has_link ) {
	$html .= $this->get_link_open( [
		'class' => 'wpex-card-inner wpex-relative wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-no-underline wpex-image-hover-parent',
		'attributes' => [
			'aria-label' => get_the_title( $this->post_id ),
		],
	] );
} else {
	$html .= '<div class="wpex-card-inner wpex-relative wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-image-hover-parent">';
}

	$html .= $this->get_thumbnail( [
		'link' => false,
		'class' => 'wpex-flex wpex-h-100 wpex-rounded-sm wpex-overflow-hidden',
		'image_class' => 'wpex-h-100 wpex-w-100 wpex-object-cover',
	] );

	$html .= '<div class="wpex-card-details wpex-' . $bk . '-absolute wpex-bottom-0 wpex-' . $bk . '-inset-x-0 wpex-surface-1 wpex-text-1 wpex-mt-20 wpex-' . $bk . '-m-15 wpex-' . $bk . '-p-25 wpex-' . $bk . '-pt-0 wpex-' . $bk . '-shadow-xl wpex-rounded-sm wpex-text-center">';

		$html .= $this->get_primary_term( [
			'class' => "wpex-leading-tight wpex-uppercase wpex-text-white wpex-tracking-wider -wpex-{$bk}-translate-y-50",
			'term_class' => 'wpex-inline-block wpex-bg-accent wpex-hover-bg-accent_alt wpex-no-underline wpex-px-10 wpex-py-5 wpex-rounded-sm',
			'has_term_background_color' => true,
			'link' => false,
			'css' => 'font-size:max(10px,0.7em)',
		] );

		if ( totaltheme_has_classic_styles() ) {
			$title_class = "wpex-heading wpex-text-current wpex-text-md wpex-mt-20 wpex-{$bk}-mt-10";
		} else {
			$title_class = "wpex-heading wpex-text-current wpex-text-lg wpex-mt-20 wpex-{$bk}-mt-10";
		}

		$html .= $this->get_title( [
			'class' => $title_class,
			'link' => false,
		] );

		$html .= $this->get_date( [
			'class' => 'wpex-text-3 wpex-mt-15',
			'link' => false,
		] );

	$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
