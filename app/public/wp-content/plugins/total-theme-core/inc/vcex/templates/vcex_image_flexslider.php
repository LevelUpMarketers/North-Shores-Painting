<?php

/**
 * vcex_image_flexslider shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.3
 */

defined( 'ABSPATH' ) || exit;

// Define output var
$output = '';

// Extra setting check for Elementor
if ( ! empty( $atts['slide_animation'] ) && isset( $atts['is_elementor_widget'] ) ) {
	$custom_animation = $atts['slide_animation'];
}

// Get and extract shortcode attributes
extract( $atts );

// Get images from custom field
if ( ! empty( $atts['custom_field_gallery'] ) ) {
	$cf_fallback = $image_ids;
	$image_ids  = [];
	$meta_value = vcex_get_acf_field( sanitize_text_field( $atts['custom_field_gallery'] ) );

	if ( $meta_value ) {
		// most likely an ACF field
		if ( is_array( $meta_value ) ) {
			foreach ( $meta_value as $k => $v ) {
				$image_id = $v['id'] ?? $v;
				if ( is_numeric( $image_id ) && wp_attachment_is_image( $image_id ) ) {
					$image_ids[] = $image_id;
				}
			}
		} else {
			$image_ids = $meta_value;
		}
	}

	if ( ! $image_ids && vcex_is_template_edit_mode() ) {
		$image_ids = $cf_fallback;
	}
}

// Get images from post gallery
elseif ( vcex_validate_boolean( $post_gallery ) ) {
	$image_ids = vcex_get_post_gallery_ids( vcex_get_the_ID(), $image_ids );
}

// Get images based on Real Media folder
elseif ( defined( 'RML_VERSION' )&& ! empty( $atts['rml_folder'] ) ) {
	$rml_query = new WP_Query( [
		'post_status'    => 'inherit',
		'posts_per_page' => ! empty( $atts['posts_per_page'] ) ? intval( $atts['posts_per_page'] ) : '12',
		'post_type'      => 'attachment',
		'orderby'        => 'rml', // Order by custom order of RML
		'rml_folder'     =>  sanitize_text_field( $atts['rml_folder'] ),
		'fields'         => 'ids',
	] );
	if ( $rml_query->have_posts() ) {
		$image_ids = $rml_query->posts;
	}
}

// If there aren't any images bail
if ( empty( $image_ids ) ) {
	return;
}

// Otherwise if there are images lets turn it into an array
else {

	// Get image ID's.
	if ( is_string( $image_ids ) ) {
		$attachments = explode( ',', $image_ids );
	} elseif ( is_array( $image_ids ) ) {
		$attachments = [];
		foreach ( $image_ids as $image_id ) {
			$attachments[] = $image_id['id'] ?? $image_id;
		}
	}

	// Translate images.
	foreach ( $attachments as $key => $attachment ) {
		if ( function_exists( 'wpex_parse_obj_id' ) ) {
			$attachments[ $key ] = wpex_parse_obj_id( $attachment, 'attachment' );
		}
	}

}

// Sanitize attachments to make sure they exist.
$attachments = array_filter( $attachments, 'vcex_validate_attachment' );

if ( ! $attachments ) {
	return;
}

// Turn links into array.
$thumbnail_link = ! empty( $atts['thumbnail_link'] ) ? $atts['thumbnail_link'] : 'none';
if ( 'custom_link' == $thumbnail_link ) {

	// Remove duplicate images.
	$attachments = array_unique( $attachments );

	// Turn links into array.
	if ( is_string( $custom_links ) ) {
		if ( isset( $atts['is_elementor_widget'] ) && true === $atts['is_elementor_widget'] ) {
			$custom_links = wp_parse_list( $custom_links );
		} else {
			$custom_links = explode( ',', $custom_links );
		}
	} else {
		$custom_links = [];
	}

	// Count items.
	$attachments_count  = count( $attachments );
	$custom_links_count = count( $custom_links );

	// Add empty values to custom_links array for images without links.
	if ( $attachments_count > $custom_links_count ) {
		$count = 0;
		foreach ( $attachments as $val ) {
			$count++;
			if ( ! isset( $custom_links[$count] ) ) {
				$custom_links[ $count ] = '#';
			}
		}
	}

	// New custom links count.
	$custom_links_count = count( $custom_links );

	// Remove extra custom links.
	if ( $custom_links_count > $attachments_count ) {
		$count = 0;
		foreach ( $custom_links as $key => $val ) {
			$count ++;
			if ( $count > $attachments_count ) {
				unset( $custom_links[ $key ] );
			}
		}
	}

	// Set links as the keys for the images.
	$attachments = array_combine( $attachments, $custom_links );

} else {

	$attachments = array_combine( $attachments, $attachments );

}

