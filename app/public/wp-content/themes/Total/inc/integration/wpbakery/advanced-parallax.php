<?php

namespace TotalTheme\Integration\WPBakery;

use TotalTheme\Integration\WPBakery\Helpers as WPB_Helpers;

\defined( 'ABSPATH' ) || exit;

final class Advanced_Parallax {

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
		if ( ! \class_exists( '\TotalTheme\Integration\WPBakery\Helpers' ) ) {
			return;
		}

		\add_action( 'vc_after_init', [ $this, 'vc_after_init' ] );
		\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ $this, 'add_classes' ], 10, 3 );

		if ( 'vc_edit_form' === \vc_post_param( 'action' ) ) {
			\add_filter( 'vc_edit_form_fields_attributes_vc_row', [ $this, 'edit_form_fields' ] );
		}

		foreach ( $this->shortcodes as $shortcode ) {
			// @note the priority is important!
			\add_filter( $this->get_insert_hook( $shortcode ), [ $this, 'insert_parallax' ], 5, 2 );
			\add_filter( "shortcode_atts_{$shortcode}", [ $this, 'parse_shortcode_atts' ], 99 );
		}
	}

	/**
	 * Returns the hook name for inserting the shape dividers.
	 */
	protected function get_insert_hook( $shortcode = '' ) {
		if ( 'vc_column' === $shortcode ) {
			$shortcode = 'vc_column_inner';
		}
		return "wpex_hook_{$shortcode}_top";
	}

	/**
	 * Runs on vc_after_init
	 */
	public function vc_after_init() {
		$this->modify_params();
		$this->add_params();
	}

	/**
	 * Modify shortcode params.
	 */
	public function modify_params() {
		if ( ! \function_exists( 'vc_update_shortcode_param' ) ) {
			return;
		}

		foreach ( $this->shortcodes as $shortcode ) {

			// Alter Parallax dropdown.
			$param = \WPBMap::getParam( $shortcode, 'parallax' );
			if ( $param ) {
				$param['group'] = \esc_html__( 'Parallax', 'total' );
				$param['value'][\esc_html__( 'Advanced Parallax', 'total' )] = 'vcex_parallax';
				\vc_update_shortcode_param( $shortcode, $param );
			}

			// Alter Parallax image location.
			$param = \WPBMap::getParam( $shortcode, 'parallax_image' );
			if ( $param ) {
				$param['group'] = \esc_html__( 'Parallax', 'total' );
				\vc_update_shortcode_param( $shortcode, $param );
			}

			// Alter Parallax speed location.
			$param = \WPBMap::getParam( $shortcode, 'parallax_speed_bg' );
			if ( $param ) {
				$param['group'] = \esc_html__( 'Parallax', 'total' );
				$param['dependency'] = [
					'element' => 'parallax',
					'value' => [ 'content-moving', 'content-moving-fade' ],
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
				'type'        => 'vcex_ofswitch',
				'heading'     => \esc_html__( 'Enable parallax for mobile devices', 'total' ),
				'param_name'  => 'parallax_mobile',
				'vcex'        => array( 'off' => 'no', 'on'  => 'yes' ),
				'std'         => 'no',
				'description' => \esc_html__( 'Parallax effects would most probably cause slowdowns when your site is viewed in mobile devices. By default it is disabled.', 'total' ),
				'group'       => \esc_html__( 'Parallax', 'total' ),
				'dependency'  => [
					'element' => 'parallax',
					'value'   => 'vcex_parallax',
				],
			],
			[
				'type'        => 'dropdown',
				'heading'     => \esc_html__( 'Parallax Style', 'total' ),
				'param_name'  => 'parallax_style',
				'group'       => \esc_html__( 'Parallax', 'total' ),
				'value'       => array(
					\esc_html__( 'Cover', 'total' )               => '',
					\esc_html__( 'Fixed and Repeat', 'total' )    => 'fixed-repeat',
					\esc_html__( 'Fixed and No-Repeat', 'total' ) => 'fixed-no-repeat',
				),
				'dependency'  => array(
					'element' => 'parallax',
					'value'   => 'vcex_parallax',
				),
			],
			[
				'type'        => 'dropdown',
				'heading'     => \esc_html__( 'Parallax Direction', 'total' ),
				'param_name'  => 'parallax_direction',
				'value'       => [
					\esc_html__( 'Up', 'total' )    => '',
					\esc_html__( 'Down', 'total' )  => 'down',
					\esc_html__( 'Left', 'total' )  => 'left',
					\esc_html__( 'Right', 'total' ) => 'right',
				],
				'group'       => \esc_html__( 'Parallax', 'total' ),
				'dependency'  => [
					'element' => 'parallax',
					'value'   => 'vcex_parallax',
				],
			],
			[
				'type'        => 'textfield',
				'heading'     => \esc_html__( 'Parallax Speed', 'total' ),
				'param_name'  => 'parallax_speed',
				'description' => \esc_html__( 'The movement speed, value should be between 0.1 and 1.0. A lower number means slower scrolling speed. Be mindful of the background size and the dimensions of your background image when setting this value. Faster scrolling means that the image will move faster, make sure that your background image has enough width or height for the offset.', 'total' ),
				'group'       => \esc_html__( 'Parallax', 'total' ),
				'dependency'  => [
					'element' => 'parallax',
					'value'   => 'vcex_parallax',
				],
			],
		];
	}

	/**
	 * Adds classes to shortcodes that have parallax.
	 *
	 * @param string $class_string The class string to add to the shortcode.
	 * @param string $tag The shortcode tag.
	 * @param array $atts The shortcode attributes.
	 */
	public function add_classes( $class_string, $tag, $atts ) {
		if ( \in_array( $tag, $this->shortcodes ) && ! empty( $atts['vcex_parallax'] ) ) {
			$class_string .= ' wpex-parallax-bg-wrap';
			if ( ! str_contains( $class_string, 'wpex-relative' ) ) {
				$class_string .= ' wpex-relative';
			}
		}
		return $class_string;
	}

	/**
	 * Inserts the parallax HTML into the shortcodes.
	 *
	 * @param  string $content The wpex_hook_{shortcode}_bottom content.
	 * @param array $atts The shortcode attributes.
	 */
	public function insert_parallax( $content, $atts ) {
		if ( $parallax = $this->render_parallax_bg( $atts ) ) {
			$content .= $parallax;
		}
		return $content;
	}

	/**
	 * Parses the shortcode attributes to set parallax to null if vcex_parallax is selected.
	 *
	 * @param array $atts The shortcode attributes.
	 */
	public function parse_shortcode_atts( $atts ) {

		// Set parallax image equal to custom image.
		if ( ! empty( $atts['background_image_id'] ) && ( ! empty( $atts['parallax'] ) || ! empty( $atts['vcex_parallax'] ) ) ) {
			$atts['parallax_image'] = $atts['background_image_id'];
		}

		// Advanced parallax.
		$advanced_parallax = false;

		if ( ! empty( $atts['parallax'] ) ) {
			if ( 'vcex_parallax' === $atts['parallax']
				|| 'simple' === $atts['parallax']
				|| 'advanced' === $atts['parallax']
				|| 'true' === $atts['parallax']
			) {
				$advanced_parallax = true;
			}
		} elseif ( ! empty( $atts['bg_style'] )
			&& ( 'parallax' === $atts['bg_style'] || 'parallax-advanced' === $atts['bg_style'] )
		) {
			$advanced_parallax = true;
		}

		if ( $advanced_parallax ) {
			$atts['parallax']      = '';
			$atts['vcex_parallax'] = true; // this is a "fake" attribute.

			// Set the correct bg image from css param if not defined.
			if ( empty( $atts['parallax_image'] )
				&& empty( $atts['bg_image'] )
				&& ! empty( $atts['css'] )
				&& $bg_image = WPB_Helpers::get_background_image_url_from_css( $atts['css'] )
			) {
				$atts['parallax_image'] = $bg_image;
			}

		}

		return $atts;
	}

	/**
	 * Parses shortcode attributes when editing the shortcodes.
	 *
	 * @param array $atts The shortcode attributes.
	 */
	public function edit_form_fields( $atts ) {
		if ( ! empty( $atts['parallax'] ) ) {
			if ( 'simple' === $atts['parallax']
				|| 'advanced' === $atts['parallax']
				|| 'true' === $atts['parallax']
			) {
				$atts['parallax'] = 'vcex_parallax';
			}
		} elseif ( ! empty( $atts['bg_style'] )
			&& ( 'parallax' == $atts['bg_style'] || 'parallax-advanced' == $atts['bg_style'] )
		) {
			$atts['parallax'] = 'vcex_parallax';
			unset( $atts['bg_style'] );
		}
		return $atts;
	}

	/**
	 * Render the parallax bg.
	 */
	public function render_parallax_bg( $shortcode_atts ) {
		if ( empty( $shortcode_atts['vcex_parallax'] )
			|| ! empty( $shortcode_atts['wpex_self_hosted_video_bg'] )
		) {
			return;
		}

		// Get $bg_image.
		if ( ! empty( $shortcode_atts['parallax_image'] ) ) {
			$bg_image = $shortcode_atts['parallax_image'];
		} elseif ( ! empty( $shortcode_atts['bg_image'] ) ) {
			$bg_image = $shortcode_atts['bg_image'];
		} else {
			return;
		}
		
		// Convert bg image from attachment ID to URL.
		if ( \is_numeric( $bg_image ) ) {
			$bg_image = \wp_get_attachment_url( $bg_image );
		}

		// Sanitize bg image.
		$bg_image_safe = \esc_url( $bg_image );

		if ( ! $bg_image_safe ) {
			return;
		}

		// Default settings.
		$parallax_style     = '';
		$parallax_speed     = '0.2';
		$parallax_direction = 'up';
		$fixed_bg           = 'false';

		// Custom settings.
		if ( ! empty( $shortcode_atts['parallax_style'] ) ) {
			$parallax_style = \sanitize_text_field( $shortcode_atts['parallax_style'] );
			/*if ( 'fixed-repeat' === $shortcode_atts['parallax_style']
				|| 'fixed-no-repeat' === $shortcode_atts['parallax_style']
			) {
				//$fixed_bg = 'true'; // @todo was this deprecated?
			}*/
		}

		if ( ! empty( $shortcode_atts['parallax_speed'] ) ) {
			$parallax_speed = \floatval( $shortcode_atts['parallax_speed'] );
		}

		if ( ! empty( $shortcode_atts['parallax_direction'] ) ) {
			$parallax_direction = \sanitize_text_field( $shortcode_atts['parallax_direction'] );
		}

		// Classes.
		$classes = [
			'wpex-parallax-bg',
			'wpex-absolute',
			'wpex-inset-0',
			'wpex-bg-fixed',
			'wpex-bg-left-top',
		];

		switch ( $parallax_style ) {
			case 'fixed-repeat':
				$classes[] = 'wpex-bg-auto';
				$classes[] = 'wpex-bg-repeat';
				break;
			case 'fixed-no-repeat':
				$classes[] = 'wpex-bg-auto';
				$classes[] = 'wpex-bg-no-repeat';
				break;
			default:
				$classes[] = 'wpex-bg-cover';
				break;
		}

		if ( $parallax_style ) {
			$classes[] = $parallax_style;
		}

		if ( isset( $shortcode_atts['parallax_mobile'] ) && 'no' === $shortcode_atts['parallax_mobile'] ) {
			$classes[] = 'not-mobile';
		}

		/**
		 * Filters the parallax classes
		 *
		 * @param array $classes
		 * @param array $shortcode_atts
		 */
		$classes = (array) \apply_filters( 'wpex_parallax_classes', $classes, $shortcode_atts );

		$html_attributes = [
			'class'          => $classes,
			'data-direction' => $parallax_direction,
			'data-velocity'  => "-{$parallax_speed}",
			'data-fixed'     => $fixed_bg,
			'style'          => "background-image:url({$bg_image_safe});",
		];

		/**
		 * Filters the parallax background html attributes.
		 *
		 * @param array $html_attributes.
		 * @param array $shortcode_attributes
		 */
		$attributes = (array) \apply_filters( 'wpex_parallax_html_attributes', $html_attributes, $shortcode_atts );

		\wp_enqueue_script( 'wpex-parallax-backgrounds' );

		return \wpex_parse_html( 'div', $attributes );
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
