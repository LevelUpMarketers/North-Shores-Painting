<?php

defined( 'ABSPATH' ) || exit;

/**
 * Video Shortcode.
 */
if ( ! class_exists( 'Vcex_Video_Shortcode' ) ) {

	class Vcex_Video_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_video';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			$this->scripts = $this->scripts_to_register();

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title(): string {
			return esc_html__( 'Video', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Embeded or self hosted video', 'total-theme-core' );
		}

		/**
		 * Register scripts.
		 */
		public function scripts_to_register(): array {
			return [
				[
					'vcex-video-overlay',
					vcex_get_js_file( 'frontend/video-overlay' ),
					[],
					TTC_VERSION,
					true
				],
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Type', 'total-theme-core' ),
					'param_name' => 'type',
					'admin_label' => true,
					'std' => 'youtube',
					'choices' => [
						'youtube' => 'YouTube',
						'vimeo' => 'Vimeo',
						'iframe' => esc_html__( 'Iframe', 'total-theme-core' ),
						'oembed' => esc_html__( 'WordPress oEmbed', 'total-theme-core' ),
						'video_tag' => esc_html__( 'HTML Video (External or Self Hosted)', 'total-theme-core' ),
					],
					'description' => esc_html__( 'Note: The Iframe type will not allow you to insert iframe code for security reasons. You will need to provide a video URL and the <iframe> will be generated for you.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'source',
					'std' => 'custom',
					'choices' => [
						'custom'        => esc_html__( 'Custom URL', 'total-theme-core' ),
						'media_library' => esc_html__( 'Media Library', 'total-theme-core' ),
						'custom_field'  => esc_html__( 'Custom Field', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Video URL', 'total-theme-core' ),
					'param_name' => 'url',
					'dependency' => [ 'element' => 'source', 'value' => 'custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_media_select',
					'media_type' => 'video',
					'return_val' => 'id',
					'heading' => esc_html__( 'Select Video', 'total-theme-core' ),
					'param_name' => 'attachment_id',
					'dependency' => [ 'element' => 'source', 'value' => 'media_library' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'video',
					'heading' => esc_html__( 'Video Custom Field ID', 'total-theme-core' ),
					'description' => esc_html__( 'Your custom field should return a URL or attachment ID.', 'total-theme-core' ),
					'param_name' => 'custom_field',
					'dependency' => [ 'element' => 'source', 'value' => 'custom_field' ],
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
					'css' => true,
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
					'css' => true,
					'editors' => [ 'wpbakery' ],
				],
				// Self Hosted/YouTube/Vimeo Settings
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Poster Image Source', 'total-theme-core' ),
					'param_name' => 'poster_source',
					'choices' => [
						'none' => esc_html__( 'None (No Poster)', 'total-theme-core' ),
						'media_library' => esc_html__( 'Media Library', 'total-theme-core' ),
						'featured' => esc_html__( 'Featured Image', 'total-theme-core' ),
						'custom_field'  => esc_html__( 'Custom Field', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'type', 'value' => 'video_tag' ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Poster Image', 'total-theme-core' ),
					'param_name' => 'poster_attachment_id',
					'dependency' => [ 'element' => 'poster_source', 'value' => 'media_library' ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'image',
					'heading' => esc_html__( 'Poster Custom Field', 'total-theme-core' ),
					'description' => esc_html__( 'Your custom field should return a URL or attachment ID.', 'total-theme-core' ),
					'param_name' => 'poster_custom_field',
					'dependency' => [ 'element' => 'poster_source', 'value' => 'custom_field' ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Preload', 'total-theme-core' ),
					'param_name' => 'preload',
					'choices' => [
						'metadata' => esc_html__( 'Metadata', 'total-theme-core' ),
						'auto' => esc_html__( 'Auto', 'total-theme-core' ),
						'none' => esc_html__( 'None', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'type', 'value' => 'video_tag' ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lazy Load', 'total-theme-core' ),
					'param_name' => 'lazy',
					'dependency' => [ 'element' => 'type', 'value' => [ 'youtube', 'vimeo', 'iframe' ] ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Autoplay', 'total-theme-core' ),
					'param_name' => 'autoplay',
					'dependency' => [ 'element' => 'type', 'value' => [ 'video_tag', 'youtube', 'vimeo' ] ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Loop', 'total-theme-core' ),
					'param_name' => 'loop',
					'dependency' => [ 'element' => 'type', 'value' => [ 'video_tag', 'vimeo' ] ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Muted', 'total-theme-core' ),
					'description' => esc_html__( 'Most browsers will require this setting enabled if you\'ve enabled autoplay.', 'total-theme-core' ),
					'param_name' => 'muted',
					'dependency' => [ 'element' => 'type', 'value' => [ 'video_tag', 'youtube', 'vimeo' ] ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Playback controls', 'total-theme-core' ),
					'param_name' => 'controls',
					'dependency' => [ 'element' => 'type', 'value' => [ 'video_tag', 'youtube' ] ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Download Button', 'total-theme-core' ),
					'param_name' => 'allow_download',
					'dependency' => [ 'element' => 'type', 'value' => 'video_tag' ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Play Inline', 'total-theme-core' ),
					'description' => esc_html__( 'Controls whether videos play inline or fullscreen on iOS. If you\'ve enabled autoplay this setting should also be enabled.', 'total-theme-core' ),
					'param_name' => 'playsinline',
					'dependency' => [ 'element' => 'type', 'value' => [ 'video_tag', 'youtube', 'vimeo' ] ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// YouTube only Settings
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Disable Cookies', 'total-theme-core' ),
					'description' => esc_html__( 'Prevents the service from tracking cookies.', 'total-theme-core' ),
					'param_name' => 'privacy_mode',
					'dependency' => [ 'element' => 'type', 'value' => [ 'youtube', 'vimeo' ] ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Start At in Seconds', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'param_name' => 'start',
					'dependency' => [ 'element' => 'type', 'value' => [ 'youtube' ] ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Vimeo specific settings.
				[
					'type' => 'colorpicker',
					'heading' => esc_html__( 'Controls Color', 'total-theme-core' ),
					'description' => esc_html__( 'Video must be hosted by a Starter, Standard, Advanced, Plus, Pro, Business, Premium, or Enterprise account.', 'total-theme-core' ),
					'param_name' => 'vimeo_color',
					'dependency' => [ 'element' => 'type', 'value' => [ 'vimeo' ] ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Iframe Settings
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Iframe Title', 'total-theme-core' ),
					'param_name' => 'title',
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'type', 'value' => 'iframe' ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Iframe Extra URL Parameters', 'total-theme-core' ),
					'description' => esc_html__( 'Enter your custom URL attributes using the format name=value. Hit enter after each set of data attributes. Example: autoplay=1', 'total-theme-core' ),
					'param_name' => 'iframe_params',
					'dependency' => [ 'element' => 'type', 'value' => 'iframe' ],
					'group' => esc_html__( 'Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Overlay Image
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to display an image over the video so that the user must click on the image to play the video.', 'total-theme-core' ),
					'param_name' => 'overlay',
					'group' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Overlay Image Source', 'total-theme-core' ),
					'param_name' => 'overlay_source',
					'choices' => [
						'media_library' => esc_html__( 'Media Library', 'total-theme-core' ),
						'featured' => esc_html__( 'Featured Image', 'total-theme-core' ),
						'custom_field'  => esc_html__( 'Custom Field', 'total-theme-core' ),
						'external' => esc_html__( 'External', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'overlay', 'value' => 'true' ],
					'group' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'param_name' => 'overlay_attachment_id',
					'dependency' => [ 'element' => 'overlay_source', 'value' => 'media_library' ],
					'group' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'image',
					'heading' => esc_html__( 'Overlay Image: Custom Field', 'total-theme-core' ),
					'description' => esc_html__( 'Your custom field should return a URL or attachment ID.', 'total-theme-core' ),
					'param_name' => 'overlay_custom_field',
					'dependency' => [ 'element' => 'overlay_source', 'value' => 'custom_field' ],
					'group' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Image: External', 'total-theme-core' ),
					'param_name' => 'overlay_external',
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'overlay_source', 'value' => 'external' ],
					'group' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => [
						'' => esc_html__( 'Disabled', 'total-theme-core' ),
						'video-icon' => esc_html__( 'Style 1', 'total-theme-core' ),
						'video-icon_2' => esc_html__( 'Style 2', 'total-theme-core' ),
						'video-icon_3' => esc_html__( 'Style 3', 'total-theme-core' ),
						'video-icon_4' => esc_html__( 'Style 4', 'total-theme-core' ),
					],
					'heading' => esc_html__( 'Overlay Play Icon', 'total-theme-core' ),
					'param_name' => 'overlay_play_icon',
					'dependency' => [ 'element' => 'overlay', 'value' => 'true' ],
					'group' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Overlay Play Icon Size', 'total-theme-core' ),
					'description' => self::param_description( 'width' ),
					'param_name' => 'overlay_play_icon_dims',
					'css' => [
						'selector' => '.overlay__video-svg',
						'property' => [ 'width', 'height' ],
					],
					'dependency' => [
						'element' => 'overlay_play_icon',
						'value' => [
							'video-icon_2',
							'video-icon_3',
							'video-icon_4',
						]
					],
					'group' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Overlay Play Icon Opacity', 'total-theme-core' ),
					'placeholder' => '20%',
					'description' => esc_html__( 'Enter 0% to disable.', 'total-theme-core' ),
					'param_name' => 'overlay_play_icon_bg_opacity',
					'css' => [
						'selector' => '.overlay-bg',
						'property' => 'opacity',
					],
					'dependency' => [
						'element' => 'overlay_play_icon',
						'value' => [
							'video-icon_2',
							'video-icon_3',
							'video-icon_4',
						]
					],
					'group' => esc_html__( 'Overlay Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Style
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Fill Column', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to make the element fill the parent column. If other elements are added to the same column, it will fill the remaining space.', 'total-theme-core' ),
					'param_name' => 'fill_column',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Aspect Ratio', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Enter a custom aspect ratio value for your video. Learn more from the %sFirefox manual%s.', 'total-theme-core' ), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' ),
					'param_name' => 'aspect_ratio',
					'css' => [
						'selector' => ":is(video,iframe)",
						'property' => 'aspect-ratio',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'description' => self::param_description( 'width' ),
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			];
		}

		/**
		 * Render shortcode video.
		 */
		public static function render_video( array $atts ): string {
			$video_url = '';
			$source    = ! empty( $atts['source'] ) ? $atts['source'] : 'custom';

			switch ( $source ) {
				case 'oembed':
				case 'custom':
					$video_url = $atts['url'] ?? '';
					break;
				case 'media_library':
					$video_id = $atts['attachment_id']['id'] ?? $atts['attachment_id'] ?? '';
					if ( $video_id = absint( $video_id ) ) {
						$video_url = wp_get_attachment_url( $video_id );
					}
					break;
				case 'custom_field':
					if ( ! empty( $atts['custom_field'] ) ) {
						$cf_val = vcex_get_meta_value( $atts['custom_field'], false, false );
						if ( $cf_val ) {
							if ( is_numeric( $cf_val ) ) {
								$cf_val = wp_get_attachment_url( $cf_val );
							}
							if ( is_string( $cf_val ) ) {
								$video_url = $cf_val;
							}
						}
					}
					break;
			}

			if ( ! $video_url ) {
				return '';
			}

			$video_html = '';
			$type       = ! empty( $atts['type'] ) ? $atts['type'] : 'youtube';
			$title      = ! empty( $atts['title'] ) ? vcex_parse_text( $atts['title'] ) : '';

			// Check URLs.
			if ( ! self::verify_video_url( $video_url, $type ) ) {
				return '';
			}

			// Generate video HTML.
			switch ( $type ) {
				case 'oembed':
					global $wp_embed;
					if ( $wp_embed && is_a( $wp_embed, 'WP_Embed' ) && $video_url_safe = esc_url( $video_url ) ) {
						$video_html = $wp_embed->run_shortcode( "[embed]{$video_url_safe}[/embed]" );
						if ( $video_html && vcex_validate_att_boolean( 'overlay', $atts ) ) {
							$video_html = str_replace( 'src="', 'data-src="', $video_html );
							if ( ! str_contains( $video_html, 'autoplay=1' ) ) {
								$video_html = str_replace( '?feature=oembed', '?feature=oembed&autoplay=1', $video_html );
							}
						}
					}
					break;
				case 'video_tag':
					if ( $video_url_safe = esc_url( $video_url ) ) {
						$attrs = array_merge( [ 'src' => $video_url_safe ], self::get_video_tag_attrs( $atts ) );
						$video_html = '<video' . vcex_parse_html_attributes( $attrs ) . '></video>';
					}
					break;
				case 'vimeo':
				case 'youtube':
				case 'iframe':
					$video_url = vcex_get_video_embed_url( $video_url );
					if ( $video_url ) {
						$iframe_params = self::get_iframe_url_params( $atts, $type );
						if ( $iframe_params ) {
							$video_url = add_query_arg( $iframe_params, $video_url );
						}

						if ( 'youtube' === $type && vcex_validate_att_boolean( 'privacy_mode', $atts ) ) {
							$video_url = str_replace( 'youtube.com', 'youtube-nocookie.com', $video_url );
							$video_url = str_replace( '/youtube-nocookie.com', '/www.youtube-nocookie.com', $video_url );
						}

						$video_url = str_replace( 'http://', 'https://', $video_url );

						$video_html = self::get_video_iframe( $video_url, $title, $atts );
					}
					break;
			}

			return $video_html;
		}

		/**
		 * Verify the URL based on it's type.
		 */
		protected static function verify_video_url( string $video_url, string $type ): bool {
			$check = true;
			switch ( $type ) {
				case 'youtube':
					$check = str_contains( $video_url, 'youtu' );
					break;
				case 'vimeo':
					$check = str_contains( $video_url, 'vimeo' );
					break;
			}
			return $check;
		}

		/**
		 * Return iframe params.
		 */
		protected static function get_iframe_url_params( array $atts, string $type ): array {
			$params = [];

			switch ( $type ) {
				case 'vimeo':
					if ( vcex_validate_att_boolean( 'autoplay', $atts ) || vcex_validate_att_boolean( 'overlay', $atts ) ) {
						$params['autoplay'] = '1';
					}
					if ( vcex_validate_att_boolean( 'muted', $atts ) ) {
						$params['muted'] = '1';
					}
					if ( vcex_validate_att_boolean( 'playsinline', $atts ) ) {
						$params['playsinline'] = '1';
					}
					if ( vcex_validate_att_boolean( 'privacy_mode', $atts ) ) {
						$params['dnt'] = '1';
					}
					if ( vcex_validate_att_boolean( 'loop', $atts ) ) {
						$params['loop'] = '1';
					}
					if ( ! empty( $atts['vimeo_color'] )
						&& $vimeo_color = sanitize_hex_color( $atts['vimeo_color'] )
					) {
						$params['color'] = esc_attr( ltrim( $vimeo_color, '#' ) );
					}
					break;
				case 'youtube':
					if ( vcex_validate_att_boolean( 'autoplay', $atts ) || vcex_validate_att_boolean( 'overlay', $atts ) ) {
						$params['autoplay'] = '1';
					}
					if ( vcex_validate_att_boolean( 'muted', $atts ) ) {
						$params['mute'] = '1';
					}
					if ( ! vcex_validate_att_boolean( 'controls', $atts, true ) ) {
						$params['controls'] = '0';
					}
					if ( vcex_validate_att_boolean( 'playsinline', $atts ) ) {
						$params['playsinline'] = '1';
					}
					if ( ! empty( $atts['start'] ) ) {
						$params['start'] = absint( $atts['start'] );
					}
					break;
				case 'iframe':
					if ( ! empty( $atts['iframe_params'] ) && is_string( $atts['iframe_params'] ) ) {
						$params = explode( ',', $atts['iframe_params'] );
						$params_safe = [];
						foreach ( $params as $param ) {
							if ( ! is_string( $param ) ) {
								continue;
							}
							if ( str_contains( $param, '=') ) {
								$param = explode( '=', $param );
								if ( is_array( $param ) && 2 === count( $param ) ) {
									$params_safe[ sanitize_text_field( $param[0] ) ] = esc_attr( $param[1] );
								}
							} else {
								$params_safe[ sanitize_text_field( $param ) ] = '';
							}
						}
						$params = $params_safe;
					}
				break;
			}

			$params = apply_filters( 'vcex_video_iframe_url_parameters', $params, $atts );

			return (array) $params;
		}

		/**
		 * Get video iframe.
		 */
		protected static function get_video_iframe( string $video_url, string $title, array $atts ): string {
			$src_type = vcex_validate_att_boolean( 'overlay', $atts ) ? 'data-src' : 'src';

			$lazy = vcex_validate_att_boolean( 'lazy', $atts, true ) ? ' loading="lazy"' : '';

			return '<iframe class="wpex-block wpex-w-100 wpex-h-100 wpex-aspect-16-9" ' . esc_attr( $src_type ) . '="' . esc_url( $video_url ) . '" title="' . esc_attr( $title ) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen' . $lazy . '></iframe>';
		}

		/**
		 * Get video tag attributes.
		 */
		protected static function get_video_tag_attrs( array $atts ): array {
			$attrs = [
				'class' => 'wpex-w-100 wpex-aspect-16-9 wpex-align-middle wpex-object-cover',
			];

			if ( vcex_validate_att_boolean( 'overlay', $atts ) ) {
				$attrs['class'] .= ' wpex-invisible';
			}

			if ( vcex_validate_att_boolean( 'stretch', $atts, true ) ) {
				$attrs['class'] .= ' wpex-w-100';
			}

			if ( vcex_validate_att_boolean( 'controls', $atts, true ) ) {
				$attrs['controls'] = '';
			}

			if ( vcex_validate_att_boolean( 'playsinline', $atts ) ) {
				$attrs['playsinline'] = '';
			}

			if ( vcex_validate_att_boolean( 'autoplay', $atts ) ) {
				$attrs['autoplay'] = 'true';
			}

			if ( vcex_validate_att_boolean( 'loop', $atts ) ) {
				$attrs['loop'] = 'true';
			}

			if ( vcex_validate_att_boolean( 'muted', $atts ) ) {
				$attrs['muted'] = '';
			}

			if ( ! vcex_validate_att_boolean( 'allow_download', $atts, true ) ) {
				$attrs['controlslist'] = 'nodownload';
			}

			if ( ! empty( $atts['poster_source' ] ) && 'none' !== $atts['poster_source'] ) {
				switch ( $atts['poster_source'] ) {
					case 'featured':
						$poster_url = get_the_post_thumbnail_url( null, 'full' );
						break;
					case 'custom_field':
						if ( ! empty( $atts['poster_custom_field'] ) ) {
							$poster_cf_val = vcex_get_meta_value( $atts['poster_custom_field'], false, false );
							if ( $poster_cf_val ) {
								if ( is_numeric( $poster_cf_val ) ) {
									$poster_cf_val = wp_get_attachment_url( $poster_cf_val );
								}
								$poster_url = $poster_cf_val;
							}
						}
						break;
					case 'media_library':
						if ( ! empty( $atts['poster_attachment_id'] ) ) {
							$poster_img = $atts['poster_attachment_id']['id'] ?? $atts['poster_attachment_id'];
							if ( is_numeric( $poster_img ) ) {
								$poster_url = wp_get_attachment_url( $atts['poster_attachment_id'] );
							}
						}
						break;
				}
			}

			if ( ! empty( $atts['preload' ] ) && in_array( $atts['preload'], [ 'none', 'auto' ] ) ) {
				$attrs['preload'] = esc_attr( $atts['preload'] );
			}

			if ( ! empty( $poster_url ) ) {
				$attrs['poster'] = esc_html( $poster_url );
			}

			return $attrs;
		}

		/**
		 * Renders the video overlay.
		 */
		public static function render_overlay( $atts ): string {
			$overlay_attrs = [
				'class' => 'vcex-video-overlay wpex-block wpex-absolute wpex-inset-0 wpex-cursor-pointer overlay-parent',
			];

			$overlay_source = $atts['overlay_source'] ?? 'media_library';

			switch ( $overlay_source ) {
				case 'featured':
					$overlay_bg = get_the_post_thumbnail_url( null, 'full' );
					$overlay_bg_attachment = get_post_thumbnail_id();
					break;
				case 'custom_field':
					if ( ! empty( $atts['overlay_custom_field'] ) ) {
						$overlay_cf_val = vcex_get_meta_value( $atts['overlay_custom_field'], false, false );
						if ( $overlay_cf_val ) {
							if ( is_numeric( $overlay_cf_val ) ) {
								$overlay_bg_attachment = $overlay_cf_val;
								$overlay_cf_val = wp_get_attachment_url( $overlay_cf_val );
							}
							if ( is_string( $overlay_cf_val ) ) {
								$overlay_bg = $overlay_cf_val;
							}
						}
					}
					break;
				case 'external':
					$overlay_bg = ! empty( $atts['overlay_external'] ) ? vcex_parse_text_safe( $atts['overlay_external'] ) : '';
					break;
				default:
				case 'media_library':
					$overlay_img = $atts['overlay_attachment_id']['id'] ?? $atts['overlay_attachment_id'] ?? '';
					if ( $overlay_img && is_numeric( $overlay_img ) ) {
						$overlay_bg = wp_get_attachment_url( $overlay_img );
						$overlay_bg_attachment = $overlay_img;
					}
					break;
			}

			if ( isset( $overlay_bg ) && $overlay_bg_safe = esc_url( $overlay_bg ) ) {
				$overlay_bg_alt = ! empty( $overlay_bg_attachment ) ? get_post_meta( $overlay_bg_attachment, '_wp_attachment_image_alt', true ) : '';
				$overlay_bg = '<img src="' . $overlay_bg_safe .'" loading="lazy" decoding="async" class="wpex-bg-black wpex-h-100 wpex-w-100 wpex-object-cover" alt="' . esc_attr( $overlay_bg_alt ) . '">';
			} else {
				$overlay_bg = '';
			}

			$overlay_attrs_safe = vcex_parse_html_attributes( $overlay_attrs );

			if ( ! empty( $atts['overlay_play_icon'] ) ) {
				$overlay_icon_safe = vcex_get_image_overlay( 'inside_link', $atts['overlay_play_icon'] );
			} else {
				$overlay_icon_safe = '';
			}

			wp_enqueue_script( 'vcex-video-overlay' );

			$overlay_icon_safe = (string) apply_filters( 'vcex_video_overlay_play_icon', $overlay_icon_safe, $atts );

			return "<div{$overlay_attrs_safe}>{$overlay_bg}{$overlay_icon_safe}</div>";;
		}

	}

}

new Vcex_Video_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Video' ) ) {
	class WPBakeryShortCode_Vcex_Video extends WPBakeryShortCode {}
}
