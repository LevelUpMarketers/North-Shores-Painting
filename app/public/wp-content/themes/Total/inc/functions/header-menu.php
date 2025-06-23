<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Drop Widgets ]
/*-------------------------------------------------------------------------------*/

/**
 * Get header Menu dropdown widget class.
 */
function wpex_get_header_drop_widget_class(): array {
	$dropdown_style = totaltheme_call_static( 'Header\Menu', 'get_dropdown_style' );
	$animate        = wpex_validate_boolean( get_theme_mod( 'menu_drodown_animate' ) );
	$has_border     = true;

	if ( 'black' === $dropdown_style ) {
		$has_border = get_theme_mod( 'menu_dropdown_top_border' );
	}

	$class = [
		'header-drop-widget',
	];

	if ( $has_border ) {
		$class[] ='header-drop-widget--colored-top-border';
	}

	if ( $animate ) {
		$class[] = 'header-drop-widget--animate';
	}

	// Utility classes
	$class[] = 'wpex-invisible';
	$class[] = 'wpex-opacity-0';
	$class[] = 'wpex-absolute';
	if ( ! $animate ) {
		$class[] = 'wpex-transition-all';
		$class[] = 'wpex-duration-200';
	}
	$class[] = 'wpex-translate-Z-0';
	$class[] = 'wpex-text-initial';
	$class[] = 'wpex-z-dropdown';

	if ( totaltheme_call_static( 'Header\Vertical', 'is_enabled' ) ) {
		$class[] = 'wpex-top-0';
		$vh_position = sanitize_html_class( totaltheme_call_static( 'Header\Vertical', 'position' ) );
		$class[] = "wpex-{$vh_position}-100"; // left/right 100%
	} else {
		$class[] = 'wpex-top-100';
		$class[] = 'wpex-right-0';
	}

	$class[] = 'wpex-surface-1';
	$class[] = 'wpex-text-2';

	return (array) apply_filters( 'wpex_get_header_drop_widget_class', $class );
}

/**
 * Header Menu dropdown widget class.
 */
