<?php

namespace TotalTheme\Header;

\defined( 'ABSPATH' ) || exit;

/**
 * Header Logo.
 */
class Logo {

	/**
	 * Stores the header logo image id.
	 */
	protected static $image_id;

	/**
	 * Stores the header logo retina image id.
	 */
	protected static $retina_image_id;

	/**
	 * Stores the header logo image src.
	 */
	protected static $image_src;

	/**
	 * Stores whether the header logo is an svg or not.
	 */
	protected static $is_image_svg;

	/**
	 * Stores the header logo icon.
	 */
	protected static $icon;

	/**
	 * Checks if the logo should scroll up on click.
	 */
	public static function has_scroll_top_link(): bool {
		$check = false;
		$check = \apply_filters( 'wpex_header_logo_scroll_top', $check ); // @deprecated
		$check = (bool) \apply_filters( 'totaltheme/header/logo/has_scroll_top_link', $check );
		if ( $post_id = \wpex_get_current_post_id() ) {
			$meta = \get_post_meta( $post_id, 'wpex_logo_scroll_top', true );
			if ( 'enable' === $meta ) {
				$check = true;
			} elseif ( 'disable' === $meta ) {
				$check = false;
			}
		}
		return (bool) $check;
	}

	/**
	 * Returns header logo link url.
	 */
	public static function get_link_url(): string {
		$url = '';
		if ( self::has_scroll_top_link() ) {
			$url = '#';
		} else {
			if ( wp_validate_boolean( \get_theme_mod( 'logo_has_link', true ) ) ) {
				$custom_link = (string) \get_theme_mod( 'logo_link_url' );
				if ( $custom_link ) {
					$url = $custom_link;
				} else {
					if ( \totaltheme_is_wpb_frontend_editor() ) {
						$url = \get_permalink();
					}
					$url = $url ?: \home_url( '/' );
				}
			}
		}
		$url = \apply_filters( 'wpex_header_logo_url', $url ); // @deprecated
		$url = \apply_filters( 'wpex_logo_url', $url ); // @deprecated
		return (string) \apply_filters( 'totaltheme/header/logo/link_url', $url );
	}

	/**
	 * Returns header logo image ID.
	 */
	public static function get_image_id() {
		if ( ! \is_null( self::$image_id ) ) {
			return self::$image_id;
		}
		$image_id = \wpex_get_translated_theme_mod( 'custom_logo' );
		$image_id = \apply_filters( 'wpex_header_logo_img_url', $image_id ); // @deprecated
		self::$image_id = \apply_filters( 'totaltheme/header/logo/image_id', $image_id );
		// @todo - move overlay header logo checks here.
		// return self::get_image( $parse_logo ); // @todo use recursive function?
		return self::$image_id;
	}

	/**
	 * Returns header logo image url.
	 */
	public static function get_image_url() {
		if ( $image_id = self::get_image_id() ) {
			return self::parse_image( $image_id );
		}
	}

	/**
	 * Returns header logo retina image.
	 */
	public static function get_retina_image_id() {
		if ( ! \is_null( self::$retina_image_id ) ) {
			return self::$retina_image_id;
		}
		if ( ! totaltheme_call_static( 'Header\Overlay', 'get_breakpoint' )
			&& totaltheme_call_static( 'Header\Overlay', 'is_enabled' )
			&& totaltheme_call_static( 'Header\Overlay', 'logo_img' )
		) {
			$image_id = totaltheme_call_static( 'Header\Overlay', 'logo_img_retina' );
		} else {
			$image_id = \wpex_get_translated_theme_mod( 'retina_logo' );
		}
		$image_id = \apply_filters( 'wpex_retina_logo_url', $image_id ); // @deprecated
		$image_id = \apply_filters( 'wpex_header_logo_img_retina_url', $image_id ); // @deprecated
		self::$retina_image_id = \apply_filters( 'totaltheme/header/logo/retina_image_id', $image_id );
		return self::$retina_image_id;
	}

