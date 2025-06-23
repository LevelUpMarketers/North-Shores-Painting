<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery helper functions.
 */
class Helpers {

	/**
	 * Theme Mode Enabled.
	 */
	private static $is_theme_mode_enabled;

	/**
	 * Checks if the current version of WPBakery is supported.
	 */
	public static function is_version_supported(): bool {
		return ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, WPEX_VC_SUPPORTED_VERSION, '>=' ) );
	}

	/**
	 * Checks if theme mode is enabled for WPBakery.
	 *
	 * Stores the value in the $is_theme_mode_enabled to prevent extra checks.
	 */
	public static function is_theme_mode_enabled(): bool {
		if ( \is_null( self::$is_theme_mode_enabled ) ) {
			self::$is_theme_mode_enabled = \wp_validate_boolean( \get_theme_mod( 'visual_composer_theme_mode', true ) );
		}
		return (bool) self::$is_theme_mode_enabled;
	}

	/**
	 * Checks if a given post has wpbakery content.
	 */
	public static function post_has_wpbakery( $post_id = null ): bool {
		if ( ! WPEX_VC_ACTIVE ) {
			return false;
		}
		if ( ! $post_id ) {
			$post_id = \wpex_get_current_post_id();
		}
		if ( ! $post_id ) {
			return false;
		}
		$post_content = \get_post_field( 'post_content', $post_id );
		return ( $post_content && \str_contains( $post_content, 'vc_row' ) );
	}

	/**
	 * Returns user access choices.
	 */
	public static function get_user_access_choices(): array {
		return [
			\esc_html__( 'All', 'total' )                       => '',
			\esc_html__( 'Logged in', 'total' )                 => 'logged_in',
			\esc_html__( 'Logged out', 'total' )                => 'logged_out',
			\esc_html__( 'First paginated page only', 'total' ) => 'not_paged',
			\esc_html__( 'Custom', 'total' )                    => 'custom',
		];
	}

	/**
	 * Returns choices for the user access option.
	 */
	public static function get_user_access_custom_choices(): array {
		$choices = [
			\esc_html__( '— Select —', 'total' ) => '',
		];
		if ( $restrict_content = \totaltheme_get_instance_of( 'Restrict_Content' ) ) {
			$custom_restrictions = $restrict_content->get_custom_restrictions();
			if ( $custom_restrictions ) {
				return \array_merge( $choices, \array_combine( $custom_restrictions, $custom_restrictions ) );
			}
		}
		return $choices;
	}

	/**
	 * Returns custom background image sources.
	 */
	public static function get_background_image_source_choices(): array {
		$choices = [
			\esc_html__( 'None', 'total' ) => '',
		];
		if ( \class_exists( 'TotalThemeCore\Vcex\Helpers\Get_Image_From_Source' ) ) {
			$choices = \array_merge( $choices, [
				\esc_html__( 'Featured Image', 'total' ) => 'featured',
				\esc_html__( 'Post Secondary Image', 'total' ) => 'secondary_thumbnail',
				\esc_html__( 'Post Primary Term (Category) Image', 'total' ) => 'primary_term_thumbnail',
				\esc_html__( 'Custom Field', 'total' ) => 'custom_field',
			] );
			if ( \function_exists( 'totalthemecore_call_static' ) && \totalthemecore_call_static( 'Cards\Meta', 'is_enabled' ) ) {
				$choices[ \esc_html__( 'Card Thumbnail', 'total' ) ] = 'card_thumbnail';
			}
		}
		return $choices;
	}

	/**
	 * Check shortcode access.
	 */
	public static function shortcode_has_access( $atts = [] ): bool {
		if ( isset( $atts['vcex_user_access'] ) && ! \is_admin() && ! self::is_frontend_edit_mode() ) {
			$callback = ( 'custom' === $atts['vcex_user_access'] && isset( $atts['vcex_user_access_callback'] ) ) ? $atts['vcex_user_access_callback'] : $atts['vcex_user_access'];
			if ( $callback && 'custom' !== $callback && $restrict_content = \totaltheme_get_instance_of( 'Restrict_Content' ) ) {
				return $restrict_content->check_restriction( $callback );
			}
		}
		return true;
	}

	/**
	 * Returns post thumbnail id for dynamic settings such as background images.
	 */
	public static function get_post_thumbnail_id() {
		if ( \in_the_loop() || \totaltheme_is_card() ) {
			$post_id = \get_the_ID();
		} else {
			$post_id = \wpex_get_dynamic_post_id();
		}
		if ( $post_id ) {
			if ( 'attachment' === \get_post_type( $post_id ) ) {
				$thumbnail_id = $post_id;
			} else {
				$thumbnail_id = \get_post_thumbnail_id( $post_id );
			}
		} else {
			$thumbnail_id = \wpex_get_term_thumbnail_id();
		}
		if ( isset( $thumbnail_id ) && 'attachment' === \get_post_type( $thumbnail_id ) ) {
			return $thumbnail_id;
		}
	}

	/**
     * Check if currently editing the page using the vc frontend editor.
     *
     * @note checking is_admin() doesn't work.
     */
    public static function is_frontend_edit_mode(): bool {
        return \function_exists( 'vc_is_inline' ) && \vc_is_inline();
    }

	/**
	 * Check if currently editing a specific post type using WPBakery.
	 */
	public static function is_post_type_edit_mode( string $post_type ): bool {
		return ( self::is_post_type_backend_mode( $post_type ) || self::is_post_type_frontend_mode( $post_type ) );
	}

	/**
     * Check if currently editing a specific post type in backend mode.
     */
	public static function is_post_type_backend_mode( string $post_type ): bool {
		return ( \WPEX_VC_ACTIVE && \is_admin() && isset( $_GET['post'] ) && $post_type === \get_post_type( $_GET['post'] ) );
	}

	/**
	 * Check if currently editing a specific post type in frontend mode.
	 */
	public static function is_post_type_frontend_mode( string $post_type ): bool {
		return ( \WPEX_VC_ACTIVE && \vc_is_page_editable() && $post_type === \get_post_type( \vc_get_param( 'vc_post_id' ) ) );
	}

	/**
	 * Conditional check to see if we should be parsing deprecated css options.
	 */
	public static function parse_deprecated_css_check( string $element ): bool {
	//	$check = \totaltheme_version_check( 'initial', '3.0', '<=' ); //  @todo?
		return (bool) apply_filters( 'wpex_vc_parse_deprecated_css_options', true, $element );
	}

	/**
	 * Returns classname for fixed background.
	 */
	public static function get_fixed_background_class( string $fixed_style ): string {
		$safe_class = \sanitize_html_class( $fixed_style );
		$class = "bg-{$safe_class}"; // older deprecated class.
		$class .= ' wpex-vc-bg-fixed';
		switch ( $safe_class ) {
			case 'fixed-top':
				$class .= ' wpex-vc-bg-top';
				break;
			case 'fixed-bottom':
				$class .= ' wpex-vc-bg-bottom';
				break;
			case 'fixed':
			default:
				$class .= ' wpex-vc-bg-center';
				break;
		}
		return $class;
	}

	/**
	 * Returns background image from CSS param.
	 */
	public static function get_background_image_url_from_css( $css ): string {
		if ( ! $css ) {
			return '';
		}
		if ( false === \preg_match( '/\?id=(\d+)/', $css, $id ) ) {
			return '';
		}
		if ( \count( $id ) < 2 || ! isset( $id[1] ) ) {
			return '';
		}
		return \wp_get_attachment_url( $id[1] );
	}

}
