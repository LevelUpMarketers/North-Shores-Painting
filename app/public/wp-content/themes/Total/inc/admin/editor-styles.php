<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Enqueues scripts and adds inline CSS for the WordPress editor.
 */
class Editor_Styles {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Checks if editor styles are enabled in the Theme Panel.
	 */
	public static function is_enabled(): bool {
		return \wp_validate_boolean( \get_theme_mod( 'editor_styles_enable', true ) );
	}

	/**
	 * Init.
	 */
	public static function init(): void {
		\add_action( 'after_setup_theme', [ self::class, 'classic_editor_style' ], 10 );
		
		if ( \is_admin() ) {
			\add_action( 'enqueue_block_assets', [ self::class, 'on_enqueue_block_assets' ] );
		}

		if ( self::is_enabled() ) {
			\add_filter( 'tiny_mce_before_init', [ self::class, 'filter_tiny_mce_before_init' ], 10, 2 );
			\add_action( 'admin_enqueue_scripts', [ self::class, 'on_admin_enqueue_scripts' ] );
		}
	}

	/**
	 * Add styles for the classic.
	 */
	public static function classic_editor_style() {
		\add_editor_style( 'assets/css/wpex-custom-properties.min.css' );
		\add_editor_style( 'assets/css/wpex-utility.min.css' );
		\add_editor_style( 'assets/css/admin/classic-editor-base.min.css' );
	}

	/**
	 * Adds inline CSS for the editor to match Customizer settings.
	 */
	public static function filter_tiny_mce_before_init( $settings, $editor_id ) {
		$editor_id_list = (array) \apply_filters( 'totaltheme/editor/editor_styles/tiny_mce/editor_id_list', [
			'content',
			'excerpt', // WooCommerce
			'classic-block',
			'vc-hidden-editor',
		] );

		if ( ! \in_array( $editor_id, $editor_id_list, true ) ) {
			return $settings;
		}

		if ( \file_exists( \WPEX_THEME_DIR . '/assets/css/admin/classic-editor.min.css' ) ) {
			$stylesheets = $settings['content_css'] ?? '';
			$theme_css = str_replace( ',', '%2C', wpex_asset_url( 'css/admin/classic-editor.min.css?v=' . WPEX_THEME_VERSION ) );
			$settings['content_css'] = trim( "{$stylesheets},{$theme_css}", ' ,' );
		}

		if ( $editor_style = self::get_editor_css( 'classic' ) ) {
			$content_style = $settings['content_style'] ?? '';
			$content_style .= str_replace( '"', "'", $editor_style ); // convert double quotes to single quotes to prevent errors.
			$settings['content_style'] = $content_style;
		}

        return $settings;
	}

	/**
	 * Hooks into "admin_enqueue_scripts".
	 */
	public static function on_admin_enqueue_scripts( $hook ) {
		if ( $hook !== 'post-new.php' && $hook !== 'post.php' ) {
			return;
		}

		self::enqueue_custom_fonts();
	}

	/**
	 * Enqueues custom defined typography fonts for use with the editor styles.
	 */
	public static function enqueue_custom_fonts(): void {
		foreach ( \array_keys( self::get_typography_settings_list() ) as $font_setting ) {
			$font_family = \get_theme_mod( "{$font_setting}_typography" )['font-family'] ?? '';
			if ( $font_family
				&& 'custom' !== \wpex_get_font_type( $font_family )
				&& $font = \wpex_enqueue_font( $font_family )
			) {
				\add_editor_style( $font );
			}
		}
	}

	/**
	 * Hooks into "enqueue_block_assets".
	 */
	public static function on_enqueue_block_assets() {
		\wp_enqueue_style(
			'totaltheme-gutenberg-blocks',
			\get_theme_file_uri( '/assets/css/gutenberg/blocks.min.css' ),
			[],
			\WPEX_THEME_VERSION
		);
		if ( self::is_enabled() ) {
			\wp_enqueue_style(
				'totaltheme-gutenberg-editor',
				\get_theme_file_uri( '/assets/css/gutenberg/editor.min.css' ),
				[],
				\WPEX_THEME_VERSION
			);
			\wp_add_inline_style( 'totaltheme-gutenberg-editor', self::get_editor_css( 'gutenberg' ) );
		}
	}

	/**
	 * Hooks into "enqueue_block_editor_assets".
	 */
	public static function on_enqueue_block_editor_assets() {
		\_deprecated_function( __METHOD__, 'Total Theme 5.20' );
	}

