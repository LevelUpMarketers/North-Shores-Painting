<?php

namespace TotalTheme\Header;

\defined( 'ABSPATH' ) || exit;

/**
 * Overlay (Transparent) Header.
 */
final class Overlay {

	/**
	 * Overlay header is enabled globally or not.
	 */
	protected static $is_global;

	/**
	 * Overlay header is enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * Overlay header logo img.
	 */
	protected static $logo_img;

	/**
	 * Overlay header retina logo img.
	 */
	protected static $logo_img_retina;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns the overlay header breakpoint.
	 */
	public static function get_breakpoint() {
		return ( \wpex_is_layout_responsive() && $breapoint = \get_theme_mod( 'overlay_header_breakpoint' ) ) ? \absint( $breapoint ) : false;
	}

	/**
	 * Checks if the overlay header is using a mobile_first design.
	 */
	public static function is_mobile_first(): bool {
		return \wpex_validate_boolean( \get_theme_mod( 'overlay_header_mobile_first', false ) );
	}

	/**
	 * Returns overlay header media query.
	 */
	public static function get_stylesheet_media_query(): string {
		$media = 'all';
		if ( $breakpoint = self::get_breakpoint() ) {
			if ( self::is_mobile_first() ) {
				$media = "only screen and (max-width:{$breakpoint}px)";
			} else {
				$breakpoint = $breakpoint + 1;
				$media = "only screen and (min-width:{$breakpoint}px)";
			}
		}
		return (string) apply_filters( 'totaltheme/header/overlay/stylesheet_media_query', $media );
	}

	/**
	 * Registers the overlay header stylesheet.
	 */
	public static function register_stylesheet(): void {
		$theme_handle = \totaltheme_call_static( 'Scripts\CSS', 'get_theme_handle' );
		\wp_register_style(
			'wpex-overlay-header',
			\totaltheme_get_css_file( 'frontend/header/overlay' ),
			$theme_handle ? [ $theme_handle ] : [],
			\WPEX_THEME_VERSION,
			self::get_stylesheet_media_query()
		);
	}

	/**
	 * Enqueues the overlay header stylesheet.
	 */
	public static function enqueue_stylesheet(): void {
		\wp_enqueue_style( 'wpex-overlay-header' );
	}

	/**
	 * Enqueues the overlay header stylesheet if enabled.
	 */
	public static function maybe_enqueue_stylesheet(): void {
		if ( ! self::is_enabled() ) {
			return;
		}
		self::register_stylesheet();
		self::enqueue_stylesheet();
	}

	/**
	 * Checks if the overlay header is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		if ( ! Core::is_enabled() ) {
			self::$is_enabled = false;
			return self::$is_enabled; // don't pass through filter.
		}

		// Check if enabled globally.
		$global_check = \get_theme_mod( 'overlay_header', false );

		// Check if enabled for specific post types only.
		if ( $global_check ) {
			$condition = \get_theme_mod( 'overlay_header_condition', null );
			if ( $condition ) {
				$conditional_logic = totaltheme_init_class( 'Conditional_Logic', $condition );
				if ( isset( $conditional_logic->result ) ) {
					$global_check = $conditional_logic->result;
				}
			}
		}

		// Default check based on global setting.
		$check = $global_check;

		// Get current post id
		$post_id = \wpex_get_current_post_id();

		// Return true if enabled via the post meta.
		// NOTE: The overlay header meta can still be filtered it's not hard set.
		if ( $post_id ) {
			$meta = \get_post_meta( $post_id, 'wpex_overlay_header', true );
			if ( $meta ) {
				$check = \wpex_validate_boolean( $meta );
			}
		}

		// Prevent issues on password protected pages.
		// @todo may need to revise this...for example now that you can insert a template
		// under the header overlay perhaps this is not needed.
		if ( ! $global_check && \post_password_required() && ! totaltheme_call_static( 'Page\Header', 'is_enabled' ) ) {
			$check = false;
		}

		$check = \apply_filters( 'wpex_has_overlay_header', $check ); // @deprecated
		self::$is_enabled = \apply_filters( 'totaltheme/header/overlay/is_enabled', $check );

		return self::$is_enabled;
	}

	/**
	 * Checks if the overlay header is enabled globally.
	 */
	public static function is_global(): bool {
		if ( ! \is_null( self::$is_global ) ) {
			return self::$is_global;
		}
		$check = false;
		if ( \wp_validate_boolean( \get_theme_mod( 'overlay_header', false ) )
			&& ! \get_theme_mod( 'overlay_header_condition', null )
			&& ! \wpex_has_post_meta( 'wpex_overlay_header' )
		) {
			$check = true;
		}
		$check = \apply_filters( 'wpex_is_overlay_header_global', $check ); // @deprecated
		self::$is_global = (bool) \apply_filters( 'totaltheme/header/overlay/is_global', $check );
		return self::$is_global;
	}

