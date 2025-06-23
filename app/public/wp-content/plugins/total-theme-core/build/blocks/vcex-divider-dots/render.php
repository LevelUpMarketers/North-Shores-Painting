<?php

if ( ! empty( $attributes['margin'] ) ) {
    if ( in_array( $attributes['margin'], [ '0px', '5px', '10px', '15px', '20px', '25px', '30px', '40px', '50px' ], true ) ) {
        $attributes['margin'] = "{$attributes['margin']} 0";
    }
}

$wrap_class = 'wp-block-vcex-divider-dots';

if ( ! empty( $attributes['className'] ) ) {
    $wrap_class .= " {$attributes['className']}";
}

echo '<div class="' . esc_attr( trim( $wrap_class ) ) . '">' . vcex_do_shortcode_function( 'vcex_divider_dots', $attributes ) . '</div>';