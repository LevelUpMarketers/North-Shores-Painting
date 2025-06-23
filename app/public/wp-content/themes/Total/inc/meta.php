<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Meta Class.
 */
class Meta {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns the array of registered meta blocks.
	 */
	public static function registered_blocks(): array {
		return [
			'date'                => \esc_html__( 'Published Date', 'total' ),
			'date-modified'       => \esc_html__( 'Modified Date', 'total' ),
			'estimated-read-time' => \esc_html__( 'Estimated Read Time', 'total' ),
			'author'              => \esc_html__( 'Author', 'total' ),
			'categories'          => \esc_html__( 'Categories', 'total' ),
			'first_category'      => \esc_html__( 'First Category', 'total' ),
			'comments'            => \esc_html__( 'Comments', 'total' ),
		];
	}

	/**
	 * Render meta blocks.
	 */
	protected static function render_blocks( $args = [] ): void {
		$args = (array) \apply_filters( 'wpex_meta_args', $args );

		$blocks = $args['blocks'] ?? [];

		if ( ! $blocks ) {
			return;
		}

		foreach ( $blocks as $block_key => $block_val ) {
			$has_custom_callback = false;

			if ( \is_string( $block_val ) && \array_key_exists( $block_val, self::registered_blocks() ) ) {
				$block_type = $block_val; // fixes issues where blocks array doesn't have defined keys.
			} else {
				if ( \is_numeric( $block_key ) && \is_string( $block_val ) ) {
					$block_type = $block_val;
				} else {
					$block_type = $block_key;
					if ( is_callable( $block_val ) ) {
						$has_custom_callback = true;
					}
				}
			}

			if ( 'meta' === $block_type ) {
				continue; // prevent infinite loop with get_template_part()
			}

			if ( 'first_category' === $block_type ) {
				$block_type = 'first-category'; // consistency.
			}

			$block_args = [
				'icon'      => self::get_block_icon( $block_type ),
				'singular'  => $args['singular'] ?? true,    // used for schema markup.
				'hook_name' => $args['hook_name'] ?? 'meta',  // used for \apply_filters.
			];

			if ( $has_custom_callback ) {
				$block_type = 'custom';
			}

			switch ( $block_type ) {
				case 'date':
				case 'date-modified':
				case 'date-event':
					$block_args['format'] = $args['date_format'] ?? '';
					\get_template_part( "partials/meta/blocks/{$block_type}", null, $block_args );
					break;
				case 'author':
					$block_args['link'] = $args['author_link'] ?? true;
					\get_template_part( 'partials/meta/blocks/author', null, $block_args );
					break;
				case 'categories':
					$taxonomy = $args['categories_tax'] ?? \apply_filters( 'wpex_meta_categories_taxonomy', \wpex_get_post_type_cat_tax() );
					if ( $taxonomy ) {
						$block_args['taxonomy'] = $taxonomy;
						\get_template_part( 'partials/meta/blocks/categories', null, $block_args );
					}
					break;
				case 'first-category':
					$taxonomy = $args['first_category_tax'] ?? $args['categories_tax'] ?? \apply_filters( 'wpex_meta_first_category_taxonomy', \wpex_get_post_type_cat_tax() );
					if ( $taxonomy ) {
						$block_args['taxonomy'] = $taxonomy;
						\get_template_part( 'partials/meta/blocks/first-category', null, $block_args );
					}
					break;
				case 'estimated-read-time':
				case 'comments':
					\get_template_part( "partials/meta/blocks/{$block_type}", null, $block_args );
					break;
				case 'custom':
				default:
					$block_args['block_type']      = $block_key;
					$block_args['render_callback'] = $block_val;
					\get_template_part( 'partials/meta/blocks/custom', null, $block_args );
					break;
			}
		}
	}

	/**
	 * Return icon name for meta block.
	 */
	protected static function get_block_icon( $block_type = '' ): string {
		$icon = '';
		switch ( $block_type ) {
			case 'date':
			case 'date-event':
			case 'date-modified':
				$icon = 'calendar-o';
				break;
			case 'estimated-read-time':
				$icon = 'clock-o';
				break;
			case 'author':
				$icon = 'user-o';
				break;
			case 'categories':
			case 'first-category':
				$icon = 'folder-o';
				break;
			case 'comments':
				$icon = 'comment-o';
				break;
		}
		$icon = \apply_filters( 'wpex_meta_block_icon', $icon, $block_type ); // @deprecated
		return (string) \apply_filters( 'totaltheme/meta/block_icon', $icon, $block_type );
	}

}
