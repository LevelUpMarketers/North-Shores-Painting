<?php

/**
 * vcex_blog_grid shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Define output var.
$output = '';

// Extract shortcode attributes.
extract( $atts );

// Define entry counter.
$entry_count = ! empty( $atts['entry_count'] ) ? absint( $atts['entry_count'] ) : 0;

// Add paged attribute for load more button (used for WP_Query).
if ( ! empty( $atts['paged'] ) ) {
	$atts['paged'] = $atts['paged'];
}

// Define static attributes.
$atts['post_type'] = 'post';
$atts['taxonomy']  = 'category';
$atts['tax_query'] = '';

// Build the WordPress query.
$vcex_query = vcex_build_wp_query( $atts, 'vcex_blog_grid' );

// Output posts
if ( $vcex_query && $vcex_query->have_posts() ) :

	// IMPORTANT: Fallback required from VC update when params are defined as empty.
	// AKA - set things to enabled by default.
	$atts['date']        = ( ! $date ) ? 'true' : $date;
	$atts['excerpt']     = ( ! $excerpt ) ? 'true' : $excerpt;
	$atts['read_more']   = ( ! $read_more ) ? 'true' : $read_more;

	// Sanitize & declare variables.
	$wrap_class         = [ 'vcex-module', 'vcex-blog-grid-wrap', 'wpex-clr' ];
	$grid_classes       = [ 'wpex-row', 'vcex-blog-grid', 'wpex-clr', 'entries' ];
	$grid_data          = [];
	$is_isotope         = false;
	$has_filter         = vcex_validate_boolean( $filter );
	$css_animation      = vcex_get_css_animation( $css_animation );
	$css_animation      = $has_filter ? false : $css_animation;
	$equal_heights_grid = ( 'true' == $equal_heights_grid && $columns > '1' ) ? true : false;

	// Bottom Margin.
	if ( ! empty( $atts['bottom_margin'] ) ) {
		$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
	}

	// Wrap classes.
	if ( $visibility ) {
		$wrap_class[] = vcex_parse_visibility_class( $visibility );
	}

	if ( $classes ) {
		$wrap_class[] = vcex_get_extra_class( $classes );
	}

	// Get title tag.
	$title_tag_escaped = $title_tag ? tag_escape( $title_tag ) : apply_filters( 'vcex_grid_default_title_tag', 'h2', $atts );

	// Get link target.
	if ( $url_target ) {
		$atts['link_target'] = $url_target;
	}

	// Load lightbox script.
	if ( 'lightbox' == $thumb_link ) {
		vcex_enqueue_lightbox_scripts();
	}

	// Enable Isotope?
	if ( $has_filter || 'masonry' === $grid_style ) {
		$is_isotope = true;
		vcex_enqueue_isotope_scripts();
	}

	// Get filter taxonomy.
	if ( $has_filter ) {

		/**
		 * Filters the taxonomy used for the filter.
		 *
		 * @param string $taxonomy.
		 */
		$filter_taxonomy = (string) apply_filters( 'vcex_filter_taxonomy', $atts['taxonomy'], $atts );

		if ( ! taxonomy_exists( $filter_taxonomy ) ) {
			$filter_taxonomy = '';
		}

		if ( $filter_taxonomy ) {
			$atts['filter_taxonomy'] = $filter_taxonomy; // Add to array to pass on to vcex_grid_filter_args()
		}

	} else {
		$filter_taxonomy = null;
	}

	// Get filter terms.
	if ( $filter_taxonomy ) {

		// Get filter terms.
		$filter_terms = get_terms( $filter_taxonomy, vcex_grid_filter_args( $atts, $vcex_query ) );

		// If terms are found.
		if ( $filter_terms ) {

			// Check url for filter cat.
			if ( $active_cat_query_arg = vcex_grid_filter_get_active_item( $filter_taxonomy ) ) {
				$filter_active_category = $active_cat_query_arg;
			}

			// Check if filter active cat exists on current page.
			$filter_has_active_cat = in_array( $filter_active_category, wp_list_pluck( $filter_terms, 'term_id' ) ) ? true : false;

			// Add show on load animation when active filter is enabled to prevent double animation.
			if ( $filter_has_active_cat ) {
				$grid_classes[] = 'wpex-show-on-load';
			}

		} else {
			$has_filter = false;
		}

	}

	// Columns class.
	$columns_class = vcex_get_grid_column_class( $atts );

	// Grid classes.
	if ( $columns_gap ) {
		$grid_classes[] = 'gap-' . sanitize_html_class( $columns_gap );
	}

	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
		$grid_classes[] = 'wpex-overflow-hidden';
	}

	if ( isset( $atts['columns'] )
		&& 1 === (int) $atts['columns']
		&& isset( $atts['single_column_style'] )
		&& 'left_thumbs' == $atts['single_column_style']
	) {
		$grid_classes[] = 'left-thumbs';
	}

	if ( $equal_heights_grid ) {
		$grid_classes[] = 'match-height-grid';
	}

	// Grid data attributes.
	if ( $has_filter ) {
		if ( 'fitRows' === $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="fitRows"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $filter_speed ) . '"';
		}
		if ( $filter_has_active_cat ) {
			$grid_data[] = 'data-filter=".cat-' . esc_attr( $filter_active_category ) . '"';
		}
	} else {

		$isotope_transition_duration = apply_filters( 'vcex_isotope_transition_duration', null, 'vcex_blog_grid' );
		if ( $isotope_transition_duration ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $isotope_transition ) . '"';
		}

	}

	// Apply filters.
	$wrap_class   = apply_filters( 'vcex_blog_grid_wrap_class', $wrap_class ); // @todo deprecated
	$grid_classes = apply_filters( 'vcex_blog_grid_classes', $grid_classes );
	$grid_data    = apply_filters( 'vcex_blog_grid_data_attr', $grid_data );

	// Convert arrays into strings.
	$wrap_class = implode( ' ', $wrap_class );
	$grid_classes = implode( ' ', $grid_classes );
	$grid_data    = $grid_data ? ' '. implode( ' ', $grid_data ) : '';

	// VC core filter.
	$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_blog_grid', $atts );

	/*** Begin Grid output ***/
	$output .= '<div class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Display header if enabled.
		if ( $header ) {

			$output .= vcex_get_module_header( array(
				'style'   => $header_style,
				'content' => $header,
				'classes' => array( 'vcex-module-heading vcex_blog_grid-heading' ),
			) );

		}

		/*** Display Filter Links ***/
		if ( $filter_taxonomy && ! empty( $filter_terms ) ) :
			$all_text              = $all_text ?: esc_html__( 'All', 'total-theme-core' );
			$filter_button_classes = vcex_get_button_classes( $filter_button_style, $filter_button_color );
			$has_filter_all_link   = vcex_validate_att_boolean( 'filter_all_link', $atts, true );
			$filter_select_bk      = ! empty( $atts['filter_select_bk'] ) ? $atts['filter_select_bk'] : null;
			$has_filter_select     = $filter_select_bk ? true : false;

			$filter_classes = 'vcex-blog-filter vcex-filter-links wpex-flex-wrap wpex-list-none wpex-m-0';
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
				$filter_select_class = "vcex-blog-filter-select vcex-filter-links-select wpex-{$filter_select_bk_safe}-hidden wpex-select-wrap";
				$filter_select_class .= vcex_has_classic_styles() ? ' wpex-mb-25' : ' wpex-mb-30';

				$output .= '<div class="' . esc_attr( $filter_select_class ) . '">';

					$output .= '<select>';

						$mobile_select_all_text = $has_filter_all_link ? $all_text : esc_html__( 'Filter', 'total-theme-core' );

						$output .= '<option value="*">' . esc_html( $mobile_select_all_text ) . '</option>';

						foreach ( $filter_terms as $term ) {
							$selected = '';
							if ( ! empty( $atts['filter_active_category'] ) && $atts['filter_active_category'] == $term->term_id ) {
								$selected = ' selected';
							}
							$output .= '<option value=".cat-' . absint( $term->term_id ) . '"' . $selected . '>' . esc_html( $term->name ) . '</option>';
						}

					$output .= '</select>';

				if ( is_callable( array( 'TotalTheme\Forms\Select_Wrap', 'arrow' ) ) ) {
					ob_start();
						TotalTheme\Forms\Select_Wrap::arrow();
					$output .= ob_get_clean();
				}

				$output .= '</div>';

			}

			// Filter Buttons.
			$output .= '<ul class="' . esc_attr( $filter_classes ) . '">';

				if ( $has_filter_all_link ) {

					$output .= '<li';
						if ( ! $filter_has_active_cat ) {
							$output .= ' class="active"';
						}
					$output .= '>';

						$output .= '<a href="#" data-filter="*" class="' . esc_attr( $filter_button_classes ) . '"><span>' . esc_html( $all_text ) . '</span></a>';

					$output .= '</li>';

				}

				foreach ( $filter_terms as $term ) {

					$output .= '<li class="filter-cat-' . absint( $term->term_id );

						if ( $filter_active_category == $term->term_id ) {
							$output .= ' active';
						}

					$output .= '">';

					$output .= '<a href="#" data-filter=".cat-' . absint( $term->term_id ) . '" class="' . esc_attr( $filter_button_classes ) . '">';

						$output .= esc_html( $term->name );

					$output .= '</a></li>';

				}

				if ( $vcex_after_grid_filter = apply_filters( 'vcex_after_grid_filter', '', $atts ) ) {
					$output .= $vcex_after_grid_filter;
				}

			$output .= '</ul>';

		endif; // End filter links check.

		/*--------------------------------*/
		/* [ Begin Entry output ]
		/*--------------------------------*/
		$output .= '<div class="' . esc_attr( $grid_classes ) . '"' . $grid_data . '>';

			// Start loop.
			$first_run = true;
			while ( $vcex_query->have_posts() ) :

				// Get post from query.
				$vcex_query->the_post();

				// Post Data.
				$atts['post_id']           = get_the_ID();
				$atts['post_title']        = get_the_title();
				$atts['post_esc_title']    = vcex_esc_title();
				$atts['post_permalink']    = vcex_get_permalink( $atts['post_id'] );
				$atts['post_format']       = get_post_format( $atts['post_id'] );
				$atts['post_video']        = '';
				$atts['post_video_oembed'] = '';

				// Counter.
				$entry_count++;

				// Get video.
				if ( 'video' == $atts['post_format'] ) {
					$atts['post_video']        = vcex_get_post_video( $atts['post_id'] );
					$atts['post_video_oembed'] = $atts['post_video'] ? vcex_get_post_video_html( $atts['post_video'] ) : '';
				}

				// Apply filters to attributes.
				$latts = (array) apply_filters( 'vcex_shortcode_loop_atts', $atts, 'vcex_blog_grid' );

				// Check to see what's enabled.
				$show_title     = vcex_validate_att_boolean( 'title', $latts, true );
				$show_date      = vcex_validate_att_boolean( 'date', $latts, true );
				$show_excerpt   = vcex_validate_att_boolean( 'excerpt', $latts, true );
				$show_read_more = vcex_validate_att_boolean( 'read_more', $latts, true );

				// Get excerpt.
				if ( $show_excerpt && ! isset( $latts['post_excerpt'] ) ) {
					$latts['post_excerpt'] = vcex_get_excerpt( [
						'length'  => $excerpt_length,
						'context' => 'vcex_blog_grid',
					] );
				}

				// Does entry have details?
				if ( $show_title || $show_date || ( $show_excerpt && ! empty( $latts['post_excerpt'] ) ) || $show_read_more ) {
					$entry_has_details = true;
				} else {
					$entry_has_details = false;
				}

				// Entry Classes.
				$entry_classes = [
					'vcex-blog-entry',
					'vcex-grid-item'
				];

				if ( $entry_has_details ) {
					$entry_classes[] = 'entry-has-details';
				}

				$entry_classes[] = $columns_class;
				$entry_classes[] = 'col-' . sanitize_html_class( $entry_count );

				if ( 'false' == $columns_responsive ) {
					$entry_classes[] = 'nr-col';
				} else {
					$entry_classes[] = 'col';
				}

				if ( $is_isotope ) {
					$entry_classes[] = 'vcex-isotope-entry';
				}

				if ( $css_animation ) {
					$entry_classes[] = $css_animation;
				}

				if ( $filter_taxonomy ) {
					if ( $post_terms = get_the_terms( $latts['post_id'], $filter_taxonomy ) ) {
						foreach ( $post_terms as $post_term ) {
							$entry_classes[] = 'cat-' . sanitize_html_class( $post_term->term_id );
						}
					}
				}

				if ( $content_alignment ) {
					$entry_classes[] = vcex_parse_text_align_class( $content_alignment );
				}

				// Begin entry output.
				$output .= '<div ' . vcex_grid_get_post_class( $entry_classes, $latts['post_id'] ) . '>';

					$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_inner_class( array( 'vcex-blog-entry-inner' ), 'vcex_blog_grid', $latts ) ) ) . '">';

						/*** Display Featured Image/Video ***/
						if ( vcex_validate_att_boolean( 'entry_media', $latts, true ) ) {

							$media_output = '';

							// Display post video if defined and is video format.
							if ( 'true' == $featured_video && ! empty( $latts['post_video_oembed'] ) ) {

								$latts['media_type'] = 'video';

								$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'vcex-blog-entry-media' ), 'vcex_blog_grid', $latts ) ) ) . '">';

									$media_output .= $latts['post_video_oembed'];

								$media_output .= '</div>';

							}

							// Display Featured Image.
							elseif ( has_post_thumbnail( $latts['post_id'] ) ) {

								$latts[ 'media_type' ] = 'thumbnail';

								$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'vcex-blog-entry-media' ), 'vcex_blog_grid', $latts ) ) ) . '">';

									// Lightbox Links.
									if ( 'lightbox' === $thumb_link ) {

										if ( 'video' == $latts['post_format'] ) {
											$embed_url = vcex_get_video_embed_url( $latts['post_video'] );
											$latts['lightbox_link'] = $embed_url ?: $latts['post_video'];
										} else {
											$latts['lightbox_link'] = vcex_get_lightbox_image();
										}

										$link_attrs = array(
											'href'  => $latts['lightbox_link'],
											'title' => $latts['post_esc_title'], //@todo should this be the image alt?
											'class' => 'wpex-lightbox',
										);

									}

									// Standard no lightbox.
									elseif ( 'nowhere' !== $thumb_link ) {

										$link_attrs = array(
											'href'   => esc_url( $latts['post_permalink'] ),
											'title'  => $latts['post_esc_title'],
											'target' => $latts['link_target'],
										);

									}

									// Open Link.
									if ( 'nowhere' !== $thumb_link && isset( $link_attrs ) ) {
										$media_output .= '<a' . vcex_parse_html_attributes( $link_attrs ) .'>';
									}

									// Get thumbnail class.
									$thumbnail_class = implode( ' ' , vcex_get_entry_thumbnail_class(
										array( 'vcex-blog-entry-img' ),
										'vcex_blog_grid',
										$latts
									) );

									// Define thumbnail args.
									$thumbnail_args = array(
										'size'          => $img_size,
										'width'         => $img_width,
										'height'        => $img_height,
										'crop'          => $img_crop,
										'class'         => $thumbnail_class,
										'apply_filters' => 'vcex_blog_grid_thumbnail_args',
										'filter_arg1'   => $latts,
									);

									// Disable lazy loading.
									if ( $is_isotope ) {
										$thumbnail_args['lazy'] = false;
									}

									// Display thumbnail.
									$media_output .= vcex_get_post_thumbnail( $thumbnail_args );

									// Inner link overlay HTML.
									$media_output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_blog_grid', $latts );

									// Entry after media hook.
									$media_output .= vcex_get_entry_media_after( 'vcex_blog_grid' );

									// Close link tag.
									if ( 'nowhere' != $thumb_link ) {
										$media_output .= '</a>';
									}

									// Outer link overlay HTML.
									$media_output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_blog_grid', $latts );

								$media_output .= '</div>';

							} // Video/thumbnail checks.

							$output .= apply_filters( 'vcex_blog_grid_media', $media_output, $latts );

						}

						/*** Display Entry Details ***/
						if ( $entry_has_details ) {

							if ( $first_run ) {
								$content_style = '';
								if ( empty( $atts['content_css'] ) ) {
									$content_style = vcex_inline_style( [
										'background' => $atts['content_background'] ?? '',
										'padding'    => $atts['content_padding'] ?? '',
										'margin'     => $atts['content_margin'] ?? '',
										'border'     => $atts['content_border'] ?? '',
									] );
								}
							}

							$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( array( 'vcex-blog-entry-details' ), 'vcex_blog_grid', $latts ) ) ) . '"' . $content_style . '>';

								// Open equal heights div if equal heights is enabled.
								if ( $equal_heights_grid ) {
									$output .= '<div class="match-height-content">';
								}

								/*** Display Title ***/
								if ( $show_title ) {

									$title_output = '<' . $title_tag_escaped . ' class="' . esc_attr( implode( ' ', vcex_get_entry_title_class( [ 'vcex-blog-entry-title' ], 'vcex_blog_grid', $latts ) ) ) . '">';

										$title_output .= '<a' . vcex_parse_html_attributes( [
											'href'   => $latts['post_permalink'],
											'target' => $url_target,
										] ) . '>';

											$title_output .= vcex_parse_text_safe( $latts['post_title'] );

										$title_output .= '</a>';

									$title_output .= '</' . $title_tag_escaped . '>';

									$output .= apply_filters( 'vcex_blog_grid_title', $title_output, $latts );

								}

								/*** Display Date ***/
								if ( $show_date ) {
									$date_output = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_date_class( [ 'vcex-blog-entry-date' ], 'vcex_blog_grid', $latts ) ) ) . '">';
										$date_output .= get_the_date();
									$date_output .= '</div>';
									$output .= apply_filters( 'vcex_blog_grid_date', $date_output, $latts );
								}

								/*** Display Excerpt ***/
								if ( $show_excerpt && ! empty( $latts['post_excerpt'] ) ) {
									$excerpt_output = '';
									if ( '-1' === $excerpt_length ) {
										$excerpt_output .= vcex_wpb_shortcodes_custom_css( $latts['post_id'] );
									}
									$excerpt_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( [ 'vcex-blog-entry-excerpt' ], 'vcex_blog_grid', $latts ) ) ) . '">';
										$excerpt_output .= $latts['post_excerpt'];
									$excerpt_output .= '</div>';
									$output .= apply_filters( 'vcex_blog_grid_excerpt', $excerpt_output, $latts );
								}

								/*** Display Readmore button ***/
								if ( $show_read_more ) {

									if ( $first_run ) {
										$read_more_text = $latts['read_more_text'] ?: esc_html__( 'Read more', 'total-theme-core' );
										$readmore_classes = vcex_get_button_classes( $readmore_style, $readmore_style_color );
									}

									$readmore_output = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_button_wrap_class( array( 'vcex-blog-entry-readmore-wrap' ), 'vcex_blog_grid', $latts ) ) ) . '">';

										$readmore_attrs = [
											'href'   => esc_url( $latts['post_permalink'] ),
											'class'  => "entry-readmore {$readmore_classes}",
											'target' => $url_target,
										];

										$aria_label = sprintf( esc_attr_x( '%s about %s', '*read more text* about *post name* aria label', 'total-theme-core' ), $read_more_text, $latts['post_esc_title'] );

										/**
										 * Filters the more_link aria label.
										 *
										 * @param string $aria_label
										 */
										$aria_label = apply_filters( 'wpex_aria_label', $aria_label, 'more_link' );
										$aria_label = apply_filters( 'vcex_blog_grid_readmore_aria_label', $aria_label, $latts );

										if ( $aria_label ) {
											$readmore_attrs['aria-label'] = strip_shortcodes( $aria_label );
										}

										$readmore_output .= '<a' . vcex_parse_html_attributes( $readmore_attrs ) . '>';

											$readmore_output .= $read_more_text;

											if ( 'true' == $readmore_rarr ) {
												$readmore_output .= ' <span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
											}

										$readmore_output .= '</a>';

									$readmore_output .= '</div>';

									$output .= apply_filters( 'vcex_blog_grid_readmore', $readmore_output, $latts );

								}

								// Close equal heights div if equal heights is enabled.
								if ( $equal_heights_grid ) {
									$output .= '</div>';
								}

							$output .= '</div>';

						} // End details check.

					$output .= '</div>'; // Close entry inner

				$output .= '</div>'; // Close entry

			// Reset entry counter.
			if ( $entry_count === absint( $columns ) ) {
				$entry_count = 0;
			}

			$first_run = false;

			endwhile; // End main loop.

		$output .= '</div>';

		/*** Pagination ***/

		// Load more button.
		if ( 'true' == $atts['pagination_loadmore'] ) {
			if ( ! empty( $vcex_query->max_num_pages ) ) {
				vcex_loadmore_scripts();
				$atts['entry_count'] = $entry_count; // Update counter.
				$output .= vcex_get_loadmore_button( 'vcex_blog_grid', $atts, $vcex_query );
			}
		}

		// Standard pagination.
		elseif (
			'true' == $atts['pagination']
			|| ( 'true' == $atts['custom_query'] && ! empty( $vcex_query->query['pagination'] ) )
		) {
			$output .= vcex_pagination( $vcex_query, false );
		}

	$output .= '</div>';

	// Reset the post data to prevent conflicts with WP globals.
	wp_reset_postdata();

	// @codingStandardsIgnoreLine
	echo $output;


// If no posts are found display message.
else :

	// Display no posts found error if function exists.
	echo vcex_no_posts_found_message( $atts );

// End post check
endif;
