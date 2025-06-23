<?php
/**
 * Flex Container shortcode template.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$html             = '';
$unique_class     = vcex_element_unique_classname( 'vcex-flex-container' );
$flex_direction   = $atts['flex_direction'] ?: 'row';
$stack_breakpoint = $atts['row_stack_bp'] ?? '';
$breakpoints      = [ 'xl', 'lg', 'md', 'sm' ];
$will_stack       = ( $stack_breakpoint && in_array( $stack_breakpoint, $breakpoints ) );

$wrap_class = [
	'vcex-flex-container',
	'vcex-module',
	'wpex-flex',
];

if ( empty( $atts['el_class'] ) || ! str_contains( (string) $atts['el_class'], '-gap-' ) ) {
	$wrap_class[] = 'wpex-gap-20';
}

if ( ! empty( $atts['width'] ) ) {
	$align = ! empty( $atts['align'] ) ? $atts['align'] : 'center';
	$wrap_class[] = vcex_parse_align_class( $align );
}

if ( vcex_validate_att_boolean( 'flex_wrap', $atts ) ) {
	$wrap_class[] = 'wpex-flex-wrap';
}

if ( vcex_validate_att_boolean( 'flex_grow', $atts ) ) {
	$wrap_class[] = 'vcex-flex-container--items_grow';
}

if ( 'column' === $flex_direction ) {
	$wrap_class[] = 'wpex-flex-col';
} elseif ( $will_stack ) {
	if ( 'true' == $atts['row_stack_reverse'] ) {
		$wrap_class[] = 'wpex-flex-col-reverse';
	} else {
		$wrap_class[] = 'wpex-flex-col';
	}
	$wrap_class[] = 'wpex-' . sanitize_html_class( $stack_breakpoint ) . '-flex-row';
}

if ( ! empty( $atts['align_items'] ) ) {
	$align_items_bk = ( $will_stack ) ? $stack_breakpoint : '';
	$wrap_class[] = vcex_parse_align_items_class( $atts['align_items'], $align_items_bk );
}

if ( ! empty( $atts['justify_content'] ) ) {
	$justify_content_bk = ( $will_stack ) ? $stack_breakpoint : '';
	$wrap_class[] = vcex_parse_justify_content_class( $atts['justify_content'], $justify_content_bk );
}

if ( ! empty( $atts['shadow'] ) ) {
	$wrap_class[] = vcex_parse_shadow_class( $atts['shadow'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'] );
}

if ( ! empty( $atts['css'] ) ) {
	$wrap_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
}

$style = '';

$parent_style = vcex_inline_style( [
	'gap'       => $atts['gap'] ?? '',
	'max_width' => $atts['width'] ?? '',
], false );

// Min-Height: Needs to be added here because of WPBakery bugs we need an important attribute.
if ( ! empty( $atts['min_height'] ) ) {
	$min_height_safe = is_numeric( $atts['min_height'] ) ? "{$atts['min_height']}.px" : esc_attr( $atts['min_height']);
	$parent_style .= "min-height:{$min_height_safe}!important;";
}

if ( $parent_style ) {
	$style .= '.' . $unique_class . '{' . $parent_style . '}';
}

// Define breakpoints.
switch ( $stack_breakpoint ) {
	case 'xl':
		$stack_bp = '1280px';
		break;
	case 'lg':
		$stack_bp = '1024px';
		break;
	case 'md':
		$stack_bp = '768px';
		break;
	case 'sm':
		$stack_bp = '640px';
		break;
}

// Add flex basis CSS.
if ( 'row' === $flex_direction && ! empty( $atts['flex_basis'] ) ) {
	$flex_basis = $atts['flex_basis'];
	if ( $will_stack ) {
		$style .= '@media only screen and (min-width: ' . esc_attr( $stack_bp ) . ') {';
	}
	if ( is_string( $flex_basis ) && false !== strpos( $flex_basis, ',' ) ) {
		$flex_basis = explode( ',', $flex_basis );
		$count = 0;
		foreach ( $flex_basis as $flex_basis_item ) {
			$count++;
			$flex_basis_item = trim( $flex_basis_item );
			$flex_basis_item_shrink_grow = ''; // reset for each item.
			if ( 'auto' !== $flex_basis_item ) {
				if ( is_numeric( $flex_basis_item ) && 0 !== $flex_basis_item ) {
					$flex_basis_item = $flex_basis_item . 'px';
				}
				$flex_basis_item_shrink_grow = 'flex-grow:0;flex-shrink:1;'; // must allow shrink to prevent issues on smaller devices when flex_wrap is enabled.
			}
			$style .= '.' . $unique_class . ' > *:nth-child(' . $count . ') {flex-basis:' . esc_attr( $flex_basis_item ) . ';' . $flex_basis_item_shrink_grow . '}';
		}

	} else {
		if ( 'auto' === $flex_basis ) {
			$style .= '.' . $unique_class . ' > * {flex-basis:' . esc_attr( $flex_basis ) . ';}';
		} else {
			$style .= '.' . $unique_class . ' > * {flex-basis:' . esc_attr( $flex_basis ) . ';flex-grow:0;flex-shrink:1;}';
		}
	}

	if ( $will_stack ) {
		$style .= '}';
	}

}

// Scacked CSS
if ( $will_stack ) {

	$stack_bp = absint( $stack_bp ) - 1 . 'px'; // remove 1px from breakpoint

	$stack_css = '';

	if ( ! empty( $atts['row_stack_gap'] ) ) {
		if ( is_numeric( $atts['row_stack_gap'] ) ) {
			$atts['row_stack_gap'] = $atts['row_stack_gap'] . 'px';
		}
		$stack_css .= '.' . $unique_class . '{gap:' . esc_attr( $atts['row_stack_gap'] ) . ';}';
	}

	if ( $stack_css ) {
		$style .= '@media only screen and (max-width: ' . esc_attr( $stack_bp ) . ') {' . trim( $stack_css ) . '}';
	}

}

if ( $style ) {
	$wrap_class[] = $unique_class;
	$html .= '<style>' . $style . '</style>';
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_flex_container', $atts );

$html .= '<div class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $atts['unique_id'] ) . '>';

	$html .= do_shortcode( wp_kses_post( $content ) );

$html .= '</div>';

echo $html; // @codingStandardsIgnoreLine
