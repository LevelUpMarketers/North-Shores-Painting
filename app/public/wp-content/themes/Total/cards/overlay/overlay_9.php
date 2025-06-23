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

	$html .= '<span class="wpex-card-backdrop wpex-absolute wpex-inset-0" style="background-image:linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,.15) 40%, rgba(0,0,0,.85) 100%);"></span>';

	$html .= $this->get_terms_list( [
		'class' => 'wpex-absolute wpex-top-0 wpex-left-0 wpex-mx-25 wpex-mt-25 wpex-leading-tight wpex-uppercase wpex-tracking-wide wpex-surface-1 wpex-text-1 wpex-px-10 wpex-py-10 wpex-shadow-xl',
		'separator' => ', ',
		'link' => false,
		'css' => 'font-size:max(10px,0.75em)',
	] );

	$html .= '<div class="wpex-card-details wpex-absolute wpex-bottom-0 wpex-inset-x-0 wpex-text-white wpex-p-25">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-current wpex-text-lg',
			'link' => false,
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-text-current wpex-my-15 wpex-opacity-80 wpex-text-sm',
			'link' => false,
		] );

		$html .= '<div class="wpex-card-meta wpex-mt-15 wpex-opacity-80 wpex-flex wpex-flex-wrap wpex-gap-20 wpex-text-sm">';

			$html .= $this->get_date( [
				'class' => 'wpex-flex wpex-items-center',
				'type' => 'published',
				'link' => false,
				'icon' => 'material-schedule',
				'icon_class' => 'wpex-mr-10',
			] );

			$html .= $this->get_author( [
				'class' => 'wpex-flex wpex-items-center',
				'type' => 'published',
				'link' => false,
				'prefix' => esc_html_x( 'By', 'prefix for post author name', 'total' ) . ' ',
				'icon' => 'material-person-outline',
				'icon_class' => 'wpex-mr-10',
			] );

			$html .= $this->get_comment_count( [
				'class' => 'wpex-flex wpex-items-center',
				'link' => false,
				'icon' => 'material-chat-outline',
				'icon_class' => 'wpex-mr-10',
				'show_empty' => true,
			] );

		$html .= '</div>';

	$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
