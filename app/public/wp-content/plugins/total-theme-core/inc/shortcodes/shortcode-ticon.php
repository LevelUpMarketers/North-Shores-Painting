<?php

namespace TotalThemeCore\Shortcodes;

\defined( 'ABSPATH' ) || exit;

final class Shortcode_Ticon {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! \shortcode_exists( 'font_awesome' ) ) {
			\add_shortcode( 'font_awesome', [ self::class, 'output' ] );
		}

		if ( ! shortcode_exists( 'ticon' ) ) {
			\add_shortcode( 'ticon', [ self::class, 'output' ] );
		}
	}

	/**
	 * Shortcode callback function.
	 */
	public static function output( $atts, $content = '' ) {
		if ( \is_admin() && ! \wp_doing_ajax() ) {
			return;
		}

		\extract( \shortcode_atts( [
			'icon'          => '',
			'link'          => '',
			'link_title'    => '',
			'link_target'   => '',
			'link_rel'      => '',
			'margin_right'  => '',
			'margin_end'    => '',
			'margin_left'   => '',
			'margin_start'  => '',
			'margin_top'    => '',
			'margin_bottom' => '',
			'color'         => '',
			'size'          => '',
			'class'         => '',
			'bidirectional' => '',
		], $atts ) );

		$icon = $icon ? \sanitize_text_field( $icon ) : '';

		if ( ! $icon ) {
			return;
		}

		// Define vars.
		$output           = '';
		$link             = $link ? \sanitize_text_field( $link ) : '';
		$extra_class_safe = $class ? \esc_attr( $class ) : '';

		// Sanitize $icon
		if ( \apply_filters( 'wpex_font_awesome_shortcode_parse_fa', false ) ) {
			$icon = \str_replace( 'fa ', 'ticon ', $icon );
			$icon = \str_replace( 'fa-', 'ticon-', $icon );
		}

		// Generate inline styles.
		$style = [];

		if ( $color ) {
			if ( \function_exists( '\wpex_parse_color' ) ) {
				$color = \wpex_parse_color( $color );
			}
			if ( $color && $color_safe = \esc_attr( \sanitize_text_field( $color ) ) ) {
				$style[] = "color:{$color_safe};";
			}
		}

		$margins = [
			'left'         => $margin_left,
			'right'        => $margin_right,
			'inline-end'   => $margin_end,
			'inline-start' => $margin_start,
			'top'          => $margin_top,
			'bottom'       => $margin_bottom,
		];

		foreach ( \array_filter( $margins ) as $margin_name => $margin_val ) {
			$style[] = self::get_margin_style( $margin_val, $margin_name );
		}

		if ( ! in_array( $size, [ 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl' ], true ) ) {
			$size = sanitize_text_field( $size );
			if ( \is_numeric( $size ) ) {
				$size = "{$size}px";
			}
			$style[] = "font-size:{$size};";
			$size = '';
		}

		if ( $style ) {
			$style_escaped = ' style="' . \esc_attr( \implode( '', \array_filter( $style ) ) ) . '"';
		} else {
			$style_escaped = '';
		}

		// Open link tag.
		if ( $link ) {
			$a_attrs = [ 'href' => \esc_url( $link ) ];
			if ( $link_title ) {
				$a_attrs['title'] = \esc_attr( $link_title );
			}
			if ( $link_target ) {
				if ( ! \str_starts_with( '_', $link_target ) ) {
					$link_target = "_{$link_target}";
				}
				$a_attrs['target'] = \esc_attr( $link_target );
			}
			if ( $link_rel ) {
				$a_attrs['rel'] = \esc_attr( $link_rel );
			}
			$output .= '<a';
				foreach ( $a_attrs as $a_attrs_k => $a_attrs_v ) {
					$output .= ' ' . $a_attrs_k . '=' . '"' . $a_attrs_v . '"';
				}
			$output .= '>';
		}

		// Add icon.
		if ( \function_exists( '\totaltheme_get_icon' ) ) {
			$icon_html = (string) \totaltheme_get_icon( $icon, $extra_class_safe, $size, \wp_validate_boolean( $bidirectional ) );
			if ( $icon_html ) {
				if ( $style_escaped ) {
					$icon_html = \str_replace( '<span', '<span ' . $style_escaped, $icon_html );
				}
				$output .= $icon_html;
			}
		}

		// Close link tag.
		if ( $link ) {
			$output .= '</a>';
		}

		return $output;
	}

	/**
	 * Shortcode callback function.
	 */
	private static function get_margin_style( $margin, $dir ): string {
		$margin_safe = \sanitize_text_field( $margin );
		if ( \is_numeric( $margin_safe ) ) {
			$margin_safe = "{$margin_safe}px";
		}
		return "margin-{$dir}:{$margin_safe};";
	}

}
