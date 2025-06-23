<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Sanitize data for display on the frontend.
 */
class Sanitize_Data {

	/**
	 * Class instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Sanitize_Data.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Main class function parses input to return sanitized output.
	 */
	public function parse_data( $input = '', string $type = '' ) {
		if ( $type && \str_starts_with( $type, '--wpex-' ) ) {
			return $this->css_var( $input, $type );
		} else {
			if ( $type ) {
				$type = \str_replace( '-', '_', $type );
			}
			if ( $type && \method_exists( $this, $type ) ) {
				return $this->$type( $input );
			} else {
				return $this->strip_all_tags( $input );
			}
		}
	}

	/**
	 * URL.
	 */
	public function url( $input ) {
		return \esc_url( $input );
	}

	/**
	 * Text.
	 */
	public function text( $input ) {
		return \sanitize_text_field( $input );
	}

	/**
	 * Text Field.
	 */
	public function text_field( $input ) {
		return \sanitize_text_field( $input );
	}

	/**
	 * Textarea.
	 */
	public function textarea( $input ) {
		return \wp_kses_post( (string) $input );
	}

	/**
	 * Boolean.
	 */
	public function boolean( $input ) {
		return \wpex_validate_boolean( $input );
	}

	/**
	 * Pixels.
	 */
	public function px( $input ) {
		$input = (string) $input;
		if ( 'none' === $input || '0' === $input || '0px' === $input ) {
			return '0px';
		} elseif ( $input_safe = \floatval( \sanitize_text_field( $input ) ) ) {
			return "{$input_safe}px";
		}
	}

	/**
	 * Milliseconds.
	 */
	public function ms( $input ) {
		$input = (string) $input;
		if ( 'none' === $input || '0' === $input || '0ms' === $input || '0s' === $input ) {
			return '0ms';
		} elseif ( $input_safe = \floatval( \sanitize_text_field( $input ) ) ) {
			return "{$input_safe}ms";
		}
	}

	/**
	 * Pixel Fallback.
	 */
	public function fallback_px( $input ) {
		$input = (string) $input;
		if ( 'none' === $input || '0' === $input || '0px' === $input ) {
			return '0px';
		} elseif ( $input_safe = \sanitize_text_field( $input ) ) {
			if ( \is_numeric( $input_safe ) ) {
				return \floatval( $input_safe ) . 'px';
			}
			return $input_safe;
		}
	}

	/**
	 * Container width.
	 */
	public function container_width( $input ) {
		$input = (string) $input;
		if ( \is_numeric( $input ) || '0px' === $input || '0' === $input ) {
			return; // The is_numeric() check prevents values without units from working - this is a fix for pre 5.4.
		}
		return \sanitize_text_field( $input );
	}

	/**
	 * Margin.
	 */
	public function margin( $input ) {
		$input = (string) $input;
		if ( 'none' === $input || '0' === $input || '0px' === $input ) {
			return '0px';
		} elseif ( $input_safe = \sanitize_text_field( $input ) ) {
			if ( \is_numeric( $input_safe ) ) {
				return "{$input_safe}px";
			}
			return $input_safe;
		}
	}

	/**
	 * Padding.
	 */
	public function padding( $input ) {
		$input = (string) $input;
		if ( 'none' === $input || '0' === $input || '0px' === $input ) {
			return '0px';
		} elseif ( $input_safe = \sanitize_text_field( $input ) ) {
			if ( \is_numeric( $input_safe ) ) {
				return "{$input_safe}px";
			}
			return $input_safe;
		}
	}

	/**
	 * Font Size utl.
	 */
	public function utl_font_size( $input ) {
		$input_safe = \sanitize_text_field( $input );
		if ( $input_safe && \array_key_exists( $input_safe, \wpex_utl_font_sizes() ) ) {
			return "var(--wpex-text-{$input_safe})";
		}
	}

	/**
	 * Border Radius.
	 */
	public function border_radius( $input ) {
		$input = (string) $input;
		if ( 'none' === $input || '0' === $input || '0px' === $input ) {
			return '0px';
		} else {
			$input_safe = \sanitize_text_field( $input );
			if ( $input_safe && \array_key_exists( $input_safe, \wpex_utl_border_radius() ) ) {
				return "var(--wpex-{$input_safe})";
			} else {
				if ( \is_numeric( $input_safe ) ) {
					$input_safe = "{$input_safe}px";
				}
				return $input_safe;
			}
		}
	}

	/**
	 * Utility Border Radius.
	 */
	public function utl_border_radius( $input ) {
		if ( 'rounded-0' === $input ) {
			return '0px';
		} elseif ( 'rounded-full' === $input ) {
			return '9999px';
		} else {
			$input_safe = \sanitize_text_field( $input );
			if ( $input_safe && \array_key_exists( $input_safe, \wpex_utl_border_radius() ) ) {
				return "var(--wpex-{$input_safe})";
			}
		}
	}

