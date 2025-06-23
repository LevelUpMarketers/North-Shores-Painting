<?php

namespace TotalTheme\Mobile;

\defined( 'ABSPATH' ) || exit;

/**
 * Mobile Menu.
 */
class Menu {

	/**
	 * Mobile menu is enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * Mobile menu style.
	 */
	protected static $style;

	/**
	 * The mobile menu breakpoint.
	 */
	protected static $breakpoint;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns style choices.
	 */
	public static function style_choices(): array {
		$choices = [
			'sidr'                     => esc_html__( 'Sidebar', 'total' ),
			'toggle'                   => esc_html__( 'Dropdown', 'total' ),
			'toggle_inline'            => esc_html__( 'Dropdown (Inline)', 'total' ),
			'toggle_full'              => esc_html__( 'Dropdown (Full Height)', 'total' ),
			'full_screen'              => esc_html__( 'Full Screen (Overlay)', 'total' ),
			'full_screen_under_header' => esc_html__( 'Full Screen (Under Header)', 'total' ),
			'disabled'                 => esc_html__( 'Disabled', 'total' ),
		];
		$choices = \apply_filters( 'wpex_get_mobile_menu_styles', $choices ); // @deprecated
		return (array) \apply_filters( 'totaltheme/mobile/menu/style_choices', $choices );
	}

	/**
	 * Hook mobile menu template part into the wp_footer hook.
	 */
	public static function get_template_part(): void {
		if ( ! self::is_enabled() ) {
			return;
		}
		$template_map = [
			'toggle'                   => 'mobile_menu_toggle',
			'toggle_inline'            => 'mobile_menu_toggle',
			'toggle_full'              => 'mobile_menu_toggle',
			'full_screen'              => 'mobile_menu_full_screen',
			'full_screen_under_header' => 'mobile_menu_full_screen',
			'sidr'                     => 'mobile_menu_sidr',
		];
		$template_part = $template_map[ self::style() ] ?? '';
		if ( $template_part ) {
			\wpex_get_template_part( $template_part );
		}
	}

	/**
	 * Returns the mobile menu style.
	 */
	public static function style(): string {
		if ( ! \is_null( self::$style ) ) {
			return self::$style;
		}

		$style = \wpex_is_layout_responsive() ? \get_theme_mod( 'mobile_menu_style' ) : 'disabled';

		if ( empty( $style ) ) {
			$style = 'sidr';
		}

		// @note we don't add a Header:is_custom() check incase we want to use the mobile menu
		// in a custom header manually.
		if ( \totaltheme_call_static( 'Header\Menu', 'is_custom' ) && ! \wpex_has_mobile_menu_alt() ) {
			$style = 'disabled';
		}

		$style = \apply_filters( 'wpex_mobile_menu_style', $style ); // @deprecated
		$style = \apply_filters( 'wpex_header_menu_mobile_style', $style ); // @deprecated
		self::$style = (string) \apply_filters( 'totaltheme/mobile/menu/style', $style );

		return self::$style;
	}

	/**
	 * Check if the mobile menu is enabled.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		if ( \totaltheme_call_static( 'Header\Core', 'is_custom' ) ) {
			self::$is_enabled = false;
			return false; // don't run through filters.
		}

		$check = false;

		if ( ( \wpex_has_mobile_menu_alt() || \totaltheme_call_static( 'Header\Menu', 'is_enabled' ) )
			&& \wpex_is_layout_responsive()
			&& 'disabled' !== self::style()
		) {
			$check = true;
		}

		$check = \apply_filters( 'wpex_has_mobile_menu', $check ); // @deprecated
		$check = \apply_filters( 'wpex_has_header_menu_mobile', $check ); // @deprecated

		self::$is_enabled = (bool) \apply_filters( 'totaltheme/mobile/menu/is_enabled', $check );

		return self::$is_enabled;
	}

	/**
	 * Returns the mobile menu breakpoint.
	 */
	public static function breakpoint() {
		if ( ! \is_null( self::$breakpoint ) ) {
			return self::$breakpoint;
		}
		$breakpoint = ( $breakpoint = \get_theme_mod( 'mobile_menu_breakpoint' ) ) ? \absint( $breakpoint ) : 959;
		$breakpoint = \apply_filters( 'wpex_header_menu_mobile_breakpoint', $breakpoint ); // @deprecated
		$breakpoint = (int) \apply_filters( 'totaltheme/mobile/menu/breakpoint', $breakpoint );
		self::$breakpoint = \absint( $breakpoint ) ?: 959; // can't be empty since Total 5.0
		return self::$breakpoint;
	}

