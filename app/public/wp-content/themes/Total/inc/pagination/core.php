<?php

namespace TotalTheme\Pagination;

\defined( 'ABSPATH' ) || exit;

/**
 * Core Pagination.
 */
class Core {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns the array of pagination choices available in the theme.
	 */
	public static function choices() {
		$choices = [
			''                => \esc_html__( 'Default', 'total' ),
			'standard'        => \esc_html__( 'Standard', 'total' ),
			'load_more'       => \esc_html__( 'Load More', 'total' ),
			'infinite_scroll' => \esc_html__( 'Infinite Scroll', 'total' ),
			'next_prev'       => \esc_html__( 'Next/Prev', 'total' )
		];
		return $choices;
	}

	/**
	 * Returns the pagination display type.
	 */
	protected static function get_display_type( $loop_type = '' ) {
		switch ( $loop_type ) {
			case 'blog':
				return self::get_blog_display_type();
				break;
			default:
				if ( \totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
					if ( \is_post_type_archive() ) {
						$ptu_check = \wpex_get_ptu_type_mod( $loop_type, 'archive_pagination_style' );
						if ( $ptu_check ) {
							return $ptu_check;
						}
					} elseif ( \is_tax() ) {
						$ptu_check = \wpex_get_ptu_tax_mod( \get_query_var( 'taxonomy' ), 'pagination_style' );
						if ( $ptu_check ) {
							return $ptu_check;
						}
					}
				}
				return \get_theme_mod( "{$loop_type}_pagination_style", 'standard' );
				break;
		}
	}

	/**
	 * Returns blog pagination type based on the current archive.
	 */
	protected static function get_blog_display_type() {
		$pagination_style = \get_theme_mod( 'blog_pagination_style', 'standard' );

		if ( \is_category() ) {
			$cat_meta = \wpex_get_category_meta( \get_query_var( 'cat' ), 'wpex_term_pagination' );
			if ( $cat_meta ) {
				$pagination_style = $cat_meta;
			}
		}

		return $pagination_style;
	}

	/**
	 * Display pagination.
	 */
	public static function render( $loop_type = '' ) {
		$loop_type       = $loop_type ?: \wpex_get_index_loop_type();
		$pagination_type = self::get_display_type( $loop_type );
		switch ( $pagination_type ) {
			case 'infinite_scroll':
			case 'load_more':
				if ( \class_exists( 'TotalTheme\Pagination\Load_More' ) ) {
					$args = [
						'loop_type' => $loop_type,
					];
					if ( 'infinite_scroll' === $pagination_type  ) {
						$args['infinite_scroll'] = true;
					}
					Load_More::render_button( $args );
				}
				break;
			case 'next_prev':
				if ( \class_exists( 'TotalTheme\Pagination\Next_Prev' ) ) {
					(new Next_Prev())->render();
				}
				break;
			default:
				if ( \class_exists( 'TotalTheme\Pagination\Standard' ) ) {
					(new Standard())->render();
				}
				break;
		}
	}
}
