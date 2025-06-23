<?php

defined( 'ABSPATH' ) || exit;

/**
 * Custom hook that returns custom content after any entry.
 *
 * @todo remove instance.
 */
function wpex_get_entry_media_after( $instance = '' ) {
	return apply_filters( 'wpex_get_entry_media_after', '', $instance );
}

/**
 * Outputs entry media after hook content.
 *
 * @todo hook instance.
 */
function wpex_entry_media_after( $instance = '' ) {
	echo wpex_get_entry_media_after( $instance );
}

/**
 * Returns post media position.
 */
function wpex_get_custom_post_media_position( $post_id = '', $context = 'deprecated' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$position = null;

	if ( 'post' === get_post_type()
		&& true === wpex_validate_boolean( get_theme_mod( 'blog_post_media_position_above' ) )
	) {
		$position = 'above';
	}

	if ( $post_id ) {
		$meta = get_post_meta( $post_id, 'wpex_post_media_position', true );
		if ( $meta ) {
			$position = $meta;
		}

	}

	/**
	 * Filters the current post media position.
	 *
	 * @param string|null $position
	 */
	$position = apply_filters( 'wpex_get_custom_post_media_position', $position );

	return $position;
}

/**
 * Checj if post has a custom media position.
 */
function wpex_has_custom_post_media_position( $post_id = '' ) {
	return (bool) wpex_get_custom_post_media_position( $post_id );
}

/**
 * Echo post media.
 */
function wpex_post_media( $post = '', $args = [] ) {
	echo wpex_get_post_media( $post, $args );
}

/**
 * Returns post media.
 */
function wpex_get_post_media( $post = '', $args = [] ) {
	$post      = get_post( $post );
	$post_id   = ! empty( $post ) ? $post->ID : false;
	$post_type = get_post_type( $post );

	if ( 'post' === $post_type ) {
		$post_type = 'blog'; // we use blog to referance standard posts.
	}

	$defaults = [
		'thumbnail_args' => [
			'post' => $post,
			'size' => "{$post_type}_post",
		],
		'lightbox'        => false,
		'supported_media' => [ 'video', 'audio', 'gallery', 'thumbnail' ],
	];

	$output = '';
	$args = wp_parse_args( $args, $defaults );

	if ( in_array( 'video', $args['supported_media'] ) && $video = wpex_get_post_video( $post_id ) ) {
		$output = wpex_get_post_video_html( $video );
	} elseif ( in_array( 'audio', $args['supported_media'] ) && $audio = wpex_get_post_audio( $post_id ) ) {
		$output = wpex_get_post_audio_html( $audio );
	} elseif ( in_array( 'gallery', $args['supported_media'] ) && wpex_has_post_gallery( $post_id ) ) {
		$output = wpex_get_post_media_gallery_slider( $post_id, $args );
	} elseif ( in_array( 'thumbnail', $args['supported_media'] ) && has_post_thumbnail( $post_id ) ) {
		if ( $args['lightbox'] ) {
			wpex_enqueue_lightbox_scripts();
			$output .= '<a href="' . wpex_get_lightbox_image( get_post_thumbnail_id( $post_id ) ) . '" class="wpex-lightbox">';
			$output .= wpex_get_post_thumbnail( $args['thumbnail_args'] );
			$output .= '</a>';
		} else {
			$output = wpex_get_post_thumbnail( $args['thumbnail_args'] );
		}
	}

	if ( shortcode_exists( 'featured_revslider' ) ) {
		$output = do_shortcode( '[featured_revslider]' . $output . '[/featured_revslider]' );
	}

	/**
	 * Filters the post media output html.
	 *
	 * @param string $output
	 */
	$output = (string) apply_filters( 'wpex_get_post_media', $output, $post_id, $args );

	return $output;
}

/**
 * Displays post media gallery.
 */
function wpex_post_media_gallery_slider( $post_id = '', $args = [] ) {
	echo wpex_get_post_media_gallery_slider( $post_id, $args );
}

/**
 * Returns post media gallery.
 *
 * @todo move output to partial template file?
 */
