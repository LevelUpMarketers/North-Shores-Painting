<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds self-hosted video background support to WPBakery.
 */
final class Video_Backgrounds {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Shortcodes to add overlay settings to.
	 */
	private $shortcodes = [
		'vc_row',
		'vc_section',
		'vc_column',
	];

	/**
	 * Create or retrieve the class instance.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		\add_action( 'vc_after_init', [ $this, 'vc_after_init' ] );
		\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ $this, 'add_classes' ], 10, 3 );

		if ( \function_exists( '\vc_post_param' ) && 'vc_edit_form' === \vc_post_param( 'action' ) ) {
			\add_filter( 'vc_edit_form_fields_attributes_vc_section', [ $this, 'vc_section_parse_atts' ] );
			\add_filter( 'vc_edit_form_fields_attributes_vc_row', [ $this, 'vc_row_parse_atts' ] );
		}

		\add_filter( 'shortcode_atts_vc_section', [ $this, 'vc_section_parse_atts' ], 99 );
		\add_filter( 'shortcode_atts_vc_row', [ $this, 'vc_row_parse_atts' ], 99 );

		foreach ( $this->shortcodes as $shortcode ) {
			\add_filter( "shortcode_atts_{$shortcode}", [ $this, 'frontend_atts' ], 99 );
			\add_filter( $this->get_insert_hook( $shortcode ), [ $this, 'insert_video' ], 5, 2 ); // priority is important.
		}
	}

	/**
	 * Runs on vc_after_init
	 */
	public function vc_after_init() {
		$this->modify_params();
		$this->add_params();
	}

	/**
	 * Returns the hook name for inserting the video bg.
	 */
	protected function get_insert_hook( $shortcode = '' ) {
		if ( 'vc_column' === $shortcode ) {
			$shortcode = 'vc_column_inner';
		}
		return "wpex_hook_{$shortcode}_top";
	}

	/**
	 * Modify shortcode params.
	 */
	public function modify_params() {
		if ( ! \function_exists( 'vc_update_shortcode_param' ) ) {
			return;
		}

		foreach ( $this->shortcodes as $shortcode ) {

			// Modify the video_bg_url setting.
			$param = \WPBMap::getParam( $shortcode, 'video_bg_url' );
			if ( $param ) {
				$param['description'] = \esc_html__( 'Note: Because of how YouTube works, videos may not always play so it\'s generally recommended to use Self Hosted video backgrounds.', 'total' );
				\vc_update_shortcode_param( $shortcode, $param );
			}

			// Modify video_bg_parallax setting.
			$param = \WPBMap::getParam( $shortcode, 'video_bg_parallax' );
			if ( $param ) {
				$param['group'] = \esc_html__( 'Video', 'total' );
				$param['dependency'] = [
					'element' => 'video_bg',
					'value' => 'youtube',
				];
				\vc_update_shortcode_param( $shortcode, $param );
			}

			// Modify video_bg_url setting.
			$param = \WPBMap::getParam( $shortcode, 'video_bg_url' );
			if ( $param ) {
				$param['group'] = \esc_html__( 'Video', 'total' );
				$param['dependency'] = [
					'element' => 'video_bg',
					'value' => 'youtube',
				];
				\vc_update_shortcode_param( $shortcode, $param );
			}

			// Modify parallax_speed_video setting.
			$param = \WPBMap::getParam( $shortcode, 'parallax_speed_video' );
			if ( $param ) {
				$param['group'] = \esc_html__( 'Video', 'total' );
				$param['dependency'] = [
					'element' => 'video_bg',
					'value' => 'youtube',
				];
				\vc_update_shortcode_param( $shortcode, $param );
			}

		}

	}

	/**
	 * Hooks into "wpex_vc_attributes" to add new params.
	 */
	public function add_params() {
		if ( ! \function_exists( 'vc_add_params' ) ) {
			return;
		}
		foreach ( $this->shortcodes as $shortcode ) {
			\vc_add_params( $shortcode, $this->get_attributes() );
		}
	}

