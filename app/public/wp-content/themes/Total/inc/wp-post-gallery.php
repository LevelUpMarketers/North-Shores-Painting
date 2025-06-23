<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Create custom gallery output for the WP gallery shortcode.
 */
final class WP_Post_Gallery {

	/**
	 * Init.
	 */
	public static function init(): void {
		\add_action( 'init', [ self::class, 'on_init' ], 10 );
	}

	/**
	 * Get things started...adds extra check via filter that we can use for vendor integrations.
	 */
	public static function on_init(): void {
		\add_filter( 'wpex_image_sizes', [ self::class, 'add_image_sizes' ], 999 );
		if ( \wpex_is_request( 'frontend' ) ) {
			\add_filter( 'post_gallery', [ self::class, 'output' ], 10, 2 );
		}
	}

	/**
	 * Checks if the custom gallery is enabled.
	 */
	public static function is_enabled(): bool {
		if ( \totaltheme_is_integration_active( 'elementor' ) ) {
			$check = false;
		} else {
			$check = \wp_validate_boolean( \get_theme_mod( 'custom_wp_gallery_enable', true ) );
		}
		$check = \apply_filters( 'wpex_custom_wp_gallery_supported', $check );
		return (bool) \apply_filters( 'wpex_custom_wp_gallery', $check );
	}

	/**
	 * Adds image sizes for your galleries to the image sizes panel.
	 */
	public static function add_image_sizes( $sizes ): array {
		$sizes['gallery'] = [
			'label'   => \esc_html__( 'WordPress Gallery', 'total' ),
			'section' => 'other',
		];
		return $sizes;
	}

