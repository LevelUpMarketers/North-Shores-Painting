<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Parses a color to return correct value.
 */
function vcex_parse_color( $color = '' ) {
	return function_exists( 'wpex_parse_color' ) ? wpex_parse_color( $color ) : $color;
}

/**
 * Parses a direction for RTL compatibility.
 */
function vcex_parse_direction( $direction = '' ) {
	if ( $direction && is_rtl() ) {
		$direction = ( $direction === 'left' ) ? 'right' : ( ( $direction === 'right' ) ? 'left' : $direction );
	}
	return $direction;
}

/**
 * Parses multi attribute setting.
 */
function vcex_parse_multi_attribute( $value = '', $default = [] ) {
	$result = $default;
	if ( $value ) {
		$params_pairs = explode( '|', $value );
		if ( ! empty( $params_pairs ) ) {
			foreach ( $params_pairs as $pair ) {
				$param = preg_split( '/\:/', $pair );
				if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
					if ( 'http' === $param[1] && isset( $param[2] ) ) {
						$param[1] = rawurlencode( 'http:' . $param[2] ); // fix for incorrect urls that are not encoded
					}
					$result[ $param[0] ] = rawurldecode( $param[1] );
				}
			}
		}
	}
	return $result;
}

/**
 * Parses textarea HTML.
 */
function vcex_parse_textarea_html( $html = '' ) {
	if ( $html && base64_decode( $html, true ) ) {
		return rawurldecode( base64_decode( strip_tags( $html ) ) );
	}
	return $html;
}



/**
 * Parses the font_control / typography param (used for mapper and front-end).
 */
function vcex_parse_typography_param( $value ) {
	$defaults = [
		'tag'               => '',
		'text_align'        => '',
		'font_size'         => '',
		'line_height'       => '',
		'color'             => '',
		'font_style_italic' => '',
		'font_style_bold'   => '',
		'font_family'       => '',
		'letter_spacing'    => '',
		'font_family'       => '',
	];
	if ( ! function_exists( 'vc_parse_multi_attribute' ) ) {
		return $defaults;
	}
	$values = wp_parse_args( vc_parse_multi_attribute( $value ), $defaults );
	return $values;
}

/*-------------------------------------------------------------------------------*/
/* [ HTML Attributes ]
/*-------------------------------------------------------------------------------*/

/**
 * Wrapper for wpex_parse_html.
 */
function vcex_parse_html( string $tag = '', array $attrs = [], string $content = '' ) {
	if ( function_exists( 'wpex_parse_html' ) ) {
		return wpex_parse_html( $tag, $attrs, $content );
	}
}

/**
 * Wrapper for the wpex_parse_attrs function()
 */