	/**
	 * Outputs mobile menu top hook content.
	 */
	public static function hook_top(): void {
		\ob_start();
			\wpex_hook_mobile_menu_top();
		$top_safe = \ob_get_clean();
		if ( $top_safe ) {
			$class_map = [
				'sidr'                     => 'wpex-mobile-menu-top wpex-hidden wpex-px-20 wpex-pb-20',
				'toggle_full'              => 'wpex-mobile-menu-top wpex-mb-20',
				'full_screen'              => 'wpex-mobile-menu-top wpex-pb-20',
				'full_screen_under_header' => 'wpex-mobile-menu-top wpex-pb-20',
				'toggle'                   => 'wpex-mobile-menu-top wpex-pb-20',
				'toggle_inline'            => 'wpex-mobile-menu-top wpex-pb-20',
			];
			$class_safe = $class_map[ self::style() ] ?? '';
			echo '<div class="' . $class_safe . '">' . $top_safe . '</div>';
		}
	}

	/**
	 * Outputs mobile menu bottom hook content.
	 */
	public static function hook_bottom(): void {
		\ob_start();
			\wpex_hook_mobile_menu_bottom();
		$bottom_safe = \ob_get_clean();
		if ( $bottom_safe ) {
			$class_map = [
				'sidr'                     => 'wpex-mobile-menu-bottom wpex-hidden wpex-px-20 wpex-pt-20',
				'toggle_full'              => 'wpex-mobile-menu-bottom wpex-mt-30',
				'full_screen'              => 'wpex-mobile-menu-bottom wpex-pt-20',
				'full_screen_under_header' => 'wpex-mobile-menu-bottom wpex-pt-20',
				'toggle'                   => 'wpex-mobile-menu-bottom wpex-pb-20',
				'toggle_inline'            => 'wpex-mobile-menu-bottom wpex-pb-20',
			];
			$class_safe = $class_map[ self::style() ] ?? '';
			echo '<div class="' . $class_safe . '">' . $bottom_safe . '</div>';
		}
	}

	/**
	 * Outputs mobile menu searchform.
	 */
	public static function search_form( $args = [] ): void {
		if ( \get_theme_mod( 'mobile_menu_search', true ) ) {
			\wpex_get_template_part( 'mobile_searchform', null, $args );
		}
	}

	/**
	 * Outputs mobile menu top section (header/close icon).
	 */
	public static function render_top( $args = [] ): void {
		$has_logo    = 'sidr' === self::style() && \wp_validate_boolean( \get_theme_mod('mobile_menu_logo_enable' ) );
		$top_gap     = ( $gap = \get_theme_mod( 'mobile_menu_top_gap' ) ) ? \absint( $gap ) : 15;
		$top_class   = "wpex-mobile-menu__top wpex-p-20 wpex-gap-{$top_gap}";
		$close_class = 'wpex-mobile-menu__close wpex-inline-flex wpex-no-underline';

		if ( $has_logo && self::is_title_centered() ) {
			$logo_centered = true;
			$top_class .= ' wpex-flex wpex-flex-col-reverse';
		} else {
			$top_class .= ' wpex-flex wpex-justify-between';
		}

		if ( $has_logo ) {
			$top_class .= ' wpex-mobile-menu__top--has-logo';
			if ( isset( $logo_centered ) && $logo_centered ) {
				$close_class .= ' wpex-ml-auto';
			}
		} else {
			$close_align = \get_theme_mod( 'mobile_menu_sidr_close_align' );
			if ( $close_align && ( 'end' === $close_align || 'right' === $close_align ) ) {
				$top_class .= ' wpex-flex-row-reverse';
			}
		}
		?>
		<div class="<?php echo \esc_attr( $top_class ); ?>">
			<?php if ( $has_logo ) {
				echo self::get_logo();
			} ?>
			<a href="#" role="button" class="<?php echo \esc_attr( $close_class ); ?>" aria-label="<?php echo \esc_attr( \wpex_get_aria_label( 'mobile_menu_close' ) ); ?>"><?php echo self::get_close_icon(); ?></a>
		</div>
		<?php
	}

	/**
	 * Check if the title is centered.
	 */
	protected static function is_title_centered(): bool {
		return \wp_validate_boolean( \get_theme_mod( 'mobile_menu_title_center', true ) );
	}

