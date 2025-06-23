<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Theme Icons.
 */
final class Theme_Icons {

	/**
	 * Font icon stylesheet name.
	 */
	public const STYLE_HANDLE = 'ticons';

	/**
	 * Store array of icons to prevent extra lookups.
	 */
	protected static $icons_list = null;

	/**
	 * Stores the icon format type (font/svg).
	 */
	protected static $format = null;

	/**
	 * Stores shim array.
	 */
	protected static $shim = null;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Checks if theme icons are enabled.
	 *
	 * @todo rename filter.
	 */
	public static function is_enabled(): bool {
		return (bool) \apply_filters( 'wpex_ticons_enable', true );
	}

	/**
	 * Returns Theme Icons CSS file.
	 */
	public static function get_css_url(): string {
		return (string) \wpex_asset_url( 'icons/font/ticons.min.css' );
	}

	/**
	 * Returns a the URL to the json file with the list of icons and their SVGs.
	 */
	public static function get_json_url(): string {
		return (string) \get_parent_theme_file_uri( 'assets/icons/list.json' );
	}

	/**
	 * Returns the path to the json file with the list of icons and their svgs.
	 */
	public static function get_json_path(): string {
		return (string) \get_parent_theme_file_path( 'assets/icons/list.json' );
	}

	/**
	 * Registers the theme icon font stylesheet.
	 */
	public static function register_font_style(): void {
		\wp_register_style(
			self::STYLE_HANDLE,
			self::get_css_url(),
			[],
			WPEX_THEME_VERSION
		);
	}

	/**
	 * Enqueues theme icon font stylesheet.
	 */
	public static function enqueue_font_style(): void {
		\wp_enqueue_style( self::STYLE_HANDLE );
	}

	/**
	 * Returns list of all available theme icons.
	 */
	public static function get_icons_list(): array {
		if ( \is_null( self::$icons_list ) ) {
			self::$icons_list = (array) \json_decode( self::get_icons_list_json(), true );
		}
		return self::$icons_list;
	}

	/**
	 * Returns a json list of all available theme icons.
	 */
	public static function get_icons_list_json(): string {
		$json_file = self::get_json_path();
		if ( \file_exists( $json_file ) ) {
			return (string) \file_get_contents( $json_file );
		}
		return '{}';
	}

	/**
	 * Returns the preferred format for theme icons.
	 */
	public static function get_format(): string {
		if ( ! \is_null( self::$format ) ) {
			return self::$format;
		}
		$format = 'svg';
		self::$format = (string) \apply_filters_deprecated( 'wpex_theme_icon_format', [ $format ], 'Total 6.0' );
		return self::$format;
	}

	/**
	 * Checks if a given icon is valid.
	 */
	protected static function is_icon_valid( $icon ): bool {
		return ! ( ! $icon || '' === $icon || 'ticon ticon-none' === $icon || 'none' === $icon );
	}

