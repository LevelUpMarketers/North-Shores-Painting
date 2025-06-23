<?php

namespace TotalTheme\Footer;

\defined( 'ABSPATH' ) || exit;

/**
 * Footer Callout.
 */
class Callout {

	/**
	 * Stores content output to prevent extra db checks.
	 */
	protected static $content;

	/**
	 * Stores button output to prevent extra db checks.
	 */
	protected static $button;

	/**
	 * Is the footer callout enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns the callout breakpoint.
	 */
	public static function breakpoint(): string {
		$bk = \get_theme_mod( 'footer_callout_breakpoint' ) ?: 'md';
		return (string) $bk;
	}

	/**
	 * Checks if the footer callout is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		$post_id = \wpex_get_current_post_id();

		if ( \totaltheme_call_static( 'Integration\Elementor', 'location_exists', 'footer_callout' ) ) {
			$check = true;
		} else {
			$check = \get_theme_mod( 'callout', true );
		}

		$check = \apply_filters( 'wpex_callout_enabled', $check ); // @deprecated
		$check = \apply_filters( 'wpex_has_footer_callout', $check ); // @deprecated
		$check = (bool) \apply_filters( 'totaltheme/footer/callout/is_enabled', $check );

		if ( $post_id && $meta = \get_post_meta( $post_id, 'wpex_disable_footer_callout', true ) ) {
			if ( 'on' == $meta ) {
				$check = false;
			} elseif ( 'enable' == $meta ) {
				$check = true;
			}
		}

		self::$is_enabled = (bool) $check;

		return self::$is_enabled;
	}

	/**
	 * Returns the footer callout content.
	 */
	public static function get_content() {
		if ( ! \is_null( self::$content ) ) {
			return self::$content;
		}

		$content = '';
		$post_id = \wpex_get_current_post_id();
		$meta    = $post_id ? \get_post_meta( $post_id, 'wpex_callout_text', true ) : '';

		// Clean up meta (previously when using the wp_editor it would sometimes save with junk).
		if ( $meta ) {
			$meta = \str_replace( '<br data-mce-bogus="1">', '', $meta );
			$meta = \str_replace( '<p>&nbsp;<br></p>', '', $meta );
			$meta = \totaltheme_remove_empty_p_tags( $meta );
			$content = $meta;
		}

		// Return Customizer content.
		if ( ! \trim( $content ) ) {

			// Get content from theme mod.
			$content = \wpex_get_translated_theme_mod( 'callout_text', 'I am the footer call-to-action block, here you can add some relevant/important information about your company or product. I can be disabled in the Customizer.' );
			$content = \apply_filters( 'wpex_get_footer_callout_content', $content ); // @deprected
			$content = \apply_filters( 'wpex_footer_callout_content', $content ); // @deprected
			$content = (string) \apply_filters( 'totaltheme/footer/callout/content', $content );
		}

		// If page content is numeric and it's a post return the post content.
		if ( $content && \is_numeric( $content ) ) {
			$post_id = \wpex_parse_obj_id( $content, \get_post_type( $content ) );
			$temp_post = \get_post( $post_id );
			if ( $temp_post && ! \is_wp_error( $temp_post ) ) {
				if ( $wpb_style = totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' ) ) {
					$wpb_style->render_style( $post_id );
				}

				$content = \totaltheme_shortcode_unautop( $temp_post->post_content );
			}
		}

		self::$content = $content;

		return self::$content;
	}

	/**
	 * Returns the footer callout button link.
	 */
	public static function get_button_link() {
		$post_id = \wpex_get_current_post_id();
		if ( $post_id && $meta = \get_post_meta( $post_id, 'wpex_callout_link', true ) ) {
			$link = $meta;
		} else {
			$link = \wpex_translate_theme_mod( 'callout_link', \get_theme_mod( 'callout_link', '#' ) );
		}
		$link = \apply_filters( 'wpex_footer_callout_button_link', $link ); // @deprecated
		$link = (string) \apply_filters( 'totaltheme/footer/callout/button_link', $link );
		return $link ? esc_url( $link ) : '';
	}

	/**
	 * Returns the footer callout button text.
	 */
	public static function get_button_text(): string {
		$post_id = \wpex_get_current_post_id();
		if ( $post_id && $meta = \get_post_meta( $post_id, 'wpex_callout_link_txt', true ) ) {
			$text = $meta;
		} else {
			$text = \get_theme_mod( 'callout_link_txt', 'Get In Touch' );
			$text = \wpex_translate_theme_mod( 'callout_link_txt', $text );
		}
		$text = \apply_filters( 'wpex_footer_callout_button_text', $text ); // @deprecated
		return (string) \apply_filters( 'totaltheme/footer/callout/button_text', $text );
	}

	/**
	 * Returns the footer callout button icon.
	 */
	public static function get_button_icon(): string {
		$icon = \get_theme_mod( 'callout_button_icon' );
		$icon = \apply_filters( 'wpex_footer_callout_button_icon', $icon ); // @deprecated
		return (string) \apply_filters( 'totaltheme/footer/callout/button_icon', $icon );;
	}

