<?php

namespace TotalTheme\Header\Menu;

\defined( 'ABSPATH' ) || exit;

/**
 * Header Menu.
 */
class Search {

	/**
	 * Check if enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * The style.
	 */
	protected static $style;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns header menu search style.
	 */
	public static function is_enabled(): bool {
		if ( null !== self::$is_enabled ) {
			return self::$is_enabled;
		}
		if ( \totaltheme_call_static( 'Header\core', 'is_custom' ) || 'disabled' === self::style() ) {
			$check = false;
		} else {
			$check = \get_theme_mod( 'menu_search_enable', true );
			if ( ! $check && \totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
				$check = \totaltheme_call_static( 'Header\Flex\Aside', 'has_search_icon' );
			}
		}
		self::$is_enabled = (bool) \apply_filters( 'totaltheme/header/menu/search/is_enabled', $check );
		return self::$is_enabled;
	}

	/**
	 * Checks if the header menu is currently supported.
	 */
	public static function is_supported(): bool {
		if ( ! \totaltheme_call_static( 'Header\Core', 'is_enabled' ) ) {
			return false;
		}
		$check = true;
		$check = \apply_filters( 'wpex_has_menu_search', $check ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/header/menu/search/is_supported', $check );
	}

	/**
	 * Registers and enqueues header menu search js.
	 */
	public static function enqueue_js(): void {
		$style = self::style();
		if ( $style && 'modal' !== $style ) {
			$js_file_name = \str_replace( '_', '-', $style );
			if ( \file_exists( \WPEX_THEME_DIR . "/assets/js/frontend/search/{$js_file_name}.min.js" ) ) {
				\wp_enqueue_script(
					"wpex-search-{$style}",
					\totaltheme_get_js_file( "frontend/search/{$js_file_name}" ),
					[ \WPEX_THEME_JS_HANDLE ],
					\WPEX_THEME_VERSION,
					[
						'strategy' => 'defer',
					]
				);
			}
		}
	}

	/**
	 * Returns header menu search style.
	 */
	public static function style(): string {
		if ( null !== self::$style ) {
			return self::$style;
		}
		if ( self::is_supported() ) {
			$style = \get_theme_mod( 'menu_search_style', 'drop_down' );
			// Convert old disabled theme_mod.
			if ( 'disabled' === $style ) {
				\remove_theme_mod( 'menu_search_style' );
				\set_theme_mod( 'menu_search_enable', false );
			}
			$style = \apply_filters( 'wpex_menu_search_style', $style ); // @deprecated
			$style = (string) \apply_filters( 'totaltheme/header/menu/search/style', $style );
			if ( ! $style ) {
				$style = 'drop_down'; // style must never be empty.
			}
		} else {
			$style = 'disabled';
		}
		if ( 'modal' === $style ) {
			\totaltheme_init_class( 'Search\Modal' );
		}
		self::$style = $style;
		return self::$style;
	}

	/**
	 * Returns header menu search form placeholder text.
	 */
	public static function get_placeholder_text(): string {
		$placeholder = \wpex_get_translated_theme_mod( 'menu_search_placeholder' );
		if ( ! $placeholder ) {
			$placeholder = \esc_html__( 'Search', 'total' );
			$style = self::style();
			if ( 'overlay' === $style || 'header_replace' === $style ) {
				$placeholder = \esc_html__( 'Type then hit enter to search&hellip;', 'total' );
			}
		} elseif ( 'none' === $placeholder ) {
			$placeholder = '';
		}
		$placeholder = \apply_filters( 'wpex_get_header_menu_search_form_placeholder', $placeholder ); // @deprecated
		return (string) \apply_filters( 'totaltheme/header/menu/search/placeholder', $placeholder );
	}

	/**
	 * Renders the dropdown widget classes.
	 */
	public static function drop_widget_class(): void {
		$class = [
			'header-searchform-wrap',
		];
		if ( $widget_class = \wpex_get_header_drop_widget_class() ) {
			$class = \array_merge( $class, $widget_class );
		}
		$class[] = 'wpex-p-15';
		echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
	}

	/**
	 * Returns header menu search form.
	 */
	public static function get_form( array $args = [] ): string {
		$args = \wp_parse_args( $args, [
			'echo'        => false,
			'placeholder' => self::get_placeholder_text(),
		] );
		$args = (array) \apply_filters( 'totaltheme/header/menu/search/form_args', $args );
		$form = \get_search_form( $args );
		$form = \apply_filters( 'wpex_get_header_menu_search_form', $form ); // @deprecated
		return (string) \apply_filters( 'totaltheme/header/menu/search/form', $form, $args );
	}

	/**
	 * Returns header menu search icon choices.
	 */
	public static function icon_choices(): array {
		$choices = [
			'search',
			'bootstrap-search',
			'material-search',
			'ionicons-search',
			'ionicons-search-outline',
			'ionicons-search-sharp',
		];
		$choices = \apply_filters( 'wpex_header_menu_icon_choices', $choices ); // @deprecated
		return (array) \apply_filters( 'totaltheme/header/menu/search/icon_choices', $choices );
	}

	/**
	 * Returns header menu search icon html.
	 */
	protected static function get_icon_html(): string {
		$icon = ( $icon = \get_theme_mod( 'menu_search_icon' ) ) ? \sanitize_text_field( $icon ) : 'search';
		$icon_class = 'wpex-menu-search-icon';
		if ( 'six' === \totaltheme_call_static( 'Header\Core', 'style' ) ) {
			$icon_class .= ' wpex-icon--w';
		}
		// @important - we don't use wpex-flex on the icon because it causes height issues with innner spans.
		return (string) \totaltheme_get_icon( $icon ?: 'search', $icon_class );
	}

	/**
	 * Checks if the header menu item should be inserted.
	 */
	public static function auto_insert_icon( $menu_location ): bool {
		$check = self::is_enabled() && \get_theme_mod( 'menu_search_enable', true ); // must check if it's for the menu only!
		if ( $check ) {
			$allowed_menu_locations = (array) \apply_filters( 'wpex_menu_search_icon_theme_locations', [ 'main_menu' ] ); // @deprecated
			$check = \in_array( $menu_location, $allowed_menu_locations, true );
		}
		return (bool) apply_filters( 'totaltheme/header/menu/search/auto_insert_icon', $check, $menu_location );
	}

	/**
	 * Inserts the header menu search icon into the menu.
	 */
	public static function insert_icon( $items, $args ) {
		if ( ! self::auto_insert_icon( $args->theme_location ?? '' ) ) {
			return $items;
		}

		// Get search style.
		$search_style = self::style();

		// Get header style.
		$header_style = \totaltheme_call_static( 'Header\Core', 'style' );

		// Define classes.
		$li_classes = 'search-toggle-li menu-item wpex-menu-extra';
		$a_classes  = 'site-search-toggle';

		// Remove icon margin.
		if ( 'six' !== $header_style ) {
			$li_classes .= ' no-icon-margin';
		}

		// Define aria vars.
		$aria_controls = '';

		// Define vars based on search style.
		switch ( $search_style ) {
			case 'modal':
				$a_classes .= ' wpex-open-modal';
				$aria_controls = 'wpex-search-modal';
				break;
			case 'overlay':
				$a_classes .= ' search-overlay-toggle';
				$aria_controls = 'wpex-searchform-overlay';
				break;
			case 'drop_down':
				$a_classes .= ' search-dropdown-toggle';
				$aria_controls = 'searchform-dropdown';
				break;
			case 'header_replace':
				$a_classes .= ' search-header-replace-toggle';
				$aria_controls = 'searchform-header-replace';
				break;
		}

		// Ubermenu integration.
		if ( \class_exists( 'UberMenu' ) && \apply_filters( 'wpex_add_search_toggle_ubermenu_classes', true ) ) {
			$li_classes .= ' ubermenu-item-level-0 ubermenu-item';
			$a_classes  .= ' ubermenu-target ubermenu-item-layout-default ubermenu-item-layout-text_only';
		}

		// Max Mega Menu integration.
		if ( \function_exists( 'max_mega_menu_is_enabled' ) && \max_mega_menu_is_enabled( $args->theme_location ) ) {
			$li_classes .= ' mega-menu-item';
			$a_classes  .= ' mega-menu-link';
		}

		// Add search icon and dropdown style.
		$menu_search = '';
		$menu_search .= '<li class="' . \esc_attr( $li_classes ) . '">';

			$a_attributes = [
				'href'          => '#',
				'class'         => \esc_attr( $a_classes ),
				'role'          => 'button',
				'aria-expanded' => 'false',
				'aria-controls' => $aria_controls,
			];

			if ( 'custom_link' === $search_style && $custom_link = \get_theme_mod( 'menu_search_custom_link' ) ) {
				if ( \str_starts_with( $custom_link, '/' ) ) {
					$custom_link = \home_url( $custom_link );
				}
				$a_attributes['href'] = $custom_link;
			}

			if ( $a_aria = \wpex_get_aria_label( 'search' ) ) {
				$a_attributes['aria-label'] = $a_aria;
			}

			$menu_search .= '<a ' . \wpex_parse_attrs( $a_attributes ) . '>';

				$menu_search .= '<span class="link-inner">';

					$text = \esc_html__( 'Search', 'total' );
					$text = \apply_filters( 'wpex_header_search_text', $text ); // @deprecated
					$text = (string) \apply_filters( 'totaltheme/header/menu/search/icon_label', $text );

					$icon = self::get_icon_html();

					if ( 'six' === $header_style ) {
						$menu_search .= $icon . '<span class="wpex-menu-search-text wpex-hidden">' . \esc_html( $text ) . '</span>';
					} else {
						$menu_search .= '<span class="wpex-menu-search-text wpex-hidden">' . \esc_html( $text ) . '</span>' . $icon;
					}

					$menu_search .= '</span>';

			$menu_search .= '</a>';

			if ( 'drop_down' === $search_style && true === \wpex_maybe_add_header_drop_widget_inline( 'search' ) ) {
				\ob_start();
				\wpex_get_template_part( 'header_search_dropdown' );
				$menu_search .= \ob_get_clean();
			}

		$menu_search .= '</li>';

		$menu_search_position = \apply_filters( 'wpex_header_menu_search_position', 'end' );

		switch ( $menu_search_position ) {
			case 'start':
				$items = $menu_search . $items;
				break;
			case 'end':
			default;
				$items = $items . $menu_search;
				break;
		}

		return $items;
	}

}
