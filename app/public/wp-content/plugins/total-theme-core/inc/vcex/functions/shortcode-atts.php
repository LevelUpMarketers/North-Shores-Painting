<?php

defined( 'ABSPATH' ) || exit;

/**
 * Get shortcode attributes.
 */
function vcex_shortcode_atts( $shortcode = '', $atts = '', $class = null ) {
	if ( $class && is_callable( array( $class, 'parse_deprecated_attributes' ) ) ) {
		$atts = $class::parse_deprecated_attributes( $atts ); // Parse deprecated attributes (must run first)
	}

	// Fix inline shortcodes - @see WPBakeryShortCode => prepareAtts()
	if ( is_array( $atts ) ) {
		foreach ( $atts as $key => $val ) {
			if ( ! is_string( $val ) ) {
				continue;
			}
			$atts[ $key ] = str_replace( [
				'`{`',
				'`}`',
				'``',
			], [
				'[',
				']',
				'"',
			], $val );
		}
	}

	// Check for the is_elementor_widget att before parsing the attributes so we can re add it later
	$is_elementor_widget = ! empty( $atts['is_elementor_widget'] );

	// Check Elementor global settings
	if ( $is_elementor_widget && ! empty( $atts['__globals__'] ) && is_array( $atts['__globals__'] ) ) {
		$elementor_globals = $atts['__globals__'];
	}

	// Parse shortcode attributes
	if ( function_exists( 'vc_map_get_attributes' ) ) {
		$atts = vc_map_get_attributes( $shortcode, $atts ); // !important!! must use WPBakery function to support vc_add_param
	} else {
		$atts = shortcode_atts( vcex_shortcode_class_attrs( $class ), $atts, $shortcode );
		$atts = (array) apply_filters( 'vc_map_get_attributes', $atts, $shortcode );
	}

	// Add elementor globals to shortcode attributes
	if ( isset( $elementor_globals ) ) {
		foreach ( $elementor_globals as $eg_k => $eg_v ) {
			if ( isset( $atts[ $eg_k ] ) && ! $atts[ $eg_k ] && is_string( $eg_v ) && str_starts_with( $eg_v, 'globals/colors?id=' ) ) {
				$color = str_replace( 'globals/colors?id=', '', $eg_v );
				if ( $color ) {
					$atts[ $eg_k ] = "var(--e-global-color-{$color})";
				}
			}
		}
	}

	// Re-add the is_elementor check
	$atts['is_elementor_widget'] = $is_elementor_widget;

	// Apply filters and return shortcode attributes
	return (array) apply_filters( 'vcex_shortcode_atts', $atts, $shortcode );
}

/**
 * Returns all shortcode atts and default values.
 */
function vcex_shortcode_class_attrs( $class ) {
	$atts = [];

	if ( is_callable( [ $class, 'get_params' ] ) ) {
		$params = $class::get_params();
	} elseif ( is_object( $class ) && is_callable( [ $class, 'map' ] ) ) {
		$map = $class->map();
		$params = $map['params'] ?? null;
	}

	if ( isset( $params ) && is_array( $params ) ) {
		foreach ( $params as $param ) {
			if ( isset( $param['param_name'] ) && 'content' !== $param['param_name'] ) {
				$value = '';
				if ( isset( $param['std'] ) ) {
					$value = $param['std'];
				} elseif ( isset( $param['value'] ) ) {
					if ( is_array( $param['value'] ) ) {
						$value = current( $param['value'] );
						if ( is_array( $value ) ) {
							// in case if two-dimensional array provided (vc_basic_grid)
							$value = current( $value );
						}
						// return first value from array (by default)
					} else {
						$value = $param['value'];
					}
				}
				if ( function_exists( 'vc_map_get_attributes' ) ) {
					$atts[ $param['param_name'] ] = apply_filters( 'vc_map_get_param_defaults', $value, $param );
				} else {
					$atts[ $param['param_name'] ] = $value;
				}
			}
		}
	}

	return $atts;
}

/**
 * Helper function returns a shortcode attribute with a fallback.
 */
function vcex_shortcode_att( $atts, $att, $default = '' ) {
	return $atts[ $att ] ?? $default;
}
