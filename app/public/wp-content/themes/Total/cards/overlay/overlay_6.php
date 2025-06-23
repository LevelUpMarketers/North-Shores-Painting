<?php

defined( 'ABSPATH' ) || exit;

$this->thumbnail_overlay_style = false;
$this->allowed_media = [ 'thumbnail' ];

$has_link = $this->has_link();

$html = '';

if ( $has_link ) {
	$html .= $this->get_link_open( [
		'class' => 'wpex-card-inner wpex-relative wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-no-underline wpex-image-hover-parent',
		'attributes' => array(
			'aria-label' => get_the_title( $this->post_id ),
		),
	] );
} else {
	$html .= '<div class="wpex-card-inner wpex-relative wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-image-hover-parent">';
}

	$html .= $this->get_thumbnail( [
		'link' => false,
		'class' => 'wpex-flex wpex-h-100',
		'image_class' => 'wpex-h-100 wpex-w-100 wpex-object-cover',
	] );

	$html .= '<span class="wpex-card-backdrop wpex-absolute wpex-inset-0" style="background-image:linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,.15) 60%, rgba(0,0,0,.85) 100%);"></span>';

	$html .= $this->get_primary_term( [
		'class' => 'wpex-absolute wpex-right-0 wpex-top-0 wpex-leading-tight wpex-mt-20 wpex-mr-20 wpex-bold wpex-uppercase wpex-text-white',
		'term_class' => 'wpex-inline-block wpex-bg-accent wpex-hover-bg-accent_alt wpex-no-underline wpex-px-10 wpex-py-5 wpex-rounded-full',
		'has_term_background_color' => true,
		'link' => false,
		'css' => 'font-size:max(10px,0.7em)',
	] );

	$html .= '<div class="wpex-card-details wpex-absolute wpex-bottom-0 wpex-inset-x-0 wpex-text-white wpex-p-25">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-current wpex-text-lg',
			'link' => false,
		] );

		$excerpt = $this->get_excerpt( [
			'class' => 'wpex-opacity-60 wpex-text-sm',
			'link' => false,
			'length' => 16,
		] );

		if ( $excerpt ) {
			if ( $this->post_id && $primary_term = totaltheme_get_post_primary_term( $this->post_id ) ) {
				$term_bg_class = totaltheme_get_term_color_background_classname( $primary_term );
			} else {
				$term_bg_class = '';
			}

			$html .= $this->get_empty_element( [
				'html_tag' => 'span',
				'class'    => "wpex-card-divider wpex-h-3px wpex-block wpex-bg-accent wpex-my-10 {$term_bg_class}",
				'css'      => 'width:min(80px,3vw);',
			] );

			$html .= $excerpt;

		}

	$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
