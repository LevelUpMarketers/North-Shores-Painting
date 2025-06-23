<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Returns inline style tag or inline CSS based on array of properties and values.
 */
final class Inline_Style {

	/**
	 * CSS to return.
	 */
	private $style;

	/**
	 * Whether to add the style tag or not.
	 */
	private $add_style_tag;

	/**
	 * Class Constructor.
	 */
	public function __construct( $properties = [], $add_style_tag = true ) {
		$this->style = [];
		$this->add_style_tag = $add_style_tag;

		// Loop through css properties.
		foreach ( $properties as $property => $value ) {

			// If the value is an array the second parameter is the name of the sanitization callback function.
			if ( \is_array( $value ) && isset( $value[0] ) ) {
				$sanitize = $value[1] ?? null;
				$value = $value[0];
				if ( $value && $sanitize ) {
					$sanitize_method = "sanitize_{$sanitize}";
					if ( \method_exists( $this, $sanitize_method ) ) {
						$value = $this->$sanitize_method( $value );
					}
				}
			}

			// Run the method based on the property name.
			if ( ! empty( $value ) && ( \is_string( $value ) || \is_numeric( $value ) ) ) {
				if ( $this->property_is_var( $property ) ) {
					$this->parse_css_var( $property, $value );
				} else {
					$parse_method = 'parse_' . str_replace( '-', '_', $property );
					if ( \method_exists( $this, $parse_method ) ) {
						$this->$parse_method( $value );
					} else {
						$this->parse_other_property( $property, $value );
					}
				}
			}
		}
	}

	/**
	 * CSS variable.
	 */
	private function parse_css_var( $property, $value ) {
		if ( '--wpex-border-main' === $property
			|| \str_contains( $property, '-color' )
			|| \str_contains( $property, '-bg' )
			|| \str_contains( $property, '-accent' )
			|| \str_contains( $property, '-palette' )
		) {
			$value_safe = $this->sanitize_color( $value );
		} elseif ( str_contains( $property, '-gap' ) || str_contains( $property, '-gutter' ) ) {
			$value_safe = $this->sanitize_gap( $value );
		} elseif ( \str_contains( $property, '-margin' ) || '--vcex-spacing' === $property ) {
			$value_safe = $this->sanitize_margin( $value );
		} elseif ( \str_contains( $property, '-padding' ) ) {
			$value_safe = $this->sanitize_padding( $value );
		} elseif ( \str_contains( $property, '-border-radius' ) ) {
			$value_safe = $this->sanitize_border_radius( $value );
		} elseif ( \str_contains( $property, '-border-width' ) ) {
			$value_safe = $this->sanitize_border_width( $value );
		} elseif ( \str_contains( $property, '-width' ) || \str_contains( $property, '-height' ) ) {
			$value_safe = $this->fallback_px( $value );
		} elseif ( '--wpex-link-decoration-line' === $property || '--wpex-hover-link-decoration-line' === $property ) {
			$value_map = [
				'false' => 'none',
				'true'  => 'underline',
			];
			$value_safe = $value_map[ $value ] ?? \esc_attr( $value ); 
		} else {
			$value_safe = \esc_attr( $value );
		}
		$this->add_style( $property, $value_safe );
	}

	/**
	 * Float.
	 */
	private function parse_float( $input ) {
		if ( 'center' === $input ) {
			$this->style[] = 'margin-inline:auto;float:none;';
		} else {
			$this->add_style( 'float', \esc_attr( $input ) );
		}
	}

	/**
	 * Width.
	 */
	private function parse_width( $input ) {
		$this->add_style( 'width', $this->sanitize_width( $input ) );
	}

	/**
	 * Max-Width.
	 */
	private function parse_max_width( $input ) {
		if ( '100%' !== $input ) {
			$this->add_style( 'max-width', $this->sanitize_width( $input ) );
		}
	}

	/**
	 * Min-Width.
	 */
	private function parse_min_width( $input ) {
		$this->add_style( 'min-width', $this->sanitize_width( $input ) );
	}

	/**
	 * Background Image.
	 */
	private function parse_background_image( $input ) {
		if ( $bg_img_safe = \esc_attr( esc_url( $input ) ) ) {
			$this->add_style( 'background-image', "url({$bg_img_safe})" );
		}
	}