// Return if there are no attachments to display.
if ( ! $attachments ) {
	return;
}

// Load slider scripts.
if ( vcex_vc_is_inline() ) {
	vcex_enqueue_slider_scripts();
	vcex_enqueue_slider_scripts( true ); // needs both in builder incase user switches settings.
} else {
	$noCarouselThumbnails = vcex_validate_boolean( $control_thumbs_carousel );
	vcex_enqueue_slider_scripts( $noCarouselThumbnails );
}

// Load lightbox scripts.
if ( 'lightbox' === $thumbnail_link ) {
	vcex_enqueue_lightbox_scripts();
}

// Sanitize data and declare main vars.
$caption_data     = [];
$wrap_data        = [];
$slideshow        = vcex_vc_is_inline() ? 'false' : $slideshow;
$caption          = vcex_validate_att_boolean( 'caption', $atts );
$show_placeholder = vcex_validate_att_boolean( 'placeholder', $atts, true ) && ! vcex_validate_att_boolean( 'randomize', $atts );

// Get animation type.
if ( ! empty( $custom_animation ) ) {
	$animation = $custom_animation;
} else {
	$animation = ! empty( $atts['animation'] ) ? $atts['animation'] : 'slide';
}

// Slider attributes.
if ( in_array( $animation, array( 'fade', 'fade_slides' ) ) ) {
	$wrap_data[] = 'data-fade="true"';
}

if ( vcex_validate_boolean( $randomize ) ) {
	$wrap_data[] = 'data-shuffle="true"';
}

if ( vcex_validate_boolean( $loop ) ) {
	$wrap_data[] = ' data-loop="true"';
}

if ( vcex_validate_boolean( $counter ) ) {
	$wrap_data[] = ' data-counter="true"';
}

if ( ! vcex_validate_boolean( $slideshow ) ) {
	$wrap_data[] = 'data-auto-play="false"';
} else {
	if ( $autoplay_on_hover && 'pause' != $autoplay_on_hover ) {
		$wrap_data[] = 'data-autoplay-on-hover="' . esc_attr( $autoplay_on_hover ) . '"';
	}
}

if ( $slideshow && $slideshow_speed ) {
	$wrap_data[] = 'data-auto-play-delay="' . esc_attr( $slideshow_speed ) . '"';
}

if ( ! vcex_validate_boolean( $direction_nav ) ) {
	$wrap_data[] = 'data-arrows="false"';
}

if ( ! vcex_validate_boolean( $control_nav ) ) {
	$wrap_data[] = 'data-buttons="false"';
}

if ( ! vcex_validate_boolean( $direction_nav_hover ) ) {
	$wrap_data[] = 'data-fade-arrows="false"';
}

if ( vcex_validate_boolean( $control_thumbs ) ) {
	$wrap_data[] = 'data-thumbnails="true"';
}

if ( vcex_validate_boolean( $control_thumbs ) && vcex_validate_boolean( $control_thumbs_pointer ) ) {
	$wrap_data[] = 'data-thumbnail-pointer="true"';
}

if ( $animation_speed ) {
	$wrap_data[] = 'data-animation-speed="' . esc_attr( intval( $animation_speed ) ) . '"';
}

if ( ! vcex_validate_boolean( $auto_height ) ) {
	$wrap_data[] = 'data-auto-height="false"';
} elseif ( $height_animation ) {
	$height_animation = intval( $height_animation );
	$height_animation = 0 == $height_animation ? '0.0' : $height_animation;
	$wrap_data[] = 'data-height-animation-duration="' . esc_attr( $height_animation ) . '"';
}

