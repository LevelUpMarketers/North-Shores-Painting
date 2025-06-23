<?php

if ( ! empty( $attributes['hidden_fields'] ) && \is_string( $attributes['hidden_fields'] ) ) {
    $attributes['hidden_fields'] = \str_replace( "\n", ",", $attributes['hidden_fields'] );
}

$wrap_class = 'wp-block-vcex-newsletter-form';

if ( ! empty( $attributes['className'] ) ) {
    $wrap_class .= " {$attributes['className']}";
}

echo '<div class="' . esc_attr( trim( $wrap_class ) ) . '">' . vcex_do_shortcode_function( 'vcex_newsletter_form', $attributes ) . '</div>';