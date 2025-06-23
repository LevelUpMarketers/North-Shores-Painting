<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Polylang_Switcher {

	public function __construct() {
		if ( ! \shortcode_exists( 'polylang_switcher' ) ) {
			\add_shortcode( 'polylang_switcher', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		if ( ! \function_exists( 'pll_the_languages' ) ) {
			return;
		}

		\extract( \shortcode_atts( [
			'dropdown'               => false,
			'show_flags'             => true,
			'show_names'             => false,
			'classes'                => '',
			'hide_if_empty'          => true,
			'force_home'             => false,
			'hide_if_no_translation' => false,
			'hide_current'           => false,
			'post_id'                => null,
			'raw'                    => false,
			'echo'                   => 0
		], $atts ) );

		$output = '';

		$dropdown   = 'true' == $dropdown;
		$show_flags = 'true' == $show_flags;
		$show_names = 'true' == $show_names;

		if ( $dropdown ) {
			$show_flags = $show_names = false;
		}

		$classes = 'polylang-switcher-shortcode wpex-inline-flex wpex-flex-wrap wpex-items-center wpex-list-none wpex-m-0 wpex-p-0 wpex-last-mr-0';
		if ( $show_names && ! $dropdown ) {
			$classes .= ' flags-and-names';
		}

		if ( ! $dropdown ) {
			$output .= '<ul class="' . \esc_attr( $classes ) . '">';
		}

		$output .= \pll_the_languages( [
			'dropdown'               => $dropdown,
			'show_flags'             => $show_flags,
			'show_names'             => $show_names,
			'hide_if_empty'          => $hide_if_empty,
			'force_home'             => $force_home,
			'hide_if_no_translation' => $hide_if_no_translation,
			'hide_current'           => $hide_current,
			'post_id'                => $post_id,
			'raw'                    => $raw,
			'echo'                   => $echo,
		] );

		if ( $show_names ) {
			$output = \str_replace( 'class="lang-item', 'class="polylang-switcher-shortcode__item lang-item wpex-mr-10', $output );
		} else {
			$output = \str_replace( 'class="lang-item', 'class="polylang-switcher-shortcode__item lang-item wpex-mr-5', $output );
		}

		if ( ! $dropdown ) {
			$output .= '</ul>';
		}

		return $output;
	}

}
