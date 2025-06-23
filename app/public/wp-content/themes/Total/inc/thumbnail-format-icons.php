<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Display format icons over featured images.
 *
 * @todo change to use wpex_hook_entry_media_after for better consistency.
 */
class Thumbnail_Format_Icons {

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_filter( 'wpex_get_entry_media_after', [ $this, 'on_wpex_get_entry_media_after' ] );
	}

	/**
	 * Check if the thumbnail format icon html is enabled.
	 */
	protected static function is_enabled(): bool {
		$check = 'post' === \get_post_type();

		/**
		 * Filters whether the format icons are enabled.
		 *
		 * @param bool $check
		 */
		$check = \apply_filters( 'totaltheme/thumbnail_format_icons/is_enabled', $check );

		/*** deprecated */
		$check = \apply_filters( 'wpex_thumbnails_have_format_icons', $check );
		$check = \apply_filters( 'wpex_has_post_thumbnail_format_icon', $check );

		return (bool) $check;
	}

	/**
	 * Return icon name.
	 */
	protected function icon_name( $format = '' ): string {
		$icon_name  = \totaltheme_get_post_format_icon_name( $format );
		$icon_name = \apply_filters( 'wpex_get_thumbnail_format_icon_class', $icon_name, $format );
		return (string) $icon_name;
	}

	/**
	 * Hooks into wpex_get_entry_media_after.
	 */
	public function on_wpex_get_entry_media_after( $media_after = '' ) {
		if ( ! self::is_enabled() ) {
			return $media_after;
		}

		$post_format = \get_post_format();
		$icon_name   = $this->icon_name( $post_format );

		if ( ! $icon_name ) {
			return $media_after;
		}

		$icon_html = totaltheme_get_icon( $icon_name );

		if ( ! $icon_html ) {
			$icon_html = '<span class="' . \esc_attr( $icon_name ) . '" aria-hidden="true"></span>';
		}

		/**
		 * Filters the thumbnail post format icon html.
		 *
		 * @param string $icon_html
		 * @param string $post_format
		 *
		 * @todo rename filter.
		 */
		$icon_html = \apply_filters( 'wpex_get_thumbnail_format_icon_html', $icon_html, $post_format );

		if ( $icon_html ) {

			$class = [
				'wpex-thumbnail-format-icon',
				'wpex-block',
				'wpex-right-0',
				'wpex-bottom-0',
				'wpex-mr-15',
				'wpex-mb-15',
				'wpex-absolute',
				'wpex-text-white',
				'wpex-text-center',
				'wpex-leading-none',
			];

			/**
			 * Filters the post thumbnail format icon class.
			 *
			 * @param string $class
			 * @param string $post_format
			 */
			$class = \apply_filters( 'wpex_post_thumbnail_format_icon_class', $class, $post_format );

			$icon_html = '<i class="' . \esc_attr( \implode( ' ', $class ) ) . '" aria-hidden="true">' . $icon_html . '</i>';

			return  $media_after . $icon_html;
		}

	}

}
