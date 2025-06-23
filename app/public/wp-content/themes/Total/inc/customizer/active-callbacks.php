<?php

namespace TotalTheme\Customizer;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Active Callbacks.
 */
final class Active_Callbacks {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Check if the toggle bar is enabled.
	 */
	public static function has_toggle_bar(): bool {
		return (bool) \get_theme_mod( 'toggle_bar', true );
	}

	/**
	 * Check if the toggle bar toggle button is enabled.
	 */
	public static function has_toggle_bar_btn(): bool {
		return ! ( \get_theme_mod( 'toggle_bar_enable_dismiss' ) && 'visible' === \get_theme_mod( 'toggle_bar_default_state' ) );
	}

	/**
	 * Check if the top bar is enabled.
	 */
	public static function has_top_bar(): bool {
		return (bool) \get_theme_mod( 'top_bar', true );
	}

	/**
	 * Check if breadcrumbs is enabled.
	 */
	public static function has_breadcrumbs(): bool {
		return \function_exists( 'yoast_breadcrumb' ) || (bool) \get_theme_mod( 'breadcrumbs', true );
	}

	/**
	 * Check if footer widgets are enabled.
	 */
	public static function has_footer_widgets(): bool {
		if ( totaltheme_call_static( 'Footer\Core', 'is_custom' ) ) {
			return (bool) \get_theme_mod( 'footer_builder_footer_widgets', false );
		} else {
			return (bool) \get_theme_mod( 'footer_widgets', true );
		}
	}

	/**
	 * Check if a logo image is set.
	 */
	public static function has_image_logo(): bool {
		return (bool) \get_theme_mod( 'custom_logo' );
	}

	/**
	 * Check if a logo image is not set.
	 */
	public static function hasnt_image_logo(): bool {
		return ! self::has_image_logo();
	}

	/**
	 * Check if the logo suports a max ratio.
	 */
	public static function can_logo_max_ratio(): bool {
		return self::has_image_logo() && \in_array( self::get_header_style(), [ 'seven', 'eight', 'nine', 'ten' ], true );
	}

	/**
	 * Check if a sticky header is enabled.
	 */
	public static function has_sticky_header(): bool {
		return (bool) \get_theme_mod( 'fixed_header' ) && 'disabled' !== \get_theme_mod( 'fixed_header_style' );
	}

	/**
	 * Check if shrink sticky header is enabled.
	 */
	public static function has_sticky_header_shrink(): bool {
		return self::has_sticky_header() && \in_array( \get_theme_mod( 'fixed_header_style' ), [ 'shrink', 'shrink_animated' ], true );
	}

	/**
	 * Check if the sticky header supports a custom logo.
	 */
	public static function can_sticky_header_custom_logo(): bool {
		return ( self::has_sticky_header() && self::has_image_logo() );
	}

	/**
	 * Check if the sticky header has a custom logo.
	 */
	public static function has_sticky_header_custom_logo(): bool {
		return \get_theme_mod( 'fixed_header_logo' ) && self::can_sticky_header_custom_logo();
	}

	/**
	 * Check if a sticky menu is supported.
	 */
	public static function can_sticky_menu(): bool {
		return \in_array( self::get_header_style(), [ 'two', 'three', 'four' ], true );
	}

	/**
	 * Check if the sticky header on mobile notice should display.
	 */
	public static function has_sticky_menu_notice(): bool {
		return \in_array( self::get_header_style(), [ 'two', 'three', 'four', 'six' ], true );
	}

	/**
	 * Check if the mobile menu icons menu is enabled.
	 */
	public static function has_mobile_menu_icons(): bool {
		$mobile_menu_toggle_styles_with_icons = [
			'icon_buttons',
			'icon_buttons_under_logo',
			'centered_logo',
			'next_to_logo',
		];
		return 'disabled' !== \get_theme_mod( 'mobile_menu_style' ) && \in_array( \get_theme_mod( 'mobile_menu_toggle_style', 'icon_buttons' ), $mobile_menu_toggle_styles_with_icons, true );
	}

	/**
	 * Check if the mobile menu hamburger can animate.
	 */
	public static function can_mobile_menu_hamburger_animate(): bool {
		return (bool) \get_theme_mod( 'mobile_menu_icon_toggle_state', true ) && self::has_mobile_menu_icons();
	}

	/**
	 ** Check if the mobile menu hamburger has a label.
	 */
	public static function has_mobile_menu_hamburger_label(): bool {
		return (bool) \get_theme_mod( 'mobile_menu_icon_label' ) && self::has_mobile_menu_icons();
	}

