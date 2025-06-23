<?php

if ( ( \defined( '\REST_REQUEST' ) && true === \REST_REQUEST ) ) {
    if ( ! empty( $_GET['postId'] ) ) {
        /**
         * Fix for Gutenberg issue.
         *
         * @see https://github.com/WordPress/gutenberg/issues/34882
         * @see https://github.com/WordPress/gutenberg/issues/40714
         */
        $post_id = \absint( \sanitize_text_field( \wp_unslash( $_GET['postId'] ) ) );
    } elseif ( ! empty( $block->context['postId'] ) ) {
        $post_id = \absint( \sanitize_text_field( $block->context['postId'] ) );
    }
}

$wrap_class = 'wp-block-vcex-post-meta';

if ( ! empty( $attributes['className'] ) ) {
    $wrap_class .= ' ' . esc_attr( $attributes['className'] );
}

if ( isset( $attributes['textAlign'] ) ) {
    $wrap_class .= ' has-text-align-' . \sanitize_html_class( $attributes['textAlign'] );
}

if ( isset( $post_id ) ) {
    global $post;
    $og_post = $post;
    $post = get_post( $post_id );
}

echo '<div class="' . esc_attr( trim( $wrap_class ) ) . '">' . vcex_do_shortcode_function( 'vcex_post_meta', $attributes ) . '</div>';

if ( isset( $og_post ) ) {
    $post = $og_post;
}