	/**
	 * Border.
	 */
	private function parse_border( $input ) {
		$property = ( $input && str_contains( trim( $input ), ' ' ) ) ? 'border' : 'border-width';
		if ( 'border-width' === $property ) {
			$this->parse_border_width( $input );
		} else {
			$this->add_style( 'border', \esc_attr( $input ) );
		}
	}

	/**
	 * Border Width.
	 */
	private function parse_border_width( $input ) {
		$input = 'none' === $input ? '0' : $input;
		$this->add_style( 'border-width', $this->sanitize_border_width( $input ) );
	}

	/**
	 * Border: Top Width.
	 */
	private function parse_border_top_width( $input ) {
		$this->add_style( 'border-top-width', $this->sanitize_border_width( $input ) );
	}

	/**
	 * Border: Bottom Width.
	 */
	private function parse_border_bottom_width( $input ) {
		$this->add_style( 'border-bottom-width', $this->sanitize_border_width( $input ) );
	}

	/**
	 * Margin.
	 */
	private function parse_margin( $input ) {
		if ( ! $this->parse_trbl_property( $input, 'margin' ) ) {
			$this->add_style( 'margin', $this->sanitize_margin( $input ) );
		}
	}

	/**
	 * Margin: Right.
	 */
	private function parse_margin_right( $input ) {
		$this->add_style( 'margin-inline-end', $this->sanitize_margin( $input ) );
	}

	/**
	 * Margin: Left.
	 */
	private function parse_margin_left( $input ) {
		$this->add_style( 'margin-inline-start', $this->sanitize_margin( $input ) );
	}

	/**
	 * Padding.
	 */
	private function parse_padding( $input ) {
		if ( ! $this->parse_trbl_property( $input, 'padding' ) ) {
			$this->add_style( 'padding', $this->sanitize_padding( $input ) );
		}
	}

	/**
	 * Object Fit.
	 */
	private function parse_object_fit( $input ) {
		if ( \in_array( $input, [ 'fill', 'contain', 'cover', 'scale-down' ], true ) ) {
			$this->add_style( 'object-fit', $input );
		}
	}

	/**
	 * Z-index
	 */
	private function parse_z_index( $input ) {
		if ( $int = absint( $input ) ) {
			$this->add_style( 'z-index', \esc_attr( $int ) );
		}
	}

	/**
	 * Icon Size.
	 */
	private function parse_icon_size( $input ) {
		if ( $input && \in_array( $input, [ 'xs', 'sm', 'normal', 'md', 'lg', 'xl' ] ) ) {
			$font_size = "var(--vcex-icon-{$input})";
		} else {
			$font_size = $this->parse_font_size( $input );
		}
		$this->add_style( 'font-size', \esc_attr( $font_size ) );
	}

	/**
	 * Font-Size.
	 */
	private function parse_font_size( $input ) {
		if ( $input && ! \str_contains( $input, '|' ) ) {
			$this->add_style( 'font-size', \esc_attr( $this->sanitize_font_size( $input ) ) );
		}
	}

	/**
	 * Font Weight.
	 */
	private function parse_font_weight( $input ) {
		$weights = [
			'normal'    => '400',
			'medium'    => '500',
			'semibold'  => '600',
			'bold'      => '700',
			'extrabold' => '800',
			'black'     => '900',
		];
		$input = $weights[ $input ] ?? $input; 
		$this->add_style( 'font-weight', \esc_attr( $input ) );
	}

	/**
	 * Font Family.
	 */
	private function parse_font_family( $input ) {
		\vcex_enqueue_font( $input );
		if ( \function_exists( 'wpex_sanitize_font_family' ) ) {
			$input_safe = \wpex_sanitize_font_family( $input );
		} else {
			$input_safe = \sanitize_text_field( $input );
		}
		$this->add_style( 'font-family', $input_safe );
	}

	/**
	 * Opacity.
	 */
	private function parse_opacity( $input ) {
		$input = \str_replace( '%', '', $input ); // % is the only non numeric character allowed.
		if ( ! \is_numeric( $input ) ) {
			return;
		}
		if ( $input > 1 ) {
			$input = $input / 100;
		}
		if ( $input <= 1 ) {
			$this->add_style( 'opacity', \esc_attr( $input ) );
		}
	}

	/**
	 * Justify Content.
	 */
	private function parse_justify_content( $input ) {
		$allowed = [
			'start',
			'flex-start',
			'center',
			'end',
			'flex-end',
			'space-between',
			'space-end',
			'space-around',
			'space-evenly',
		];
		if ( in_array( $input, $allowed, true ) ) {
			$this->add_style( 'justify-content', \esc_attr( $input ) );
		}
	}