if ( $control_thumbs_height ) {
	$wrap_data[] = 'data-thumbnail-height="' . esc_attr( absint( $control_thumbs_height ) ) . '"';
}

if ( $control_thumbs_width ) {
	$wrap_data[] = 'data-thumbnail-width="' . esc_attr( absint( $control_thumbs_width ) ) . '"';
}

if ( ! vcex_validate_boolean( $autoplay_videos ) ) {
	$wrap_data[] = 'data-reach-video-action="none"';
}

if ( ! vcex_validate_att_boolean( 'desktop_touch', $atts, true )
	|| 'none' !== $thumbnail_link && apply_filters( 'vcex_sliders_disable_desktop_swipe', true, 'vcex_image_flexslider' )
) {
	$wrap_data[] = 'data-touch-swipe-desktop="false"';
}

// Caption attributes and classes.
if ( $caption ) {
	$caption_position = ! empty( $atts['caption_position'] ) ? $atts['caption_position'] : 'bottomCenter';
	$caption_style    = ! empty( $atts['caption_style'] ) ? $atts['caption_style'] : 'black';

	if ( 'static' !== $caption_position ) {
		$caption_delay = ! empty( $atts['caption_delay'] ) ? $atts['caption_delay'] : '500';
		$caption_show_transition = ! empty( $atts['caption_show_transition'] ) ? $atts['caption_show_transition'] : 'up';
		$caption_hide_transition = ! empty( $atts['caption_hide_transition'] ) ? $atts['caption_hide_transition'] : 'down';

		$caption_data[] = 'data-position="' . esc_attr( $caption_position ) . '"';

		if ( 'false' !== $caption_show_transition && 'false' !== $caption_hide_transition ) {
			$caption_data[] = 'data-show-delay="' . esc_attr( intval( $caption_delay ) ) . '"';

			if ( $caption_show_transition ) {
				$caption_data[] = 'data-show-transition="' . esc_attr( $caption_show_transition ) . '"';
			}

			if ( $caption_hide_transition ) {
				$caption_data[] = 'data-hide-transition="' . esc_attr( $caption_hide_transition ) . '"';
			}
		}

		if ( ! empty( $atts['caption_width'] ) ) {
			$caption_data[] = 'data-width="' . vcex_validate_px_pct( $atts['caption_width'], 'px-pct' ) . '"';
		} else {
			$caption_data[] = 'data-width="100%"';
		}

		if ( ! empty( $atts['caption_horizontal'] ) ) {
			$caption_data[] = 'data-horizontal="' . esc_attr( intval( $atts['caption_horizontal'] ) ) . '"';
		}

		if ( ! empty( $atts['caption_vertical'] ) ) {
			$caption_data[] = 'data-vertical="' . esc_attr( intval( $atts['caption_vertical'] ) ) . '"';
		}
	}

	// Caption classes.
	$caption_class = [
		'wpex-slider-caption',
	];

	if ( 'static' === $caption_position ) {
		$caption_class[] = 'wpex-slider-caption--static';
		$caption_class[] = 'wpex-last-mb-0';
		switch ( $caption_style ) {
			case 'black':
				$caption_class[] = 'wpex-bg-black';
				$caption_class[] = 'wpex-text-white';
				break;
			case 'white':
				$caption_class[] = 'wpex-bg-white';
				$caption_class[] = 'wpex-text-black';
				break;
			case 'none';
			default:
				$caption_class[] = 'wpex-p-0';
				$caption_class[] = 'wpex-mt-20';
				break;
		}
	} else {
		if ( 'none' === $caption_style ) {
			$caption_class[] = 'wpex-text-lg';
			$caption_class[] = 'wpex-md-text-3xl';
			$caption_class[] = 'wpex-text-white';
			$caption_class[] = 'wpex-font-semibold';
		} else {
			$caption_class[] = 'sp-' . sanitize_html_class( $atts['caption_style'] );
		}

		$caption_class[] = 'sp-layer';

		if ( vcex_validate_boolean( $atts['caption_rounded'] ?? false ) ) {
			$caption_class[] = 'sp-rounded';
		}
	}

	if ( 'static' === $caption_position
		|| ( 'false' === $caption_show_transition && 'false' === $caption_hide_transition )
	 ) {
		$caption_class[] = 'sp-static';
	}

	if ( ! empty( $atts['caption_visibility'] ) ) {
		$caption_class[] = esc_attr( $atts['caption_visibility'] );
	}

}