	/**
	 * Returns array of typography settings to loop through and insert editor styles for.
	 */
	private static function get_typography_settings_list(): array {
		if ( ! \get_theme_mod( 'typography_enable', true ) ) {
			return [];
		}

		return [
			'body'         => '',
			'post_content' => '',
			'headings'     => ':is(h1,h2,h3,h4,h5,h6,.wpex-h1,.wpex-h2,.wpex-h3,.wpex-h4,.wpex-h5,.wpex-h6,h1.wp-block,h2.wp-block,h3.wp-block,h4.wp-block,h5.wp-block,h6.wp-block)',
			'blockquote'   => 'blockquote',
			'entry_h1'     => ':is(h1,h1.wp-block,.wpex-h1)',
			'entry_h2'     => ':is(h2,h2.wp-block,.wpex-h2)',
			'entry_h3'     => ':is(h3,h3.wp-block,.wpex-h3)',
			'entry_h4'     => ':is(h4,h4.wp-block,.wpex-h4)',
			'button'       => '',
		];
	}

	/**
	 * Generate inline CSS for the WP editor based on Customizer settings.
	 */
	public static function get_editor_css( $editor = 'classic' ) {
		$parent_selector = ( 'gutenberg' === $editor ) ? '.editor-styles-wrapper' : '.mce-content-body';

		\ob_start();

		// Classic Editor only
		if ( 'classic' === $editor ) {
			// Font Smoothing.
			if ( \get_theme_mod( 'enable_font_smoothing', false ) ) {
				echo "{$parent_selector}{-webkit-font-smoothing:antialiased!important;}";
			} else {
				echo "{$parent_selector}{-webkit-font-smoothing:auto!important;}";
			}
		}

		// Link styles
		if ( \get_theme_mod( 'link_underline' ) ) {
			echo "{$parent_selector}{--wpex-link-decoration-line:underline;}";
		}

		if ( $link_color = \get_theme_mod( 'link_color' ) ) {
			$link_color_parsed = \wpex_parse_color( $link_color );
			if ( $link_color_parsed && $link_color_parsed_safe = \esc_attr( $link_color_parsed ) ) {
				echo "{$parent_selector}{--wpex-link-color:{$link_color_parsed_safe};}";
			}
		}

		if ( $link_underline_color = \get_theme_mod( 'link_underline_color' ) ) {
			$link_underline_color_parsed = \wpex_parse_color( $link_underline_color );
			if ( $link_underline_color_parsed && $link_underline_color_safe = \esc_attr( $link_underline_color_parsed ) ) {
				echo "{$parent_selector}{--wpex-link-decoration-color:{$link_underline_color_safe};}";
			}
		}

		if ( $link_offset = \get_theme_mod( 'link_underline_offset' ) ) {
			$link_offset_safe = \esc_attr( $link_offset );
			if ( $link_offset_safe ) {
				echo "{$parent_selector}{--wpex-link-underline-offset:{$link_offset_safe};}";
			}
		}

		if ( $link_thickness = \get_theme_mod( 'link_underline_thickness' ) ) {
			$link_thickness_safe = \esc_attr( $link_thickness );
			if ( $link_thickness_safe ) {
				echo "{$parent_selector}{--wpex-link-decoration-thickness:{$link_thickness_safe};}";
			}
		}

		// DOC Font size
		if ( totaltheme_has_classic_styles() ) {
			echo "{$parent_selector}{--wpex-body-font-size:13px}";
		}

		// Body bg color
		$layout = get_post_meta( get_the_ID(), 'wpex_main_layout', true ) ?: \get_theme_mod( 'main_layout_style' );
		if ( 'boxed' !== $layout ) {
			$body_color = get_post_meta( get_the_ID(), 'wpex_page_background_color', true ) ?: get_theme_mod( 't_background_color' );
			if ( $body_color && $body_color_safe = wpex_parse_color( $body_color ) ) {
				echo "{$parent_selector}{background-color:{$body_color_safe};}";
			}
		}

		// Loop through typography settings that should apply in the editor
		if ( $typography_settings = self::get_typography_settings_list() ) {

			if ( 'gutenberg' === $editor ) {
				$parent_selector = '.editor-styles-wrapper > *';
			}

			$allowed_properties = [
				'color',
				'margin',
				'font-size',
				'font-weight',
				'font-family',
				'font-style',
				'line-height',
				'letter-spacing',
				'text-transform',
			];

			foreach ( $typography_settings as $setting_name => $selector ) {
				
				$theme_mod = \get_theme_mod( "{$setting_name}_typography" );
				
				if ( empty( $theme_mod ) || ! \is_array( $theme_mod )) {
					continue;
				}

				$el_css = '';

				foreach ( $theme_mod as $property => $value ) {
					if ( ! in_array( $property, $allowed_properties, true ) ) {
						continue;
					}
					
					// Only get the first value if it's an array
					if ( \is_array( $value ) ) {
						$value = \reset( $value );
					}

					// Remove extra quotes - fix for old redux settings
					$value = \str_replace( '"', '', $value );

					// Sanitize value
					$value_safe = \wpex_sanitize_data( $value, $property );

					if ( $value_safe ) {
						if ( 'button' === $setting_name ) {
							$css_vars = [
								'text-transform' => '--wpex-btn-text-transform',
								'letter-spacing' => '--wpex-btn-letter-spacing',
								'font-family'    => '--wpex-btn-font-family',
								'font-style'     => '--wpex-btn-font-style',
								'font-weight'    => '--wpex-btn-font-weight',
								'line-height'    => '--wpex-btn-line-height',
								'font-size'      => '--wpex-btn-font-size',
							];
							if ( ! isset( $css_vars[ $property ] ) ) {
								continue;
							}
							$property = $css_vars[ $property ];
						}

						$el_css .= "{$property}:{$value_safe};";
					}

					// Custom fonts CSS
					if ( 'font-family' === $property && 'custom' === \wpex_get_font_type( $value ) ) {
						echo \wpex_render_custom_font_css( $value );
					}
				}
				
				if ( $el_css ) {
					$selector = $selector ? "{$parent_selector} {$selector}" : $parent_selector;
					echo "{$selector}{{$el_css}}";
				}

			}

		}

		// Add customizer CSS
		echo self::get_customizer_settings_css( $parent_selector, $editor );

		return \ob_get_clean();
	}

