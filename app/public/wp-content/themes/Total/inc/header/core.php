<?php

namespace TotalTheme\Header;

\defined( 'ABSPATH' ) || exit;

/**
 * Core header methods.
 */
class Core {

	/**
	 * Header is enabled or not.
	 */
	protected static $is_enabled = null;

	/**
	 * Header Template ID.
	 */
	protected static $template_id = null;

	/**
	 * Header is custom, aka using the header builder.
	 */
	protected static $is_custom = null;

	/**
	 * The header style.
	 */
	protected static $style = null;

	/**
	 * Header has flex container.
	 */
	protected static $has_flex_container = null;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Checks if the header is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( null !== self::$is_enabled ) {
			return self::$is_enabled;
		}
		if ( \is_page_template( [ 'templates/landing-page.php', 'templates/blank.php' ] ) ) {
			$check = false;
		} else {
			if ( self::is_custom() || \totaltheme_call_static( 'Integration\Elementor', 'location_exists', 'header' ) ) {
				$check = true;
			} else {
				$check = \get_theme_mod( 'enable_header', true );
			}
			$post_id = \wpex_get_current_post_id();
			if ( $post_id && $meta = \get_post_meta( $post_id, 'wpex_disable_header', true ) ) {
				if ( 'on' === $meta ) {
					$check = false;
				} elseif ( 'enable' === $meta ) {
					$check = true;
				}
			}
		}
		$check = \apply_filters( 'wpex_display_header', $check ); // @deprecated
		self::$is_enabled = (bool) \apply_filters( 'totaltheme/header/is_enabled', $check );
		return self::$is_enabled;
	}

	/**
	 * Return template ID.
	 */
	public static function get_template_id(): int {
		if ( null === self::$template_id ) {
			self::$template_id = 0;
			if ( \get_theme_mod( 'header_builder_enable', true ) ) {
				$id = \get_theme_mod( 'header_builder_page_id' );
				$id = (int) \apply_filters( 'wpex_header_builder_page_id', $id ); // @deprecated
				if ( $id && 'publish' === \get_post_status( $id ) ) {
					self::$template_id = $id;
				}
			}
		}
		return self::$template_id;
	}

	/**
	 * Check if currently editing the header.
	 */
	public static function is_edit_mode( $editor = 'any' ): bool {
		switch ( $editor ) {
			case 'wpbakery':
				$editor_check = totaltheme_is_wpb_frontend_editor();
				break;
			case 'elementor':
				$editor_check = totaltheme_is_elementor_editor();
				break;
			default:
				$editor_check = totaltheme_is_wpb_frontend_editor() || totaltheme_is_elementor_editor();
				break;
		}
		return $editor_check && self::get_template_id() === wpex_get_current_post_id();
	}

	/**
	 * Checks if the header is custom or not.
	 */
	public static function is_custom(): bool {
		if ( null === self::$is_custom ) {
			self::$is_custom = (bool) self::get_template_id();
		}
		return self::$is_custom;
	}

	/**
	 * Returns an array of style choices for the header.
	 */
	public static function style_choices(): array {
		$choices = [
			'one'   => '1. ' . \esc_html__( 'Default: Left Logo & Right Menu','total' ),
			'two'   => '2. ' . \esc_html__( 'Bottom Menu','total' ),
			'three' => '3. ' . \esc_html__( 'Bottom Menu Centered','total' ),
			'four'  => '4. ' . \esc_html__( 'Top Menu Centered','total' ),
			'five'  => '5. ' . \esc_html__( 'Centered Logo Inside Menu','total' ),
			'six'   => '6. ' . \esc_html__( 'Vertical','total' ),
			'seven' => '7. ' . \esc_html__( 'Flex: Centered Menu','total' ),
			'eight' => '8. ' . \esc_html__( 'Flex: Left Menu','total' ),
			'nine'  => '9. ' . \esc_html__( 'Flex: Right Menu','total' ),
			'ten'   => '10. ' . \esc_html__( 'Flex: Centered Logo','total' ),
			'dev'   => '11. ' . \esc_html__( 'Dev (No Styling)','total' ),
		];
		$choices = \apply_filters( 'wpex_header_styles', $choices ); // @deprecated
		return (array) \apply_filters( 'totaltheme/header/style_choices', $choices );
	}

	/**
	 * Returns the header style.
	 */
	public static function style(): string {
		if ( null !== self::$style ) {
			return self::$style;
		}

		if ( ! self::is_enabled() ) {
			self::$style = 'disabled';
			return self::$style;
		}

		// Check if builder is enabled.
		if ( self::is_custom() ) {
			self::$style = 'builder';
			return self::$style;
		}

		// Get header style from customizer setting.
		$style = ( $style = \get_theme_mod( 'header_style' ) ) ? \sanitize_text_field( $style ) : 'one';

		// Check overlay header.
		if ( \totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) {
			$excluded_overlay_header_styles = (array) \apply_filters( 'wpex_overlay_header_excluded_header_styles', [] ); // @deprecated
			if ( \in_array( $style, $excluded_overlay_header_styles, true ) ) {
				$style = 'one';
			}
		}

		// Check custom field.
		$post_id = \wpex_get_current_post_id();
		if ( 'dev' !== $style && $post_id && $meta = \get_post_meta( $post_id, 'wpex_header_style', true ) ) {
			$style = \sanitize_text_field( $meta );
		}

		if ( ! $style ) {
			$style = 'one';
		}

		$style = \apply_filters( 'wpex_header_style', $style ); // @deprecated
		self::$style = (string) \apply_filters( 'totaltheme/header/style', $style );

		return self::$style;
	}

	/**
	 * Checks if the header is full width.
	 */
	public static function is_full_width(): bool {
		return 'full-width' === \wpex_site_layout() && \get_theme_mod( 'full_width_header' );
	}

	/**
	 * Checks if the header has a fixed height.
	 */
	public static function has_fixed_height(): bool {
		$header_style = self::style();
		$check        = \in_array( $header_style, [ 'seven', 'eight', 'nine', 'ten' ], true );
		$check        = \apply_filters( 'wpex_header_has_fixed_height', $check, $header_style ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/header/has_fixed_height', $check, $header_style );
	}

	/**
	 * Checks if the header has a flex container or not.
	 */
	public static function has_flex_container(): bool {
		if ( null === self::$has_flex_container ) {
			$check = \in_array( self::style(), [ 'seven', 'eight', 'nine', 'ten' ], true );
			$check = \apply_filters( 'wpex_has_flex_header', $check, self::style() ); // @deprecated
			self::$has_flex_container = (bool) \apply_filters( 'totaltheme/header/has_flex_container', $check, self::style() );
		}
		return self::$has_flex_container;
	}

	/**
	 * Returns header image background image url.
	 */
	public static function get_background_image_url(): string {
		$image = \get_theme_mod( 'header_background_image' );
		$image = \apply_filters( 'wpex_header_background_image', $image ); // @deprecated
		$image = \apply_filters( 'totaltheme/header/background_image', $image );
		$post_id = wpex_get_current_post_id();
		if ( $post_id && $meta_image = \get_post_meta( $post_id, 'wpex_header_background_image', true ) ) {
			$image = $meta_image; // meta overrides filters.
		}
		return $image ? \wpex_get_image_url( $image ) : '';
	}

	/**
	 * Return header wrapper classes.
	 *
	 * Provides a fallback for the older wpex_header_classes() function.
	 */
	public static function get_wrapper_classes(): string {
		$post_id            = \wpex_get_current_post_id();
		$header_style       = self::style();
		$header_style_class = \sanitize_html_class( $header_style );
		$has_flex_header    = self::has_flex_container();
		$is_sticky          = totaltheme_call_static( 'Header\Sticky', 'is_enabled' );

		// Setup classes array.
		$classes = [
			"header-{$header_style_class}",
		];

		if ( $is_sticky ) {
			$classes[] = 'wpex-z-sticky';
		} elseif ( 'builder' === $header_style ) {
			// z-index prevents issues with vc rows and dropdowns from 3rd party menu plugins.
			// Also a z-index of at least 3 is needed when footer-reveal is enabled.
			$classes[] = 'wpex-z-10';
		}

		// Builder editor mode class.
		if ( 'builder' === $header_style && self::is_edit_mode( 'wpbakery' ) ) {
			$classes[] = 'header-builder--vc-compose-mode';
		}

		// Non-Builder classes.
		if ( 'builder' !== $header_style ) {

			// Full width header.
			if ( self::is_full_width() ) {
				$classes[] = 'header-full-width';
			}

			// Non-dev classes
			if ( 'dev' !== $header_style ) {

				// Fixed height class.
				if ( self::has_fixed_height() ) {
					$classes[] = 'header-fixed-height';
				}

				// Flex header style two.
				if ( 'two' === $header_style && \wp_validate_boolean( \get_theme_mod( 'header_flex_items', false ) ) ) {
					$classes[] = 'wpex-header-two-flex-v';
				}

				// Dropdown style (must be added here so we can target shop/search dropdowns).
				if ( $drop_style = \totaltheme_call_static( 'Header\Menu', 'get_dropdown_style' ) ) {
					$classes[] = \sanitize_html_class( "wpex-dropdown-style-{$drop_style}" );
				}

				// Dropdown shadows.
				if ( $drop_shadow = \totaltheme_call_static( 'Header\Menu', 'get_dropdown_shadow_style' ) ) {
					$classes[] = \sanitize_html_class( "wpex-dropdowns-shadow-{$drop_shadow}" );
				}

			}

		}

		// Sticky Header.
		if ( \totaltheme_call_static( 'Header\Sticky', 'is_enabled' ) ) {

			if ( 'css' === totaltheme_call_static( 'Header\Sticky', 'style' ) ) {
				$classes[] = 'wpex-sticky';
			} else {

				// Main fixed class.
				$classes[] = 'fixed-scroll';
				if ( ! \in_array( $header_style, \wpex_get_header_styles_with_sticky_support(), true ) ) {
					$classes[] = 'fixed-scroll--mobile-only';
				}

				if ( \get_theme_mod( 'has_fixed_header_dropshadow', true )
					&& ( 'builder' !== $header_style || 'css' !== \get_theme_mod( 'header_builder_sticky_type', 'js' ) )
				) {
					$classes[] = 'has-sticky-dropshadow';
				}

				if ( \totaltheme_call_static( 'Header\Sticky', 'is_shrink_enabled' ) ) {
					$classes[] = 'shrink-sticky-header';
					if ( 'shrink_animated' === \totaltheme_call_static( 'Header\Sticky', 'style' ) ) {
						$classes[] = 'anim-shrink-header';
					}
					if ( ! $has_flex_header ) {
						$classes[] = 'on-shrink-adjust-height';
					}
				}
			}

		}

		// Header Overlay Style
		if ( \totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) {

			// Add overlay header class.
			$classes[] = 'overlay-header';

			// Add responsive class.
			if ( \totaltheme_call_static( 'Header\Overlay', 'get_breakpoint' ) ) {
				$classes[] = 'overlay-header--responsive';
			}

			// Add overlay header style class.
			if ( $overlay_style = \totaltheme_call_static( 'Header\Overlay', 'style' ) ) {
				$classes[] = \sanitize_html_class( "{$overlay_style}-style" );
			}

		}

		// Custom bg.
		if ( \get_theme_mod( 'header_background' ) ) {
			$classes[] = 'custom-bg';
		}

		// Background style.
		if ( self::get_background_image_url() ) {
			$bg_style = ( $bg_style = \get_theme_mod( 'header_background_image_style' ) ) ? \sanitize_text_field( $bg_style ) : '';
			$bg_style = \apply_filters( 'wpex_header_background_image_style', $bg_style ); // @deprecated
			$bg_style = (string) \apply_filters( 'totaltheme/header/background_image_style', $bg_style );
			if ( $bg_style ) {
				$classes[] = \sanitize_html_class( "bg-{$bg_style}" ); // @todo deprecated
				$classes[] = \wpex_parse_background_style_class( $bg_style );
			}
		}

		// Dynamic style class.
		$classes[] = 'dyn-styles';

		// Hide for print.
		$classes[] = 'wpex-print-hidden';

		// Add relative class always.
		$classes[] = 'wpex-relative';  // !! important !!!

		// Clearfix class.
		if ( ! $has_flex_header ) {
			$classes[] = 'wpex-clr';
		}

		$classes = \array_combine( $classes, $classes );
		$classes = \apply_filters( 'wpex_header_classes', $classes ); // @deprecated
		$classes = (array) \apply_filters( 'totaltheme/header/wrapper_class', $classes );

		return \implode( ' ', $classes );
	}

	/**
	 * Output header wrapper class.
	 */
	public static function wrapper_class(): void {
		$classes = self::get_wrapper_classes();
		if ( $classes ) {
			echo 'class="' . \esc_attr( $classes ) . '"';
		}
	}

	/**
	 * Output header inner class.
	 */
	public static function inner_class(): void {
		$header_style     = self::style();
		$has_flex_header  = self::has_flex_container();
		$has_fixed_height = self::has_fixed_height();
		$add_clearfix     = true;

		$class = [
			"header-{$header_style}-inner",
		];

		if ( ! $has_fixed_height ) {
			$class[] = 'header-padding';
		}

		$class[] = 'container';

		/* Utility Classes */
		$class[] = 'wpex-relative';
		$class[] = 'wpex-h-100';

		if ( 'builder' !== $header_style ) {
			if ( $has_flex_header ) {
				$add_clearfix = false;
				$class[] = 'wpex-flex';
				$class[] = 'wpex-z-10'; // fixes issues with relative positioning and overflows, changed from z-2 to z-10 in 5.4.3
			} else {
				$class[] = 'wpex-py-30';
			}
		}

		if ( 'two' === $header_style && \wp_validate_boolean( \get_theme_mod( 'header_flex_items', false ) ) ) {
			$is_flex = true;
			$class[] = 'wpex-flex';
			$class[] = 'wpex-items-center';
		}

		if ( $add_clearfix ) {
			$class[] = 'wpex-clr';
		}

		$class = \apply_filters( 'wpex_header_inner_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/header/inner_class', $class );

		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

}
