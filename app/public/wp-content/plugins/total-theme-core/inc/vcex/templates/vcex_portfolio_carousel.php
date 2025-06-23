<?php

/**
 * vcex_portfolio_carousel shortcode output
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Define output.
$output = '';

// Define attributes for Query.
$atts['post_type'] = 'portfolio';
$atts['taxonomy']  = 'portfolio_category';
$atts['tax_query'] = '';

// Build the WordPress query.
$vcex_query = vcex_build_wp_query( $atts, 'vcex_portfolio_carousel' );

//Output posts.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// All carousels need a unique classname.
	$unique_classname = vcex_element_unique_classname();

	// Get carousel settings.
	$carousel_settings = vcex_get_carousel_settings( $atts, 'vcex_portfolio_carousel', false );
	$carousel_css = vcex_get_carousel_inline_css( $unique_classname, $carousel_settings );

	if ( $carousel_css ) {
		$output .= $carousel_css;
	}

	// Enqueue scripts.
	vcex_enqueue_carousel_scripts();

	// Extract attributes.
	extract( $atts );

	// IMPORTANT: Fallback required from VC update when params are defined as empty.
	// AKA - set things to enabled by default.
	$media   = ( ! $media ) ? 'true' : $media;
	$title   = ( ! $title ) ? 'true' : $title;
	$excerpt = ( ! $excerpt ) ? 'true' : $excerpt;

	// Items to scroll fallback for old setting.
	if ( 'page' === $items_scroll ) {
		$items_scroll = $items;
	}

	// Main Classes.
	$wrap_class = [
		'vcex-portfolio-carousel',
		'wpex-carousel',
		'wpex-carousel-portfolio',
		'wpex-clr',
		'vcex-module',
	];

	if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
		$wrap_class[] = 'owl-carousel';
	}

	if ( $carousel_css ) {
		$wrap_class[] = 'wpex-carousel--render-onload';
		$wrap_class[] = $unique_classname;
	}

	// Carousel style
	if ( $style && 'default' !== $style ) {
		$wrap_class[] = $style;
		$arrows_position = ( 'no-margins' === $style && 'default' === $arrows_position ) ? 'abs' : $arrows_position;
	}

	// Alignment
	if ( $content_alignment ) {
		$wrap_class[] = vcex_parse_text_align_class( $content_alignment );
	}

	// Arrow style
	$arrows_style = $arrows_style ?: 'default';
	$wrap_class[] = 'arrwstyle-' . sanitize_html_class( $arrows_style );

	// Arrow position
	if ( $arrows_position && 'default' !== $arrows_position ) {
		$wrap_class[] = 'arrwpos-' . sanitize_html_class( $arrows_position );
	}

	// Bottom margin
	if ( $bottom_margin ) {
		$wrap_class[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
	}

	// Visiblity
	if ( $visibility ) {
		$wrap_class[] = vcex_parse_visibility_class( $visibility );
	}

	// CSS animations
	if ( $css_animation_class = vcex_get_css_animation( $atts['css_animation'] ) ) {
		$wrap_class[] = $css_animation_class;
	}

	// Lightbox classes & scripts
	if ( 'lightbox' === $thumbnail_link ) {
		vcex_enqueue_lightbox_scripts();
		if ( 'true' == $lightbox_gallery ) {
			$wrap_class[] = 'wpex-carousel-lightbox';
		}
	}

	// Custom Classes
	if ( $classes ) {
		$wrap_class[] = vcex_get_extra_class( $classes );
	}

	// Disable autoplay
	if ( vcex_vc_is_inline() || '1' == count( $vcex_query->posts ) ) {
		$atts['auto_play'] = false;
	}

	// Turn arrays into strings
	$wrap_class = implode( ' ', $wrap_class );

	// Apply filters
	$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_portfolio_carousel', $atts );

	// Display header if enabled
	if ( $header ) {

		$output .= vcex_get_module_header( array(
			'style'   => $header_style,
			'content' => $header,
			'classes' => array( 'vcex-module-heading vcex_portfolio_carousel-heading' ),
		) );

	}

	/*--------------------------------*/
	/* [ Begin Carousel Output ]
	/*--------------------------------*/
	$output .= '<div class="' . esc_attr( $wrap_class ) . '" data-wpex-carousel="' . vcex_carousel_settings_to_json( $carousel_settings ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Start loop
		$lcount = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) :

			// Get post from query
			$vcex_query->the_post();

			// Post VARS
			$atts['post_id']        = get_the_ID();
			$atts['post_permalink'] = vcex_get_permalink( $atts['post_id'] );
			$atts['post_title']     = get_the_title( $atts['post_id'] );
			$atts['post_esc_title'] = vcex_esc_title();
			$atts['post_format']    = get_post_format();

			/*--------------------------------*/
			/* [ Begin Entry ]
			/*--------------------------------*/
			$output .= '<div class="wpex-carousel-slide">';

				// Display media
				if ( 'true' == $media ) :

					$media_output = '';

					/*--------------------------------*/
					/* [ Featured Image ]
					/*--------------------------------*/
					if ( has_post_thumbnail() ) {

						$atts['media_type'] = 'thumbnail';

						$thumbnail_class = implode( ' ' , vcex_get_entry_thumbnail_class(
							array( 'wpex-carousel-entry-img' ),
							'vcex_portfolio_carousel',
							$atts
						) );

						// Image html
						$img_html = vcex_get_post_thumbnail( array(
							'size'          => $img_size,
							'crop'          => $img_crop,
							'width'         => $img_width,
							'height'        => $img_height,
							'class'         => $thumbnail_class,
							'lazy'          => false,
							'apply_filters' => 'vcex_portfolio_carousel_thumbnail_args',
							'filter_arg1'   => $atts,
						) );

						$media_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'wpex-carousel-entry-media' ), 'vcex_portfolio_carousel', $atts ) ) ) . '">';

							switch ( $thumbnail_link ) {

								// No links
								case 'none':

									$media_output .= $img_html;
									$media_output .= vcex_get_entry_media_after( 'vcex_portfolio_carousel' );

									break;

								// Lightbox
								case 'lightbox':

									$lcount ++;

									$atts['lightbox_data'] = array(); // must reset for each item

									$lightbox_image_escaped = vcex_get_lightbox_image();

									$atts['lightbox_link'] = $lightbox_image_escaped;

									if ( 'true' == $lightbox_gallery ) {
										$atts['lightbox_class'] = 'wpex-carousel-lightbox-item';
									} else {
										$atts['lightbox_class'] = 'wpex-lightbox';
									}

									// Check for video
									if ( $oembed_video_url = vcex_get_post_video_oembed_url( $atts['post_id'] ) ) {
										$embed_url = vcex_get_video_embed_url( $oembed_video_url );
										if ( $embed_url ) {
											$atts['lightbox_link']               = esc_url( $embed_url );
											$atts['lightbox_data']['data-thumb'] = 'data-thumb="' . $lightbox_image_escaped . '"';
										}
									}

									$link_attrs = array(
										'href'       => $atts['lightbox_link'],
										'class'      => 'wpex-carousel-entry-img ' . $atts['lightbox_class'],
										'title'      => $atts['post_esc_title'],
										'data-title' => $atts['post_esc_title'],
										'data-count' => $lcount,
									);

									if ( ! empty( $atts['lightbox_data'] ) ) {
										foreach ( $atts['lightbox_data'] as $ld_k => $ld_v ) {
											$link_attrs[$ld_k] = $ld_v;
										}
									}

									$media_output .= '<a'. vcex_parse_html_attributes( $link_attrs ) . '>';

									$media_output .= $img_html;

									break;

								// Link to post
								default :

									$media_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '" title="' . $atts['post_esc_title'] . '" class="wpex-carousel-entry-img">';

									$media_output .= $img_html;

									break;

							} // end switch

							// Inner Overlay
							$media_output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_portfolio_carousel', $atts );

							// Entry after media hook
							$media_output .= vcex_get_entry_media_after( 'vcex_portfolio_carousel' );

							// Close link
							if ( 'none' !== $thumbnail_link ) {
								$media_output .= '</a>';
							}

							// Outside Overlay
							$media_output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_portfolio_carousel', $atts );

						$media_output .= '</div>';

					}

					$output .= apply_filters( 'vcex_portfolio_carousel_media', $media_output, $atts );

				endif;

				/*--------------------------------*/
				/* [ Entry Details ]
				/*--------------------------------*/
				if ( 'true' == $title || 'true' == $excerpt || 'true' == $read_more ) :

					$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( [ 'wpex-carousel-entry-details' ], 'vcex_portfolio_carousel', $atts ) ) ) . '">';

						/*--------------------------------*/
						/* [ Entry Title ]
						/*--------------------------------*/
						if ( 'true' == $title && $atts['post_title'] ) {
							$title_output = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_title_class( array( 'wpex-carousel-entry-title' ), 'vcex_portfolio_carousel', $atts ) ) ) . '">';
								$title_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '">';
									$title_output .= esc_html( $atts['post_title'] );
								$title_output .= '</a>';
							$title_output .= '</div>';
							$output .= apply_filters( 'vcex_portfolio_carousel_title', $title_output, $atts );
						}

						/*--------------------------------*/
						/* [ Entry Excerpt ]
						/*--------------------------------*/
						if ( 'true' == $excerpt ) {
							$excerpt_output = '';
							// Generate excerpt
							$atts['post_excerpt'] = vcex_get_excerpt( [
								'length'  => $excerpt_length,
								'context' => 'vcex_portfolio_carousel',
							] );
							if ( $atts['post_excerpt'] ) {
								$excerpt_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( array( 'wpex-carousel-entry-excerpt' ), 'vcex_portfolio_carousel', $atts ) ) ) . '">';
									$excerpt_output .= $atts['post_excerpt']; // Escaped via wp_trim_words
								$excerpt_output .= '</div>';
							}
							$output .= apply_filters( 'vcex_portfolio_carousel_excerpt', $excerpt_output, $atts );
						}

						/*--------------------------------*/
						/* [ Entry Read More ]
						/*--------------------------------*/
						if ( 'true' == $read_more ) {

							if ( $first_run ) {
								$readmore_classes = vcex_get_button_classes( $readmore_style, $readmore_style_color );
							}

							$readmore_output = '<div class="' . esc_attr( implode( ' ', vcex_get_entry_button_wrap_class( array( 'wpex-carousel-entry-button' ), 'vcex_portfolio_carousel', $atts ) ) ) . '">';

								$attrs = [
									'href'  => esc_url( $atts['post_permalink'] ),
									'class' => "entry-readmore {$readmore_classes}",
								];

								$readmore_output .= '<a' . vcex_parse_html_attributes( $attrs ) . '>';

									if ( $read_more_text ) {
										$readmore_output .= $read_more_text;
									} else {
										$readmore_output .= esc_html__( 'Read more', 'total-theme-core' );
									}

									if ( 'true' == $readmore_rarr ) {
										$readmore_output .= ' <span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
									}

								$readmore_output .= '</a>';

							$readmore_output .= '</div>';

							$output .= apply_filters( 'vcex_portfolio_carousel_readmore', $readmore_output, $atts );

						}

					$output .= '</div>';

				endif;

			$output .= '</div>';

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
