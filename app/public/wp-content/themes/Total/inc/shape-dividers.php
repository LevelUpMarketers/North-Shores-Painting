<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Shape Dividers.
 */
final class Shape_Dividers {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of our class.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Returns shape divider choices.
	 */
	public function choices() {
		$choices = [
			''                      => \esc_html__( 'None', 'total' ),
			'tilt'                  => \esc_html__( 'Tilt', 'total' ),
			'triangle'              => \esc_html__( 'Triangle', 'total' ),
			'triangle_asymmetrical' => \esc_html__( 'Triangle Asymmetrical', 'total' ),
			'arrow'                 => \esc_html__( 'Arrow', 'total' ),
			'curve'                 => \esc_html__( 'Curve', 'total' ),
			'waves'                 => \esc_html__( 'Waves', 'total' ),
			'clouds'                => \esc_html__( 'Clouds', 'total' ),
			'mountains'             => \esc_html__( 'Mountains', 'total' ),
			'wave_brush'            => \esc_html__( 'Wave Brush', 'total' ),
		];

		$choices = (array) \apply_filters( 'wpex_get_section_shape_divider_types', $choices );

		/**
		 * Filters the available shape divider choices.
		 *
		 * @param array $choices
		 */
		$choices = (array) \apply_filters( 'wpex_shape_divider_types', $choices );

		return $choices;
	}

	/**
	 * Parse shortcode atts.
	 */
	private function get_settings( $position = 'top', $atts = [] ) {
		$settings = [
			'color'      => '',
			'width'      => '',
			'height'     => '',
			'infront'    => false,
			'flip'       => false,
			'invert'     => false,
			'visibility' => '',
		];

		foreach ( $settings as $k => $v ) {
			$atts_setting_k = 'wpex_shape_divider_' . $position . '_' . $k;
			if ( isset( $atts[ $atts_setting_k ] ) ) {
				$settings[ $k ] = $atts[ $atts_setting_k ];
			}
		}

		return (array) \apply_filters( 'wpex_get_shape_divider_settings', $settings, $atts );
	}

	/**
	 * Array of top shapes that need rotating.
	 */
	private function get_top_shapes_to_rotate() {
		return [
			'triangle',
			'triangle_asymmetrical',
			'arrow',
			'clouds',
			'curve',
			'waves',
		];
	}

	/**
	 * Array of bottom shapes that need rotating.
	 */
	private function get_bottom_shapes_to_rotate() {
		return [
			'tilt',
			'triangle',
			'triangle_asymmetrical',
			'arrow',
			'clouds',
			'curve',
			'waves',
			'mountains',
			'wave_brush',
		];
	}

	/**
	 * Check if divider should rotate.
	 */
	private function rotate_check( $position, $type, $invert ) {
		if ( 'top' === $position ) {
			if ( $invert && \in_array( $type, $this->get_top_shapes_to_rotate() ) ) {
				return true;
			}
		}
		if ( 'bottom' === $position ) {
			if ( ! $invert && \in_array( $type, $this->get_bottom_shapes_to_rotate() ) ) {
				return true;
			}
		}
	}

