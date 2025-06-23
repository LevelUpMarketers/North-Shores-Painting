<?php

/**
 * vcex_image_galleryslider shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

// Define output
$output = '';

// Extract shortcode attributes
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
elseif ( defined( 'RML_VERSION' ) && ! empty( $atts['rml_folder'] ) ) {
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

// If there aren't any images lets display a notice
if ( empty( $image_ids ) ) {
	return;
}

// Otherwise if there are images lets turn it into an array.
else {

	// Get image ID's.
	if ( ! is_array( $image_ids ) ) {
		$attachments = explode( ',', $image_ids );
	} else {
		$attachments = $image_ids;
	}

}

// Turn links into array.
if ( $custom_links && 'custom_link' == $thumbnail_link ) {

	// Remove duplicate images.
	$attachments = array_unique( $attachments );

	// Turn links into array.
	if ( $custom_links ) {
		$custom_links = explode( ',', $custom_links );
	} else {
		$custom_links = [];
	}

	// Count items.
	$attachments_count = count( $attachments );
	$custom_links_count   = count( $custom_links );

	// Add empty values to custom_links array for images without links.
	if ( $attachments_count > $custom_links_count ) {
		$count = 0;
		foreach ( $attachments as $val ) {
			$count++;
			if ( ! isset( $custom_links[$count] ) ) {
				$custom_links[$count] = '#';
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
				unset( $custom_links[$key] );
			}
		}
	}

	// Set links as the keys for the images
	$attachments = array_combine( $attachments, $custom_links );

} else {

	$attachments = array_combine( $attachments, $attachments );

};

// Output gallery.
if ( $attachments ) :

	// Load slider scripts.
	vcex_enqueue_slider_scripts( true );

	// Load lightbox scripts.
	if ( 'lightbox' == $thumbnail_link ) {
		vcex_enqueue_lightbox_scripts();
	}

	// Sanitize data and declare main vars.
	$wrap_attributes    = [];
	$caption_attributes = [];
	$slideshow          = vcex_vc_is_inline() ? 'false' : $slideshow;
	$thumbnails_columns = $thumbnails_columns ?: '5';

	// Slider attributes.
	$wrap_attributes[] = 'data-thumbnails="true"';
	$wrap_attributes[] = 'data-thumbnail-height="auto"';
	if ( in_array( $animation, array( 'fade', 'fade_slides' ) ) ) {
		$wrap_attributes[] = 'data-fade="true"';
	}
	if ( 'true' == $randomize ) {
		$wrap_attributes[] = 'data-shuffle="true"';
	}
	if ( 'true' == $loop ) {
		$wrap_attributes[] = 'data-loop="true"';
	}
	if ( 'false' == $slideshow ) {
		$wrap_attributes[] = 'data-auto-play="false"';
	}
	if ( $slideshow && $slideshow_speed ) {
		$wrap_attributes[] = 'data-auto-play-delay="' . esc_attr( $slideshow_speed ) . '"';
	}
	if ( 'false' == $direction_nav ) {
		$wrap_attributes[] = 'data-arrows="false"';
	}
	if ( 'false' == $control_nav ) {
		$wrap_attributes[] = 'data-buttons="false"';
	}
	if ( 'false' == $direction_nav_hover ) {
		$wrap_attributes[] = 'data-fade-arrows="false"';
	}
	if ( $animation_speed ) {
		$wrap_attributes[] = 'data-animation-speed="' . intval( $animation_speed ) . '"';
	}
	if ( $height_animation ) {
		$height_animation = intval( $height_animation );
		$height_animation = 0 == $height_animation ? '0.0' : $height_animation;
		$wrap_attributes[] = 'data-height-animation-duration="' . esc_attr( $height_animation ) . '"';
	}
	if ( $custom_links && apply_filters( 'vcex_sliders_disable_desktop_swipe', true, 'vcex_image_galleryslider' ) ) {
		$wrap_attributes[] = 'data-touch-swipe-desktop="false"';
	}

	// Caption attributes.
	if ( 'true' == $caption ) {

		// Caption attributes.
		if ( $caption_position ) {
			$caption_attributes[] = ' data-position="' . esc_attr( $caption_position ) . '"';
		}
		if ( $caption_show_transition ) {
			$caption_attributes[] = ' data-show-transition="' . esc_attr( $caption_show_transition ) . '"';
		}
		if ( $caption_hide_transition ) {
			$caption_attributes[] = ' data-hide-transition="' . esc_attr( $caption_hide_transition ) . '"';
		}
		if ( $caption_width ) {
			$caption_attributes[] = ' data-width="' . esc_attr( vcex_validate_px_pct( $caption_width, 'px-pct' ) ) . '"';
		}
		if ( $caption_horizontal ) {
			$caption_attributes[] = ' data-horizontal="' . esc_attr( intval( $caption_horizontal ) ) . '"';
		}
		if ( $caption_vertical ) {
			$caption_attributes[] = ' data-vertical="' . esc_attr( intval( $caption_vertical ) ) . '"';
		}
		if ( $caption_delay ) {
			$caption_attributes[] = ' data-show-delay="' . esc_attr( intval( $caption_delay ) ) . '"';
		}
		if ( empty( $caption_show_transition ) && empty( $caption_hide_transition ) ) {
			$caption_attributes[] = ' data-sp-static="false"';
		}
		$caption_attributes = implode( ' ', $caption_attributes );

		// Caption classes
		$caption_classes = [
			'wpex-slider-caption',
			'sp-layer',
			'wpex-clr',
		];

		if ( $caption_visibility ) {
			$caption_classes[] = $caption_visibility;
		}

		if ( $caption_style ) {
			$caption_classes[] = 'sp-' . $caption_style;
		}

		if ( 'true' == $caption_rounded ) {
			$caption_classes[] = 'sp-rounded';
		}

		$caption_classes = implode( ' ', $caption_classes );
	}

	// Main Classes
	$wrap_classes = [
		'vcex-module',
		'wpex-slider',
		'slider-pro',
		'no-margin-thumbnails',
		'vcex-image-gallery-slider',
	];

	if ( $bottom_margin ) {
		$wrap_classes[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
	}

	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	if ( $visibility ) {
		$wrap_classes[] = vcex_parse_visibility_class( $visibility );
	}

	if ( 'lightbox' == $thumbnail_link ) {
		vcex_enqueue_lightbox_scripts();
		$wrap_classes[] = 'wpex-lightbox-group';
		if ( ! $lightbox_title || 'none' == $lightbox_title ) {
			$wrap_attributes[] = 'data-show_title="false"';
		}
	}

	// Convert arrays to strings
	$wrap_classes    = implode( ' ', $wrap_classes );
	$wrap_attributes = ' ' . implode( ' ', $wrap_attributes );

	// Apply filters
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_image_galleryslider', $atts );

	// Open animation wrapper.
	if ( $css_animation && 'none' !== $css_animation ) {

		$css_animation_style = vcex_inline_style( array(
			'animation_delay'    => $atts['animation_delay'],
			'animation_duration' => $atts['animation_duration'],
		) );

		$output .= '<div class="' . vcex_get_css_animation( $css_animation ) . '"' . $css_animation_style . '>';
	}

	// Open css wrapper
	if ( $css ) {
		$output .= '<div class="vcex-image-gallery-slider-css-wrap ' . vcex_vc_shortcode_custom_css_class( $css ) . '">';
	}

	// Preloader image
	if ( 'true' != $randomize ) {

		$output .= '<div class="wpex-slider-preloaderimg ' . vcex_parse_visibility_class( $visibility ) . '">';

			$first_attachment = reset( $attachments );

			$output .= vcex_get_post_thumbnail( array(
				'attachment'    => current( array_keys( $attachments ) ),
				'size'          => $img_size,
				'crop'          => $img_crop,
				'width'         => $img_width,
				'height'        => $img_height,
				'lazy'          => false,
				'apply_filters' => 'vcex_image_galleryslider_thumbnail_args',
				'filter_arg1'   => $atts,
			) );

		$output .= '</div>';

	}

	// Main output begins
	$output .= '<div class="' . esc_attr( $wrap_classes ) .'"' . vcex_get_unique_id( $unique_id ) . $wrap_attributes . '>';

		$output .= '<div class="wpex-slider-slides sp-slides">';

			// Loop through attachments.
			$first_run = true;
			foreach ( $attachments as $attachment => $custom_link ) :

				// Attachment VARS.
				$custom_link      = ( '#' != $custom_link ) ? $custom_link : '';
				$attachment_link  = get_post_meta( $attachment, '_wp_attachment_url', true );
				$attachment_data  = vcex_get_attachment_data( $attachment );
				$caption_type     = $caption_type ?: 'caption';
				$caption_output   = $attachment_data[$caption_type];
				$attachment_video = $attachment_data['video'];

				// Get and crop image if needed.
				$attachment_img = vcex_get_post_thumbnail( array(
					'attachment'    => $attachment,
					'size'          => $img_size,
					'crop'          => $img_crop,
					'width'         => $img_width,
					'height'        => $img_height,
					'alt'           => $attachment_data['alt'],
					'lazy'          => false,
					'retina_data'   => 'retina',
					'apply_filters' => 'vcex_image_galleryslider_thumbnail_args',
					'filter_arg1'   => $atts,
				) );

				$output .= '<div class="wpex-slider-slide sp-slide">';

					$output .= '<div class="wpex-slider-media">';

						// Display video.
						if ( $attachment_video ) :

							$output .= '<div class="wpex-slider-video responsive-video-wrap">';

								$output .= vcex_video_oembed( $attachment_video, 'sp-video', array(
									'youtube' => array(
										'enablejsapi' => '1',
									)
								) );

							$output .= '</div>';

						else :

							// Lightbox links.
							if ( 'lightbox' == $thumbnail_link ) :

								$lightbox_data_attributes = '';
								if ( $lightbox_title && 'none' != $lightbox_title ) {
									if ( 'title' == $lightbox_title && $attachment_data['title'] ) {
										$lightbox_data_attributes .= ' data-title="'. esc_attr( $attachment_data['title'] ) .'"';
									} elseif ( esc_attr( $attachment_data['alt'] ) ) {
										$lightbox_data_attributes .= ' data-title="'. esc_attr( $attachment_data['alt'] ) .'"';
									}
								}

								// Caption data
								if ( $attachment_data['caption'] && 'false' != $lightbox_caption ) {
									$lightbox_data_attributes .= ' data-caption="' . str_replace( '"',"'", $attachment_data['caption'] ) . '"';
								}

								$output .= '<a href="' . vcex_get_lightbox_image( $attachment ) . '" class="vcex-galleryslider-entry-img wpex-lightbox-group-item"' . $lightbox_data_attributes . '>';

									$output .= $attachment_img;

								$output .= '</a>';

							// Custom links
							elseif ( 'custom_link' == $thumbnail_link ) :

								// Check for a meta link value
								if ( $link_meta_key ) {
									$meta_custom_link = get_post_meta( $attachment, wp_strip_all_tags( $link_meta_key ), true );
									if ( ! empty( $meta_custom_link ) ) {
										$custom_link = $meta_custom_link;
									}
								}

								if ( $custom_link ) {

									$output .= '<a href="' . esc_url( $custom_link ) . '"' . vcex_html( 'target_attr', $custom_links_target ) . '>';

										$output .= $attachment_img;

									$output .= '</a>';

								} else {

									$output .= $attachment_img;

								}

							// No links
							else :

								$output .= $attachment_img;

							endif; // End link check

						endif; // End video check

						// Display caption
						if ( empty( $attachment_video ) && 'true' == $caption && $caption_output ) :

							$output .= '<div class="' . $caption_classes . '"' . $caption_attributes . '>';

								if ( in_array( $caption_type, array( 'description', 'caption' ) ) ) :

									$output .= wpautop( $caption_output );

								else :

									$output .= $caption_output;

								endif;

							$output .= '</div>';

						endif;

					$output .= '</div>';

				$output .= '</div>';

			// End loop.
			$first_run = false; endforeach;

		$output .= '</div>';

		$output .= '<div class="wpex-slider-thumbnails sp-nc-thumbnails wpex-grid wpex-grid-cols-' . absint( $thumbnails_columns ) . '">';

			// Loop through attachments to display thumbnails.
			foreach ( $attachments as $attachment => $custom_link ) :

				// Output thumbnail image.
				$output .= vcex_get_post_thumbnail( [
					'attachment'  => $attachment,
					'size'        => 'wpex_custom',
					'width'       => $img_thumb_width,
					'height'      => $img_thumb_height,
					'crop'        => ( 'soft-crop' !== $atts['img_thumb_crop'] ) ? $atts['img_thumb_crop'] : false,
					'class'       => 'wpex-slider-thumbnail wpex-block wpex-w-100 sp-nc-thumbnail',
					'retina_data' => 'retina',
					'lazy'        => false,
				] );

			endforeach;

		$output .= '</div>';

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

endif;
