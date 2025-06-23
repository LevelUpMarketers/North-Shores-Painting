<?php

namespace TotalTheme;

use ReflectionMethod;
use WPEX_Color_Palette;

\defined( 'ABSPATH' ) || exit;

/**
 * Color Palette.
 */
class Color_Palette {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Initialize.
	 */
	public static function init(): void {
		self::inline_style_hooks();
	}

	/**
	 * Add color palette styles inline for use with editors, settings, login screen, etc.
	 */
	private static function inline_style_hooks(): void {
		if ( \is_customize_preview() ) {
			\add_action( 'customize_controls_print_styles', [ self::class, 'add_inline_styles' ], 100 );
		} else {
			if ( \is_admin() ) {
				\add_action( 'admin_enqueue_scripts', [ self::class, 'add_inline_styles' ], 100 );
				\add_action( 'enqueue_block_assets', [ self::class, 'add_inline_styles' ], 100 );
				\add_filter( 'tiny_mce_before_init', [ self::class, 'on_tiny_mce_before_init' ] );
			} else {
				\add_action( 'login_enqueue_scripts', [ self::class, 'on_login_enqueue_scripts' ], 100 );
			}	
		}
	}

	/**
	 * Returns colors array for color picker compontent.
	 */
	public static function get_color_component_list(): array {
		$color_palette = \array_values( self::get_all_colors() );
		foreach ( $color_palette as $k => $color ) {
			if ( ! empty( $color['css_var'] ) ) {
				$color_palette[ $k ]['color'] = "var({$color['css_var']})"; // we use vars so the colors are updated in the customizer
			}
			unset( $color_palette[ $k ]['css_var'] );
			unset( $color_palette[ $k ]['mod_name'] );
			unset( $color_palette[ $k ]['fallback'] );
		}
		return $color_palette;
	}

	/**
	 * Returns an array of color palette choices in the form of key => name.
	 */
	public static function get_choices(): array {
		$choices = [];
		foreach ( self::get_all_colors() as $k => $v ) {
			$choices[ $k ] = $v['name'];
		}
		return $choices;
	}

	/**
	 * Returns a list of colors.
	 */
	public static function get_all_colors(): array {
		$colors = self::get_extra_colors() + self::get_theme_colors() + self::get_custom_colors();
		$colors = (array) apply_filters( 'wpex_color_palette', $colors ); // @deprecated
		return $colors;
	}

	/**
	 * Returns a list of extra colors.
	 */
	public static function get_extra_colors(): array {
		$colors = [
			'transparent' => [
				'name'        => 'Transparent',
				'color'       => 'transparent',
				'slug'        => 'transparent',
				'type'        => 'extra',
				'description' => \esc_html__( 'No color.', 'total' ),
			],
			'currentColor' => [
				'name'        => 'currentColor',
				'color'       => 'currentColor',
				'slug'        => 'currentColor',
				'type'        => 'extra',
				'description' => \esc_html__( 'Use the current text color for the element.', 'total' ),
			],
		];

		if ( \get_theme_mod( 'term_colors_enable', true ) && ! is_customize_preview() ) {
			$colors['term_color'] = [
				'name'        => esc_html__( 'Term Color', 'total' ),
				'color'       => '',
				'css_var'     => '--wpex-term-color',
				'slug'        => 'term_color',
				'type'        => 'extra',
				'description' => \esc_html__( 'Dynamic color based on the current taxonomy term for archives, the primary term color for singular posts or the term color for specific term elements like buttons.', 'total' ),
			];
		}

		return $colors;
	}

