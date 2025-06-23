<?php

defined( 'ABSPATH' ) || exit;

$html = '';

if ( $this->term ) {
	$tagged = get_taxonomy( $this->term->taxonomy )->labels->name ?? '';
} else {
	$post_type = get_post_type( $this->post_id );
	if ( ! $post_type || 'post' === $post_type ) {
		if ( 'post' === $post_type && $primary_term = totaltheme_get_post_primary_term( $this->post_id ) ) {
			$tagged = $primary_term->name ?? $post_type;
		} else {
			$tagged = esc_html__( 'Articles', 'total' );
		}
	} else {
		$tagged = get_post_type_object( $post_type )->labels->name ?? ucfirst( $post_type );
	}
}

$html .= $this->get_element( [
	'class' => 'wpex-card-tags wpex-text-xs wpex-opacity-60 wpex-uppercase wpex-tracking-wider wpex-font-medium',
	'content' => '<span>' . esc_html( $tagged ) . '</span>',
] );

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-lg wpex-m-0',
] );

$html .= $this->get_excerpt( [
	'class' => 'wpex-mt-5'
] );

return $html;
