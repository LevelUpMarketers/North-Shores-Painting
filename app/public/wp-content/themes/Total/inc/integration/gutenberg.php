<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Gutenberg Integration Class.
 */
final class Gutenberg {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		\add_filter( 'wp_theme_json_data_theme', [ self::class, '_filter_wp_theme_json_data_theme' ] );
		\add_action( 'after_setup_theme', [ self::class, '_on_after_setup_theme' ] );
		\add_action( 'init', [ self::class, '_on_init' ] );
		if ( \is_admin() ) {
			\add_action( 'enqueue_block_assets', [ self::class, '_on_enqueue_block_assets' ] );
		}
	}

	/**
	 * Runs on the "after_setup_theme" hook.
	 */
	public static function _on_after_setup_theme(): void {
		if ( ! self::is_block_editor_enabled() ) {
			return;
		}

		if ( ! \totaltheme_has_classic_styles() ) {
			self::register_editor_font_sizes();
		}

		\add_theme_support( 'gutenberg-editor' );

		if ( \apply_filters( 'wpex_color_palette_gutenberg_support', true ) ) {
			Gutenberg\Editor_Color_Palette::register_colors();
		}
	}

	/**
	 * Runs on the "init" hook.
	 */
	public static function _on_init(): void {
		if ( \current_theme_supports( 'gutenberg-editor' ) ) {
			self::enabled();
		} else {
			self::disabled();
		}
	}

	/**
	 * Runs when Gutenberg is enabled.
	 */
	private static function enabled(): void {
		\totaltheme_init_class( __CLASS__ . '\Register_Block_Styles' );

		if ( \apply_filters( 'wpex_has_block_gallery_lightbox_integration', true ) ) {
			\totaltheme_init_class( __CLASS__ . '\Block_Gallery_Lightbox' );
		}
	}

	/**
	 * Runs when Gutenberg is disabled.
	 */
	private static function disabled(): void {
		$remove_css = \apply_filters( 'wpex_remove_block_library_css', true ); // @deprecated
		$remove_css = (bool) \apply_filters( 'totaltheme/integration/gutenberg/remove_core_styles/enabled', true );
		if ( $remove_css ) {
			\totaltheme_init_class( __CLASS__ . '\Dequeue_Styles' );
		}
	}

	/**
	 * Checks if the block editor is enabled.
	 */
	public static function is_block_editor_enabled(): bool {
		return ! ( class_exists( 'Classic_Editor', false )
			|| ( defined( 'WPEX_VC_ACTIVE' ) && WPEX_VC_ACTIVE && self::wpb_gutenberg_disabled_check() ) );
	}

	/**
	 * Runs on the "enqueue_block_assets" hook.
	 *
	 * @important We must re-register the styles here because the admin_enqueue_scripts
	 *            hook won't fire when Gutenberg is inside an iFrame.
	 */
	public static function _on_enqueue_block_assets() {
		\wp_enqueue_style(
			'wpex-custom-properties',
			\get_theme_file_uri( '/assets/css/wpex-custom-properties.min.css' ),
			[],
			\WPEX_THEME_VERSION,
			'all'
		);
		\wp_enqueue_style(
			'wpex-utility',
			\get_theme_file_uri( '/assets/css/wpex-utility.min.css' ),
			[],
			\WPEX_THEME_VERSION,
			'all'
		);

		// Better typography when Editor styles are disabled.
		if ( ! \totaltheme_call_static( 'Admin\Editor_Styles', 'is_enabled' ) ) {
			\wp_register_style( 'totaltheme-gutenberg-typography', false );
			$css = '
				.editor-styles-wrapper {
					font-size: 1.125rem;
					line-height: 1.5;
					color: #424242;
				}
			';
			\wp_add_inline_style( 'totaltheme-gutenberg-typography', $css );
			\wp_enqueue_style( 'totaltheme-gutenberg-typography' );
		}
	}

	/**
	 * Checks if the block editor is enabled.
	 */
	protected static function wpb_gutenberg_disabled_check(): bool {
		if ( \get_option( 'wpb_js_gutenberg_disable' ) ) {
			return true;
		}
		return totaltheme_call_static( 'Integration\WPBakery\Slim_Mode', 'is_enabled' ) && totaltheme_call_static( 'Integration\WPBakery\Helpers', 'is_frontend_edit_mode' );
	}

	/**
	 * Register editor font sizes.
	 */
	private static function register_editor_font_sizes(): void {
		$font_sizes = wpex_utl_font_sizes();

		if ( ! $font_sizes ) {
			return;
		}

		$editor_font_sizes = [];

		foreach ( $font_sizes as $slug => $name ) {
			if ( $slug ) {
				$editor_font_sizes[] = [
					'name' => $name,
					'slug' => $slug,
					'size' => "var(--wpex-text-{$slug})",
				];
			}
		}

		\add_theme_support( 'editor-font-sizes', $editor_font_sizes );
	}

	/**
	 * Filters the theme.json so we can make tweaks based on PHP.
	 */
	public static function _filter_wp_theme_json_data_theme( $theme_json ) {
		$new_data = [];

		if ( ! \totaltheme_has_classic_styles() ) {
			$new_data = [
				'version'  => 3, // without this it won't work!
				'settings' => [
					'typography' => [
						'defaultFontSizes' => false,
					]
				],
			];
		}

		if ( $new_data ) {
			return $theme_json->update_with( $new_data );
		}
		
		return $theme_json;
	}

}