	/**
	 * Returns icon.
	 */
	public static function get_icon( $icon = '', $extra_class = '', $size = '', $bidi = false ): ?string {
		$icon = trim( (string) $icon );

		if ( ! self::is_icon_valid( $icon ) ) {
			return null;
		}

		$html = '';
		$icon_attrs = [
			'class' => 'wpex-icon'
		];

		if ( \str_starts_with( $icon, 'id_' ) ) {
			$format     = 'svg';
			$icon_id   = \str_replace( 'id_', '', $icon );
			$attachment = \get_post( $icon_id );
			if ( $attachment && 'image/svg+xml' === \get_post_mime_type( $attachment ) && $attached_file = \get_attached_file( $icon_id, true ) ) {
				$icon_svg = (string) \totaltheme_call_non_static( 'Helpers\SVG_Sanitizer', 'sanitize', \file_get_contents( $attached_file ) );
				if ( ! $icon_svg ) {
					return null;
				}
			}
		} elseif ( \str_starts_with( $icon, '<svg' ) ) {
			$format   = 'svg';
			$icon_svg = $icon;
		} else {
			$format = self::get_format();

			if ( 'font' === $format && \str_contains( $icon, '/' ) ) {
				$format = 'svg';
			}

			if ( 'fonticons' !== $icon && \str_contains( $icon, 'ticon' ) ) {
				$icon = \str_replace( 'ticons/', '', $icon );
				$icon = \str_replace( 'ticon-', '', $icon );
				$icon = \str_replace( 'ticon', '', $icon );
				$icon = \trim( $icon );
			}

			if ( 'font' === $format ) {
				self::enqueue_font_style();
				$icon_attrs['class'] .= " ticon ticon-{$icon}";
			}

			/*if ( 'svg' === $format ) {
				$icon_attrs['data-name'] = $icon;
			}*/
		}

		if ( $extra_class ) {
			$extra_class = $extra_class['class'] ?? $extra_class; // backward compat.
			if ( \is_array( $extra_class ) ) {
				$extra_class = \implode( ' ', $extra_class );
			}
			$icon_attrs['class'] = trim( $extra_class ) . ' ' . $icon_attrs['class'];
		}

		if ( $size && 'font' !== $format && \in_array( $size, [ '2xs', 'xs', 'sm', 'md', 'lg', 'xl', '2xl' ], true ) ) {
			$icon_attrs['class'] .= " wpex-icon--{$size}";
		}

		if ( ! $bidi
			&& ! isset( $icon_svg )
			&& (bool) \apply_filters( 'totaltheme/icons/is_bidirectional', true ) // this filter is only if bidi isn't defined on a per-icon basis!
			&& ( \str_contains( $icon, 'right' ) || \str_contains( $icon, 'left' ) )
		) {
			$bidi = true;
		}

		if ( $bidi ) {
			$icon_attrs['class'] .= ' wpex-icon--bidi';
		}

		$icon_attrs['aria-hidden'] = 'true';

		$icon_attrs = array_filter( array_map( 'esc_attr', $icon_attrs ) );
		$icon_attrs_string = '';
		foreach ( $icon_attrs as $name => $value ) {
			$icon_attrs_string .= ' ' . $name . '="' . $value . '"';
		}

		if ( 'font' === $format ) {
			$html = "<span{$icon_attrs_string}></span>";
		} else {
			if ( ! isset( $icon_svg ) ) {
				$icon_svg = self::get_svg( trim( $icon ) );
			}
			if ( $icon_svg ) {
				$html = "<span{$icon_attrs_string}>{$icon_svg}</span>";
			}
		}

		$html = \apply_filters( 'wpex_theme_icon_html', $html, $icon, $extra_class ); // @deprecated - use custom icons instead!

		return (string) $html;
	}

	/**
	 * Returns icon svg.
	 */
	public static function get_svg( string $name ): string {
		$name = self::parse_icon_name( \sanitize_text_field( $name ) );
		$svg = self::get_icons_list()[ $name ] ?? '';
		// Check child theme if SVG wasn't found.
		if ( ! $svg && \is_child_theme() ) {
			$stylesheet_dir = \get_stylesheet_directory();
			if ( \file_exists( "{$stylesheet_dir}/assets/svgs/{$name}.svg" ) ) {
				$svg = (string) \file_get_contents( "{$stylesheet_dir}/assets/svgs/{$name}.svg" );
				if ( $svg ) {
					$svg = \totaltheme_call_non_static( 'Helpers\SVG_Sanitizer', 'sanitize', $svg );
				}
			}
		}
		return $svg;
	}

	/**
	 * Renders an icon.
	 */
	public static function render_icon( $icon = '', $extra_class = '', $inline = false ): void {
		echo self::get_icon( $icon, $extra_class, $inline );
	}

	/**
	 * Parse Icon Name.
	 */
	protected static function parse_icon_name( string $icon_name ): string {
		return self::get_shim()[ $icon_name ] ?? $icon_name;
	}

	/**
	 * Get icon shims.
	 */
	protected static function get_shim(): array {
		if ( \is_null( self::$shim ) ) {
			$shim = \get_parent_theme_file_path( '/assets/icons/shim.json' );
			if ( \file_exists( $shim ) ) {
				self::$shim = \json_decode( \file_get_contents( $shim ), true );
			}
		}
		return (array) self::$shim;
	}

	/**
	 * Returns an array of icon size choices.
	 */
	public static function get_size_choices(): array {
		return [
			''     => \esc_html__( 'Default', 'total' ),
			'2xs'  =>\esc_html__( '2x Small', 'total' ),
			'xs'   => \esc_html__( 'x Small', 'total' ),
			'sm'   => \esc_html__( 'Small', 'total' ),
			'lg'   => \esc_html__( 'Large', 'total' ),
			'xl'   => \esc_html__( 'x Large', 'total' ),
			'2xl'  => \esc_html__( '2x Large', 'total' ),
		];
	}

	/**
	 * Returns the icons json.
	 */
	protected static function get_icons_json() {
		\_deprecated_function( __METHOD__, 'Total Theme 6.0' );
	}

}
