<?php

/**
 * vcex_staff_grid shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

// Define output var
$output = '';

// Define entry counter
$entry_count = ! empty( $atts['entry_count'] ) ? $atts['entry_count'] : 0;

// Extract shortcode atts
extract( $atts );

// Add paged attribute for load more button (used for WP_Query)
if ( ! empty( $atts['paged'] ) ) {
	$atts['paged'] = $atts['paged'];
}

// Define user-generated attributes
$atts['post_type'] = 'staff';
$atts['taxonomy']  = 'staff_category';
$atts['tax_query'] = '';

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts, 'vcex_staff_grid' );

// Output posts
if ( $vcex_query && $vcex_query->have_posts() ) :

	// IMPORTANT: Fallback required from VC update when params were defined as empty
	$atts['entry_media'] = ( ! $entry_media ) ? 'true' : $entry_media;
	$atts['title']       = ( ! $title ) ? 'true' : $title;
	$atts['excerpt']     = ( ! $excerpt ) ? 'true' : $excerpt;
	$atts['read_more']   = ( ! $read_more ) ? 'true' : $read_more;

	// Sanitize data & declare main variables
	$wrap_classes       = array( 'vcex-module', 'vcex-staff-grid-wrap', 'wpex-clr' );
	$grid_classes       = array( 'wpex-row', 'vcex-staff-grid', 'entries', 'wpex-clr' );
	$grid_data          = array();
	$is_isotope         = false;
	$excerpt_length     = $excerpt_length ?: '30';
	$css_animation      = ( $css_animation && 'true' != $filter ) ? vcex_get_css_animation( $css_animation ) : false;
	$equal_heights_grid = ( 'true' == $equal_heights_grid && $columns > '1' ) ? true : false;

	// Load lightbox scripts
	if ( 'lightbox' == $thumb_link ) {
		vcex_enqueue_lightbox_scripts();
	}

	// Enable Isotope
	if ( 'true' == $filter || 'masonry' == $grid_style || 'no_margins' == $grid_style ) {
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

	// Get filter terms
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
			$filter = false; // No terms
		}

	}

	// Wrap classes
	if ( $visibility ) {
		$wrap_classes[] = vcex_parse_visibility_class( $visibility );
	}

	if ( $bottom_margin ) {
		$wrap_classes[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
	}

	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Grid classes
	if ( $columns_gap ) {
		$grid_classes[] = 'gap-' . sanitize_html_class( $columns_gap );
	}

	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
		$grid_classes[] = 'wpex-overflow-hidden';
	}

	if ( 'no_margins' == $grid_style ) {
		$grid_classes[] = 'vcex-no-margin-grid';
	}

	if ( 'left_thumbs' == $single_column_style ) {
		$grid_classes[] = 'left-thumbs';
	}

	if ( $equal_heights_grid ) {
		$grid_classes[] = 'match-height-grid';
	}

	if ( 'true' == $thumb_lightbox_gallery ) {
		$grid_classes[] = 'wpex-lightbox-group';
		$lightbox_single_class = ' wpex-lightbox-group-item';
	} else {
		$lightbox_single_class = ' wpex-lightbox';
	}

	// Grid data attributes when filter is enabled
	if ( 'true' == $filter ) {
		if ( 'fitRows' == $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="fitRows"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $filter_speed ) . '"';
		}
		if ( ! empty( $filter_has_active_cat ) ) {
			$grid_data[] = 'data-filter=".cat-' . esc_attr( $filter_active_category ) . '"';
		}
	} else {

		$isotope_transition_duration = apply_filters( 'vcex_isotope_transition_duration', null, 'vcex_staff_grid' );
		if ( $isotope_transition_duration ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $isotope_transition ) . '"';
		}

	}

	// Apply filters
	$wrap_classes  = apply_filters( 'vcex_staff_grid_wrap_classes', $wrap_classes ); // @todo deprecate?
	$grid_classes  = apply_filters( 'vcex_staff_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_staff_grid_data_attr', $grid_data );

	// Convert arrays into strings
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' '. implode( ' ', $grid_data ) : '';

	// VC filter
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_staff_grid', $atts );

	// Begin shortcode output
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Display header if enabled
		if ( $header ) {

			$output .= vcex_get_module_header( array(
				'style'   => $header_style,
				'content' => $header,
				'classes' => array( 'vcex-module-heading vcex_staff_grid-heading' ),
			) );

		}

		/*--------------------------------*/
		/* [ Filter Links ]
		/*--------------------------------*/
		if ( 'true' == $filter && ! empty( $filter_terms ) ) {

			// Filter settings.
			$all_text              = $all_text ?: esc_html__( 'All', 'total-theme-core' );
			$filter_button_classes = vcex_get_button_classes( $filter_button_style, $filter_button_color );
			$has_filter_all_link   = vcex_validate_att_boolean( 'filter_all_link', $atts, true );
			$filter_select_bk      = ! empty( $atts['filter_select_bk'] ) ? $atts['filter_select_bk'] : null;
			$has_filter_select     = $filter_select_bk ? true : false;

			// Filter font size
			$filter_css = vcex_inline_style( array(
				'font_size' => $filter_font_size,
			) );

			$filter_classes = 'vcex-staff-filter vcex-filter-links wpex-flex-wrap wpex-list-none wpex-m-0';
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
				$filter_select_class = "vcex-staff-filter-select vcex-filter-links-select wpex-{$filter_select_bk_safe}-hidden wpex-select-wrap";
				$filter_select_class .= vcex_has_classic_styles() ? ' wpex-mb-25' : ' wpex-mb-30';

				$output .= '<div class="' . esc_attr( $filter_select_class ) . '">';

					$output .= '<select>';

						$mobile_select_all_text = $has_filter_all_link ? $all_text : esc_html__( 'Filter', 'total-theme-core' );

						$output .= '<option value="*">' . esc_html( $mobile_select_all_text ) . '</option>';

						foreach ( $filter_terms as $term ) {
							$selected = '';
							if ( $filter_active_category && $filter_active_category == $term->term_id ) {
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
			$output .= '<ul class="' . esc_attr( $filter_classes ) . '"' . $filter_css . '>';

				if ( $has_filter_all_link ) {

					$output .= '<li';

						if ( ! $filter_has_active_cat ) {
							$output .= ' class="active"';
						}

					$output .= '>';

						$output .= '<a href="#" data-filter="*" class="' . esc_attr( $filter_button_classes ) . '"><span>' . esc_html( $all_text ) . '</span></a>';

					$output .= '</li>';

				}

				foreach ( $filter_terms as $term ) :

					$output .= '<li class="filter-cat-' . absint( $term->term_id );

						if ( $filter_active_category == $term->term_id ) {
							$output .= ' active';
						}

					$output .= '">';

					$output .= '<a href="#" data-filter=".cat-' . absint( $term->term_id ) . '" class="' . esc_attr( $filter_button_classes ) . '">';

						$output .= esc_html( $term->name );

					$output .= '</a></li>';

				endforeach;

				if ( $vcex_after_grid_filter = apply_filters( 'vcex_after_grid_filter', '', $atts ) ) {
					$output .= $vcex_after_grid_filter;
				}

			$output .= '</ul>';

		}

		/*--------------------------------*/
		/* [ Start Grid Output ]
		/*--------------------------------*/
		$output .= '<div class="' . esc_attr( $grid_classes ) . '"' . $grid_data . '>';

			// Start loop
			$first_run = true;
			while ( $vcex_query->have_posts() ) :

				// Get post from query
				$vcex_query->the_post();

				// Post Data
				$atts['post_id']        = get_the_ID();
				$atts['post_permalink'] = vcex_get_permalink( $atts['post_id'] );
				$atts['post_title']     = get_the_title();
				$atts['post_esc_title'] = vcex_esc_title();
				$atts['post_excerpt']   = '';

				// Generate post Excerpt
				if ( 'true' == $atts['excerpt'] || 'true' == $thumb_lightbox_caption ) {
					$atts['post_excerpt'] = vcex_get_excerpt( array(
						'length'  => $excerpt_length,
						'context' => 'vcex_staff_grid',
					) );
				}

				// Apply filters to attributes
				$latts = apply_filters( 'vcex_shortcode_loop_atts', $atts, 'vcex_staff_grid' );

				// Add to the counter var
				$entry_count++;

				// Add classes to the entries
				$entry_classes = array(
					'staff-entry',
					'vcex-grid-item',
				);

				$entry_classes[] = vcex_get_grid_column_class( $atts );

				if ( $content_alignment ) {
					$entry_classes[] = vcex_parse_text_align_class( $content_alignment );
				}

				if ( 'false' == $columns_responsive ) {
					$entry_classes[] = 'nr-col';
				} else {
					$entry_classes[] = 'col';
				}

				if ( $entry_count ) {
					$entry_classes[] = 'col-' . sanitize_html_class( $entry_count );
				}

				if ( 'true' == $latts['read_more'] ) {
					$entry_classes[] = 'has-readmore';
				}

				if ( $css_animation && 'none' != $css_animation ) {
					$entry_classes[] = $css_animation;
				}

				if ( $is_isotope ) {
					$entry_classes[] = 'vcex-isotope-entry';
				}

				if ( 'no_margins' == $grid_style ) {
					$entry_classes[] = 'vcex-no-margin-entry';
				}

				/*--------------------------------*/
				/* [ Entry output Start ]
				/*--------------------------------*/
				$output .= '<div ' . vcex_grid_get_post_class( $entry_classes, $latts['post_id'] ) . '>';

					$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_inner_class( array( 'staff-entry-inner' ), 'vcex_staff_grid', $latts ) ) ) . '">';

						/*--------------------------------*/
						/* [ Entry Media ]
						/*--------------------------------*/
						$media_output = '';
						if ( 'true' == $latts['entry_media'] && has_post_thumbnail() ) {

							$latts['media_type'] = 'thumbnail'; // so overlays can work

							$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'staff-entry-media' ), 'vcex_staff_grid', $latts ) ) ) . '">';

								// Thumbnail with link
								if ( ! in_array( $thumb_link, array( 'none', 'nowhere' ) ) ) {

									// Lightbox link
									if ( $thumb_link == 'lightbox' ) {

										// Lightbox data
										if ( 'lightbox' == $thumb_link ) {

											// Lightbox link
											$latts['lightbox_link'] = vcex_get_lightbox_image();

											// Lightbox data
											$latts['lightbox_data'] = array();
											if ( 'true' == $thumb_lightbox_title ) {
												$latts['lightbox_data'][] = 'data-title="' . esc_attr( $latts['post_esc_title'] ) . '"';
											} else {
												$latts['lightbox_data'][] = 'data-show_title="false"';
											}
											if ( 'true' == $thumb_lightbox_caption && $latts['post_excerpt'] ) {
												$latts['lightbox_data'][] = 'data-caption="' . esc_attr( str_replace( '"',"'", $latts['post_excerpt'] ) ) . '"';
											}
											$lightbox_data = ' ' . implode( ' ', $latts['lightbox_data'] );
										}

										$media_output .= '<a href="' . esc_url( $latts['lightbox_link'] ) . '" title="' . esc_attr( $latts['post_esc_title'] ) . '" class="staff-entry-media-link' . esc_attr( $lightbox_single_class ) . '"' . $lightbox_data . '>';

									// Standarad post link
									} else {

										$media_output .= '<a href="' . esc_url( $latts['post_permalink'] ) . '" title="' . esc_attr( $latts['post_esc_title'] ) . '" class="staff-entry-media-link"' . vcex_html( 'target_attr', $link_target ) . '>';

									}

								} // End link tag

									// Thumbnail class.
									$thumbnail_class = implode( ' ' , vcex_get_entry_thumbnail_class(
										array( 'staff-entry-media-img staff-entry-img wpex-align-middle' ),
										'vcex_staff_grid',
										$latts
									) );

									// Define thumbnail args.
									$thumbnail_args = array(
										'size'          => $img_size,
										'crop'          => $img_crop,
										'width'         => $img_width,
										'height'        => $img_height,
										'class'         => $thumbnail_class,
										'apply_filters' => 'vcex_staff_grid_thumbnail_args',
									);

									// Disable lazy loading.
									if ( $is_isotope ) {
										$thumbnail_args['lazy'] = false;
									}

									// Output post thumbnail.
									$media_output .= vcex_get_post_thumbnail( $thumbnail_args );

									// Inner Overlay
									$media_output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_staff_grid', $latts );

								// Close link.
								if ( ! in_array( $thumb_link, array( 'none', 'nowhere' ) ) ) {
									$media_output .= '</a>';
								}

								// After media output.
								$media_output .= vcex_get_entry_media_after( 'vcex_staff_grid' );

								// Outer link overlay HTML.
								$media_output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_staff_grid', $latts );

							$media_output .= '</div>';

							$output .= apply_filters( 'vcex_staff_grid_media', $media_output, $latts );

						}

						/*--------------------------------*/
						/* [ Entry Content ]
						/*--------------------------------*/
						if ( 'true' == $latts['title']
							|| 'true' == $latts['excerpt']
							|| 'true' == $latts['read_more']
							|| 'true' == $latts['position']
							|| 'true' == $latts['show_categories']
							|| 'true' == $latts['social_links']
						) {

							if ( $first_run ) {

								// Content Design
								$content_style = array(
									'color'            => $atts['content_color'],
									'opacity'          => $atts['content_opacity'],
									'background_color' => $atts['content_background_color'],
									'border_color'     => $atts['content_border_color'],
								);

								if ( empty( $content_css ) ) {

									if ( isset( $content_background ) ) {
										$content_style[ 'background' ] = $content_background;
									}

									if ( isset( $content_padding ) ) {
										$content_style[ 'padding' ] = $content_padding;
									}

									if ( isset( $content_margin ) ) {
										$content_style[ 'margin' ] = $content_margin;
									}

									if ( isset( $content_border ) ) {
										$content_style[ 'border' ] = $content_border;
									}

								}

								$content_style = vcex_inline_style( $content_style );

							}

							$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( array( 'staff-entry-details' ), 'vcex_staff_grid', $latts ) ) ) . '"' . $content_style .'>';

								// Open equal height container
								// Equal height div
								if ( $equal_heights_grid ) {
									$output .= '<div class="match-height-content">';
								}

								/*--------------------------------*/
								/* [ Entry Title ]
								/*--------------------------------*/
								$title_output = '';
								if ( 'true' == $latts['title'] ) {

									if ( $first_run ) {

										$title_tag_escaped = $latts['title_tag'] ? tag_escape( $latts['title_tag'] ) : apply_filters( 'vcex_grid_default_title_tag', 'h2', $latts );

										$heading_style = vcex_inline_style( array(
											'margin'         => $content_heading_margin,
											'font_size'      => $content_heading_size,
											'color'          => $content_heading_color,
											'font_weight'    => $content_heading_weight,
											'text_transform' => $content_heading_transform,
											'line_height'    => $content_heading_line_height,
										) );

									}

									// Open title tag
									$title_output .= '<' . $title_tag_escaped . ' class="' . esc_attr( implode( ' ', vcex_get_entry_title_class( array( 'staff-entry-title' ), 'vcex_staff_grid', $latts ) ) ) . '"' . $heading_style . '>';

										// Display title and link to post
										if ( 'post' == $title_link ) {

											$title_output .= '<a href="' . esc_attr( $latts['post_permalink'] ) . '"' . vcex_html( 'target_attr', $link_target ) . '>' . wp_kses_post( $latts['post_title'] ) . '</a>';

										// Display title and link to lightbox
										} elseif ( 'lightbox' == $title_link ) {

											// Load lightbox script
											vcex_enqueue_lightbox_scripts();

											$title_output .= '<a href="' . esc_url( vcex_get_lightbox_image() ) . '"' . $heading_link_style . ' class="wpex-lightbox">' . wp_kses_post( $latts['post_title'] ) . '</a>';

										// Display title without link
										} else {

											$title_output .= wp_kses_post( $latts['post_title'] );

										}

									// Close title tag
									$title_output .= '</' . $title_tag_escaped . '>';

									$output .= apply_filters( 'vcex_staff_grid_title', $title_output, $latts );

								}

								/*--------------------------------*/
								/* [ Entry Staff Position ]
								/*--------------------------------*/
								$position_output = '';
								if ( 'true' == $latts['position'] ) {
									if ( $first_run ) {
										$position_style = vcex_inline_style( array(
											'font_size'   => $position_size,
											'font_weight' => $position_weight, // @todo move to class
											'margin'      => $position_margin,
											'color'       => $position_color,
										) );
									}

									$get_position = get_post_meta( $latts['post_id'], 'wpex_staff_position', true );
									if ( $get_position ) {
										$position_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_staff_position_class( array( 'staff-entry-position' ), 'vcex_staff_grid', $atts ) ) ) . '"' . $position_style . '>';
											$position_output .= apply_filters( 'wpex_staff_entry_position', vcex_parse_text_safe( $get_position ) );
										$position_output .= '</div>';
									}
								}
								$output .= apply_filters( 'vcex_staff_grid_position', $position_output, $latts );

								/*--------------------------------*/
								/* [ Entry Categories ]
								/*--------------------------------*/
								$categories_output = $get_categories = '';
								if ( 'true' == $latts['show_categories'] ) {

									if ( $first_run ) {
										$categories_style = vcex_inline_style( array(
											'padding'   => $categories_margin,
											'font_size' => $categories_font_size,
											'color'     => $categories_color,
										) );
									}

									if ( 'true' == $show_first_category_only ) {

										if ( ! vcex_validate_boolean( $latts[ 'categories_links' ] ) ) {
											$get_categories = vcex_get_first_term( $latts['post_id'], 'staff_category' );
										} else {
											$get_categories = vcex_get_first_term_link( $latts['post_id'], 'staff_category' );
										}

									} else {
										$get_categories = vcex_get_list_post_terms( 'staff_category', vcex_validate_boolean( $latts[ 'categories_links' ] ) );
									}

									if ( $get_categories ) {

										$categories_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_categories_class( array( 'staff-entry-categories' ), 'vcex_staff_grid', $atts ) ) ) . '"' . $categories_style . '"' . $categories_style . '>';

											$categories_output .= $get_categories;

										$categories_output .= '</div>';

									}

								}

								$output .= apply_filters( 'vcex_staff_grid_categories', $categories_output, $latts );

								/*--------------------------------*/
								/* [ Entry Excerpt ]
								/*--------------------------------*/
								$excerpt_output = '';
								if ( 'true' == $latts['excerpt'] && $latts['post_excerpt'] ) {

									if ( empty( $excerpt_style ) ) {
										$excerpt_style = vcex_inline_style( array(
											'font_size' => $content_font_size,
										) );
									}

									$excerpt_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( array( 'staff-entry-excerpt' ), 'vcex_staff_grid', $latts ) ) ) . '"' . $excerpt_style . '>';

										$excerpt_output .= wp_kses_post( $latts['post_excerpt'] );

									$excerpt_output .= '</div>';

								}
								$output .= apply_filters( 'vcex_staff_grid_excerpt', $excerpt_output, $latts );

								/*--------------------------------*/
								/* [ Entry Social Links ]
								/*--------------------------------*/
								$social_output = '';
								if ( 'true' == $latts['social_links'] ) {

									if ( function_exists( 'wpex_get_staff_social' ) ) {

										if ( $first_run ) {
											$social_links_inline_css = vcex_inline_style( array(
												'margin' => $social_links_margin,
											) );
										}

										$social_output .= '<div class="staff-entry-social-links wpex-clr"' . $social_links_inline_css . '>';

											$social_output .= wpex_get_staff_social( array(
												'style'     => $social_links_style,
												'font_size' => $social_links_size,
											) );

										$social_output .= '</div>';

									}

								}
								$output .= apply_filters( 'vcex_staff_grid_social', $social_output, $latts );

								/*--------------------------------*/
								/* [ Entry ReadMore ]
								/*--------------------------------*/
								$readmore_output = '';
								if ( 'true' == $latts['read_more'] ) {

									if ( $first_run ) {

										// Readmore text
										$read_more_text = $read_more_text ?: esc_html__( 'Read more', 'total-theme-core' );

										// Readmore classes
										$readmore_classes = vcex_get_button_classes( $readmore_style, $readmore_style_color );

										// Readmore style
										$readmore_style = vcex_inline_style( array(
											'background'    => $readmore_background,
											'color'         => $readmore_color,
											'font_size'     => $readmore_size,
											'padding'       => $readmore_padding,
											'border_radius' => $readmore_border_radius,
											'margin'        => $readmore_margin,
										) );

										// Readmore hover data
										$readmore_hover_data = array();
										if ( ! empty( $atts['readmore_hover_background'] ) ) {
											$readmore_hover_data['background'] = vcex_parse_color( $atts['readmore_hover_background'] );
										}
										if ( ! empty( $atts['readmore_hover_color'] ) ) {
											$readmore_hover_data['color'] = vcex_parse_color( $atts['readmore_hover_color'] );
										}
										if ( $readmore_hover_data ) {
											$readmore_hover_data = htmlspecialchars( wp_json_encode( $readmore_hover_data ) );
										}

									}

									$readmore_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_button_wrap_class( array( 'staff-entry-readmore-wrap' ), 'vcex_staff_grid', $latts ) ) ) . '">';

										$attrs = array(
											'href'   => esc_url( $latts['post_permalink'] ),
											'class'  => esc_attr( $readmore_classes ),
											'target' => $link_target,
											'style'  => $readmore_style,
										);

										if ( $readmore_hover_data ) {
											$attrs['data-wpex-hover'] = $readmore_hover_data;
										}

										$aria_label = sprintf( esc_attr_x( '%s about %s', '*read more text* about *post name* aria label', 'total-theme-core' ), $read_more_text, $latts['post_esc_title'] );

										/**
										 * Filters the more_link aria label.
										 *
										 * @param string $aria_label
										 */
										$aria_label = apply_filters( 'wpex_aria_label', $aria_label, 'more_link' );
										$aria_label = apply_filters( 'vcex_staff_grid_readmore_aria_label', $aria_label, $latts );

										if ( $aria_label ) {
											$attrs['aria-label'] = strip_shortcodes( $aria_label );
										}

										$readmore_output .= '<a' . vcex_parse_html_attributes( $attrs ) . '>';

											$readmore_output .= esc_html( $read_more_text );

											if ( 'true' == $readmore_rarr ) {
												$readmore_output .= ' <span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
											}

										$readmore_output .= '</a>';

									$readmore_output .= '</div>';

								}

								$output .= apply_filters( 'vcex_staff_grid_readmore', $readmore_output, $latts );

								// Close Equal height div
								if ( $equal_heights_grid ) {

									$output .= '</div>';

								}

							$output .= '</div>'; // Entry details

						} // End staff entry details check

					$output .= '</div>'; // Entry inner

				$output .= '</div>'; // Entry

				// Reset counter
				if ( $entry_count == $columns ) {
					$entry_count = 0;
				}

			// End loop
			$first_run = false;
			endwhile;

		$output .= '</div>';

		/*--------------------------------*/
		/* [ Load More ]
		/*--------------------------------*/
		if ( 'true' == $atts['pagination_loadmore'] ) {
			if ( ! empty( $vcex_query->max_num_pages ) ) {
				vcex_loadmore_scripts();
				$atts['entry_count'] = $entry_count; // Update counter
				$output .= vcex_get_loadmore_button( 'vcex_staff_grid', $atts, $vcex_query );
			}
		}

		/*--------------------------------*/
		/* [ Standard Pagination ]
		/*--------------------------------*/
		elseif ( ( 'true' == $atts['pagination']
			|| ( 'true' == $atts['custom_query'] && ! empty( $vcex_query->query['pagination'] ) ) )
		) {

			$output .= vcex_pagination( $vcex_query, false );

		}

	$output .= '</div>';

	// Reset the post data to prevent conflicts with WP globals
	wp_reset_postdata();

	// @codingStandardsIgnoreLine
	echo $output;

// If no posts are found display message
else :

	echo vcex_no_posts_found_message( $atts );

// End post check
endif;