	/**
	 * Text Align.
	 */
	private function parse_text_align( $input ) {
		$old_classes = [
			'textcenter' => 'center',
			'textleft'   => 'left',
			'textright'  => 'right',
			'none'       => 'unset',
		];
		$input = $old_classes[ $input ] ?? $input;
		if ( vcex_is_bidirectional() ) {
			switch ( $input ) {
				case 'left':
					$input = 'start';
					break;
				case 'right':
					$input = 'end';
					break;
			}
		}
		if ( $input ) {
			$this->add_style( 'text-align', \esc_attr( $input ) );
		}
	}

	/**
	 * Text Transform.
	 */
	private function parse_text_transform( $input ) {
		$allowed_values = [
			'none',
			'capitalize',
			'uppercase',
			'lowercase',
			'initial',
			'inherit',
		];
		if ( \in_array( $input, $allowed_values, true ) ) {
			$this->add_style( 'text-transform', $input );
		}
	}

	/**
	 * Text Decoration Line.
	 */
	private function parse_text_decoration_line( $input ) {
		if ( in_array( $input, [ 'none', 'underline', 'overline', 'line-through', 'underline overline' ], true ) ) {
			$this->add_style( 'text-decoration-line', \esc_attr( $input ) );
		}
	}

	/**
	 * Text decoration thickness.
	 */
	private function parse_text_decoration_thickness( $input ) {
		$this->add_style( 'text-decoration-thickness', $this->fallback_px( $input ) );
	}

	/**
	 * Text underline offset.
	 */
	private function parse_text_underline_offset( $input ) {
		$this->add_style( 'text-underline-offset', $this->fallback_px( $input ) );
	}

	/**
	 * Letter Spacing.
	 */
	private function parse_letter_spacing( $input ) {
		if ( \function_exists( '\wpex_utl_letter_spacing' )
			&& array_key_exists( $input, \wpex_utl_letter_spacing() )
		) {
			$input = "var(--wpex-tracking-{$input})";
		}
		$this->add_style( 'letter-spacing', $this->fallback_px( $input ) );
	}

	/**
	 * Line-Height.
	 */
	private function parse_line_height( $input ) {
		if ( \function_exists( '\wpex_utl_line_height' )
			&& \array_key_exists( $input, \wpex_utl_line_height() )
		) {
			$input = "var(--wpex-leading-{$input})";
		}
		$this->add_style( 'line-height', \esc_attr( $input ) );
	}

	/**
	 * Line-Height with px sanitize.
	 */
	private function parse_line_height_px( $input ) {
		$this->add_style( 'line-height', $this->sanitize_px( $input ) );
	}

	/**
	 * Height.
	 */
	private function parse_height( $input ) {
		$this->add_style( 'height', $this->sanitize_height( $input ) );
	}

	/**
	 * Height with px sanitize.
	 */
	private function parse_height_px( $input ) {
		$this->add_style( 'height', $this->sanitize_px( $input ) );
	}

	/**
	 * Min-Height.
	 */
	private function parse_min_height( $input ) {
		$this->add_style( 'min-height', $this->sanitize_height( $input ) );
	}

	/**
	 * Border Radius.
	 */
	private function parse_border_radius( $input ) {
		$this->add_style( 'border-radius', $this->sanitize_border_radius( $input ) );
	}

	/**
	 * Italic.
	 */
	private function parse_italic( $input ) {
		if ( 'true' === $input || 'yes' === $input || true === $input ) {
			$this->add_style( 'font-style', 'italic' );
		}
	}

