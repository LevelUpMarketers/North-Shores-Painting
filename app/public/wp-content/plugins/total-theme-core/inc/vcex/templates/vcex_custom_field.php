<?php

/**
 * vcex_custom_field shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $atts['name'] ) ) {
	return;
}

$output            = '';
$cf_value          = '';
$custom_field_name = $atts['name'];

// Get value from ACF.
if ( function_exists( 'acf_is_field_key' ) && acf_is_field_key( $custom_field_name ) ) {

	if ( function_exists( 'get_field_object' ) ) {
		$field_obj = get_field_object( $custom_field_name );
		if ( is_array( $field_obj ) ) {
			$acf_field_type = $field_obj['type'] ?? '';
			$acf_return_format = $field_obj['return_format'] ?? '';
			if ( 'link' === $acf_field_type && 'array' === $acf_return_format ) {
				if ( ! empty( $field_obj['value']['url'] ) && ! empty( $field_obj['value']['title'] ) ) {
					$cf_value = vcex_parse_html( 'a', [
						'href'   => esc_url( $field_obj['value']['url'] ),
						'target' => $field_obj['value']['target'] ?? '',
					], esc_html( $field_obj['value']['title'] ) );
				}
			}
		}
	}

	if ( ! $cf_value ) {
		$cf_value = vcex_get_acf_field( $custom_field_name );
		if ( $cf_value ) {
			// We always need a string value so if it's returning an array lets convert it to a string.
			if ( is_array( $cf_value ) ) {
				$cf_value = implode( ',', $cf_value );
			}
			$cf_value = wp_kses_post( $cf_value );
		}
	}

}

// Get value using core WP functions.
if ( empty( $cf_value ) && 0 !== $cf_value ) {
	$cf_value = vcex_get_meta_value( $custom_field_name );
	if ( $cf_value && is_string( $cf_value ) ) {
		$cf_value = wp_kses_post( $cf_value );
	}
}

// Parses the value based on user callback.
if ( ! empty( $atts['parse_callback'] )
	&& is_callable( $atts['parse_callback'] )
	&& vcex_validate_user_func( $atts['parse_callback'] )
) {
	$cf_value = call_user_func( $atts['parse_callback'], $cf_value, $custom_field_name );
}

// Fallback value.
if ( empty( $cf_value ) && 0 !== $cf_value && ! empty( $atts['fallback'] ) ) {
	$cf_value = wp_kses_post( $atts['fallback'] );
}

if ( ! $cf_value && vcex_is_template_edit_mode() ) {
	$cf_value = vcex_custom_field_placeholder( $custom_field_name );
}

// No need to show anything if value is empty.
if ( empty( $cf_value ) || ! is_string( $cf_value ) ) {
	return;
}

$tag_escaped = ! empty( $atts['tag'] ) ? tag_escape( $atts['tag'] ) : 'div';

// Shortcode Classes.
$shortcode_class = [
	'vcex-custom-field',
	'vcex-module',
	'wpex-clr',
];

if ( isset( $atts['heading_margin'] )
	&& 'false' == $atts['heading_margin']
	&& ! in_array( $tag_escaped, [ 'div', 'span' ] )
) {
	$shortcode_class[] = 'wpex-my-0';
}

if ( ! empty( $atts['align'] ) ) {
	$shortcode_class[] = vcex_parse_text_align_class( $atts['align'] );
}

if ( vcex_validate_att_boolean( 'italic', $atts ) ) {
	$shortcode_class[] = 'wpex-italic';
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_custom_field' );

if ( $extra_classes ) {
	$shortcode_class = array_merge( $shortcode_class, $extra_classes );
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_custom_field', $atts );

// Shortcode Output.
$output .= '<' . $tag_escaped . ' class="' . esc_attr( $shortcode_class ) . '">';

	$icon_html = vcex_get_icon_html( $atts, 'icon' );

	if ( $icon_html ) {
		$icon_font_family = $atts['icon_type'] ?? '';
		$icon_side_margin = ! empty( $atts['icon_side_margin'] ) ? sanitize_text_field( $atts['icon_side_margin'] ) : '10';
		$output .= '<span class="vcex-custom-field-icon wpex-inline-block wpex-mr-' . absint( $icon_side_margin ) . '">';
			$output .= $icon_html;
		$output .= '</span>';
	}

	if ( ! empty( $atts['before'] ) ) {
		$before_class = 'vcex-custom-field-before';

		if ( ! empty( $atts['before_el_class'] ) ) {
			$before_class .= ' ' . vcex_get_extra_class( $atts['before_el_class'] );
		}

		if ( empty( $atts['before_el_class'] ) || false === strpos( $atts['before_el_class'], 'wpex-font-' ) ) {
			$before_font_weight = ! empty( $atts['before_font_weight'] ) ? $atts['before_font_weight'] : 'bold';
			$before_font_weight_class = vcex_parse_font_weight_class( $before_font_weight );

			if ( $before_font_weight_class ) {
				$before_class .= ' ' . $before_font_weight_class;
			}
		}

		$output .= '<span class="' . esc_attr( trim( $before_class ) ) . '">' . vcex_parse_text_safe( $atts['before'] ) . '</span> ';
	}

	if ( vcex_validate_att_boolean( 'autop', $atts ) ) {
		$cf_value = do_shortcode( shortcode_unautop( wpautop( $cf_value ) ) );
	} else {
		$cf_value = do_shortcode( $cf_value );
	}

	/**
	 * Filters the vcex_custom_field shortcode custom field value.
	 *
	 * @param string $cf_value
	 * @param array $shortcode_attributes
	 */
	$content = (string) apply_filters( 'vcex_custom_field_value_output', $cf_value, $atts );

	$prefix = ! empty( $atts['prefix'] ) ? trim( vcex_parse_text_safe( $atts['prefix'] ) ) : '';
	$suffix = ! empty( $atts['suffix'] ) ? trim( vcex_parse_text_safe( $atts['suffix'] ) ) : '';

	if ( vcex_validate_att_boolean( 'affix_spacing', $atts, true ) ) {
		$prefix = $prefix ? "{$prefix} " : '';
		$suffix = $suffix ? " {$suffix}" : '';
	}

	$output .= $prefix . $content . $suffix;

$output .= "</{$tag_escaped}>";

echo $output; // @codingStandardsIgnoreLine
