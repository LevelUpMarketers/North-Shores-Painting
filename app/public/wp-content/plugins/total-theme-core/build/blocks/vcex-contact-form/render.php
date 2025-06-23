<?php

$wrap_class = 'wp-block-vcex-contact-form';

if ( ! empty( $attributes['className'] ) ) {
    $wrap_class .= " {$attributes['className']}";
}

echo '<div class="' . esc_attr( trim( $wrap_class ) ) . '">' . vcex_do_shortcode_function( 'vcex_contact_form', $attributes ) . '</div>';