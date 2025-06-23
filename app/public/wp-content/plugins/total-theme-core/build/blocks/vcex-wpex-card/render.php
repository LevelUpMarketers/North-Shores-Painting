<?php
$is_gutenberg = \defined( '\REST_REQUEST' ) && true === \REST_REQUEST;

if ( $is_gutenberg ) {
    $attributes['url'] = '#void';
    $attributes['link_type'] = 'none';
}

if ( ! empty( $_GET['postId'] ) && $is_gutenberg ) {
    /**
     * Fix for Gutenberg issue.
     *
     * @see https://github.com/WordPress/gutenberg/issues/34882
     * @see https://github.com/WordPress/gutenberg/issues/40714
     */
    $attributes['post_id'] = \absint( \sanitize_text_field( \wp_unslash( $_GET['postId'] ) ) );
} elseif ( ! empty( $block->context['postId'] ) ) {
    $attributes['post_id'] = \absint( \sanitize_text_field( $block->context['postId'] ) );
} else {
    $attributes['post_id'] = get_the_ID();
}

if ( isset( $attributes['excerpt_length'] ) && '' === $attributes['excerpt_length'] ) {
    unset( $attributes['excerpt_length'] );
}

if ( ! empty( $attributes['card_style'] ) ) {
    $attributes['style'] = sanitize_text_field( $attributes['card_style'] );
}

if ( function_exists( 'wpex_card' ) ) {
    wpex_card( array_map( 'sanitize_text_field', $attributes ) );
}