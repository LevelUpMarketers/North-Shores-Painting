<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

final class BG_Overlays {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Shortcodes to add overlay settings to.
	 */
	private $shortcodes = [
		'vc_section',
		'vc_row',
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
		\add_action( 'vc_after_init', [ $this, 'add_params' ] );
		\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ $this, 'add_classes' ], 10, 3 );
		\add_filter( 'vc_edit_form_fields_attributes_vc_row', [ $this, 'edit_form_fields' ] );

		foreach ( $this->shortcodes as $shortcode ) {
			\add_filter( "shortcode_atts_{$shortcode}", [ $this, '_filter_shortcode_atts' ], 99, 4 );
			\add_filter( $this->get_insert_hook( $shortcode ), [ $this, 'insert_overlay' ], 50, 2 ); // priority is important.
		}
	}

	/**
	 * Returns the hook for inserting the overlay.
	 */
	protected function get_insert_hook( $shortcode = '' ) {
		if ( 'vc_column' === $shortcode ) {
			$shortcode = 'vc_column_inner';
		}
		return "wpex_hook_{$shortcode}_top";
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
				'heading' => \esc_html__( 'Overlay Style', 'total' ),
				'param_name' => 'wpex_bg_overlay',
				'group' => \esc_html__( 'Overlay', 'total' ),
				'choices' => [
					'' => \esc_html__( 'None', 'total' ),
					'color' => \esc_html__( 'Color', 'total' ),
					'dark' => \esc_html__( 'Dark', 'total' ),
					'dotted' => \esc_html__( 'Dotted', 'total' ),
					'dashed' => \esc_html__( 'Diagonal Lines', 'total' ),
					'custom' => \esc_html__( 'Custom', 'total' ),
				],
			],
			[
				'type' => 'vcex_select',
				'heading' => \esc_html__( 'Overlay Mix Blend Mode', 'total' ),
				'param_name' => 'wpex_bg_overlay_blend',
				'choices' => 'mix_blend_mode',
				'group' => \esc_html__( 'Overlay', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_overlay', 'not_empty' => true ],
			],
			[
				'type' => 'vcex_colorpicker',
				'heading' => \esc_html__( 'Overlay Color', 'total' ),
				'param_name' => 'wpex_bg_overlay_color',
				'group' => \esc_html__( 'Overlay', 'total' ),
				'dependency' => [
					'element' => 'wpex_bg_overlay',
					'value' => [ 'color', 'dark', 'dotted', 'dashed', 'custom' ]
				],
			],
			[
				'type' => 'vcex_colorpicker',
				'heading' => \esc_html__( 'Hover: Overlay Color', 'total' ),
				'param_name' => 'wpex_bg_overlay_color_hover',
				'group' => \esc_html__( 'Overlay', 'total' ),
				'dependency' => [
					'element' => 'wpex_bg_overlay',
					'value' => [ 'color', 'dark', 'dotted', 'dashed', 'custom' ]
				],
			],
			[
				'type' => 'attach_image',
				'heading' => \esc_html__( 'Custom Overlay Pattern', 'total' ),
				'param_name' => 'wpex_bg_overlay_image',
				'group' => \esc_html__( 'Overlay', 'total' ),
				'dependency' => [ 'element' => 'wpex_bg_overlay', 'value' => 'custom' ],
			],
			[
				'type' => 'vcex_text',
				'heading' => \esc_html__( 'Overlay Opacity', 'total' ),
				'param_name' => 'wpex_bg_overlay_opacity',
				'placeholder' => '65%',
				'dependency' => [
					'element' => 'wpex_bg_overlay',
					'value' => [ 'color', 'dark', 'dotted', 'dashed', 'custom' ]
				],
				'group' => \esc_html__( 'Overlay', 'total' ),
			],
			[
				'type' => 'vcex_text',
				'heading' => \esc_html__( 'Overlay Opacity: Hover', 'total' ),
				'param_name' => 'wpex_bg_overlay_opacity_hover',
				'dependency' => [
					'element' => 'wpex_bg_overlay',
					'value' => [ 'color', 'dark', 'dotted', 'dashed', 'custom' ]
				],
				'group' => \esc_html__( 'Overlay', 'total' ),
			],
			[
				'type' => 'vcex_select',
				'choices' => 'transition_duration',
				'heading' => esc_html__( 'Hover Animation Speed', 'total-theme-core' ),
				'param_name' => 'wpex_bg_overlay_transition_duration',
				'dependency' => [
					'element' => 'wpex_bg_overlay',
					'value' => [ 'color', 'dark', 'dotted', 'dashed', 'custom' ]
				],
				'group' => \esc_html__( 'Overlay', 'total' ),
			],
		];
	}

	/**
	 * Parses shortcode attributes when editing the shortcodes.
	 */
	public function edit_form_fields( $atts ) {
		if ( ! empty( $atts['video_bg_overlay'] ) && 'none' !== $atts['video_bg_overlay'] ) {
			$atts['wpex_bg_overlay'] = $atts['video_bg_overlay'];
			unset( $atts['video_bg_overlay'] );
		}
		return $atts;
	}

	/**
	 * Adds classes to shortcodes that have overlays.
	 */
	public function add_classes( $class_string, $tag, $atts ) {
		if ( \in_array( $tag, $this->shortcodes, true ) && $this->has_overlay( $atts ) ) {
			$class_string .= ' wpex-has-overlay';
			if ( isset( $atts['wpex_bg_overlay_css_data'] ) && ! empty( $atts['wpex_bg_overlay_css_data']['class'] ) ) {
				$class_string .= ' ' . esc_attr( $atts['wpex_bg_overlay_css_data']['class'] );
			}
			if ( ! \str_contains( $class_string, 'wpex-relative' ) && ! \str_contains( $class_string, 'wpex-sticky' ) ) {
				$class_string .= ' wpex-relative';
			}
		}
		return $class_string;
	}

	/**
	 * Parses atts on front-end to add a mock "wpex_bg_overlay_style" attr.
	 */
	public function _filter_shortcode_atts( $out, $pairs, $atts, $shortcode ) {
		if ( $this->has_overlay( $out ) ) {
			$css = $this->get_inline_css_data( $out, $shortcode );
			if ( $css ) {
				$out['wpex_bg_overlay_css_data'] = $css;
			}
		}
		return $out;
	}

	/**
	 * Inserts the overlay HTML into the shortcodes.
	 */
	public function insert_overlay( $content, $atts ) {
		if ( $overlay = $this->render_overlay( $atts ) ) {
			$content .= $overlay;
		}
		return $content;
	}

	/**
	 * Render the overlay.
	 */
	private function render_overlay( $atts ) {
		if ( ! $this->has_overlay( $atts ) ) {
			return;
		}

		$overlay = $atts['wpex_bg_overlay'];

		// Inner classes
		$inner_class = [
			'wpex-bg-overlay',
			\sanitize_html_class( $overlay ),
			'wpex-absolute',
			'wpex-inset-0',
			'wpex-rounded-inherit',
		];

		if ( ! empty( $atts['wpex_bg_overlay_transition_duration'] ) ) {
			$inner_class[] = 'wpex-duration-' . \absint( $atts['wpex_bg_overlay_transition_duration'] );
		} elseif ( ! empty( $atts['wpex_bg_overlay_css_data'] ) ) {
			$inner_class[] = 'wpex-duration-500';
		}

		if ( ! in_array( $overlay, [ 'dotted', 'dashed' ], true ) ) {
			$inner_class[] = 'wpex-opacity-60';
		}

		if ( ! empty( $atts['wpex_bg_overlay_blend'] ) ) {
			$inner_class[] = 'wpex-mix-blend-' . \sanitize_html_class( $atts['wpex_bg_overlay_blend'] );
		}
		
		if ( in_array( $overlay, [ 'custom', 'dotted', 'dashed' ], true )
			|| ( 'custom' === $overlay
				&& ! empty( $atts['wpex_bg_overlay_image'] )
				&& \wp_get_attachment_url( $atts['wpex_bg_overlay_image'] )
			)
		) {
			$inner_class[] = 'wpex-bg-transparent';
		} else {
			$inner_class[] = 'wpex-bg-black';
		}

		if ( isset( $atts['wpex_bg_overlay_css_data'] ) && ! empty( $atts['wpex_bg_overlay_css_data']['styles'] ) ) {
			$style = '<style class="wpex-bg-overlay-css">' . $atts['wpex_bg_overlay_css_data']['styles'] . '</style>';
		} else {
			$style = '';
		}

		return '<div class="wpex-bg-overlay-wrap wpex-absolute wpex-inset-0 wpex-rounded-inherit">' . $style . '<span class="' . \esc_attr( implode( ' ', $inner_class ) ) . '"></span></div>';
	}

	/**
	 * Return inline CSS for an overlay.
	 */
	private function get_inline_css_data( $atts, $shortcode ) {
		$overlay = $atts['wpex_bg_overlay'];
		$style = $css = $hover_css = $bg_image = '';

		switch ( $overlay ) {
			case 'custom':
				$bg_image = ! empty( $atts['wpex_bg_overlay_image'] ) ? \wp_get_attachment_url( $atts['wpex_bg_overlay_image'] ) : '';
				break;
			case 'dotted':
				$bg_image = \wpex_asset_url( 'images/overlays/dotted.png' );
				break;
			case 'dashed':
				$bg_image = \wpex_asset_url( 'images/overlays/dashed.png' );
				break;
		}

		if ( $bg_image ) {
			$bg_image_safe = \esc_url( $bg_image );
			if ( $bg_image_safe ) {
				$css .= "background-image:url({$bg_image_safe});";
			}
		}
		
		if ( $bg_image || in_array( $overlay, [ 'custom', 'dotted', 'dashed' ], true ) ) {
			$inner_class[] = 'wpex-bg-transparent';
		} else {
			$inner_class[] = 'wpex-bg-black';
		}

		if ( ! empty( $atts['wpex_bg_overlay_color'] ) && $overlay_color_safe = \wpex_parse_color( $atts['wpex_bg_overlay_color'] ) ) {
			$css .= "background-color:{$overlay_color_safe};";
		}

		if ( ! empty( $atts['wpex_bg_overlay_opacity'] ) && $opacity_safe = sanitize_text_field( $atts['wpex_bg_overlay_opacity'] ) ) {
			if ( '1' !== $opacity_safe && \is_numeric( $opacity_safe ) && ! \str_contains( $opacity_safe, '.' ) ) {
				$opacity_safe = "{$opacity_safe}%";
			}
			$css .= "opacity:{$opacity_safe};";
		}

		if ( ! empty( $atts['wpex_bg_overlay_color_hover'] ) ) {
			$color_hover_safe = \wpex_parse_color( $atts['wpex_bg_overlay_color_hover'] );
			if ( $color_hover_safe ) {
				$hover_css .= "background-color:{$color_hover_safe};";
			}
		}

		if ( ! empty( $atts['wpex_bg_overlay_opacity_hover'] ) ) {
			$opacity_hover_safe = sanitize_text_field( $atts['wpex_bg_overlay_opacity_hover'] );
			if ( $opacity_hover_safe ) {
				if ( '1' !== $opacity_hover_safe && \is_numeric( $opacity_hover_safe ) && ! \str_contains( $opacity_hover_safe, '.' ) ) {
					$opacity_hover_safe = "{$opacity_hover_safe}%";
				}
				$hover_css .= "opacity:{$opacity_hover_safe};";
			}
		}

		if ( $css || $hover_css ) {
			$uniqid = uniqid( 'wpex-has-overlay--' );
			$all_css = [];
			$mid_target = ( 'vc_column' === $shortcode ) ? '> .vc_column-inner >' : '>';
			if ( $css ) {
				$all_css[] = ".{$uniqid} {$mid_target} .wpex-bg-overlay-wrap .wpex-bg-overlay{{$css}}";
			}
			if ( $hover_css ) {
				$all_css[] = ".{$uniqid}:hover {$mid_target} .wpex-bg-overlay-wrap .wpex-bg-overlay{{$hover_css}}";
			}
			return [
				'class'  => $uniqid,
				'styles' => implode( '', $all_css ),
			];
		}
	}

	/**
	 * Helper to check if a shortcode supports overlay bgs and has one.
	 */
	private function has_overlay( $atts ): bool {
		return ! empty( $atts['wpex_bg_overlay'] ) && 'none' !== $atts['wpex_bg_overlay'];
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
