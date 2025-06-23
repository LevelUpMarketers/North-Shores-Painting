<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Class used to resize and crop images.
 */
class Resize_Image {

	/**
	 * Instance.
	 */
	protected static $instance;

	/**
	 * Class Instance.
	 */
	public static function getInstance() {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Image Resizing Happens Here.
	 */
	public function process( $args ) {
		$args = self::sanitize_args( $args );

		// Return if args are null.
		if ( ! $args ) {
			return;
		}

		// Extract args.
		extract( $args );

		// Define intermediate size.
		$intermediate_size = $size;

		// Return full size (no extra checks needed and no retina required).
		if ( 'full' === $intermediate_size ) {

			if ( $retina ) {
				return;
			}

			if ( $image_src ) {
				if ( $attachment ) {
					return \wp_get_attachment_image_src( $attachment, 'full' );
				}
				return $image_src;
			}

			if ( $attachment ) {

				// Fixes bug where full sizes images may have been cropped and saved.
				$meta = \wp_get_attachment_metadata( $attachment );
				if ( $meta && ! empty( $meta['sizes']['full']['wpex_dynamic'] ) ) {
					unset( $meta['sizes']['full'] );
					\update_post_meta( $attachment, '_wp_attachment_metadata', $meta );
				}

				if ( $src = \wp_get_attachment_image_src( $attachment, 'full', false ) ) {
					$result = self::parse_attachment_src( $src );
					return self::parse_result( $result, $args );
				}

			} elseif ( $image ) {
				return \set_url_scheme( $image );
			}

		}

		// Get upload path & dir.
		$upload_info = \wp_upload_dir();
		$upload_dir  = $upload_info[ 'basedir' ];
		$upload_url  = \set_url_scheme( $upload_info[ 'baseurl' ] ); // Make sure url scheme is correct

		// Get image path.
		if ( $attachment ) {

			$meta     = \wp_get_attachment_metadata( $attachment );
			$img_path = \get_attached_file( $attachment );
			$rel_path = \str_replace( $upload_dir, '', $img_path );

		} elseif ( $image ) {

			// Set correct url scheme.
			$image = \set_url_scheme( $image );

			// Image isn't in uploads so we can't dynamically resize it,
			// so return full url and empty width/height.
			if ( ! str_contains( $image, $upload_url ) ) {
				return self::parse_result( [
					'url'    => $image,
					'width'  => '',
					'height' => '',
				], $args );
			}

			$meta     = ''; // no meta for direct image input.
			$rel_path = \str_replace( $upload_url, '', $image );
			$img_path = $upload_dir . $rel_path;

		}

		// Make sure file exists.
		if ( ! \file_exists( $img_path ) ) {
			if ( ! empty( $image ) ) {
				return $image;
			}
			return;
		}

		// Get image info.
		$info = pathinfo( $img_path );
		$ext  = $info['extension'];

		// Get image size, using getimagesize is better then getting from meta as meta could be wrong.
		list( $orig_w, $orig_h ) = \getimagesize( $img_path );

		// Meta must be an array.
		if ( ! \is_array( $meta ) ) {
			$meta = [ $meta ];
		}

		// Define empty vars.
		$img_url = '';

		// Check what the image size would be after resizing/cropping.
		$dst_dims = \image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );

		// If we can't resize return original image.
		if ( false === $dst_dims || ! \is_array( $dst_dims ) ) {

			// Don't return original if we are generating a retina image, just bail.
			if ( $retina ) {
				return;
			}

			// Important !!! must return full image to prevent issues with cropped old images.
			// such as in WooCommerce.
			if ( $image_src ) {
				return \wp_get_attachment_image_src( $attachment, 'full', false );
			}

			if ( $src = \wp_get_attachment_image_src( $attachment, 'full', false ) ) {
				$result = self::parse_attachment_src( $src );
				return self::parse_result( $result, $args );
			}

		}

		// Get dimensions.
		$dst_w = $dst_dims[4];
		$dst_h = $dst_dims[5];

		// Define crop_suffix for custom crop locations.
		// Used for destination and for saving meta.
		$crop_suffix = '';
		if ( $crop && \is_array( $crop ) ) {
			$crop_suffix = \array_combine( $crop, $crop );
			$crop_suffix = \implode( '-', $crop_suffix );
		}

		// Define Intermediate size name.
		// Must be defined early on so retina image dst_w and dst_he matches non-retina image.
		if ( $meta ) {

			// If no size is defined then intermediate size should be the crop_suffix + height&width.
			if ( ! $intermediate_size ) {
				if ( $crop_suffix ) {
					$intermediate_size = "wpex_{$crop_suffix}-{$dst_w}x{$dst_h}";
				} else {
					$intermediate_size = "wpex_{$dst_w}x{$dst_h}";
				}
			}

			// Retina intermediate size should be same as intermediate size with added @2x.
			if ( $intermediate_size && $retina ) {
				$intermediate_size = "{$intermediate_size}@2x";
			}

		}

		// Check that the file size is smaller then the destination size.
		// If it's not smaller then we don't have to do anything but return the original image.
		if ( $orig_w > $dst_w || $orig_h > $dst_h ) {

			// Define image saving destination.
			$dst_rel_path = \str_replace( ".{$ext}", '', $rel_path );

			// Suffix.
			$suffix = "{$dst_w}x{$dst_h}";

			// Generate correct suffix based on crop_suffix and destination sizes.
			$suffix = $crop_suffix ? "{$crop_suffix}-{$suffix}" : $suffix;

			// Check original image destination
			$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

			// Retina should only be generated if the target image size already exists
			// No need to create a retina version for a non-existing image.
			if ( $retina && \file_exists( $destfilename ) && \getimagesize( $destfilename ) ) {

				$dst_w_2x = $dst_w * 2;
				$dst_h_2x = $dst_h * 2;

				// Return if the destination width or height aren't at least 2x as big.
				if ( ( $orig_w < $dst_w_2x ) || ( $orig_h < $dst_h_2x ) ) {
					return;
				}

				// Set retina version to @2x the output of the default cropped image.
				if ( $orig_h == $dst_h_2x && $orig_w == $dst_w_2x ) {
					$dst_dims = self::image_resize_dimensions( $orig_w, $orig_h, $dst_w_2x, $dst_h_2x, $crop );
				} else {
					$dst_dims = \image_resize_dimensions( $orig_w, $orig_h, $dst_w_2x, $dst_h_2x, $crop );
				}

				// If image_resize_dimensions fails we can't generate the retina image.
				if ( false === $dst_dims || ! \is_array( $dst_dims ) ) {
					return;
				}

				// Set retina image destination width and height.
				$dst_w = $dst_dims[4];
				$dst_h = $dst_dims[5];

				// Set correct resize dimensions for retina images.
				$width  = $width * 2;
				$height = $height * 2;

				// Add retina sufix.
				$suffix = $suffix . '@2x';

				// Update destfilename to include retina suffix.
				$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

			}

			// If file exists set image url else generate image.
			if ( \file_exists( $destfilename ) && \getimagesize( $destfilename ) ) {
				$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
			}

			// Cached image doesn't exist so lets try and create it.
			// Can not use image_make_intermediate_size() unfortunately because it.
			// does not allow for custom naming conventions or custom crop arrays.
			else {

				$editor = \wp_get_image_editor( $img_path );

				// Create image.
				if ( ! \is_wp_error( $editor ) && ! \is_wp_error( $editor->resize( $width, $height, $crop ) ) ) {

					// Get resized file.
					$filename = $editor->generate_filename( $suffix );
					$editor   = $editor->save( $filename );

					// Set new image url from resized image.
					if ( ! \is_wp_error( $editor ) ) {
						$path    = \str_replace( $upload_dir, '', $editor['path'] );
						$img_url = $upload_url . $path;

						/**
						 * Runs after saving the resized image.
						 *
						 * @param int $attachment The image attachement ID.
						 * @param string $intermediate_size The intermediate image size name.
						 */
						do_action( 'totaltheme/resize-image/after_save_image', $attachment, $intermediate_size );

					}

				}

			} // End cache check.

		} // End size check.

		// If dynamic image couldn't be created return original image.
		if ( ! $img_url ) {

			// Don't return original if we are generating a retina image.
			if ( $retina ) {
				return;
			}

			// Important !!! must return full image to prevent issues with cropped old images
			// such as in WooCommerce.
			if ( $image_src ) {
				return \wp_get_attachment_image_src( $attachment, 'full', false );
			}

			if ( $src = \wp_get_attachment_image_src( $attachment, 'full', false ) ) {
				$result = self::parse_attachment_src( $src );
				return self::parse_result( $result, $args );
			}

		}

		// Update attachment meta data if custom size not found (fallback for images already created)
		// or if the sizes don't match up.
		// @todo update to use wp_generate_attachment_metadata (I tried but seemed way slower, maybe we can optimize that).
		if ( $meta ) {

			// Get destination filename.
			$dst_filename = \basename( \str_replace( $upload_url . '/', '', $img_url ) );

			// Meta sizes.
			$meta_sizes = $meta['sizes'] ?? [];

			// Update meta if needed.
			if ( ! \array_key_exists( $intermediate_size, $meta_sizes )
				|| empty( $meta_sizes[$intermediate_size]['file'] )
				|| $meta_sizes[$intermediate_size]['file'] !== $dst_filename
			) {

				// Make sure meta sizes exist if not lets add them.
				$meta['sizes'] = $meta_sizes;

				// Check correct mime type.
				$mime_type = \wp_check_filetype( $img_url );
				$mime_type = $mime_type['type'] ?? '';

				// Add cropped image to image meta.
				if ( 'full' !== $size ) {
					$meta['sizes'][$intermediate_size] = [
						'file'         => $dst_filename,
						'width'        => $dst_w,
						'height'       => $dst_h,
						'mime-type'    => $mime_type,
						'wpex_dynamic' => true,
					];
				}

				// Update meta.
				// wp_update_attachment_metadata( $attachment, $meta ); @todo use wp_update_attachment_metadata instead.
				\update_post_meta( $attachment, '_wp_attachment_metadata', $meta ); // fix smush-it plugin error.

			}

			// Set intermediate var to true.
			$is_intermediate = true;

			// Return image url pased through the 'wp_get_attachment_image_src' wp filter.
			// This should provide better support to 3rd party image plugins.
			if ( ! $image_src ) {
				$src = \wp_get_attachment_image_src( $attachment, $intermediate_size, false );
				if ( isset( $src[0] ) ) {
					$img_url = $src[0];
				}
			}

		}

		// Return result.
		if ( $image_src || 'src' === $return ) {
			return array( $img_url, $dst_w, $dst_h, $is_intermediate );
		} else {
			return self::parse_result( [
				'url'             => $img_url,
				'width'           => $dst_w,
				'height'          => $dst_h,
				'is_intermediate' => $is_intermediate,
			], $args );
		}
	}

