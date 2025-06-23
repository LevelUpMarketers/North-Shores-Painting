<?php

namespace TotalTheme\Integration\WPBakery\Elements;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Single Image Configuration.
 */
final class Single_Image {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Initialize the class.
	 */
	public static function init(): void {
		\add_action( 'vc_after_init', [ self::class, 'add_params' ], 40 ); // add params first
		\add_action( 'vc_after_init', [ self::class, 'modify_params' ], 40 ); // priority is crucial.
		\add_filter( 'shortcode_atts_vc_single_image', [ self::class, 'parse_attributes' ], 99 );
		\add_filter( 'vc_shortcode_output', [ self::class, 'custom_output' ], 10, 3 );

		if ( \defined( '\VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
			\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ self::class, 'shortcode_classes' ], 99, 3 );
		}

		if ( \function_exists( '\vc_post_param' ) && 'vc_edit_form' === \vc_post_param( 'action' ) ) {
			\add_filter( 'vc_edit_form_fields_attributes_vc_single_image', [ self::class, 'edit_form_fields' ] );
		}
	}

	/**
	 * Adds custom params.
	 */
	public static function add_params() {
		if ( ! \function_exists( '\vc_add_params' ) ) {
			return;
		}

		\vc_add_params( 'vc_single_image', array(
			// General
			array(
				'type'=> 'vcex_select',
				'heading' => \esc_html__( 'Visibility', 'total' ),
				'param_name' => 'visibility',
				'weight' => 99,
			),
			array(
				'type' => 'dropdown',
				'heading' => \esc_html__( 'Image alignment', 'total' ),
				'param_name' => 'alignment',
				'value' => array(
					\esc_html__( 'Default', 'total' ) => '',
					\esc_html__( 'Left', 'total' ) => 'left',
					\esc_html__( 'Right', 'total' ) => 'right',
					\esc_html__( 'Center', 'total' ) => 'center',
				),
				'description' => \esc_html__( 'Select image alignment.', 'total' )
			),
			array(
				'type' => 'textfield',
				'heading' => \esc_html__( 'Over Image Caption', 'total' ),
				'param_name' => 'img_caption',
				'description' => \esc_html__( 'Use this field to add a caption to any single image with a link.', 'total' ),
			),
			array(
				'type' => 'vcex_select',
				'heading' => \esc_html__( 'Image Filter', 'total' ),
				'param_name' => 'img_filter',
				'description' => \esc_html__( 'Select an image filter style.', 'total' ),
			),
			array(
				'type' => 'vcex_select',
				'choices' => 'image_hover',
				'heading' => \esc_html__( 'Image Hover', 'total' ),
				'param_name' => 'img_hover',
				'description' => \esc_html__( 'Select your preferred image hover effect. Please note this will only work if the image links to a URL or a large version of itself. Please note these effects may not work in all browsers.', 'total' ),
			),
			// Lightbox
			array(
				'type' => 'textfield',
				'heading' => \esc_html__( 'Video, SWF, Flash, URL Lightbox', 'total' ),
				'param_name' => 'lightbox_video',
				'description' => \esc_html__( 'Enter the URL to a video, SWF file, flash file or a website URL to open in lightbox.', 'total' ),
				'group' => \esc_html__( 'Lightbox', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => \esc_html__( 'Lightbox Type', 'total' ),
				'param_name' => 'lightbox_iframe_type',
				'value' => array(
					\esc_html__( 'Auto Detect (Image, Video or Inline)', 'total' ) => '',
					\esc_html__( 'Image', 'total' )   => 'image',
					\esc_html__( 'Video', 'total' )   => 'video',
					\esc_html__( 'URL', 'total' )     => 'url',
					\esc_html__( 'HTML5', 'total' )   => 'html5',
					\esc_html__( 'iFrame', 'total' )  => 'video_embed', // this used to be Video, iframe combined
					\esc_html__( 'Quicktime (deprecated, will be treaded as video type)', 'total' ) => 'quicktime', // deprecated
				),
				'group' => \esc_html__( 'Lightbox', 'total' ),
				'dependency' => array( 'element' => 'lightbox_video', 'not_empty' => true ),
			),
			array(
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Video Overlay Icon?', 'total' ),
				'param_name' => 'lightbox_video_overlay_icon',
				'group' => \esc_html__( 'Lightbox', 'total' ),
				'std' => 'false',
				'dependency' => array( 'element' => 'lightbox_iframe_type', 'value' => 'video_embed' ),
			),
			array(
				'type' => 'textfield',
				'heading' => \esc_html__( 'HTML5 Webm URL', 'total' ),
				'param_name' => 'lightbox_video_html5_webm',
				'description' => \esc_html__( 'Enter the URL to a video, SWF file, flash file or a website URL to open in lightbox.', 'total' ),
				'group' => \esc_html__( 'Lightbox', 'total' ),
				'dependency' => array( 'element' => 'lightbox_iframe_type', 'value' => 'html5' ),
			),
			array(
				'type' => 'textfield',
				'heading' => \esc_html__( 'Lightbox Title', 'total' ),
				'param_name' => 'lightbox_title',
				'group' => \esc_html__( 'Lightbox', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => \esc_html__( 'Lightbox Dimensions', 'total' ),
				'param_name' => 'lightbox_dimensions',
				'description' => \esc_html__( 'Enter a custom width and height for your lightbox pop-up window. Use format widthxheight. Example: 900x600.', 'total' ),
				'group' => \esc_html__( 'Lightbox', 'total' ),
				'dependency' => array( 'element' => 'lightbox_iframe_type', 'value' => array( 'video', 'url', 'html5', 'iframe' ) ),
			),
			array(
				'type' => 'attach_image',
				'admin_label' => false,
				'heading' => \esc_html__( 'Custom Image Lightbox', 'total' ),
				'param_name' => 'lightbox_custom_img',
				'description' => \esc_html__( 'Select a custom image to open in lightbox format', 'total' ),
				'group' => \esc_html__( 'Lightbox', 'total' ),
			),
			array(
				'type' => 'attach_images',
				'admin_label' => false,
				'heading' => \esc_html__( 'Gallery Lightbox', 'total' ),
				'param_name' => 'lightbox_gallery',
				'description' => \esc_html__( 'Select images to create a lightbox Gallery.', 'total' ),
				'group' => \esc_html__( 'Lightbox', 'total' ),
			),
			array(
				'type' => 'hidden',
				'param_name' => 'rounded_image',
			)
		) );
	}

	/**
	 * Modify default params.
	 */
	public static function modify_params() {
		if ( ! \function_exists( '\vc_update_shortcode_param' ) ) {
			return;
		}

		// Modify source.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'source' ) ) {
			$param['weight'] = 100;
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify image.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'image' ) ) {
			$param['weight'] = 100;
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify img_size.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'img_size' ) ) {
			$param['weight'] = 100;
			$param['value']  = 'full';
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify externam_link.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'externam_link' ) ) {
			$param['weight'] = 100;
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify external_img_size.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'external_img_size' ) ) {
			$param['weight'] = 100;
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify el_id.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'el_id' ) ) {
			$param['weight'] = 98;
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify el_class.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'el_class' ) ) {
			$param['weight'] = 98;
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify css_animation.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'css_animation' ) ) {
			$param['weight'] = 98;
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify css.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'css' ) ) {
			$param['weight'] = -1;
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify img_link_target.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'img_link_target' ) ) {
			$param['value'][\esc_html__( 'Local', 'total' )] = 'local';
			$param['dependency'] = array(
				'element' => 'onclick',
				'value' => array( 'custom_link' ),
			);
			$param['group'] = \esc_html__( 'Link', 'total' );
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify onclick.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'onclick' ) ) {
			$param['group'] = \esc_html__( 'Link', 'total' );
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}

		// Modify link.
		if ( $param = \WPBMap::getParam( 'vc_single_image', 'link' ) ) {
			$param['group'] = \esc_html__( 'Link', 'total' );
			\vc_update_shortcode_param( 'vc_single_image', $param );
		}
	}

	/**
	 * Alter fields on edit.
	 */
	public static function edit_form_fields( $atts ) {
		if ( ! empty( $atts['rounded_image'] ) && 'yes' == $atts['rounded_image'] && empty( $atts['style'] ) ) {
			$atts['style'] = 'vc_box_circle';
			unset( $atts['rounded_image'] );
		}
		if ( ! empty( $atts['link'] ) && empty( $atts['onclick'] ) ) {
			$atts['onclick'] = 'custom_link';
		}
		return $atts;
	}

	/**
	 * Parse attributes on front-end.
	 */
	public static function parse_attributes( $atts ) {

		// Custom lightbox.
		if ( ! empty( $atts['lightbox_gallery'] ) ) {
			$atts['link'] = '#';
			$atts['onclick'] = 'custom_link';
		} elseif ( ! empty( $atts['lightbox_custom_img'] ) ) {
			if ( $lb_image = \wpex_get_lightbox_image( $atts['lightbox_custom_img'] ) ) {
				$atts['link'] = $lb_image;
				$atts['onclick'] = 'wpex_lightbox';
			}
		} elseif ( ! empty( $atts['lightbox_video'] ) ) {
			if ( ! empty( $atts['lightbox_video'] ) ) {
				$atts['lightbox_video'] = \set_url_scheme( \esc_url( $atts['lightbox_video'] ) );
				$atts['onclick'] = 'wpex_lightbox'; // Since we use total functions
				// Check if perhaps the iFrame is a video and if so set type to video type.
				if ( strpos( $atts[ 'lightbox_video' ], 'youtube' ) !== false
					|| strpos( $atts[ 'lightbox_video' ], 'vimeo' ) !== false
				) {
					$atts['lightbox_iframe_type'] = 'video';
				}
				// Set link.
				$atts['link'] = $atts['lightbox_video'];
			}
		} elseif ( ! empty( $atts['onclick'] ) && 'img_link_large' == $atts['onclick'] ) {
			$atts['onclick'] = 'wpex_lightbox'; // Since we use total functions
			if ( ! empty( $atts['image'] ) ) {
				$atts['link'] = \wpex_get_lightbox_image( $atts['image'] );
			} elseif ( isset( $atts['source'] ) && 'featured_image' == $atts['source'] ) {
				$atts['link'] = \wpex_get_lightbox_image( \get_post_thumbnail_id() );
			}
		} elseif ( empty( $atts['onclick'] ) && isset( $atts['img_link_large'] ) && 'yes' == $atts['img_link_large'] ) {
			$atts['onclick'] = 'wpex_lightbox'; // Since we use total functions
			$atts['link'] = \wpex_get_lightbox_image( $atts['image'] );
		}

		// Local scroll.
		if ( isset( $atts['img_link_target'] ) && 'local' === $atts['img_link_target'] ) {
			$atts['img_link_target'] = '_self';
		}

		return $atts;
	}

	/**
	 * Tweak shortcode classes.
	 */
	public static function shortcode_classes( $class_string, $tag, $atts ) {
		if ( 'vc_single_image' !== $tag ) {
			return $class_string;
		}
		if ( ! empty( $atts['visibility'] ) && $visibility_class = \totaltheme_get_visibility_class( $atts['visibility'] ) ) {
			$class_string .= " {$visibility_class}";
		}
		if ( ! empty( $atts['img_filter'] ) && $img_filter_class = \wpex_image_filter_class( $atts['img_filter'] ) ) {
			$class_string .= " {$img_filter_class}";
		}
		if ( ( ! empty( $atts['onclick'] ) && 'wpex_lightbox' == $atts['onclick'] ) ) {
			$class_string .= ' wpex-lightbox'; // MUST BE LAST FOR ADDING DATA ATTRIBUTES !!!
		}
		return $class_string;
	}

	/**
	 * Add custom HTML to ouput.
	 */
	public static function custom_output( $output, $obj, $atts ) {
		if ( 'vc_single_image' !== $obj->settings( 'base' ) ) {
			return $output;
		}

		$lb_data = array();

		// Check if lightbox CSS should enqueue.
		if ( ( ! empty( $atts['onclick'] ) && 'img_link_large' === $atts['onclick'] )
			|| ! empty( $atts['lightbox_gallery'] )
			|| ! empty( $atts['lightbox_custom_img'] )
			|| ! empty( $atts['lightbox_video'] )
			|| ( ! empty( $atts['img_link_large'] ) && 'yes' === $atts['img_link_large'] )
		) {
			\wpex_enqueue_lightbox_scripts();
		}

		// Add over image caption.
		if ( ! empty( $atts['img_caption'] ) ) {
			$caption_escaped = '<span class="wpb_single_image_caption">' . wp_kses_post( $atts['img_caption'] ) . '</span>';
			if ( \str_contains( $output, '/></a>' ) ) {
				$output = \str_replace( '/></a>', '/>' . $caption_escaped . '</a>', $output );
			} else {
				$output = \str_replace( '</figure>', $caption_escaped . '</figure>', $output );
			}
		}

		// Add video overlay icon.
		if ( isset( $atts['lightbox_video_overlay_icon'] ) && 'true' == $atts['lightbox_video_overlay_icon'] ) {
			$icon = '<div class="overlay-icon"><span>&#9658;</span></div>';
			$output = \str_replace( '</a>', $icon . '</a>', $output );
		}

		// Add hover classes.
		if ( ! empty( $atts['img_hover'] ) ) {
			$class = \wpex_image_hover_classes( $atts['img_hover'] );
			$output = \str_replace( 'vc_single_image-wrapper', 'vc_single_image-wrapper ' . $class, $output );
		}

		// Add local scroll classes.
		if ( isset( $atts['img_link_target'] ) && 'local' === $atts['img_link_target'] ) {
			$output = \str_replace( 'vc_single_image-wrapper', 'vc_single_image-wrapper local-scroll-link', $output );
		}

		// Lightbox gallery.
		if ( ! empty( $atts['lightbox_gallery'] ) && $gallery_ids = \explode( ',', $atts['lightbox_gallery'] ) ) {
			$output = \str_replace( '<a', '<a data-gallery="' . vcex_parse_inline_lightbox_gallery( $gallery_ids, ',' ) . '"', $output );
			$output = \str_replace( 'vc_single_image-wrapper', 'vc_single_image-wrapper wpex-lightbox-gallery', $output );
		}

		// Add Lightbox data attributes.
		if ( ! empty( $atts['lightbox_video'] ) && empty( $atts['lightbox_custom_img'] ) && empty( $atts['lightbox_gallery'] ) ) {

			// Set video iframe type.
			if ( \str_contains( (string) $atts['lightbox_video'], 'youtube' ) || \str_contains( (string) $atts['lightbox_video'], 'vimeo' ) ) {
				$atts['lightbox_iframe_type'] = 'video';
			}

			// iFrame type.
			$lb_iframe_type = $atts['lightbox_iframe_type'] ?? '';

			// Get lightbox dimensions.
			if ( ! empty( $atts['lightbox_dimensions'] )
				&& \in_array( $lb_iframe_type, [ 'video', 'url', 'html5', 'iframe' ] )
				&& \function_exists( 'vcex_parse_lightbox_dims' )
				&& $lightbox_dims = \vcex_parse_lightbox_dims( $atts['lightbox_dimensions'], 'array' )
			) {
				if ( ! empty( $lightbox_dims['width'] ) ) {
					$lb_data['data-width'] = \esc_attr( $lightbox_dims['width'] );
				}
				if ( ! empty( $lightbox_dims['height'] ) ) {
					$lb_data['data-height'] = \esc_attr( $lightbox_dims['height'] );
				}
			}

			// Set lightbox data attributes based on iframe type.
			if ( 'video_embed' === $lb_iframe_type ) {
				$lb_data['data-type'] = 'iframe';
			} elseif ( 'url' === $lb_iframe_type ) {
				$lb_data['data-type'] = 'video';
			} elseif ( 'url' === $lb_iframe_type ) {
				$lb_data['data-type'] = 'iframe';
			}  elseif ( 'quicktime' == $lb_iframe_type ) {
				$lb_data[ 'data-type' ] = 'video';
			} elseif ( 'html5' === $lb_iframe_type ) {
				if ( ! empty( $atts['img_id'] ) ) {
					$poster = \wp_get_attachment_image_src( $atts['img_id'], 'full' )[0] ?? '';
				} else {
					$poster = '';
				}
				$webem = $atts['lightbox_video_html5_webm'] ?? '';
				$lb_data['data-type'] = 'video';
				$lb_data['data-options'] = 'html5video:{ webm: \'' . \esc_url( $webem ) . '\', poster: \'' . \esc_url( $poster ) . '\' }';
				$lb_data['data-show_title'] = 'false';
			}
		}

		if ( ! empty( $atts['lightbox_title'] ) ) {
			$lb_data['data-title'] = $atts['lightbox_title'];
			$lb_data['data-show_title'] = 'true';
		}

		if ( $lb_data ) {
			$output = \str_replace( '<a', '<a ' . \wpex_parse_attrs( $lb_data ) . ' ', $output );
		}

		return $output;
	}

}
