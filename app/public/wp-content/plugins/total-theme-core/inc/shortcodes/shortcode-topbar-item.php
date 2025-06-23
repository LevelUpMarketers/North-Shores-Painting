<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Topbar_Item {

	public function __construct() {
		if ( ! \shortcode_exists( 'topbar_item' ) ) {
			\add_shortcode( 'topbar_item', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		$atts = shortcode_atts( [
			'type'            => '',
			'icon'            => '',
			'icon_logged_in'  => '',
			'text'            => '',
			'text_logged_in'  => '',
			'link'            => '',
			'link_target'     => '',
			'link_rel'        => '',
			'login_redirect'  => '',
			'logout_redirect' => '',
			'spacing'         => '20',
			'class'           => '',
		], $atts, 'topbar_item' );

		$has_icon        = false;
		$user_logged_in  = is_user_logged_in();
		$topbar_split_bk = 'md';

		if ( \function_exists( 'totaltheme_call_static' ) ) {
			$topbar_split_bk = totaltheme_call_static( 'Topbar\Core', 'breakpoint' );
		}

		// Get item content/text.
		if ( empty( $content ) && ! empty( $atts['text'] ) ) {
			$content = $atts['text'];
		}

		// Login type item.
		if ( ! empty( $atts['type'] ) && 'login' === $atts['type'] ) {
			if ( $user_logged_in ) {
				$logout_redirect = ! empty( $atts['logout_redirect'] ) ? $atts['logout_redirect'] : home_url( '/' );
				$atts['link'] = wp_logout_url( $logout_redirect );
			} else {
				if ( empty( $atts['link'] ) ) {
					$atts['link'] = wp_login_url( $atts['login_redirect'] );
				}
			}
		}

		// Custom logged in icon, text, etc.
		if ( $user_logged_in ) {
			if ( ! empty( $atts['text_logged_in'] ) ) {
				$content = $atts['text_logged_in'];
			}
			if ( ! empty( $atts['icon_logged_in'] ) ) {
				$atts['icon'] = $atts['icon_logged_in'];
			}
		}

		// Item content is required.
		if ( ! $content ) {
			return;
		}

		// Get topbar style.
		$topbar_style = '';

		if ( \function_exists( 'totaltheme_call_static' ) ) {
			$topbar_style = totaltheme_call_static( 'Topbar\Core', 'style' );
		}

		// Start output.
		$html = '';

		// Add icon.
		if ( ! empty( $atts['icon'] )
			&& function_exists( 'totaltheme_get_icon' )
			&& $icon = totaltheme_get_icon( $atts['icon'], 'wpex-mr-10' )
		) {
			$has_icon = true;
			$html .= $icon;
		}

		// Open link.
		if ( ! empty( $atts['link'] ) ) {
			if ( \str_contains( $atts['link'], '@' ) ) {
				if ( \str_starts_with( $atts['link'], 'mailto:' ) ) {
					$atts['link'] = 'mailto:' . \antispambot( \str_replace( 'mailto:', '', \wp_strip_all_tags( $atts['link'] ) ) );
				} elseif ( \is_email( $atts['link'] ) ) {
					$atts['link'] = 'mailto:' . \antispambot( \sanitize_email( $atts['link'] ) );
				}
			}
			$html .= '<a href="' . esc_url( trim( $atts['link'] ) ) . '"';
			if ( ! empty( $atts['link_target'] ) ) {
				$html .= 'target="' . esc_attr( trim( $atts['link_target'] ) ) . '"';
				if ( 'blank' === $atts['link_target'] || '_blank' === $atts['link_target'] ) {
					if ( empty( $atts['link_rel'] ) ) {
						$atts['link_rel'] = 'noopener';
					} elseif ( is_string( $atts['link_rel'] ) && false === strpos( $atts['link_rel'], 'noopener' ) ) {
						$atts['link_rel'] .= ' noopener';
					}
				}
			}
			if ( ! empty( $atts['link_rel'] ) ) {
				$html .= 'rel="' . esc_attr( trim( $atts['link_rel'] ) ) . '"';
			}
			$html .= '>';
		}

		// Add content/text.
		if ( $content ) {
			if ( function_exists( 'vcex_parse_text_safe' ) ) {
				$html .= vcex_parse_text_safe( $content );
			} else {
				$html .= do_shortcode( wp_kses_post( $content ) );
			}
		}

		// Close link.
		if ( ! empty( $atts['link'] ) ) {
			$html .= '</a>';
		}

		// Item wrap classes.
		$shortcode_class = 'top-bar-item';

		if ( 'none' == $topbar_split_bk || ! $topbar_split_bk ) {
			$shortcode_class .= ' wpex-inline-block';
		} else {
			$item_bk = get_theme_mod( 'topbar_item_breakpoint' ) ?: 'sm';
			if ( 'none' === $item_bk ) {
				$shortcode_class .= ' wpex-inline-block';
			} else {
				$shortcode_class .= ' wpex-' . esc_attr( $item_bk ) . '-inline-block';
			}
		}

		if ( isset( $atts['spacing'] ) ) {
			$spacing_escaped = absint( $atts['spacing'] );
			if ( 0 !== $spacing_escaped ) {
				switch ( $topbar_style ) {
					case 'one':
						$shortcode_class .= ' wpex-mr-' . $spacing_escaped;
						break;
					case 'two':
						$shortcode_class .= ' wpex-ml-' . $spacing_escaped;
						break;
					case 'three':
						$shortcode_class .= ' wpex-mx-' . $spacing_escaped;
						break;
				}
			}
		}

		if ( ! empty( $atts['class'] ) ) {
			$shortcode_class .= ' ' . $atts['class'];
		}

		if ( $has_icon && \function_exists( 'totaltheme_call_static' ) && 'svg' === \totaltheme_call_static( 'Theme_Icons', 'get_format' ) ) {
			$html = '<span class="top-bar-item__inner wpex-inline-flex wpex-items-center">' . $html . '</span>';
		}

		return '<div class="' . esc_attr( $shortcode_class )  . '">' . $html . '</div>';
	}

}