	/**
	 * Returns vc_map params.
	 */
	private function get_attributes() {
		return [
			[
				'type' => 'vcex_select',
				'heading' => \esc_html__( 'Video Background', 'total' ),
				'param_name' => 'video_bg',
				'choices' => [
					'' => esc_html__( 'None', 'total' ),
					'youtube' => 'YouTube',
					'self_hosted' => esc_html__( 'Self Hosted', 'total' ),
				],
				'group' => \esc_html__( 'Video', 'total' ),
			],
			[
				'type' => 'vcex_select',
				'heading' => \esc_html__( 'Visibility', 'total' ),
				'choices' => 'visibility',
				'param_name' => 'video_bg_visibility',
				'group' => \esc_html__( 'Video', 'total' ),
				'dependency'  => [ 'element' => 'video_bg', 'value' => 'self_hosted' ],
			],
			[
				'type' => 'vcex_media_select',
				'media_type' => 'video',
				'return_val' => 'id',
				'heading' => \esc_html__( 'Video: MP4', 'total' ),
				'description' => \esc_html__( 'Make sure to optimize your videos so that your site can still load quickly. We recommend videos between 15-30 seconds around 0.5mb per second or smaller if possible. Since video backgrounds don\'t have sound make sure to also remove any sound from the video to keep the file size as small as possible.', 'total' ),
				'param_name' => 'video_bg_mp4',
				'dependency' => [ 'element' => 'video_bg', 'value' => 'self_hosted' ],
				'group' => \esc_html__( 'Video', 'total' ),
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Video Loop', 'total' ),
				'param_name' => 'video_bg_loop',
				'std' => 'true',
				'group' => \esc_html__( 'Video', 'total' ),
				'dependency'  => [ 'element' => 'video_bg', 'value' => 'self_hosted' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Video Zoom', 'total' ),
				'param_name' => 'video_bg_zoom',
				'std' => 'true',
				'group' => \esc_html__( 'Video', 'total' ),
				'description' => \esc_html__( 'Enable to stretch the video to fit the parent container.', 'total' ),
				'dependency'  => [ 'element' => 'video_bg', 'value' => 'self_hosted' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Center Video', 'total' ),
				'param_name' => 'video_bg_center',
				'std' => 'false',
				'group' => \esc_html__( 'Video', 'total' ),
				'dependency'  => [ 'element' => 'video_bg', 'value' => 'self_hosted' ],
			],
			[
				'type' => 'attach_image',
				'heading' => \esc_html__( 'Poster Image', 'total' ),
				'description' => \esc_html__( 'Specify an image to be shown while the video is downloading. If this is not included, the first frame of the video will be used instead.', 'total' ),
				'param_name' => 'video_bg_poster',
				'dependency' => [ 'element' => 'video_bg', 'value' => 'self_hosted' ],
				'group' => \esc_html__( 'Video', 'total' ),
			],
			[
				'type' => 'vcex_media_select',
				'media_type' => 'video',
				'return_val' => 'id',
				'heading' => \esc_html__( 'Video: WEBM', 'total' ),
				'param_name' => 'video_bg_webm',
				'dependency' => [ 'element' => 'video_bg', 'value' => 'self_hosted' ],
				'group' => \esc_html__( 'Video', 'total' ),
			],
			[
				'type' => 'vcex_media_select',
				'media_type' => 'video',
				'return_val' => 'id',
				'heading' => \esc_html__( 'Video: OGV', 'total' ),
				'param_name' => 'video_bg_ogv',
				'dependency' => [ 'element' => 'video_bg', 'value' => 'self_hosted' ],
				'group' => \esc_html__( 'Video', 'total' ),
			],
		];
	}

	/**
	 * Check if the video bg is enabled.
	 */
	protected function shortcode_has_video( $atts ) {
		if ( isset( $atts['wpex_self_hosted_video_bg'] )
			&& true === $atts['wpex_self_hosted_video_bg']
			&& ( ! empty( $atts['video_bg_mp4'] )
				|| ! empty( $atts['video_bg_webm'] )
				|| ! empty( $atts['video_bg_ogv'] )
			)
		) {
			return true;
		}
	}

	/**
	 * Parses vc_section atts.
	 */
	public function vc_section_parse_atts( $atts ) {
		if ( ! empty( $atts['video_bg'] ) && 'yes' === $atts['video_bg'] ) {
			$atts['video_bg'] = 'youtube';
		}
		return $atts;
	}

	/**
	 * Parses vc_row atts.
	 */
	public function vc_row_parse_atts( $atts ) {
		if ( isset( $atts['video_bg'] ) && 'yes' === $atts['video_bg'] ) {
			$atts['video_bg'] = 'self_hosted';
		}
		return $atts;
	}

	/**
	 * Parses atts on front-end to add mock "wpex_self_hosted_video_bg" attribute.
	 */
	public function frontend_atts( $atts ) {
		if ( ! empty( $atts['video_bg'] ) && 'self_hosted' === $atts['video_bg'] ) {
			$atts['video_bg'] = ''; // prevent VC from loading it's own video struff.
			$atts['wpex_self_hosted_video_bg'] = true;
		}
		return $atts;
	}

	/**
	 * Adds classes to shortcodes that have video backgrounds.
	 */
	public function add_classes( $class_string, $tag, $atts ) {
		if ( in_array( $tag, $this->shortcodes ) && $this->shortcode_has_video( $atts ) ) {
			$class_string .= ' wpex-has-video-bg';
			if ( ! str_contains( $class_string, 'wpex-relative' ) ) {
				$class_string .= ' wpex-relative';
			}
		}
		return $class_string;
	}

	/**
	 * Inserts the video background HTML into the shortcodes.
	 */
	public function insert_video( $content, $atts ) {
		if ( $video_bg = $this->render_video( $atts ) ) {
			$content .= $video_bg;
		}
		return $content;
	}

	/**
	 * Render the video background.
	 */
	private function render_video( $atts ) {
		if ( ! $this->shortcode_has_video( $atts ) ) {
			return;
		}

		$video_html = '<div class="wpex-video-bg-wrap wpex-absolute wpex-inset-0 wpex-overflow-hidden wpex-rounded-inherit">';

			$class = 'wpex-video-bg';

			if ( isset( $atts['video_bg_center'] ) && \wpex_validate_boolean( $atts['video_bg_center'] ) ) {
				$class .= ' wpex-video-bg-center wpex-absolute wpex-top-50 wpex-left-50 -wpex-translate-xy-50';
			} else {
				$class .= ' wpex-absolute wpex-top-0 wpex-left-0';
			}

			$class .= ' wpex-w-auto wpex-h-auto wpex-min-w-100 wpex-min-h-100'; // max-width-none prevents black lines on sides.

			$zoom = ! empty( $atts['video_bg_zoom'] ) ? \wpex_validate_boolean( $atts['video_bg_zoom'] ) : true;
			if ( $zoom ) {
				$class .= ' wpex-max-w-none';
			}

			if ( ! empty( $atts['video_bg_visibility'] ) && $visibility_class = \totaltheme_get_visibility_class( $atts['video_bg_visibility'] ) ) {
				$class .= " {$visibility_class}";
			}

			$video_attributes = [
				'class'       => \trim( $class ),
				'preload'     => 'auto',
				'autoplay'    => 'true',
				'aria-hidden' => 'true',
				'playsinline' => '',
			];

			if ( empty( $atts['video_bg_loop'] ) || 'false' !== $atts['video_bg_loop'] ) {
				$video_attributes['loop'] = 'loop';
			}

			if ( ! \apply_filters( 'vcex_self_hosted_row_video_sound', false ) ) {
				$video_attributes['muted']  = '';
				$video_attributes['volume'] = '0';
			}

			if ( ! empty( $atts['video_bg_poster'] ) ) {
				$poster = \wp_get_attachment_url( $atts['video_bg_poster'] );
				if ( $poster && \is_string( $poster ) ) {
					$video_attributes['poster'] = esc_url( $poster );
				}
			}

			/**
			 * Filters the self hosted video background attributes.
			 *
			 * @param array $video_attributes
			 * @param array $atts
			 */
			$video_attributes = (array) \apply_filters(
				'totaltheme/integration/wpbakery/video_backgrounds/video_attributes',
				$video_attributes,
				$atts
			);

			/*** deprecated ***/
			$video_attributes = (array) \apply_filters( 'wpex_self_hosted_video_bg_attributes', $video_attributes, $atts );

			$video_html .= '<video';

				if ( ! empty( $video_attributes ) && \is_array( $video_attributes ) ) {
					foreach ( $video_attributes as $name => $value ) {
						if ( $value || '0' === $value ) {
							$video_html .= ' ' . $name . '="' . \esc_attr( $value ) . '"';
						} else {
							$video_html .= ' ' . $name;
						}
					}
				}

			$video_html .= '>';

				if ( ! empty( $atts['video_bg_webm'] ) && $webm_url = $this->get_video_url( $atts['video_bg_webm'] ) ) {
					$video_html .= '<source src="' . \esc_url( $webm_url ) . '" type="video/webm">';
				}

				if ( ! empty( $atts['video_bg_ogv'] ) && $ogv_url = $this->get_video_url( $atts['video_bg_ogv'] ) ) {
					$video_html .= '<source src="' . \esc_url( $ogv_url ) . '" type="video/ogg ogv">';
				}

				if ( ! empty( $atts['video_bg_mp4'] ) && $mp4_url = $this->get_video_url( $atts['video_bg_mp4'] ) ) {
					$video_html .= '<source src="' . \esc_url( $mp4_url ) . '" type="video/mp4">';
				}

			$video_html .= '</video>';

		$video_html .= '</div>';

		/**
		 * Video overlay fallack.
		 *
		 * @deprecated in 3.6.0
		 * @todo Remove. Hook into shortcode_atts to swap video_bg_overlay for standard overlay.
		 */
		if ( ! empty( $atts['video_bg_overlay'] ) && 'none' !== $atts['video_bg_overlay'] ) {
			$video_html .= '<span class="wpex-video-bg-overlay ' . \esc_attr( $atts['video_bg_overlay'] ) . ' wpex-absolute wpex-inset-0 wpex-rounded-inherit"></span>';
		}

		return $video_html;
	}

	/**
	 * Get video URL.
	 */
	private function get_video_url( $video = '' ) {
		if ( is_numeric( $video ) && $video_post = get_post( $video ) ) {
			$video = ''; // make sure it does nothing if checks don't passs
			if ( ! empty( $video_post->ID )
				&& 'publish' === get_post_status( $video_post )
				&& \str_starts_with( get_post_mime_type( $video_post ), 'video/' )
			) {
				$video = \wp_get_attachment_url( $video_post->ID );
			}
		}
		return $video;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
