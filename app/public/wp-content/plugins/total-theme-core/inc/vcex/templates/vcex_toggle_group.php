<?php

/**
 * Toggle Group.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! $content ) {
	return;
}

$output    = '';
$vc_inline = vcex_vc_is_inline();
$style     = ( ! empty( $atts['style'] ) && 'none' !== $atts['style'] ) ? sanitize_text_field( $atts['style'] ) : '';

$wrap_class = [
	'vcex-toggle-group',
];

if ( $vc_inline ) {
	if ( ! empty( $atts['icon_position'] ) && 'right' === $atts['icon_position'] ) {
		$wrap_class[] = 'vcex-toggle-group--icon-end';
	}
	if ( vcex_validate_att_boolean( 'heading_inline', $atts ) ) {
		$wrap_class[] = 'vcex-toggle-group--heading-inline';
	}
}

if ( $style ) {
	$wrap_class[] = 'vcex-toggle-group--' . sanitize_html_class( $style );
	if ( 'w-borders' === $style ) {
		if ( vcex_validate_att_boolean( 'no_top_border', $atts ) ) {
			$wrap_class[] = 'vcex-toggle-group--no-top-border';
		}
		if ( vcex_validate_att_boolean( 'no_bottom_border', $atts ) ) {
			$wrap_class[] = 'vcex-toggle-group--no-bottom-border';
		}
	}
}

$wrap_class[] = 'vcex-module';
$wrap_class[] = 'wpex-mx-auto';

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_toggle_group' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_toggle_group', $atts );

$output .= '<div' . vcex_get_unique_id( $atts ) . ' class="' . esc_attr( $wrap_class ) . '">';
	if ( $vc_inline ) {
		$output .= do_shortcode( $content );
	} else {
		if ( vcex_validate_att_boolean( 'parse_content', $atts, true ) ) {
			$regex = get_shortcode_regex( [ 'vcex_toggle' ] );
			preg_match_all( "/{$regex}/", $content, $matches );
			if ( empty( $matches[0] ) ) {
				return '';
			}
			$content_sanitized = implode( ' ', $matches[0] );
			$single_attrs = [
				'animate'        => ! empty( $atts['animate'] ) ? sanitize_text_field( $atts['animate']  ) : '',
				'faq_microdata'  => ! empty( $atts['faq_microdata'] ) ? sanitize_text_field( $atts['faq_microdata']  ) : '',
				'icon_type'      => ( ! empty( $atts['icon_type'] ) && 'angle' === $atts['icon_type'] ) ? 'angle' : '',
				'icon_position'  => ( ! empty( $atts['icon_position'] ) && 'right' === $atts['icon_position'] ) ? 'right' : '',
				'heading_inline' => ( ! empty( $atts['heading_inline'] ) && 'true' === $atts['heading_inline'] ) ? 'true' : '',
			];
			foreach ( array_filter( $single_attrs ) as $k => $v ) {
				$content_sanitized = str_replace( '[vcex_toggle', '[vcex_toggle ' . $k . '="' . $v . '"', $content_sanitized );
			}
			$output .= do_shortcode( $content_sanitized );
		} else {
			$output .= $content;
		}
	}
$output .= '</div>';

echo $output; // @codingStandardsIgnoreLine
