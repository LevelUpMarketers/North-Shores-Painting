<?php declare(strict_types=1);

namespace TotalTheme\Header;

\defined( 'ABSPATH' ) || exit;

/**
 * Header Menu.
 */
class Menu {

	/**
	 * Header menu is enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * Header menu is custom check.
	 */
	protected static $is_custom;

	/**
	 * Check if the logo was injected into the menu (header five).
	 */
	protected static $logo_inserted = false;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns the theme_location for the header menu.
	 */
	public static function get_theme_location(): string {
		$location = 'main_menu';
		$location = \apply_filters( 'wpex_main_menu_location', $location ); // @deprecated
		return (string) \apply_filters( 'totaltheme/header/menu/theme_location', $location );
	}

	/**
	 * Returns a menu ID, slug, name, or object.
	 */
	public static function get_wp_menu() {
		$menu = \get_post_meta( \wpex_get_current_post_id(), 'wpex_custom_menu', true );
		if ( 'default' === $menu ) {
			$menu = '';
		}
		$menu = \apply_filters( 'wpex_custom_menu', $menu ); // @deprecated
		return \apply_filters( 'totaltheme/header/menu/wp_menu', $menu );
	}

	/**
	 * Checks if the header menu is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		if ( ! \totaltheme_call_static( 'Header\Core', 'is_enabled' ) || \totaltheme_call_static( 'Header\Core', 'is_custom' ) ) {
			self::$is_enabled = false;
			return self::$is_enabled;
		}

		// Check meta first and don't pass through filters.
		$meta_check = \get_post_meta( \wpex_get_current_post_id(), 'wpex_header_menu', true );

		if ( $meta_check ) {
			self::$is_enabled = \wpex_validate_boolean( $meta_check );
			return self::$is_enabled;
		}

		$check = self::has_menu();
		$check = \apply_filters( 'wpex_has_header_menu', $check ); // @deprecated
		self::$is_enabled = (bool) \apply_filters( 'totaltheme/header/menu/is_enabled', $check );

		return self::$is_enabled;
	}

	/**
	 * Check if the theme should run a multisite menu check.
	 */
	protected static function multisite_check(): bool {
		$check = false;
		$check = \apply_filters( 'wpex_ms_global_menu', $check ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/header/menu/multisite_check', $check );
	}

	/**
	 * Checks if a menu is assigned.
	 */
	public static function has_menu(): bool {
		return ( \has_nav_menu( self::get_theme_location() ) || self::get_wp_menu() || self::multisite_check() );
	}

	/**
	 * Checks if the header menu is custom.
	 */
	public static function is_custom(): bool {
		if ( ! \is_null( self::$is_custom ) ) {
			return self::$is_custom;
		}

		$check = false;

		if ( \function_exists( '\max_mega_menu_is_enabled' )
			&& \max_mega_menu_is_enabled( self::get_theme_location() )
		) {
			$check = true;
		}

		if ( \function_exists( '\ubermenu_get_menu_instance_by_theme_location' )
			&& \ubermenu_get_menu_instance_by_theme_location( self::get_theme_location() )
		) {
			$check = true;
		}

		$check = \apply_filters( 'wpex_is_header_menu_custom', $check ); // @deprecated
		self::$is_custom = (bool) \apply_filters( 'totaltheme/header/menu/is_custom', $check );

		return self::$is_custom;
	}

	/**
	 * Returns the header menu dropdown method.
	 */
	public static function get_dropdown_method(): string {
		$method = ( $method = \get_theme_mod( 'menu_dropdown_method' ) ) ? \sanitize_text_field( $method ) : '';
		$method = (string) \apply_filters( 'totaltheme/header/menu/dropdown_method', $method );
		return $method ?: 'hover';
	}

	/**
	 * Returns the header menu dropdown shadow.
	 */
	public static function get_dropdown_shadow_style(): string {
		return ( $drop_shadow = \get_theme_mod( 'menu_dropdown_dropshadow' ) ) ? \sanitize_text_field( $drop_shadow ) : '';
	}

	/**
	 * Returns the header menu dropdown style.
	 */
	public static function get_dropdown_style(): string {
		$style   = ( $style = \get_theme_mod( 'menu_dropdown_style' ) ) ? \sanitize_text_field( $style ) : '';
		$post_id = \wpex_get_current_post_id();
		if ( $post_id && \totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) && \wpex_has_post_meta( 'wpex_overlay_header' ) ) {
			$style = \get_post_meta( $post_id, 'wpex_overlay_header_dropdown_style', true ) ?: $style;
		}
		$style = \apply_filters( 'wpex_header_menu_dropdown_style', $style ); // @deprecated
		$style = (string) \apply_filters( 'totaltheme/header/menu/dropdown_style', $style );
		return 'default' === $style ? '' : $style;
	}

