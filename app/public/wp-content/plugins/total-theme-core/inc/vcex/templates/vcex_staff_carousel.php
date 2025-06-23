<?php

/**
 * vcex_staff_carousel shortcode.
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

// Inline check.
$is_inline = vcex_vc_is_inline();

// Build the WordPress query.
$atts['post_type'] = 'staff';
$atts['taxonomy']  = 'staff_category';
$atts['tax_query'] = '';
$vcex_query = vcex_build_wp_query( $atts, 'vcex_staff_carousel' );

// Output posts.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// All carousels need a unique classname.
	$unique_classname = vcex_element_unique_classname();

	// Get carousel settings.
	$carousel_settings = vcex_get_carousel_settings( $atts, 'vcex_staff_carousel', false );
	$carousel_css = vcex_get_carousel_inline_css( $unique_classname, $carousel_settings );

	if ( $carousel_css ) {
		$output .= $carousel_css;
	}

	// Enqueue scripts.
	vcex_enqueue_carousel_scripts();

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$title   = ( ! $title ) ? 'true' : $title;
	$excerpt = ( ! $excerpt ) ? 'true' : $excerpt;

	// Prevent auto play in visual composer.
	if ( $is_inline ) {
		$atts['auto_play'] = false;
	}

	// Items to scroll fallback for old setting.
	if ( 'page' === $items_scroll ) {
		$items_scroll = $items;
	}

	// Main Classes.
	$wrap_class = [
		'vcex-module',
		'wpex-carousel',
		'wpex-carousel-staff',
		'wpex-clr',
	];

	if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
		$wrap_class[] = 'owl-carousel';
	}

	if ( $carousel_css ) {
		$wrap_class[] = 'wpex-carousel--render-onload';
		$wrap_class[] = $unique_classname;
	}

	// Main carousel style & arrow position.
	if ( $style && 'default' != $style ) {
		$wrap_class[] = $style;
		$arrows_position = ( 'no-margins' == $style && 'default' == $arrows_position ) ? 'abs' : $arrows_position;
	}

	if ( $bottom_margin ) {
		$wrap_class[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
	}

	// Content alignment.
	if ( $content_alignment ) {
		$wrap_class[] = vcex_parse_text_align_class( $content_alignment );
	}

	// Arrow style.
	$arrows_style = $arrows_style ?: 'default';
	$wrap_class[] = 'arrwstyle-' . sanitize_html_class( $arrows_style );

	// Arrow position.
	if ( $arrows_position && 'default' != $arrows_position ) {
		$wrap_class[] = 'arrwpos-' . sanitize_html_class( $arrows_position );
	}

	// Visibility.
	if ( $visibility_class = vcex_parse_visibility_class( $visibility ) ) {
		$wrap_class[] = $visibility_class;
	}

	// CSS animation.
	if ( $css_animation_class = vcex_get_css_animation( $css_animation ) ) {
		$wrap_class[] = $css_animation_class;
	}

	// Extra classes.
	if ( $el_class = vcex_get_extra_class( $classes ) ) {
		$wrap_class[] = $el_class;
	}

	// Entry media classes.
	if ( 'true' == $media && 'lightbox' === $thumbnail_link ) {
		vcex_enqueue_lightbox_scripts();
		if ( 'true' == $lightbox_gallery ) {
			$wrap_class[] = 'wpex-carousel-lightbox';
		}
	}

	// Parse older content styles.
	$content_style = array(
		'opacity' => $content_opacity,
		'background_color' => $atts['content_background_color'],
		'border_color' => $atts['content_border_color'],
	);

	if ( empty( $content_css ) ) {

		if ( ! empty( $content_background ) ) {
			$content_style['background'] = $content_background;
		}

		if ( ! empty( $content_padding ) ) {
			$content_style['padding'] = $content_padding;
		}

		if ( ! empty( $content_margin ) ) {
			$content_style['margin'] = $content_margin;
		}

		if ( ! empty( $content_border ) ) {
			$content_style['border'] = $content_border;
		}

	}

	$content_style = vcex_inline_style( $content_style );

	// Social links style.
	if ( 'true' == $social_links ) {
		$social_links_inline_css = vcex_inline_style( array(
			'margin' => $social_links_margin,
		), false );
	}

	// Title design.
	if ( 'true' == $title ) {

		$heading_style = vcex_inline_style( array(
			'margin'         => $content_heading_margin,
			'text_transform' => $content_heading_transform,
			'font_weight'    => $content_heading_weight,
			'font_size'      => $content_heading_size,
			'line_height'    => $content_heading_line_height,
			'color'          => $content_heading_color,
		) );

	}

	// Readmore design and classes.
	if ( 'true' == $read_more ) {

		// Readmore text.
		$read_more_text = $read_more_text ?: esc_html__( 'Read more', 'total-theme-core' );

		// Readmore classes.
		$readmore_classes = vcex_get_button_classes( $readmore_style, $readmore_style_color );

		// Readmore style.
		$readmore_style = vcex_inline_style( array(
			'background'    => $readmore_background,
			'color'         => $readmore_color,
			'font_size'     => $readmore_size,
			'padding'       => $readmore_padding,
			'border_radius' => $readmore_border_radius,
			'margin'        => $readmore_margin,
		) );

		// Readmore data.
		$readmore_hover_data = array();
		if ( $readmore_hover_background ) {
			$readmore_hover_data['background'] = esc_attr( vcex_parse_color( $readmore_hover_background ) );
		}
		if ( $readmore_hover_color ) {
			$readmore_hover_data['color'] = esc_attr( vcex_parse_color( $readmore_hover_color ) );
		}
		if ( $readmore_hover_data ) {
			$readmore_hover_data = htmlspecialchars( wp_json_encode( $readmore_hover_data ) );
		}

	}

	// Disable autoplay
	if ( $is_inline || '1' == count( $vcex_query->posts ) ) {
		$atts['auto_play'] = false;
	}

	// Turn array to strings
	$wrap_class = implode( ' ', $wrap_class );

	// VC filter
	$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_staff_carousel', $atts );

	// Wrap Style.
	$wrap_style = vcex_inline_style( array(
		'animation_delay'    => $atts['animation_delay'],
		'animation_duration' => $atts['animation_duration'],
	) );

	// Display header if enabled
	if ( $header ) {

		$output .= vcex_get_module_header( array(
			'style'   => $header_style,
			'content' => $header,
			'classes' => array( 'vcex-module-heading vcex_staff_carousel-heading' ),
		) );

	}

	/*--------------------------------*/
	/* [ Start Output ]
	/*--------------------------------*/
	$output .= '<div class="' . esc_attr( $wrap_class ) . '" data-wpex-carousel="' . vcex_carousel_settings_to_json( $carousel_settings ) . '"' . vcex_get_unique_id( $unique_id ) . $wrap_style . '>';

		// Loop through posts
		$lcount = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) :

			// Get post from query
			$vcex_query->the_post();

			// Post VARS
			$atts['post_id']        = get_the_ID();
			$atts['post_title']     = get_the_title( $atts['post_id'] );
			$atts['post_permalink'] = vcex_get_permalink( $atts['post_id'] );
			$atts['post_esc_title'] = esc_attr( $atts['post_title'] );

			/*--------------------------------*/
			/* [ Entry ]
			/*--------------------------------*/
			if ( ( 'true' == $media && has_post_thumbnail() )
				|| 'true' == $title
				|| 'true' == $show_categories
				|| 'true' == $position
				|| 'true' == $excerpt
				|| 'true' == $social_links
				|| 'true' == $read_more
			) :

				$output .= '<div class="wpex-carousel-slide wpex-clr">';

					/*--------------------------------*/
					/* [ Featured Image ]
					/*--------------------------------*/
					$media_output = '';
					if ( 'true' == $media && has_post_thumbnail() ) {

						$atts['media_type'] = 'thumbnail';

						$thumbnail_class = implode( ' ' , vcex_get_entry_thumbnail_class(
							array( 'wpex-carousel-entry-img' ),
							'vcex_staff_carousel',
							$atts
						) );

						// Generate featured image
						$thumbnail = vcex_get_post_thumbnail( array(
							'size'          => $img_size,
							'crop'          => $img_crop,
							'width'         => $img_width,
							'height'        => $img_height,
							'class'         => $thumbnail_class,
							'lazy'          => false,
							'apply_filters' => 'vcex_staff_carousel_thumbnail_args',
						) );

						$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'wpex-carousel-entry-media' ), 'vcex_staff_carousel', $atts ) ) ) . '">';

							// No links
							if ( in_array( $thumbnail_link, array( 'none', 'nowhere' ) ) ) {

								$media_output .= $thumbnail;

								$media_output .= vcex_get_entry_media_after( 'vcex_staff_carousel' );

							}
							// Lightbox
							elseif ( 'lightbox' == $thumbnail_link ) {

								$lcount ++;

								$link_attrs = array(
									'href'       => vcex_get_lightbox_image(),
									'class'      => 'wpex-carousel-entry-img',
									'title'      => $atts['post_esc_title'],
									'data-title' => $atts['post_esc_title'],
									'data-count' => $lcount,
								);

								if ( 'true' == $lightbox_gallery ) {
									$link_attrs['class'] .= ' wpex-carousel-lightbox-item';
								} else {
									$link_attrs['class'] .= ' wpex-lightbox';
								}

								$media_output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

									$media_output .= $thumbnail;

							}
							// Link to post
							else {

								$media_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '" title="' . $atts['post_esc_title'] . '" class="wpex-carousel-entry-img">';

									$media_output .= $thumbnail;

							}

							// Inner Overlay
							$media_output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_staff_carousel', $atts );

							// Close link
							if ( 'none' !== $thumbnail_link && 'nowhere' !== $thumbnail_link ) {
								$media_output .= vcex_get_entry_media_after( 'vcex_staff_carousel' );
								$media_output .= '</a>';
							}

							// Outside Overlay
							$media_output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_staff_carousel', $atts );

						$media_output .= '</div>';

					} // End media

					$output .= apply_filters( 'vcex_staff_carousel_media', $media_output, $atts );

					/*--------------------------------*/
					/* [ Details ]
					/*--------------------------------*/
					if ( 'true' == $title
						|| 'true' == $show_categories
						|| 'true' == $position
						|| 'true' == $excerpt
						|| 'true' == $social_links
						|| 'true' == $read_more
					) {

						$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( array( 'wpex-carousel-entry-details' ), 'vcex_staff_carousel', $atts ) ) ) . '"' . $content_style . '>';

							/*--------------------------------*/
							/* [ Title ]
							/*--------------------------------*/
							$title_output = '';
							if ( 'true' == $title ) {
								$title_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_title_class( array( 'wpex-carousel-entry-title' ), 'vcex_staff_carousel', $atts ) ) ) . '"' . $heading_style . '>';
									if ( 'nowhere' == $title_link ) {
										$title_output .= wp_kses_post( $atts['post_title'] );
									} else {
										$title_output .= '<a class="" href="' . esc_url( $atts['post_permalink'] ) . '">';
											$title_output .= wp_kses_post( $atts['post_title'] );
										$title_output .= '</a>';
									}
								$title_output .= '</div>';
							}

							$output .= apply_filters( 'vcex_staff_carousel_title', $title_output, $atts );

							/*--------------------------------*/
							/* [ Position ]
							/*--------------------------------*/
							$position_output = '';
							if ( 'true' == $position ) {
								if ( $first_run ) {
									$position_style = vcex_inline_style( array(
										'font_size'   => $position_size,
										'font_weight' => $position_weight,
										'margin'      => $position_margin,
										'color'       => $position_color,
									) );
								}
								if ( $get_position = get_post_meta( $atts['post_id'], 'wpex_staff_position', true ) ) {
									$position_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_staff_position_class( array( 'wpex-carousel-entry-staff-position' ), 'vcex_staff_carousel', $atts ) ) ) . '"' . $position_style . '>';
										$position_output .= apply_filters( 'wpex_staff_entry_position', vcex_parse_text_safe( $get_position ) );
									$position_output .= '</div>';
								}
							}
							$output .= apply_filters( 'vcex_staff_carousel_position', $position_output, $atts );

							/*--------------------------------*/
							/* [ Categories ]
							/*--------------------------------*/
							$categories_output = '';
							if ( 'true' == $show_categories ) {

								if ( $first_run ) {
									$categories_style = vcex_inline_style( array(
										'padding'   => $categories_margin,
										'font_size' => $categories_font_size,
										'color'     => $categories_color,
									) );
								}

								$categories_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_categories_class( array( 'wpex-carousel-entry-categories', 'staff-entry-categories' ), 'vcex_staff_carousel', $atts ) ) ) . '"' . $categories_style . '>';

									if ( 'true' == $show_first_category_only ) {

										$categories_output .= vcex_get_first_term_link( $atts['post_id'], 'staff_category' );

									} else {

										$categories_output .= vcex_get_list_post_terms( 'staff_category', true );

									}

								$categories_output .= '</div>';

							}

							$output .= apply_filters( 'vcex_staff_carousel_categories', $categories_output, $atts );

							/*--------------------------------*/
							/* [ Excerpt ]
							/*--------------------------------*/
							$excerpt_output = '';
							if ( 'true' == $excerpt ) {

								if ( $first_run ) {
									$excerpt_styling = vcex_inline_style( array(
										'color'     => $content_color,
										'font_size' => $content_font_size,
									) );
								}

								// Generate excerpt
								$atts['post_excerpt'] = vcex_get_excerpt( array(
									'length'  => intval( $excerpt_length ),
									'context' => 'vcex_staff_carousel',
								) );

								// Display excerpt if there is one
								if ( $atts['post_excerpt'] ) {

									$excerpt_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( array( 'wpex-carousel-entry-excerpt' ), 'vcex_staff_carousel', $atts ) ) ) . '"' . $excerpt_styling . '>';

										$excerpt_output .= $atts['post_excerpt'];

									$excerpt_output .= '</div>';

								}

								$output .= apply_filters( 'vcex_staff_carousel_excerpt', $excerpt_output, $atts );

							} // End excerpt

							/*--------------------------------*/
							/* [ Social ]
							/*--------------------------------*/
							$social_output = '';
							if ( 'true' == $social_links ) {

								if ( function_exists( 'wpex_get_staff_social' ) ) {

									$social_output .= wpex_get_staff_social( array(
										'style'        => $social_links_style,
										'font_size'    => $social_links_size,
										'inline_style' => $social_links_inline_css,
									) );

								}

								$output .= apply_filters( 'vcex_staff_carousel_social', $social_output, $atts );

							} // End social check

							/*--------------------------------*/
							/* [ Read More ]
							/*--------------------------------*/
							$readmore_output = '';
							if ( 'true' == $read_more ) {

								$readmore_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_button_wrap_class( array( 'wpex-carousel-entry-button' ), 'vcex_staff_carousel', $atts ) ) ) . '">';

									$attrs = array(
										'href'  => esc_url( $atts['post_permalink'] ),
										'class' => $readmore_classes,
										'style' => $readmore_style,
									);

									if ( $readmore_hover_data ) {
										$attrs['data-wpex-hover'] = $readmore_hover_data;
									}

									$aria_label = sprintf( esc_attr_x( '%s about %s', '*read more text* about *post name* aria label', 'total-theme-core' ), $read_more_text, $atts['post_esc_title'] );

									/**
									 * Filters the more_link aria label.
									 *
									 * @param string $aria_label
									 */
									$aria_label = apply_filters( 'wpex_aria_label', $aria_label, 'more_link' );
									$aria_label = apply_filters( 'vcex_staff_carousel_readmore_aria_label', $aria_label, $atts );

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

								$output .= apply_filters( 'vcex_staff_carousel_readmore', $readmore_output, $atts );

							} // End readmore check

						$output .= '</div>';

					} // End content

				$output .= '</div>';

			endif;

		// End loop
		$first_run = false;
		endwhile;

	$output .= '</div>';

	// Reset the post data to prevent conflicts with WP globals
	wp_reset_postdata();

	// @codingStandardsIgnoreLine
	echo $output;

// If no posts are found display message
else :

	// Display no posts found error if function exists
	echo vcex_no_posts_found_message( $atts );

// End post check
endif;