	/**
	 * Returns header logo retina image.
	 */
	public static function get_retina_image_url() {
		if ( $image_id = self::get_retina_image_id() ) {
			return self::parse_image( $image_id );
		}
	}

	/**
	 * Returns header logo image src.
	 *
	 * @important wp_get_attachment_image_src can return [] or false.
	 */
	public static function get_image_src() {
		if ( ! \is_null( self::$image_src ) ) {
			return self::$image_src;
		}
		self::$image_src = [];
		if ( ! totaltheme_call_static( 'Header\Overlay', 'get_breakpoint' )
			&& totaltheme_call_static( 'Header\Overlay', 'is_enabled' )
		) {
			$overlay_logo = totaltheme_call_static( 'Header\Overlay', 'logo_img', false );
			if ( $overlay_logo && \is_numeric( $overlay_logo ) ) {
				self::$image_src = \wp_get_attachment_image_src( $overlay_logo, 'full', false );
			}
		} else {
			$logo_id = self::get_image_id();
			if ( $logo_id && \is_numeric( $logo_id ) ) {
				self::$image_src = \wp_get_attachment_image_src( $logo_id, 'full', false );
			}
		}
		return self::$image_src;
	}

	/**
	 * Return logo image width.
	 */
	public static function get_image_width() {
		$width = \get_theme_mod( 'logo_width' );
		$width = \apply_filters( 'logo_width', $width ); // @deprecated
		$width = (int) \apply_filters( 'totaltheme/header/logo/image_width', $width );
		if ( ! $width && ! self::is_image_svg() ) {
			$logo_src = self::get_image_src();
			if ( ! empty( $logo_src[1] ) ) {
				$width = \absint( $logo_src[1] );
			}
		}
		if ( $width ) {
			return \absint( $width );
		}
	}

	/**
	 * Return logo image height.
	 */
	public static function get_image_height() {
		$height = \get_theme_mod( 'logo_height' );
		$height = \apply_filters( 'logo_height', $height ); // @deprecated
		$height = (int) \apply_filters( 'totaltheme/header/logo/image_height', $height );
		if ( ! $height && ! self::is_image_svg() ) {
			$logo_src = self::get_image_src();
			if ( ! empty( $logo_src[2] ) ) {
				$height = \absint( $logo_src[2] );
			}
		}
		if ( $height ) {
			return \absint( $height );
		}
	}

	/**
	 * Checks if the header logo image is an svg or not.
	 */
	public static function is_image_svg(): bool {
		if ( ! \is_null( self::$is_image_svg ) ) {
			return self::$is_image_svg;
		}
		$check = false;
		if ( $logo = self::get_image_id() ) {
			if ( \is_numeric( $logo ) ) {
				$mime_type = get_post_mime_type( $logo );
				if ( 'image/svg+xml' === $mime_type ) {
					$check = true;
				}
			} elseif ( \is_string( $logo ) ) {
				$check = \str_contains( $logo, '.svg' ); // @todo should we change to str_ends_with?
			}
		}
		$check = \apply_filters( 'wpex_header_logo_is_svg', $check ); // @deprecated
		self::$is_image_svg = (bool) \apply_filters( 'totaltheme/header/logo/is_image_svg', $check );
		return self::$is_image_svg;
	}