	/**
	 * Checks if the header menu has flush dropdowns (aka full height menu items).
	 */
	public static function has_flush_dropdowns(): bool {
		$supported_header_styles = [ 'one', 'seven', 'eight', 'nine', 'ten' ];
		$check = \get_theme_mod( 'menu_flush_dropdowns' ) && \in_array( totaltheme_call_static( 'Header\Core', 'style' ), $supported_header_styles, true );
		$check = \apply_filters( 'wpex_has_header_menu_flush_dropdowns', $check ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/header/menu/has_flush_dropdowns', $check );
	}

	/**
	 * Renders the header menu via wp_nav_menu.
	 */
	public static function wp_nav_menu(): void {
		if ( 'five' === \totaltheme_call_static( 'Header\Core', 'style' ) || is_customize_preview() ) {
			$wp_nav_menu_objects_filter = (bool) \add_filter( 'wp_nav_menu_objects', [ self::class, 'insert_nav_menu_objects' ], 100, 2 );
		}

		$menu_args = [
			'container'      => false,
			'fallback_cb'    => false,
			'menu_class'     => self::get_menu_class(),
			'theme_location' => self::get_theme_location(),
			'link_before'    => '<span class="link-inner">',
			'link_after'     => '</span>',
			'walker'         => \totaltheme_init_class( 'Walkers\Main_Nav_Menu' ),
		];

		if ( $wp_menu = self::get_wp_menu() ) {
			$menu_args['menu'] = $wp_menu;
		}

		/*** deprecated - you should just hook into wp_nav_menu_args ***/
		$menu_args = (array) \apply_filters( 'wpex_header_menu_args', $menu_args );

		if ( \is_multisite() && self::multisite_check() ) {
			\switch_to_blog( 1 );
			\wp_nav_menu( $menu_args );
			\restore_current_blog();
		} else {
			\wp_nav_menu( $menu_args );
		}

		if ( isset( $wp_nav_menu_objects_filter ) ) {
			\remove_filter( 'wp_nav_menu_objects', [ self::class, 'insert_nav_menu_objects' ], 100, 2 );
			if ( self::$logo_inserted ) {
				\remove_filter( 'walker_nav_menu_start_el', [ self::class, 'replace_menu_item_with_logo' ], 1, 4 );
			}
		}
	}

	/**
	 * Insert menu items to the ordered list.
	 */
	public static function insert_nav_menu_objects( $objects, $args ) {
		if ( 'five' !== \totaltheme_call_static( 'Header\Core', 'style' ) ) {
			return $objects; // currently only used for header style 5
		}

		if ( ! \is_array( $objects ) || 0 === count( $objects ) || ! isset( $args->theme_location ) || 'main_menu' !== $args->theme_location ) {
			return $objects;
		}

		// Get custom logo position.
		if ( $custom_position = \get_theme_mod( 'header_five_logo_menu_position' ) ) {
			// Since the customizer is a position option we need to subtract one to convert into "after".
			$insert_after = ( $custom_position = \absint( $custom_position ) ) ? $custom_position - 1 : 0;
		} else {
			$insert_after = false;
		}

		// Get array of top level items.
		$first_level_keys = [];
		$i = $insert_after ? 1 : 0; // we used 0 when calculating with JS - this is a fallback.
		foreach ( $objects as $k => $object ) {
			if ( empty( $object->menu_item_parent ) || '0' === (string) $object->menu_item_parent ) {
				$class = $object->classes ?? [];
				if ( isset( $object->title ) && 'header-logo-placeholder' === $object->title ) {
					break;
				}
				if ( \is_array( $class )
					&& ! \array_intersect( [ 'wpex-hidden', 'show-at-mm-breakpoint', 'hidden', 'wpex-invisible', 'wpex-opacity-0' ], $class )
				) {
					$first_level_keys[ $i ] = $k;
					$i++;
				}
			}
		}

		// Calculate the middle.
		if ( ! $insert_after ) {
			$extra_items = 0;
			if ( true === \totaltheme_call_static( 'Header\Menu\Search', 'auto_insert_icon', 'main_menu' ) ) {
				$extra_items++;
			}
			if ( true === \totaltheme_call_static( 'Dark_Mode', 'auto_insert_menu_icon', 'main_menu' ) ) {
				$extra_items++;
			}
			if ( \totaltheme_is_integration_active( 'woocommerce' )
				&& true === \totaltheme_call_static( 'Integration\WooCommerce\Cart', 'auto_insert_menu_icon', 'main_menu' )
			) {
				$extra_items++;
			}
			$first_level_items_count = \count( $first_level_keys ) + $extra_items;
			if ( $first_level_items_count ) {
				$split_offset = \apply_filters( 'wpex_localize_array', [] )['headerFiveSplitOffset'] ?? 1;
				$insert_after = \intval( \ceil( $first_level_items_count / 2 ) - $split_offset );
			}
		}

		// Insert logo object.
		if ( $insert_after && isset( $first_level_keys[ $insert_after ] ) && isset( $objects[ $first_level_keys[ $insert_after ] ] ) ) {
			$insert_after = $first_level_keys[ $insert_after ];
			$new_objects = [];
			$i=1; // menu order starts at 1 !!
			foreach ( $objects as $ob_k => $ob_v ) {
				$new_objects[ $i ] = $ob_v;
				$i++;
				if ( ! self::$logo_inserted && $ob_k === $insert_after ) {
					$logo_item = [
						'ID'               => 'logo',
						'db_id'            => 0,
						'menu_item_parent' => '0',
						'object_id'        => 0,
						'post_parent'      => 0,
						'type'             => 'custom',
						'object'           => 'custom',
						'type_label'       => 'Logo Placeholder',
						'title'            => 'header-logo-placeholder',
						'url'              => '#',
						'target'           => '',
						'attr_title'       => '',
						'description'      => '',
						'classes'          => [
							'wpex-px-40',
							'hide-at-mm-breakpoint',
						],
						'xfn'              => '',
						'current'          => false,
						// These are for uber menu...
						'ref_id'           => '',
						'custom_type'      => '',
					];
					$new_objects[ $i ] = (object) $logo_item;
					self::$logo_inserted = true;
					$i++;
					\add_filter( 'walker_nav_menu_start_el', [ self::class, 'replace_menu_item_with_logo' ], 1, 4 );
				}
			}
			$objects = $new_objects;
			unset( $new_objects );
		}
		return $objects;
	}

	/**
	 * Returns the inline Logo HTML.
	 */
	public static function replace_menu_item_with_logo( $item_output, $menu_item, $depth, $args ): string {
		if ( isset( $menu_item->title ) && 'header-logo-placeholder' === $menu_item->title ) {
			ob_start();
				wpex_header_logo();
			$logo_html = ob_get_clean();
			// @note we used to use "display" when it was JS added.
			$item_output = str_replace( 'show-at-mm-breakpoint', 'display', $logo_html );
		}
		return $item_output;
	}

	/**
	 * Return wrapper classes.
	 *
	 * Fallback for the older wpex_header_menu_classes() function.
	 */
	public static function get_wrapper_classes(): string {
		$header_style    = \totaltheme_call_static( 'Header\Core', 'style' );
		$has_flex_header = \totaltheme_call_static( 'Header\Core', 'has_flex_container' );
		$is_sticky       = \totaltheme_call_static( 'Header\Menu\Sticky', 'is_enabled' );

		$classes = [
			"navbar-style-{$header_style}",
		];

		// Fixed Height class.
		if ( 'one' === $header_style ) {
			$classes[] = 'navbar-fixed-height';
		}

		// Z-index.
		if ( $is_sticky )  {
			$classes[] = 'wpex-z-sticky';
		}

		// 100% height class.
		if ( 'five' === $header_style ) {
			$classes[] = 'wpex-h-100';
		}

		// Line Height class.
		if ( \in_array( $header_style, [ 'one', 'two', 'three', 'four', 'five' ], true ) ) {
			if ( 'one' === $header_style ) {
				if ( ! \get_theme_mod( 'menu_flush_dropdowns' ) ) {
					$classes[] = 'navbar-fixed-line-height';
				}
			} else {
				$classes[] = 'navbar-fixed-line-height';
			}
		}

		// Add classes for the sticky header menu.
		if ( $is_sticky ) {
			$classes[] = 'fixed-nav';
		}

		// Style specific classes.
		if ( 'dev' !== $header_style && ! \totaltheme_call_static( 'Header\Menu', 'is_custom' ) ) {

			// Flex Header styles.
			if ( $has_flex_header ) {
				$classes[] = 'wpex-max-h-100';
				if ( 'seven' === $header_style || 'eight' === $header_style ) {
					$classes[] = 'wpex-mr-auto';
				} elseif ( 'nine' === $header_style ) {
					$classes[] = 'wpex-ml-auto';
				}
			}

			// Active underline.
			if ( 'six' !== $header_style && get_theme_mod( 'menu_active_underline' ) ) {
				$classes[] = 'has-menu-underline';
			}

			// Dropdown caret.
			if ( ! \get_theme_mod( 'menu_flush_dropdowns' )
				&& ! \get_theme_mod( 'menu_dropdown_top_border' )
				&& ! \in_array( \totaltheme_call_static( 'Header\Menu', 'get_dropdown_style' ), [ 'minimal-sq', 'minimal' ], true )
				&& \in_array( $header_style, [ 'one', 'five', 'seven', 'eight', 'nine', 'ten' ], true )
			) {
				$has_caret = \get_theme_mod( 'menu_dropdown_caret', true );
			} else {
				$has_caret = false;
			}

			$has_caret = (bool) \apply_filters( 'wpex_has_header_menu_dropdown_caret', $has_caret ); // @deprecated

			if ( $has_caret ) {
				$classes[] = 'wpex-dropdowns-caret';
			}

			// Flush Dropdowns.
			if ( \totaltheme_call_static( 'Header\Menu', 'has_flush_dropdowns' ) ) {
				$classes[] = 'wpex-flush-dropdowns';
				if ( $has_flex_header ) {
					$classes[] = 'wpex-self-stretch';
				}
			}

			// Add special class if the dropdown top border option in the admin is enabled.
			if ( \get_theme_mod( 'menu_dropdown_top_border' ) ) {
				$classes[] = 'wpex-dropdown-top-border';
			}

			// Disable outer borders.
			if ( \in_array( $header_style, [ 'two', 'three', 'four' ], true ) && \get_theme_mod( 'header_menu_disable_outline' ) ) {
				$classes[] = 'no-outline';
			}

			// Disable inner borders.
			if ( \in_array( $header_style, [ 'two', 'six' ], true ) && \get_theme_mod( 'header_menu_disable_borders' ) ) {
				$classes[] = 'no-borders';
			}

			// Center items.
			if ( 'two' === $header_style && \get_theme_mod( 'header_menu_center' ) ) {
				$classes[] = 'center-items';
			}

			// Stretch items.
			if ( \get_theme_mod( 'header_menu_stretch_items' ) && \in_array( $header_style, [ 'two', 'three', 'four', 'five' ], true ) ) {
				$classes[] = 'wpex-stretch-items';
			}

		}

		// Stretch megamenus.
		if ( ( 'one' === $header_style || $has_flex_header ) && \get_theme_mod( 'megamenu_stretch', true ) ) {
			$classes[] = 'wpex-stretch-megamenus';
		}

		// Add breakpoint class.
		if ( \totaltheme_call_static( 'Mobile\Menu', 'is_enabled' ) ) {
			$classes[] = 'hide-at-mm-breakpoint';
		}

		// Add clearfix.
		if ( ! $has_flex_header ) {
			$classes[] = 'wpex-clr';
		}

		$classes[] = 'wpex-print-hidden';

		$classes = (array) \apply_filters( 'wpex_header_menu_wrap_classes', \array_combine( $classes, $classes ) ); // @deprecated

		return \implode( ' ', $classes );
	}

	/**
	 * Output wrapper class.
	 */
	public static function wrapper_class(): void {
		if ( $classes = self::get_wrapper_classes() ) {
			echo 'class="' . \esc_attr( $classes ) . '"';
		}
	}

	/**
	 * Return inner classes.
	 *
	 * Fallback for the older wpex_header_menu_classes() function.
	 */
	public static function get_inner_classes(): string {
		$header_style = \totaltheme_call_static( 'Header\Core', 'style' );

		$classes = [
			'navigation',
			'main-navigation',
			"main-navigation-{$header_style}"
		];

		if ( 'two' === $header_style || 'three' === $header_style || 'four' === $header_style ) {
			$classes[] = 'container';
			$classes[] = 'wpex-relative'; // !!important!!!
		}

		if ( 'five' === $header_style ) {
			$classes[] = 'wpex-h-100';
		}

		if ( \totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
			if ( ! \get_theme_mod( 'megamenu_stretch', true ) ) {
				$classes[] = 'wpex-relative';
			}
		} else {
			$classes[] = 'wpex-clr';
		}

		$classes = (array) \apply_filters( 'wpex_header_menu_classes', \array_combine( $classes, $classes ) ); // @deprecated

		return \implode( ' ', $classes );
	}

	/**
	 * Output inner class.
	 */
	public static function inner_class(): void {
		if ( $classes = self::get_inner_classes() ) {
			echo 'class="' . \esc_attr( $classes ) . '"';
		}
	}

	/**
	 * Returns the menu class for wp_nav_menu.
	 */
	public static function get_menu_class(): string {
		$classes         = [];
		$header_style    = \totaltheme_call_static( 'Header\Core', 'style' );
		$dropdown_method = \totaltheme_call_static( 'Header\Menu', 'get_dropdown_method' );

		if ( 'dev' === $header_style ) {
			$classes[] = 'main-navigation-dev-ul';
		} else {
			$classes[] = 'main-navigation-ul'; // !!! important - don't target dev !!!
			// The dropdown-menu class was deprecated in 5.20 to prevent extra bootstrap checks.
			// @todo - deprecate completely!
			if ( ! \totaltheme_version_check( 'initial', '6.0', '>' )
				&& ! \wp_style_is( 'arm_bootstrap_all_css' )
				&& ! \wp_style_is( 'bootstrap' )
				&& ! \wp_style_is( 'toolset_bootstrap_4' )
			) {
				$classes[] = 'dropdown-menu';
			}
		}

		// Flex classes.
		if ( \totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
			$classes[] = 'wpex-flex';
			$classes[] = 'wpex-items-center';
			// Flex Wrap.
			if ( ! \totaltheme_call_static( 'Header\Menu', 'has_flush_dropdowns' ) && 'ten' === $header_style ) {
				$classes[] = 'wpex-flex-wrap';
			}
			/* @todo add flex wrap for headers 8 and 9 as well - but should be optional since it will change things.
			if ( \in_array( $header_style, [ 'eight', 'nine', 'ten' ], true )
				&& ! \totaltheme_call_static( 'Header\Menu', 'has_flush_dropdowns' )
			) {
				$classes[] = 'wpex-flex-wrap';
				if ( 'nine' === $header_style ) {
					$classes[] = 'wpex-justify-end';
				}
			}*/
		}

		// Header specific classes.
		switch ( $header_style ) {
			case 'five':
				$classes[] = 'wpex-flex';
				$classes[] = 'wpex-float-none';
				$classes[] = 'wpex-h-100';
				$classes[] = 'wpex-justify-center';
				$classes[] = 'wpex-items-center';
				break;
		}

		// Add dropdown classes.
		switch ( $dropdown_method ) {
			case 'sfhover':
				$classes['sf-menu'] = 'sf-menu';
				break;
			case 'click':
				$classes[] = 'wpex-dropdown-menu';
				$classes[] = 'wpex-dropdown-menu--onclick';
				break;
			case 'hover':
			default;
				$classes[] = 'wpex-dropdown-menu';
				$classes[] = 'wpex-dropdown-menu--onhover';
				break;
		}

		// Dropdown animations.
		if ( \wpex_validate_boolean( \get_theme_mod( 'menu_drodown_animate' ) ) ) {
			switch ( $dropdown_method ) {
				case 'sfhover':
					$classes[] = 'sf-menu--animate';
					break;
				default:
					$classes[] = 'wpex-dropdown-menu--animate';
					break;
			}
		}

		$classes = (array) \apply_filters( 'wpex_header_menu_ul_classes', $classes ); // @deprecated

		return implode( ' ', $classes );
	}

}
