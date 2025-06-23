<?php

/**
 * vcex_post_type_flexslider shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$html = '';

// Check caption location early because it affects the query.
$caption_location = ! empty( $atts['caption_location'] ) ? $atts['caption_location'] : 'over-image';

// Query posts with thumbnails_only.
if ( 'over-image' === $caption_location ) {
	$atts['thumbnail_query'] = 'true';
}

// Build the WordPress query.
$vcex_query = vcex_build_wp_query( $atts, 'vcex_post_type_flexslider' );

//Output posts.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// Sanitize data, declare main vars & fallbacks
	$wrap_data = array();
	$has_caption = vcex_validate_att_boolean( 'caption', $atts, true, true );
	$overlay_style  = $atts['overlay_style'] ?? '';
	$has_title = vcex_validate_att_boolean( 'title', $atts, true, true );
	$has_meta = vcex_validate_att_boolean( 'meta', $atts, true );
	$has_excerpt = vcex_validate_att_boolean( 'excerpt', $atts, true );
	$excerpt_length = $atts['excerpt_length'] ?? '15';

	// Slider vars.
	$animation_speed         = ! empty( $atts['animation_speed'] ) ? $atts['animation_speed'] : '600';
	$slideshow_speed         = ! empty( $atts['slideshow_speed'] ) ? $atts['slideshow_speed'] : '5000';
	$animation               = ! empty( $atts['animation'] ) ? $atts['animation'] : 'slide';
	$height_animation        = $atts['height_animation'] ?? 500;
	$slideshow               = vcex_vc_is_inline() ? 'false' : vcex_validate_att_boolean( 'slideshow', $atts, true );
	$randomize               = vcex_validate_att_boolean( 'randomize', $atts, false );
	$control_thumbs          = vcex_validate_att_boolean( 'control_thumbs', $atts, true );
	$control_thumbs_carousel = vcex_validate_att_boolean( 'control_thumbs_carousel', $atts, true );

	if ( $control_thumbs ) {
		$control_thumbs_height = ! empty( $atts['control_thumbs_height'] ) ? intval( $atts['control_thumbs_height'] ) : '70';
		$control_thumbs_width  = ! empty( $atts['control_thumbs_width'] ) ? intval( $atts['control_thumbs_width'] ) : '70';
	}

	// Caption vars.
	if ( $has_caption ) {
		$caption_breakpoint  = ! empty( $atts['caption_breakpoint'] ) ? $atts['caption_breakpoint'] : 'md';
		$caption_bkp_escaped = sanitize_html_class( $caption_breakpoint );
		$caption_opacity     = ! empty( $atts['caption_opacity'] ) ? absint( $atts['caption_opacity'] ) : '80';
	}

	// Load slider scripts.
	if ( vcex_vc_is_inline() ) {
		vcex_enqueue_slider_scripts();
		vcex_enqueue_slider_scripts( true ); // needs both in builder incase user switches settings.
	} else {
		vcex_enqueue_slider_scripts( $control_thumbs_carousel ? false : true );
	}

	// Slider attributes
	if ( 'fade' === $animation || 'fade_slides' === $animation ) {
		$wrap_data[] = 'data-fade="true"';
	}

	if ( apply_filters( 'vcex_sliders_disable_desktop_swipe', true, 'vcex_post_type_flexslider' ) ) {
		$wrap_data[] = 'data-touch-swipe-desktop="false"';
	}

	if ( $randomize ) {
		$wrap_data[] = 'data-shuffle="true"';
	}

	if ( vcex_validate_att_boolean( 'loop', $atts, false ) ) {
		$wrap_data[] = ' data-loop="true"';
	}

	if ( ! $slideshow ) {
		$wrap_data[] = 'data-auto-play="false"';
	}

	if ( $slideshow && $slideshow_speed ) {
		$wrap_data[] = 'data-auto-play-delay="' . esc_attr( $slideshow_speed ) . '"';
	}

	if ( ! vcex_validate_att_boolean( 'direction_nav', $atts, true ) ) {
		$wrap_data[] = 'data-arrows="false"';
	}

	if ( ! vcex_validate_att_boolean( 'control_nav', $atts, true ) ) {
		$wrap_data[] = 'data-buttons="false"';
	}

	if ( ! vcex_validate_att_boolean( 'direction_nav_hover', $atts, true ) ) {
		$wrap_data[] = 'data-fade-arrows="false"';
	}

	if ( $control_thumbs ) {
		$wrap_data[] = 'data-thumbnails="true"';

		if ( vcex_validate_att_boolean( 'control_thumbs_pointer', $atts, false ) ) {
			$wrap_data[] = 'data-thumbnail-pointer="true"';
		}

		if ( $control_thumbs_height ) {
			$wrap_data[] = 'data-thumbnail-height="' . esc_attr( $control_thumbs_height ) . '"';
		}

		if ( $control_thumbs_width ) {
			$wrap_data[] = 'data-thumbnail-width="' . esc_attr( $control_thumbs_width ) . '"';
		}

	}

	if ( $animation_speed ) {
		$wrap_data[] = 'data-animation-speed="' . intval( $animation_speed ) . '"';
	}

	if ( $height_animation ) {
		$height_animation = intval( $height_animation );
		if ( 0 == $height_animation ) {
			$height_animation = '0.0';
		}
		$wrap_data[] = 'data-height-animation-duration="' . esc_attr( $height_animation ) . '"';
	}

	// Main Classes.
	$wrap_classes = array(
		'vcex-module',
		'vcex-posttypes-slider',
		'wpex-slider',
		'slider-pro',
		'vcex-image-slider',
	);

	if ( ! empty( $atts['bottom_margin'] ) ) {
		$wrap_classes[] = vcex_parse_margin_class( $atts['bottom_margin'], 'wpex-mb-' );
	}

	if ( ! empty( $atts['classes'] ) ) {
		$wrap_classes[] = vcex_get_extra_class( $atts['classes'] );
	}

	if ( 'under-image' === $caption_location ) {
		$wrap_classes[] = 'arrows-topright';
	}

	if ( ! empty( $atts['visibility'] ) ) {
		$wrap_classes[] = vcex_parse_visibility_class( $atts['visibility'] );
	}

	if ( $has_excerpt && $excerpt_length ) {
		$wrap_classes[] = 'vcex-posttypes-slider-w-excerpt';
	}

	if ( $control_thumbs ) {
		$wrap_classes[] = 'vcex-posttypes-slider-w-thumbnails';
	}

	$wrap_classes[] = 'wpex-clr';

	// Apply filters.
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_post_type_flexslider', $atts );

	// Open css wrapper.
	if ( ! empty( $atts['css'] ) ) {
		$html .= '<div class="vcex-posttype-slider-css-wrap ' . vcex_vc_shortcode_custom_css_class( $atts['css'] ) . '">';
	}

	// Display the first image of the slider as a "preloader".
	$first_post = $vcex_query->posts[0]->ID ?? null;
	if ( $first_post && ! $randomize ) {

		$html .= '<div class="wpex-slider-preloaderimg">';

			$html .= vcex_get_post_thumbnail( array(
				'attachment'    => get_post_thumbnail_id( $first_post ),
				'size'          => $atts['img_size'] ?? 'wpex_custom',
				'crop'          => $atts['img_crop'] ?? null,
				'width'         => $atts['img_width'] ?? null,
				'height'        => $atts['img_height'] ?? null,
				'lazy'          => false,
				'apply_filters' => 'vcex_post_type_flexslider_thumbnail_args',
			) );

		$html .= '</div>';

	}

	$html .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $atts['unique_id'] ?? null ) . implode( ' ', $wrap_data ) . '>';

		$html .= '<div class="wpex-slider-slides sp-slides">';

			// Store posts in an array for use with the thumbnails later.
			$posts_cache = array();

			// Loop through posts.
			while ( $vcex_query->have_posts() ) :

				// Get post from query.
				$vcex_query->the_post();

				if ( ! has_post_thumbnail() ) {
					continue;
				}

				// Get post data.
				$post_id   = get_the_ID();
				$post_type = get_post_type();
				$permalink = vcex_get_permalink();
				$esc_title = vcex_esc_title();

				// Store post ids.
				$posts_cache[] = $post_id;

				$html .= '<div class="wpex-slider-slide sp-slide">';

					if ( ! isset( $media_classes ) ) {

						$media_classes = array(
							'wpex-slider-media',
							'wpex-relative',
						);

						if ( ! empty( $atts['img_filter'] ) ) {
							$media_classes[] = vcex_image_filter_class( $atts['img_filter'] );
						}

						$media_classes = apply_filters( 'vcex_post_type_flexslider_media_class', $media_classes );

						$media_classes = implode( ' ', $media_classes );

					}

					$html .= '<div class="' . esc_attr( $media_classes ) . '">';

						if ( $overlay_style && 'none' !== $overlay_style ) {
							$html .= '<div class="' . vcex_image_overlay_classes( $overlay_style ) . '">';
						}

						$html .= '<a ' . vcex_parse_html_attributes( array(
							'href'   => esc_url( $permalink ),
							'title'  => $esc_title,
							'target' => $atts['link_target'] ?? null,
							'class'  => 'wpex-slider-media-link',
						) ) . '>';

							$html .= vcex_get_post_thumbnail( array(
								'lazy'          => false,
								'size'          => $atts['img_size'] ?? 'wpex_custom',
								'crop'          => $atts['img_crop'] ?? null,
								'width'         => $atts['img_width'] ?? null,
								'height'        => $atts['img_height'] ?? null,
								'apply_filters' => 'vcex_post_type_flexslider_thumbnail_args',
							) );

							// Inner overlay.
							$html .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_post_type_flexslider', $atts );

						$html .= '</a>';

						// Outer overlay.
						$html .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_post_type_flexslider', $atts );

						if ( $overlay_style && 'none' !== $overlay_style ) {
							$html .= '</div>';
						}

						// WooComerce Price.
						if ( 'product' === $post_type ) {
							$html .= '<div class="vcex-posttype-slider-price wpex-absolute wpex-right-0 wpex-top-0 wpex-mt-20 wpex-mr-20 wpex-py-5 wpex-px-10 wpex-bg-accent wpex-backface-hidden">';
								$html .= vcex_get_woo_product_price();
							$html .= '</div>';
						}

						if ( $has_caption ) {

							if ( ! isset( $caption_classes ) ) {

								$caption_classes = array(
									'vcex-posttypes-slider-caption',
									'wpex-backface-hidden',
									sanitize_html_class( $caption_location ),
								);

								if ( ! empty( $atts['caption_visibility'] ) ) {
									$caption_classes[] = sanitize_html_class( $atts['caption_visibility'] );
								}

								switch ( $caption_location ) {
									case 'over-image':
										$caption_classes[] = 'wpex-z-1';
										$caption_classes[] = 'wpex-text-gray-500';
										$caption_classes[] = "wpex-{$caption_bkp_escaped}-absolute";
										$caption_classes[] = "wpex-{$caption_bkp_escaped}-bottom-0";
										$caption_classes[] = "wpex-{$caption_bkp_escaped}-inset-x-0";
										$caption_classes[] = 'wpex-p-20';
										break;
									case 'under-image':
										if ( $control_thumbs ) {
											$caption_classes[] = 'wpex-mt-20';
											$caption_classes[] = 'wpex-mb-15';
										} else {
											$caption_classes[] = 'wpex-mt-20';
										}
										break;
								} // end switch $caption_location.

								$caption_classes[] = 'wpex-last-mb-0';

								$caption_classes = apply_filters( 'vcex_post_type_flexslider_caption_class', $caption_classes );

								$caption_classes = implode( ' ', $caption_classes );

							}

							$html .= '<div class="' . esc_attr( $caption_classes ) . '">';

								if ( 'over-image' === $caption_location ) {

									if ( ! isset( $caption_bg_classes ) ) {

										$caption_bg_classes = [
											'vcex-posttype-slider-caption-bg',
											'wpex-absolute',
											'wpex-inset-0',
											'-wpex-z-1',
											'wpex-bg-black',
											sanitize_html_class( "wpex-{$caption_bkp_escaped}-opacity-{$caption_opacity}" ),
										];

										$caption_bg_classes = apply_filters( 'vcex_post_type_flexslider_caption_class', $caption_bg_classes );

										$caption_bg_classes = implode( ' ', $caption_bg_classes );

									}

									$html .= '<div class="' . esc_attr( $caption_bg_classes ) . '"></div>';

								}


								if ( $has_title || $has_meta ) {

									$html .= '<header class="vcex-posttype-slider-header wpex-mb-10 wpex-clr">';

										// Display title.
										if ( $has_title ) {

											if ( ! isset( $title_class ) ) {

												$title_class = array(
													'vcex-posttype-slider-title',
													'entry-title',
													'wpex-text-xl',
													'wpex-mb-5',
												);

												switch ( $caption_location ) {
													case 'over-image':
														$title_class[] = 'wpex-text-white';
														break;
													case 'under-image':
														break;
												}

												$title_class = apply_filters( 'vcex_post_type_flexslider_title_class', $title_class );

												$title_class = implode( ' ', $title_class );

											}

											$html .= '<div class="' . esc_attr( $title_class ) . '">';

												$html .= '<a ' . vcex_parse_html_attributes( array(
													'href'   => esc_url( $permalink ),
													'title'  => $esc_title,
													'target' => $atts['link_target'] ?? null,
													'class'  => 'title',
												) ) . '>' . wp_kses_post( get_the_title() ) . '</a>';

											$html .= '</div>';

										} // End title.

										// Meta.
										if ( $has_meta ) {

											switch ( $post_type ) {

												case 'staff':

													$position = get_post_meta( $post_id, 'wpex_staff_position', true );

													$html .= '<div class="vcex-posttypes-slider-staff-position wpex-uppercase wpex-text-xs wpex-text-">';

														$html .= do_shortcode( wp_kses_post( $position ) );

													$html .= '</div>';

													break;

												default:

													$html .= '<ul class="vcex-posttypes-slider-meta meta wpex-clr">';

														$date_icon = '';

														if ( is_callable( 'TotalTheme\\Theme_Icons::get_icon' ) ) {
															$date_icon = TotalTheme\Theme_Icons::get_icon( 'clock-o' );
														}

														$html .= '<li class="meta-date">' . $date_icon . '<span class="updated">' . esc_html( get_the_date() ) . '</span></li>';

														$author_link = get_the_author_posts_link();

														if ( $author_link ) {

															$author_icon = '';

															if ( is_callable( 'TotalTheme\\Theme_Icons::get_icon' ) ) {
																$author_icon = TotalTheme\Theme_Icons::get_icon( 'user-o' );
															}

															if ( false !== strpos( $author_link, 'class="' ) ) {
																$author_link = str_replace( 'class="', 'class="wpex-inherit-color-important', $author_link );
															} else {
																$author_link = str_replace( '<a', '<a class="wpex-inherit-color-important"', $author_link );
															}

															$html .= '<li class="meta-author">' . $author_icon . '<span class="vcard author">' . $author_link . '</span></li>';

														}

														// Display category.
														if ( ! vcex_validate_att_boolean( 'tax_query', $atts, false ) ) {

															$category = vcex_get_post_type_cat_tax( $post_type );

															if ( $category ) {

																$cat_icon = '';

																if ( is_callable( 'TotalTheme\\Theme_Icons::get_icon' ) ) {
																	$cat_icon = TotalTheme\Theme_Icons::get_icon( 'folder-open-o' );
																}

																$terms = vcex_get_list_post_terms( array(
																	'taxonomy' => $category,
																	'class'    => 'wpex-inherit-color-important',
																) );

																if ( $terms ) {

																	$html .= '<li class="meta-category">' . $cat_icon . $terms . '</li>';

																}

															}

														}

													$html .= '</ul>';

													break;

											} // end $post_type switch.

										} // End meta.

									$html .= '</header>';

								}

								// Display excerpt.
								if ( $has_excerpt && $excerpt_length ) {
									$html .= '<div class="vcex-posttypes-slider-excerpt excerpt wpex-last-mb-0 wpex-clr">';
										$html .= vcex_get_excerpt( array(
											'length' => $excerpt_length,
										) );
									$html .= '</div>';
								}

								ob_start();
									do_action( 'vcex_hook_post_type_flexslider_caption_bottom', $atts );
								$html .= ob_get_clean();

							$html .= '</div>';

						}

					$html .= '</div>';

				$html .= '</div>';

			endwhile;

		$html .= '</div>';

		// Thumbnails.
		if ( $control_thumbs ) {

			$container_classes = array(
				'wpex-slider-thumbnails',
			);

			if ( $control_thumbs_carousel ) {
				$container_classes[] = 'sp-thumbnails';
			} else {
				$container_classes[] = 'sp-nc-thumbnails wpex-clr';
			}

			$html .= '<div class="' . esc_attr( implode( ' ', $container_classes ) ) . '">';

				$thumb_args = array(
					'lazy'          => false,
					'size'          => $atts['img_size'] ?? 'wpex_custom',
					'crop'          => $atts['img_crop'] ?? null,
					'width'         => $atts['img_width'] ?? null,
					'height'        => $atts['img_height'] ?? null,
					'apply_filters' => 'vcex_post_type_flexslider_nav_thumbnail_args',
				);

				if ( $control_thumbs_carousel ) {
					$thumb_args['class'] = 'wpex-slider-thumbnail sp-thumbnail';
				} else {
					$thumb_args['class'] = 'wpex-slider-thumbnail sp-nc-thumbnail';
					if ( $control_thumbs_height || $control_thumbs_width ) {
						$thumb_args['size'] = null;
						if ( $control_thumbs_width ) {
							$thumb_args['width']  = $control_thumbs_width;
						}
						if ( $control_thumbs_height ) {
							$thumb_args['height'] = $control_thumbs_height;
						}
					}
				}

				if ( ! empty( $atts['img_filter'] ) ) {
					$thumb_args['class'] .= ' ' . trim( vcex_image_filter_class( $atts['img_filter']  ) );
				}

				foreach ( $posts_cache as $post_id ) {

					$thumb_args['attachment'] = get_post_thumbnail_id( $post_id );

					$html .= vcex_get_post_thumbnail( $thumb_args );

				}

			$html .= '</div>';

		}

	$html .= '</div>';

	// Close css wrapper.
	if ( ! empty( $atts['css'] ) ) {
		$html .= '</div>';
	}

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
