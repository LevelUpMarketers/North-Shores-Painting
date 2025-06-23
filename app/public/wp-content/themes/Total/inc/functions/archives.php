<?php

defined( 'ABSPATH' ) || exit;

/**
 * Get index loop type.
 */
function wpex_get_index_loop_type() {
	if ( wpex_is_blog_query() ) {
		$loop_type = 'blog';
	} elseif ( is_search() ) {
		$search_style = totaltheme_call_static( 'Search\Archive', 'style' );
		$loop_type = ( 'default' === $search_style ) ? 'search' : $search_style;
	} elseif ( is_tax() ) {
		$taxonomy = get_query_var( 'taxonomy' );
		if ( $taxonomy && is_string( $taxonomy ) ) {
			switch ( $taxonomy ) {
				case 'portfolio_tag':
				case 'portfolio_category':
					$loop_type = 'portfolio';
					break;
				case 'staff_tag':
				case 'staff_category':
					$loop_type = 'staff';
					break;
				case 'testimonials_tag':
				case 'testimonials_category':
					$loop_type = 'testimonials';
					break;
			}
		}
	} elseif ( is_post_type_archive() ) {
		$loop_type = get_query_var( 'post_type' );
	}

	if ( empty( $loop_type ) ) {
		$loop_type = get_post_type(); // important fallback
	}

	return (string) apply_filters( 'wpex_get_index_loop_type', $loop_type );
}

/**
 * Returns true if the current Query is a query related to standard blog posts.
 */
function wpex_is_blog_query() {
	$check = false;
	if ( is_search() ) {
		$check = 'blog' === totaltheme_call_static( 'Search\Archive', 'style' );
	} elseif (
		is_home()
		|| is_category()
		|| is_tag()
		|| is_date()
		|| is_author()
		|| is_page_template( 'templates/blog.php' )
		|| is_page_template( 'templates/blog-content-above.php' )
		|| ( is_tax( 'post_series' ) && 'post' == get_post_type() )
		|| ( is_tax( 'post_format' ) && 'post' == get_post_type() )
	) {
		$check = true;
	}
	return (bool) apply_filters( 'wpex_is_blog_query', $check );
}

/**
 * Return archive grid style.
 */
function wpex_archive_grid_style() {
	$style = 'fit-rows';

	if ( totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
		$is_cpt_archive = is_post_type_archive();
		$loadmore       = \TotalTheme\Pagination\Load_More::get_data();

		if ( $loadmore ) {
			$taxonomy       = $loadmore['query_vars']['taxonomy'] ?? null;
			$post_type      = $loadmore['query_vars']['post_type'] ?? null;
			$is_cpt_archive = $taxonomy ? false : true;
		}

		if ( $is_cpt_archive ) {
			$post_type = $post_type ?? get_query_var( 'post_type' );
			if ( $ptu_style = wpex_get_ptu_type_mod( $post_type, 'archive_grid_style' ) ) {
				$style = $ptu_style;
			}
		}

		if ( ! empty( $taxonomy ) || is_tax() ) {
			$taxonomy = $taxonomy ?? get_query_var( 'taxonomy' );
			if ( $ptu_style = wpex_get_ptu_tax_mod( $taxonomy, 'grid_style' ) ) {
				$style = $ptu_style;
			}
		}
	}

	return (string) apply_filters( 'wpex_archive_grid_style', $style );
}

/**
 * Return archive grid gap.
 */
function wpex_archive_grid_gap() {
	$gap = '';
	if ( totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
		if ( is_post_type_archive() ) {
			if ( $ptu_gap = wpex_get_ptu_type_mod( get_query_var( 'post_type' ), 'archive_grid_gap' ) ) {
				$gap = $ptu_gap;
			}
		}
		if ( is_tax() ) {
			if ( $ptu_gap = wpex_get_ptu_tax_mod( get_query_var( 'taxonomy' ), 'grid_gap' ) ) {
				$gap = $ptu_gap;
			}
		}
	}
	return apply_filters( 'wpex_archive_grid_gap', $gap );
}

/**
 * Returns correct classes for archive grid.
 */
function wpex_get_archive_grid_class(): string {
	$classes = [
		'archive-grid',
		'entries',
		'wpex-row',
	];

	$grid_style = wpex_archive_grid_style();

	if ( 'masonry' === $grid_style || 'no-margins' === $grid_style ) {
		$classes[] = 'wpex-masonry-grid';
		wpex_enqueue_isotope_scripts(); // This is a good spot to enqueue grid scripts
	}

	if ( $gap = wpex_archive_grid_gap() ) {
		$classes[] = wpex_gap_class( $gap );
	}

	$classes[] = 'wpex-clr';

	$classes = apply_filters( 'wpex_get_archive_grid_class', $classes ); // legacy
	$classes = apply_filters( 'wpex_archive_grid_class', $classes ); // @since 5.0

	return implode( ' ', (array) $classes );
}

/**
 * Returns correct grid columns for custom types.
 *
 * @todo rename to wpex_cpt_entry_columns for consistency?
 * @todo cast return value to (int) then locate any (int) wpex_get_grid_entry_columns and remove casting.
 */