	/**
	 ** Check if the menu supports the active underline.
	 */
	public static function can_menu_active_underline(): bool {
		return ! \in_array( \get_theme_mod( 'header_style' ), [ 'six' , 'dev' ], true );
	}

	/**
	 ** Check if the menu has the active underline.
	 */
	public static function has_menu_active_underline(): bool {
		return (bool) \get_theme_mod( 'menu_active_underline', false ) && self::can_menu_active_underline();
	}

	/**
	 ** Check if the menu dropdown has a top border.
	 */
	public static function has_menu_dropdown_top_border(): bool {
		return (bool) \get_theme_mod( 'menu_dropdown_top_border', false );
	}

	/**
	 ** Check if the menu dropdown supports a pointer.
	 */
	public static function can_menu_dropdown_pointer(): bool {
		return ! ( ! \in_array( \get_theme_mod( 'header_style', 'one' ), [ 'one', 'five', 'seven', 'eight', 'nine', 'ten' ], true )
			|| \in_array( \get_theme_mod( 'menu_dropdown_style' ), [ 'minimal-sq', 'minimal' ], true )
			|| \get_theme_mod( 'menu_flush_dropdowns', false )
			|| \get_theme_mod( 'menu_dropdown_top_border', false )
		);
	}

	/**
	 ** Check if the menu supports custom typography.
	 */
	public static function can_menu_typography(): bool {
		return 'dev' !== self::get_header_style();
	}

	/**
	 * Check if the singular page header title is enabled.
	 */
	public static function has_page_header(): bool {
		return ( 'hidden' !== \get_theme_mod( 'page_header_style' ) );
	}

	/**
	 * Checks if the page header custom text field should display.
	 */
	public static function can_page_header_custom_text( $control ): bool {
		$type = self::get_control_post_type( $control );

		if ( ! $type ) {
			return true;
		}

		$title_key = ( 'blog' === $type ) ? 'post_singular_page_title' : "{$type}_singular_page_title";

		return ( \get_theme_mod( $title_key, true ) && 'custom_text' === \get_theme_mod( "{$type}_single_header", 'custom_text' ) && self::has_page_header() );
	}

	/**
	 * Check if the singular page header title has a background.
	 */
	public static function has_page_header_background(): bool {
		return (bool) \get_theme_mod( 'page_header_background_img' ) || 'background-image' === \get_theme_mod( 'page_header_style' );
	}

	/**
	 * Checks if the post has a dynamic template.
	 */
	public static function has_single_template( $control ): bool {
		$type = self::get_control_post_type( $control );

		if ( ! $type ) {
			return true;
		}

		switch ( $type ) {
			case 'blog':
				$type = 'post';
				break;
			case 'testimonial':
				$type = 'testimonials';
				break;
		}

		$template = (int) \get_theme_mod( "{$type}_singular_template" );

		if ( ! $template ) {
			return false;
		}

		$template_post = \get_post( $template );

		return ( $template_post && \is_a( $template_post, 'WP_Post' ) && 'trash' !== \get_post_status( $template_post ) );
	}

	/**
	 * Checks if the post does not have a dynamic template.
	 */
	public static function hasnt_single_template( $control ): bool {
		return ! self::has_single_template( $control );
	}

	/**
	 * Checks if the archive has a dynamic template.
	 */
	public static function has_archive_template( $control ): bool {
		$type = self::get_control_post_type( $control );

		if ( ! $type ) {
			return true;
		}

		if ( 'testimonial' === $type ) {
			$type = 'testimonials';
		}

		$template = (int) \get_theme_mod( "{$type}_archive_template_id" );

		if ( ! $template ) {
			return false;
		}

		$template_post = \get_post( $template );

		return ( \is_a( $template_post, 'WP_Post' ) && 'trash' !== \get_post_status( $template_post ) );
	}

	/**
	 * Checks if the archive does not have a dynamic template.
	 */
	public static function hasnt_archive_template( $control ): bool {
		return ! self::has_archive_template( $control );
	}

	/**
	 * Checks if there is a card style set for entries.
	 */
	public static function has_entry_card_style( $control ): bool {
		$type = self::get_control_post_type( $control );

		if ( ! $type ) {
			return true;
		}

		if ( 'testimonial' === $type ) {
			$type = 'testimonials';
		}

		if ( 'woo' !== $type && self::has_archive_template( $control ) ) {
			return false;
		}

		return (bool) \get_theme_mod( "{$type}_entry_card_style", false );
	}