function vcex_parse_html_attributes( array $attrs = [] ) {
	if ( function_exists( 'wpex_parse_attrs' ) ) {
		$attributes_string = trim( wpex_parse_attrs( $attrs ) );
		return " {$attributes_string}"; // note: always include space at the front!
	} else {
		$string = '';
		foreach ( $attrs as $k => $v ) {
			if ( $v ) {
				if ( is_array( $v ) ) {
					$v = implode( ' ', $v );
				}
				$string .= ' ' . sanitize_key( $k ) . '="' . esc_attr( $v ) . '"';
			}
		}
		return ' ' . trim( $string );
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Lightbox Data ]
/*-------------------------------------------------------------------------------*/

/**
 * Parses the inline gallery lightbox html.
 */
function vcex_parse_inline_lightbox_gallery( $attachements = '' ) {
	if ( function_exists( 'wpex_parse_inline_lightbox_gallery' ) ) {
		return wpex_parse_inline_lightbox_gallery( $attachements );
	}
}

/**
 * Parses lightbox dimensions.
 */
function vcex_parse_lightbox_dims( $dims = '', $return = '' ) {
	if ( ! $dims ) {
		return;
	}

	$dims = explode( 'x', $dims );
	$w    = isset( $dims[0] ) ? absint( $dims[0] ) : null;
	$h    = isset( $dims[1] ) ? absint( $dims[1] ) : null;

	if ( ! $w || ! $h ) {
		return;
	}

	switch ( $return ) {
		case 'width' :
			return $w;
			break;
		case 'height' :
			return $h;
			break;
		case 'array' :
			return [
				'width'  => $w,
				'height' => $h,
			];
			break;
		default :
			return "width:{$w},height:{$h}"; // old deprecated
			break;
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Classnames ]
/*-------------------------------------------------------------------------------*/

/**
 * Parses the typography style class.
 */
function vcex_parse_typography_style_class( string $style = '' ): string {
	return $style ? sanitize_html_class( $style ) : '';
}

/**
 * Parses the shortcode classes.
 */
function vcex_parse_shortcode_classes( $class = '', $shortcode_base = '', $atts = '' ): string {
	if ( is_array( $class ) ) {
		$class = trim( implode( ' ', array_unique( array_filter( $class ) ) ) );
	}

	if ( ! empty( $atts['vcex_class'] ) ) {
		$class .= " {$atts['vcex_class']}";
	}

	// @todo deprecate?
	if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
		/**
		 * Filters the shortcode class when WPBakery is active.
		 *
		 * @param string $class
		 * @param string $shortcode_base
		 * @param array $atts
		 */
		$class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $shortcode_base, $atts );
	}

	/**
	 * Filters the shortcode class.
	 *
	 * @param string $class
	 * @param string $shortcode_base
	 * @param array $atts
	 */
	$class = apply_filters( 'vcex_shortcodes_css_class', $class, $shortcode_base, $atts );

	return (string) $class;
}

/**
 * Return aspect ratio class.
 */
function vcex_parse_aspect_ratio_class( string $aspect_ratio = '' ): string {
	$class = '';
	$allowed = [ '1/1', '1-1', '4/3', '4-3', '3/4', '3-4', '3/2', '3-2', '2/3', '2-3', '16/9', '16-8', '9/16', '9-16' ];
	if ( $aspect_ratio && in_array( $aspect_ratio, $allowed, true ) ) {
		$aspect_ratio = str_replace( '/', '-', $aspect_ratio );
		$class = "wpex-aspect-{$aspect_ratio}";
	}
	return (string) apply_filters( 'vcex_aspect_ratio_class', $class, $aspect_ratio );
}

/**
 * Return object fit class.
 */
function vcex_parse_object_fit_class( $object_fit = '' ): string {
	$class = '';
	if ( in_array( $object_fit, [ 'fill', 'contain', 'cover', 'scale-down' ], true ) ) {
		$class = "wpex-object-{$object_fit}";
	}
	return (string) apply_filters( 'vcex_object_fit_class', $class, $object_fit );
}

/**
 * Return object position class.
 */
function vcex_parse_object_position_class( $position = '' ) {
	$class = '';
	$allowed = [ 'top', 'center', 'bottom', 'left-top', 'left-center', 'left-bottom', 'right-top', 'right-center', 'right-bottom' ];
	if ( in_array( $position, $allowed, true ) ) {
		return "wpex-object-{$position}";
	}
}

/**
 * Return mix blend mode class.
 */
function vcex_parse_mix_blend_mode_class( $mode = '' ) {
	$class = '';
	$allowed = [ 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'hard-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity', 'plus-lighter', 'multiply', 'screen', 'darken', 'overlay' ];
	if ( in_array( $mode, $allowed, true ) ) {
		return "wpex-mix-blend-{$mode}";
	}
}

/**
 * Returns text transform classname.
 */
