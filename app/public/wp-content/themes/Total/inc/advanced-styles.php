<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Advanced frontend styles based on user settings.
 */
final class Advanced_Styles {

	/**
	 * CSS var.
	 */
	private static $css = '';

	/**
	 * Static only class.
	 */
	private function __construct() {}

	/**
	 * Init hooks.
	 */
	public static function init() {
		\add_filter( 'wpex_head_css', [ self::class, '_on_wpex_head_css' ], 999 );
	}

	/**
	 * Hooks into wpex_head_css to add custom css to the <head> tag.
	 */
	public static function _on_wpex_head_css( $head_css ) {
		self::generate_css();

		if ( self::$css ) {
			$head_css .= '/*ADVANCED STYLING CSS*/' . self::$css;
			self::$css = ''; // free up memory.
		}

		return $head_css;
	}

	/**
	 * Generates css.
	 */
	private static function generate_css() {
		$methods = [
			'header_background',
			'overlay_header',
			'logo_mobile_side_margin',
			'logo_height',
			'page_header_title',
			'footer_background_image',
			'footer_callout_background_image',
			'blockquote_border',
			'bold_font_weight',
			'mobile_menu',
		];

		if ( totaltheme_is_integration_active( 'wpbakery' ) ) {
			$methods[] = 'vc_inline_shrink_sticky_header_height';
			if ( \get_option( 'wpb_js_use_custom' ) ) {
				$methods[] = 'vc_column_gutter';
			}
		}

		if ( totaltheme_is_integration_active( 'woocommerce' ) ) {
			$methods[] = 'woo_pagination_align';
			$methods[] = 'woo_full_width_add_to_cart';
			$methods[] = 'woo_customizer_hide_elements';
			$methods[] = 'woo_hide_quantity';
			$methods[] = 'woo_thumbnails_aspect_ratios';
		}

		if ( \is_multisite() ) {
			if ( \did_action( 'activate_header' ) ) {
				$methods[] = 'wp_activate_template';
			} elseif ( \did_action( 'before_signup_header' ) ) {
				$methods[] = 'wp_signup_template';
			}
		}

		foreach ( $methods as $method ) {
			if ( $method_css = self::$method() ) {
				self::$css .= $method_css;
			}
		}

	}

	/**
	 * Header background.
	 */
	private static function header_background() {
		$header_bg = totaltheme_call_static( 'Header\Core', 'get_background_image_url' );
		if ( $header_bg ) {
			return '#site-header{background-image:url(' . \esc_url( $header_bg ) . ');}';
		}
	}

	/**
	 * Overlay header.
	 */
	private static function overlay_header() {
		$is_enabled = totaltheme_call_static( 'Header\Overlay', 'is_enabled' );

		if ( ! $is_enabled ) {
			return;
		}

		$css     = '';
		$post_id = \wpex_get_current_post_id();

		if ( $post_id && \wpex_has_post_meta( 'wpex_overlay_header' ) ) {

			// Custom overlay header font size.
			$oh_font_size = \get_post_meta( $post_id, 'wpex_overlay_header_font_size', true );

			if ( $oh_font_size ) {
				$oh_font_size = \sanitize_text_field( $oh_font_size );
				if ( \is_numeric( $oh_font_size ) ) {
					$oh_font_size = "{$oh_font_size}px";
				}
				$css .= '#site-navigation, #site-navigation .main-navigation-ul a{font-size:' . esc_attr( $oh_font_size ) . ';}';
			}

			// Apply overlay header background color.
			// Note we use background and not background-color.
			$overlay_header_bg = \get_post_meta( $post_id, 'wpex_overlay_header_background', true );

			if ( $overlay_header_bg && $overlay_header_bg_safe = \esc_attr( \wpex_parse_color( $overlay_header_bg ) ) ) {
				$css .= "#site-header.overlay-header.dyn-styles{background:{$overlay_header_bg_safe};}#site-header.overlay-header.dyn-styles #site-header-inner{--wpex-site-header-bg-color:{$overlay_header_bg_safe};}";
			}

		}

		// Add overlay header responsive CSS and media query wrapper.
		$media_query = totaltheme_call_static( 'Header\Overlay', 'get_stylesheet_media_query' );
		if ( $media_query && ! in_array( $media_query, [ 'all', 'screen', 'print', 'speech' ] ) ) {

			// Add @media wrapper around CSS.
			if ( $css ) {
				$css = "@media {$media_query}{{$css}}";
			}

			// Hide logo at breakpoint.
			$breakpoint = totaltheme_call_static( 'Header\Overlay', 'get_breakpoint' );

			if ( $breakpoint && is_int( $breakpoint ) ) {
				if ( totaltheme_call_static( 'Header\Overlay', 'is_mobile_first' ) ) {
					$breakpoint = $breakpoint + 1;
					$media_query = "@media only screen and (min-width:{$breakpoint}px)";
				} else {
					$media_query = "@media only screen and (max-width:{$breakpoint}px)";
				}

				$css .= "{$media_query}{.overlay-header--responsive .logo-img--overlay{display: none;}}";
			}
		}

		return $css;
	}

