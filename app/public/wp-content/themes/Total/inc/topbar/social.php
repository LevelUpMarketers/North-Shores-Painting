<?php

namespace TotalTheme\Topbar;

\defined( 'ABSPATH' ) || exit;

/**
 * Topbar Social.
 */
class Social {

	/**
	 * Topbar social is enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * Topbar social icon style.
	 */
	protected static $icon_style;

	/**
	 * Stores the topbar social template id if defined.
	 */
	protected static $template_id;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Checks if the overlay topbar is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		$check = \get_theme_mod( 'top_bar_social', true );

		if ( $check
			&& ( empty( self::get_profile_options() ) || empty( self::get_registered_profiles() ) )
		) {
			$check = false;
		}

		if ( ! $check && self::get_alt_content() ) {
			$check = true;
		}

		$check = \apply_filters( 'wpex_has_topbar_social', $check ); // @deprecated
		self::$is_enabled = (bool) \apply_filters( 'totaltheme/topbar/social/is_enabled', $check );

		return self::$is_enabled;
	}

	/**
	 * Returns an array of social options.
	 */
	public static function get_profile_options(): array {
		$options = \wpex_social_profile_options_list();
		$options = \apply_filters( 'wpex_topbar_social_options', $options ); // @deprecated
		return (array) \apply_filters( 'totaltheme/topbar/social/choices', $options );
	}

	/**
	 * Returns an array of registered social profiles for the topbar.
	 */
	public static function get_registered_profiles() {
		$profiles = \get_theme_mod( 'top_bar_social_profiles' );
		if ( $profiles ) {
			// used to be saved as a string via sanitize_text_field
			if ( \is_string( $profiles ) ) {
				$profiles = \json_decode( $profiles, true );
			}
			// in 6.1 the field was updated to be saved as an array so each url passes through sanitize_url
			if ( \is_array( $profiles ) ) {
				$profiles = \array_filter( $profiles );
			}
		}
		return $profiles;
	}

	/**
	 * Returns topbar social icon style.
	 */
	public static function get_icon_style() {
		if ( ! \is_null( self::$icon_style ) ) {
			return self::$icon_style;
		}

		$style = \get_theme_mod( 'top_bar_social_style' ) ?: 'none';

		if ( 'colored-icons' == $style  || 'images' === $style ) {
			$style = 'flat-color-rounded'; // old styles deprecated in v4.9
		}

		$style = \apply_filters( 'wpex_topbar_social_style', $style ); // @deprecated

		self::$icon_style = (string) \apply_filters( 'totaltheme/topbar/social/icon_style', $style );

		return self::$icon_style;
	}

	/**
	 * Return the topbar social alt content from the theme mod.
	 */
	private static function get_alt_content_from_mod() {
		$alt_content = (string) \wpex_get_translated_theme_mod( 'top_bar_social_alt' );
		if ( $alt_content ) {
			return trim( $alt_content );
		}
	}

	/**
	 * Return topbar social template ID.
	 */
	public static function get_template_id() {
		if ( ! \is_null( self::$template_id ) ) {
			return self::$template_id;
		}
		$template_id = ''; // make sure it isn't null to prevent extra checks.
		$content = self::get_alt_content_from_mod();
		if ( \is_numeric( $content ) ) {
			$post_id = \wpex_parse_obj_id( $content, 'page' );
			$post    = \get_post( $post_id );
			if ( $post && ! \is_wp_error( $post ) ) {
				$template_id = $post_id;
			}
		}
		self::$template_id = $template_id;
		return self::$template_id;
	}

	/**
	 * Get alternative content.
	 */
	public static function get_alt_content() {
		$template_id = self::get_template_id();
		if ( $template_id ) {
			$content = \totaltheme_shortcode_unautop( \get_post_field( 'post_content', $template_id ) );
		} else {
			$content = self::get_alt_content_from_mod();
		}
		$content = \apply_filters( 'wpex_topbar_social_alt_content', $content ); // @deprecated
		return (string) \apply_filters( 'totaltheme/topbar/social/alt_content', $content );
	}

	/**
	 * Echo class attribute for the the topbar social wrapper element.
	 */
	public static function wrapper_class() {
		$class        = [];
		$topbar_style = \totaltheme_call_static( 'Topbar\Core', 'style' );
		$split_bk     = \totaltheme_call_static( 'Topbar\Core', 'breakpoint' );

		switch ( $topbar_style ) {
			case 'one':
				$class[] = 'top-bar-right';
				if ( \totaltheme_call_static( 'Topbar\Core', 'get_content' ) && $split_bk ) {
					$class[] = 'wpex-mt-10';
					$class[] = "wpex-{$split_bk}-mt-0";
				}
				break;
			case 'two':
				$class[] = 'top-bar-left';
				break;
			case 'three':
				$class[] = 'top-bar-centered';
				if ( \has_nav_menu( 'topbar_menu' ) || \totaltheme_call_static( 'Topbar\Core', 'get_content' ) ) {
					$class[] = 'wpex-mt-10'; // extra spacing for centered top bar when there is content
				}
				break;
		}

		if ( empty( self::get_alt_content() ) ) {
			$social_style = self::get_icon_style();
			if ( $social_style ) {
				$class[] = 'social-style-' . \sanitize_html_class( $social_style ); // @todo rename to top-bar-social--{style}
			}
		}

		if ( ! $split_bk ) {
			$class[] = 'wpex-flex-shrink-0';
		}

		$class = \apply_filters( 'wpex_topbar_social_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/topbar/social/wrapper_class', $class );

		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Render list.
	 */
	public static function render_list() {
		$profiles       = self::get_registered_profiles();
		$social_options = self::get_profile_options();

		if ( empty( $profiles ) || empty( $social_options ) ) {
			return;
		}

		$social_style    = self::get_icon_style();
		$link_target     = \get_theme_mod( 'top_bar_social_target', 'blank' );
		$class           = 'wpex-inline-flex wpex-flex-wrap wpex-gap-y-5 wpex-list-none wpex-m-0 wpex-last-mr-0';

		// Get list gap
		$gap = ( $gap = \get_theme_mod( 'top_bar_social_gap' ) ) ? \absint( $gap ) : null;
		if ( null === $gap ) {
			if ( 'none' === $social_style || 'default' === $social_style || empty( $social_style ) ) {
				$gap = 15;
			} else {
				$gap = 5;
			}
		}

		if ( $gap ) {
			$class .= " wpex-gap-x-{$gap}";
		}

		// Justify class
		$justify = ( 'three' === \totaltheme_call_static( 'Topbar\Core', 'style' ) ) ? 'center' : 'start';
		$justify_class = "wpex-justify-{$justify}";
		if ( $split_bk = \totaltheme_call_static( 'Topbar\Core', 'breakpoint' ) ) {
			if ( $collapse_align = \totaltheme_call_static( 'Topbar\Core', 'alignment' ) ) {
				$collapse_align = ( 'left' === $collapse_align ) ? 'start' : ( 'right' === $collapse_align ? 'end' : $collapse_align );
				if ( $justify !== $collapse_align ) {
					$justify_class  = "wpex-justify-{$collapse_align} wpex-{$split_bk}-justify-{$justify}";
				}
			}
		}
		if ( 'wpex-justify-start' !== $justify_class ) {
			$class .= " {$justify_class}";
		}

		// Begin output.
		$list = '<ul id="top-bar-social-list" class="' . \esc_attr( $class ) . '">';

		// Loop through social options
		$list_items = '';
		foreach ( $profiles as $site => $url ) {

			if ( ! $url || ! \array_key_exists( $site, $social_options ) ) {
				continue;
			}

			$site = \sanitize_key( $site );

			// Sanitize email and remove link target
			if ( 'email' === $site ) {
				$sanitize_email = \sanitize_email( $url );
				if ( \is_email( $url ) ) {
					$link_target = '';
					$sanitize_email = \antispambot( $sanitize_email );
					$url = "mailto:{$sanitize_email}";
				} elseif ( \str_contains( $url, 'mailto' ) ) {
					$link_target = '';
				}
			}

			// Add tel: to phone number
			if ( 'phone' === $site && ! \str_starts_with( $url, 'tel:' ) && ! \str_starts_with( $url, 'callto:' ) ) {
				$url = "tel:{$url}";
			}

			// Generate link HTML based on attributes and content
			$list_items .= '<li class="top-bar-social-list__item">';

				$label = $social_options[ $site ]['label'] ?? '';

				$link_attrs = [
					'href'   => $url,
					'target' => $link_target,
					'class'  => 'top-bar-social-list__link wpex-' . $site . ' ' . \wpex_get_social_button_class( $social_style ),
				];

				$link_attrs = \apply_filters( 'wpex_topbar_social_link_attrs', $link_attrs, $site ); // @deprecated
				$link_attrs = (array) \apply_filters( 'totaltheme/topbar/social/link_attributes', $link_attrs, $site );

				$icon_name = $social_options[ $site ]['icon_class'] ?? $social_options[ $site ]['icon'] ?? $site;
				$icon_html = \totaltheme_get_icon( $icon_name );

				if ( ! $icon_html ) {
					$icon_html = '<span class="' . esc_attr( $icon_name ) . '" aria-hidden="true"></span>';
				}

				if ( $icon_html ) {
					$icon_html .= '<span class="screen-reader-text">' . \esc_attr( $label ) . '</span>';
				} else {
					$icon_html = $label;
				}

			 	$list_items .= \wpex_parse_html( 'a', $link_attrs, $icon_html );

			 $list_items .= '</li>';

		} // endforeach

			$list_items = (string) \apply_filters( 'wpex_topbar_social_links_output', $list_items ); // @deprecated

			$list .= $list_items;

		$list .= '</ul>';

		echo (string) \apply_filters( 'wpex_topbar_social_list', $list );
	}

}
