<?php

/**
 * vcex_image_carousel shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Define main vars.
$output = '';
$image_ids = $atts['image_ids'] ?? '';

// Get images from custom field.
if ( ! empty( $atts['custom_field_gallery'] ) ) {
	$cf_fallback = $image_ids;
	$image_ids = ''; // !!! important !!!
	$custom_field_gallery = sanitize_text_field( $atts['custom_field_gallery'] );

	if ( function_exists( 'get_field_object' ) && str_starts_with( $custom_field_gallery, 'field_' ) ) {
		$field_obj = get_field_object( $custom_field_gallery );
		if ( ! empty( $field_obj['type'] ) && 'gallery' === $field_obj['type'] && ! empty( $field_obj['value'] ) ) {
			$image_ids = $field_obj['value'];
		}
	}

	$image_ids = $image_ids ?: get_post_meta( vcex_get_the_ID(), $custom_field_gallery, true );

	if ( ! $image_ids && vcex_is_template_edit_mode() ) {
		$image_ids = $cf_fallback;
	}

// Get images from post gallery.
} elseif ( vcex_validate_att_boolean( 'post_gallery', $atts, false ) ) {
	$image_ids = vcex_get_post_gallery_ids( vcex_get_the_ID(), $image_ids );
}

// Get images based on Real Media folder.
elseif ( defined( 'RML_VERSION' ) && ! empty( $atts['rml_folder'] ) ) {
	$rml_query = new WP_Query( [
		'post_status'    => 'inherit',
		'posts_per_page' => ! empty( $atts['posts_per_page'] ) ? intval( $atts['posts_per_page'] ) : '12',
		'post_type'      => 'attachment',
		'orderby'        => 'rml', // Order by custom order of RML.
		'rml_folder'     => sanitize_text_field( $atts['rml_folder'] ),
		'fields'         => 'ids',
	] );
	if ( $rml_query->have_posts() ) {
		$image_ids = $rml_query->posts;
	}
}

// Sanitize image ids.
if ( ! $image_ids ) {
	return;
} else {
	if ( is_string( $image_ids ) ) {
		$attachment_ids = explode( ',', $image_ids );
	} elseif ( is_array( $image_ids ) ) {
		$attachment_ids = [];
		foreach ( $image_ids as $image_id ) {
			$attachment_ids[] = $image_id['id'] ?? $image_id;
		}
	}
}

// Remove duplicate images.
$attachment_ids = array_unique( $attachment_ids );

// Sanitize attachments to make sure they exist.
$attachment_ids = array_filter( $attachment_ids, 'vcex_validate_attachment' );

if ( ! $attachment_ids ) {
	return;
}

// Turn links into array.
if ( ! empty( $atts['custom_links'] ) && is_string( $atts['custom_links'] ) ) {
	if ( isset( $atts['is_elementor_widget'] ) && true === $atts['is_elementor_widget'] ) {
		$custom_links = wp_parse_list( $atts['custom_links'] );
	} else {
		$custom_links = explode( ',', $atts['custom_links'] );
	}
} else {
	$custom_links = [];
}

// Count items
$attachment_ids_count = count( $attachment_ids );
$custom_links_count   = count( $custom_links );

// Add empty values to custom_links array for images without links.
if ( $attachment_ids_count > $custom_links_count ) {
	$count = 0;
	foreach ( $attachment_ids as $val ) {
		$count++;
		if ( ! isset( $custom_links[ $count ] ) ) {
			$custom_links[ $count ] = '#';
		}
	}
}

// New custom links count.
$custom_links_count = count( $custom_links );

// Remove extra custom links.
if ( $custom_links_count > $attachment_ids_count ) {
	$count = 0;
	foreach ( $custom_links as $key => $val ) {
		$count ++;
		if ( $count > $attachment_ids_count ) {
			unset( $custom_links[ $key ] );
		}
	}
}

// Set links as the keys for the images.
$images_links_array = array_combine( $attachment_ids, $custom_links );

// Return if no images.
if ( ! $images_links_array ) {
	return;
}

// Lets create a new Query for the image carousel.
$vcex_query = new WP_Query( [
	'post_type'      => 'attachment',
	'post_status'    => 'any',
	'posts_per_page' => -1,
	'paged'          => NULL,
	'no_found_rows'  => true,
	'post__in'       => $attachment_ids,
	'orderby'        => ! empty( $atts['orderby'] ) ? $atts['orderby'] : 'post__in',
	'order'          => $atts['order'],
] );

// Display carousel if there are images.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// Define main vars.
	$carousel_style    = ! empty( $atts['style'] ) ? $atts['style'] : '';
	$thumbnail_link    = ! empty( $atts['thumbnail_link'] ) ? $atts['thumbnail_link'] : '';
	$show_title        = vcex_validate_att_boolean( 'title', $atts, false );
	$title_type        = ! empty( $atts['title_type'] ) ? $atts['title_type'] : 'title';
	$show_caption      = vcex_validate_att_boolean( 'caption', $atts, false );
	$inline_videos     = (bool) apply_filters( 'vcex_image_carousel_video_support', false );
	$unique_classname  = vcex_element_unique_classname();
	$carousel_settings = vcex_get_carousel_settings( $atts, 'vcex_image_carousel', false );
	$carousel_css      = vcex_get_carousel_inline_css( $unique_classname, $carousel_settings );

	// Make sure scripts are loaded.
	vcex_enqueue_carousel_scripts();

	// Main Classes.
	$wrap_class = [
		'vcex-image-carousel',
		'wpex-carousel',
		'wpex-carousel-images',
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

	// Bottom margin.
	if ( ! empty( $atts['bottom_margin'] ) ) {
		$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
	}

	// Vertical align.
	if ( vcex_validate_att_boolean( 'vertical_align', $atts, false ) ) {
		$wrap_class[] = 'wpex-carousel-items-center';
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

	// CSS animation class.
	if ( ! empty( $atts['css_animation'] ) ) {
		$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
	}

	// Entry classes.
	$entry_classes = [
		'wpex-carousel-slide',
	];

	if ( ! empty( $atts['content_alignment'] ) ) {
		$entry_classes[] = sanitize_html_class( "wpex-text-{$atts['content_alignment']}" );
	}

	if ( ! empty( $atts['entry_css'] ) ) {
		$entry_classes[] .= vcex_vc_shortcode_custom_css_class( $atts['entry_css'] );
	}

	// Lightbox css/js/classes
	if ( 'lightbox' === $thumbnail_link ) {
		vcex_enqueue_lightbox_scripts();
		$has_lightbox_gallery = vcex_validate_att_boolean( 'lightbox_gallery', $atts, true );
		if ( $has_lightbox_gallery ) {
			$wrap_class[] = 'wpex-carousel-lightbox';
		}
	}

	// Image class.
	$image_class = [];
	if ( ! empty( $atts['img_aspect_ratio'] ) ) {
		$image_class[] = vcex_parse_aspect_ratio_class( $atts['img_aspect_ratio'] );
		if ( ! empty( $atts['img_object_fit'] ) ) {
			$image_class[] = vcex_parse_object_fit_class( $atts['img_object_fit'] );
		}
	}

	// Deprecated content css.
	if ( $show_title || $show_caption ) {
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

	// Custom classes (add last).
	// @todo rename param to el_class.
	if ( ! empty( $atts['classes'] ) ) {
		$wrap_class[] = vcex_get_extra_class( $atts['classes'] );
	}

	// Apply filters
	$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_image_carousel', $atts );

	// Display header if enabled
	if ( ! empty( $atts['header'] ) ) {
		$output .= vcex_get_module_header( [
			'style'   => $atts['header_style'] ?? '',
			'content' => $atts['header'],
			'classes' => [
				'vcex-module-heading',
				'vcex_image_carousel-heading',
			],
		] );
	}

	// Carousel CSS (important must be added directly before the carousel).
	if ( $carousel_css ) {
		$output .= $carousel_css;
	}

	// Open wrapper for auto height
	if ( vcex_validate_att_boolean( 'auto_height', $atts, false ) ) {
		$has_owl_wrapper = true;
		$output .= '<div class="owl-wrapper-outer">';
	}

	/*** Begin Carousel Output ***/
	$output .= '<div class="' . esc_attr( $wrap_class ) . '" data-wpex-carousel="' . vcex_carousel_settings_to_json( $carousel_settings ) . '"' . vcex_get_unique_id( $atts ) . '>';

		// Start counter used for lightbox.
		$count=0;

		// Loop through images
		while ( $vcex_query->have_posts() ) :

			// Add to counter.
			$count++;

			// Reset entry_classes.
			$loop_entry_classes = $entry_classes;

			// Get post from query.
			$vcex_query->the_post();

			// Store entry data in $atts array so we can apply a filter later.
			$atts['post_url']     = '';
			$atts['post_id']      = get_the_ID();
			$atts['post_data']    = vcex_get_attachment_data( $atts['post_id'] );
			$atts['post_link']    = $atts['post_data']['url'] ?? '#';
			$atts['post_alt']     = $atts['post_data']['alt'] ?? '';
			$atts['post_caption'] = $atts['post_data']['caption'] ?? '';
			$atts['link_target']  = $atts['custom_links_target'] ?? '';
			$atts['post_video']   = $atts['post_data']['video'] ?? '';

			switch ( $title_type ) {
				case 'alt':
					$atts['post_title'] = esc_attr( $atts['post_data']['alt'] );
					break;
				case 'title':
				default:
					$atts['post_title'] = get_the_title();
					break;
			}

			// Define other useful vars.
			$og_attachment_id = $atts['post_id'];
			$has_video_icon   = false;


			// Get original attachment ID - fix for WPML.
			if ( $custom_links_count && function_exists( 'icl_object_id' ) ) {
				global $sitepress;
				if ( $sitepress ) {
					$_icl_lang_duplicate_of = get_post_meta( $atts['post_id'], '_icl_lang_duplicate_of', true );
					$wpml_attachment_id = icl_object_id( $atts['post_id'], 'attachment', false, $sitepress->get_default_language() );
					if ( ! array_key_exists( $wpml_attachment_id, $images_links_array ) ) {
						$wpml_attachment_id = icl_object_id( $atts['post_id'], 'attachment', false, apply_filters( 'wpml_current_language', NULL ) );
					}
					if ( array_key_exists( $wpml_attachment_id, $images_links_array ) ) {
						$og_attachment_id = $wpml_attachment_id;
					}
				}
			}

			// Pluck array to see if item has custom link.
			if ( array_key_exists( $og_attachment_id, $images_links_array ) ) {
				$atts['post_url'] = $images_links_array[ $og_attachment_id ];
			}

			// Check for custom meta links.
			if ( 'custom_link' === $thumbnail_link ) {
				if ( ! empty( $atts['link_meta_key'] ) ) {
					$meta_custom_link = get_post_meta( $atts['post_id'], trim( $atts['link_meta_key'] ), true );
					if ( ! empty( $meta_custom_link ) ) {
						$atts['post_url'] = $meta_custom_link;
					}
				} else {
					$meta_custom_link = get_post_meta( $atts['post_id'], '_wpex_custom_link', true );
					if ( ! empty( $meta_custom_link ) ) {
						$atts['post_url'] = $meta_custom_link;
					}
				}
			}

			// Remove links if the post_url is a # symbol.
			if ( '#' === $atts['post_url'] ) {
				$atts['post_url'] = '';
			}

			// Image|Video output.
			if ( $inline_videos && $atts['post_video'] ) {
				$atts['media_type'] = 'video';
				$loop_entry_classes[] = 'owl-item-video';
			} else {
				$atts['media_type'] = 'thumbnail';
				$thumbnail_class = vcex_get_entry_thumbnail_class( $image_class, 'vcex_image_carousel', $atts );
				$image_output = vcex_get_post_thumbnail( [
					'lazy'          => false,
					'attachment'    => $atts['post_id'],
					'alt'           => $atts['post_alt'],
					'crop'          => $atts['img_crop'] ?? '',
					'size'          => $atts['img_size'] ?? '',
					'width'         => $atts['img_width'] ?? '',
					'height'        => $atts['img_height'] ?? '',
					'class'         => implode( ' ', $thumbnail_class ),
					'apply_filters' => 'vcex_image_carousel_thumbnail_args',
					'filter_arg1'   => $atts,
				] );
			}

			/*** Begin Entry Output ***/
			$output .= '<div class="' . esc_attr( implode( ' ', $loop_entry_classes ) ) . '">';

				$output .= '<figure class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( array( 'wpex-carousel-entry-media' ), 'vcex_image_carousel', $atts ) ) ) . '">';

					/*** Entry Media ***/

					// Entry Video.
					if ( $inline_videos && $atts['post_video'] ) {
						$output .= '<a href="' . esc_url( set_url_scheme( $atts['post_video'] ) ) . '" class="owl-video"></a>';
					}

					// Image thumbnail.
					else {

						// Add custom links to attributes for use with the overlay styles.
						if ( 'custom_link' === $thumbnail_link ) {
							$atts['overlay_link'] = $atts['post_url'] ?: 'disable';
						}

						// Lightbox.
						if ( 'lightbox' === $thumbnail_link ) {

							$atts['lightbox_data']  = []; // must reset for each item
							$lightbox_image_escaped = vcex_get_lightbox_image( $atts['post_id'] );
							$atts['lightbox_link']  = $lightbox_image_escaped;

							// Main link attributes.
							$link_attrs = [
								'href'  => '',
								'title' => $atts['post_alt'],
								'class' => 'wpex-carousel-entry-img',
							];

							// Main link lightbox attributes.
							if ( 'lightbox' == $thumbnail_link ) {

								if ( $has_lightbox_gallery ) {
									$link_attrs['class'] .= ' wpex-carousel-lightbox-item';
								} else {
									$link_attrs['class'] .= ' wpex-lightbox';
								}

								if ( ! empty( $atts['post_alt'] ) ) {
									$link_attrs['data-title'] = $atts['post_alt'];
								}

								$link_attrs['data-count'] = $count;

								if ( isset( $atts['lightbox_title'] ) && in_array( $atts['lightbox_title'], [ 'title', 'alt' ], true ) ) {
									switch( $atts['lightbox_title'] ) {
										case 'title':
											$link_attrs['data-title'] = get_the_title( $atts['post_id'] );
											break;
										case 'alt':
											$link_attrs['data-title'] = $atts['post_alt'];
											break;
									}
								} else {
									$link_attrs['data-show_title'] = 'false';
								}

								// Video lightbox.
								if ( $atts['post_video']
									&& $video_embed_url = vcex_get_video_embed_url( $atts['post_data']['video'] )
								) {
									$has_video_icon = true;
									$atts['lightbox_link']    = $video_embed_url;
									$link_attrs['data-thumb'] = $lightbox_image_escaped;
									// Pass lightbox data to $atts for use with image overlays.
									$atts['lightbox_data']['data-thumb'] = 'data-thumb="' . esc_attr( $lightbox_image_escaped ) . '"';
								}

								// Caption data.
								if ( vcex_validate_att_boolean( 'lightbox_caption', $atts, true )
									&& $attachment_caption = get_post_field( 'post_excerpt', $atts['post_id'] )
								) {
									$link_attrs['data-caption'] = str_replace( '"',"'", $attachment_caption );
								}

								$link_attrs['href'] = $atts['lightbox_link'];
							}

							$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';
								$output .= $image_output;
								if ( $has_video_icon && ( empty( $atts['overlay_style'] ) || 'none' === $atts['overlay_style'] ) ) {
									$output .= vcex_get_image_overlay( 'inside_link', 'video-icon' );
								}
								$output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_image_carousel', $atts );
							$output .= '</a>';


						}

						// Parent page link.
						elseif ( 'parent_page' === $thumbnail_link ) {

							$post_parent = get_post_parent( $atts['post_id'] );

							// Open link.
							if ( $post_parent ) {
								$link_attrs = [
									'href'   => get_permalink( $post_parent ),
									'class'  => 'wpex-carousel-entry-img',
									'target' => $atts['link_target'],
								];

								$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

							}

								// Display image.
								$output .= $image_output;

								// Inner link overlay HTML.
								$output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_image_carousel', $atts );

							// Close link.
							if ( $post_parent ) {
								$output .= '</a>';
							}

						}

						// Attachment page.
						elseif ( 'attachment_page' === $thumbnail_link || 'full_image' === $thumbnail_link ) {

							// Open link tag.
							$link_attrs = [
								'href'   =>  ( 'attachment_page' === $thumbnail_link ) ? get_permalink() : wp_get_attachment_url( $atts['post_id'] ),
								'class'  => 'wpex-carousel-entry-img',
								'target' => $atts['link_target'],
							];

							$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';
								$output .= $image_output;
								$output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_image_carousel', $atts );
							$output .= '</a>';
						}

						// Custom Link.
						elseif ( 'custom_link' == $thumbnail_link && $atts['post_url'] ) {

							$link_attrs = [
								'href'   => $atts['post_url'],
								'class'  => 'wpex-carousel-entry-img',
								'target' => $atts['link_target'],
							];

							$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

								// Display Image.
								$output .= $image_output;

								// Inner link overlay HTML.
								$output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_image_carousel', $atts );

							$output .= '</a>';

						}

						// No link.
						else {

							// Display Image.
							$output .= $image_output;

							// Inner link overlay HTML.
							$output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_image_carousel', $atts );

						}

						// Outer link overlay HTML.
						$output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_image_carousel', $atts );

					} // end video/image check.

				$output .= '</figure>';

				/*** Details ***/
				if ( ( $show_title && $atts['post_title'] ) || ( $show_caption && $atts['post_caption'] ) ) :

					$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( array( 'wpex-carousel-entry-details' ), 'vcex_image_carousel', $atts ) ) ) . '"' . $content_style . '>';

						/*** Title ***/
						if ( $show_title && $atts['post_title'] ) {
							$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_title_class( array( 'wpex-carousel-entry-title' ), 'vcex_image_carousel', $atts ) ) ) . '">';
								$output .= vcex_parse_text_safe( $atts['post_title'] );
							$output .= '</div>';
						}

						/*** Caption ***/
						if ( $show_caption && $atts['post_caption'] ) {
							$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( [ 'wpex-carousel-entry-excerpt' ], 'vcex_image_carousel', $atts ) ) ) . '">';
								$output .= vcex_parse_text_safe( $atts['post_caption'] );
							$output .= '</div>';
						}

					$output .= '</div>';

				endif;

			$output .= '</div>';

		endwhile;

	$output .= '</div>';

	// Close wrap for single item auto height.
	if ( isset( $has_owl_wrapper ) && true === $has_owl_wrapper ) {
		$output .= '</div>';
	}

	// Reset the post data to prevent conflicts with other queries.
	vcex_reset_postdata();

	// @codingStandardsIgnoreLine.
	echo $output;

// End Query.
endif;