function wpex_get_post_media_gallery_slider( $post_id = '', $args = [] ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = [
		'before'         => '',
		'after'          => '',
		'slider_data'    => apply_filters( 'wpex_get_post_media_gallery_slider_data', null ),
		'thumbnail_args' => [
			'size' => 'full',
		],
		'attachments'    => '',
		'lightbox'       => wpex_gallery_is_lightbox_enabled( $post_id ),
		'lightbox_title' => true,
		'thumbnails'     => true, // Old deprecated setting. Thumbnail check no in slider_data parameter since v4.4.1.
		'captions'       => true,
		'class'          => '',
	];

	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wpex_get_post_media_gallery_args', $args ); // Remove all other filters? Maybe we can make a single function that hooks into this filter for all the fallbacks? We really should only have 1 end filter.

	extract( $args );

	// Get attachments if not defined in $args.
	$attachments = $attachments ?: wpex_get_gallery_ids( $post_id );

	// Attachments are required.
	if ( ! $attachments || ! is_array( $attachments ) ) {
		return;
	}

	// Disable lazy loading.
	$thumbnail_args['lazy'] = false;

	// Sanitize extra class.
	if ( is_array( $class ) ) {
		$class = array_map( 'esc_attr', $class );
		$class = implode( ' ', $class );
	}

	// Enqueue slider scripts.
	wpex_enqueue_slider_pro_scripts();

	// Get slider data.
	$slider_data = wpex_get_post_slider_settings( $slider_data ); // parses with default values

	// Check if thumbnails are enabled.
	$thumbnails  = ( $thumbnails && isset( $slider_data['thumbnails'] ) && 'true' == $slider_data['thumbnails'] ) ? true : false;

	// Update slider data with current thumbnail display.
	$slider_data['thumbnails'] = wp_validate_boolean( $thumbnails ) ? 'true' : 'false';

	$output = '';
	$thumbnails_html = '';

	// Display preloader image.
	$preloader_class = 'wpex-slider-preloaderimg';
	if ( $class && is_string( $class ) ) {
		$preloader_class .= ' ' . trim( $class );
	}
	$output .= '<div class="' . esc_attr( $preloader_class ) . '">';

		$thumbnail_args['attachment'] = $attachments[0];
		$thumbnail_args['alt']        = (string) get_post_meta( $attachments[0], '_wp_attachment_image_alt', true );

		$output .= wpex_get_post_thumbnail( $thumbnail_args );

	$output .= '</div>';

	// Slider wrap attrs.
	$wrap_attrs = [
		'class' => [
			'wpex-slider',
			'slider-pro',
		],
		'data'  => wpex_get_slider_data( $slider_data ),
	];

	if ( $class ) {
		$wrap_attrs['class'][] = $class;
	}

	// Open slider wrap element.
	$output .= '<div ' . wpex_parse_attrs( $wrap_attrs ) . '>';

		// Slider attrs.
		$slides_attrs = [
			'class' => 'wpex-slider-slides sp-slides',
		];

		if ( $lightbox ) {
			wpex_enqueue_lightbox_scripts();
			$slides_attrs['class'] .= ' wpex-lightbox-group';
			if ( ! $lightbox_title ) {
				$slides_attrs['data-show_title'] = 'false';
			}
		}

		// Open inner slider element.
		$output .= '<div ' . wpex_parse_attrs( $slides_attrs ) . '>';

			// Loop through attachments.
			foreach ( $attachments as $attachment ) {

				// Get attachment data.
				$attachment_data    = wpex_get_attachment_data( $attachment );
				$attachment_alt     = $attachment_data['alt'] ;
				$attachment_video   = $attachment_data['video'];
				$attachment_caption = $attachment_data['caption'];

				// Get image output.
				$thumbnail_args['attachment'] = $attachment;
				$thumbnail_args['alt']        = $attachment_alt;
				$attachment_html_safe         = wpex_get_post_thumbnail( $thumbnail_args );

				// Add html to thumbnails.
				if ( $thumbnails ) {
					$small_thumb_args = $thumbnail_args;
					$small_thumb_args['class'] = 'wpex-slider-thumbnail sp-thumbnail';
					$thumbnails_html .= wpex_get_post_thumbnail( $small_thumb_args );
				}

				// Generate video.
				if ( $attachment_video ) {
					$attachment_video = wpex_video_oembed( $attachment_video, 'sp-video', [
						'youtube' => [
							'enablejsapi' => '1',
						]
					] );
				}

				$output .= '<div class="wpex-slider-slide sp-slide">';

					// Display attachment video.
					if ( $attachment_video ) {

						$output .= '<div class="wpex-slider-video">';
							$output .= $attachment_video;
						$output .= '</div>';

					// Display attachment image.
					} else {

						$output .= '<div class="wpex-slider-media wpex-clr">';

							// Display with lightbox.
							if ( $lightbox ) {

								$lightbox_link_attrs = [
									'href'      => wpex_get_lightbox_image( $attachment ),
									'title'     => esc_attr( $attachment_alt ),
									'class'     => 'wpex-lightbox-group-item',
									'data-type' => 'image',
								];

								if ( $lightbox_title ) {
									$lightbox_link_attrs['data-title'] = esc_attr( $attachment_alt );
								}

								$output .= wpex_parse_html( 'a', $lightbox_link_attrs, $attachment_html_safe );

							}

							// Display single image.
							else {
								$output .= $attachment_html_safe;
							}

							// Display captions.
							if ( $captions && $attachment_caption ) {
								$output .= '<div class="wpex-slider-caption sp-layer sp-black wpex-clr" data-position="bottomCenter" data-show-transition="up" data-hide-transition="down" data-width="100%" data-show-delay="500">';
									$output .= wp_kses_post( $attachment_caption );
								$output .= '</div>';
							}

						$output .= '</div>';

					}

				$output .= '</div>';

			}

		$output .= '</div>';

		// Show thumbnails if enabled.
		if ( $thumbnails && $thumbnails_html ) {
			$output .= '<div class="wpex-slider-thumbnails sp-thumbnails">';
				$output .= $thumbnails_html;
			$output .= '</div>';
		}

	$output .= '</div>';

	if ( $output ) {
		return $before . $output . $after;
	}
}
