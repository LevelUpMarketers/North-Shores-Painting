<?php

defined( 'ABSPATH' ) || exit;

/**
 * Parses color to return correct value for frontend display.
 */
function wpex_parse_color( $color = '' ) {
	$color = sanitize_text_field( (string) $color );
	if ( ! $color ) {
		return;
	}
	if ( 'transparent' === $color || 'inherit' === $color || 'currentColor' === $color ) {
		return $color;
	} elseif ( 'link' === $color ) {
		return 'var(--wpex-link-color, var(--wpex-accent))';
	} elseif ( 'link-hover' === $color ) {
		return 'var(--wpex-hover-link-color,var(--wpex-link-color, var(--wpex-accent)))';
	} elseif ( 'term_color' === $color ) {
		$card = totaltheme_card_instance();
		$in_loop = ( in_the_loop() || $card );
		if ( ! empty( $card->term ) ) {
			$primary_term = $card->term;
		} elseif ( ( is_tax() || is_category() || is_tag() ) && ! $in_loop ) {
			$primary_term = get_queried_object();
		} elseif ( is_singular() || $in_loop ) {
			$primary_term = totaltheme_get_post_primary_term();
		}
		if ( ! empty( $primary_term ) && totaltheme_get_term_color( $primary_term )) {
			return 'var(--wpex-term-' . absint( $primary_term->term_id ) . '-color)';
		}
	} elseif ( str_starts_with( $color, '#' ) || str_starts_with( $color, 'rgb' ) ) {
		return $color;
	} elseif ( str_starts_with( $color, 'palette-' ) ) {
		$custom_colors = (array) totaltheme_get_color_palette( 'custom' );
		if ( $custom_colors && in_array( $color, array_keys( $custom_colors ) ) ) {
			return "var(--wpex-{$color}-color)";
		}
	} else {
		$theme_colors = (array) totaltheme_get_color_palette( 'theme' );
		if ( ! empty( $theme_colors[ $color ]['css_var'] ) ) {
			return "var({$theme_colors[ $color ]['css_var']})";
		}
		return $theme_colors[ $color ]['color'] ?? $color;
	}
}

/**
 * Parse CSS.
 */
function wpex_parse_css( string $value = '', string $property = '', string $selector = '', string $unit = '', bool $important = false ): string {
	$css = '';
	if ( $selector && $property && $value ) {
		if ( ! empty( $unit ) ) {
			$value .= strtolower( $unit );
		}
		if ( $value_safe = wpex_sanitize_data( $value, $property ) ) {
			if ( $important ) {
				$value_safe .= '!important';
			}
			$css = "{$selector}{{$property}:{$value_safe};}";
		}
	}
	return $css;
}

/**
 * Takes an array of attributes and outputs them for HTML.
 */
function wpex_parse_html( string $tag = '', array $attrs = [], string $content_safe = '' ): string {
	$attrs       = wpex_parse_attrs( $attrs );
	$tag_escaped = tag_escape( $tag ) ?: 'div';
	$output = "<{$tag_escaped} {$attrs}>";
	if ( $content_safe ) {
		$output .= $content_safe;
	}
	$output .= "</{$tag_escaped}>";
	return $output;
}

/**
 * Parses an html data attribute.
 */
