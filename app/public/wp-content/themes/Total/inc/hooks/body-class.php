<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "body_class".
 */
final class Body_Class {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $classes ) {
		$extra_classes = self::get_theme_classes();

		if ( $extra_classes ) {
			$classes = \array_merge( $classes, $extra_classes );
		}

		return $classes;
	}

	/**
	 * Returns theme classes to add to the body class.
	 */
	protected static function get_theme_classes() {
		$classes        = [];
		$post_id        = \wpex_get_current_post_id();
		$main_layout    = \wpex_site_layout();
		$classic_styles = \totaltheme_has_classic_styles();

		// Customizer
		if ( \is_customize_preview() ) {
			$classes[] = 'is_customize_preview'; // @todo remove?
		}

		// Main class
		$classes[] = 'wpex-theme';

		// Responsive
		if ( \wpex_is_layout_responsive() ) {
			$classes[] = 'wpex-responsive';
		}

		// Layout Style (older classname)
		if ( $classic_styles ) {
			$classes[] = "{$main_layout}-main-layout";
		}

		// WPBakery
		if ( \WPEX_VC_ACTIVE ) {
			$classes[] = \totaltheme_call_static( 'Integration\WPBakery\Helpers', 'post_has_wpbakery', $post_id ) ? 'has-composer' : 'no-composer';
			// @todo deprecate completely
			if ( $classic_styles && ! \totaltheme_is_wpb_frontend_editor() ) {
				$classes[] = 'wpex-live-site';
			}
		}

		// Add primary element bottom margin
		if ( \wpex_has_primary_bottom_spacing() ) {
			$classes[] = 'wpex-has-primary-bottom-spacing';
		}

		// Boxed Layout dropshadow
		if ( 'boxed' === $main_layout && \get_theme_mod( 'boxed_dropdshadow' ) ) {
			$classes[] = 'wrap-boxshadow';
		}

		// Main & Content layouts
		$classes[] = "site-{$main_layout}"; // @added in 5.1.3 (newer class)
		$classes[] = 'content-' . \sanitize_html_class( \wpex_content_area_layout( $post_id ) );

		// Sidebar
		if ( \wpex_has_sidebar() ) {
			$classes[] = 'has-sidebar';
		}

		// Extra header classes
		if ( \totaltheme_call_static( 'Header\Core', 'is_enabled' ) ) {
			if ( \totaltheme_call_static( 'Header\Vertical', 'is_enabled' ) ) {
				$classes[] = 'wpex-has-vertical-header';
				$classes[] = 'wpex-vertical-header-' . \sanitize_html_class( \totaltheme_call_static( 'Header\Vertical', 'position' ) );
				if ( 'fixed' === \totaltheme_call_static( 'Header\Vertical', 'style' ) ) {
					$classes[] = 'wpex-fixed-vertical-header';
				}
			}
			if ( \totaltheme_call_static( 'Header\Core', 'has_fixed_height' ) ) {
				$classes[] = 'header-has-fixed-height';
			}
		} else {
			$classes[] = 'wpex-site-header-disabled';
		}

		// Topbar
		if ( \totaltheme_call_static( 'Topbar\Core', 'is_enabled' ) ) {
			$classes[] = 'has-topbar';
		}

		// Single Post cagegories - @todo deprecate completely
		if ( $classic_styles && \is_singular( 'post' ) ) {
			$cats = \get_the_category( $post_id );
			foreach ( $cats as $cat ) {
				if ( ! empty( $cat->category_nicename ) ) {
					$classes[] = 'post-in-category-' . \sanitize_html_class( $cat->category_nicename );
				}
			}
		}

		// Widget Icons
		if ( \get_theme_mod( 'has_widget_icons', true ) ) {
			$classes[] = 'sidebar-widget-icons';
		}

		// Overlay header style
		if ( \totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) {
			$classes[] = 'has-overlay-header';
		} else {
			$classes[] = 'hasnt-overlay-header';
		}

		// Footer reveal
		if ( \totaltheme_call_static( 'Footer\Core', 'is_enabled' ) ) {
			if ( \totaltheme_call_static( 'Footer\Core', 'has_reveal' ) ) {
				$classes[] = 'footer-has-reveal';
			}
		}

		// Fixed Footer - adds min-height to main wraper
		if ( \get_theme_mod( 'fixed_footer' ) ) {
			$classes[] = 'wpex-has-fixed-footer';
		}

		// Disabled header
		if ( \totaltheme_call_static( 'Page\Header', 'is_enabled' ) ) {
			if ( 'background-image' === \totaltheme_call_static( 'Page\Header', 'style' ) ) {
				$classes[] = 'page-with-background-title';
			}
		} else {
			$classes[] = 'page-header-disabled';
		}

		// Disable title margin
		if ( $post_id ) {
			$disable_header_margin = \get_post_meta( $post_id, 'wpex_disable_header_margin', true );
			if ( $disable_header_margin && \wpex_validate_boolean( $disable_header_margin ) ) {
				$classes[] = 'no-header-margin';
			}
		}

		// Page slider
		if ( \wpex_has_post_slider( $post_id ) && $slider_position = \wpex_post_slider_position( $post_id ) ) {
			if ( $classic_styles ) {
				$classes[] = 'page-with-slider'; // Deprecated @todo remove completely
			}
			$classes[] = 'has-post-slider';
			$classes[] = 'post-slider-' . \sanitize_html_class( \str_replace( '_', '-', $slider_position ) );
		}

		// Font smoothing
		if ( \get_theme_mod( 'enable_font_smoothing', false ) ) {
			$classes[] = 'wpex-antialiased';
		}

		// Mobile menu toggle style
		if ( \totaltheme_call_static( 'Mobile\Menu', 'is_enabled' ) ) {
			$classes[] = 'has-mobile-menu';
			$classes[] = 'wpex-mobile-toggle-menu-' . \sanitize_html_class( \wpex_header_menu_mobile_toggle_style() );
		}

		// Navbar inner span bg
		if ( \get_theme_mod( 'menu_link_span_background' ) ) {
			$classes[] = 'navbar-has-inner-span-bg';
		}

		// Togglebar
		if ( $classic_styles && 'inline' === \wpex_togglebar_style() ) {
			$classes[] = 'togglebar-is-inline'; // class not used anymore @todo remove completely
		}

		// Frame border
		if ( \get_theme_mod( 'site_frame_border' ) ) {
			$classes[] = 'has-frame-border';
		}

		// Pagination gutter - @todo can we do this a different way?
		$pag_gutter = \get_theme_mod( 'pagination_gutter' );
		if ( $pag_gutter && '0' !== $pag_gutter && '-1' !== $pag_gutter ) {
			$classes[] = 'has-pagination-gutter';
		}

		// Social share position
		$share_position = \wpex_social_share_position();
		if ( 'vertical' === $share_position && \wpex_has_social_share() ) {
			$classes[] = 'wpex-share-p-vertical';
		}

		// No JS class gets removed when JS loads
		$classes[] = 'wpex-no-js';

		return $classes;
	}

}
