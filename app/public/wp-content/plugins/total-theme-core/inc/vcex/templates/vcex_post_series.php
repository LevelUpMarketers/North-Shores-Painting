<?php
/**
 * vcex_post_series shortcode output
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

$wrap_class = [
    'vcex-module',
    'vcex-post-series',
];

if ( ! empty( $atts['max_width'] ) ) {
    $wrap_class[] = vcex_parse_align_class( ! empty( $atts['align'] ) ? $atts['align'] : 'center' );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_post_series', $atts );

$output = '<div class="' . esc_attr( $wrap_class ) . '">';
    if ( vcex_is_template_edit_mode() ) {
        $output .= '<div class="wpex-post-series-toc wpex-boxed wpex-p-30">';
		     $output .= '<div class="wpex-post-series-toc-header wpex-text-1 wpex-text-xl wpex-font-semibold wpex-mb-15">' . \esc_html__( 'Sample Post Series', 'total-theme-core' ) . '</div>';
		    $output .= '<div class="wpex-post-series-toc-list wpex-last-mb-0">';
                $output .= '<div class="wpex-post-series-toc-entry wpex-mb-5"><span class="wpex-post-series-toc-number post-series-count wpex-font-medium">1.</span> <span class="wpex-post-series-toc-active">' .  \esc_html__( 'Sample Post Title', 'total-theme-core' ) . '</span></div>';
                $output .= '<div class="wpex-post-series-toc-entry wpex-mb-5"><span class="wpex-post-series-toc-number post-series-count wpex-font-medium">2.</span> <a href="#">' .  \esc_html__( 'Sample Post Title', 'total-theme-core' ) . '</a></div>';
                $output .= '<div class="wpex-post-series-toc-entry wpex-mb-5"><span class="wpex-post-series-toc-number post-series-count wpex-font-medium">3.</span> <a href="#">' .  \esc_html__( 'Sample Post Title', 'total-theme-core' ) . '</a></div>';
            $output .= '</div>';
        $output .= '</div>';
    } elseif ( function_exists( 'wpex_get_template_part' ) ) {
        ob_start();
            wpex_get_template_part( 'post_series' );
        $output .= ob_get_clean();
    }
$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