function vcex_parse_text_transform_class( $transform = '' ): string {
	$class = '';
	if ( in_array( $transform, [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ) {
		if ( 'none' === $transform ) {
			$transform = 'normal-case';
		}
		$safe_transform = sanitize_html_class( $transform );
		$class = "wpex-{$safe_transform}";
	}
	return (string) apply_filters( 'vcex_text_transform_class', $class, $transform );
}

/**
 * Returns align class based on alignment.
 */
function vcex_parse_align_class( $align = '' ): string {
	$class = '';
	if ( $align && 'none' !== $align ) {
		if ( ! vcex_is_bidirectional() ) {
			$align = vcex_parse_direction( $align ); // swap direction to opposite since the util classes are direction aware.
		}
		$class_map = [
			'left'   => 'wpex-mr-auto',
			'center' => 'wpex-mx-auto',
			'right'  => 'wpex-ml-auto',
		];
		$class = $class_map[ $align ] ?? '';
	}
	return (string) apply_filters( 'vcex_align_class', $class, $align );
}

/**
 * Parses text_align class.
 */
function vcex_parse_text_align_class( $align = '' ): string {
	$class = '';
	if ( $align && 'none' !== $align && in_array( $align, [ 'left', 'center', 'right', 'justify' ], true ) ) {
		if ( ! vcex_is_bidirectional() ) {
			$align = vcex_parse_direction( $align ); // swap direction to opposite since the util classes are direction aware.
		}
		$class = "wpex-text-{$align}";
	}
	return (string) apply_filters( 'vcex_text_align_class', $class, $align );
}

/**
 * Parses justify_content class.
 */
function vcex_parse_justify_content_class( $position = '', $prefix = '' ) {
	if ( ! $position ) {
		return;
	}
	$class_map = [
		'left'          => 'justify-start',
		'start'         => 'justify-start',
		'center'        => 'justify-center',
		'right'         => 'justify-end',
		'end'           => 'justify-end',
		'between'       => 'justify-between',
		'space-between' => 'justify-between',
		'around'        => 'justify-around',
		'space-around'  => 'justify-around',
		'evenly'        => 'justify-evenly',
		'space-evenly'  => 'justify-evenly',
	];
	if ( isset( $class_map[ $position ] ) ) {
		$justify_class = $class_map[ $position ];
		if ( $prefix ) {
			return "wpex-{$prefix}-{$justify_class}";
		} else {
			return "wpex-{$justify_class}";
		}
	}
}

/**
 * Parses align items class.
 */
function vcex_parse_align_items_class( $align = '', $prefix = '' ) {
	$align_map = [
		'left'       => 'items-start',
		'start'      => 'items-start',
		'flex-start' => 'items-start',
		'middle'     => 'items-center',
		'center'     => 'items-center',
		'right'      => 'items-end',
		'end'        => 'items-end',
		'flex-end'   => 'items-end',
		'stretch'    => 'items-stretch',
		'baseline'   => 'items-baseline',
	];
	if ( isset( $align_map[ $align ] ) ) {
		$align_class = $align_map[ $align ];
		if ( $prefix ) {
			return "wpex-{$prefix}-{$align_class}";
		} else {
			return "wpex-{$align_class}";
		}
	}
}

/**
 * Parses font_size class.
 */
function vcex_parse_font_size_class( $size = '' ) {
	if ( $size ) {
		return function_exists( 'wpex_sanitize_utl_font_size' ) ? wpex_sanitize_utl_font_size( $size ) : sanitize_html_class( "wpex-text-{$size}" );
	}
}

/**
 * Parses visibility class.
 */
function vcex_parse_visibility_class( $class = '' ) {
	if ( $class && is_string( $class ) ) {
		return function_exists( 'totaltheme_get_visibility_class' ) ? totaltheme_get_visibility_class( $class ) : sanitize_html_class( $class );
	}
}

/**
 * Parses padding class.
 */
function vcex_parse_padding_class( $padding = '', $sides = 'all' ) {
	if ( ! $padding ) {
		return;
	}
	$prefix_map = [
		'y'            => 'wpex-py',
		'block'        => 'wpex-py',
		'x'            => 'wpex-px',
		'inline'       => 'wpex-px',
		'top'          => 'wpex-pt',
		'block-start'  => 'wpex-pt',
		'bottom'       => 'wpex-pb',
		'block-end'    => 'wpex-pb',
		'left'         => 'wpex-pl',
		'inline-start' => 'wpex-pl',
		'right'        => 'wpex-pr',
		'inline-end'   => 'wpex-pr',
		'all'          => 'wpex-p',
	];
	$prefix = $prefix_map[ $sides ] ?? 'wpex-p';
	$padding_safe = absint( $padding );
	return "{$prefix}-{$padding_safe}";
}

/**
 * Parses shadow class.
 */
function vcex_parse_shadow_class( $shadow = '', $prefix = '' ) {
	if ( $shadow && str_starts_with( $shadow, 'shadow' ) ) {
		if ( $prefix ) {
			return sanitize_html_class( "wpex-{$prefix}-{$shadow}" );
		} else {
			return sanitize_html_class( "wpex-{$shadow}" );
		}
	}
}

/**
 * Parses border_radius class.
 */
function vcex_parse_border_radius_class( $border_radius = '' ) {
	if ( $border_radius && 'none' !== $border_radius ) {
		return sanitize_html_class( "wpex-{$border_radius}" );
	}
}

/**
 * Parses border_width class.
 */
function vcex_parse_border_width_class( $border_width = '', $sides = 'all' ) {
	if ( ! $border_width ) {
		return;
	}
	$prefix_map = [
		'block-start'  => 'wpex-border-t',
		'top'          => 'wpex-border-t',
		'block-end'    => 'wpex-border-b',
		'bottom'       => 'wpex-border-b',
		'inline-start' => 'wpex-border-l',
		'left'         => 'wpex-border-l',
		'inline-end'   => 'wpex-border-r',
		'right'        => 'wpex-border-r',
		'all'          => 'wpex-border',
	];
	$prefix = $prefix_map[ $sides ] ?? 'wpex-border';
	$border_width_safe = absint( $border_width );
	if ( 1 === $border_width_safe ) {
		return $prefix;
	} else {
		return "{$prefix}-{$border_width_safe}";
	}
}

/**
 * Parses border_radius class.
 */
function vcex_parse_border_style_class( $border_style = '' ) {
	if ( $border_style ) {
		$allowed_styles = [ 'solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset' ];
		if ( in_array( $border_style, $allowed_styles, true ) ) {
			return "wpex-border-{$border_style}";
		}
	}
}

/**
 * Parses gap class.
 */
function vcex_parse_gap_class( $gap = '' ) {
	if ( $gap || '0' == $gap ) {
		if ( 'none' === $gap ) {
			$gap = 0;
		}
		$gap_safe = absint( $gap );
		return "wpex-gap-{$gap_safe}";
	}
}

/**
 * Parses margin class.
 */
function vcex_parse_margin_class( $margin = '', $sides = 'all' ) {
	if ( ! $margin ) {
		return;
	}
	$prefixes = [
		'mt-'          => 'wpex-mt',
		'top'          => 'wpex-mt',
		'block-start'  => 'wpex-mt',
		'wpex-mt-'     => 'wpex-mt',
		'mb-'          => 'wpex-mb',
		'bottom'       => 'wpex-mb',
		'block-end'    => 'wpex-mb',
		'wpex-mb-'     => 'wpex-mb',
		'ml-'          => 'wpex-ml',
		'left'         => 'wpex-ml',
		'inline-start' => 'wpex-ml',
		'mr-'          => 'wpex-mr',
		'right'        => 'wpex-mr',
		'inline-end'   => 'wpex-mr',
		'mx-'          => 'wpex-mx',
		'x'            => 'wpex-mx',
		'inline'       => 'wpex-mx',
		'my-'          => 'wpex-my',
		'y'            => 'wpex-my',
		'block'        => 'wpex-my',
		'all'          => 'wpex-m',
	];
	$prefix = $prefixes[ $sides ] ?? 'wpex-m';	
	$margin_safe = absint( $margin );
	return "{$prefix}-{$margin_safe}";
}

/**
 * Parses font weight class.
 */
function vcex_parse_font_weight_class( $font_weight = '' ) {
	$allowed = [ 'hairline', 'thin', 'light', 'normal', 'medium', 'semibold', 'bold', 'extrabold', 'black', 'bolder', 'lighter', '100', '200', '300', '400', '500', '600', '700', '800', '900' ];
	if ( ! $font_weight || ! in_array( $font_weight, $allowed, true ) ) {
		return;
	}
	$font_map = [
		'100' => 'hairline',
		'200' => 'thin',
		'300' => 'light',
		'400' => 'normal',
		'500' => 'medium',
		'600' => 'semibold',
		'700' => 'bold',
		'800' => 'extrabold',
		'900' => 'black',
	];
	$font_weight = $font_map[ $font_weight ] ?? $font_weight;	
	return "wpex-font-{$font_weight}";
}

/**
 * Parses responsive_text font size.
 */
function vcex_parse_min_max_text_font_size( $font_size = '' ) {

	// Check if it's a responsive font size.
	if ( str_contains( $font_size, '|' ) ) {
		$font_size_opts = vcex_parse_multi_attribute( $font_size );
		if ( ! empty( $font_size_opts['d'] ) ) {
			$font_size = $font_size_opts['d'];
		} else {
			return '';
		}
	}

	// Convert em to px.
	if ( is_string( $font_size ) && str_ends_with( $font_size, 'em' ) ) {
		$font_size = str_replace( 'em', '', $font_size );
		$body_font_size = vcex_get_body_font_size();
		$body_font_size = apply_filters( 'vcex_responsive_font_size_base', $body_font_size );
		$font_size = $font_size * absint( $body_font_size );
	}

	return $font_size;
}

/*-------------------------------------------------------------------------------*/
/* [ Old Params ]
/*-------------------------------------------------------------------------------*/

/**
 * Parses icon parameter to make sure the icon & icon_type is set properly.
 *
 * @deprecated in 6.0 because it's not necessary but also because it's technically broken
 * since WPBakery added lazy loading to their Icon Picker.
 */
function vcex_parse_icon_param( $atts, $icon_param = 'icon', $icon_type_param = 'icon_type' ) {
	if ( ! empty( $atts[ $icon_param ] ) ) {
		$icon = $atts[ $icon_param ];
		$icon_type_param_val = $atts[ $icon_type_param ] ?? '';
		if ( ! $icon_type_param_val || 'ticons' === $icon_type_param_val ) {
			$icon_type = vcex_get_icon_type_from_class( (string) $icon );
			if ( $icon_type !== $icon_type_param_val ) {
				if ( 'ticons' === $icon_type ) {
					$atts[ $icon_param ] = str_replace( 'fa fa-', '', $icon ); // rename old fa icon
				} else {
					$atts[ $icon_type_param ] = $icon_type;
					$atts[ "{$icon_param}_{$icon_type}" ] = $icon;
				}
			}
		}
	}
	return $atts;
}

/**
 * Parses old content CSS params.
 */
function vcex_parse_deprecated_grid_entry_content_css( $atts ) {
	if ( ! empty( $atts['content_css'] ) ) {
		return $atts;
	}

	$css = '';

	if ( ! empty( $atts['content_background'] ) ) {
		$css .= "background-color: {$atts['content_background']};";
	}

	if ( ! empty( $atts['content_border'] ) ) {
		$border = $atts['content_border'];
		if ( '0px' == $border || 'none' == $border ) {
			$css .= 'border: 0px none rgba(255,255,255,0.01);'; // reset border
		} else {
			$css .= "border: {$border};";
		}
	}

	if ( ! empty( $atts['content_padding'] ) ) {
		$css .= "padding: {$atts['content_padding']};";
	}

	if ( ! empty( $atts['content_margin'] ) ) {
		$css .= "margin: {$atts['content_margin']};";
	}

	if ( $css_safe = strip_tags( $css ) ) {
		$atts['content_css'] = ".temp{{$css_safe}}";
	}

	unset( $atts['content_background'] );
	unset( $atts['content_padding'] );
	unset( $atts['content_margin'] );
	unset( $atts['content_border'] );

	return $atts;
}

/**
 * Sanitize border radius.
 *
 * @todo deprecate
 */
function vcex_sanitize_border_radius( $input = '' ) {
	switch ( $input ) {
		case '5px':
			$input = 'rounded-sm';
			break;
		case '10px':
			$input = 'rounded';
			break;
		case '15px':
			$input = 'rounded-md';
			break;
		case '20px':
			$input = 'rounded-lg';
			break;
		case '9999px':
		case '50%':
			$input = 'rounded-full';
			break;
	}
	return sanitize_html_class( $input );
}
