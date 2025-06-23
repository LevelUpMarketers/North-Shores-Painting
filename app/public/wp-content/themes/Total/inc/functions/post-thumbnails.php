<?php

defined( 'ABSPATH' ) || exit;

/**
 * Returns thumbnail sizes.
 *
 * @todo update to use wp_get_registered_image_subsizes()
 */
function wpex_get_thumbnail_sizes( $size = '' ) {
	global $_wp_additional_image_sizes;

	$sizes = [
		'full'  => [
			'width'  => 9999,
			'height' => 9999,
			'crop'   => false,
		],
	];

	$get_intermediate_image_sizes = get_intermediate_image_sizes();

	foreach ( $get_intermediate_image_sizes as $_size ) {
		if ( in_array( $_size, [ 'thumbnail', 'medium', 'large' ], true ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = [
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'] ?? '',
				'height' => $_wp_additional_image_sizes[ $_size ]['height'] ?? '',
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'] ?? '',
			];
		}
	}

	if ( $size ) {
		return $sizes[ $size ] ?? false;
	}

	return $sizes;
}

/**
 * Generates a retina image.
 */
function wpex_generate_retina_image( $attachment, $width, $height, $crop, $size = '' ) {
	return wpex_image_resize( [
		'attachment' => $attachment,
		'width'      => $width,
		'height'     => $height,
		'crop'       => $crop,
		'return'     => 'url',
		'retina'     => true,
		'size'       => $size, // Used to update metadata accordingly
	] );
}

/**
 * Echo post thumbnail url.
 */
function wpex_post_thumbnail_url( $args = [] ) {
	echo wpex_get_post_thumbnail_url( $args );
}

/**
 * Return post thumbnail url.
 */
function wpex_get_post_thumbnail_url( $args = [] ) {
	$args['return'] = 'url';
	return wpex_get_post_thumbnail( $args );
}

/**
 * Return post thumbnail src.
 */
function wpex_get_post_thumbnail_src( $args = [] ) {
	$args['return'] = 'src';
	return wpex_get_post_thumbnail( $args );
}

/**
 * Outputs the img HTMl thubmails used in the Total VC modules.
 */
function wpex_post_thumbnail( $args = [] ) {
	echo wpex_get_post_thumbnail( $args );
}

/**
 * Returns HTMl for post thumbnails.
 *
 * @todo Create a class for this to better organize things.
 */
function wpex_get_post_thumbnail( $args = [] ) {
	$defaults = [
		'post'            => null,
		'attachment'      => '',
		'size'            => '',
		'width'           => '',
		'height'          => '',
		'crop'            => '',
		'return'          => 'html',
		'style'           => '',
		'alt'             => '',
		'before'          => '',
		'after'           => '',
		'attributes'      => [],
		'class'           => [],
		'add_image_dims'  => true,
		'placeholder'     => false,
		'lazy'            => true,
		'aspect_ratio'    => '',
		'object_fit'      => 'cover',
		'object_position' => '',
		'mix_blend_mode'  => '',
		'apply_filters'   => '',
		'filter_arg1'     => '',
		'retina'          => wpex_is_retina_enabled(),
		'decoding'        => apply_filters( 'wp_img_tag_add_decoding_attr', true ) ? 'async' : '',
	];

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Apply filters = Must run here !!
	if ( $args['apply_filters'] ) {
		$args = apply_filters( $args['apply_filters'], $args, $args['filter_arg1'] );
	}

	// @todo add a default filter that will run here so you can target any image_size (totaltheme/post-thumbnail/args/)

	// If attachment is empty get attachment from current post.
	if ( empty( $args['attachment'] ) ) {
		$args['attachment'] = get_post_thumbnail_id( $args['post'] );
	}

	// Custom post thumbnail output that runs before fetching the thumbnail.
	$custom_output = apply_filters( 'wpex_get_post_thumbnail_custom_output', null, $args );

	if ( $custom_output ) {
		return $custom_output;
	}

	// Extract args.
	extract( $args );

	// Check if return has been set to null via filter.
	if ( null === $return ) {
		return;
	}

	// Return placeholder image.
	if ( $placeholder || 'placeholder' === $attachment ) {
		return ( $placeholder = wpex_placeholder_img_src() ) ? '<img src="' . esc_url( $placeholder ) . '">' : '';
	}

	// Set/sanitize main vars.
	$is_custom     = false;
	$width         = absint( $width );
	$height        = absint( $height );
	$fetchpriority = $attributes['fetchpriority'] ?? '';

	// If size is not defined it's either going to be custom or full.
	if ( ! $size ) {
		if ( $width || $height ) {
			$size = 'wpex_custom';
			$is_custom = true;
		} else {
			$size = 'full';
		}
	}

	// Set size to null if set to custom as we won't need it later.
	if ( 'wpex-custom' === $size || 'wpex_custom' === $size ) {
		$size = null;
		$is_custom = true;
	}

	// Check aspect ratio (must run before the width/height check so it gets added for the full size).
	if ( ! $aspect_ratio && $size && 'wpex_custom' !== $size && 'full' !== $size ) {
		$aspect_ratio = get_theme_mod( "{$size}_image_aspect_ratio" );
		if ( $aspect_ratio ) {
			$object_fit = get_theme_mod( "{$size}_image_fit" ) ?: $object_fit;
			$object_position = get_theme_mod( "{$size}_image_position" ) ?: $object_position;
		}
	}

	// Get image dimensions for defined image size.
	if ( $size && $size !== 'full' ) {
		$dims = wpex_get_thumbnail_sizes( $size );
		if ( $dims && is_array( $dims ) ) {
			if ( array_key_exists( 'width', $dims ) ) {
				$width = $dims['width'];
			}
			if ( array_key_exists( 'height', $dims ) ) {
				$height = $dims['height'];
			}
			if ( array_key_exists( 'crop', $dims ) ) {
				$crop = $dims['crop'];
			}
		}
		// If image size is empty or greater then or equal to 9999 set size to full.
		// This allows the WordPress srcset function to work properly on full images,
		// as it bypasses the theme's resizing.
		if ( ! $width && ! $height || ( $width >= 9999 && $height >= 9999 ) ) {
			$size = 'full';
		}
	}

	// Set size to full if size isn't defined and the width/height are massive values;
	if ( ! $size && ( ( $width >= 9999 && $height >= 9999 ) || ( ! $width && ! $height ) ) ) {
		$size = 'full';
	}

	// Disable lazy loading.
	if ( ! empty( $attributes['data-no-lazy'] ) || 'high' === $fetchpriority ) {
		$lazy = false;
	}

	if ( ! $lazy ) {
		$attributes['data-no-lazy'] = '1';
	}

	// Extra attributes for html return.
	if ( 'html' === $return ) {

		// Convert class to array for easily adding new classes.
		if ( is_string( $class ) ) {
			$class = explode( ' ', $class );
		}

		// Add native browser lazy loading support for theme featured images.
		if ( $lazy ) {
			if ( $has_lazy_loading = apply_filters( 'wpex_has_post_thumbnail_lazy_loading', get_theme_mod( 'post_thumbnail_lazy_loading', true ) ) ) {
				$attributes['loading'] = 'lazy';
			}
		}

		// Add skip-lazy class for use with 3rd party plugins like Jetpack.
		else {
			if ( 'async' === $decoding ) {
				$decoding = '';
			}
			$class[] = 'skip-lazy';
		}

		// Add aspect ratio & object fit classes @note aspect ratio must be defined for opbect fit and object position to be added
		if ( $aspect_ratio && array_key_exists( $aspect_ratio, totaltheme_get_aspect_ratio_choices() ) ) {
			$class[] = 'wpex-aspect-' . str_replace( '/', '-', $aspect_ratio );
			if ( $object_fit && in_array( $object_fit, [ 'cover', 'contain', 'fill', 'scale-down', 'none' ], true ) ) {
				$class[] = "wpex-object-{$object_fit}";
			}
			if ( $object_position && in_array( $object_position, [ 'top', 'center', 'bottom', 'left-top', 'left-center', 'left-bottom', 'right-top', 'right-center', 'right-bottom' ], true ) ) {
				$class[] = "wpex-object-{$object_position}";
			}
		}

		// Mix blend mode.
		if ( $mix_blend_mode && in_array( $mix_blend_mode, [ 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'hard-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity', 'plus-lighter', 'multiply', 'screen', 'darken', 'overlay' ], true ) ) {
			$class[] = "wpex-mix-blend-{$mix_blend_mode}";
		}

		// Add custom classes.
		if ( $class ) {
			$attributes['class'] = implode( ' ', array_map( 'esc_attr', $class ) ); // class must be a string to pass to wp_get_attachment_image
		}

		// Add style.
		if ( $style ) {
			$attributes['style'] = $style;
		}

		// Add alt.
		if ( $alt ) {
			$attributes['alt'] = esc_attr( $alt );
		}

		// Add decoding attribute
		if ( $decoding && ( ! $fetchpriority || 'high' !== $fetchpriority ) ) {
			$attributes['decoding'] = esc_attr( $decoding );
		}

	}

	/**
	 * On demand resizing.
	 * Custom Total output (needs to run even when image_resizing is disabled for custom image cropping in WPBakery and widgets).
	 */
	if ( 'full' !== $size && ( get_theme_mod( 'image_resizing', true ) || $is_custom ) ) {

		// Crop standard image.
		$image = wpex_image_resize( [
			'attachment' => $attachment,
			'size'       => $size,
			'width'      => $width,
			'height'     => $height,
			'crop'       => $crop,
		] );

		// Image couldn't be generated for some reason or another.
		if ( ! $image ) {
			return;
		}

		// Return image URL.
		if ( 'url' === $return ) {
			return $image['url'];
		}

		// Return src.
		if ( 'src' === $return ) {
			return [
				$image['url'],
				$image['width'],
				$image['height'],
				$image['is_intermediate'],
			];
		}

		// Return image HTMl (default return)
		if ( 'html' === $return ) {

			// Get image srcset.
			if ( $size ) {
				$srcset = wp_get_attachment_image_srcset( $attachment, $size );
			} elseif ( ! empty( $image['width'] ) && ! empty( $image['height'] ) ) {
				$srcset = wp_get_attachment_image_srcset( $attachment, array( $image['width'], $image['height'] ) );
			}

			// Add src tag.
			$attributes['src'] = esc_url( $image['url'] );

			// Check for custom alt if no alt is defined manually.
			if ( ! $alt ) {
				$alt = wpex_get_attachment_data( $attachment, 'alt' );
			}

			// Add alt attribute (add empty if none is found).
			$attributes['alt'] = ( $alt && is_string( $alt ) ) ? trim( esc_attr( $alt ) ) : '';

			// Generate retina version.
			if ( $retina ) {
				$retina_img = apply_filters( 'wpex_get_post_thumbnail_retina', null, $attachment, $size );

				if ( ! $retina_img ) {
					$retina_img = wpex_generate_retina_image( $attachment, $width, $height, $crop, $size );
				}

				// Add retina attributes.
				if ( $retina_img ) {
					//$attributes['data-' . $retina_data] = $retina_img; // @deprecated 5.3
					if ( ! empty( $srcset ) ) {
						$srcset .= ', ' . $retina_img . ' 2x';
					} else {
						$srcset = $retina_img . ' 2x';
					}
					// By default retina images will display at the original image size,
					// by setting this filter to false retina images will render at their full size.
					if ( ! apply_filters( 'wpex_retina_resize', true ) ) {
						$attributes['data-no-resize'] = '';
						$add_image_dims = false;
					}
				}

			}

			// Define srcset attribute.
			if ( ! empty( $srcset ) && is_string( $srcset ) ) {
				$attributes['srcset'] = esc_attr( trim( $srcset ) );
			}

			// Add width and height if not empty (we don't want to add 0 values)
			// Also only add the dims if we haven't specified them previously via the attributes param.
			if ( true === $add_image_dims ) {
				if ( ! empty( $image['width'] ) && empty( $attributes['width'] ) ) {
					$attributes['width'] = intval( $image['width'] );
				}
				if ( ! empty( $image['height'] ) && empty( $attributes['height'] ) ) {
					$attributes['height'] = intval( $image['height'] );
				}
			}
			$attr = (array) apply_filters( 'wpex_get_post_thumbnail_image_attributes', $attributes, $attachment, $args );
			//$img_html = wp_get_attachment_image( $attachment, $size, false, $attributes );
			$img_html = (string) apply_filters( 'wpex_post_thumbnail_html', '<img ' . wpex_parse_attrs( $attributes ) . '>' );
			if ( $img_html ) {
				return $before . $img_html . $after;
			}

		}

	}

	// Return image from add_image_size.
	// If on-the-fly is disabled for defined sizes or image size is set to "full".
	else {

		// Return image URL.
		if ( 'url' === $return ) {
			return wp_get_attachment_image_url( $attachment, $size, false );
		}

		// Return src.
		elseif ( 'src' === $return ) {
			return wp_get_attachment_image_src( $attachment, $size, false );
		}

		// Return image HTML.
		// @todo should this use get_the_post_thumbnail instead?.
		elseif ( 'html' === $return ) {

			// Get alt (we need to do this to support WPML)
			if ( ! $alt ) {
				$attachment_alt = wpex_get_attachment_data( $attachment, 'alt' );
				if ( $attachment_alt ) {
					$attributes['alt'] = trim( esc_attr( $attachment_alt ) );
				}
			}

			// Demove lazy loading and decoding attributes.
			if ( ! $lazy ) {
				$attributes['loading']  = false;
				$attributes['decoding'] = false; // @todo should we keep decoding?
			}

			// Parses the style attribute to prevent issues where the style tag may already be there.
			if ( ! empty( $attributes['style'] )
				&& is_string( $attributes['style'] )
				&& str_starts_with( trim( $attributes['style'] ), 'style=' )
			) {
				$parsed_style = trim( $attributes['style'] );
				$parsed_style = str_replace( 'style="', '', $parsed_style );
				$parsed_style = substr( $parsed_style, 0, -1 );
				$attributes['style'] = $parsed_style;
			}

			$image = wp_get_attachment_image( $attachment, $size, false, $attributes );
			$img_html = (string) apply_filters( 'wpex_post_thumbnail_html', $image );

			if ( $img_html ) {
				return $before . $img_html . $after;
			}

		}

	}
}

/**
 * Returns secondary thumbnail.
 */
function totaltheme_get_post_secondary_thumbnail_id( $post_id = 0, $check_gallery = true ): int {
	$thumbnail_id = 0;
	$post_id      = $post_id ?: get_the_ID();
	$meta         = get_post_meta( $post_id, 'wpex_secondary_thumbnail', true );
	if ( ! empty( $meta ) ) {
		$thumbnail_id = (int) $meta;
	} elseif ( $check_gallery ) {
		$gallery_ids = wpex_get_gallery_ids( $post_id );
		if ( $gallery_ids && is_array( $gallery_ids ) ) {
			$post_thumbnail = (int) get_post_thumbnail_id( $post_id );
			if ( isset( $gallery_ids[0] ) && (int) $gallery_ids[0] !== $post_thumbnail ) {
				$thumbnail_id = $gallery_ids[0];
			} elseif ( ! empty( $gallery_ids[1] ) && is_numeric( $gallery_ids[1] ) ) {
				$thumbnail_id = $gallery_ids[1];
			}
		}
	}
	$thumbnail_id = apply_filters( 'wpex_secondary_post_thumbnail_id', $thumbnail_id, $post_id );
	return (int) apply_filters( 'totaltheme/post/secondary_thumbnail/id', $thumbnail_id, $post_id );
}