function wpex_parse_attrs( $attrs = [] ): string {
	if ( ! $attrs ) {
		return '';
	}

	if ( is_string( $attrs ) ) {
		return $attrs;
	}

	$output = '';

	// Add noopener noreferrer automatically to nofollow links if rel attr isn't set.
	if ( isset( $attrs['href'] )
		&& isset( $attrs['target'] )
		&& in_array( $attrs['target'], [ '_blank', 'blank' ] )
	) {
		$rel = (string) apply_filters( 'wpex_targeted_link_rel', 'noopener noreferrer', $attrs['href'] );
		if ( $rel ) {
			if ( ! empty( $attrs['rel'] ) ) {
				$attrs['rel'] .= " {$rel}";
			} else {
				$attrs['rel'] = $rel;
			}
		}
	}

	// Loop through attributes.
	foreach ( $attrs as $key => $val ) {

		// Attributes used for other things, we can skip these.
		if ( 'content' === $key ) {
			continue;
		}

		// If the attribute is an array convert to string.
		if ( is_array( $val ) && $val = array_filter( $val ) ) {
			$val = array_map( 'trim', $val );
			$val = implode( ' ', $val );
		}

		// Sanitize specific attributes (must go here to prevent ending up with empty attrs).
		switch ( $key ) {
			case 'href':
			case 'src':
				if ( 'href' === $key && $val && str_starts_with( $val, 'mailto:' ) && str_contains( $val, '@' ) ) {
					$val = 'mailto:' . antispambot( str_replace( 'mailto:', '', wp_strip_all_tags( $val ) ) );
				}
				$val = $val ? esc_url( $val ) : ''; // allow empty don't set to #
				break;
			case 'id':
				if ( $val && is_string( $val ) ) {
					$val = ltrim( str_replace( ' ', '', $val ), '#' );
				}
				break;
			case 'target':
				if ( ! in_array( $val, [ '_blank', 'blank', '_self', '_parent', '_top' ], true ) ) {
					$val = '';
				} elseif ( 'blank' === $val ) {
					$val = '_blank';
				}
				break;
		}

		// Add attribute to output if value exists or is a string equal to 0.
		if ( $val || '0' === $val ) {
			switch ( $key ) {
				// Attributes that don't have values and equal themselves.
				case 'download':
					$safe_attr = preg_replace( '/[^a-z0-9_\-]/', '', $val );
					$output .= ' ' . trim( $safe_attr ); // Used for example on total button download attribute.
					break;
				// Attributes with values.
				default:
					$needle = 'data' === $key ? 'data-' : "{$key}=";
					if ( str_contains( $val, $needle ) ) {
						$output .= ' ' . trim( wp_strip_all_tags( $val ) );
					} else {
						$safe_attr = preg_replace( '/[^a-z0-9_\-]/', '', $key );
						$output .= ' ' . trim( $safe_attr ) . '="' . esc_attr( trim( $val ) ) . '"';
					}
					break;
			}
		}

		// Attributes without values.
		elseif ( 'alt' === $key ) {
			$output .= ' alt=""';
		} elseif ( in_array( $key, [ 'itemscope', 'controls', 'playsinline', 'muted' ], true ) ) {
			$output .= " {$key}";
		} elseif ( 'data-wpex-hover' !== $key && str_contains( $key, 'data-' ) ) {
			$safe_attr = preg_replace( '/[^a-z0-9_\-]/', '', $key );
			$output .= ' ' . trim( $safe_attr );
		}

	}

	return trim( $output );
}

/**
 * Parses background style class.
 */
function wpex_parse_background_style_class( string $style = '' ): string {
	$class = '';
	switch ( $style ) {
		case 'stretched':
		case 'stretch':
		case 'cover':
			$class = 'wpex-bg-cover wpex-bg-center wpex-bg-no-repeat';
			break;
		case 'repeat':
		case 'no-repeat':
		case 'repeat-x':
		case 'repeat-y':
			$class = "wpex-bg-{$style}";
			break;
		case 'fixed':
			$class = 'wpex-bg-fixed wpex-bg-cover wpex-bg-center wpex-bg-no-repeat';
			break;
		case 'fixed-top':
			$class = 'wpex-bg-fixed wpex-bg-cover wpex-bg-top wpex-bg-no-repeat';
			break;
		case 'fixed-bottom':
			$class = 'wpex-bg-fixed wpex-bg-cover wpex-bg-bottom wpex-bg-no-repeat';
			break;
	}
	return $class;
}

/**
 * Parses a multi value CSS attribute.
 */
function totaltheme_parse_css_multi_property( string $value, string $property ): array {
	$result = [];
	$params_pairs = \explode( '|', $value );
	if ( ! empty( $params_pairs ) ) {
		foreach ( $params_pairs as $pair ) {
			$param = \preg_split( '/\:/', $pair );
			if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
				$dir = $param[0];
				switch ( $param[0] ) {
					case 'top':
						$dir = 'block-start';
						break;
					case 'bottom':
						$dir = 'block-end';
						break;
					case 'left':
						$dir = 'inline-start';
						break;
					case 'right':
						$dir = 'inline-end';
						break;
				}
				$key = $property ? "{$property}-{$dir}" : $param[0];
				$result[ $key ] = $param[1];
			}
		}
	}
	return $result;
}