function wpex_header_drop_widget_class(): void {
	if ( $class = wpex_get_header_drop_widget_class() ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Check if header menu dropdown widgets should be added inline (insite the li element).
 */
function wpex_maybe_add_header_drop_widget_inline( $widget = '' ): bool {
	$check        = true;
	$header_style = totaltheme_call_static( 'Header\Core', 'style' );
	if ( 'one' === $header_style || totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
		$check = false; // set items flush with the header bottom.
	}
	if ( function_exists( 'max_mega_menu_is_enabled' ) && max_mega_menu_is_enabled( 'main_menu' ) ) {
		$check = true;
	}
	return (bool) apply_filters( 'wpex_maybe_add_header_drop_widget_inline', $check, $widget );
}

/*-------------------------------------------------------------------------------*/
/* [ Mobile ]
/*-------------------------------------------------------------------------------*/

/**
 * Conditional check for alternative menu.
 */
function wpex_has_mobile_menu_alt() {
	$check = ( has_nav_menu( 'mobile_menu_alt' ) && ! totaltheme_call_static( 'Header\Menu', 'get_wp_menu' ) );
	return (bool) apply_filters( 'wpex_has_mobile_menu_alt', $check );
}

/**
 * Return header mobile menu classes.
 */
function wpex_header_mobile_menu_classes() {
	$style        = wpex_header_menu_mobile_toggle_style();
	$header_style = totaltheme_call_static( 'Header\Core', 'style' );
	$mm_style     = totaltheme_call_static( 'Mobile\Menu', 'style' );
	$flex_header  = totaltheme_call_static( 'Header\Core', 'has_flex_container' );

	$classes = [
		'wpex-mobile-menu-toggle',
		'show-at-mm-breakpoint',
		// Add flex styles to all toggles.
		'wpex-flex',
		'wpex-items-center',
	];

	switch ( $style ) {
		case 'icon_buttons':
			if ( ! $flex_header ) {
				$classes[] = 'wpex-absolute wpex-top-50 -wpex-translate-y-50 wpex-right-0';
			}
			break;
		case 'icon_buttons_under_logo':
			if ( $flex_header ) {
				$classes[] = 'wpex-mt-10';
			} else {
				$classes[] = 'wpex-mt-20';
			}
			$classes[] = 'wpex-justify-center';
			break;
		case 'navbar':
			$classes[] = 'wpex-bg-gray-A900';
			break;
		case 'fixed_top':
			$classes[] = 'wpex-fixed';
			$classes[] = 'wpex-sticky-el-offset';
			$classes[] = 'wpex-ls-offset';
			$classes[] = 'wpex-z-dropdown'; // supports dropdown menu and search dropdown.
			$classes[] = 'wpex-top-0';
			$classes[] = 'wpex-inset-x-0';
			if ( 'toggle_full' === $mm_style ) {
				$classes[] = 'wpex-bg-gray-A900';
			} else {
				$classes[] = 'wpex-surface-dark';
			}
			break;
	}

	$styles_w_icons = [
		'icon_buttons',
		'icon_buttons_under_logo',
		'centered_logo',
		'next_to_logo',
	];

	if ( $flex_header && in_array( $style, $styles_w_icons ) ) {
		$classes[] = 'wpex-h-100';
	}

	$classes = (array) apply_filters( 'wpex_mobile_menu_toggle_class', $classes, $style );

	return esc_attr( implode( ' ', $classes ) );
}

/**
 * Returns classes for the header menu mobile toggle.
 */
function wpex_mobile_menu_toggle_class() {
	if ( $classes = wpex_header_mobile_menu_classes() ) {
		echo 'class="' . esc_attr( $classes ) . '"';
	}
}

/**
 * Return correct mobile menu toggle style for the header.
 */
function wpex_header_menu_mobile_toggle_style() {
	static $style = null;
	if ( isset( $style ) ) {
		return $style;
	}
	if ( 'disabled' === totaltheme_call_static( 'Mobile\Menu', 'style' ) ) {
		$style = false;
	} else {
		$style = get_theme_mod( 'mobile_menu_toggle_style' );
		if ( ( 'centered_logo' === $style || 'next_to_logo' === $style )
			&& ! totaltheme_call_static( 'Header\Core', 'has_flex_container' )
		) {
			$style = 'icon_buttons';
		}

		if ( ! $style ) {
			$style = 'icon_buttons';
		}
		$style = (string) apply_filters( 'wpex_mobile_menu_toggle_style', $style );
	}
	return $style;
}

/**
 * Return sidr menu source.
 */
function wpex_sidr_menu_source( $deprecated = '' ) {
	$items = [];
	if ( wpex_has_mobile_menu_alt() ) {
		$items['nav'] = '#mobile-menu-alternative';
	} else {
		$items['nav'] = '#site-navigation';
	}
	if ( get_theme_mod( 'mobile_menu_search', true ) ) {
		$items['search'] = '#mobile-menu-search';
	}
	$items = (array) apply_filters( 'wpex_mobile_menu_source', $items );
	return implode( ', ', $items );
}

/**
 * Return mobile toggle icon html.
 */
function wpex_get_mobile_menu_toggle_icon() {
	$mobile_menu_style = totaltheme_call_static( 'Mobile\Menu', 'style' );
	$text              = wpex_get_translated_theme_mod( 'mobile_menu_icon_label' );
	$toggle_state      = get_theme_mod( 'mobile_menu_icon_toggle_state', true );
	$toggle_state      = apply_filters( 'wpex_mobile_menu_icon_toggle_state', $toggle_state );

	$args = [
		'toggle_state' => $toggle_state,
		'animate'      => get_theme_mod( 'mobile_menu_icon_animate', true ),
		'rounded'      => get_theme_mod( 'mobile_menu_icon_rounded', false ),
	];

	$icon = totaltheme_call_static( 'Hamburger_Icon', 'render', $args );

	if ( $icon ) {
		$icon = '<span class="mobile-menu-toggle__icon wpex-flex">' . $icon . '</span>';
	}

	$el_class = 'mobile-menu-toggle';

	if ( $text ) {
		$el_class .= ' wpex-gap-10';
		$label_position = get_theme_mod( 'mobile_menu_icon_label_position' );
		if ( 'left' === $label_position ) {
			$el_class .= ' wpex-flex-row-reverse';
		}
		$text = '<span class="mobile-menu-toggle__label">' . esc_html( $text ) . '</span>';
	}

	// Begin output.
	$html = '<a href="#" class="' . esc_attr( $el_class ) . '" role="button" aria-expanded="false">';
		$button_text = $icon . $text;
		$html .= apply_filters( 'wpex_mobile_menu_open_button_text', $button_text );
		$html .= '<span class="screen-reader-text" data-open-text>' . esc_html( wpex_get_aria_label( 'mobile_menu_open' ) ) . '</span>';
		$html .= '<span class="screen-reader-text" data-open-text>' . esc_html( wpex_get_aria_label( 'mobile_menu_close' ) ) . '</span>';
	$html .= '</a>';

	$html = apply_filters( 'wpex_get_mobile_menu_toggle_icon', $html ); // @todo deprecate
	$html = apply_filters( 'wpex_mobile_menu_toggle_icon', $html );

	return $html;
}

/**
 * Return mobile menu extra icons.
 *
 * @todo rename menu area to mobile_toggle_icons.
 */
function wpex_mobile_menu_toggle_extra_icons() {
	$icons_escaped = '';

	if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ 'mobile_menu' ] ) ) {

		$menu = wp_get_nav_menu_object( $locations[ 'mobile_menu' ] );

		if ( ! empty( $menu ) ) {

			$menu_items = wp_get_nav_menu_items( $menu->term_id );

			if ( $menu_items ) {

				foreach ( $menu_items as $key => $menu_item ) {

					$icon_is_title = false;
					$icon_name     = \get_post_meta( $menu_item->ID ?? 0, '_menu_item_totaltheme_icon', true );

					if ( ! $icon_name ) {
						$icon_is_title = true;
						$icon_name = $menu_item->title ?? '';
					}

					if ( ! $icon_name ) {
						continue;
					}

					$link_attrs = [
						'href' => esc_url( $menu_item->url ),
					];

					$title       = $menu_item->title ?? '';
					$title_attr  = $menu_item->attr_title ?? '';
					$description = '';
					$reader_text = '';
					$icon_html   = totaltheme_get_icon( $icon_name );

					if ( ! $icon_html ) {
						return;
					}

					$a_class = [
						'mobile-menu-extra-icons',
						'mobile-menu-' . sanitize_html_class( $icon_name ),
						'wpex-inline-block',
						'wpex-no-underline',
					];

					// Get mobile menu toggle style.
					$toggle_style = wpex_header_menu_mobile_toggle_style();

					/// Add margin to icons
					$toggle_style_w_icons = [
						'icon_buttons',
						'icon_buttons_under_logo',
						'centered_logo',
						'next_to_logo',
					];

					if ( in_array( $toggle_style, $toggle_style_w_icons ) ) {
						$a_class[] = 'wpex-mr-20';
					} else {
						$a_class[] = 'wpex-ml-20';
					}

					if ( totaltheme_is_integration_active( 'woocommerce' )
						&& ( '#cart' === $menu_item->url
							|| \str_contains( $icon_name, 'shopping-' )
							|| \str_contains( $icon_name, 'cart' )
						)
					) {
						if ( 'icon_dot' !== totaltheme_call_static( 'Integration\WooCommerce\Cart', 'header_display' ) ) {
							wp_enqueue_script( 'wc-cart-fragments' );
							$icon_html = '<span class="wpex-relative wpex-inline-block">' . $icon_html . totaltheme_call_static( 'Integration\WooCommerce\Cart', 'get_count_bubble' ) . '</span>';
						}
						if ( 'off-canvas' === totaltheme_call_static( 'Integration\WooCommerce\Cart', 'style' ) ) {
							$link_attrs['role'] = 'button';
							$link_attrs['aria-expanded'] = 'false';
							$link_attrs['data-wpex-toggle'] = 'off-canvas';
							$link_attrs['aria-controls'] = 'wpex-off-canvas-cart';
						}
					}

					if ( ! empty( $menu_item->classes ) && is_array( $menu_item->classes ) ) {
						$a_class = array_merge( $a_class, $menu_item->classes );
					}

					if ( '#search' === $menu_item->url ) {
						$link_attrs['role'] = 'button';
						$link_attrs['aria-expanded'] = 'false';
					}

					if ( ! empty( $menu_item->description ) ) {
						$description = '<span class="wpex-icon-label wpex-ml-10">' . esc_html( $menu_item->description ) . '</span>';
					}

					if ( $title_attr ) {
						$link_attrs['title'] = esc_attr( $title_attr );
					} elseif ( ! $icon_is_title && $title ) {
						$reader_text = '<span class="screen-reader-text">' . esc_html( $title ) . '</span>';
					}

					// Add classes at the end.
					$link_attrs['class'] = implode( ' ', array_map( 'esc_attr', $a_class ) );

					// Add icon to output.
					$icons_escaped .= wpex_parse_html( 'a', $link_attrs, $icon_html . $reader_text . $description );

				} // end foreach.

			} // End menu items check.

		} // End menu check.

	} // End location check.

	$icons_escaped = apply_filters( 'wpex_get_mobile_menu_extra_icons', $icons_escaped ); // @todo deprecate legacy filter
	$icons_escaped = apply_filters( 'wpex_header_menu_mobile_toggle_icons', $icons_escaped );

	if ( $icons_escaped ) {
		echo '<div class="wpex-mobile-menu-toggle-extra-icons">' . $icons_escaped . '</div>';
	}
}
