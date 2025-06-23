<?php

/**
 * vcex_post_type_carousel shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Define output var
$output = '';

// Extract shortcode attributes.
extract( $atts );

// Build the WordPress query.
$vcex_query = vcex_build_wp_query( $atts, 'vcex_post_type_carousel' );

//Output posts.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// All carousels need a unique classname.
	$unique_classname = vcex_element_unique_classname();

	// Get carousel settings.
	$carousel_settings = vcex_get_carousel_settings( $atts, 'vcex_post_type_carousel', false );
	$carousel_css = vcex_get_carousel_inline_css( $unique_classname, $carousel_settings );

	if ( $carousel_css ) {
		$output .= $carousel_css;
	}

	// Enqueue scripts.
	vcex_enqueue_carousel_scripts();

	// Extract attributes.
	extract( $atts );

	// Disable auto play if there is only 1 post.
	if ( '1' == count( $vcex_query->posts ) ) {
		$auto_play = false;
	}

	// Prevent auto play in WPBakery front-end editor.
	if ( vcex_vc_is_inline() ) {
		$atts['auto_play'] = false;
	}

	// Items to scroll fallback for old setting.
	if ( 'page' === $items_scroll ) {
		$items_scroll = $items;
	}

	// Main Classes.
	$wrap_classes = [
		'vcex-post-type-carousel',
		'vcex-module',
		'wpex-carousel',
		'wpex-carousel-post-type',
		'wpex-clr',
	];

	if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
		$wrap_classes[] = 'owl-carousel';
	}

	if ( $carousel_css ) {
		$wrap_classes[] = 'wpex-carousel--render-onload';
		$wrap_classes[] = $unique_classname;
	}

	if ( $bottom_margin ) {
		$wrap_classes[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
	}

	// Carousel style.
	if ( $style && 'default' !== $style ) {
		$wrap_classes[] = $style;
		$arrows_position = ( 'no-margins' === $style && 'default' === $arrows_position ) ? 'abs' : $arrows_position;
	}

	// Arrow style.
	if ( ! $arrows_style ) {
		$arrows_style = 'default';
	}
	$wrap_classes[] = 'arrwstyle-' . sanitize_html_class( $arrows_style );

	// Arrow position.
	if ( $arrows_position && 'default' !== $arrows_position ) {
		$wrap_classes[] = 'arrwpos-' . sanitize_html_class( $arrows_position );
	}

	// Alignment.
	if ( $content_alignment ) {
		$wrap_classes[] = vcex_parse_text_align_class( $content_alignment );
	}

	// Visibility.
	if ( $visibility ) {
		$wrap_classes[] = vcex_parse_visibility_class( $visibility );
	}

	// CSS animation.
	if ( $css_animation_class = vcex_get_css_animation( $css_animation ) ) {
		$wrap_classes[] = $css_animation_class;
	}

	// Custom Classes.
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Lightbox classes and scripts.
	if ( 'true' == $media && 'lightbox' === $thumbnail_link ) {
		vcex_enqueue_lightbox_scripts();
		if ( 'true' == $lightbox_gallery ) {
			$wrap_classes[] = 'wpex-carousel-lightbox';
		}
	}

	// Readmore design and classes.
	if ( 'true' == $read_more ) {
		$read_more_text = $read_more_text ?: esc_html__( 'Read more', 'total-theme-core' );
		$readmore_classes = vcex_get_button_classes( $readmore_style, $readmore_style_color );
	}

	$wrap_classes[] = $unique_classname;

	/**
	 * Filters the vcex_post_type_carousel wrap classes.
	 *
	 * @param array $wrap_classes
	 * @param array $shortcode_attributes
	 */
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_post_type_carousel', $atts );

	/*-----------------------------------------------------*/
	/* [ Module Header ]
	/*-----------------------------------------------------*/
	if ( $header ) {

		$output .= vcex_get_module_header( array(
			'style'   => $header_style,
			'content' => $header,
			'classes' => array( 'vcex-module-heading', 'vcex_post_type_carousel-heading' ),
		) );

	}

	/*-----------------------------------------------------*/
	/* [ Begin Carousel Output ]
	/*-----------------------------------------------------*/
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '" data-wpex-carousel="' . vcex_carousel_settings_to_json( $carousel_settings ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Define entry classes.
		$entry_classes = array( 'wpex-carousel-slide', 'wpex-clr' );
		if ( $entry_css ) {
			$entry_classes[] = vcex_vc_shortcode_custom_css_class( $entry_css );
		}

		// Start loop.
		$lcount = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) :

			// Get post from query.
			$vcex_query->the_post();

			// Post VARS.
			$atts['post_id']        = get_the_ID();
			$atts['post_type']      = get_post_type( $atts['post_id'] );
			$atts['post_permalink'] = vcex_get_permalink( $atts['post_id'] );
			$atts['post_title']     = get_the_title( $atts['post_id'] );
			$atts['post_title_esc'] = vcex_esc_title( $atts['post_id'] );

			/*-----------------------------------------------------*/
			/* [ Begin Entry Output ]
			/*-----------------------------------------------------*/
			if ( ( 'true' == $media && has_post_thumbnail() )
				|| 'true' == $title
				|| 'true' == $date
				|| 'true' == $excerpt
				|| 'true' == $read_more
			) :

				$output .= '<div ' . vcex_grid_get_post_class( $entry_classes, $atts['post_id'] ) . '>';

					/*-----------------------------------------------------*/
					/* [ Featured Image ]
					/*-----------------------------------------------------*/
					$media_output = '';
					if ( 'true' == $media ) {

						if ( has_post_thumbnail() ) {

							$atts['media_type'] = 'thumbnail';

							$thumbnail_class = implode( ' ' , vcex_get_entry_thumbnail_class(
								array( 'wpex-carousel-entry-img' ),
								'vcex_post_type_carousel',
								$atts
							) );

							// Generate image html.
							$img_html = vcex_get_post_thumbnail( array(
								'size'          => $img_size,
								'crop'          => $img_crop,
								'width'         => $img_width,
								'height'        => $img_height,
								'class'         => $thumbnail_class,
								'lazy'          => false,
								'apply_filters' => 'vcex_post_type_carousel_thumbnail_args',
								'filter_arg1'   => $atts,
							) );

							$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'wpex-carousel-entry-media' ), 'vcex_post_type_carousel', $atts ) ) ) . '">';

								// No links.
								if ( 'none' == $thumbnail_link ) {

									$media_output .= $img_html;

									$media_output .= vcex_get_entry_media_after( 'vcex_post_type_carousel' );

								// Lightbox.
								} elseif ( 'lightbox' === $thumbnail_link ) {

									$lcount++;

									$atts['lightbox_data']  = array(); // must reset for each item
									$lightbox_image_escaped = vcex_get_lightbox_image();
									$atts['lightbox_link']  = $lightbox_image_escaped;

									$link_attrs = array(
										'href'       => '',
										'title'      => $atts['post_title_esc'],
										'class'      => 'wpex-carousel-entry-img',
										'data-count' => absint( $lcount )
									);

									if ( 'true' == $lightbox_gallery ) {
										$link_attrs['class'] .= ' wpex-carousel-lightbox-item';
									} else {
										$link_attrs['class'] .= ' wpex-lightbox';
									}

									// Check for video.
									if ( $oembed_video_url = vcex_get_post_video_oembed_url( $atts['post_id'] ) ) {
										$embed_url = vcex_get_video_embed_url( $oembed_video_url );
										if ( $embed_url ) {
											$atts['lightbox_link']               = esc_url( $embed_url );
											$atts['lightbox_data']['data-thumb'] = 'data-thumb="' . $lightbox_image_escaped . '"';
										}
									}

									$link_attrs['href'] = $atts['lightbox_link'];

									if ( ! empty( $atts['lightbox_data'] ) ) {
										foreach ( $atts['lightbox_data'] as $ld_k => $ld_v ) {
											$link_attrs[$ld_k] = $ld_v;
										}
									}

									$media_output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

										$media_output .= $img_html;

								// Link to post.
								} else {

									$media_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '" title="'. $atts['post_title_esc'] .'" class="wpex-carousel-entry-img">';

										$media_output .= $img_html;

								} // End thumbnail_link check

								// Overlay & close link.
								if ( 'none' !== $thumbnail_link ) {

									$media_output .= vcex_get_entry_media_after( 'vcex_post_type_carousel' );

									// Inner Overlay.
									$media_output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_post_type_carousel', $atts );

									// Close link.
									$media_output .= '</a>';

								}

								// Outside Overlay.
								$media_output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_post_type_carousel', $atts );

							$media_output .= '</div>';

						}

						$output .= apply_filters( 'vcex_post_type_carousel_media', $media_output, $atts );

					}

					/*-----------------------------------------------------*/
					/* [ Details ]
					/*-----------------------------------------------------*/
					if ( 'true' == $title || 'true' == $excerpt || 'true' == $date || 'true' == $read_more ) {

						$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( array( 'wpex-carousel-entry-details' ), 'vcex_post_type_carousel', $atts ) ) ) . '">';

							/*-----------------------------------------------------*/
							/* [ Title ]
							/*-----------------------------------------------------*/
							$title_output = '';
							if ( 'true' == $title && $atts['post_title'] ) {
								$title_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_title_class( array( 'wpex-carousel-entry-title' ), 'vcex_post_type_carousel', $atts ) ) ) . '">';
									$title_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '">';
										$title_output .= wp_kses_post( $atts['post_title'] );
									$title_output .= '</a>';
								$title_output .= '</div>';
								$output .= apply_filters( 'vcex_post_type_carousel_title', $title_output, $atts );
							} // End title check

							/*-----------------------------------------------------*/
							/* [ Date ]
							/*-----------------------------------------------------*/
							$date_output = '';
							if ( 'true' == $date ) {
								$date_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_date_class( array( 'wpex-carousel-entry-date' ), 'vcex_post_type_carousel', $atts ) ) ) . '">';
									if ( 'tribe_events' == $atts['post_type'] && function_exists( 'tribe_get_start_date' ) ) {
										$date_output .= esc_html( tribe_get_start_date( $atts['post_id'], false, get_option( 'date_format' ) ) );
									} else {
										$date_output .= get_the_date();
									}
								$date_output .= '</div>';
								$output .= apply_filters( 'vcex_post_type_carousel_date', $date_output, $atts );
							} // End date check

							/*-----------------------------------------------------*/
							/* [ Categories ]
							/*-----------------------------------------------------*/
							if ( 'true' == $show_categories ) {

								$categories_output = '';

								if ( taxonomy_exists( $categories_taxonomy ) ) {
									$categories_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_categories_class( array( 'wpex-carousel-entry-categories' ), 'vcex_post_type_carousel', $atts ) ) ) . '">';
										if ( 'true' == $show_first_category_only ) {
											if ( ! vcex_validate_boolean( $atts[ 'categories_links' ] ) ) {
												$categories_output .= vcex_get_first_term( $atts['post_id'], $categories_taxonomy );
											} else {
												$categories_output .= vcex_get_first_term_link( $atts['post_id'], $categories_taxonomy );
											}
										} else {
											$categories_output .= vcex_get_list_post_terms( $categories_taxonomy, vcex_validate_boolean( $atts[ 'categories_links' ] ) );
										}
									$categories_output .= '</div>';
								}

								$output .= apply_filters( 'vcex_post_type_carousel_categories', $categories_output, $atts );

							}  // End categories

							/*-----------------------------------------------------*/
							/* [ Excerpt ]
							/*-----------------------------------------------------*/
							$excerpt_output = '';
							if ( 'true' == $excerpt ) {

								// Generate excerpt.
								$atts['post_excerpt'] = vcex_get_excerpt( array(
									'length'  => $excerpt_length,
									'context' => 'vcex_post_type_carousel',
								) );

								if ( $atts['post_excerpt'] ) {

									$excerpt_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( array( 'wpex-carousel-entry-excerpt' ), 'vcex_post_type_carousel', $atts ) ) ) . '">';

										$excerpt_output .= $atts['post_excerpt']; // Sanitized already via vcex_get_excerpt

									$excerpt_output .= '</div>';

								}

								$output .= apply_filters( 'vcex_post_type_carousel_excerpt', $excerpt_output, $atts );

							} // End excerpt check

							/*-----------------------------------------------------*/
							/* [ Read More ]
							/*-----------------------------------------------------*/
							$readmore_output = '';
							if ( 'true' == $read_more ) {

								$readmore_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_button_wrap_class( array( 'wpex-carousel-entry-button' ), 'vcex_post_type_carousel', $atts ) ) ) . '">';

									$attrs = [
										'href'  => esc_url( $atts['post_permalink'] ),
										'class' => "entry-readmore {$readmore_classes}",
									];

									$aria_label = sprintf( esc_attr_x( '%s about %s', '*read more text* about *post name* aria label', 'total-theme-core' ), $read_more_text, $atts['post_title_esc'] );

									$aria_label = apply_filters( 'wpex_aria_label', $aria_label, 'more_link' );
									$aria_label = apply_filters( 'vcex_post_type_carousel_readmore_aria_label', $aria_label, $atts );

									if ( $aria_label ) {
										$attrs['aria-label'] = strip_shortcodes( $aria_label );
									}

									$readmore_output .= '<a' . vcex_parse_html_attributes( $attrs ) . '>';
										$readmore_output .= do_shortcode( wp_kses_post( $read_more_text ) );
										if ( 'true' == $readmore_rarr ) {
											$readmore_output .= ' <span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
										}
									$readmore_output .= '</a>';

								$readmore_output .= '</div>';

								$output .= apply_filters( 'vcex_post_type_carousel_readmore', $readmore_output, $atts );

						} // End readmore check

						$output .= '</div>';

					} // End content area check

				$output .= '</div>';

			endif;

		$first_run = false; endwhile;

	$output .= '</div>';

	// @codingStandardsIgnoreLine
	echo $output;

	// Reset the post data to prevent conflicts with WP globals.
	wp_reset_postdata();

// If no posts are found display message.
else :

	// Display no posts found error if function exists.
	echo vcex_no_posts_found_message( $atts );

// End post check
endif;