	/**
	 * Returns an array of style choices for the overlay header.
	 */
	public static function style_choices(): array {
		$choices = [
			''      => \esc_html__( 'Default', 'total' ),
			'white' => \esc_html__( 'White Text', 'total' ),
			'light' => \esc_html__( 'Light Text', 'total' ),
			'dark'  => \esc_html__( 'Black Text', 'total' ),
			'core'  => \esc_html__( 'Core Styles', 'total' ),
		];
		$choices = \apply_filters( 'wpex_header_overlay_styles', $choices ); //  @deprecated
		$choices = (array) \apply_filters( 'totaltheme/header/overlay/style_choices', $choices );
		return $choices;
	}

	/**
	 * Returns the overlay header style.
	 *
	 * This function should only be called a single time so no need to store the style in a var.
	 */
	public static function style(): string {
		$style = ( $style = \get_theme_mod( 'overlay_header_style' ) ) ? \sanitize_text_field( $style ) : '';

		// Get overlay style based on meta option if hard enabled on the post.
		if ( self::is_enabled() && \wpex_has_post_meta( 'wpex_overlay_header' ) ) {
			$meta = (string) \get_post_meta( \wpex_get_current_post_id(), 'wpex_overlay_header_style', true );
			if ( $meta ) {
				$style = \sanitize_text_field( $meta );
			}
		}

		// White is the default/fallback style.
		if ( ! $style ) {
			$style = 'white';
		}

		$style = \apply_filters( 'wpex_header_overlay_style', $style ); // @deprecated
		return (string) \apply_filters( 'totaltheme/header/overlay/style', $style );
	}

	/**
	 * Returns overlay header logo image src.
	 */
	public static function get_logo_image_src(): array {
		$logo = self::logo_img( false );
		if ( \is_numeric( $logo ) ) {
			return \wp_get_attachment_image_src( $logo, 'full', false ) ?: [];
		} elseif ( \is_string( $logo ) ) {
			return [ $logo, '', '', '' ];
		}
	}

	/**
	 * Returns overlay header logo image url.
	 */
	public static function get_logo_image_url() {
		return self::logo_img();
	}

	/**
	 * Returns overlay header retina logo image url.
	 */
	public static function get_retina_logo_image_url() {
		return self::logo_img_retina();
	}

	/**
	 * Returns overlay header logo image height.
	 */
	public static function get_logo_image_height() {
		$logo_src = self::get_logo_image_src();
		if ( ! empty( $logo_src[2] ) ) {
			return \absint( $logo_src[2] );
		}
	}

	/**
	 * Returns overlay header logo image width.
	 */
	public static function get_logo_image_width() {
		$logo_src = self::get_logo_image_src();
		if ( ! empty( $logo_src[1] ) ) {
			return \absint( $logo_src[1] );
		}
	}