	/**
	 * Returns header logo icon.
	 */
	public static function get_icon() {
		if ( ! \is_null( self::$icon ) ) {
			return self::$icon;
		}

		$html = '';

		$custom_icon = (int) \wpex_get_translated_theme_mod( 'logo_icon_img' );

		// Custom image based icon
		if ( $custom_icon ) {
			$custom_icon_image_url = \wp_get_attachment_image_url( $custom_icon, 'full' );
			if ( $custom_icon_image_url ) {
				$dims = (string) \get_theme_mod( 'logo_icon_img_dims' );
				$dims_escaped = \esc_attr( \absint( $dims ) );
				$img_attrs = [
					'src'    => \esc_url( $custom_icon_image_url ),
					'width'  => $dims_escaped ?: null,
					'height' => $dims_escaped ?: null, // it will use auto anyway
					'alt'    => \wpex_get_attachment_data( $custom_icon, 'alt' ),
				];
				$html = '<span id="site-logo-icon" class="wpex-inline-flex wpex-items-center wpex-flex-shrink-0 wpex-max-h-100 wpex-mr-10" aria-hidden="true"><img ' . \wpex_parse_attrs( $img_attrs ) . '></span>';
			}
		}

		// Theme icon based icon
		else {
			$icon = ( $icon = \get_theme_mod( 'logo_icon' ) ) ? \sanitize_text_field( $icon ) : '';
			$icon = (string) \apply_filters( 'wpex_header_logo_icon', $icon );
			if ( $html = \totaltheme_get_icon( $icon ) ) {
				$html = '<span id="site-logo-fa-icon" class="site-logo-text__icon wpex-mr-10">' . $html . '</span>';
			}
		}

		$html = \apply_filters( 'wpex_header_logo_icon_html', $html ); // @deprecated
		self::$icon = (string) \apply_filters( 'totaltheme/header/logo/icon', $html );

		return self::$icon;
	}

	/**
	 * Returns header logo text.
	 */
	public static function get_text(): string {
		$text = \get_theme_mod( 'logo_text' );
		if ( empty( $text ) || ! \is_string( $text ) ) {
			$text = \get_bloginfo( 'name' );
		}
		$text = \apply_filters( 'wpex_header_logo_text', $text ); // @deprecated
		$text = \apply_filters( 'wpex_logo_title', $text ); // @deprecated
		return (string) \apply_filters( 'totaltheme/header/logo/text', $text );
	}

	/**
	 * Return logo image class.
	 */
	public static function get_image_class(): string {
		$class = [
			'logo-img',
			'wpex-h-auto',
			'wpex-max-w-100',
			'wpex-align-middle',
		];
		$class = \apply_filters( 'wpex_header_logo_img_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/header/logo/image_class', $class );
		return \implode( ' ', \array_map( '\esc_attr', $class ) );
	}

	/**
	 * Return logo text classes.
	 */
	public static function get_text_class(): string {
		$class = [
			'site-logo-text',
			'wpex-text-2xl',
			'wpex-font-bold',
			'wpex-leading-normal',
			'wpex-no-underline',
		];
		if ( self::get_icon() ) {
			$class[] = 'wpex-inline-flex';
			$class[] = 'wpex-items-center';
		}
		$class = \apply_filters( 'wpex_header_logo_txt_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/header/logo/text_class', $class );
		return \implode( ' ', \array_map( '\esc_attr', $class ) );
	}

