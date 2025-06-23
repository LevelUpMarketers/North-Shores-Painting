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

	$html .= '<span class="wpex-card-backdrop wpex-absolute wpex-inset-0" style="background-image:linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,.15) 50%, rgba(0,0,0,.85) 100%);"></span>';

	$html .= '<div class="wpex-card-details wpex-absolute wpex-bottom-0 wpex-inset-x-0 wpex-text-white wpex-p-25">';

		$html .= $this->get_primary_term( [
			'class' => 'wpex-leading-tight wpex-mb-15',
			'term_class' => 'wpex-inline-block wpex-bg-accent wpex-hover-bg-accent_alt wpex-no-underline wpex-px-15 wpex-py-5 wpex-rounded-full',
			'has_term_background_color' => true,
			'link' => false,
			'css' => 'font-size:max(10px,0.7em)',
		] );

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-current wpex-text-xl',
			'link' => false,
		] );

		$html .= '<div class="wpex-card-meta wpex-mt-25 wpex-flex wpex-flex-wrap wpex-gap-25 wpex-text-xs">';

			$html .= '<div class="wpex-flex wpex-items-center">';

				$html .= $this->get_avatar( [
					'size' => 25,
					'link' => false,
					'class' => 'wpex-flex-shrink-0 wpex-mr-10',
					'image_class' => 'wpex-rounded-full wpex-align-middle',
				] );

				$html .= $this->get_author( [
					'link' => false,
				] );

			$html .= '</div>';

			$html .= $this->get_date( [
				'class' => 'wpex-flex wpex-items-center',
				'type' => 'published',
				'link' => false,
				'icon' => 'material-schedule',
				'icon_class' => 'wpex-mr-10 wpex-text-accent',
			] );

			$html .= $this->get_comment_count( [
				'class' => 'wpex-flex wpex-items-center',
				'link' => false,
				'icon' => 'material-chat-outline',
				'icon_class' => 'wpex-mr-10 wpex-text-accent',
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