	/**
	 * Returns a list of theme colors.
	 */
	public static function get_theme_colors(): array {
		$colors = [
			'accent' => [
				'name'        => esc_html( 'Accent', 'total' ),
				'css_var'     => '--wpex-accent',
				'color'       => '#1a73e8', // we use lowercase to be consistent with color picker
				'color_dark'  => '#8ab4f8',
				'slug'        => 'accent',
				'mod_name'    => 'accent_color',
				'type'        => 'theme',
				'description' => esc_html( 'Main accent background color used for links, buttons, etc.', 'total' ),
			],
			'on-accent'       => [
				'name'        => esc_html( 'On Accent', 'total' ),
				'color'       => '#ffffff',
				'color_dark'  => '#202124',
				'css_var'     => '--wpex-on-accent',
				'slug'        => 'on-accent',
				'mod_name'    => 'accent_on_color',
				'type'        => 'theme',
				'description' => esc_html( 'Main accent text color used for links, buttons, etc.', 'total' ),
			],
			'accent-alt' => [
				'name'        => esc_html( 'Accent Alt', 'total' ),
				'color'       => '#1464cc',
				'color_dark'  => '#99bcf7',
				'css_var'     => '--wpex-accent-alt',
				'slug'        => 'accent-alt',
				'mod_name'    => 'accent_color_hover',
				'fallback'    => 'accent',
				'type'        => 'theme',
				'description' => esc_html( 'Alternative accent background color used for accent hovers.', 'total' ),
			],
			'on-accent-alt' => [
				'name'        => esc_html( 'On Accent Alt', 'total' ),
				'color'       => '#ffffff',
				'color_dark'  => '#202124',
				'css_var'     => '--wpex-on-accent-alt',
				'slug'        => 'on-accent-alt',
				'mod_name'    => 'accent_on_color_hover',
				'fallback'    => 'on-accent',
				'type'        => 'theme',
				'description' => esc_html( 'Alternative accent text color used for accent hovers.', 'total' ),
			],
		];

		// The surface and text colors are not allowed to be selected in the Color picker (for good reasons).
		// But are needed in the Custom Login screen and tiny MCE editor.
		$color_scheme_allowed_hooks =  [
			'tiny_mce_before_init',
			'enqueue_block_assets',
			'enqueue_block_editor_assets',
			'login_enqueue_scripts',
			'wp_ajax_totalthemecore_import_theme_color_palette',
		];

		if ( \in_array( \current_filter(), $color_scheme_allowed_hooks, true ) ) {
			$colors = \array_merge( $colors, [
				// Surfaces
				'surface-1' => [
					'name'        => esc_html__( 'Surface 1', 'total' ),
					'color'       => '#ffffff',
					'color_dark'  => '#262626',
					'css_var'     => '--wpex-surface-1',
					'slug'        => 'surface-1',
					'mod_name'    => 'wpex_surface_1_color',
					'type'        => 'theme',
					'description' => esc_html__( 'Main background color.', 'total' ),
				],
				'surface-2' => [
					'name'        => esc_html__( 'Surface 2', 'total' ),
					'color'       => '#f7f7f7',
					'color_dark'  => '#2D2D2D',
					'css_var'      => '--wpex-surface-2',
					'slug'        => 'surface-2',
					'mod_name'    => 'wpex_surface_2_color',
					'type'        => 'theme',
					'description' => esc_html__( 'Used for secondary element backgrounds such as page header title, footer callout, author bio, etc.', 'total' ),
				],
				'surface-3' => [
					'name'        => esc_html__( 'Surface 3', 'total' ),
					'color'       => '#eeeeee',
					'color_dark'  => '#333333',
					'css_var'     => '--wpex-surface-3',
					'slug'        => 'surface-3',
					'mod_name'    => 'wpex_surface_3_color',
					'type'        => 'theme',
					'description' => esc_html__( 'Used for borders around elements using a Surface 2 background.', 'total' ),
				],
				'surface-4' => [
					'name'        => esc_html__( 'Surface 4', 'total' ),
					'color'       => '#e0e0e0',
					'color_dark'  => '#3C3C3C',
					'css_var'     => '--wpex-surface-4',
					'slug'        => 'surface-4',
					'mod_name'    => 'wpex_surface_4_color',
					'type'        => 'theme',
					'description' => esc_html__( 'Used in a similar manner as surface 3 but providing greater contrast.', 'total' ),
				],
				// Texts
				'text-1' => [
					'name'        => esc_html__( 'Text 1', 'total' ),
					'color'       => '#222222',
					'color_dark'  => '#ffffff',
					'css_var'     => '--wpex-text-1',
					'slug'        => 'text-1',
					'mod_name'    => 'wpex_text_1_color',
					'type'        => 'theme',
					'description' => esc_html__( 'Headings and bold text.', 'total' ),
				],
				'text-2' => [
					'name'        => esc_html__( 'Text 2', 'total' ),
					'color'       => '#424242',
					'color_dark'  => '#9e9e9e',
					'css_var'     => '--wpex-text-2',
					'slug'        => 'text-2',
					'mod_name'    => 'wpex_text_2_color',
					'type'        => 'theme',
					'description' => esc_html__( 'Primary text color.', 'total' ),
				],
				'text-3' => [
					'name'        => esc_html__( 'Text 3', 'total' ),
					'color'       => '#616161',
					'color_dark'  => '#757575',
					'css_var'     => '--wpex-text-3',
					'slug'        => 'text-3',
					'mod_name'    => 'wpex_text_3_color',
					'type'        => 'theme',
					'description' => esc_html__( 'Subtext such as dates and meta.', 'total' ),
				],
				'text-4' => [
					'name'        => esc_html__( 'Text 4', 'total' ),
					'color'       => '#757575',
					'color_dark'  => '#616161',
					'css_var'     => '--wpex-text-4',
					'slug'        => 'text-4',
					'mod_name'    => 'wpex_text_4_color',
					'type'        => 'theme',
					'description' => esc_html__( 'Subtext with the lowest emphasis.', 'total' ),
				],
			] );
		}

		/** deprecated */
		$colors = (array) apply_filters( 'wpex_theme_color_palette', $colors );
		
		return $colors;
	}