	/**
	 * Parse top/right/bottom/left fields.
	 */
	private function parse_trbl_property( $value, $property ) {
		if ( \function_exists( 'vcex_parse_multi_attribute' )
			&& \str_contains( $value, ':' )
			&& $values = \vcex_parse_multi_attribute( $value )
		) {

			// All values are the same
			if ( isset( $values['top'] )
				&& \count( $values ) == 4
				&& \count( \array_unique( $values ) ) <= 1
			) {
				$value = $values['top'];
				$value = ( 'none' === $value ) ? '0' : $value;
				$value = \is_numeric( $value ) ? $value  . 'px' : $value;
				$this->style[] = \esc_attr( \trim( $property ) ) . ':' . \esc_attr( $value ) . ';';
			}
			// Values are different.
			else {
				// If top/bottom or left/right are the same we can use block-{$property} and inline-{$property} instead.
				if ( 'margin' === $property || 'padding' === $property ) {
					if ( ! empty( $values['top'] ) && ! empty( $values['bottom'] ) && $values['top'] === $values['bottom'] ) {
						$values['block'] = $values['top'];
						unset( $values['top'] );
						unset( $values['bottom'] );
					}
					if ( ! empty( $values['left'] ) && ! empty( $values['right'] ) && $values['left'] === $values['right'] ) {
						$values['inline'] = $values['left'];
						unset( $values['left'] );
						unset( $values['right'] );
					}
				}
				// Loop through values to add inline style.
				foreach ( $values as $k => $v ) {
					if ( ( 'left' === $k || 'right' === $k ) ) {
						if ( vcex_is_bidirectional() ) {
							$k = ( 'left' === $k ) ? 'inline-start' : 'inline-end';
						}
					} elseif ( 'top' === $k ) {
						$k = 'block-start';
					} elseif ( 'bottom' === $k ) {
						$k = 'block-end';
					}
					if ( 0 === $v || '0' === $v ) {
						$v = '0px';
					}
					if ( ! empty( $v ) ) {
						$method = "parse_{$property}_{$k}";
						if ( \method_exists( $this, $method ) ) {
							$this->$method( $v );
						} else {
							$this->parse_other_property( "{$property}-{$k}", $v );
						}
					}
				}
			}
			return true; // !! important !!
		}
	}

	/**
	 * Parse other properties that don't require their own method.
	 */
	private function parse_other_property( $property, $value ) {
		$value_safe = \sanitize_text_field( (string) $value );
		if ( $value_safe || '0' === $value_safe ) {
			$property = \str_replace( '_', '-', $property );
			if ( 'background' === $property || \str_ends_with( $property, 'color' ) ) {
				$value_safe = $this->sanitize_color( $value_safe );
			} elseif ( \str_starts_with( $property, 'padding' ) ) {
				$value_safe = $this->sanitize_padding( $value_safe );
			} elseif ( \str_starts_with( $property, 'margin' ) ) {
				$value_safe = $this->sanitize_margin( $value_safe );
			} elseif ( 'gap' === $property || 'column-gap' === $property || 'row-gap' === $property ) {
				$value_safe = $this->sanitize_gap( $value_safe );
			} elseif ( 'animation-duration' === $property || 'animation-delay' === $property || 'transition-duration' === $property ) {
				$value_safe = $this->fallback_seconds( $value_safe );
			} elseif ( 'top' === $property || 'bottom' === $property || 'right' === $property || 'left' === $property || \str_starts_with( $property, 'inset' ) ) {
				$value_safe = $this->sanitize_position( $value_safe );
			}
			if ( $value_safe ) {
				$this->add_style( $property, \esc_attr( $value_safe ) );
			}
		}
	}

	/**
	 * Sanitize border_radius input.
	 */
	private function sanitize_border_radius( $input ) {
		if ( 'rounded-0' === $input
			|| 'none' === $input
			|| '0px' === $input
			|| '0' === $input
			|| 0 === $input
		) {
			return '0px';
		}
		if ( 'full' === $input ) {
			return '9999px';
		}
		if ( \is_numeric( $input ) ) {
			return "{$input}px"; // px is the default value.
		}
		if ( \function_exists( '\wpex_utl_border_radius' )
			&& \array_key_exists( $input, \wpex_utl_border_radius( true ) )
		) {
			return "var(--wpex-{$input})";
		}
		return \esc_attr( $input );
	}

	/**
	 * Sanitize height input.
	 */
	private function sanitize_height( $input ) {
		return $this->fallback_px( $input );
	}

	/**
	 * Sanitize border_width input.
	 */
	private function sanitize_border_width( $input ) {
		return $this->fallback_px( $input );
	}

	/**
	 * Sanitize width input.
	 */
	private function sanitize_width( $input ) {
		return $this->fallback_px( $input );
	}

	/**
	 * Sanitize gap input.
	 */
	private function sanitize_gap( $input ) {
		if ( 'none' === $input ) {
			return '0';
		} elseif ( \is_numeric( $input ) ) {
			return "{$input}px";
		} else {
			return \esc_attr( $input );
		}
	}

	/**
	 * Parse color input.
	 */
	private function sanitize_color( $color ) {
		switch ( $color ) {
			case 'none':
			case 'transparent';
				$color = 'transparent';
				break;
			case 'inherit':
				$color = 'inherit';
				break;
			case 'currentColor':
				$color = 'currentColor';
				break;
			default:
				$color = \vcex_parse_color( $color );
				break;
		}
		return \esc_attr( $color );
	}

