<?php

$content = ! empty( $attributes['content'] ) ? $attributes['content'] : esc_html__( 'Widget Title', 'total-theme-core' );

if ( isset( $attributes['location'] ) && 'footer' === $attributes['location'] ) {
    $class = 'TotalTheme\Footer\Widgets';
} else {
    $class = 'TotalTheme\Sidebars\Primary';
}

if ( \class_exists( $class ) && \is_callable( [ $class, 'widget_title_args' ] ) ) {
    echo $class::widget_title_args()['before'] . wp_kses_post( do_shortcode( $content ) ) . $class::widget_title_args()['after'];
}