	/**
	 * Returns the overlay header logo image.
	 */
	public static function logo_img( $parse_logo = true ) {
		if ( ! \is_null( self::$logo_img ) ) {
			if ( ! self::$logo_img ) {
				return '';
			}
			if ( $parse_logo ) {
				return self::parse_logo( self::$logo_img );
			} else {
				return self::$logo_img;
			}
		}

		if ( self::is_global() ) {
			$logo = ''; // we use the default logo for a global overlay header.
		} else {
			$logo = \wpex_get_translated_theme_mod( 'overlay_header_logo' );
		}

		// Check overlay logo meta option.
		if ( $post_id = \wpex_get_current_post_id() ) {
			$meta_logo = \get_post_meta( $post_id, 'wpex_overlay_header_logo', true );
			if ( $meta_logo ) {
				// Deprecated redux check.
				if ( \is_array( $meta_logo ) ) {
					if ( ! empty( $meta_logo['url'] ) ) {
						$logo = $meta_logo['url'];
					}
				} else {
					$logo = $meta_logo;
				}
			}
		}

		$logo = \apply_filters( 'wpex_header_overlay_logo', $logo ); // @deprecated
		self::$logo_img = \apply_filters( 'totaltheme/header/overlay/logo_image_id', $logo );

		// return self::logo_img( $parse_logo ); // @todo use recursive function?

		if ( self::$logo_img ) {
			if ( $parse_logo ) {
				return self::parse_logo( self::$logo_img );
			} else {
				return self::$logo_img;
			}
		}
	}

	/**
	 * Returns the overlay header retina logo image.
	 */
	public static function logo_img_retina( $parse_logo = true ) {
		if ( ! \is_null( self::$logo_img_retina ) ) {
			if ( ! self::$logo_img_retina ) {
				return '';
			}
			if ( $parse_logo ) {
				return self::parse_logo( self::$logo_img_retina );
			} else {
				return self::$logo_img_retina;
			}
		}

		if ( self::is_global() ) {
			$logo = ''; // we use the default logo for a global overlay header.
		} else {
			$logo = \wpex_get_translated_theme_mod( 'overlay_header_logo_retina' );
		}

		if ( $post_id = \wpex_get_current_post_id() ) {
			$meta_logo = \get_post_meta( $post_id, 'wpex_overlay_header_logo_retina', true );
			if ( $meta_logo ) {
				$logo = $meta_logo;
			}
		}

		$logo = \apply_filters( 'wpex_header_overlay_logo_retina', $logo ); // @deprecated
		self::$logo_img_retina = \apply_filters( 'totaltheme/header/overlay/logo_retina_image_id', $logo );

		// return self::logo_img_retina();

		if ( self::$logo_img_retina ) {
			if ( $parse_logo ) {
				return self::parse_logo( self::$logo_img_retina );
			} else {
				return self::$logo_img_retina;
			}
		}
	}

	/**
	 * Parses the logo img.
	 */
	private static function parse_logo( $logo = '' ) {
		return \wpex_get_image_url( $logo );
	}

	/**
	 * Returns the overlay header template id.
	 *
	 * This is the template inserted under the header - not the header itself!
	 */
	public static function get_template_id(): int {
		$template_id = ( $template_id = \get_theme_mod( 'overlay_header_template' ) ) ? \absint( $template_id ) : 0;
		$template_id = \apply_filters( 'wpex_overlay_header_template', $template_id ); // @deprecated
		return (int) \apply_filters( 'totaltheme/header/overlay/template_id', $template_id );
	}

	/**
	 * Render overlay header template.
	 */
	public static function render_template(): void {
		$id = self::get_template_id();

		if ( ! $id ) {
			return;
		}

		$post = \get_post( $id );

		if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		$parsed_content = \totaltheme_render_content( $post->post_content );

		if ( $parsed_content ) {
			if ( WPEX_VC_ACTIVE && $wpb_style = \totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' ) ) {
				$wpb_style->render_style( $id );
			}
			echo '<div class="overlay-header-template"><div class="container wpex-clr">' . $parsed_content . '</div></div>';
		}
	}

}
