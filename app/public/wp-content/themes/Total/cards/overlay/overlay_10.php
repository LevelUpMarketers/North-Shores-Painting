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

	$html .= '<div class="wpex-card-details wpex-absolute wpex-bottom-0 wpex-inset-x-0 wpex-text-white wpex-p-25">';

		$html .= $this->get_primary_term( [
			'class' => 'wpex-leading-tight wpex-mb-10 wpex-text-xs',
			'link' => false,
		] );

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-current wpex-text-lg',
			'link' => false,
		] );

		if ( $this->post_id && $primary_term = totaltheme_get_post_primary_term( $this->post_id ) ) {
			$term_bg_class = totaltheme_get_term_color_background_classname( $primary_term );
		} else {
			$term_bg_class = '';
		}

		$html .= '<div class="wpex-h-2px wpex-w-100 wpex-my-10 wpex-flex wpex-relative"><span class="wpex-bg-white wpex-opacity-20 wpex-absolute wpex-inset-0"></span><span class="' . trim( "wpex-relative wpex-bg-accent {$term_bg_class}" ) . '" style="width:min(40%,140px);"></span></div>';

		$html .= '<div class="wpex-card-meta wpex-opacity-80 wpex-flex wpex-flex-wrap wpex-justify-between wpex-gap-15 wpex-text-xs">';

			$html .= '<div class="wpex-flex wpex-items-center">';

				$html .= $this->get_avatar( [
					'size' => 30,
					'link' => false,
					'class' => 'wpex-flex-shrink-0 wpex-mr-5',
					'image_class' => 'wpex-rounded-full wpex-align-middle',
				] );

				$html .= $this->get_author( [
					'link' => false,
				] );

				$html .= '<div class="wpex-mx-5">&ndash;</div>';

				$html .= $this->get_date( [
					'type' => 'time_ago',
					'link' => false,
				] );

			$html .= '</div>';

			$html .= '<div class="wpex-card-more-arrow wpex-flex wpex-items-center"><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="18px" viewBox="0 0 24 24" width="18px" fill="currentColor"><rect fill="none" height="24" width="24"/><path d="M15,5l-1.41,1.41L18.17,11H2V13h16.17l-4.59,4.59L15,19l7-7L15,5z"/></svg></div>';

		$html .= '</div>';

	$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