	/**
	 * Get resized image dimensions.
	 * We don't use the native image_resize_dimensions() so we can bypass filters.
	 *
	 * @todo We don't appear to be using this, should we use it or remove it?
	 * @since 1.0.0
	 * @see https://developer.wordpress.org/reference/functions/image_resize_dimensions/#source
	 */
	private static function image_resize_dimensions( $orig_w, $orig_h, $dest_w, $dest_h, $crop = false ) {
		if ( $orig_w <= 0 || $orig_h <= 0 ) {
			return false;
		}

		// At least one of $dest_w or $dest_h must be specific.
		if ( $dest_w <= 0 && $dest_h <= 0 ) {
			return false;
		}

		// Stop if the destination size is larger than the original image dimensions.
		if ( empty( $dest_h ) ) {
			if ( $orig_w < $dest_w ) {
				return false;
			}
		} elseif ( empty( $dest_w ) ) {
			if ( $orig_h < $dest_h ) {
				return false;
			}
		} else {
			if ( $orig_w < $dest_w && $orig_h < $dest_h ) {
				return false;
			}
		}

		if ( $crop ) {
			/*
			 * Crop the largest possible portion of the original image that we can size to $dest_w x $dest_h.
			 * Note that the requested crop dimensions are used as a maximum bounding box for the original image.
			 * If the original image's width or height is less than the requested width or height
			 * only the greater one will be cropped.
			 * For example when the original image is 600x300, and the requested crop dimensions are 400x400,
			 * the resulting image will be 400x300.
			 */
			$aspect_ratio = $orig_w / $orig_h;
			$new_w        = \min( $dest_w, $orig_w );
			$new_h        = \min( $dest_h, $orig_h );

			if ( ! $new_w ) {
				$new_w = (int) \round( $new_h * $aspect_ratio );
			}

			if ( ! $new_h ) {
				$new_h = (int) \round( $new_w / $aspect_ratio );
			}

			$size_ratio = \max( $new_w / $orig_w, $new_h / $orig_h );

			$crop_w = \round( $new_w / $size_ratio );
			$crop_h = \round( $new_h / $size_ratio );

			if ( ! \is_array( $crop ) || \count( $crop ) !== 2 ) {
				$crop = array( 'center', 'center' );
			}

			list( $x, $y ) = $crop;

			if ( 'left' === $x ) {
				$s_x = 0;
			} elseif ( 'right' === $x ) {
				$s_x = $orig_w - $crop_w;
			} else {
				$s_x = \floor( ( $orig_w - $crop_w ) / 2 );
			}

			if ( 'top' === $y ) {
				$s_y = 0;
			} elseif ( 'bottom' === $y ) {
				$s_y = $orig_h - $crop_h;
			} else {
				$s_y = \floor( ( $orig_h - $crop_h ) / 2 );
			}
		} else {
			// Resize using $dest_w x $dest_h as a maximum bounding box.
			$crop_w = $orig_w;
			$crop_h = $orig_h;

			$s_x = 0;
			$s_y = 0;

			if ( \function_exists( 'wp_constrain_dimensions' ) ) {
				list( $new_w, $new_h ) = \wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
			}
		}

		// The return array matches the parameters to imagecopyresampled().
		// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
		return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
	}

