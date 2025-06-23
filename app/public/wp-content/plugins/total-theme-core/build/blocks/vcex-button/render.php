<?php

$attributes['text_source'] = 'custom';
$content = $attributes['content'] ?? '';

if ( isset( $attributes['target_blank'] ) && true === wp_validate_boolean( $attributes['target_blank'] ) ) {
    $attributes['target'] = '_blank';
    unset( $attributes['target_blank'] );
}

if ( isset( $attributes['expanded'] ) && true === wp_validate_boolean( $attributes['expanded'] ) ) {
    $attributes['layout'] = 'expanded';
    unset( $attributes['expanded'] );
}

$wrap_class = 'wp-block-vcex-button';

if ( ! empty( $attributes['className'] ) ) {
    $wrap_class .= " {$attributes['className']}";
}

echo '<div class="' . esc_attr( trim( $wrap_class ) ) . '">' . vcex_do_shortcode_function( 'vcex_button', $attributes, $content ) . '</div>';