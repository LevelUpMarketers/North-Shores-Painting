<?php

namespace TotalTheme\Customizer;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Sanitize Callbacks.
 */
final class Sanitize_Callbacks {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Absint (that won't save 0).
	 */
	public static function absint( $input, $setting ) {
		return \absint( $input ) ?: '';
	}

	/**
	 * Template Select.
	 */
	public static function template_id( $input, $setting ) {
		return \absint( $input ) ?: '';
	}

	/**
	 * Checkbox.
	 */
	public static function checkbox( $checked, $setting ): bool {
		return ! empty( $checked );
	}

	/**
	 * Select.
	 */
	public static function select( $input, $setting ) {
		$input   = \sanitize_key( $input );
		$choices = $setting->manager->get_control( $setting->id )->choices;
		if ( \array_key_exists( $input, $choices ) ) {
			return \sanitize_text_field( $input );
		}
		if ( ! empty( $setting->default ) ) {
			return $setting->default;
		}
	}

	/**
	 * Grid Columns.
	 */
	public static function grid_columns( $input, $setting ) {
		if ( \is_numeric( $input ) ) {
			return \absint( $input );
		}
		return \array_map( 'absint', (array) $input );
	}

	/**
	 * Font Size.
	 */
	public static function font_size( $input, $setting ) {
		if ( \is_string( $input ) && \str_contains( $input, '{' ) ) {
			$input = \json_decode( $input, true );
		}
		if ( \is_array( $input ) ) {
			return \array_map( [ self::class, 'sanitize_font_size' ], $input );
		}
		return self::sanitize_font_size( $input );
	}

	/**
	 * Length Unit.
	 */
	public static function length_unit( $input, $setting ): string {
		if ( \is_numeric( $input ) ) {
			$allow_numeric = $setting->manager->get_control( $setting->id )->allow_numeric ?? true;
			if ( ! $allow_numeric ) {
				return '0px';
			}
			$input = \floatval( $input ) . 'px';
		}
		return $input ? \sanitize_text_field( (string) $input ) : '';
	}

	/**
	 * Pixel.
	 */
	public static function pixel( $input, $setting ): string {
		if ( 'none' === $input || '0px' === $input ) {
			return '0px';
		} elseif ( $input && \is_string( $input ) ) {
			return \sanitize_text_field( \floatval( $input ) . 'px' );
		} else {
			return '';
		}
	}

	/**
	 * Color.
	 */
	public static function color( $input, $setting ) {
		return \sanitize_text_field( $input );
	}

	/**
	 * Visibility
	 */
	public static function visibility( $input, $setting ) {
		if ( $input
			&& is_string( $input )
			&& \in_array( $input, \array_keys( \totaltheme_get_visibility_choices( false ) ), true )
		) {
			return $input;
		} else {
			return '';
		}
	}

	/**
	 * Sanitize social_profiles array.
	 */
	public static function social_profiles( $input ) {
		if ( $input ) {
			if ( ! \is_array( $input ) ) {
				$input = \json_decode( $input, true );
			}
			if ( $input && \is_array( $input ) ) {
				return \array_filter( \array_map( 'sanitize_url', $input ) );
			}
		}
		return '';
	}

	/**
	 * Sanitize font size.
	 */
	private static function sanitize_font_size( $input ) {
		if ( ! $input || '0' === $input || '0px' === $input || ! \is_string( $input ) ) {
			return '';
		} elseif ( \is_numeric( $input ) ) {
			return \absint( $input ) . 'px';
		} else {
			return \sanitize_text_field( $input );
		}
	}

}
