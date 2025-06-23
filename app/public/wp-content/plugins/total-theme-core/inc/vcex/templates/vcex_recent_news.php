<?php

/**
 * vcex_recent_news shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Add paged attribute for load more button (used for WP_Query).
if ( ! empty( $atts['paged'] ) ) {
	$atts['paged'] = $atts['paged'];
}

// Define non-vc attributes.
$atts['tax_query']  = '';
$atts['taxonomies'] = 'category';

// Extract shortcode atts.
extract( $atts );

// Define vars.
$output          = '';
$entry_count     = ! empty( $atts['entry_count'] ) ? absint( $atts['entry_count'] ) : 0;
$has_date        = vcex_validate_att_boolean( 'date', $atts, true, true );
$has_title       = vcex_validate_att_boolean( 'title', $atts, true, true );
$has_excerpt     = vcex_validate_att_boolean( 'excerpt', $atts, true, true );
$has_read_more   = vcex_validate_att_boolean( 'read_more', $atts, true, true );
$show_categories = vcex_validate_att_boolean( 'show_categories', $atts, false );
$show_thumbnail  = vcex_validate_att_boolean( 'featured_image', $atts, false );
$show_videos     = vcex_validate_att_boolean( 'featured_video', $atts, true );

// Get Standard posts.
if ( 'standard_post_types' === $get_posts ) {
	$atts['post_types'] = 'post';
}

// Build the WordPress query.
$vcex_query = vcex_build_wp_query( $atts, 'vcex_recent_news' );

// Output posts.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// Sanitize grid columns.
	$grid_columns = $grid_columns ? absint( $grid_columns ) : 1;

	// Set category taxonomy.
	if ( ! $categories_taxonomy ) {
		$categories_taxonomy = str_contains( $post_types, ',' ) ? 'category' : vcex_get_post_type_cat_tax( $post_types );
	}

	// Set show_categories to false if taxonomy doesn't exist.
	if ( ! taxonomy_exists( $categories_taxonomy ) ) {
		$show_categories = false;
	}

	// Wrap Classes.
	$wrap_classes = [
		'vcex-recent-news-wrap',
		'vcex-module',
	];

	if ( $bottom_margin ) {
		$wrap_classes[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
	}

	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	if ( $visibility ) {
		$wrap_classes[] = vcex_parse_visibility_class( $visibility );
	}

	if ( $css ) {
		$wrap_classes[] = vcex_vc_shortcode_custom_css_class( $css );
	}

	// Row classes.
	$row_classes = [
		'vcex-recent-news',
		'wpex-clr',
	];

	// Single column.
	if ( 1 === $grid_columns ) {

		if ( vcex_validate_boolean( $divider_remove_last ) ) {
			$row_classes[] = 'wpex-last-divider-none';
		}

	}

	// Multiple columns.
	else {
		$row_classes[] = 'wpex-row';
		if ( $columns_gap ) {
			$row_classes[] = 'gap-' . sanitize_html_class( $columns_gap );
		}
		$atts['columns'] = $grid_columns;
		$grid_columns_class = vcex_get_grid_column_class( $atts );

	}

	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_recent_news', $atts );

	/*-------------------------------------------*/
	/* [ Begin Output ]
	/*-------------------------------------------*/

	// Add wrapper (introduced in 4.8 for load more function).
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '">';

	// Output element.
	$output .= '<div class="' . esc_attr( implode( ' ', $row_classes ) ) . '"' . vcex_get_unique_id( $atts ) . '>';

		/*-------------------------------------------*/
		/* [ Header ]
		/*-------------------------------------------*/
		if ( $header ) {
			$output .= vcex_get_module_header( [
				'style'   => $header_style,
				'content' => $header,
				'classes' => [
					'vcex-recent-news-header',
					'vcex-module-heading',
				],
			] );
		}

		// Loop through posts.
		$total_count = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) :

			// Get post from query.
			$vcex_query->the_post();

			// Add to counters.
			$entry_count++;
			$total_count++;

			// Create new post object.
			$post = new stdClass();

			// Post vars
			$post->ID                  = get_the_ID();
			$post->permalink           = vcex_get_permalink( $post->ID );
			$post->the_title           = get_the_title( $post->ID );
			$post->the_title_escaped   = esc_attr( the_title_attribute( 'echo=0' ) );
			$post->type                = get_post_type( $post->ID );
			$post->video_embed_escaped = vcex_get_post_video_html();
			$post->format              = get_post_format( $post->ID );

			/*-------------------------------------------*/
			/* [ Open entry wrap ]
			/*-------------------------------------------*/

			if ( $first_run ) {

				$entry_wrap_classes = [
					'vcex-recent-news-entry-wrap',
					'vcex-grid-item',
				];

				if ( $grid_columns > 1 ) {
					$entry_wrap_classes[] = 'col';
					$entry_wrap_classes[] = $grid_columns_class;
				}

			}

			$entry_wrap_classes[ 'count' ] = 'col-' . sanitize_html_class( $entry_count );


			$output .= '<div class="' . esc_attr( implode( ' ', $entry_wrap_classes ) ) . '">';

			/*-------------------------------------------*/
			/* [ Open entry ]
			/*-------------------------------------------*/
			if ( $first_run ) {
				$entry_classes = [
					'vcex-recent-news-entry',
					'wpex-flex',
				];
				if ( $css_animation_class = vcex_get_css_animation( $css_animation ) ) {
					$entry_classes[] = $css_animation_class;
				}
			}

			$output .= '<article ' . vcex_get_post_class( $entry_classes, $post->ID ) . '>';

				/*-------------------------------------------*/
				/* [ Date ]
				/*-------------------------------------------*/
				if ( $has_date ) {

					$date_output = '';

					if ( $first_run ) {
						$date_side_margin = $date_side_margin ? absint( $date_side_margin ) : '20';
						$date_class = apply_filters( 'vcex_recent_news_date_class', [
							'vcex-recent-news-date',
							'wpex-mr-' . sanitize_html_class( $date_side_margin ),
							'wpex-flex-shrink-0',
							'wpex-text-center',
						] );
					}

					$date_output .= '<div class="' . esc_attr( implode( ' ', $date_class ) ) . '">';

						// Display date day.
						if ( $first_run ) {

							$date_day_class = apply_filters( 'vcex_recent_news_date_day_class', [
								'vcex-recent-news-date__day',
								'wpex-block',
								'wpex-p-5',
								'wpex-px-10',
								'wpex-surface-2',
								'wpex-text-2',
								'wpex-text-2xl',
								'wpex-border',
								'wpex-border-b-0',
								'wpex-border-solid',
								'wpex-border-surface-3',
								'wpex-font-light',
								'wpex-leading-relaxed',
								'day',
							] );

							$day_format = $day_format ? sanitize_text_field( $day_format ) : 'd';

						}

						$date_output .= '<span class="' . esc_attr( implode( ' ', $date_day_class ) ) . '">';

							if ( 'tribe_events' === $post->type && function_exists( 'tribe_get_start_date' ) ) {
								$day = tribe_get_start_date( $post->ID, false, $day_format );
							} else {
								$day = get_the_time( $day_format, $post->ID );
							}

							// Apply filters and return date.
							$date_output .= apply_filters( 'vcex_recent_news_day_output', $day );

						$date_output .= '</span>';

						// Display date month.
						if ( $first_run ) {

							$date_month_class = apply_filters( 'vcex_recent_news_date_month_class', [
								'vcex-recent-news-date__month',
								'wpex-block',
								'wpex-text-xs',
								'wpex-py-5',
								'wpex-px-10',
								'wpex-bg-accent',
								'wpex-uppercase',
								'wpex-leading-none',
								'month',
							] );

							$month_year_format = $month_year_format ? sanitize_text_field( $month_year_format ) : 'M y';

						}

						$date_output .= '<span class="' . esc_attr( implode( ' ', $date_month_class ) ) . '">';

							if ( 'tribe_events' === $post->type && function_exists( 'tribe_get_start_date' ) ) {
								$month_year = tribe_get_start_date( $post->ID, false, $month_year_format );
							} else {
								$month_year = get_the_time( $month_year_format, $post->ID );
							}

							// Echo the month/year.
							$date_output .= apply_filters( 'vcex_recent_news_month_year_output', $month_year );

						// Close month.
						$date_output .= '</span>';

					$date_output .= '</div>';

					$output .= apply_filters( 'vcex_recent_news_date', $date_output, $atts );

				}

				/*-------------------------------------------*/
				/* [ Details ]
				/*-------------------------------------------*/
				$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( [ 'vcex-news-entry-details wpex-flex-grow' ], 'vcex_recent_news', $atts ) ) ) . '">';

					/*-------------------------------------------*/
					/* [ Media ]
					/*-------------------------------------------*/
					$media_output = '';
					if ( $show_thumbnail ) {

						/*-------------------------------------------*/
						/* [ Video ]
						/*-------------------------------------------*/
						if ( $show_videos && $post->video_embed_escaped ) {

							$atts['media_type'] = 'video';

							$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'vcex-news-entry-video' ), 'vcex_recent_news', $atts ) ) ) . '">' . $post->video_embed_escaped . '</div>';

						}

						/*-------------------------------------------*/
						/* [ Featured Image ]
						/*-------------------------------------------*/
						elseif ( has_post_thumbnail( $post->ID ) ) {

							$atts['media_type'] = 'thumbnail';

							$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'vcex-news-entry-thumbnail' ), 'vcex_recent_news', $atts ) ) ) . '">';

								$media_output .= '<a href="' . esc_url( $post->permalink ) . '" title="' . vcex_esc_title() . '">';

									// Thumbnail args
									$thumbnail_class = implode( ' ' , vcex_get_entry_thumbnail_class(
										null,
										'vcex_recent_news',
										$atts
									) );

									// Display thumbnail
									$media_output .= vcex_get_post_thumbnail( array(
										'size'          => $img_size,
										'crop'          => $img_crop,
										'width'         => $img_width,
										'height'        => $img_height,
										'class'         => $thumbnail_class,
										'apply_filters' => 'vcex_recent_news_thumbnail_args',
										'filter_arg1'   => $atts,
									) );

									$media_output .= vcex_get_entry_media_after( 'vcex_recent_news' );

									// Inner overlay.
									$media_output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_recent_news', $atts );

								$media_output .= '</a>';

								// Outer link overlay HTML.
								$media_output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_recent_news', $atts );

							$media_output .= '</div>';

						} // End thumbnail check.

					} // End featured image check.

					$output .= apply_filters( 'vcex_recent_news_media', $media_output, $atts );

					/*-------------------------------------------*/
					/* [ Categories ]
					/*-------------------------------------------*/
					if ( $show_categories ) {

						$categories_output = '';
						$get_categories    = '';

						// Generate inline CSS for categories but we only need to do this 1x
						if ( $first_run ) {
							$categories_class = apply_filters( 'vcex_recent_news_categories_class', [
								'vcex-recent-news-entry-categories',
								'entry-categories',
								'wpex-mb-5',
								'wpex-text-xs',
								'wpex-text-3',
								'wpex-uppercase',
								'wpex-child-inherit-color',
								'wpex-clr',
							] );
						}

						if ( vcex_validate_att_boolean( 'show_first_category_only', $atts, false ) ) {
							if ( vcex_validate_att_boolean( 'categories_links', $atts, true ) ) {
								$get_categories = vcex_get_first_term( $post->ID, $categories_taxonomy );
							} else {
								$get_categories = vcex_get_first_term_link( $post->ID, $categories_taxonomy );
							}
						} else {
							$get_categories = vcex_get_list_post_terms( $categories_taxonomy, vcex_validate_boolean( $categories_links ) );
						}

						$get_categories = apply_filters( 'vcex_post_type_grid_get_categories', $get_categories, $atts );

						if ( $get_categories ) {
							$categories_output .= '<div class="' . esc_attr( implode( ' ', $categories_class ) ) . '">';
								$categories_output .= $get_categories; // already sanitized.
							$categories_output .= '</div>';
						}

						$output .= apply_filters( 'vcex_recent_news_categories', $categories_output, $atts );

					}

					/*-------------------------------------------*/
					/* [ Title ]
					/*-------------------------------------------*/
					if ( $has_title ) {

						$title_output = '';

						if ( $first_run ) {

							$title_tag_escaped = $title_tag ? tag_escape( $title_tag ) : apply_filters( 'vcex_recent_news_default_title_tag', 'h2', $atts );

							$title_class = array(
								'vcex-recent-news-entry-title-heading',
							);

							if ( 1 === $grid_columns ) {
								$title_class[] = 'wpex-text-lg';
							}

						}

						$title_output .= '<header class="vcex-recent-news-entry-title">';

							$title_output .= '<' . $title_tag_escaped . ' class="' . esc_attr( implode( ' ', vcex_get_entry_title_class( $title_class, 'vcex_recent_news', $atts ) ) ) . '">';

								$title_output .= '<a href="' . esc_url( $post->permalink ) . '">' . wp_kses_post( $post->the_title ) . '</a>';

							$title_output .= '</' . $title_tag_escaped . '>';

						$title_output .= '</header>';

						$output .= apply_filters( 'vcex_recent_news_title', $title_output, $atts );

					} // End title check.

					/*-------------------------------------------*/
					/* [ Excerpt & Read More ]
					/*-------------------------------------------*/
					if ( $has_excerpt || $has_read_more ) {

							/*-------------------------------------------*/
							/* [ Excerpt ]
							/*-------------------------------------------*/
							if ( $has_excerpt ) {

								$excerpt_output = '';

								$excerpt_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( array( 'vcex-recent-news-entry-excerpt' ), 'vcex_recent_news', $atts ) ) ) . '">';

									// Output excerpt
									$excerpt_output .= vcex_get_excerpt( array(
										'length'  => $excerpt_length,
										'context' => 'vcex_recent_news',
									) );

								$excerpt_output .= '</div>';

								$output .= apply_filters( 'vcex_recent_news_excerpt', $excerpt_output, $atts );

							}

							/*-------------------------------------------*/
							/* [ Read More ]
							/*-------------------------------------------*/
							if ( $has_read_more ) {

								$read_more_output = '';

								if ( $first_run ) {
									// Readmore text.
									$read_more_text = $read_more_text ?: esc_html__( 'Read more', 'total-theme-core' );

									// Readmore classes.
									$readmore_classes = [
										'vcex-recent-news-entry-readmore',
									];
									$readmore_classes_xtra = vcex_get_button_classes( $readmore_style, $readmore_style_color );
									if ( $readmore_classes_xtra ) {
										$readmore_classes[] = $readmore_classes_xtra;
									}
								}

								$read_more_output .= '<div class="vcex-recent-news-entry-readmore-wrap entry-readmore-wrap wpex-clr">';

									$read_more_output .= '<a href="' . esc_url( $post->permalink ) . '" class="' . esc_attr( implode( ' ', $readmore_classes ) ) . '">';

										$read_more_output .= vcex_parse_text_safe( $read_more_text );

										if ( vcex_validate_att_boolean( 'readmore_rarr', $atts, false ) ) {
											$read_more_output .= ' <span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
										}

									$read_more_output .= '</a>';

								$read_more_output .= '</div>';

								$output .= apply_filters( 'vcex_recent_news_read_more', $read_more_output, $atts );

							} // End readmore text.

					} // End excerpt + readmore.

				$output .= '</div>';

			$output .= '</article>';


			// Add entry divider.
			if ( 1 === $grid_columns ) {

				if ( $first_run ) {

					$divider_margin = $divider_margin ? absint( $divider_margin ) : 20;

					$divider_class = [
						'vcex-recent-news-entry__divider',
						'wpex-divider',
						'wpex-my-' . sanitize_html_class( $divider_margin ),
					];

					if ( $divider_style && 'solid' !== $divider_style ) {
						$divider_class[] = 'wpex-divider-' . sanitize_html_class( $divider_style );
					}

					if ( $divider_width = absint( $divider_width ) ) {
						if ( 1 == $divider_width ) {
							$divider_class[] = 'wpex-border-b';
						} else {
							$divider_class[] = 'wpex-border-b-' . sanitize_html_class( $divider_width );
						}
					}

					$divider_class = implode( ' ', $divider_class );
				}

				$output .= '<div class="' . esc_attr( $divider_class ) . '"></div>';
			}

			$output .= '</div>'; // entry wrap close.

			if ( $entry_count == $grid_columns ) {
				$entry_count=0;
			}

		$first_run = false;
		$post = null;

	endwhile;

	// End vcex-module
	$output .= '</div>';

	/*-------------------------------------------*/
	/* [ Standard Pagination ]
	/*-------------------------------------------*/
	if ( ( 'true' == $atts['pagination'] || ( 'true' == $atts['custom_query'] && ! empty( $vcex_query->query['pagination'] ) ) )
		&& 'true' != $atts['pagination_loadmore']
	) {

		$output .= vcex_pagination( $vcex_query, false );

	}

	/*-------------------------------------------*/
	/* [ Load More ]
	/*-------------------------------------------*/
	if ( 'true' == $atts['pagination_loadmore'] && ! empty( $vcex_query->max_num_pages ) ) {
		vcex_loadmore_scripts();
		if ( isset( $atts ) ) {
			$atts['entry_count'] = $entry_count; // Update counter.
		}
		$output .= vcex_get_loadmore_button( 'vcex_recent_news', $atts, $vcex_query );
	}

	// Close wrap.
	$output .= '</div>';

	// Reset the post data to prevent conflicts with WP globals.
	wp_reset_postdata();

	// @codingStandardsIgnoreLine
	echo $output;

// If no posts are found display message.
else :

	// Display no posts found error if function exists.
	echo vcex_no_posts_found_message( $atts );

// End post check.
endif;
