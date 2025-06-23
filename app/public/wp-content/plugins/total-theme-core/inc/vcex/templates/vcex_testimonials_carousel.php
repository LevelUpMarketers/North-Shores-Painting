<?php

/**
 * vcex_testimonials_carousel shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Define output.
$html = '';

// Define attributes for Query.
$atts['post_type'] = 'testimonials';
$atts['taxonomy']  = 'testimonials_category';
$atts['tax_query'] = '';

// Build the WordPress query.
$vcex_query = vcex_build_wp_query( $atts, 'vcex_testimonials_carousel' );

//Output posts.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// All carousels need a unique classname.
	$unique_classname = vcex_element_unique_classname();

	// Get carousel settings.
	$carousel_settings = vcex_get_carousel_settings( $atts, 'vcex_testimonials_carousel', false );
	$carousel_css = vcex_get_carousel_inline_css( $unique_classname, $carousel_settings );

	if ( $carousel_css ) {
		$html .= $carousel_css;
	}

	// Enqueue scripts.
	vcex_enqueue_carousel_scripts();

	// Define vars.
	$img_size          = $atts['img_size'] ?? '';
	$img_width		   = $atts['img_width'] ?? '';
	$img_height        = $atts['img_height'] ?? '';
	$img_crop          = $atts['img_crop'] ?? '';
	$auto_height       = vcex_validate_att_boolean( 'auto_height', $atts, false );
	$title_tag         = $atts['title_tag'] ?? null;
	$title_tag_escaped = $title_tag ? tag_escape( $title_tag ) : 'h2';
	$read_more_text    = $atts['read_more_text'] ?? esc_html__( 'read more', 'total-theme-core' );

	// Define wrap attributes.
	$wrap_attrs = [];

	// Add unique ID to wrap attributes.
	if ( ! empty( $atts['unique_id'] ) ) {
		$wrap_attrs['id'] = esc_attr( $atts['unique_id'] );
	}

	// Main Wrap Classes.
	$wrap_class = [
		'vcex-module',
		'wpex-carousel',
		'vcex-testimonials-carousel',
		'wpex-clr',
	];

	if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
		$wrap_class[] = 'owl-carousel';
	}

	if ( $carousel_css ) {
		$wrap_class[] = 'wpex-carousel--render-onload';
		$wrap_class[] = $unique_classname;
	}

	if ( ! empty( $atts['arrows_style'] ) ) {
		$wrap_class[] = 'arrwstyle-' . sanitize_html_class( $atts['arrows_style'] );
	} else {
		$wrap_class[] = 'arrwstyle-default';
	}

	if ( ! empty( $atts['arrows_position'] ) && 'default' !== $atts['arrows_position'] ) {
		$wrap_class[] = 'arrwpos-' . sanitize_html_class( $atts['arrows_position'] );
	}

	if ( ! empty( $atts['bottom_margin'] ) ) {
		$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
	}

	if ( ! empty( $atts['visibility'] ) ) {
		$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
	}

	if ( ! empty( $atts['css_animation'] ) ) {
		$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
	}

	if ( ! empty( $atts['classes'] ) ) {
		$wrap_class[] = vcex_get_extra_class( $atts['classes'] );
	}

	if ( ! empty( $atts['css'] ) ) {
		$wrap_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
	}

	$shortcode_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_testimonials_carousel', $atts );

	if ( $shortcode_class ) {
		$wrap_attrs['class'] = esc_attr( $shortcode_class );
	}

	// Disable autoplay.
	if ( vcex_vc_is_inline() || '1' == count( $vcex_query->posts ) ) {
		$atts['auto_play'] = false;
	}

	// Open wrapper for auto height.
	if ( $auto_height ) {
		$html .= '<div class="owl-wrapper-outer">';
	}

	// Display header if enabled.
	if ( ! empty( $atts['header'] ) ) {
		$html .= vcex_get_module_header( array(
			'style'   => $atts['header_style'] ?? '',
			'content' => $atts['header'],
			'classes' => array( 'vcex-module-heading vcex_testimonials_carousel-heading' ),
		) );
	}

	$wrap_style = vcex_inline_style( array(
		'animation_delay' => $atts['animation_delay'] ?? null,
		'animation_duration' => $atts['animation_duration'] ?? null,
	) );

	/*--------------------------------*/
	/* [ Carousel Start ]
	/*--------------------------------*/
	$html .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . ' data-wpex-carousel="' . vcex_carousel_settings_to_json( $carousel_settings ) . '"' . $wrap_style . '>';

		// Start loop
		while ( $vcex_query->have_posts() ) :

			// Get post from query
			$vcex_query->the_post();

			// Post VARS
			$atts['post_title']     = get_the_title();
			$atts['post_permalink'] = vcex_get_permalink();

			/*--------------------------------*/
			/* [ Entry Start ]
			/*--------------------------------*/
			$html .= '<div class="wpex-carousel-slide">';

				$html .= '<div ' . vcex_get_post_class( array( 'testimonial-entry' ) ) . '>';

					$content_class = (array) apply_filters( 'wpex_testimonials_entry_content_class', array(
						'testimonial-entry-content',
						'wpex-relative', // for caret
						'wpex-boxed',
						'wpex-border-0',
						'wpex-clr',
					) );

					$html .= '<div class="' . esc_attr( implode( ' ', $content_class ) ) . '">';

						$html .= '<span class="testimonial-caret wpex-absolute wpex-block wpex-w-0 wpex-h-0"></span>';

						/*--------------------------------*/
						/* [ Title ]
						/*--------------------------------*/
						$title_html = '';
						if ( vcex_validate_att_boolean( 'title', $atts, false ) ) :

							if ( ! isset( $title_class ) ) {
								if ( function_exists( 'wpex_testimonials_entry_title_class' ) ) {
									ob_start();
										wpex_testimonials_entry_title_class();
									$title_class = ' ' . trim( ob_get_clean() );
								} else {
									$title_class = '';
								}
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
								if ( 'true' == $atts['title_link'] && is_post_type_viewable( 'testimonials' ) ) {

									$title_html .= '<a href="' . esc_url( $atts['post_permalink'] ) . '">';

										$title_html .= esc_html( $atts['post_title'] );

									$title_html .= '</a>';

								}

								// Title without link.
								else {

									$title_html .= esc_html( $atts['post_title'] );

								}

							$title_html .= '</' . $title_tag_escaped . '>';

							$html .= apply_filters( 'vcex_testimonials_carousel_title', $title_html, $atts );

						endif;

						/*--------------------------------*/
						/* [ Details ]
						/*--------------------------------*/
						if ( ! isset( $content_style ) ) {
							$content_style = vcex_inline_style( array(
								'font_size' => $atts['content_font_size'] ?? null,
								'color'     => $atts['content_color'] ?? null,
							) );
						}

						$html .= '<div class="testimonial-entry-details testimonial-entry-text wpex-text-pretty wpex-last-mb-0 wpex-clr"'. $content_style .'>';

							// Display excerpt if enabled (default dispays full content).
							$excerpt_html = '';
							if ( vcex_validate_att_boolean( 'excerpt', $atts, false ) ) :

								// Custom readmore text.
								if ( vcex_validate_att_boolean( 'read_more', $atts, true ) && is_post_type_viewable( 'testimonials' ) ) :

									// Add arrow.
									if ( ! isset( $read_more_rarr_html ) ) {
										if ( vcex_validate_att_boolean( 'read_more_rarr', $atts, true ) ) {
											$read_more_rarr_html = ' <span>' . vcex_readmore_button_arrow() . '</span>';
										} else {
											$read_more_rarr_html = '';
										}
									}

									// Read more text.
									if ( is_rtl() ) {
										$read_more_link = '&#8230;<a href="' . esc_url( $atts['post_permalink'] ) . '" title="' . esc_attr( $read_more_text ) . '">' . wp_kses_post( $read_more_text ) .'</a>';
									} else {
										$read_more_link = '&#8230;<a href="' . esc_url( $atts['post_permalink'] ) . '" title="' . esc_attr( $read_more_text ) . '">' . wp_kses_post( $read_more_text ) . $read_more_rarr_html .'</a>';
									}

								else :

									$read_more_link = '&#8230;';

								endif;

								// Custom Excerpt function.
								$excerpt_html .= vcex_get_excerpt( array(
									'length'  => $atts['excerpt_length'] ?? 20,
									'more'    => $read_more_link,
									'context' => 'vcex_testimonials_carousel',
								) );

							// Display full post content.
							else :

								$excerpt_html .= vcex_the_content( get_the_content(), 'vcex_testimonials_carousel' );

							// End excerpt check.
							endif;

							$html .= apply_filters( 'vcex_testimonials_carousel_excerpt', $excerpt_html, $atts );

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
						$media_html = '';
						if ( vcex_validate_att_boolean( 'entry_media', $atts, true ) ) {

							if ( ! isset( $custom_dims ) ) {
								if ( $img_width || $img_height || ! in_array( $img_size, array( 'wpex_custom', 'testimonials_entry' ) ) ) {
									$custom_dims = true;
								} else {
									$custom_dims = false;
								}
							}

							if ( ! isset( $img_style ) ) {
								$img_style = vcex_inline_style( array(
									'border_radius' => $atts['img_border_radius'] ?? null,
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
							 * Filters the vcex_testimonials_carousel shortcode media html.
							 *
							 * @param string $html
							 * @param array $shortcode_attributes
							 */
							$bottom_html .= apply_filters( 'vcex_testimonials_carousel_media', $media_html, $atts );

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
								 * Filters the vcex_testimonials_carousel shortcode author html.
								 *
								 * @param string $html
								 * @param array $shortcode_attributes
								 */
								$bottom_html .= (string) apply_filters( 'vcex_testimonials_carousel_author', $author_html, $atts );
							}

							/*--------------------------------*/
							/* [ Company ]
							/*--------------------------------*/
							if ( vcex_validate_att_boolean( 'company', $atts, true ) ) {

								ob_start();
									get_template_part( 'partials/testimonials/testimonials-entry-company' );
								$company_html = ob_get_clean();

								/**
								 * Filters the vcex_testimonials_carousel shortcode company html.
								 *
								 * @param string $html
								 * @param array $shortcode_attributes
								 */
								$bottom_html .= (string) apply_filters( 'vcex_testimonials_carousel_company', $company_html, $atts );

							}

							/*--------------------------------*/
							/* [ Rating ]
							/*--------------------------------*/
							if ( vcex_validate_att_boolean( 'rating', $atts, true ) ) {

								ob_start();
									get_template_part( 'partials/testimonials/testimonials-entry-rating' );
								$rating_html = ob_get_clean();

								/**
								 * Filters the vcex_testimonials_carousel shortcode entry rating html.
								 *
								 * @param string $html
								 * @param array $shortcode_attributes
								 */
								$bottom_html .= (string) apply_filters( 'vcex_testimonials_carousel_rating', $rating_html, $atts );

							}

						$bottom_html .= '</div>';

					$bottom_html .= '</div>';

					/**
					 * Filters the vcex_testimonials_carousel shortcode entry bottom html.
					 *
					 * @param string $html
					 * @param array $shortcode_attributes
					 */
					$html .= apply_filters( 'vcex_testimonials_carousel_bottom', $bottom_html, $atts );

				$html .= '</div>';

			$html .= '</div>';

		endwhile;

	$html .= '</div>';

	// Close wrap for single item auto height.
	if ( $auto_height ) {
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