// Main Classes.
$wrap_class = [
	'vcex-image-slider',
	'vcex-module',
	'wpex-slider',
	'slider-pro',
	'wpex-clr' // @todo remove
];

if ( $bottom_margin ) {
	$wrap_class[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
}

if ( $classes ) {
	$wrap_class[] = vcex_get_extra_class( $classes );
}

if ( ! vcex_validate_att_boolean( 'img_strech', $atts, true ) ) {
	$wrap_class[] = 'no-stretch';
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( 'lightbox' === $thumbnail_link ) {
	vcex_enqueue_lightbox_scripts();
	$wrap_class[] = 'wpex-lightbox-group';
	if ( 'none' === $lightbox_title ) {
		$wrap_data[] = 'data-show_title="false"';
	}
}

// Parse wrap class.
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_image_flexslider', $atts );

// Filter wrap data attributes.
$wrap_data = (array) apply_filters( 'vcex_image_flexslider_data_attributes', $wrap_data, $atts );

if ( $wrap_data && is_array( $wrap_data ) ) {
	$wrap_data = ' ' . trim( implode( ' ', $wrap_data ) );
}

// Open animation wrapper.
if ( $css_animation && 'none' !== $css_animation ) {

	$css_animation_style = vcex_inline_style( [
		'animation_delay'    => $atts['animation_delay'] ?? null,
		'animation_duration' => $atts['animation_duration'] ?? null,
	] );

	$output .= '<div class="' . vcex_get_css_animation( $css_animation ) . '"' . $css_animation_style . '>';
}

// Open css wrapper.
if ( $css ) {
	$output .= '<div class="vcex-image-slider-css-wrap ' . vcex_vc_shortcode_custom_css_class( $css ) . '">';
}

// Create overlay HTML.
if ( vcex_validate_att_boolean( 'overlay', $atts ) ) {
	$overlay_html = '<div class="wpex-slider__overlay wpex-absolute wpex-inset-0 wpex-bg-black wpex-opacity-30"></div>';
}

// Preloader image.
if ( $show_placeholder ) {
	$preloader_class = 'wpex-slider-preloaderimg wpex-relative';

	if ( ! vcex_validate_att_boolean( 'img_strech', $atts, true ) ) {
		$preloader_class .= ' no-stretch';
	}

	if ( ! empty( $atts['visibility'] ) ) {
		$preloader_class .= ' ' . sanitize_html_class( $atts['visibility'] );
	}

	$output .= '<div class="' . esc_attr( $preloader_class ) . '">';

		if ( ! empty( $overlay_html ) ) {
			if ( ! empty( $atts['overlay_color'] ) ) {
				$overlay_html = str_replace( '<div', '<div style="background:' . esc_attr( vcex_parse_color( $atts['overlay_color'] ) ) . ';"', $overlay_html );
			}
			$output .= $overlay_html;
		}

		$first_attachment = reset( $attachments );

		$placeholder_img_args = [
			'attachment'    => current( array_keys( $attachments ) ),
			'size'          => $img_size,
			'crop'          => $img_crop,
			'width'         => $img_width,
			'height'        => $img_height,
			'lazy'          => false,
			'apply_filters' => 'vcex_image_flexslider_thumbnail_args',
			'filter_arg1'   => $atts,
		];

		if ( ! empty( $atts['img_aspect_ratio'] ) ) {
			$placeholder_img_args['class'] = vcex_parse_aspect_ratio_class( $atts['img_aspect_ratio'] ) . ' wpex-object-cover';
		}

		$output .= vcex_get_post_thumbnail( $placeholder_img_args );

	$output .= '</div>';

}

// Start slider output.
$output .= '<div class="' . $wrap_class . '"' . vcex_get_unique_id( $unique_id ) . $wrap_data . '>';

	$output .= '<div class="wpex-slider-slides sp-slides">';

		// Loop through attachments.
		foreach ( $attachments as $attachment => $custom_link ) :

			// Define main vars
			$custom_link      = ( '#' != $custom_link ) ? $custom_link : '';
			$attachment_link  = get_post_meta( $attachment, '_wp_attachment_url', true );
			$attachment_data  = vcex_get_attachment_data( $attachment );
			$get_caption      = $caption ? $attachment_data[ $caption_type ] : '';
			$attachment_video = $attachment_data['video'];

			// Generate img HTML.
			$image_args = array(
				'attachment'    => $attachment,
				'size'          => $img_size,
				'crop'          => $img_crop,
				'width'         => $img_width,
				'height'        => $img_height,
				'alt'           => $attachment_data['alt'],
				'retina_data'   => 'retina',
				'lazy'          => false,
				'apply_filters' => 'vcex_image_flexslider_thumbnail_args',
				'filter_arg1'   => $atts,
			);

			if ( ! empty( $atts['img_aspect_ratio'] ) ) {
				$image_args['class'] = vcex_parse_aspect_ratio_class( $atts['img_aspect_ratio'] ) . ' wpex-object-cover';
			}

			$attachment_img = vcex_get_post_thumbnail( $image_args );

			// Image or video needed.
			if ( $attachment_img || $attachment_video ) {

				$output .= '<div class="wpex-slider-slide sp-slide">';

					$output .= '<div class="wpex-slider-media wpex-relative">';

						// Check if the current attachment has a video.
						if ( $attachment_video && ! vcex_validate_boolean( $lighbox_videos ) ) {

							if ( ! empty( $overlay_html ) ) {
								$output .= $overlay_html;
							}

							if ( ! vcex_validate_boolean( $video_captions ) ) {
								$get_caption = '';
							}

							// Output video.
							$output .= '<div class="wpex-slider-video responsive-video-wrap">';

								$output .= vcex_video_oembed( $attachment_video, 'sp-video', array(
									'youtube' => array(
										'enablejsapi' => '1',
									)
								) );

							$output .= '</div>';

						} elseif( $attachment_img ) {

							// Lightbox links.
							if ( 'lightbox' === $thumbnail_link ) {

								// Video lightbox.
								if ( $attachment_video ) {

									$lightbox_url = vcex_get_video_embed_url( $attachment_video );
									$lightbox_data_attributes .= ' data-thumb="' . vcex_get_lightbox_image( $attachment ) . '"';

								}

								// Image lightbox.
								else {

									$lightbox_url = vcex_get_lightbox_image( $attachment );

								}

								// Define data attributes var.
								$lightbox_data_attributes = '';

								// Lightbox titles.
								if ( 'title' === $lightbox_title && $attachment_data['title'] ) {
									$lightbox_data_attributes .= ' data-title="' . $attachment_data['title'] . '"';
								} elseif ( 'alt' === $lightbox_title ) {
									$lightbox_alt = get_post_meta( $attachment, '_wp_attachment_image_alt', true );
									if ( $lightbox_alt ) {
										$lightbox_data_attributes .= ' data-title="' . esc_attr( $lightbox_alt ) . '"';
									} else {
										$lightbox_data_attributes .= ' data-title="false"';
									}
								}

								// Lightbox Captions.
								if ( $attachment_data['caption'] && vcex_validate_boolean( $lightbox_caption ) ) {
									$lightbox_data_attributes .= ' data-caption="' . str_replace( '"',"'", $attachment_data['caption'] ) . '"';
								}

								$output .= '<a href="' . esc_url( $lightbox_url ) . '" class="vcex-flexslider-entry-img wpex-slider-media-link wpex-lightbox-group-item"' . $lightbox_data_attributes . '>';

									if ( ! empty( $overlay_html ) ) {
										$output .= $overlay_html;
									}

									$output .= $attachment_img;

								$output .= '</a>';

							// Custom Links.
							} elseif ( 'custom_link' === $thumbnail_link ) {

								// Check for a meta link value.
								if ( $link_meta_key ) {
									$meta_custom_link = get_post_meta( $attachment, wp_strip_all_tags( $link_meta_key ), true );
									if ( ! empty( $meta_custom_link ) ) {
										$custom_link = $meta_custom_link;
									}
								}

								// Custom link.
								if ( $custom_link ) {

									$output .= '<a href="' . esc_url( $custom_link ) . '"' . vcex_html( 'target_attr', $custom_links_target ) . ' class="wpex-slider-media-link">';

										if ( ! empty( $overlay_html ) ) {
											$output .= $overlay_html;
										}

										$output .= $attachment_img;

									$output .= '</a>';

								// No link.
								} else {

									if ( ! empty( $overlay_html ) ) {
										$output .= $overlay_html;
									}

									$output .= $attachment_img;

								}

							// Just images, no links.
							} else {

								if ( ! empty( $overlay_html ) ) {
									$output .= $overlay_html;
								}

								// Display the main slider image.
								$output .= $attachment_img;

							}

						}

						// Close media element before caption.
						if ( 'static' === $caption_position ) {
							$output .= '</div>';
						}

						// Display caption
						if ( $get_caption ) {
							$output .= '<div class="' . esc_attr( implode( ' ', $caption_class ) ) . '"' . implode( ' ', $caption_data ) . '>';
								if ( in_array( $caption_type, array( 'description', 'caption' ) ) ) {
									$output .= wpautop( $get_caption );
								} else {
									$output .= $get_caption;
								}
							$output .= '</div>';
						}
					
					// Close media element after caption.
					if ( 'static' !== $caption_position ) {
						$output .= '</div>';
					}

				$output .= '</div>';

			}

		endforeach;

	$output .= '</div>';

	if ( vcex_validate_att_boolean( 'control_thumbs', $atts, true ) ) {
		$thumbnails = array_keys( $attachments ); // strip out URL's.
		$thumbnails = apply_filters( 'vcex_image_flexslider_thumbnails', $thumbnails ); // allows people to display different images via filter.

		if ( ! empty( $thumbnails ) && is_array( $thumbnails ) ) {
			$thumbnails_carousel = vcex_validate_att_boolean( 'control_thumbs_carousel', $atts, true );
			$control_thumbs_fit = vcex_validate_att_boolean( 'control_thumbs_fit', $atts );

			$thumbnails_wrap_class = [
				'wpex-slider-thumbnails',
			];

			if ( $thumbnails_carousel ) {
				$thumbnails_wrap_class[] = 'sp-thumbnails';
			} else {
				$thumbnails_wrap_class[] = 'wpex-flex';
				if ( ! $control_thumbs_fit ) {
					$thumbnails_wrap_class[] = 'wpex-flex-wrap';
				}
				$thumbnails_wrap_class[] = 'wpex-gap-5';
				$thumbnails_wrap_class[] = 'wpex-pt-5';
				$thumbnails_wrap_class[] = 'sp-nc-thumbnails';
			}

			$output .= '<div class="' . esc_attr( trim( implode( ' ', $thumbnails_wrap_class ) ) ) . '">';

				$args = [
					'size'        => $img_size,
					'crop'        => $img_crop,
					'width'       => $img_width,
					'height'      => $img_height,
					'lazy'        => false,
					'retina_data' => 'retina',
				];

				if ( $thumbnails_carousel ) {
					$args['class'] = 'wpex-slider-thumbnail sp-thumbnail';
				} else {
					$args['class'] = 'wpex-slider-thumbnail wpex-align-middle';
					if ( vcex_validate_att_boolean( 'control_thumbs_resize', $atts, true ) ) {
						if ( $control_thumbs_height || $control_thumbs_width ) {
							$args['size']   = null;
							$args['width']  = $control_thumbs_width ?: null;
							$args['height'] = $control_thumbs_height ?: null;
						}
					} else {
						$args['class'] .= ' wpex-object-cover';
					}
					// Add sliderPro class last.
					$args['class'] .= ' sp-nc-thumbnail';
				}

				foreach ( $thumbnails as $thumbnail ) {
					$args['attachment'] = $thumbnail;
					$output .= vcex_get_post_thumbnail( $args );
				}

			$output .= '</div>'; // close thumbnails container.

		}

	}

$output .= '</div>';

// Close css wrapper.
if ( $css ) {
	$output .= '</div>';
}

// Close animation wrapper.
if ( $css_animation && 'none' !== $css_animation ) {
	$output .= '</div>';
}

// @codingStandardsIgnoreLine
echo $output;