	/**
	 * Parse arguments.
	 */
	private static function sanitize_args( $args ) {
		$defaults = [
			'attachment'      => '',
			'image'           => '',
			'width'           => '',
			'height'          => '',
			'crop'            => '',
			'retina'          => false,
			'return'          => 'array',
			'size'            => '',
			'is_intermediate' => false,
			'image_src'       => null, // Allows cropping inside the wp_get_attachment_image_src filter
		];

		// Parse args.
		$args = wp_parse_args( $args, $defaults );

		// Return null if there isn't any image or attachment.
		if ( ! $args['attachment'] && ! $args['image'] ) {
			return null;
		}

		// Get dimensions for image size.
		if ( $args['size']
			&& ! in_array( $args['size'], array( 'full', 'wpex-custom', 'wpex_custom' ) )
			&& empty( $args['width'] )
			&& empty( $args['height'] ) ) {
				$dims = \wpex_get_thumbnail_sizes( $args['size'] );
				if ( \is_array( $dims ) ) {
					$args['width'] = $dims['width'] ?? '';
					$args['height'] = $dims['height'] ?? '';
					$args['crop'] = $dims['crop'] ?? $args['crop'];
				}
		}

		// Sanitize width and height to make sure they are integers.
		$args['width']  = \intval( $args['width'] );
		$args['height'] = \intval( $args['height'] );

		// Check width if empty or greater then 9999 set to 9999.
		if ( empty( $args['width'] ) || $args['width'] >= 9999 ) {
			$args['width'] = 9999;
		}

		// Check height if empty or greater then 9999 set to 9999.
		if ( empty( $args['height'] ) || $args['height'] >= 9999 ) {
			$args['height'] = 9999;
		}

		// If image width and height equal '9999' simply return "full" size.
		if ( 9999 == $args['width'] && 9999 == $args['height'] ) {
			$args['size'] = 'full';
		}

		// Set crop to false for soft-crop.
		if ( 'soft-crop' === $args['crop'] || $args['height'] >= 9999 || $args['width'] >= 9999 ) {
			$args['crop'] = false;
		}

		// Sanitize crop.
		$args['crop'] = self::parse_crop( $args['crop'] );

		// Sanitize return.
		if ( $args['image_src'] ) {
			$args['return'] = 'src';
		}

		return $args;
	}

	/**
	 * Parse crop value.
	 */
	public static function parse_crop( $crop ) {
		if ( 'false' === $crop || false === $crop ) {
			return false;
		}

		// Return default crop if crop is an empty string.
		if ( '' === $crop || null === $crop ) {
			return true;
		}

		// center-center crop needs to be set to true,
		// prevent's a prefix from being added to the image suffix.
		if ( 'center-center' === $crop || 'true' === $crop || true === $crop ) {
			return true;
		}

		if ( \is_string( $crop ) ) {
			$crop_locations = \wpex_image_crop_locations();
			if ( \array_key_exists( $crop, $crop_locations ) ) {
				return \explode( '-', $crop );
			}
		}

		return $crop;
	}

	/**
	 * Parses the attachment src.
	 */
	public static function parse_attachment_src( $src ) {
		return [
			'url'             => $src[0] ?? '',
			'width'           => $src[1] ?? '',
			'height'          => $src[2] ?? '',
			'is_intermediate' => $src[3] ?? '',
		];
	}

	/**
	 * Return correct result.
	 */
	public static function parse_result( $result, $args ) {
		if ( 'array' === $args['return'] ) {
			return $result;
		} else {
			return $result['url'];
		}
	}

}
