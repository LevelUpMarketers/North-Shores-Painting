<?php
defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-justify-between wpex-items-start wpex-gap-20">';

	$html .= '<div class="wpex-card-details wpex-flex-grow">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-lg wpex-m-0',
		] );

		if ( $this->term ) {
			$tagged = get_taxonomy( $this->term->taxonomy )->labels->name ?? '';
		} else {
			$post_type = get_post_type( $this->post_id );
			if ( ! $post_type || 'post' === $post_type ) {
				$tagged = esc_html__( 'Articles', 'total' );
			} else {
				$tagged = get_post_type_object( $post_type )->labels->name ?? ucfirst( $post_type );
			}
		}

		$html .= $this->get_element( [
			'class' => 'wpex-card-trail wpex-opacity-60 wpex-text-sm',
			'content' => esc_html( 'Home', 'total' ) . ' &rsaquo; ' . esc_html( $tagged ),
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-mt-5'
		] );

	$html .= '</div>';

	$thumbnail_args = [
		'class' => 'wpex-flex-shrink-0 wpex-w-20 wpex-p-3 wpex-border wpex-border-solid wpex-border-gray-200',
		'css' => 'padding:2px;',
		'image_class' => 'wpex-w-100',
	];
	if ( empty( $this->args['media_width'] ) ) {
		$thumbnail_args['css'] .= 'max-width:63px;';
	}
	$html .= $this->get_thumbnail( $thumbnail_args );

$html .= '</div>';

return $html;
