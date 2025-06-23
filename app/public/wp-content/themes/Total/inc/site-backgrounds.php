<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom site backgrounds.
 */
class Site_Backgrounds {

	/**
	 * Init.
	 */
	public static function init() {
		\add_filter( 'wpex_head_css', [ self::class, 'update_head_css' ], 999 );
	}

	/**
	 * Hooks into wpex_head_css to add custom css to the <head> tag.
	 */
	public static function update_head_css( $head_css ) {
		$css      = '';
		$image    = (string) \get_theme_mod( 't_background_image' ); // converted from background_img in 4.3 to prevent conflict with WP
		$style    = (string) \get_theme_mod( 't_background_style' );
		$position = (string) \get_theme_mod( 't_background_position' );
		$pattern  = (string) \get_theme_mod( 't_background_pattern' );
		$post_id  = \wpex_get_current_post_id();

		// Check Theme Settings post metabox.
		if ( $post_id ) {

			// Color.
			$single_color = (string) \get_post_meta( $post_id, 'wpex_page_background_color', true );

			// Image.
			$single_image = \get_post_meta( $post_id, 'wpex_page_background_image_redux', true );
			if ( $single_image ) {
				if ( is_array( $single_image ) ) {
					$single_image = ( ! empty( $single_image['url'] ) ) ? $single_image['url'] : '';
				} else {
					$single_image = (string) $single_image;
				}
			} else {
				$single_image = (string) \get_post_meta( $post_id, 'wpex_page_background_image', true );
			}

			// Background style.
			$single_style = (string) \get_post_meta( $post_id, 'wpex_page_background_image_style', true );

		}

		// Sanitize meta data.
		$color = ( ! empty( $single_color ) && '#' !== $single_color ) ? $single_color : '';
		$style = ( ! empty( $single_image ) && ! empty( $single_style ) ) ? $single_style : $style;
		$image = ! empty( $single_image ) ? $single_image : $image;

		// Create array of background settings.
		$settings = [
			'color'    => $color,
			'image'    => $image,
			'style'    => $style,
			'pattern'  => $pattern,
			'position' => $position,
		];

		$settings = (array) \apply_filters( 'wpex_body_background_settings', $settings );

		if ( ! $settings ) {
			return;
		}

		extract( $settings );

		if ( $image && \is_numeric( $image ) ) {
			$image = \wp_get_attachment_image_src( $image, 'full' );
			$image = $image[0] ?? '';
		}

		$style = ! empty( $style ) ? $style : 'stretched';

		/*-----------------------------------------------------------------------------------*/
		/*  - Generate CSS
		/*-----------------------------------------------------------------------------------*/

		// Color.
		if ( ! empty( $color ) && '#' !== $color && $color_safe = \wpex_parse_color( $color ) ) {
			$css .= "--wpex-bg-color:{$color_safe}!important;";
		}

		// Image.
		if ( ! empty( $image ) && empty( $pattern ) ) {
			$bg_safe = esc_url( $image );
			$css .= "background-image:url({$bg_safe})!important;";
			$css .= \wpex_sanitize_data( $style, 'background_style_css' );
			if ( ! empty( $position ) ) {
				$position_safe = esc_attr( $position );
				$css .= "background-position:{$position_safe};";
			}
		}

		// Pattern.
		if ( ! empty( $pattern )
			&& \is_string( $pattern )
			&& $pattern_url = \get_theme_file_uri( 'assets/images/patterns/' . \sanitize_key( $pattern ) . '.png' )
		) {
			$css .= 'background-image:url(' . \esc_url( $pattern_url ) . ');background-repeat:repeat;';
		}

		/*-----------------------------------------------------------------------------------*/
		/*  - Return $css
		/*-----------------------------------------------------------------------------------*/
		if ( ! empty( $css ) ) {
			$head_css .= "/*SITE BACKGROUND*/body{{$css}}";
		}

		return $head_css;
	}

}