	/**
	 * Font Size.
	 */
	public function font_size( $input ) {
		if ( '0' == $input || '0px' === $input ) {
			return;
		}
		if ( $input_safe = \sanitize_text_field( $input ) ) {
			if ( \array_key_exists( $input_safe, \wpex_utl_font_sizes() ) ) {
				return "var(--wpex-text-{$input_safe})";
			} elseif ( \is_numeric( $input_safe ) ) {
				if ( $input_safe_abs = \absint( $input_safe ) ) {
					return "{$input_safe_abs}px";
				}
			} else {
				return $input_safe;
			}
		}
	}

	/**
	 * Letter Spacing.
	 */
	public function letter_spacing( $input ) {
		if ( $input_safe = \sanitize_text_field( $input ) ) {
			if ( \array_key_exists( $input_safe, \wpex_utl_letter_spacing() ) ) {
				return "var(--wpex-tracking-{$input_safe})";
			} elseif ( \is_numeric( $input_safe ) ) {
				return \floatval( $input_safe ) . 'px';
			} else {
				return $input_safe;
			}
		}
	}

	/**
	 * Font Weight.
	 */
	public function font_weight( $input ) {
		if ( $input_safe = \sanitize_text_field( $input ) ) {
			switch ( $input_safe ) {
				case 'normal':
					$input_safe = '400';
					break;
				case 'medium':
					$input_safe = '500';
					break;
				case 'semibold':
					$input_safe = '600';
					break;
				case 'bold':
					$input_safe = '700';
					break;
				case 'bolder':
					$input_safe = '900';
					break;
			}
			return $input_safe;
		}
	}

	/**
	 * Font Family.
	 */
	public function font_family( $input ) {
		if ( 'system-ui' === $input ) {
			return \wpex_get_system_ui_font_stack();
		} elseif ( $input_safe = \sanitize_text_field( $input ) ) {
			// Converts special chars that may have been converted by sanitize_text_field().
			$input_safe = \wp_specialchars_decode( $input_safe );

			// Fixes issue with fonts saved in WPB shortcodes.
			$input_safe = \str_replace( "``", "'", $input_safe );

			// Add font stack to the end of the font family - must be done first!
			$input_safe = \wpex_get_font_family_stack( $input_safe );

			// Remove all quotes.
			$input_safe = \str_replace( '"', '', $input_safe );
			$input_safe = \str_replace( "'", '', $input_safe );

			// Convert font into array so we can add quotes if necessary.
			$fonts_array = \explode( ',', $input_safe );
			$fonts_array_new = [];
			foreach ( $fonts_array as $font ) {
				$font = \trim( $font );
				if ( \str_contains( $font, ' ' ) ) {
					$fonts_array_new[] = "'{$font}'";
				} else {
					$fonts_array_new[] = $font;
				}
			}

			// Convert parsed array back to a string.
			$input_safe = \implode( ', ', $fonts_array_new );

			// Finally return the font family name.
			return $input_safe;
		}
	}

	/**
	 * Color.
	 */
	public function color( $input ) {
		if ( $input_safe = \sanitize_text_field( $input ) ) {
			return wpex_parse_color( $input_safe );
		}
	}

	/**
	 * Hex Color.
	 */
	public function hex_color( $input ) {
		return sanitize_hex_color( $input );
	}

	/**
	 * Border Color.
	 */
	public function border_color( $input ) {
		return $this->color( $input );
	}

	/**
	 * Background Color.
	 */
	public function background_color( $input ) {
		return $this->color( $input );
	}

	/**
	 * Pixel or Percent.
	 */
	public function px_pct( $input ) {
		$input = (string) $input;
		if ( 'none' === $input || '0' === $input || '0px' === $input ) {
			return '0px';
		} elseif ( $input_safe = \sanitize_text_field( $input ) ) {
			if ( \str_ends_with( $input_safe, '%' ) ) {
				return $input_safe;
			} elseif ( $input_safe_floatval = floatval( $input_safe ) ) {
				return "{$input_safe_floatval}px";
			}
		}
	}

	/**
	 * Pixel or Em.
	 */
	public function px_em( $input ) {
		$input = (string) $input;
		if ( 'none' === $input || '0' === $input || '0px' === $input || '0em' === $input ) {
			return '0px';
		} elseif ( $input_safe = \sanitize_text_field( $input ) ) {
			if ( \is_numeric( $input_safe ) ) {
				return "{$input_safe}px";
			} elseif ( \str_ends_with( $input_safe, 'px' ) || \str_ends_with( $input_safe, 'em' ) ) {
				return $input_safe;
			} elseif ( $input_safe_float = \floatval( $input_safe ) ) {
				return "{$input_safe_float}px";
			}
		}
	}

