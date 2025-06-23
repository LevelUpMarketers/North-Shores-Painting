<?php

/**
 * vcex_post_type_grid shortcode output
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$output = '';
$entry_count = ! empty( $atts['entry_count'] ) ? $atts['entry_count'] : 0;

// Extract shortcode attributes.
extract( $atts );

// Add paged attribute for load more button (used for WP_Query).
if ( ! empty( $atts['paged'] ) ) {
	$atts['paged'] = $atts['paged'];
}

// Build query.
$vcex_query = vcex_build_wp_query( $atts, 'vcex_post_type_grid' );

// Output posts.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// Define entry blocks output.
	$entry_blocks = [
		'media'      => $entry_media,
		'title'      => $title,
		'meta'       => $meta,
		'date'       => $date,
		'categories' => $show_categories,
		'excerpt'    => $excerpt,
		'read_more'  => $read_more,
	];

	// Filters the entry blocks.
	$entry_blocks = vcex_filter_grid_blocks_array( $entry_blocks );

	if ( isset( $entry_blocks['meta'] ) ) {
		unset( $entry_blocks['date'] );
		unset( $entry_blocks['categories'] );
	}

	/**
	 * Filters the post types grid shortcode entry blocks.
	 *
	 * @param array $blocks
	 * @param arrat $shortcode_attributes
	 */
	$entry_blocks = apply_filters( 'vcex_post_type_grid_entry_blocks', $entry_blocks, $atts );

	// Declare and sanitize useful variables.
	$wrap_classes = [ 'vcex-module', 'vcex-post-type-grid-wrap', 'wpex-clr' ];
	$grid_classes = [ 'wpex-row', 'vcex-post-type-grid', 'entries', 'wpex-clr' ];
	$grid_data = [];
	$is_isotope = false;
	$filter_taxonomy = ( $filter_taxonomy && taxonomy_exists( $filter_taxonomy ) ) ? $filter_taxonomy : '';
	$equal_heights_grid = ( 'true' == $equal_heights_grid && $columns > '1' ) ? true : false;
	$title_tag_escaped = $title_tag ? tag_escape( $title_tag ) : apply_filters( 'vcex_grid_default_title_tag', 'h2', $atts );
	$has_loadmore = vcex_validate_att_boolean( 'pagination_loadmore', $atts, false );
	$has_more_arrow = vcex_validate_att_boolean( 'readmore_rarr', $atts );

	// Disable css animation when filter is enabled.
	if ( 'true' == $filter ) {
		$css_animation = false;
	}

	// Set correct category taxonomy.
	if ( ! $categories_taxonomy ) {
		$categories_taxonomy = strpos( $post_types, ',' ) === false ? vcex_get_post_type_cat_tax( $post_types ) : 'category';
	}

	// Advanced sanitization.
	if ( 'true' == $filter || 'masonry' === $grid_style || 'no_margins' === $grid_style ) {
		$is_isotope = true;
		vcex_enqueue_isotope_scripts();
	}

	// Check url for filter cat.
	$filter_active_category = vcex_grid_filter_get_active_item( $filter_taxonomy );
	if ( $filter_active_category ) {
		$grid_classes[] = 'wpex-show-on-load';
		if ( 'post_types' === $filter_type ) {
			$filter_active_category = esc_attr( "type-{$filter_active_category}" );
		}
	}

	// Load lightbox scripts.
	if ( 'lightbox' === $atts['thumb_link'] || 'lightbox_gallery' === $atts['thumb_link'] ) {
		if ( 'true' == $atts['thumb_lightbox_gallery'] ) {
			$grid_classes[] = 'wpex-lightbox-group';
		}
		if ( 'true' != $atts['thumb_lightbox_title'] ) {
			$grid_data[] = 'data-show_title="false"';
		}
		vcex_enqueue_lightbox_scripts();
	}

	// Turn post types into array.
	$post_types = $post_types ?: 'post';

	if ( is_string( $post_types ) ) {
		$post_types = explode( ',', $post_types );
	}

	// Wrap classes.
	if ( $visibility ) {
		$wrap_classes[] = vcex_parse_visibility_class( $visibility );
	}

	if ( $bottom_margin ) {
		$wrap_classes[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
	}

	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Grid classes.
	if ( $columns_gap ) {
		$grid_classes[] = sanitize_html_class( "gap-{$columns_gap}" );
	}
	if ( 'left_thumbs' === $single_column_style ) {
		$grid_classes[] = 'left-thumbs';
	}
	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
		$grid_classes[] = 'wpex-overflow-hidden';
	}
	if ( 'no_margins' === $grid_style ) {
		$grid_classes[] = 'vcex-no-margin-grid';
	}
	if ( $equal_heights_grid ) {
		$grid_classes[] = 'match-height-grid';
	}

	// Data
	if ( 'true' == $filter ) {

		// Filter settings.
		if ( 'fitRows' === $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="fitRows"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $filter_speed ) . '"';
		}

		// Define filter prefix.
		if ( 'taxonomy' === $filter_type ) {

			if ( $filter_taxonomy ) {

				// Get filter args.
				$atts['filter_taxonomy'] = $filter_taxonomy;
				$args = vcex_grid_filter_args( $atts, $vcex_query );
				$filter_terms = get_terms( $filter_taxonomy, $args );

				// Set correct filter class prefix.
				$filter_prefix = $atts['filter_taxonomy'];
				if ( 'post_tag' === $filter_prefix ) {
					$filter_prefix = $filter_prefix;
				} elseif ( 'category' === $filter_prefix ) {
					$filter_prefix = str_replace( 'category', 'cat', $filter_prefix );
				} else {
					$parse_types = vcex_theme_post_types();
					$parse_types[] = 'post';
					foreach ( $parse_types as $type ) {
						if ( str_contains( $filter_prefix, $type ) ) {
							$filter_prefix = str_replace(
								[ "{$type}_category", 'category', "{$type}_tag" ],
								[ 'cat', 'cat', 'tag' ],
								$filter_prefix
							);
						}
					}
				}
			}

		} else {
			if ( $has_loadmore ) {
				$filter_post_types = $post_types;
			} else {
				$filter_post_types = array();
				$post_ids = wp_list_pluck( $vcex_query->posts, 'ID' );
				foreach ( $post_ids as $post_id ) {
					$type = get_post_type( $post_id );
					$filter_post_types[ $type ] = $type;
				}
			}
		}

		// Add active filter data.
		if ( $filter_active_category ) {
			if ( $filter_taxonomy ) {
				$grid_data[] = 'data-filter=".' . esc_attr( $filter_prefix . '-' . $filter_active_category ) . '"';
			} else {
				$grid_data[] = 'data-filter=".' . esc_attr( $filter_active_category ) . '"';
			}
		}

	} else {

		$isotope_transition_duration = apply_filters( 'vcex_isotope_transition_duration', null, 'vcex_post_type_grid' );
		if ( $isotope_transition_duration ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $isotope_transition ) . '"';
		}

	}

	// Entry CSS class.
	if ( $entry_css ) {
		$entry_css = vcex_vc_shortcode_custom_css_class( $entry_css );
	}

	// Apply filters.
	$wrap_classes = apply_filters( 'vcex_post_type_grid_wrap_classes', $wrap_classes );
	$grid_classes = apply_filters( 'vcex_post_type_grid_classes', $grid_classes );
	$grid_data    = apply_filters( 'vcex_post_type_grid_data_attr', $grid_data );

	// Convert arrays into strings.
	$wrap_classes = implode( ' ', $wrap_classes );
	$grid_classes = implode( ' ', $grid_classes );
	$grid_data    = $grid_data ? ' ' . implode( ' ', $grid_data ) : '';

	// VC filter.
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_post_type_grid', $atts );

	// Start output.
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		/*--------------------------------*/
		/* [ Heading ]
		/*--------------------------------*/
		if ( ! empty( $atts[ 'heading' ] ) ) {

			$output .= vcex_get_module_header( array(
				'style'   => ! empty( $atts[ 'header_style' ] ) ? $atts[ 'header_style' ] : '',
				'content' => $atts[ 'heading' ],
				'classes' => array( 'vcex-module-heading vcex_post_type_grid-heading' ),
			) );

		}

		/*--------------------------------*/
		/* [ Filter Links ]
		/*--------------------------------*/
		if ( 'true' == $filter ) :

			// Make sure the filter should display.
			if ( count( $post_types ) > 1 || 'taxonomy' === $filter_type ) {

				$all_text = $all_text ?: esc_html__( 'All', 'total-theme-core' );
				$filter_button_classes = vcex_get_button_classes( $filter_button_style, $filter_button_color );
				$filter_select_bk = ! empty( $atts['filter_select_bk'] ) ? $atts['filter_select_bk'] : null;
				$has_filter_select = $filter_select_bk ? true : false;
				$filter_classes = 'vcex-post-type-filter vcex-filter-links wpex-flex-wrap wpex-list-none wpex-m-0';
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
					$filter_select_class = "vcex-post-type-filter-select vcex-filter-links-select wpex-{$filter_select_bk_safe}-hidden wpex-select-wrap";
					$filter_select_class .= vcex_has_classic_styles() ? ' wpex-mb-25' : ' wpex-mb-30';

					$output .= '<div class="' . esc_attr( $filter_select_class ) . '">';

						$output .= '<select>';

							$output .= '<option value="*">' . esc_html( $all_text ) . '</option>';

							if ( 'taxonomy' === $filter_type ) {
								if ( $filter_taxonomy && ! empty( $filter_terms ) ) {
									foreach ( $filter_terms as $term ) {
										$selected = '';
										if ( $filter_active_category && $filter_active_category == $term->term_id ) {
											$selected = ' selected';
										}
										$output .= '<option value=".cat-' . absint( $term->term_id ) . '"' . $selected . '>' . esc_html( $term->name ) . '</option>';
									}
								}

							} elseif ( ! empty( $filter_post_types ) ) {
								foreach ( $filter_post_types as $type ) {
									$selected = '';
									if ( $filter_active_category == "type-{$type}" ) {
										$selected = ' selected';
									}
									$output .= '<option value=".type-' . esc_attr( $type ) . '"' . $selected . '>' . get_post_type_object( $type )->labels->name . '</option>';
								}
							}

						$output .= '</select>';

					if ( is_callable( 'TotalTheme\Forms\Select_Wrap::arrow' ) ) {
						ob_start();
							TotalTheme\Forms\Select_Wrap::arrow();
						$output .= ob_get_clean();
					}

					$output .= '</div>';

				}

				// Filter Buttons.
				$output .= '<ul class="'. esc_attr( $filter_classes ) .'">';

					$output .= '<li';
						if ( ! $filter_active_category ) {
							$output .= ' class="active"';
						}
					$output .= '>';
						$output .= '<a href="#" data-filter="*" class="' . esc_attr( $filter_button_classes ) . '"><span>' . esc_html( $all_text ) . '</span></a>';
					$output .= '</li>';

					// Taxonomy style filter.
					if ( 'taxonomy' === $filter_type ) {
						if ( $filter_taxonomy && ! empty( $filter_terms ) ) {
							foreach ( $filter_terms as $term ) :
								$output .= '<li class="filter-cat-'. absint( $term->term_id );
									if ( $filter_active_category == $term->term_id ) {
										$output .= ' active';
									}
								$output .= '">';
									$output .= '<a href="#" data-filter=".' . esc_attr( "{$filter_prefix}-{$term->term_id}" ) . '" class="' . esc_attr( $filter_button_classes ) . '">';
										$output .= esc_html( $term->name );
									$output .= '</a>';
								$output .= '</li>';
							endforeach;
						}
					}
					// Post types filter.
					elseif ( ! empty( $filter_post_types ) ) {
						foreach ( $filter_post_types as $type ) {
							$output .= '<li class="vcex-filter-link-' . sanitize_html_class( $type );
								if ( $filter_active_category == "type-{$type}" ) {
									$output .= ' active';
								}
							$output .= '">';
							$output .= '<a href="#" data-filter="' . esc_attr( ".type-{$type}" ) . '" class="' . esc_attr( $filter_button_classes ) . '">';
								$output .= esc_html( get_post_type_object( $type )->labels->name );
							$output .= '</a></li>';
						}
					}

				$output .= '</ul>';

				if ( $vcex_after_grid_filter = apply_filters( 'vcex_after_grid_filter', '', $atts ) ) {
					$output .= $vcex_after_grid_filter;
				}

			}

		endif; // End filter

		/*--------------------------------*/
		/* [ Begin Grid output ]
		/*--------------------------------*/
		$output .= '<div class="' . esc_attr( $grid_classes ) . '"' . $grid_data . '>';

			// Static entry classes.
			$static_entry_classes = [
				'vcex-post-type-entry',
				'vcex-grid-item',
				'wpex-clr'
			];

			if ( 'false' == $columns_responsive ) {
				$static_entry_classes[] = 'nr-col';
			} else {
				$static_entry_classes[] = 'col';
			}

			$static_entry_classes[] = vcex_get_grid_column_class( $atts );

			if ( $is_isotope ) {
				$static_entry_classes[] = 'vcex-isotope-entry';
			}

			if ( 'no_margins' === $grid_style ) {
				$static_entry_classes[] = 'vcex-no-margin-entry';
			}

			if ( $css_animation_class = vcex_get_css_animation( $css_animation ) ) {
				$static_entry_classes[] = $css_animation_class;
			}

			if ( $content_alignment ) {
				$static_entry_classes[] = sanitize_html_class( "text{$content_alignment}" );
			}

			if ( ! isset( $entry_blocks['media'] ) ) {
				$static_entry_classes[] = 'vcex-post-type-no-media-entry';
			}

			/**** Loop Start ***/
			$first_run = true;
			while ( $vcex_query->have_posts() ) :

				// Get post from query.
				$vcex_query->the_post();

				// Add to counter var.
				$entry_count++;

				// Post Data
				$atts['post_id'] = get_the_ID();
				$post_id = $atts['post_id'];
				$atts['post_type'] = get_post_type( $post_id );
				$atts['post_title'] = get_the_title();
				$atts['post_esc_title'] = vcex_esc_title();
				$atts['post_permalink'] = vcex_get_permalink( $post_id );
				$atts['post_format' ] = get_post_format( $post_id );
				$atts['post_excerpt'] = '';
				$atts['post_thumbnail_id'] = get_post_thumbnail_id( $post_id );
				$atts['post_video_html'] = ( 'true' == $featured_video ) ? vcex_get_post_video_html() : '';
				$atts['lightbox_data'] = array(); // @note DONT CHANGE DUE TO "vcex_shortcode_loop_atts" FILTER.

				// Entry Classes.
				$entry_classes = [];
				$entry_classes[] = sanitize_html_class( "col-{$entry_count}" );
				$entry_classes = array_merge( $static_entry_classes, $entry_classes );

				// Apply filters to attributes.
				$latts = apply_filters( 'vcex_shortcode_loop_atts', $atts, 'vcex_post_type_grid' );

				// Entry image output HTML.
				$entry_image = '';
				if ( $latts['post_thumbnail_id'] ) {

					$thumbnail_class = implode( ' ' , vcex_get_entry_thumbnail_class( [ 'vcex-blog-entry-img' ], 'vcex_post_type_grid', $latts ) );

					// Define thumbnail args.
					$thumbnail_args = [
						'attachment'    => $latts['post_thumbnail_id'],
						'size'          => $img_size,
						'crop'          => $img_crop,
						'width'         => $img_width,
						'height'        => $img_height,
						'class'         => $thumbnail_class,
						'apply_filters' => 'vcex_post_type_grid_thumbnail_args',
						'filter_arg1'   => $latts,
					];

					// Disable lazy loading.
					if ( $is_isotope ) {
						$thumbnail_args['lazy'] = false;
					}

					// Set entry image var
					$entry_image = vcex_get_post_thumbnail( $thumbnail_args );

				}
				$entry_image = apply_filters( 'vcex_post_type_grid_entry_image', $entry_image, $latts );

				// Get and save Lightbox data for use with Overlays, media, title, etc.
				$oembed_url = vcex_get_post_video_oembed_url( $post_id );
				$embed_url = vcex_get_video_embed_url( $oembed_url ); // returns embed url and adds custom params filter.
				$lightbox_image = vcex_get_lightbox_image();
				if ( $embed_url ) {
					$latts['lightbox_link'] = $embed_url;
					if ( $lightbox_image ) {
						$latts['lightbox_data']['data-thumb'] = 'data-thumb="' . esc_attr( $lightbox_image ) . '"';
					}
				} else {
					$latts['lightbox_link'] = $lightbox_image;
				}

				/*--------------------------------*/
				/* [ Begin Entry output ]
				/*--------------------------------*/
				$output .= '<div ' . vcex_grid_get_post_class( $entry_classes, $post_id ) . '>';

					// Inner entry output.
					$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_inner_class( array( 'vcex-post-type-entry-inner' ), 'vcex_post_type_grid', $latts ) ) ) . '">';

						// Display media.
						if ( isset( $entry_blocks['media'] ) ) {

							$latts[ 'media_type' ] = 'thumbnail'; // so overlays can work

							$media_output = '';

							// Custom output.
							if ( is_callable( $entry_blocks['media'] ) ) {
								$media_output .= call_user_func( $entry_blocks['media'] );
							}

							// Default module output.
							else {

								/*--------------------------------*/
								/* [ Entry Video ]
								/*--------------------------------*/
								if ( $latts['post_video_html'] ) {
									$latts[ 'media_type' ] = 'video';
									$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'vcex-post-type-entry-media' ), 'vcex_post_type_grid', $latts ) ) ) . '">';
										$media_output .= '<div class="vcex-video-wrap">';
											$media_output .= $latts['post_video_html'];
										$media_output .= '</div>';
									$media_output .= '</div>';
								}

								/*--------------------------------*/
								/* [ Entry Featured Image ]
								/*--------------------------------*/
								elseif ( $entry_image ) {

									$latts['media_type'] = 'thumbnail';

									$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'vcex-post-type-entry-media' ), 'vcex_post_type_grid', $latts ) ) ) . '">';

										// Image with link.
										if ( ( 'post' === $latts['thumb_link'] && $latts['post_permalink'] )
											|| 'lightbox' === $latts['thumb_link']
											|| 'lightbox_gallery' === $latts['thumb_link']
										) {

											$media_link_attrs = array(
												'href'   => esc_url( $latts['post_permalink'] ),
												'title'  => $latts['post_esc_title'],
												'target' => $latts['url_target'],
												'class'  => '',
											);

											// Lightbox
											if (  'lightbox' === $latts['thumb_link']
												|| 'lightbox_gallery' === $latts['thumb_link'] ) {

												// Lightbox post gallery.
												if ( 'lightbox_gallery' === $latts['thumb_link']
													&& $lightbox_gallery_imgs = vcex_get_post_gallery_ids( $latts['post_id'], 'lightbox' )
												) {
													$media_link_attrs['class'] .= ' wpex-lightbox-gallery';
													$media_link_attrs['data'] = 'data-gallery="' . vcex_parse_inline_lightbox_gallery( $lightbox_gallery_imgs ) . '"';
												}

												// Singular lightbox.
												elseif ( ! empty( $latts['lightbox_link'] ) ) {
													if ( 'true' == $atts['thumb_lightbox_gallery'] ) {
														$lightbox_single_class = 'wpex-lightbox-group-item';
													} else {
														$lightbox_single_class = 'wpex-lightbox';
													}
													$media_link_attrs['class'] = $media_link_attrs['class'] ? ' ' . $lightbox_single_class : $lightbox_single_class;
													$media_link_attrs['href'] = $latts['lightbox_link'];
													$media_link_attrs['data'] = $latts['lightbox_data'];
													$media_link_attrs['target'] = '';
												}

											} else {

												// Lightbox disabled.
												$latts['lightbox_link'] = null; // prevents issues w/ overlay button hover.

											}

											$media_output .= '<a' . vcex_parse_html_attributes( $media_link_attrs ) . '>';
												$media_output .= $entry_image;
												$media_output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_post_type_grid', $latts );
												$media_output .= vcex_get_entry_media_after( 'vcex_post_type_grid' );
											$media_output .= '</a>';

										// Just the image.
										} else {
											$media_output .= $entry_image;
											$media_output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_post_type_grid', $latts );
											$media_output .= vcex_get_entry_media_after( 'vcex_post_type_grid' );
										}

										$media_output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_post_type_grid', $latts );

									$media_output .= '</div>';

								}

							}

							$output .= apply_filters( 'vcex_post_type_grid_media', $media_output, $latts );

						} // End media check.

						/*--------------------------------*/
						/* [ Entry Content ]
						/*--------------------------------*/
						if ( isset( $entry_blocks['title'] )
							|| isset( $entry_blocks['meta'] )
							|| isset( $entry_blocks['date'] )
							|| isset( $entry_blocks['categories'] )
							|| isset( $entry_blocks['excerpt'] )
							|| isset( $entry_blocks['read_more'] )
						) {

							$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( array( 'vcex-post-type-entry-details' ), 'vcex_post_type_grid', $latts ) ) ) . '">';

								// Open equal heights wrapper.
								if ( $equal_heights_grid ) {
									$output .= '<div class="match-height-content">';
								}

								// Entry blocks (except media since it's inside it's own wrapper).
								foreach ( $entry_blocks as $k => $v ) :

									// Media shouldn't be here.
									if ( 'media' === $k ) {
										continue;
									}

									// Custom output.
									elseif ( $v && is_callable( $v ) ) {
										$output .= call_user_func( $v );
									}

									/*--------------------------------*/
									/* [ Entry Title ]
									/*--------------------------------*/
									elseif ( 'title' === $k ) {

										$title_output = '<' . $title_tag_escaped . ' class="' . esc_attr( implode( ' ', vcex_get_entry_title_class( array( 'vcex-post-type-entry-title' ), 'vcex_post_type_grid', $latts ) ) ) . '">';

										if ( 'post' === $latts['title_link'] && $latts['post_permalink'] ) {

											$link_attrs = [
												'href'   => esc_url( $latts['post_permalink'] ),
												'target' => $latts['url_target'],
											];

											$title_output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

												$title_output .= wp_kses_post( $latts['post_title'] );

											$title_output .= '</a>';

										} else {
											$title_output .= $latts['post_title'];
										}

										$title_output .= '</' . $title_tag_escaped . ' >';

										$output .= apply_filters( 'vcex_post_type_grid_title', $title_output, $latts );

									}

									/*--------------------------------*/
									/* [ Entry Meta ]
									/*--------------------------------*/
									elseif ( 'meta' === $k ) {

										if ( $first_run ) {
											$meta_class = 'vcex-post-type-entry-meta entry-meta wpex-mt-10';
											if ( $meta_color ) {
												$meta_class .= ' wpex-child-inherit-color';
											}
										}

										$meta_output = '<div class="' . esc_attr( $meta_class ) . '">';

										ob_start();
											$blocks = array();

											if ( ! empty( $latts[ 'meta_blocks'] ) ) {
												$blocks = $latts[ 'meta_blocks'];
												if ( is_string( $blocks ) ) {
													$blocks = explode( ',', $blocks );
												}
											}

											/**
											 * Filters the vcex_post_type_grid shortcode meta blocks.
											 *
											 * @param array $blocks
											 * @param array $shortcode_attributes
											 */
											$blocks = apply_filters( 'vcex_post_type_grid_meta_blocks', $blocks, $latts );

											// Get the meta partial file.
											get_template_part( 'partials/meta/meta', get_post_type(), [
												'blocks'   => $blocks,
												'singular' => false,
											] );

										$meta_output .= ob_get_clean();

										$meta_output .= '</div>';

										$output .= apply_filters( 'vcex_post_type_grid_meta', $meta_output, $latts );

									}

									/*--------------------------------*/
									/* [ Entry Date ]
									/*--------------------------------*/
									elseif ( 'date' === $k ) {

										$date_output = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_date_class( array( 'vcex-post-type-entry-date' ), 'vcex_post_type_grid', $latts ) ) ) . '">';

											// Get Tribe Events date.
											if ( 'tribe_events' == $latts['post_type']
												&& class_exists( 'Tribe__Events__Main' )
												&& function_exists( 'wpex_get_tribe_event_date' )
											) {
												$instance = $unique_id ?: 'vcex_post_type_grid';
												$latts['post_date'] = wpex_get_tribe_event_date( $instance );

											// Get standard date.
											} else {
												$latts['post_date'] = get_the_date();
											}

											// Output date.
											$date_output .= apply_filters( 'vcex_post_type_grid_date_inner', $latts['post_date'], $latts );

										$date_output .= '</div>';

										$output .= apply_filters( 'vcex_post_type_grid_date', $date_output, $latts );

									}

									/*--------------------------------*/
									/* [ Entry Categories ]
									/*--------------------------------*/
									elseif ( 'categories' === $k ) {

										$categories_output = '';
										$get_categories = '';

										if ( taxonomy_exists( $categories_taxonomy ) ) {

												if ( 'true' == $show_first_category_only ) {
													if ( ! vcex_validate_boolean( $latts[ 'categories_links' ] ) ) {
														$get_categories = vcex_get_first_term( $latts['post_id'], $categories_taxonomy );
													} else {
														$get_categories = vcex_get_first_term_link( $latts['post_id'], $categories_taxonomy );
													}
												} else {
													$get_categories = vcex_get_list_post_terms( $categories_taxonomy, vcex_validate_boolean( $latts[ 'categories_links' ] ) );
												}

												$get_categories = apply_filters( 'vcex_post_type_grid_get_categories', $get_categories, $latts );

												if ( $get_categories ) {
													$categories_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_categories_class( array( 'vcex-post-type-entry-categories' ), 'vcex_post_type_grid', $atts ) ) ) . '">';
														$categories_output .= $get_categories;
													$categories_output .= '</div>';
												}

										}

										$output .= apply_filters( 'vcex_post_type_grid_categories', $categories_output, $latts );

									}

									/*--------------------------------*/
									/* [ Entry Excerpt ]
									/*--------------------------------*/
									elseif ( 'excerpt' === $k ) {
										$excerpt_output = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( array( 'vcex-post-type-entry-excerpt' ), 'vcex_post_type_grid', $latts ) ) ) . '">';
											$excerpt_output .= vcex_get_excerpt( [
												'length'  => $excerpt_length,
												'context' => 'vcex_post_type_grid',
											] );
										$excerpt_output .= '</div>';
										$output .= apply_filters( 'vcex_post_type_grid_excerpt', $excerpt_output, $latts );
									}

									/*--------------------------------*/
									/* [ Entry Button ]
									/*--------------------------------*/
									elseif ( 'read_more' === $k ) {

										if ( $first_run ) {
											$readmore_classes = vcex_get_button_classes( $readmore_style, $readmore_style_color );
										}

										if ( $latts['post_permalink'] ) {

											$readmore_output = '';

											if ( ! empty( $latts['read_more_text'] ) && is_string( $latts['read_more_text'] ) ) {
												$read_more_text = $latts['read_more_text'];
												$read_more_text = str_replace( '{{title}}', $latts['post_title'], $read_more_text );
												$more_text = do_shortcode( wp_kses_post( $read_more_text ) );
											} else {
												$more_text = esc_html__( 'Read more', 'total-theme-core' );
											}

											$readmore_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_button_wrap_class( array( 'vcex-post-type-entry-readmore-wrap' ), 'vcex_post_type_grid', $latts ) ) ) . '">';

												$attrs = [
													'href'   => esc_url( $latts['post_permalink'] ),
													'class'  => "entry-readmore {$readmore_classes}",
													'target' => $latts['url_target'],
												];

												$aria_label = sprintf( esc_html_x( '%s about %s', '*read more text* about *post name* aria label', 'total-theme-core' ), $more_text, $latts['post_esc_title'] );

												/**
												 * Filters the more_link aria label.
												 *
												 * @param string $aria_label
												 */
												$aria_label = apply_filters( 'wpex_aria_label', $aria_label, 'more_link' );
												$aria_label = apply_filters( 'vcex_post_type_grid_readmore_aria_label', $aria_label, $latts );

												if ( $aria_label ) {
													$attrs['aria-label'] = strip_shortcodes( $aria_label );
												}

												$readmore_output .= '<a' . vcex_parse_html_attributes( $attrs ) . '>';

													$readmore_output .= $more_text;

													if ( $has_more_arrow ) {
														$readmore_output .= ' <span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
													}

												$readmore_output .= '</a>';

											$readmore_output .= '</div>';

										} else {
											$readmore_output = '';
										}

										$output .= apply_filters( 'vcex_post_type_grid_readmore', $readmore_output, $latts );

									}

								// End entry blocks.
								endforeach;

								// Close equal heights wrap.
								if ( $equal_heights_grid ) {
									$output .= '</div>';
								}

							$output .= '</div>';

						}

					$output .= '</div>';

				$output .= '</div>';

			// Reset count clear floats.
			if ( $entry_count == $columns ) {
				$entry_count = 0;
			}

			$first_run = false; endwhile;

		$output .= '</div>'; // End grid classes.

		/*--------------------------------*/
		/* [ Pagination ]
		/*--------------------------------*/

		// Load more button.
		if ( $has_loadmore ) {

			if ( ! empty( $vcex_query->max_num_pages ) ) {
				vcex_loadmore_scripts();
				$atts['entry_count'] = $entry_count; // Update counter
				$output .= vcex_get_loadmore_button( 'vcex_post_type_grid', $atts, $vcex_query );
			}

		}

		// Standard pagination.
		elseif ( vcex_validate_boolean( $pagination )
			|| vcex_validate_boolean( $auto_query )
			|| ( vcex_validate_boolean( $custom_query ) && ! empty( $vcex_query->query['pagination'] ) )
		) {

			$output .= vcex_pagination( $vcex_query, false );

		}

	$output .= '</div>'; // End module classes.

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