	/**
	 * Logo mobile side margin.
	 */
	private static function logo_mobile_side_margin() {
		if ( in_array( \totaltheme_call_static( 'Header\Core', 'style' ), [ 'seven', 'eight', 'nine', 'ten' ], true ) ) {
			return;
		}

		$margin = \get_theme_mod( 'logo_mobile_side_offset' );

		if ( ! $margin ) {
			return;
		}

		$margin_escaped = \absint( $margin );

		if ( ! $margin_escaped ) {
			return;
		}
	
		$css = "body.has-mobile-menu #site-logo {margin-inline-end:{$margin_escaped}px;}";

		$mm_breakpoint = (int) totaltheme_call_static( 'Mobile\Menu', 'breakpoint' );

		if ( $mm_breakpoint < 9999 ) {
			$css = "@media only screen and (max-width:{$mm_breakpoint}px){{$css}}";
		}

		return $css;
	}

	/**
	 * Custom logo height.
	 */
	private static function logo_height() {
		$logo_is_svg = totaltheme_call_static( 'Header\Logo', 'is_image_svg' );
		if ( $logo_is_svg || \get_theme_mod( 'apply_logo_height', false ) ) {
			$height = \absint( \get_theme_mod( 'logo_height' ) );
			$height_prop = 'max-height';
			if ( totaltheme_call_static( 'Header\Core', 'has_fixed_height' ) || $logo_is_svg ) {
				$height_prop = 'height';
			}
			if ( $height ) {
				return '#site-logo .logo-img{' . $height_prop . ':' . \esc_attr( $height ) . 'px;width:auto;}'; // auto width needed for responsiveness.
			}
		}
	}

	/**
	 * Shrink header height.
	 *
	 * This is used to provide consistency with the shrink header logo when using the front-end builder
	 * since the sticky header is disabled when using the builder.
	 *
	 * @todo perhaps we can target a different classname in the default CSS that gets added on the front-end only?
	 */
	private static function vc_inline_shrink_sticky_header_height() {
		if ( ! \totaltheme_is_wpb_frontend_editor() || totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
			return;  // important not needed for flex header!!
		}

		$shrink_header_style = totaltheme_call_static( 'Header\Sticky', 'style' );

		if ( 'shrink' === $shrink_header_style || 'shrink_animated' === $shrink_header_style ) {
			return '#site-logo .logo-img{max-height:var(--wpex-site-header-shrink-start-height, 60px)!important;width:auto;}';
		}
	}

	/**
	 * Page header title.
	 */
	private static function page_header_title() {
		if ( ! \wpex_has_post_meta( 'wpex_post_title_style' ) ) {
			$page_header_bg = \wpex_page_header_background_image(); // already passed through wpex_get_image_url
			if ( $page_header_bg ) {
				return '.page-header.has-bg-image{background-image:url(' . \esc_url( $page_header_bg ) . ');}';
			}
		}
	}

	/**
	 * Footer background.
	 */
	private static function footer_background_image() {
		$background = \wpex_get_image_url( \get_theme_mod( 'footer_bg_img' ) );
		if ( $background ) {
			return '#footer{background-image:url(' . \esc_url( $background ) . ');}';
		}
	}

	/**
	 * Footer callout background.
	 */
	private static function footer_callout_background_image() {
		$background = \wpex_get_image_url( \get_theme_mod( 'footer_callout_bg_img' ) );
		if ( $background ) {
			return '#footer-callout-wrap{background-image:url(' . \esc_url( $background ) . ');}';
		}
	}

	/**
	 * Define Visual Composer gutter.
	 */
	private static function vc_column_gutter() {
		if ( $custom_gutter = \get_option( 'wpb_js_gutter' ) ) {
			return ':root{--wpex-vc-gutter:' . \absint( $custom_gutter ) . 'px}';
		}
	}

	/**
	 * Adds border to the blockquote element.
	 */
	private static function blockquote_border() {
		$border_width_safe = ( $border_width = \get_theme_mod( 'blockquote_border_width' ) ) ? \absint( $border_width ) : 0;
		if ( $border_width_safe ) {
			return "blockquote{border-width:0;border-inline-start-width:{$border_width_safe}px;border-color:var(--wpex-accent);border-style:solid;padding-inline-start:25px;}blockquote::before{display:none;}";
		}
	}

	/**
	 * Alters the --wpex-bold CSS var.
	 */
	private static function bold_font_weight() {
		$value = \get_theme_mod( 'bold_font_weight' );
		if ( $value && \is_numeric( $value ) ) {
			return ':root{--wpex-bold:' . \sanitize_text_field( $value ) . ';}';
		}
	}