	/**
	 * Get customizer styles.
	 */
	private static function get_customizer_settings_css( string $parent_selector, string $editor ): string {
		$css = '';

		// Border style blockquote.
		$border_width = \get_theme_mod( 'blockquote_border_width' );
		if ( $border_width && $border_width_safe = \absint( $border_width ) ) {
			$css .= "{$parent_selector} blockquote{border-width:0;border-inline-start-width:{$border_width_safe}px;border-color:var(--wpex-accent);border-style:solid;padding-inline-start:25px;}";
			$css .= "{$parent_selector} blockquote::before{display:none;}";
		}

		// Customizer settings that should add CSS in the Editor.
		$settings = [
			$parent_selector => [
				[ 'theme_button_padding', '--wpex-btn-padding' ],
				[ 'theme_button_border_radius', '--wpex-btn-border-radius' ],
				[ 'theme_button_color', '--wpex-btn-color' ],
				[ 'theme_button_bg', '--wpex-btn-bg' ],
				[ 'theme_button_border_style', '--wpex-btn-border-style' ],
				[ 'theme_button_border_width', '--wpex-btn-border-width' ],
				[ 'theme_button_border_color', '--wpex-btn-border-color' ],
				[ 'theme_button_hover_color', '--wpex-hover-btn-color' ],
				[ 'theme_button_hover_bg', '--wpex-hover-btn-bg' ],
				[ 'theme_button_hover_border_color', '--wpex-hover-btn-border-color' ],
			],
			"{$parent_selector} blockquote" => [
				[ 'blockquote_background', 'background' ],
				[ 'blockquote_color', 'color' ],
				[ 'blockquote_padding', 'padding' ],
				[ 'blockquote_border_color', 'border-color' ],
			],
		];

		if ( 'gutenberg' === $editor ) {
			$settings[ $parent_selector ] = \array_merge( $settings[ $parent_selector ], [
				[ 'input_padding', '--wpex-input-padding' ],
				[ 'input_border_radius', '--wpex-input-border-radius' ],
				[ 'input_font_size', '--wpex-input-font-size' ],
				[ 'input_color', '--wpex-input-color' ],
				[ 'input_color', '--wpex-focus-input-color' ],
				[ 'input_background', '--wpex-input-bg' ],
				[ 'input_background', '--wpex-focus-input-bg' ],
				[ 'input_border', '--wpex-input-border-width' ],
			] );
			$settings['[class*=wp-block-vcex-]'] = [
				[ 'label_color', 'color' ],
			];
		}

		foreach ( $settings as $selector => $mods ) {
			$el_css = '';
			foreach ( $mods as $mod ) {
				$mod_name = $mod[0];
				$property = $mod[1];
				$mod_value = \get_theme_mod( $mod_name );
				if ( $mod_value && $mod_value_safe = \wpex_sanitize_data( $mod_value, $property ) ) {
					if ( \str_contains( $mod_value_safe, ':' ) ) {
						$mod_array = \totaltheme_parse_css_multi_property( $mod_value_safe, $property );
						foreach ( $mod_array as $sub_prop => $sub_val ) {
							$el_css .= "{$sub_prop}:{$sub_val};";
						}
					} else {
						$el_css .= "{$property}:{$mod_value_safe};";
					}
				}
			}
			if ( $el_css ) {
				$css .= "{$selector}{{$el_css}}";
			}
		}

		return $css;
	}

}