	/**
	 * Checks if there is a card style is not set for entries.
	 */
	public static function hasnt_entry_card_style( $control ): bool {
		return ! self::has_entry_card_style( $control );
	}

	/**
	 * Checks if there is a card style set for entries or a dynamic template set for the archive.
	 */
	public static function has_entry_card_style_or_archive_template( $control ): bool {
		$type = self::get_control_post_type( $control );
		if ( ! $type ) {
			return true;
		}
		if ( 'testimonial' === $type ) {
			$type = 'testimonials';
		}
		return ( \get_theme_mod( "{$type}_entry_card_style" ) || self::has_archive_template( $control ) );
	}

	/**
	 * Checks if there is a card style set for entries or a dynamic template set for the archive.
	 */
	public static function hasnt_entry_card_style_or_archive_template( $control ): bool {
		return ! self::has_entry_card_style_or_archive_template( $control );
	}

	/**
	 * Checks if the entry supports equal heights.
	 */
	public static function entry_supports_equal_heights( $control ): bool {
		$type = self::get_control_post_type( $control );

		if ( ! $type ) {
			return true;
		}

		if ( 'blog' === $type ) {
			return ( 'masonry' !== self::get_blog_grid_style() && self::has_blog_grid() && ! self::has_entry_card_style( 'blog' ) );
		} else {
			if ( \get_theme_mod( "{$type}_entry_card_style" ) || self::has_archive_template( $control ) ) {
				return false;
			}
			return ( ! \in_array( \get_theme_mod( "{$type}_archive_grid_style" ), [ 'masonry', 'no-margins' ], true ) );
		}
	}

	/**
	 * Checks if there is not a card set for related items.
	 */
	public static function hasnt_related_card( $control ): bool {
		return ! (bool) \get_theme_mod( self::get_control_post_type( $control ) . '_related_entry_card_style' );
	}

	/**
	 * Check if the blog entry meta block is enabled.
	 */
	public static function has_blog_entry_meta(): bool {
		return ( \str_contains( (string) \get_theme_mod( 'blog_entry_composer', 'meta' ), 'meta' ) && self::hasnt_entry_card_style_or_archive_template( 'blog' ) );
	}

	/**
	 * Check if the blog entry readmore block is enabled.
	 */
	public static function has_blog_entry_readmore(): bool {
		return self::hasnt_archive_template( 'blog' ) && ( \str_contains( (string) \get_theme_mod( 'blog_entry_composer', 'readmore' ), 'readmore' ) || self::has_entry_card_style( 'blog' ) );
	}

	/**
	 * Check if the blog is using a left thumbnail layout.
	 */
	public static function has_blog_left_thumb(): bool {
		return 'thumbnail-entry-style' === self::get_blog_entry_style();
	}

	/**
	 * Check if the blog is using a grid.
	 */
	public static function has_blog_grid(): bool {
		if ( self::has_archive_template( 'blog' ) ) {
			return false;
		}

		// @note we can't use get_blog_entry_style() because this should also return true for custom cards.
		return ( \in_array( \get_theme_mod( 'blog_style' ), [ 'grid-entry-style', 'grid' ], true ) || self::has_entry_card_style( 'blog' ) );
	}

	/**
	 * Check if the blog single meta block is enabled.
	 */
	public static function has_blog_single_meta(): bool {
		return \str_contains( (string) \get_theme_mod( 'blog_single_composer', 'meta' ), 'meta' ) && ! self::has_single_template( 'blog' );
	}

	/*-------------------------------------------------------------------------------*/
	/* [ Helpers ]
	/*-------------------------------------------------------------------------------*/

	/**
	 * Returns the header style.
	 */
	public static function get_header_style(): string {
		return (string) \get_theme_mod( 'header_style' ) ?: 'one';
	}

	/**
	 * Returns the blog entry style.
	 */
	private static function get_blog_entry_style(): string {
		return self::hasnt_entry_card_style_or_archive_template( 'blog' ) ? (string) \get_theme_mod( 'blog_style' ) : '';
	}

	/**
	 * Returns the blog grid style.
	 */
	private static function get_blog_grid_style(): string {
		return (string) \get_theme_mod( 'blog_grid_style' );
	}

	/**
	 * Get post type from control.
	 */
	private static function get_control_post_type( $control ): string {
		if ( \in_array( $control, [ 'blog', 'portfolio', 'staff', 'testimonials' ], true ) ) {
			return $control;
		}
		if ( isset( $control->id ) && \is_string( $control->id ) ) {
			return \strtok( $control->id, '_' );
		}
		return '';
	}

}
