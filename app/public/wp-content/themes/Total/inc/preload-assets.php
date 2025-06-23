<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Class used to insert links in the head for preloading assets.
 */
class Preload_Assets {

	/**
	 * Return array of links.
	 */
	public static function get_links(): array {
		$links      = [];
		$user_fonts = \wpex_get_registered_fonts();

		if ( ! empty( $user_fonts ) && \is_array( $user_fonts ) ) {

			foreach ( $user_fonts as $font => $args ) {

				switch ( $args['type'] ) {
					case 'google':
					case 'adobe':
						if ( ( isset( $args['preload'] ) && true === $args['preload'] ) && ( ! empty( $args['is_global'] ) || ! empty( $args['assign_to'] ) ) ) {
							$font_url = \wpex_enqueue_font( $font, $args['type'], $args );
							if ( $font_url ) {
								$links[] = [
									'href' => $font_url,
									'as'   => 'style',
								];
							}
						}
						break;
					case 'custom':
						if ( \is_array( $args['custom_fonts'] ) ) {
							foreach ( $args['custom_fonts'] as $custom_font_args ) {
								if ( isset( $custom_font_args['preload'] ) && \wp_validate_boolean( $custom_font_args['preload'] ) ) {
									if ( ! empty( $custom_font_args['woff2'] ) ) {
										$links[] = [
											'href'        => \esc_url( $custom_font_args['woff2'] ),
											'type'        => 'font/woff2',
											'as'          => 'font',
											'crossorigin' => true,
										];
									} elseif ( ! empty( $custom_font_args['woff'] ) ) {
										$links[] = [
											'href'        => \esc_url( $custom_font_args['woff'] ),
											'type'        => 'font/woff',
											'as'          => 'font',
											'crossorigin' => true,
										];
									}
								}
							}
						}
						break;
				}
			}
		}

		return (array) \apply_filters( 'wpex_preload_links', $links );
	}

	/**
	 * Add links to wp_head.
	 */
	public static function add_links(): void {
		if ( \defined( 'IFRAME_REQUEST' ) && \IFRAME_REQUEST ) {
			return; // prevents preloading in iFrames where it's not needed (like widgets block editor).
		}
		foreach ( self::get_links() as $link => $atts ) {
			if ( \array_key_exists( 'condition', $atts ) && false === $atts['condition'] ) {
				continue;
			}
			echo self::render_link( $atts );
		}
	}

	/**
	 * Renders a single link.
	 */
	private static function render_link( $atts = [] ): string {
		$link = '<link rel="preload" href="' . \esc_url( $atts['href'] ) . '"';
			if ( isset( $atts['type'] ) ) {
				$link .= ' type="' . \esc_attr( $atts['type'] ) . '"';
			}
			if ( isset( $atts['as'] ) ) {
				$link .= ' as="' . \esc_attr( $atts['as'] ) . '"';
			}
			if ( isset( $atts['media'] ) ) {
				$link .= ' media="' . \esc_attr( $atts['media'] ) . '"';
			}
			if ( isset( $atts['crossorigin'] ) ) {
				$link .= ' crossorigin';
			}
		$link .= '>';
		return $link;
	}

}