	/**
	 * Pagination alignment for WooCommerce.
	 */
	private static function woo_pagination_align() {
		if ( $align = \get_theme_mod( 'pagination_align' ) ) {
			$align = \sanitize_text_field( $align );
			if ( \in_array( $align, [ 'left', 'center', 'right', 'start', 'end' ], true ) ) {
				switch ( $align ) {
					case 'left':
						$align = 'start';
						break;
					case 'right':
						$align = 'end';
						break;
				}
				return ":where(.woocommerce-pagination){text-align:{$align}}";
			}
		}
	}

	/**
	 * Full Width add to cart button.
	 */
	private static function woo_full_width_add_to_cart() {
		if ( \wp_validate_boolean( \get_theme_mod( 'woo_product_add_to_cart_full_width' ) ) ) {
			return '.woocommerce .product .summary .single_add_to_cart_button, .woocommerce .product .summary .added_to_cart,.product .add_to_cart_button,.product .added_to_cart,.product-actions .button{width:100%;}.woocommerce .summary .quantity{margin-block-end:20px;}';
		}
	}

	/**
	 * Hide WooCommerce Elements while in the Customizer.
	 */
	private static function woo_customizer_hide_elements() {
		if ( ! \is_customize_preview() ) {
			return;
		}
		$css = '';
		if ( \function_exists( 'is_product' ) && \is_product() ) {
			$product_title = \get_theme_mod( 'woo_shop_single_title' );
			if ( $product_title && \str_contains( $product_title, '{{title}}' ) ) {
				$css .= '.woocommerce .summary .single-post-title{display:none !important;}';
			}
		}
		if ( ! \get_theme_mod( 'woo_shop_sort', true ) ) {
			$css .= '.woocommerce .woocommerce-ordering{display:none !important;}';
		}
		if ( ! \get_theme_mod( 'woo_shop_result_count', true ) ) {
			$css .= '.woocommerce .woocommerce-result-count{display:none !important;}';
		}
		if ( ! \get_theme_mod( 'woo_product_meta', true ) ) {
			$css .= '.woocommerce .product_meta{display:none !important;}';
		}
		return $css;
	}

	/**
	 * Hide WooCommerce Quantity.
	 */
	private static function woo_hide_quantity() {
		if ( 'disabled' === \get_theme_mod( 'woo_quantity_buttons_style' ) ) {
			return '.summary .quantity, .vcex-wc-template-part--single-product__add-to-cart .quantity{display:none;}';
		}
	}

	/**
	 * WooCommerce thumbnails aspect ratios.
	 */
	private static function woo_thumbnails_aspect_ratios() {
		$product_gallery_aspect_ratio = \get_theme_mod( 'shop_single_thumbnail_image_aspect_ratio' );
		if ( $product_gallery_aspect_ratio
			&& \get_theme_mod( 'woo_product_gallery_slider', true )
			&& \function_exists( 'is_product' )
			&& \is_product()
		) {
			return '.woocommerce-product-gallery .flex-control-thumbs img{aspect-ratio:' . esc_attr( $product_gallery_aspect_ratio ) . ';object-fit:cover;}';
		}
	}

	/**
	 * Mobile menu tweaks.
	 */
	private static function mobile_menu() {
		$css = '';
		$mm_style = \totaltheme_call_static( 'Mobile\Menu', 'style' );
		if ( \in_array( $mm_style, [ 'toggle', 'toggle_inline', 'toggle_full' ], true ) ) {
			$border_bottom = \get_theme_mod( 'mobile_menu_toggle_has_border_bottom' );
			if ( \wp_validate_boolean( $border_bottom ) ) {
				$css .= '.mobile-toggle-nav-ul { border-block-end: 1px solid var(--wpex-border-main); }';
				if ( 'toggle_full' !== $mm_style ) {
					$css .= '.mobile-toggle-nav-search { margin-block-start: 20px; }';
				}
			}
			if ( ! \wp_validate_boolean( \get_theme_mod( 'mobile_menu_toggle_has_border_top', true ) ) ) {
				$css .= '.mobile-toggle-nav-ul > li:first-child > span > a { border-block-start: 0; }';
			}
		}
		return $css;
	}

	/**
	 * wp-activate.php template tweaks.
	 */
	private static function wp_activate_template() {
		$css = '
			.wpex-responsive .wp-activate-container { max-width: var(--wpex-container-max-width); }
			.wp-activate-container { width: var(--wpex-container-width); margin: 40px auto; }
			.wp-activate-container > h2 { margin-block-start: 0; }
			.wp-activate-container #submit, .wp-activate-container #key { font-size: 1.5em; }
		';
		return $css;
	}

	/**
	 * wp-signup.php template tweaks.
	 */
	private static function wp_signup_template() {
		$css = '
			.wpex-responsive .wp-signup-container { max-width: var(--wpex-container-max-width); }
			.wp-signup-container { width: var(--wpex-container-width); margin: 40px auto; }
			.wp-signup-container > h2 { margin-block-start: 0; }
			.mu_register input[type="submit"], .mu_register #blog_title, .mu_register #user_email, .mu_register #blogname, .mu_register #user_name { font-size: 1.5em; }
			.mu_alert { margin-block-end: 25px; }
		';
		return $css;
	}

}