	/**
	 * Returns the mobile menu logo.
	 */
	public static function get_logo(): string {
		$logo_img = \wpex_get_translated_theme_mod( 'mobile_menu_logo_img' );
		$logo_img = (string) \apply_filters( 'totaltheme/mobile/menu/logo_img', $logo_img );

		if ( $logo_img ) {
			$logo_src = \wp_get_attachment_image_src( (int) $logo_img, 'full', false );
			if ( $logo_src && ! empty( $logo_src[0] ) ) {
				$alt = \get_post_meta( (int) $logo_img, '_wp_attachment_image_alt', true ) ?: '';
				$logo_width  = $logo_src[1] ?? '';
				$logo_height = $logo_src[2] ?? '';
				$logo_safe  = '<img src="' . \esc_url( $logo_src[0] ) . '" alt="' . \esc_attr( $alt ) . '" width="' . \esc_attr( $logo_width ) . '" height="' . \esc_attr( $logo_height ) . '" loading="lazy" class="wpex-align-middle">';
			}
		}

		$class = 'wpex-mobile-menu__logo';

		if ( ! isset( $logo_safe ) ) {
			$class .= ' wpex-font-bold wpex-text-1 wpex-text-2xl';
			$logo_safe = ( $text = \totaltheme_call_static( 'Header\Logo', 'get_text' ) ) ? \esc_html( \sanitize_text_field( $text ) ) : '';
		}

		if ( self::is_title_centered() ) {
			$class .= ' wpex-mx-auto wpex-text-center';
		}
		
		return '<div class="' . \esc_attr( $class ) . '">' . $logo_safe . '</div>';
	}

	/**
	 * Returns the mobile menu close icon.
	 */
	public static function get_close_icon( string $class = 'wpex-mobile-menu__close-icon', string $size = 'xl' ): string {
		return (string) \totaltheme_get_icon(
			\apply_filters( 'totaltheme/mobile/menu/close_icon', 'material-close' ),
			"{$class} wpex-flex",
			$size
		);
	}

	/**
	 * Enqueue mobile menu js.
	 */
	public static function enqueue_js(): void {
		$style = self::style();
		$class_map = [
			'full_screen'              => 'Mobile\Menu\Full_Screen',
			'full_screen_under_header' => 'Mobile\Menu\Full_Screen',
			'toggle'                   => 'Mobile\Menu\Toggle',
			'toggle_inline'            => 'Mobile\Menu\Toggle',
			'toggle_full'              => 'Mobile\Menu\Toggle',
			'sidr'                     => 'Mobile\Menu\Sidr',
		];
		if ( \array_key_exists( $style, $class_map ) ) {
			\totaltheme_call_non_static( $class_map[ $style ], 'enqueue_js' );
		}
	}

	/**
	 * Returns the submenu toggle icon.
	 */
	public static function get_submenu_toggle_icon(): string {
		if ( $custom_icon = \get_theme_mod( 'mobile_menu_open_submenu_icon' ) ) {
			$icon = \str_replace( '/', '-', \sanitize_text_field( $custom_icon ) );
		}
		if ( empty( $icon ) ) {
			$icon = 'angle-down';
		}
		$icon = (string) \apply_filters( 'wpex_mobile_menu_open_submenu_icon', $icon );
		return $icon;
	}

	/**
	 * Returns the global JS params for mobile menus.
	 */
	public static function get_global_js_l10n(): array {
		$l10n = [
			'breakpoint' => \totaltheme_call_static( 'Mobile\Menu', 'breakpoint' ),
			'i18n' => [
				'openSubmenu' => \esc_html__( 'Open submenu of %s', 'total' ),
				'closeSubmenu' => \esc_html__( 'Close submenu of %s', 'total' ),
			],
		];

		$subtoggle_icon = self::get_submenu_toggle_icon();

		if ( 'plus' === $subtoggle_icon ) {
			$l10n['openSubmenuIconActive'] = \totaltheme_get_icon(
				'minus',
				'wpex-open-submenu__icon wpex-open-submenu__icon--open wpex-hidden'
			);
			$subtoggle_icon_class = 'wpex-open-submenu__icon';
		} elseif ( 'material-add' === $subtoggle_icon ) {
			$l10n['openSubmenuIconActive'] = \totaltheme_get_icon(
				'material-remove',
				'wpex-open-submenu__icon wpex-open-submenu__icon--open wpex-hidden'
			);
			$subtoggle_icon_class = 'wpex-open-submenu__icon';
		} else {
			$subtoggle_icon_class = 'wpex-open-submenu__icon wpex-transition-transform wpex-duration-300';
		}

		$l10n['openSubmenuIcon'] = \totaltheme_call_static( 'Theme_Icons', 'get_icon', $subtoggle_icon, $subtoggle_icon_class );

		return $l10n;
	}

	/**
	 * Register mobile menu js.
	 */
	public static function register_js(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 6.0' );
	}

}