	/**
	 * Opacity.
	 */
	public function opacity( $input ) {
		$input_safe = \sanitize_text_field( $input );
		if ( \is_numeric( $input_safe ) ) {
			$input_safe_int = (int) $input_safe;
			if ( $input_safe_int <= 1 ) {
				return $input_safe_int;
			}
		} elseif ( \str_ends_with( $input_safe, '%' ) ) {
			return $input_safe;
		}
	}

	/**
	 * HTML.
	 */
	public function html( $input ) {
		return \wp_kses_post( $input );
	}

	/**
	 * Image.
	 */
	public function image( $input ) {
		return \wp_kses( $input, [
			'img' => [
				'src'      => [],
				'alt'      => [],
				'srcset'   => [],
				'id'       => [],
				'class'    => [],
				'height'   => [],
				'width'    => [],
				'data'     => [],
				'data-rjs' => [],
				'loading'  => [],
				'decoding' => [],
				'itemprop' => [],
			],
		] );
	}

	/**
	 * Image.
	 */
	public function img( $input ) {
		return $this->image( $input );
	}

	/**
	 * Image from setting.
	 */
	public function image_src_from_mod( $input ) {
		if ( $input = \wpex_get_image_url( $input ) ) {
			return \esc_url( $input );
		}
	}

	/**
	 * Background Style.
	 */
	public function background_style_css( $input ) {
		$css = '';
		switch ( $input ) {
			case 'stretched':
				$css = 'background-size: cover;
					background-position: center center;
					background-attachment: fixed;
					background-repeat: no-repeat;';
				break;
			case 'cover':
				$css = 'background-position:center center;background-size: cover;';
				break;
			case 'repeat':
				$css = 'background-repeat:repeat;';
				break;
			case 'no-repeat':
				$css = 'background-repeat:no-repeat;';
				break;
			case 'repeat-y':
				$css = 'background-position: center center;background-repeat:repeat-y;';
				break;
			case 'fixed':
				$css = 'background-repeat: no-repeat;background-position: center center;background-attachment: fixed;';
				break;
			case 'fixed-top':
				$css = 'background-repeat: no-repeat;background-position: center top;background-attachment: fixed;';
				break;
			case 'fixed-bottom':
				$css = 'background-repeat: no-repeat;background-position: center bottom;background-attachment: fixed;';
				break;
			default:
				if ( $safe_input = \esc_attr( $input ) ) {
					$css = "background-repeat:{$safe_input};";
				}
				break;
		}
		return $css;
	}

	/**
	 * Embed URL.
	 */
	public function embed_url( $input ) {
		if ( $input = \wpex_get_video_embed_url( $input ) ) {
			return \esc_url( $input );
		}
	}

	/**
	 * Google Map Embed.
	 */
	public function google_map( $input ) {
		return \wp_kses( $input, [
			'iframe' => [
				'src'             => [],
				'height'          => [],
				'width'           => [],
				'frameborder'     => [],
				'style'           => [],
				'allowfullscreen' => [],
			],
		] );
	}

	/**
	 * iFrame.
	 */
	public function iframe( $input ) {
		return \wp_kses( $input, [
			'iframe' => [
				'align'        => [],
				'width'        => [],
				'height'       => [],
				'frameborder'  => [],
				'name'         => [],
				'src'          => [],
				'id'           => [],
				'class'        => [],
				'style'        => [],
				'scrolling'    => [],
				'marginwidth'  => [],
				'marginheight' => [],
				'allow'        => [],
			],
		] );
	}

	/**
	 * SVG.
	 */
	public function svg( $input ) {
		return \totaltheme_call_non_static( 'Helpers\SVG_Sanitizer', 'sanitize', $input );
	}

	/**
	 * Sanitize CSS variable.
	 */
	public function css_var( $value, $var_name ) {
		if ( '--wpex-border-main' === $var_name
			|| \str_contains( $var_name, '-color' )
			|| \str_contains( $var_name, '-bg' )
			|| \str_contains( $var_name, '-accent' )
			|| \str_contains( $var_name, '-palette' )
		) {
			return $this->color( $value );
		} elseif ( \str_contains( $var_name, '-margin' )) {
			return $this->margin( $value );
		} elseif ( \str_contains( $var_name, '-padding' ) ) {
			return $this->padding( $value );
		} elseif ( \str_contains( $var_name, '-border-radius' ) ) {
			return $this->border_radius( $value );
		} else {
			return \sanitize_text_field( $value );
		}
	}

	/**
	 * Strip all tags.
	 */
	public function strip_all_tags( $input ) {
		if ( \is_array( $input ) ) {
			return \array_map( [ $this, 'strip_all_tags' ], $input );
		}
		return \wp_strip_all_tags( $input ); 
	}

	/**
	 * Return css unit (text) from input.
	 */
	protected function get_unit( $input ) {
		\_deprecated_function( __METHOD__, 'Total Theme 5.16' );
	}

}