	/**
	 * Returns correct SVG for shape.
	 */
	private function get_svg( $type = '', $settings = [] ) {
		$svg          = '';
		$svg_filename = $type;

		if ( isset( $settings[ 'invert' ] )
			&& 'true' == $settings[ 'invert' ]
			&& ( 'tilt' !== $type )
		) {
			$svg_filename = $svg_filename . '-invert';
		}

		$shape_template = \locate_template( 'assets/shape-dividers/' . $svg_filename . '.svg', false );

		if ( $shape_template ) {
			$svg = \file_get_contents( $shape_template );
		}

		if ( ! $svg ) {
			return;
		}

		$svg_styles      = [];
		$svg_styles_html = '';

		if ( ! empty( $settings['height'] ) ) {
			$svg_styles['height'] = \absint( $settings['height'] ) . 'px';
		}

		if ( ! empty( $settings['width'] ) ) {
			$svg_styles['width'] = 'calc(' . \absint( $settings['width'] ) . '% + 1.3px)';
		}

		if ( $svg_styles ) {
			$svg_styles_html = ' style="';
				foreach ( $svg_styles as $name => $value ) {
					$svg_styles_html .= $name . ':' . esc_attr( $value ) . ';';
				}
			$svg_styles_html .= '"';
		}

		$path_attrs      = [];
		$path_attrs_html = '';
		$fill_color      = '#fff';

		if ( ! empty( $settings['color'] ) ) {
			$custom_fill_color = \wpex_parse_color( $settings['color'] );
			if ( $custom_fill_color ) {
				$fill_color = $custom_fill_color;
			}
		}

		$path_attrs['fill'] = $fill_color;

		if ( $path_attrs ) {
			foreach ( $path_attrs as $name => $value ) {
				$path_attrs_html .= ' ' . $name . '="' . \esc_attr( $value ) . '"';
			}
		}

		if ( $svg_styles_html ) {
			$svg = \str_replace(
				'<svg class="wpex-shape-divider-svg"',
				'<svg class="wpex-shape-divider-svg"' . $svg_styles_html,
				$svg
			);
		}

		if ( $path_attrs_html ) {
			$svg = \str_replace(
				'<path class="wpex-shape-divider-path"',
				'<path class="wpex-shape-divider-path"' . $path_attrs_html,
				$svg
			);
		}

		/**
		 * Filters the shape divider output.
		 *
		 * @param html $svg The divider svg output.
		 * @param string $type SVG type.
		 * @param array $settings Shortcode settings.
		 * @param html $svg_styles_html The inline styles added to the svg element.
		 * @param html $path_attrs_html The inline attributes added to the path element.
		 *
		 * @return string $shape_divider_svg
		 */
		$shape_divider = \apply_filters( 'wpex_get_shape_dividers_svg', $svg, $type, $settings, $svg_styles_html, $path_attrs_html );

		return $shape_divider;
	}

	/**
	 * Returns a shape divider.
	 */
	public static function get_divider( $position = 'top', $atts = [] ) {
		$type = ! empty( $atts["wpex_shape_divider_{$position}"] ) ? sanitize_text_field( $atts["wpex_shape_divider_{$position}"] ) : 'triangle';

		$settings = self::instance()->get_settings( $position, $atts );

		$classes = [
			'wpex-shape-divider',
			"wpex-shape-divider-{$type}",
			"wpex-shape-divider-{$position}",
			'wpex-absolute',
			'wpex-overflow-hidden',
			( 'bottom' === $position ) ? 'wpex-bottom-0' :  'wpex-top-0',
			'wpex-left-0',
			'wpex-w-100',
			'wpex-leading-none',
			'wpex-ltr',
		];

		// Apply a negative margin to fix subpixel rendering issues caused by fractional svg height
		if ( empty( $settings['height'] ) ) {
			$classes[] = ( 'bottom' === $position ) ? '-wpex-mb-1' :  '-wpex-mt-1';
		}

		$rotate  = false;
		$infront = isset( $settings['infront'] ) ? \wp_validate_boolean( $settings['infront'] ) : false;
		$flip    = isset( $settings['flip'] ) ? \wp_validate_boolean( $settings['flip'] ) : false;
		$invert  = isset( $settings['invert'] ) ? \wp_validate_boolean( $settings['invert'] ) : false;

		if ( $flip ) {
			$classes[] = 'wpex-shape-divider-flip';
		}

		if ( self::instance()->rotate_check( $position, $type, $invert ) ) {
			$classes[] = 'wpex-shape-divider-rotate';
			$classes[] = 'wpex-rotate-180';
		}

		if ( $infront && ! \totaltheme_is_wpb_frontend_editor() ) {
			$classes[] = 'wpex-shape-divider-infront';
			$classes[] = 'wpex-z-5';
		}

		if ( ! empty( $settings['visibility'] ) ) {
			$classes[] = \esc_attr( $settings['visibility'] );
		}

		$classes = \array_unique( $classes );

		return '<div class="' . \esc_attr( \implode( ' ', $classes ) ) .'">' . self::instance()->get_svg( $type, $settings ) . '</div>';
	}

}