	/**
	 * Sanitize padding input.
	 */
	private function sanitize_padding( $padding ) {
		if ( 'none' === $padding ) {
			return '0px';
		} elseif ( \is_numeric( $padding ) ) {
			return "{$padding}px";
		} else {
			return \esc_attr( $padding );
		}
	}

	/**
	 * Sanitize margin input.
	 */
	private function sanitize_margin( $margin ) {
		if ( 'none' === $margin ) {
			return '0px';
		} elseif ( \is_numeric( $margin ) ) {
			return "{$margin}px";
		} else {
			return \esc_attr( $margin );
		}
	}

	/**
	 * Sanitize position value.
	 */
	private function sanitize_position( $input ) {
		if ( 'none' === $input || '0px' === $input ) {
			return '0px';
		} elseif ( \is_numeric( $input ) ) {
			return "{$input}px";
		}
		return \esc_attr( $input );
	}

	/**
	 * Sanitize font-size input.
	 */
	private function sanitize_font_size( $input ) {
		if ( '0px' === $input || '0em' === $input || '0rem' === $input ) {
			return; // these are not valid font sizes.
		}

		if ( \is_numeric( $input ) ) {
			return \absint( $input ) . 'px';
		}

		if ( \in_array( $input, [ 'sm', 'md', 'lg', 'base' ] ) || \str_ends_with( $input, 'xs' ) || \str_ends_with( $input, 'xl' ) ) {
			return "var(--wpex-text-{$input})";
		}

		if ( $this->is_value_a_function( $input ) ) {
			return \strip_tags( $input );
		}

		$allowed_units = [ 'px', 'em', 'rem', 'vw', 'vmin', 'vmax', 'vh', '%' ];
		
		if ( $this->is_value_a_valid_unit( $input, $allowed_units ) ) {
			$input = \esc_attr( $input );
		} else {
			$input = \abs( \floatval( $input ) ) . 'px'; // always return pixel value - important!
		}

		if ( '0px' !== $input ) {
			return $input;
		}
	}

	/**
	 * Fallback to px.
	 */
	private function fallback_px( $input ) {
		return \is_numeric( $input ) ? "{$input}px" : \esc_attr( $input );
	}

	/**
	 * Fallback to seconds.
	 */
	private function fallback_seconds( $input ) {
		return \is_numeric( $input ) ? "{$input}s" : $input;
	}

	/**
	 * Sanitize px input.
	 */
	private function sanitize_px( $input ) {
		return \vcex_validate_px( $input );
	}

	/**
	 * Checks if a property is a CSS variable.
	 */
	private function property_is_var( $property = '' ): bool {
		return (bool) \str_starts_with( $property, '--' );
	}

	/**
	 * Returns allowed css functions.
	 */
	private function get_allowed_css_functions(): array {
		return [ 'calc', 'clamp', 'min', 'max', 'var' ];
	}

	/**
	 * Checks if a value is a function.
	 */
	private function is_value_a_function( $value = '' ): bool {
		$check = false;
		foreach ( $this->get_allowed_css_functions() as $function ) {
			if ( \str_starts_with( $value, $function ) ) {
				$check = true;
				break;
			}
		}
		return $check;
	}

	/**
	 * Checks if a value is an allowed unit.
	 */
	private function is_value_a_valid_unit( $value = '', $allowed_units = [] ) {
		$check = false;
		$value = \trim( $value );
		foreach ( $allowed_units as $allowed_unit ) {
			if ( \str_ends_with( $value, $allowed_unit ) ) {
				$check = true;
				break;
			}
		}
		return $check;
	}

	/**
	 * Adds style to the style array.
	 */
	private function add_style( $property = '', $safe_value = '' ) {
		if ( $safe_value || '0' == $safe_value ) {
			$this->style[] = "{$property}:{$safe_value};";
		}
	}

	/**
	 * Returns the styles.
	 */
	public function return_style() {
		if ( ! empty( $this->style ) ) {
			$this->style = \implode( false, $this->style );
			$style_safe = \strip_tags( $this->style ); // @todo should use esc_attr()
			if ( $style_safe ) {
				if ( $this->add_style_tag ) {
					return " style=\"{$style_safe}\""; // always include extra space.
				} else {
					return $style_safe;
				}
			}
		}
	}

}
