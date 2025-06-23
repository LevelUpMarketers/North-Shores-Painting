<?php

$wrap_class = 'wp-block-vcex-widget-modern-menu';

if ( ! empty( $attributes['className'] ) ) {
    $wrap_class .= " {$attributes['className']}";
}

echo '<div class="' . esc_attr( trim( $wrap_class ) ) . '">' . vcex_do_shortcode_function( 'vcex_widget_modern_menu', $attributes ) . '</div>';