function wpex_get_grid_entry_columns() {
	$columms        = '1';
	$is_cpt_archive = is_post_type_archive();
	$post_type      = get_post_type();
	$loadmore       = \TotalTheme\Pagination\Load_More::get_data();

	if ( $loadmore ) {
		$taxonomy       = $loadmore['query_vars']['taxonomy'] ?? null;
		$post_type      = $loadmore['query_vars']['post_type'] ?? $post_type;
		$is_cpt_archive = $taxonomy ? false : true;
	}

	if ( $is_cpt_archive ) {
		$columms = get_theme_mod( $post_type . '_grid_entry_columns', $columms );
	}

	if ( totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
		if ( $is_cpt_archive ) {
			$ptu_check = wpex_get_ptu_type_mod( $post_type, 'archive_grid_columns' );
			if ( $ptu_check ) {
				$columms = $ptu_check;
				return $columms;
			}
		}
		if ( ! empty( $taxonomy ) || is_tax() ) {
			$taxonomy = $taxonomy ?? get_query_var( 'taxonomy' );
			$ptu_check = wpex_get_ptu_tax_mod( $taxonomy, 'grid_columns' );
			if ( $ptu_check ) {
				$columms = $ptu_check;
			}
		}
	}

	return apply_filters( 'wpex_get_grid_entry_columns', $columms, $post_type );
}

/**
 * Returns classes for archive grid entries.
 */
function wpex_get_archive_grid_entry_class(): array {
	$classes = [
		'cpt-entry',
		'wpex-clr',
	];

	$columns = wpex_get_grid_entry_columns();

	if ( $columns ) {
		$col_class = wpex_row_column_width_class( $columns );
		if ( $col_class ) {
			$classes[] = 'col';
			$classes[] = $col_class;
			$counter = wpex_get_loop_counter();
			if ( $counter ) {
				$classes[] = 'col-' . sanitize_html_class( $counter );
			}
		}
	}

	$grid_style = wpex_archive_grid_style();

	if ( absint( $columns ) > 1 && 'masonry' === $grid_style || 'no-margins' === $grid_style ) {
		$classes[] = 'wpex-masonry-col';
	}

	return (array) apply_filters( 'wpex_get_archive_grid_entry_class', $classes );
}

/**
 * Get term description location.
 */
function wpex_term_description_location(): string {
	if ( is_category() || is_tag() ) {
		$location = get_theme_mod( 'category_description_position' );
	} else {
		if ( wpex_is_woo_tax() ) {
			$location = get_theme_mod( 'woo_category_description_position' );
		}
		if ( totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
			$ptu_check = wpex_get_ptu_tax_mod( get_query_var( 'taxonomy' ), 'term_description_position' );
			if ( $ptu_check ) {
				$location = $ptu_check;
			}
		}
	}

	if ( empty( $location ) || ! is_string( $location ) ) {
		$location = TotalTheme\Page\Header::is_enabled() ? 'under_title' : 'above_loop';
	}

	return (string) apply_filters( 'wpex_term_description_location', $location );
}

/**
 * Check if term description should display above the loop.
 *
 * By default the term description displays in the subheading in the page header,
 * however, there are some built-in settings to enable the term description above the loop.
 * This function returns true if the term description should display above the loop and not in the header.
 */
function wpex_has_term_description_above_loop(): bool {
	$check = 'above_loop' === wpex_term_description_location();
	return (bool) apply_filters( 'wpex_has_term_description_above_loop', $check );
}

/**
 * Check if page header image is enabled for term.
 *
 * This function is only currently used in the Total Theme Core plugin.
 *
 * @todo deprecate and move code to total-theme-core
 */
function wpex_term_page_header_image_enabled( $term_id = '' ): bool {
	if ( ! get_theme_mod( 'term_page_header_image_enable', true ) ) {
		return false;
	}

	static $check = null;

	if ( null === $check ) {

		// Enabled by default.
		$check = true;

		// Disable for WooCommerce by default.
		if ( wpex_is_woo_tax() ) {
			$check = get_theme_mod( 'woo_shop_term_page_header_image_enabled', false );
		}

		if ( totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
			$ptu_check = wpex_get_ptu_tax_mod( get_query_var( 'taxonomy' ), 'term_page_header_image_enabled' );
			if ( isset( $ptu_check ) ) {
				$check = wp_validate_boolean( $ptu_check );
			}
		}

		$check = (bool) apply_filters( 'wpex_term_page_header_image_enabled', $check );

		// Get term id.
		$term_id = $term_id ?: get_queried_object_id();

		// Term id isn't empty so lets locate the thumbnail.
		if ( $term_id ) {

			$meta_check = get_term_meta( $term_id, 'page_header_bg', true );

			if ( ! isset( $meta_check ) ) {

				// Get data.
				$term_data = get_option( 'wpex_term_data' );
				$term_data = ! empty( $term_data[ $term_id ] ) ? $term_data[ $term_id ] : '';

				// Check setting.
				if ( $term_data && isset( $term_data['page_header_bg'] ) ) {
					$meta_check = $term_data['page_header_bg'];
				}

			}

			// Validate meta.
			if ( is_bool( $meta_check ) ) {
				$check = $meta_check;
			} elseif ( is_string( $meta_check ) && '' !== $meta_check ) {
				$meta_check = strtolower( $meta_check );
				if ( in_array( $meta_check, [ 'false', 'off', 'disabled' ] ) ) {
					$meta_check = false;
				}
				if ( in_array( $meta_check, [ 'true', 'on', 'enabled' ] ) ) {
					$meta_check = true;
				}
				$check = $meta_check;
			}

		}

	}

	return (bool) $check;
}