	/**
	 * Renders the header logo.
	 */
	public static function render(): void {
		$html         = '';
		$inner_html   = '';
		$logo_img_url = self::get_image_url();
		$logo_title   = self::get_text();

		// Get overlay/transparent header logo when enabled
		if ( (bool) totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) {
			$overlay_logo = totaltheme_call_static( 'Header\Overlay', 'logo_img' );
		}

		// Define logo link attributes
		$logo_link_attrs = [];

		$link = self::get_link_url();

		if ( $link ) {
			$logo_link_attrs['id'] = 'site-logo-link';
			$logo_link_attrs['href'] = self::get_link_url();
			$logo_link_attrs['rel']  = 'home';
		} else {
			$logo_link_attrs['id'] = 'site-logo-span';
		}

		// Display image logo
		if ( ! empty( $logo_img_url ) || ! empty( $overlay_logo ) ) {

			// Define these vars early
			$logo_is_overlay_logo = false;

			// Get Dark mode logo
			if ( totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
				$logo_dark = totaltheme_call_static( 'Dark_Mode', 'get_header_logo' );
			}

			// Get sticky header logo
			if ( totaltheme_call_static( 'Header\Sticky', 'is_enabled' ) ) {
				$sticky_logo = totaltheme_call_static( 'Header\Sticky', 'get_logo_image_url' );
			}

			// Define logo image attributes
			$img_attrs = [
				'src'            => $logo_img_url ? \esc_url( $logo_img_url ) : '',
				'alt'            => $logo_title,
				'class'          => self::get_image_class(),
				'width'          => self::get_image_width(),
				'height'         => self::get_image_height(),
				'data-no-retina' => '',
				'data-skip-lazy' => '',
				'fetchpriority'  => 'high',
			];

			if ( ! empty( $overlay_logo ) || ! empty( $sticky_logo ) ) {
				$img_attrs['class'] .= ' logo-img--base';
			}

			if ( ! empty( $overlay_logo ) ) {
				if ( ! totaltheme_call_static( 'Header\Overlay', 'get_breakpoint' ) ) {
					$img_attrs['src'] = \esc_url( $overlay_logo );
					$logo_is_overlay_logo = true;
				} else {
					$insert_overlay_logo = true;
				}
			}

			// Add retina logo if set
			$retina_logo = self::get_retina_image_url();

			if ( $retina_logo ) {
				$img_attrs['srcset'] = $img_attrs['src'] . ' 1x,' . \esc_url( $retina_logo ) . ' 2x';
			}

			if ( ! empty( $sticky_logo ) ) {
				$img_attrs['data-nonsticky-logo'] = '';
			}

			if ( ! empty( $logo_dark ) && empty( $logo_is_overlay_logo ) ) {
				$img_attrs['class'] .= ' hidden-dark-mode';
			}

			$img_attrs = \apply_filters( 'wpex_header_logo_img_attrs', $img_attrs ); // @deprecated
			$img_attrs = (array) \apply_filters( 'totaltheme/header/logo/image_attributes', $img_attrs );

			// Standard logo html
			$img_html = '<img ' . \wpex_parse_attrs( $img_attrs ) . '>';

			// Overlay logo
			if ( ! empty( $overlay_logo ) && isset( $insert_overlay_logo ) && true === $insert_overlay_logo ) {
				$overlay_img_attrs          = $img_attrs;
				$overlay_img_attrs['src']   = \esc_url( $overlay_logo );
				$overlay_img_attrs['class'] = \str_replace( 'logo-img--base', 'logo-img--overlay', $img_attrs['class'] );

				if ( $overlay_logo_width = totaltheme_call_static( 'Header\Overlay', 'get_logo_image_width' ) ) {
					$overlay_img_attrs['width'] = $overlay_logo_width;
				}

				if ( $overlay_logo_height = totaltheme_call_static( 'Header\Overlay', 'get_logo_image_height' ) ) {
					$overlay_img_attrs['height'] = $overlay_logo_height;
				}

				if ( $overlay_logo_retina = totaltheme_call_static( 'Header\Overlay', 'get_retina_logo_image_url' ) ) {
					$overlay_img_attrs['srcset'] = $overlay_img_attrs['src'] . ' 1x,' . \esc_url( $overlay_logo_retina ) . ' 2x';
				} else {
					unset( $overlay_img_attrs['srcset'] );
				}

				$img_html .= '<img ' . \wpex_parse_attrs( $overlay_img_attrs ) . '>';
			}

			// Dark Logo
			if ( ! empty( $logo_dark ) && empty( $logo_is_overlay_logo ) ) {
				$logo_dark_img_attrs = $img_attrs;
				$logo_dark_img_attrs['src']   = \esc_url( $logo_dark );
				$logo_dark_img_attrs['class'] = \str_replace( 'hidden-dark-mode', 'visible-dark-mode', $logo_dark_img_attrs['class'] );

				if ( $retina_logo && $logo_dark_retina = \totaltheme_call_static( 'Dark_Mode', 'get_header_logo_retina' ) ) {
					$logo_dark_img_attrs['srcset'] = $logo_dark_img_attrs['src'] . ' 1x,' . \esc_url( $logo_dark_retina ) . ' 2x';
				} else {
					unset( $logo_dark_img_attrs['srcset'] );
				}
				
				$img_html .= '<img ' . \wpex_parse_attrs( $logo_dark_img_attrs ) . '>';
			}

			// Sticky logo html
			if ( ! empty( $sticky_logo ) ) {

				$sticky_img_attrs = [
					'src'              => \esc_url( $sticky_logo ),
					'alt'              => $img_attrs['alt'],
					'class'            => self::get_image_class() . ' logo-img--sticky',
					'width'            => totaltheme_call_static( 'Header\Sticky', 'get_logo_image_width' ),
					'height'           => totaltheme_call_static( 'Header\Sticky', 'get_logo_image_height' ),
					'data-no-retina'   => '',
					'data-skip-lazy'   => '',
					'data-sticky-logo' => '',
				];

				if ( $sticky_logo_retina = totaltheme_call_static( 'Header\Sticky', 'get_retina_logo_image_url' ) ) {
					$sticky_img_attrs['srcset'] = $sticky_img_attrs['src'] . ' 1x,' . \esc_url( $sticky_logo_retina ) . ' 2x';
				}

				if ( ! empty( $logo_dark ) ) {
					$sticky_logo_dark = totaltheme_call_static( 'Dark_Mode', 'get_sticky_header_logo' );
					if ( $sticky_logo_dark ) {
						$sticky_img_attrs['class'] .= ' hidden-dark-mode';
					} elseif ( $logo_is_overlay_logo && $logo_img_url === $sticky_logo ) {
						$sticky_logo_dark = $logo_dark;
						$sticky_img_attrs['class'] .= ' hidden-dark-mode';
					}
				}

				// Sticky Logo element
				$img_html .= '<img ' . \wpex_parse_attrs( $sticky_img_attrs ) . '>';

				// Sticky Logo Dark element
				if ( ! empty( $sticky_logo_dark ) ) {
					$sticky_img_dark_attrs = $sticky_img_attrs;
					$sticky_img_dark_attrs['src']   = \esc_url( $sticky_logo_dark );
					$sticky_img_dark_attrs['class'] = \str_replace( 'hidden-dark-mode', 'visible-dark-mode', $sticky_img_dark_attrs['class'] );
					if ( $sticky_logo_retina && $sticky_logo_retina_dark = \totaltheme_call_static( 'Dark_Mode', 'get_sticky_header_logo_retina' ) ) {
						$sticky_img_dark_attrs['srcset'] = $sticky_img_dark_attrs['src'] . ' 1x,' . \esc_url( $sticky_logo_retina_dark ) . ' 2x';
					} else {
						unset( $sticky_img_dark_attrs['srcset'] );
					}
					$img_html .= '<img ' . \wpex_parse_attrs( $sticky_img_dark_attrs ) . '>';
				}
			}

			/**
			 * Custom header-overlay logo.
			 *
			 * @todo update to have new wpex_header_logo_link_class() so we don't have to write dup html here.
			 */
			if ( ! empty( $overlay_logo ) ) {
				$logo_link_attrs['class'] = 'overlay-header-logo';
			}

			// Standard site-wide image logo
			elseif ( ! empty( $logo_img_url ) ) {
				$logo_link_attrs['class'] = 'main-logo';
			}

			$img_html = \apply_filters( 'wpex_header_logo_img_html', $img_html ); // @deprecated
			$img_html = (string) \apply_filters( 'totaltheme/header/logo/image', $img_html );

			// Add image to inner html
			$inner_html = $img_html;
		}

		// Display text logo.
		else {
			$logo_link_attrs['class'] = self::get_text_class();
			$inner_html .= self::get_icon();
			$inner_html .= \do_shortcode( \wp_strip_all_tags( $logo_title ) );
		}

		$attrs = \apply_filters( 'wpex_header_logo_link_attrs', $logo_link_attrs ); // @deprecated
		$attrs = (array) \apply_filters( 'totaltheme/header/logo/link_attributes', $attrs );

		// Final html output
		$html_tag = $link ? 'a' : 'span';
		$html = \wpex_parse_html( $html_tag, $attrs, $inner_html ); // @note $inner_html is sanitized
		$html = \apply_filters( 'wpex_header_logo_output', $html ); // @deprecated

		echo (string) \apply_filters( 'totaltheme/header/logo', $html );
	}

