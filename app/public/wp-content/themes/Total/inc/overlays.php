<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Overlays Class.
 */
class Overlays {

	/**
	 * Current overlay.
	 */
	protected static $current_overlay = '';

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Get overlay template.
	 */
	public static function render_template( string $position = 'inside_link', string $style = '', array $args = [] ): void {
		if ( ! $style && 'none' === $style ) {
			return;
		}
		self::$current_overlay = $style;
		$args['overlay_position'] = $position;
		$default_args = self::get_styles()[ $style ]['args'] ?? false;
		if ( $default_args ) {
			$args = \wp_parse_args( $default_args, $args );
		}
		\do_action( 'wpex_pre_include_overlay_template', $style, $args );
		if ( $template = self::get_template( $style ) ) {
			include $template;
		}
	}

	/**
	 * Get overlay template.
	 */
	protected static function get_template( string $style ): ?string {
		if ( $template = \locate_template( "partials/overlays/{$style}.php", false ) ) {
			return $template;
		}
		$custom_template = self::get_styles()[ $style ]['template'] ?? '';
		if ( $custom_template ) {
			return (string) \locate_template( "partials/overlays/{$custom_template}.php", false );
		}
		return '';
	}

	/**
	 * Returns the overlay speed.
	 */
	public static function get_speed( string $speed = '' ): int {
		if ( ! $speed ) {
			$speed = \get_theme_mod( 'overlay_speed', '300' );
		}
		$speed = (int) \apply_filters( 'wpex_overlay_speed', $speed, self::$current_overlay ); // @deprecated
		return $speed;
	}

	/**
	 * Returns the overlay background color.
	 */
	public static function get_bg_color( string $color = '' ): string {
		if ( ! $color ) {
			$color = \get_theme_mod( 'overlay_bg', 'black' );
		}
		$color = (string) \apply_filters( 'wpex_overlay_bg', $color, self::$current_overlay ); // @deprecated
		return \sanitize_html_class( $color );
	}

	/**
	 * Returns the overlay opacity.
	 */
	public static function get_opacity( string $opacity = '' ): int {
		if ( ! $opacity ) {
			$opacity = \get_theme_mod( 'overlay_opacity', '60' );
		}
		$opacity = (int) \apply_filters( 'wpex_overlay_opacity', $opacity, self::$current_overlay );  // @deprecated
		return $opacity;
	}

	/**
	 * Returns array of overlay styles.
	 */
	public static function get_styles(): array {
		$styles = [
			'hover-button' => [
				'name' => \esc_html__( 'Hover Button', 'total' ),
			],
			'hover-title' => [
				'name' => \esc_html__( 'Hover Title', 'total' ),
			],
			'magnifying-hover' => [
				'name' => \esc_html__( 'Magnifying Glass Hover', 'total' ),
			],
			'plus-hover' => [
				'name' => \esc_html__( 'Plus Icon Hover', 'total' ),
			],
			'plus-two-hover' => [
				'name' => \esc_html__( 'Plus Icon #2 Hover', 'total' ),
			],
			'plus-three-hover' => [
				'name' => \esc_html__( 'Plus Icon #3 Hover', 'total' ),
			],
			'view-lightbox-buttons-buttons' => [
				'name' => \esc_html__( 'View/Lightbox Icons Hover', 'total' ),
			],
			'view-lightbox-buttons-text' => [
				'name' => \esc_html__( 'View/Lightbox Text Hover', 'total' ),
			],
			'title-center' => [
				'name' => \esc_html__( 'Title Centered', 'total' )
			],
			'title-center-boxed' => [
				'name' => \esc_html__( 'Title Centered Boxed', 'total' )
			],
			'title-bottom' => [
				'name' => \esc_html__( 'Title Bottom', 'total' ),
			],
			'title-bottom-see-through' => [
				'name' => \esc_html__( 'Title Bottom See Through', 'total' ),
			],
			'title-push-up' => [
				'name' => \esc_html__( 'Title Push Up', 'total' ),
			],
			'title-excerpt-hover' => [
				'name' => \esc_html__( 'Title + Excerpt Hover', 'total' ),
			],
			'title-category-hover' => [
				'name' => \esc_html__( 'Title + Category Hover', 'total' ),
			],
			'title-category-visible' => [
				'name' => \esc_html__( 'Title + Category Visible', 'total' ),
			],
			'title-date-hover' => [
				'name' => \esc_html__( 'Title + Date Hover', 'total' ),
			],
			'title-date-visible' => [
				'name' => \esc_html__( 'Title + Date Visible', 'total' ),
			],
			'post-author' => [
				'name' => \esc_html__( 'Post Author', 'total' ),
			],
			'post-author-hover' => [
				'name' => \esc_html__( 'Post Author Hover', 'total' ),
			],
			'categories-title-bottom-visible' => [
				'name' => \esc_html__( 'Categories + Title Bottom Visible', 'total' ),
			],
			'slideup-title-white' => [
				'name' => \esc_html__( 'Slide-Up Title White', 'total' ),
			],
			'slideup-title-black' =>[
				'name' => \esc_html__( 'Slide-Up Title Black', 'total' ),
			],
			'category-tag' => [
				'name' => \esc_html__( 'Category Tag', 'total' ),
			],
			'category-tag-primary' => [
				'name' => \esc_html__( 'Category Tag (Primary Term Only)', 'total' ),
				'template' => 'category-tag',
				'args' => [ 'first_term_only' => true ],
			],
			'category-tag-two' => [
				'name' => \esc_html__( 'Category Tag 2', 'total' ),
			],
			'category-tag-two-primary' => [
				'name' => \esc_html__( 'Category Tag 2 (Primary Term Only)', 'total' ),
				'template' => 'category-tag-two',
				'args' => [ 'first_term_only' => true ],
			],
			'thumb-swap' => [
				'name' => \esc_html__( 'Secondary Image Swap', 'total' ),
			],
			'thumb-swap-title' => [
				'name' => \esc_html__( 'Secondary Image Swap and Title', 'total' ),
			],
			'video-icon' => [
				'name' => \esc_html__( 'Video Icon', 'total' ) . ' 1',
			],
			'video-icon_2' => [
				'name' => \esc_html__( 'Video Icon', 'total' ) . ' 2',
			],
			'video-icon_3' => [
				'name' => \esc_html__( 'Video Icon', 'total' ) . ' 3',
			],
			'video-icon_4' => [
				'name' => \esc_html__( 'Video Icon', 'total' ) . ' 4',
			],
		];

		return (array) \apply_filters( 'totaltheme/overlays/styles', $styles );
	}