	/**
	 * Tweaks the default WP Gallery Output.
	 */
   public static function output( $output, $attr ) {
		$post = \get_post();
    	static $instance = 0;
    	$instance++;

		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( ! empty( $attr['ids'] ) ) {
			if ( empty( $attr['orderby'] ) ) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
	    }

		// Sanitize orderby statement.
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = \sanitize_sql_orderby( $attr['orderby'] );
			if ( ! $attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		// Get shortcode attributes.
		\extract( \shortcode_atts( [
			'order'      => 'ASC',
            'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'columns'    => 3,
			'gap'        => (string) \apply_filters( 'wpex_wp_gallery_shortcode_gap', '20' ),
			'include'    => '',
			'exclude'    => '',
			'img_height' => '',
			'img_width'  => '',
			'size'       => '',
			'crop'       => '',
		], $attr ) );

		// Sanitize gap.
		$gap = ( $gap = \absint( $gap ) ) ? (string) $gap : '20';

		// Get post ID.
		$id = \intval( $id );

		if ( 'RAND' === $order ) {
			$orderby = 'none';
		}

		if ( ! empty( $include ) ) {
			$include = \preg_replace( '/[^0-9,]+/', '', $include );
			$_attachments = \get_posts( [
					'include'        => $include,
					'post_status'    => '',
					'inherit'        => '',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $order,
					'orderby'        => \sanitize_sql_orderby( $orderby )
			] );

		$attachments = [];
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( ! empty( $exclude ) ) {
			$exclude     = \preg_replace( '/[^0-9,]+/', '', $exclude );
			$attachments = \get_children( [
				'post_parent'    => $id,
				'exclude'        => $exclude,
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'order'          => $order,
				'orderby'        => \sanitize_sql_orderby( $orderby )
			] );
		} else {
			$attachments = \get_children( [
				'post_parent'    => $id,
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'order'          => $order,
				'orderby'        => \sanitize_sql_orderby( $orderby )
			] );
		}

		if ( empty( $attachments ) ) {
        	return '';
    	}

		if ( \is_feed() ) {
			$output = "\n";
			$size   = $size ?: 'thumbnail';
			foreach ( $attachments as $attachment_id => $attachment )
				$output .= \wp_get_attachment_link( $attachment_id, $size, true ) . "\n";
			return $output;
		}

		// Get columns #
		$columns = \intval( $columns );

		// Set cropping sizes when gallery is greater than 1 column.
		// @todo should this be always?
		if ( $columns > 1 ) {
			$img_width  = $img_width ?: (string) \get_theme_mod( 'gallery_image_width' );
			$img_height = $img_height ?: (string) \get_theme_mod( 'gallery_image_height' );
			$crop       = $crop ?: (string) \get_theme_mod( 'gallery_image_crop' , 'center-center' );
		}

		// Get image aspect ratio.
		$img_aspect_ratio = \get_theme_mod( 'gallery_image_aspect_ratio' );
		if ( $img_aspect_ratio ) {
			$img_fit = \get_theme_mod( 'gallery_image_fit' ) ?: 'cover';
		}

		// Sanitize cropping.
		$size = $size ?: 'large';
		$size = ( $img_width || $img_height ) ? 'wpex_custom' : $size;

		// Load lightbox scripts.
		\wpex_enqueue_lightbox_scripts();

		// Gallery class.
		$gallery_class = [
			'wpex-gallery', // @todo can we rename this class?
			'wpex-grid',
			'wpex-grid-cols-' . \sanitize_html_class( $columns ),
			'wpex-gap-' . \sanitize_html_class( $gap ),
			'wpex-lightbox-group',
		];

		/**
		 * Filter the custom wp gallery grid class.
		 *
		 * @param $grid_class array
		 */
		$gallery_class = \apply_filters( 'wpex_custom_wp_gallery_class', $gallery_class );

		// Begin output.
		$output = '<div id="gallery-' . \esc_attr( $instance ) . '" class="' . \esc_attr( \implode( ' ', $gallery_class ) )  . '">';

			// Begin Loop.
			foreach ( $attachments as $attachment_id => $attachment ) {

				// Attachment Vars.
				$attachment_id = $attachment->ID;
				$alt           = (string) \get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				$video         = (string) \get_post_meta( $attachment_id, '_video_url', true );
				$excerpt       = (string) $attachment->post_excerpt ?? '';
				$excerpt       = ! empty( trim( $excerpt ) ) ? $excerpt : '';

				// Sanitize Video URL.
				if ( $video ) {
					if ( $video_embed_url = \wpex_get_video_embed_url( $video ) ) {
						$video = $video_embed_url;
					}
				}

				// Define lightbox data var.
				$lightbox_data = '';

				// Get lightbox image.
				$lightbox_image = \wpex_get_lightbox_image( $attachment_id );

				// Set correct lightbox URL.
				$lightbox_url = $video ?: $lightbox_image;

				// Set correct data values.
				if ( $video ) {
					$lightbox_data .= ' data-thumb="' . \esc_attr( esc_url( $lightbox_image ) ) . '"';
				} elseif ( $excerpt ) {
					$lightbox_data .= ' data-caption="' . \esc_attr( $excerpt ) . '"';
				}

				// Add title for lightbox.
				if ( \get_theme_mod( 'lightbox_titles', true ) && $alt ) {
					$lightbox_data .= ' data-title="' . \esc_attr( $alt ) . '"';
				}

				// Entry classes.
				$entry_classes = [ 'gallery-item' ];
				$entry_classes = \apply_filters( 'wpex_wp_gallery_entry_classes', $entry_classes );

				// Start Gallery Item.
				$output .= '<figure class="' . \esc_attr( \implode( ' ', $entry_classes ) ) . '">';

					// Display image.
					$output .= '<a href="' . \esc_url( $lightbox_url ) . '" class="wpex-lightbox-group-item"' . $lightbox_data . '>';

						$output .= \wpex_get_post_thumbnail( [
							'attachment'   => $attachment_id,
							'size'         => $size,
							'width'        => $img_width,
							'height'       => $img_height,
							'crop'         => $crop,
							'alt'          => \esc_attr( $alt ),
							'aspect_ratio' => $img_aspect_ratio,
							'object_fit'   => $img_fit ?? 'cover',
						] );

					$output .= '</a>';

					// Display Caption.
					if ( $excerpt ) {
						$output .= '<figcaption class="gallery-caption wpex-last-mb-0">';
							$output .= \wp_kses_post( \wptexturize( $excerpt ) );
						$output .= '</figcaption>';
					}

				// Close gallery item div.
				$output .= '</figure>';

			}

		// Close gallery div.
		$output .= "</div>\n";

		return $output;
	}

}
