<?php

/**
 * vcex_blog_carousel shortcode output.
 *
 * @package Total Theme Core
 * @subpackage VCEX
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Define output var.
$output = '';

// Add before carousel action.
do_action( 'vcex_blog_carousel_before', $atts );

// Define extra vars for the query.
$atts['post_type'] = 'post';
$atts['taxonomy']  = 'category';
$atts['tax_query'] = '';

// Build the WordPress query.
$vcex_query = vcex_build_wp_query( $atts, 'vcex_blog_carousel' );

// Output posts.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// Enqueue carousel script and define carousel data.
	vcex_enqueue_carousel_scripts();
	$carousel_settings = vcex_get_carousel_settings( $atts, 'vcex_blog_carousel', false );
	$unique_classname  = $atts['vcex_class'] ?? vcex_element_unique_classname();
	$carousel_css      = vcex_get_carousel_inline_css( $unique_classname, $carousel_settings );

	// Define main vars.
	$show_media     = vcex_validate_att_boolean( 'media', $atts, true, true );
	$show_title     = vcex_validate_att_boolean( 'title', $atts, true, true );
	$show_date      = vcex_validate_att_boolean( 'date', $atts, true, true );
	$show_excerpt   = vcex_validate_att_boolean( 'excerpt', $atts, true, true );
	$show_read_more = vcex_validate_att_boolean( 'read_more', $atts, false, true );
	$thumbnail_link = ! empty( $atts['thumbnail_link'] ) ? $atts['thumbnail_link'] : 'post';
	$carousel_style = ! empty( $atts['style'] ) ? $atts['style'] : '';

	// Main Classes.
	$wrap_class = [
		'vcex-blog-carousel',
		'wpex-carousel',
		'wpex-carousel-blog',
		'wpex-clr',
		'vcex-module',
	];

	if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
		$wrap_class[] = 'owl-carousel';
	}

	if ( $carousel_css ) {
		$wrap_class[] = 'wpex-carousel--render-onload';
		if ( ! isset( $atts['vcex_class'] ) ) {
			$wrap_class[] = $unique_classname;
		}
	}

	if ( ! empty( $atts['bottom_margin'] ) ) {
		$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
	}

	// Lightbox.
	if ( 'lightbox' === $thumbnail_link && $show_media ) {
		vcex_enqueue_lightbox_scripts();
		$has_lightbox_gallery = vcex_validate_att_boolean( 'lightbox_gallery', $atts, true );
		if ( $has_lightbox_gallery ) {
			$wrap_class[] = 'wpex-carousel-lightbox';
		}
	}

	// Carousel style.
	if ( $carousel_style && 'default' !== $carousel_style ) {
		$wrap_class[] = sanitize_html_class( $carousel_style );
	}

	// Arrow classes.
	if ( vcex_validate_att_boolean( 'arrows', $atts, true ) ) {
		$arrows_position = ! empty( $atts['arrows_position'] ) ? $atts['arrows_position'] : 'default';
		$arrows_style = ! empty( $atts['arrows_style'] ) ? $atts['arrows_style'] : 'default';

		if ( 'no-margins' === $carousel_style && 'default' === $arrows_position ) {
			$arrows_position = 'abs';
		}

		$wrap_class[] = sanitize_html_class( "arrwstyle-{$arrows_style}" );
		$wrap_class[] = sanitize_html_class( "arrwpos-{$arrows_position}" );
	}


	// CSS animation.
	if ( ! empty( $atts['css_animation'] ) ) {
		$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
	}

	// Extra classes.
	if ( ! empty( $atts['classes'] ) ) {
		$wrap_class[] = vcex_get_extra_class( $atts['classes'] );
	}

	// Visibility.
	if ( ! empty( $atts['visibility'] ) ) {
		$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
	}

	// Convert arrays to strings.
	$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_blog_carousel', $atts );

	// Display header if enabled.
	if ( ! empty( $atts['header'] ) ) {
		$output .= vcex_get_module_header( [
			'style'   => $atts['header_style'] ?? '',
			'content' => $atts['header'],
			'classes' => [ 'vcex-module-heading vcex_blog_carousel-heading' ],
		] );
	}

	// Carousel CSS needs to be before header so it's removed on load.
	if ( $carousel_css ) {
		$output .= $carousel_css;
	}

	/*** Begin Carousel Output ***/
	$output .= '<div class="' . esc_attr( $wrap_class ) . '" data-wpex-carousel="' . vcex_carousel_settings_to_json( $carousel_settings ) . '"' . vcex_get_unique_id( $atts ) . '>';

		// Start loop.
		$lcount = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) : $vcex_query->the_post();

			// Define post data.
			$atts['post_id']             = get_the_ID();
			$atts['post_permalink']      = vcex_get_permalink( $atts['post_id'] );
			$atts['post_title']          = get_the_title();
			$atts['post_esc_title']      = vcex_esc_title( $atts['post_id'] );
			$atts['post_thumbnail']      = get_post_thumbnail_id( $atts['post_id'] );
			$atts['post_thumbnail_link'] = $atts['post_permalink'];

			// Lets store the dynamic $atts['post_id'] into the shortcodes attributes.
			$atts['post_id'] = $atts['post_id'];

			/*** Begin Entry output ***/
			if ( ( $show_media && $atts['post_thumbnail'] ) || $show_title || $show_date || $show_excerpt ) :

				// Entry classes.
				$entry_classes = [
					'wpex-carousel-slide',
				];

				// Alignment.
				if ( ! empty( $atts['content_alignment'] ) ) {
					$entry_classes[] = sanitize_html_class( "wpex-text-{$atts['content_alignment']}" );
				}

				if ( $atts['post_thumbnail'] ) {
					$entry_classes[] = 'has-media';
				}

				$output .= '<div class="' . esc_attr( implode( ' ', $entry_classes ) ) . '">';

					/*** Featured Image ***/
					if ( $show_media ) {

						$media_html = '';
						$has_video_icon = false;

						if ( $atts['post_thumbnail'] ) {

							$atts['media_type'] = 'thumbnail';

							$media_html .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( [ 'wpex-carousel-entry-media' ], 'vcex_blog_carousel', $atts ) ) ) . '">';

								// If thumbnail link doesn't equal none.
								if ( 'none' != $thumbnail_link ) :

									// Link attributes.
									$link_attrs = [
										'href'  => '',
										'title' => $atts['post_esc_title'],
										'class' => 'wpex-carousel-entry-img',
									];

									// Add lightbox link attributes.
									if ( 'lightbox' == $thumbnail_link ) {

										$lcount++;

										$atts['lightbox_data']  = []; // must reset for each item
										$lightbox_image_escaped = vcex_get_lightbox_image();
										$atts['lightbox_link']  = $lightbox_image_escaped;

										if ( $has_lightbox_gallery ) {
											$link_attrs['class'] .= ' wpex-carousel-lightbox-item';
										} else {
											$link_attrs['class'] .= ' wpex-lightbox';
										}

										// Check for video.
										if ( $embed_url = vcex_get_video_embed_url( vcex_get_post_video_oembed_url( $atts['post_id'] ) ) ) {
										//	$has_video_icon = true; // @todo add support for this?
											$atts['lightbox_link'] = esc_url( $embed_url );
											$atts['lightbox_data']['data-thumb'] = 'data-thumb="' . $lightbox_image_escaped . '"';
										}

										$link_attrs['data-title']    = $atts['post_esc_title'];
										$link_attrs['data-count']    = intval( $lcount );
										$atts['post_thumbnail_link'] = $atts['lightbox_link'];
									}

									$link_attrs['href'] = esc_url( $atts['post_thumbnail_link'] );

									if ( ! empty( $atts['lightbox_data'] ) ) {
										foreach ( $atts['lightbox_data'] as $ld_k => $ld_v ) {
											$link_attrs[ $ld_k ] = $ld_v;
										}
									}

								$media_html .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

								endif; // End thumbnail_link check.

								// Thumbnail Args.
								$thumbnail_args = [
									'attachment'    => $atts['post_thumbnail'],
									'width'         => $atts['img_width'] ?? '',
									'height'        => $atts['img_height'] ?? '',
									'size'          => $atts['img_size'] ?? '',
									'crop'          => $atts['img_crop'] ?? '',
									'lazy'          => false,
									'class'         => implode( ' ', vcex_get_entry_thumbnail_class( null, 'vcex_blog_carousel', $atts ) ),
									'apply_filters' => 'vcex_blog_carousel_thumbnail_args',
									'filter_arg1'   => $atts,
								];

								// Display post thumbnail.
								$media_html .= vcex_get_post_thumbnail( $thumbnail_args );

								// Inner link overlay.
								$media_html .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_blog_carousel', $atts );

								// Entry after media hook.
								$media_html .= vcex_get_entry_media_after( 'vcex_blog_carousel' );

								if ( $has_video_icon && ( empty( $atts['overlay_style'] ) || 'none' === $atts['overlay_style'] ) ) {
									$media_html .= vcex_get_image_overlay( 'inside_link', 'video-icon' );
								}

								// Close link tag.
								if ( 'none' != $thumbnail_link ) {
									$media_html .= '</a>';
								}

								// Outer link overlay.
								$media_html .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_blog_carousel', $atts );

							$media_html .= '</div>';

						}

						$output .= apply_filters( 'vcex_blog_carousel_media', $media_html, $atts );

					} // End media check.

					/*** Entry Details ***/
					if ( $show_title || $show_date || $show_excerpt || $show_read_more ) {

						if ( $first_run ) {
							// Deprecated content css.
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

						$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( [ 'wpex-carousel-entry-details' ], 'vcex_blog_carousel', $atts ) ) ) . '"' . $content_style . '>';

							/*** Entry Title ***/
							if ( $show_title ) {
								$title_html = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_title_class( ['wpex-carousel-entry-title' ], 'vcex_blog_carousel', $atts ) ) ) . '">';
									$title_html .= '<a href="' . esc_url( $atts['post_permalink'] ) . '">';
										$title_html .= wp_kses_post( $atts['post_title'] );
									$title_html .= '</a>';
								$title_html .= '</div>';
								$output .= apply_filters( 'vcex_blog_carousel_title', $title_html, $atts );
							}

							/*** Entry Date ***/
							if ( $show_date ) {
								$date_html = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_date_class( [ 'wpex-carousel-entry-date', 'vcex-blog-entry-date' ], 'vcex_blog_carousel', $atts ) ) ) . '">'; // @todo deprecate vcex-blog-entry-date?
									$date_html .= get_the_date();
								$date_html .= '</div>';
								$output .= apply_filters( 'vcex_blog_carousel_date', $date_html, $atts );
							}

							/*** Entry Excerpt ***/
							if ( $show_excerpt ) {
								$excerpt_html = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( [ 'wpex-carousel-entry-excerpt' ], 'vcex_blog_carousel', $atts ) ) ) . '">';
									$excerpt_html .= vcex_get_excerpt( [
										'length'  => ! empty( $atts['excerpt_length'] ) ? $atts['excerpt_length'] : '15',
										'context' => 'vcex_blog_carousel',
									] );
								$excerpt_html .= '</div>';
								$output .= apply_filters( 'vcex_blog_carousel_excerpt', $excerpt_html, $atts );
							}

							/*** Entry Readmore ***/
							if ( $show_read_more ) {
								if ( $first_run ) {
									$read_more_text = ! empty( $atts['read_more_text'] ) ? $atts['read_more_text'] : esc_html__( 'Read more', 'total-theme-core' );
									$readmore_classes = vcex_get_button_classes( $atts['readmore_style'] ?? '', $atts['readmore_style_color'] ?? '' );
								}

								$readmore_html = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_button_wrap_class( [ 'wpex-carousel-entry-button' ], 'vcex_blog_carousel', $atts ) ) ) . '">';

									$readmore_attrs = [
										'href'  => $atts['post_permalink'],
										'class' => "entry-readmore {$readmore_classes}",
									];

									$aria_label = sprintf( esc_attr_x( '%s about %s', '*read more text* about *post name* aria label', 'total-theme-core' ), $read_more_text, $atts['post_esc_title'] );
									$aria_label = apply_filters( 'wpex_aria_label', $aria_label, 'more_link' );
									$aria_label = apply_filters( 'vcex_blog_carousel_readmore_aria_label', $aria_label, $atts );

									if ( $aria_label ) {
										$readmore_attrs['aria-label'] = strip_shortcodes( $aria_label );
									}

									$readmore_html .= '<a' . vcex_parse_html_attributes( $readmore_attrs ) . '>';

										$readmore_html .= vcex_parse_text_safe( $read_more_text );

										if ( vcex_validate_att_boolean( 'readmore_rarr', $atts ) ) {
											$readmore_html .= ' <span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
										}

									$readmore_html .= '</a>';

								$readmore_html .= '</div>';

								$output .= apply_filters( 'vcex_blog_carousel_readmore', $readmore_html, $atts );
							}

						$output .= '</div>';

					} // End details check.

				$output .= '</div>';

			endif; // End data check.

		// End entry loop.
		$first_run = false; endwhile;

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