	/**
	 * Returns array of overlay style choices (for use with select fields).
	 */
	public static function get_style_choices(): array {
		$choices = [];

		foreach ( self::get_styles() as $k => $style ) {
			$choices[ $k ] = $style['name'];
		}

		$choices = (array) \apply_filters( 'wpex_overlay_styles_array', $choices ); // @deprecated

		$choices = \array_merge(
			[
				''     => \esc_html__( 'Default', 'total' ),
				'none' => \esc_html__( 'None', 'total' ),
			],
			$choices
		);
	
		return (array) $choices;
	}

	/**
	 * Returns the default image overlay style for a post type entry.
	 */
	public static function get_entry_image_overlay_style( string $post_type = '' ): string {
		if ( ! $post_type ) {
			$post_type = \get_post_type();
		}

		if ( 'related' === \wpex_get_loop_instance() ) {
			$mod = ( 'post' === $post_type ) ? 'blog_related_overlay' : "{$post_type}_related_entry_overlay_style";
			$style = \get_theme_mod( $mod );
			// Portfolio and staff should fallback to the archive overlay style.
			if ( ! $style && ( 'staff' === $post_type || 'portfolio' === $post_type ) ) {
				$style = \get_theme_mod( "{$post_type}_entry_overlay_style" );
			}
		} else {
			$mod = ( 'post' === $post_type ) ? 'blog_entry_overlay' : "{$post_type}_entry_overlay_style";
			$style = \get_theme_mod( $mod );
		}

		$style = (string) \apply_filters( 'wpex_overlay_style', $style );

		if ( ! $style ) {
			$style = 'none'; // !important! @todo revise
		}

		return $style;
	}

	/**
	 * Returns classname for the the overlay parent given overlay style.
	 */
	public static function get_parent_class( $style = '' ): string {
		if ( ! $style || 'none' === $style || ! \is_string( $style ) ) {
			return '';
		}

		$class = [
			'overlay-parent',
			'overlay-parent-' . \sanitize_html_class( $style ),
		];

		$mobile_support = false;

		// Overlays with hover.
		$overlays_with_hover = [
			'hover-button',
			'magnifying-hover',
			//'plus-hover',
			//'plus-two-hover',
			//'plus-three-hover',
			'view-lightbox-buttons-buttons',
			'view-lightbox-buttons-text',
			'title-push-up',
			'title-excerpt-hover',
			'title-category-hover',
			'title-date-hover',
			'slideup-title-white',
			'slideup-title-black',
			'thumb-swap',
			'thumb-swap-title',
			'add-to-cart-hover',
		];

		/*** deprecated ***/
		$overlays_with_hover = (array) \apply_filters( 'wpex_overlays_with_hover', $overlays_with_hover, $style );

		if ( \in_array( $style, $overlays_with_hover, true ) ) {
			$mobile_support = true;
			$class[] = 'overlay-h'; // @note this class is only used for the mobile JS not for any actual hovers.
		}

		/**
		 * Filters if an element should hide overflow or not.
		 *
		 * @param bool $hide_overflow
		 */
		$hide_overflow = \apply_filters(
			'totaltheme/overlays/style_has_hidden_overflow',
			self::style_has_hidden_overflow( $style ),
			$style
		);

		$hide_overflow = \apply_filters( 'wpex_has_overlay_overflow_hidden', $hide_overflow ); // @deprecated

		if ( (bool) $hide_overflow ) {
			$class[] = 'wpex-overflow-hidden';
		}

		$mobile_support = \apply_filters( 'wpex_overlay_mobile_support', $mobile_support, $style ); // @deprecated
		$mobile_support = \apply_filters( 'totaltheme/overlays/style_has_mobile_support', $mobile_support, $style );

		if ( (bool) $mobile_support ) {
			$class[] = 'overlay-ms';
		}
		
		return \trim( \implode( ' ', $class ) );
	}

	/**
	 * Check if an overlay style has a hidden overflow or not.
	 */
	protected static function style_has_hidden_overflow( string $style ): bool {
		return \in_array( $style, [
			'hover-button',
			'magnifying-hover',
			'plus-hover',
			'plus-two-hover',
			'plus-three-hover',
			'view-lightbox-buttons-buttons',
			'view-lightbox-buttons-text',
			'title-center',
			'title-excerpt-hover',
			'title-category-hover',
			'title-category-visible',
			'title-price-hover',
			'title-date-hover',
			'title-date-visible',
			'slideup-title-white',
			'slideup-title-black',
			'thumb-swap',
			'thumb-swap-title',
			'video-icon_2',
			'video-icon_3',
			'video-icon_4',
			'add-to-cart-hover',
		] );
	}

}