	/**
	 * Returns a list of custom colors.
	 */
	public static function get_custom_colors(): array {
		$colors = [];
		if ( \get_theme_mod( 'color_palette_enable', true )
			&& \is_callable( '\WPEX_Color_Palette::get_colors_list' )
			&& (new ReflectionMethod( '\WPEX_Color_Palette', 'get_colors_list' ))->isStatic()
		) {
			$colors = (array) WPEX_Color_Palette::get_colors_list();
			foreach ( $colors as $k => $v ) {
				$colors[ $k ]['type'] = 'custom';
			}
		}
		return $colors;
	}

	/**
	 * Generate inline CSS for color vars.
	 */
	private static function get_css_vars( $root = ':root' ): array {
		$css_light       = '';
		$css_dark        = '';
		$get_dark_colors = totaltheme_call_static( 'Dark_Mode', 'is_enabled' );

		foreach ( self::get_theme_colors() as $theme_color ) {
			$color_safe = ''; // reset for each iteration.
			if ( isset( $theme_color['color'] ) && isset( $theme_color['css_var'] ) ) {
				$color_safe = self::get_color_from_mod( $theme_color, $theme_color['color'] ?? '' );
				if ( $color_safe && isset( $theme_color['css_var']) ) {
					$css_light .= "{$theme_color['css_var']}:{$color_safe};";
					if ( $get_dark_colors ) {
						$dark_color_safe = self::get_color_from_mod( $theme_color, $theme_color['color_dark'] ?? '' );
						if ( $dark_color_safe ) {
							$css_dark .= "{$theme_color['css_var']}:{$dark_color_safe};";
						}
					}
				}
			}
		}

		foreach ( self::get_custom_colors() as $custom_color ) {
			if ( isset( $custom_color['color'] ) && isset( $custom_color['css_var'] ) ) {
				$color_safe = \sanitize_text_field( (string) $custom_color['color'] );
				if ( $color_safe ) {
					$css_light .= "{$custom_color['css_var']}:{$color_safe};";
					if ( $get_dark_colors && isset( $custom_color['color_dark'] ) ) {
						$dark_color_safe = sanitize_text_field( (string) $custom_color['color_dark'] );
						if ( $dark_color_safe ) {
							$css_dark .= "{$custom_color['css_var']}:{$dark_color_safe};";
						}
					}
				}
			}
		}

		if ( $css_light ) {
			$css_light = "{$root}{{$css_light}}";
		}

		if ( $css_dark ) {
			$css_dark = ".wpex-dark-mode{{$css_dark}}";
			if ( ':root' !== $root ) {
				$css_dark .= "{$root} {$css_dark}}";
			}
		}

		return [
			'light' => $css_light,
			'dark'  => $css_dark,
		];
	}

	/**
	 * Returns color from theme mod.
	 */
	private static function get_color_from_mod( $args, $default = '' ): string {
		$color = '';
		$args = \is_array( $args ) ? $args : self::get_theme_colors()[ $args ];
		if ( isset( $args['mod_name'] ) ) {
			$color = (string) \get_theme_mod( $args['mod_name'] );
			if ( $color ) {
				return (string) \wpex_parse_color( $color );
			} elseif ( isset( $args['fallback'] ) ) {
				return self::get_color_from_mod( $args['fallback'], $default );
			}
		}
		if ( ! $color && $default ) {
			$color = $default;
		}
		return ( $color && is_string( $color ) ) ? \sanitize_text_field( $color ) : '';
	}

	/**
	 * Add inline style for color palette css variables.
	 */
	public static function add_inline_styles(): void {
		$css_vars = (array) self::get_css_vars();
		if ( ! $css_vars ) {
			return;
		}
		self::enqueue_css_vars( $css_vars['light'] );
		self::enqueue_css_vars( $css_vars['dark'], 'dark' );
	}

	/**
	 * Register and Enqueues CSS vars inline CSS.
	 */
	private static function enqueue_css_vars( $css = '', $mode = 'light' ) {
		if ( $css && $css_safe = esc_attr( $css ) ) {
			$handle = "totaltheme-color-palette-{$mode}";
			if ( ! wp_style_is( $handle, 'registered' ) ) {
				\wp_register_style( $handle, false, [], true, true );
				\wp_add_inline_style( $handle, $css_safe );
			}
			if ( wp_style_is( $handle ) ) {
				\wp_dequeue_style( $handle ); // remove if already added so it can be loaded at later priority.
			}
			\wp_enqueue_style( $handle );
		}
	}

	/**
	 * Hooks into login_enqueue_scripts.
	 */
	public static function on_login_enqueue_scripts(): void {
		global $interim_login;
		if ( ! $interim_login ) {
			self::add_inline_styles();
		}
	}

	/**
	 * Adds inline CSS for the editor to match Customizer settings.
	 */
	public static function on_tiny_mce_before_init( $settings ) {
		if ( $css_vars = (array) self::get_css_vars( 'body#tinymce.wp-editor' ) ) {
			$css_vars_safe = \esc_attr( $css_vars['light'] . $css_vars['dark'] );
			if ( $css_vars_safe ) {
				$content_style = $settings['content_style'] ?? '';
				$content_style .= $css_vars_safe;
				$settings['content_style'] = $content_style;
			}
		}
        return $settings;
	}

}