	/**
	 * Returns the footer callout button.
	 */
	public static function get_button(): ?string {
		if ( ! is_null( self::$button ) ) {
			return self::$button;
		}

		$text = self::get_button_text();
		$link = self::get_button_link();

		if ( ! $text || ! $link ) {
			return null;
		}

		$has_classic_styles = \totaltheme_has_classic_styles();
		$text_escaped = '<span class="footer-callout-button-text">' . \wp_kses_post( $text ) . '</span>';
		$icon         = self::get_button_icon();
		$button_style = \get_theme_mod( 'callout_button_style' );
		$button_color = \get_theme_mod( 'callout_button_color' );

		$classes = [
			'footer-callout-button-link',
			\wpex_get_button_classes( $button_style, $button_color )
		];

		$classes[] = 'wpex-flex';
		$classes[] = 'wpex-items-center';
		$classes[] = 'wpex-justify-center';
		$classes[] = 'wpex-m-0';

		if ( $has_classic_styles ) {
			$classes[] = 'wpex-py-15';
			$classes[] = 'wpex-px-20';
		}

		if ( self::get_content() ) {
			if ( $has_classic_styles ) {
				$classes[] = 'wpex-text-lg';
			}
		} else {
			if ( $has_classic_styles ) {
				$classes[] = 'wpex-text-xl';
			}
			$classes[] = 'wpex-rounded-0';
		}

		$classes = (array) \apply_filters( 'wpex_footer_callout_button_class', $classes ); // @deprecated

		// Define callout button attributes
		$attrs = [
			'href'   => $link,
			'class'  => $classes,
			'target' => \get_theme_mod( 'callout_button_target', 'blank' ),
			'rel'    => \get_theme_mod( 'callout_button_rel' ),
		];

		$attrs = \apply_filters( 'wpex_callout_button_attributes', $attrs ); // @deprecated
		$attrs = \apply_filters( 'wpex_footer_callout_button_attributes', $attrs ); // @deprecated
		$attrs = (array) \apply_filters( 'totaltheme/footer/callout/button_attributes', $attrs );

		if ( $icon && 'none' !== $icon ) {
			$icon_position = \get_theme_mod( 'callout_button_icon_position' );
			switch ( $icon_position ) {
				case 'before_text':
					$text_escaped = \totaltheme_get_icon( $icon, 'footer-callout-button-icon theme-button-icon-left' ) . $text_escaped;
					break;
				default:
					$text_escaped = $text_escaped . \totaltheme_get_icon( $icon, 'footer-callout-button-icon theme-button-icon-right' );
					break;
			}
		}

		self::$button = (string) \wpex_parse_html( 'a', $attrs, $text_escaped );

		return self::$button;
	}

	/**
	 * Renders the footer callout button.
	 */
	public static function render_button(): void {
		echo self::get_button();
	}

	/**
	 * Returns the wrapper class.
	 */
	public static function wrapper_class() {
		$class = [];

		if ( self::get_content() ) {
			$class[] = 'wpex-surface-2';
			$class[] = 'wpex-text-2';
			$class[] = 'wpex-py-30';
			$class[] = 'wpex-border-solid';
			$class[] = 'wpex-border-surface-3';
			$class[] = 'wpex-border-y';
		} else {
			$class[] = 'btn-only';
		}

		if ( $visibility = \get_theme_mod( 'callout_visibility' ) ) {
			$class[] = \totaltheme_get_visibility_class( (string) $visibility );
		}

		if ( \get_theme_mod( 'footer_callout_bg_img' )
			&& $bg_style = (string) \get_theme_mod( 'footer_callout_bg_img_style' )
		) {
			$class[] = 'bg-' . \sanitize_html_class( $bg_style ); // @todo deprecate.
			$class[] = \wpex_parse_background_style_class( $bg_style );
		}

		$class[] = 'wpex-print-hidden';

		$class = \apply_filters( 'wpex_footer_callout_wrap_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/footer/callout/wrapper_class', $class );

		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', (array) $class ) ) . '"';
		}
	}

	/**
	 * Returns the footer callout inner class.
	 */
	public static function inner_class() {
		$class = [];
		if ( self::get_content() ) {
			$class[] = 'container';
			if ( self::get_button() && $bk = self::breakpoint() ) {
				$bk_safe = \sanitize_html_class( $bk );
				$class[] = "wpex-{$bk_safe}-flex";
				$class[] = "wpex-{$bk_safe}-items-center";
			}
		}
		$class = \apply_filters( 'wpex_footer_callout_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/footer/callout/inner_class', $class );
		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Returns the footer callout content class.
	 */
	public static function content_class() {
		$class = [
			'footer-callout-content',
			\totaltheme_has_classic_styles() ? 'wpex-text-xl' : 'wpex-text-lg',
		];
		if ( self::get_button() && $bk = self::breakpoint() ) {
			$bk_safe = \sanitize_html_class( $bk );
			$class[] = "wpex-{$bk_safe}-flex-grow";
			$class[] = "wpex-{$bk_safe}-w-75";
		}
		$class = \apply_filters( 'wpex_footer_callout_left_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/footer/callout/content_class', $class );
		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Returns the footer callout button class.
	 */
	public static function button_class() {
		$class = [
			'footer-callout-button',
			'wpex-mt-20',
		];

		if ( $bk = self::breakpoint() ) {
			$bk_safe = \sanitize_html_class( $bk );
			$class[]  = "wpex-{$bk_safe}-w-25";
			$class[]  = "wpex-{$bk_safe}-pl-20";
			$class[]  = "wpex-{$bk_safe}-mt-0";
		}

		$class = \apply_filters( 'wpex_footer_callout_right_class', $class ); // @deprecated
		$class = (array) \apply_filters( 'totaltheme/footer/callout/button_class', $class );

		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', (array) $class ) ) . '"';
		}
	}

}