	/**
	 * Return logo wrapper classes.
	 *
	 * Provides a fallback for the older wpex_header_logo_classes() function.
	 */
	public static function get_wrapper_classes(): string {
		$header_style    = totaltheme_call_static( 'Header\Core', 'style' );
		$has_flex_header = totaltheme_call_static( 'Header\Core', 'has_flex_container' );

		$classes = [
			'site-branding',
		];

		// Default class
		$classes[] = "header-{$header_style}-logo";

		// Header 5 show on mobile class
		if ( 'five' === $header_style ) {
			$classes[] = 'show-at-mm-breakpoint';
		}

		// Supports vertical padding
		if ( ! \in_array( $header_style, [ 'seven', 'eight', 'nine', 'ten', 'six' ], true ) ) {
			$classes[] = 'logo-padding';
		}

		// Utility Classes
		$classes[] = 'wpex-flex';
		$classes[] = 'wpex-items-center';

		// Flex and none flex classes
		if ( $has_flex_header ) {
			$classes[] = 'wpex-h-100';
		} else {
			switch ( $header_style ) {
				case 'one':
					$classes[] = 'wpex-float-left';
					$classes[] = 'wpex-h-100';
					break;
				case 'two':
					$classes[] = 'wpex-float-left';
					break;
				case 'three':
				case 'four':
				case 'five':
					$classes[] = 'wpex-text-center';
					$classes[] = 'wpex-justify-center';
					break;
			}
		}

		// Custom class added for the overlay header when set via the Theme Settings metabox
		if ( \wpex_has_post_meta( 'wpex_overlay_header' )
			&& totaltheme_call_static( 'Header\Overlay', 'is_enabled' )
			&& totaltheme_call_static( 'Header\Overlay', 'logo_img' )
		) {
			$classes[] = 'has-overlay-logo';
		}

		// Scroll top
		if ( self::has_scroll_top_link() ) {
			$classes[] = 'wpex-scroll-top';
		}

		$classes = \apply_filters( 'wpex_header_logo_classes', $classes ); // @deprecated
		$classes = (array) \apply_filters( 'totaltheme/header/logo/wrapper_class', $classes );

		return \implode( ' ', \array_map( '\esc_attr', $classes ) );
	}

	/**
	 * Echo logo wrapper class.
	 */
	public static function wrapper_class(): void {
		$classes = self::get_wrapper_classes();
		if ( $classes ) {
			echo 'class="' . \esc_attr( $classes ) . '"';
		}
	}

	/**
	 * Echo logo inner class.
	 */
	public static function inner_class(): void {
		$classes = [];
		$classes = \apply_filters( 'wpex_header_logo_inner_class', $classes ); // @deprecated
		$classes = (array) \apply_filters( 'totaltheme/header/logo/inner_class', $classes );
		if ( $classes ) {
			echo 'class="' . \esc_attr( \implode( ' ', $classes ) ) . '"';
		}
	}

	/**
	 * Parses the logo img.
	 */
	private static function parse_image( $image = '' ) {
		return \wpex_get_image_url( $image );
	}

}
