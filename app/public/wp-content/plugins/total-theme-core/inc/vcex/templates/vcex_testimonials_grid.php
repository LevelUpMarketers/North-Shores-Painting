<?php

/**
 * vcex_testimonials_grid shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.8
 */

defined( 'ABSPATH' ) || exit;

// Define output
$html = '';

// Deprecated Attributes
if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories'] ) ) {
	$atts['include_categories'] = $atts['term_slug'];
}

// Define entry counter
$entry_count = ! empty( $atts['entry_count'] ) ? $atts['entry_count'] : 0;

// Extract shortcode attributes
extract( $atts );

// Add paged attribute for load more button (used for WP_Query)
if ( ! empty( $atts['paged'] ) ) {
	$atts['paged'] = $atts['paged'];
}

// Define user-generated attributes
$atts['post_type'] = 'testimonials';
$atts['taxonomy']  = 'testimonials_category';
$atts['tax_query'] = '';

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts, 'vcex_testimonials_grid' );

// Output posts
if ( $vcex_query && $vcex_query->have_posts() ) :

	// Declare and sanitize vars
	$wrap_classes  = array( 'vcex-module', 'vcex-testimonials-grid-wrap', 'wpex-clr' );
	$grid_classes  = array( 'wpex-row', 'vcex-testimonials-grid', 'wpex-clr' );
	$grid_data     = array();
	$is_isotope    = false;
	$css_animation = vcex_get_css_animation( $css_animation );
	$title_tag     = $title_tag ?: 'div';

	// Is Isotope var
	if ( 'true' == $filter || 'masonry' == $grid_style ) {
		$is_isotope = true;
		vcex_enqueue_isotope_scripts();
	}

	// Get filter taxonomy
	if ( 'true' == $filter ) {
		$filter_taxonomy = apply_filters( 'vcex_filter_taxonomy', $atts['taxonomy'], $atts );
		$filter_taxonomy = taxonomy_exists( $filter_taxonomy ) ? $filter_taxonomy : '';
		if ( $filter_taxonomy ) {
			$atts['filter_taxonomy'] = $filter_taxonomy; // Add to array to pass on to vcex_grid_filter_args()
		}
	} else {
		$filter_taxonomy = null;
	}

	// Get filter categories
	if ( $filter_taxonomy ) {

		// Get filter terms
		$filter_terms = get_terms( $filter_taxonomy, vcex_grid_filter_args( $atts, $vcex_query ) );

		// Make sure we have terms before doing things
		if ( $filter_terms ) {

			// Check url for filter cat
			if ( $active_cat_query_arg = vcex_grid_filter_get_active_item( $filter_taxonomy ) ) {
				$filter_active_category = $active_cat_query_arg;
			}

			// Check if filter active cat exists on current page
			$filter_has_active_cat = in_array( $filter_active_category, wp_list_pluck( $filter_terms, 'term_id' ) ) ? true : false;

			// Add show on load animation when active filter is enabled to prevent double animation
			if ( $filter_has_active_cat ) {
				$grid_classes[] = 'wpex-show-on-load';
			}

		} else {

			$filter = false; // No terms so we can't have a filter

		}

	}

	// Wrap classes
	if ( $bottom_margin_class = vcex_parse_margin_class( $bottom_margin, 'bottom' ) ) {
		$wrap_classes[] = $bottom_margin_class;
	}

	if ( $visibility ) {
		$wrap_classes[] = vcex_parse_visibility_class( $visibility );
	}

	if ( $css_animation && 'true' == $filter ) {
		$wrap_classes[] = $css_animation;
	}

	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Grid Classes
	if ( $columns_gap ) {
		$grid_classes[] = 'gap-' . sanitize_html_class( $columns_gap );
	}

	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
		$grid_classes[] = 'wpex-overflow-hidden';
	}

	// Data
	if ( $is_isotope && 'true' == $filter ) {
		if ( 'no_margins' !== $grid_style && $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="' . esc_attr( $masonry_layout_mode ) . '"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $filter_speed ) . '"';
		}
		if ( ! empty( $filter_has_active_cat ) ) {
			$grid_data[] = 'data-filter=".cat-' . esc_attr( $filter_active_category ) . '"';
		}
	} else {

		$isotope_transition_duration = apply_filters( 'vcex_isotope_transition_duration', null, 'vcex_testimonials_grid' );
		if ( $isotope_transition_duration ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $isotope_transition ) . '"';
		}

	}

	// Columns classes.
	$columns_class = vcex_get_grid_column_class( $atts );

	// Excerpt style.
	$content_style = vcex_inline_style( array(
		'font_size' => $content_font_size,
		'color'     => $content_color,
	) );

	// Apply filters.
	$wrap_classes  = (array) apply_filters( 'vcex_testimonials_grid_wrap_classes', $wrap_classes ); // @todo deprecate?
	$grid_classes  = (array) apply_filters( 'vcex_testimonials_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_testimonials_grid_data_attr', $grid_data );

	// Convert arrays into strings.
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' '. implode( ' ', $grid_data ) : '';

	// VC filter
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_testimonials_grid', $atts );

	// Begin shortcode output.
	$html .= '<div class="'. esc_attr( $wrap_classes ) .'"'. vcex_get_unique_id( $unique_id ) .'>';

		// Display header if enabled.
		if ( $header ) {

			$html .= vcex_get_module_header( array(
				'style'   => $header_style,
				'content' => $header,
				'classes' => array( 'vcex-module-heading vcex_testimonials_grid-heading' ),
			) );

		}

		/*--------------------------------*/
		/* [ Entry Filter ]
		/*--------------------------------*/
		if ( 'true' == $filter && ! empty( $filter_terms ) ) {

			$all_text              = $all_text ?: esc_html__( 'All', 'total-theme-core' );
			$filter_button_classes = vcex_get_button_classes( $filter_button_style, $filter_button_color );
			$has_filter_all_link   = vcex_validate_att_boolean( 'filter_all_link', $atts, true );
			$filter_select_bk      = ! empty( $atts['filter_select_bk'] ) ? $atts['filter_select_bk'] : null;
			$has_filter_select     = $filter_select_bk ? true : false;

			// Filter font size.
			$filter_style_escaped = vcex_inline_style( array(
				'font_size' => $filter_font_size,
			) );

			$filter_classes = "vcex-testimonials-filter vcex-filter-links wpex-flex-wrap wpex-list-none wpex-m-0";
			$filter_classes .= vcex_has_classic_styles() ? ' wpex-mb-25' : ' wpex-mb-30';
			
			if ( 'plain-text' === $filter_button_style ) {
				$filter_classes .= ' wpex-gap-x-10 wpex-gap-y-5';
			} else {
				$filter_classes .= ' wpex-gap-5';
			}

			if ( ! $has_filter_select ) {
				$filter_classes .= ' wpex-flex';
			}

			if ( 'yes' === $center_filter ) {
				$filter_classes .= ' wpex-justify-center';
				$filter_classes .= ' center'; // legacy
			}

			// Filter select
			if ( $has_filter_select ) {
				$filter_select_bk_safe = sanitize_html_class( $filter_select_bk );
				$filter_classes .= " wpex-hidden wpex-{$filter_select_bk_safe}-flex";
				$filter_select_class = "vcex-testimonials-filter-select vcex-filter-links-select wpex-{$filter_select_bk_safe}-hidden wpex-select-wrap";
				$filter_select_class .= vcex_has_classic_styles() ? ' wpex-mb-25' : ' wpex-mb-30';

				$html .= '<div class="' . esc_attr( $filter_select_class ) . '">';

					$html .= '<select>';

						$mobile_select_all_text = $has_filter_all_link ? $all_text : esc_html__( 'Filter', 'total-theme-core' );

						$html .= '<option value="*">' . esc_html( $mobile_select_all_text ) . '</option>';

						foreach ( $filter_terms as $term ) {
							$selected = '';
							if ( $filter_active_category && $filter_active_category == $term->term_id ) {
								$selected = ' selected';
							}
							$html .= '<option value=".cat-' . absint( $term->term_id ) . '"' . $selected . '>' . esc_html( $term->name ) . '</option>';
						}

					$html .= '</select>';

				if ( is_callable( array( 'TotalTheme\Forms\Select_Wrap', 'arrow' ) ) ) {
					ob_start();
						TotalTheme\Forms\Select_Wrap::arrow();
					$html .= ob_get_clean();
				}

				$html .= '</div>';

			}

			// Filter Buttons.
			$html .= '<ul class="' . esc_attr( $filter_classes ) . '"' . $filter_style_escaped . '>';

				if ( $has_filter_all_link ) {

					$html .= '<li';

						if ( ! $filter_has_active_cat ) {
							$html .= ' class="active"';
						}

					$html .= '>';

						$html .= '<a href="#" data-filter="*" class="' . esc_attr( $filter_button_classes ) . '"><span>' . wp_strip_all_tags( $all_text ) . '</span></a>';

					$html .= '</li>';

				}

				foreach ( $filter_terms as $term ) :

					$html .= '<li class="filter-cat-' . sanitize_html_class( $term->term_id );

						if ( $filter_active_category == $term->term_id ) {
							$html .= ' active';
						}

					$html .= '">';

					$html .= '<a href="#" data-filter=".cat-' . sanitize_html_class( $term->term_id ) . '" class="' . esc_attr( $filter_button_classes ) . '">';

						$html .= wp_strip_all_tags( $term->name );

					$html .= '</a></li>';

				endforeach;

				if ( $vcex_after_grid_filter = apply_filters( 'vcex_after_grid_filter', '', $atts ) ) {
					$html .= $vcex_after_grid_filter;
				}

			$html .= '</ul>';

		}

		$html .= '<div class="' . esc_attr( $grid_classes ) . '"' . $grid_data . '>';

			// Start loop.
			while ( $vcex_query->have_posts() ) :

				// Get post from query.
				$vcex_query->the_post();

				// Add to the counter var.
				$entry_count++;

				// Get post data.
				$atts['post_title']     = get_the_title();
				$atts['post_permalink'] = vcex_get_permalink();

				// Add classes to the entries.
				$entry_classes = array(
					'testimonial-entry',
					'vcex-grid-item'
				);

				$entry_classes[] = $columns_class;

				$entry_classes[] = 'col-' . sanitize_html_class( $entry_count );

				if ( 'false' == $columns_responsive ) {
					$entry_classes[] = 'nr-col';
				} else {
					$entry_classes[] = 'col';
				}

				if ( $css_animation && 'true' != $filter ) {
					$entry_classes[] = $css_animation;
				}

				if ( $is_isotope ) {
					$entry_classes[] = 'vcex-isotope-entry';
				}

				/*--------------------------------*/
				/* [ Begin Entry Output ]
				/*--------------------------------*/
				$html .= '<div ' . vcex_grid_get_post_class( $entry_classes ) . '>';

					if ( ! isset( $content_class ) ) {
						if ( function_exists( 'wpex_testimonials_entry_content_class' ) ) {
							ob_start();
								wpex_testimonials_entry_content_class();
							$content_class = ' ' . trim( ob_get_clean() );
						} else {
							$content_class = '';
						}
					}

					$html .= '<div' . $content_class . '">';

						$html .= '<span class="testimonial-caret wpex-absolute wpex-block wpex-w-0 wpex-h-0"></span>';

						/*--------------------------------*/
						/* [ Title ]
						/*--------------------------------*/
						if ( vcex_validate_att_boolean( 'title', $atts, false, true ) ) {

							$title_html = '';

							if ( ! isset( $title_class ) ) {
								if ( function_exists( 'wpex_testimonials_entry_title_class' ) ) {
									ob_start();
										wpex_testimonials_entry_title_class();
									$title_class = ' ' . trim( ob_get_clean() );
								} else {
									$title_class = '';
								}
							}

							if ( ! isset( $title_tag_escaped ) ) {
								$title_tag_escaped = $title_tag ? tag_escape( $title_tag ) : 'h2';
							}

							if ( ! isset( $title_style ) ) {
								$title_style = vcex_inline_style( array(
									'font_size'     => $atts['title_font_size'] ?? null,
									'font_family'   => $atts['title_font_family'] ?? null,
									'color'         => $atts['title_color'] ?? null,
									'margin_bottom' => $atts['title_bottom_margin'] ?? null,
								) );
							}

							$title_html .= '<' . $title_tag_escaped . $title_class . $title_style . '>';

								// Title with link.
								if ( vcex_validate_att_boolean( 'title_link', $atts, false ) && is_post_type_viewable( 'testimonials' ) ) {
									$link_class = '';
									if ( ! empty( $atts['title_color'] ) ) {
										$link_class = ' class="wpex-inherit-color-important"';
									}
									$title_html .= '<a href="' . esc_url( $atts['post_permalink'] ) . '"' . $link_class . '>';
										$title_html .= esc_html( $atts['post_title'] );
									$title_html .= '</a>';
								}

								// Title without link.
								else {
									$title_html .= esc_html( $atts['post_title'] );
								}

							$title_html .= '</'. $title_tag_escaped .'>';

							/**
							 * Filters the vcex_testimonials_grid shortcode title html.
							 *
							 * @param string $html
							 * @param array $shortcode_attributes
							 */
							$html .= (string) apply_filters( 'vcex_testimonials_grid_title', $title_html, $atts );

						}

						$html .= '<div class="testimonial-entry-details testimonial-entry-text wpex-text-pretty wpex-last-mb-0 wpex-clr"' . $content_style . '>';

							/*--------------------------------*/
							/* [ Excerpt ]
							/*--------------------------------*/
							$excerpt_html = '';
							if ( vcex_validate_att_boolean( 'excerpt', $atts, false, true ) ) {

								// Custom readmore text.
								if ( vcex_validate_att_boolean( 'read_more', $atts, true, true ) && is_post_type_viewable( 'testimonials' ) ) {

									// Add arrow.
									if ( 'false' != $read_more_rarr ) {
										$read_more_rarr_html = ' <span>' . vcex_readmore_button_arrow() . '</span>';
									} else {
										$read_more_rarr_html = '';
									}

									// Read more text.
									if ( is_rtl() ) {
										$read_more_link = '&#8230;<a href="' . esc_url( $atts['post_permalink'] ) . '">' . $read_more_text . '</a>';
									} else {
										$read_more_link = '&#8230;<a href="' . esc_url( $atts['post_permalink'] ) . '">' . esc_html( $read_more_text ) . $read_more_rarr_html . '</a>';
									}

								} else {
									$read_more_link = '&#8230;';
								}

								// Custom Excerpt function.
								$excerpt_html .= vcex_get_excerpt( array(
									'length'  => $excerpt_length,
									'more'    => $read_more_link,
									'context' => 'vcex_testimonials_grid',
								) );

							// Display full post content.
							} else {
								$excerpt_html .= vcex_the_content( get_the_content(), 'vcex_testimonials_grid' );
							} // End excerpt check.

							/**
							 * Filters the vcex_testimonials_grid shortcode excerpt html.
							 *
							 * @param string $html
							 * @param array $shortcode_attributes
							 */
							$html .= apply_filters( 'vcex_testimonials_grid_excerpt', $excerpt_html, $atts );

						$html .= '</div>';

					$html .= '</div>';

					/*--------------------------------*/
					/* [ Bottom ]
					/*--------------------------------*/

					if ( ! isset( $bottom_class ) ) {
						if ( function_exists( 'wpex_testimonials_entry_bottom_class' ) ) {
							ob_start();
								wpex_testimonials_entry_bottom_class();
							$bottom_class = ' ' . trim( ob_get_clean() );
						} else {
							$bottom_class = '';
						}
					}

					$bottom_html = '<div' . $bottom_class . '>';

						/*--------------------------------*/
						/* [ Thumbnail ]
						/*--------------------------------*/
						if ( vcex_validate_att_boolean( 'entry_media', $atts, true, true ) ) {

							if ( ! isset( $custom_dims ) ) {
								if ( $img_width || $img_height || ! in_array( $img_size, array( 'wpex_custom', 'testimonials_entry' ) ) ) {
									$custom_dims = true;
								} else {
									$custom_dims = false;
								}
							}

							if ( ! isset( $img_style ) ) {
								$img_style = vcex_inline_style( array(
									'border_radius' => $img_border_radius,
								), false );
							}

							// Define thumbnail args.
							if ( ! isset( $thumbnail_args ) ) {
								$thumbnail_args = array(
									'size'          => $img_size,
									'width'         => $img_width,
									'height'        => $img_height,
									'style'         => $img_style,
									'crop'          => $img_crop,
									'apply_filters' => 'vcex_testimonials_grid_thumbnail_args',
									'filter_arg1'   => $atts,
								);
								if ( $is_isotope ) {
									$thumbnail_args['lazy'] = false;
								}
							}

							$thumbnail_args['attachment'] = get_post_thumbnail_id();

							$avatar_args = array(
								'custom_dims'    => $custom_dims,
								'thumbnail_args' => $thumbnail_args,
							);

							ob_start();
								get_template_part(
									'partials/testimonials/testimonials-entry-avatar',
									null,
									$avatar_args
								);
							$media_html = ob_get_clean();

							/**
							 * Filters the vcex_testimonials_grid shortcode media html.
							 *
							 * @param string $html
							 * @param array $shortcode_attributes
							 */
							$bottom_html .= (string) apply_filters( 'vcex_testimonials_grid_media', $media_html, $atts );

						}

						/*--------------------------------*/
						/* [ Meta ]
						/*--------------------------------*/

						if ( ! isset( $meta_class ) ) {
							if ( function_exists( 'wpex_testimonials_entry_meta_class' ) ) {
								ob_start();
									wpex_testimonials_entry_meta_class();
								$meta_class = ' ' . trim( ob_get_clean() );
							} else {
								$meta_class = '';
							}
						}

						$bottom_html .= '<div' . $meta_class . '>';

							/*--------------------------------*/
							/* [ Author ]
							/*--------------------------------*/
							if ( vcex_validate_att_boolean( 'author', $atts, true ) ) {

								ob_start();
									get_template_part( 'partials/testimonials/testimonials-entry-author' );
								$author_html = ob_get_clean();

								/**
								 * Filters the vcex_testimonials_grid shortcode author html.
								 *
								 * @param string $html
								 * @param array $shortcode_attributes
								 */
								$bottom_html .= (string) apply_filters( 'vcex_testimonials_grid_author', $author_html, $atts );
							}

							/*--------------------------------*/
							/* [ Company ]
							/*--------------------------------*/
							if ( vcex_validate_att_boolean( 'company', $atts, true ) ) {

								ob_start();
									get_template_part( 'partials/testimonials/testimonials-entry-company' );
								$company_html = ob_get_clean();

								/**
								 * Filters the vcex_testimonials_grid shortcode company html.
								 *
								 * @param string $html
								 * @param array $shortcode_attributes
								 */
								$bottom_html .= (string) apply_filters( 'vcex_testimonials_grid_company', $company_html, $atts );

							}

							/*--------------------------------*/
							/* [ Entry Rating ]
							/*--------------------------------*/
							if ( vcex_validate_att_boolean( 'rating', $atts, true ) ) {

								ob_start();
									get_template_part( 'partials/testimonials/testimonials-entry-rating' );
								$rating_html = ob_get_clean();

								/**
								 * Filters the vcex_testimonials_grid shortcode entry rating html.
								 *
								 * @param string $html
								 * @param array $shortcode_attributes
								 */
								$bottom_html .= (string) apply_filters( 'vcex_testimonials_grid_rating', $rating_html, $atts );

							}

						$bottom_html .= '</div>';

					$bottom_html .= '</div>';

					/**
					 * Filters the vcex_testimonials_grid shortcode entry bottom html.
					 *
					 * @param string $html
					 * @param array $shortcode_attributes
					 */
					$html .= (string) apply_filters( 'vcex_testimonials_grid_bottom', $bottom_html, $atts );

				$html .= '</div>';

				if ( $entry_count === absint( $columns ) ) {
					$entry_count=0;
				}

			endwhile;

		$html .= '</div>';

		/*--------------------------------*/
		/* [ Pagination ]
		/*--------------------------------*/

		// Load more button.
		if ( vcex_shortcode_has_loadmore( $atts, $vcex_query ) ) {
			vcex_loadmore_scripts();
			$atts['entry_count'] = $entry_count; // Update counter
			$html .= vcex_get_loadmore_button( 'vcex_testimonials_grid', $atts, $vcex_query );
		}

		// Standard pagination.
		elseif ( vcex_shortcode_has_pagination( $atts, $vcex_query ) ) {
			$html .= vcex_pagination( $vcex_query, false );
		}

	$html .= '</div>';

	// Reset the post data to prevent conflicts with WP globals.
	wp_reset_postdata();

	// @codingStandardsIgnoreLine
	echo $html;

// If no posts are found display message.
else :

	// Display no posts found error if function exists.
	echo vcex_no_posts_found_message( $atts );

// End post check
endif;
