<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds slider background settings to various WPB elements.
 */
final class Slider_Backgrounds {

	/**
	 * Static only class.
	 */
	private function __construct() {}

	/**
	 * Shortcodes to add slider settings to.
	 */
	private static $shortcodes = [
		'vc_row',
		'vc_section',
	];

	/**
	 * Private constructor.
	 */
	public static function init() {
		if ( ! \class_exists( 'Homepage_And_Design_Heroes_Background_Slider' ) ) {
			\add_action( 'vc_after_init', [ self::class, '_add_params' ] );
			\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ self::class, '_filter_shortcode_class' ], 10, 3 );
			foreach ( self::$shortcodes as $shortcode ) {
				\add_filter( "wpex_hook_{$shortcode}_top", [ self::class, '_insert_slider' ], 5, 2 ); // priority is important.
			}
		}
	}

	/**
	 * Hooks into "wpex_vc_attributes" to add new params.
	 */
	public static function _add_params() {
		if ( \function_exists( 'vc_add_params' ) ) {
			foreach ( self::$shortcodes as $shortcode ) {
				\vc_add_params( $shortcode, self::get_params() );
			}
		}
	}

	/**
	 * Returns vc_map params.
	 */
	private static function get_params() {
		return [
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Slider Source', 'total' ),
				'param_name' => 'wpex_bg_slider_source',
				'group' => esc_html__( 'Slider', 'total' ),
				'value' => [
					esc_html__( '— Select —', 'total' ) => '',
					esc_html__( 'Custom', 'total' ) => 'custom',
					esc_html__( 'Post Gallery', 'total' ) => 'post_gallery',
				],
			],
			[
				'type' => 'attach_images',
				'heading' => esc_html__( 'Slider Images (Choose 1 or 3+)', 'total' ),
				'param_name' => 'wpex_bg_slider_images',
				'group' => esc_html__( 'Slider', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_slider_source', 'value' => 'custom' ],
			],
			[
				'type' => 'dropdown',
				'std' => 'load',
				'value' => [
					esc_html__( 'On Page Load', 'total' ) => 'load',
					esc_html__( 'When Scrolled Into View', 'total' ) => 'scroll',
				],
				'heading' => esc_html__( 'Start Animation', 'total' ),
				'param_name' => 'wpex_bg_slider_trigger',
				'group' => esc_html__( 'Slider', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_slider_source', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_text',
				'placeholder' => '0.5',
				'heading' => esc_html__( 'Scroll Threshold', 'total' ),
				'description' => esc_html__( 'The percentage of the element that must be visible before the animation starts.', 'total' ),
				'param_name' => 'wpex_bg_slider_observer_threshold',
				'group' => esc_html__( 'Slider', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_slider_trigger', 'value' => 'scroll' ],
			],
			[
				'type' => 'vcex_text',
				'placeholder' => '5000',
				'heading' => esc_html__( 'Interval (milliseconds)', 'total' ),
				'param_name' => 'wpex_bg_slider_interval',
				'group' => esc_html__( 'Slider', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_slider_source', 'not_empty' => true ],
			],
			[
				'type' => 'dropdown',
				'std' => 'fade',
				'heading' => esc_html__( 'Animation type', 'total' ),
				'param_name' => 'wpex_bg_slider_animation',
				'group' => esc_html__( 'Slider', 'total' ),
				'value' => [
					esc_html__( 'Fade', 'total' ) => 'fade',
					esc_html__( 'Ken Burns' ) => 'ken_burns',
				],
				'dependency' => [ 'element' => 'wpex_bg_slider_source', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_text',
				'placeholder' => '2000',
				'heading' => esc_html__( 'Fade Duration (milliseconds)', 'total' ),
				'param_name' => 'wpex_bg_slider_fade_duration',
				'group' => esc_html__( 'Slider', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_slider_source', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_text',
				'placeholder' => '1.2',
				'heading' => esc_html__( 'Zoom Level', 'total' ),
				'description' => esc_html__( 'The max amount the image will zoom in during the Ken Burns effect.', 'total' ),
				'param_name' => 'wpex_bg_slider_zoom',
				'group' => esc_html__( 'Slider', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_slider_animation', 'value' => 'ken_burns' ],
			],
			[
				'type' => 'vcex_text',
				'placeholder' => '250',
				'heading' => esc_html__( 'Zoom in Delay (milliseconds)', 'total' ),
				'description' => esc_html__( 'Extra time to delay the zoom effect, ensuring a smoother transition.', 'total' ),
				'param_name' => 'wpex_bg_slider_zoom_delay',
				'group' => esc_html__( 'Slider', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_slider_animation', 'value' => 'ken_burns' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'std' => 'true',
				'heading' => esc_html__( 'Loop', 'total' ),
				'param_name' => 'wpex_bg_slider_loop',
				'group' => esc_html__( 'Slider', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_slider_source', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_ofswitch',
				'std' => 'false',
				'heading' => esc_html__( 'Randomize', 'total' ),
				'param_name' => 'wpex_bg_slider_randomize',
				'group' => esc_html__( 'Slider', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_slider_source', 'not_empty' => true ],
			],
		];
	}

	/**
	 * Modify element class.
	 */
	public static function _filter_shortcode_class( $class_string, $tag, $atts ) {
		if ( \in_array( $tag, self::$shortcodes, true ) && self::has_slider( $atts ) ) {
			$class_string .= ' wpex-has-background-slider';
			if ( ! str_contains( $class_string, 'wpex-vc-reset-negative-margin' ) ) {
				if ( totaltheme_has_classic_styles() ) {
					$class_string .= ' wpex-vc-reset-negative-margin';
				} else {
					$class_string .= ' wpex-vc_row-mx-0';
				}
			}
		}
		return $class_string;
	}

	/**
	 * Inserts the slider html into the shortcode.
	 */
	public static function _insert_slider( $content, $atts ) {
		if ( $slider = self::_get_slider_html( $atts ) ) {
			$content .= $slider;
		}
		return $content;
	}

	/**
	 * Loose check to see if the slider is enabled.
	 */
	private static function has_slider( $atts ): bool {
		return self::get_slider_source( $atts );
	}

	/**
	 * Get slider source.
	 */
	private static function get_slider_source( $atts ) {
		return ! empty( $atts['wpex_bg_slider_source'] ) ? \sanitize_text_field( $atts['wpex_bg_slider_source'] ) : '';
	}

	/**
	 * Gets the slider html.
	 */
	private static function _get_slider_html( $atts ) {
		$source = self::get_slider_source( $atts );

		if ( ! $source ) {
			return;
		}

		switch ( $source ) {
			case 'custom':
				$images = ! empty( $atts['wpex_bg_slider_images'] ) ? \sanitize_text_field( $atts['wpex_bg_slider_images'] ) : '';
				if ( $images ) {
					$images = \array_unique( \array_filter( \explode( ',', $images ) ) );
				}
				break;
			case 'post_gallery':
				$images = \wpex_get_gallery_ids();
				break;
		}

		if ( empty( $images ) ) {
			return;
		}

		$json_list = [];

		foreach ( $images as $image ) {
			$image_url = \wp_get_attachment_url( $image );
			if ( $image_url ) {
				$alt = \get_post_meta( \wpex_parse_obj_id( $image, 'attachment' ), '_wp_attachment_image_alt', true );
				$json_list[] = [
					'src' => esc_js( esc_url( $image_url ) ),
					'alt' => $alt ? esc_js( $alt ) : '',
				];
			}
		}

		if ( ! $json_list ) {
			return;
		}

		\wp_enqueue_script( 'wpex-background-slider' );

		$class = 'wpex-background-slider wpex-absolute wpex-inset-0 wpex-overflow-hidden';

		$animation = ! empty( $atts['wpex_bg_slider_animation'] ) ? \sanitize_text_field( $atts['wpex_bg_slider_animation'] ) : 'fade';
		$trigger = ! empty( $atts['wpex_bg_slider_trigger'] ) ? \sanitize_text_field( $atts['wpex_bg_slider_trigger'] ) : 'load';

		$class .= ' wpex-background-slider--' . \sanitize_text_field( $animation );

		$json = [
			'images'       => $json_list,
			'interval'     => ! empty( $atts['wpex_bg_slider_interval'] ) ? \absint( $atts['wpex_bg_slider_interval'] ) : 5000,
			'randomize'    => ( ! empty( $atts['wpex_bg_slider_randomize'] ) && 'true' === $atts['wpex_bg_slider_randomize'] ) ? 1 : 0,
			'fadeDuration' => ! empty( $atts['wpex_bg_slider_fade_duration'] ) ? \absint( $atts['wpex_bg_slider_fade_duration'] ) : 2000,
			'loop'         => ( ! empty( $atts['wpex_bg_slider_loop'] ) && 'true' === $atts['wpex_bg_slider_loop'] ) ? 1 : 0,
			'trigger'      => $trigger,
			'animation'    => $animation,
		];

		if ( 'scroll' === $trigger && ( ! empty( $atts['wpex_bg_slider_observer_threshold'] ) || '0' == $atts['wpex_bg_slider_observer_threshold'] ) ) {
			$json['observerThreshold'] = \floatval( $atts['wpex_bg_slider_observer_threshold'] );
		}

		$inline_style = '';

		if ( 'ken_burns' === $animation ) {
			$zoom_safe = ! empty( $atts['wpex_bg_slider_zoom'] ) ? \floatval( $atts['wpex_bg_slider_zoom'] ) : '';
			if ( $zoom_safe ) {
				$inline_style .= "--wpex-bg-slider-zoom:{$zoom_safe}";
			}
			$json['zoomDelay'] = ! empty( $atts['wpex_bg_slider_zoom_delay'] ) ? \floatval( $atts['wpex_bg_slider_zoom_delay'] ) : '250'; 
		}

		if ( $inline_style ) {
			$inline_style = ' style="' . \esc_attr( $inline_style ) . '"';
		}

		return '<div class="' . esc_attr( $class ) . '" data-settings="' . \esc_attr( \json_encode( $json ) ) . '"' . $inline_style . '></div>';

	}

}
