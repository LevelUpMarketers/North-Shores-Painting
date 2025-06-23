<?php

namespace TotalTheme\Integration\Gutenberg;

\defined( 'ABSPATH' ) || exit;

/**
 * Registers color palette for the Gutenberg editor.
 */
class Editor_Color_Palette {

	/**
	 * Get color palette.
	 */
	public static function get_color_palette(): array {
		return (array) totaltheme_get_color_palette();
	}

	/**
	 * Register colors with the Gutenberg editor.
	 */
	public static function register_colors(): void {
		$color_palette = (array) self::get_color_palette();

		if ( empty( $color_palette ) ) {
			return;
		}

		$editor_palette = [];

		foreach ( $color_palette as $slug => $color ) {
			// These colors are not allowed in Gutenberg.
			if ( 'transparent' === $slug || 'currentColor' === $slug || 'term_color' === $slug ) {
				continue;
			}

			// Get color value.
			if ( ! empty( $color['css_var'] ) ) {
				$color_val = $color['css_var'];
				$color_val = "var({$color_val})";
			} else {
				$color_val = $color['color'] ?? '';
			}

			// Add color to Gutenberg palette.
			$editor_palette[] = [
				'slug'  => $slug,
				'name'  => $color['name'],
				'color' => $color_val,
			];
		}

		\add_theme_support( 'editor-color-palette', $editor_palette );
	}

}
