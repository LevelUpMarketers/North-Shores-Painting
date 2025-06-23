<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Generates the inline CSS for a shortcode.
 *
 * @todo Instead of targeting the element name like ".vcex-image.vcex_{uniqueid}" target via :is(vcex_{uniqueid}).
 */
final class Shortcode_CSS {

	/**
	 * Holds array of styles generated for the page.
	 */
	static protected $styles_list = [];

	/**
	 * Holds a counter of shortcodes and the items that have shown/rendered to generate the unique classname.
	 */
	static protected $shortcodes_counter = [];

	/**
	 * The shortcode unique classname.
	 */
	public $unique_classname;

	/**
	 * The shortcode class.
	 */
	protected $shortcode_class = '';

	/**
	 * The shortcode tag/name.
	 */
	protected $shortcode_tag = '';

	/**
	 * CSS array.
	 */
	protected $css_array = [];

	/**
	 * Checks if we should use important rules
	 */
	protected $use_important = false;

	/**
	 * Class Constructor.
	 */
	public function __construct( string $shortcode_class = '', array $shortcode_atts = [] ) {
		if ( ! \is_callable( [ $shortcode_class, 'get_params' ] ) ) {
			return;
		}

		$this->shortcode_class = $shortcode_class;
		$this->shortcode_tag   = $this->shortcode_class::TAG;
		$shortcode_params      = $this->shortcode_class::get_params();

		if ( ! $shortcode_params || ! \is_array( $shortcode_params ) ) {
			return;
		}

		$this->unique_classname = $this->create_unique_classname();
		$this->use_important = $this->important_check();

		$css_params = [];

		foreach ( $shortcode_params as $param_k => $param_v ) {
			if ( ! isset( $param_v['css'] ) || empty( $param_v['param_name'] ) || empty( $shortcode_atts[ $param_v['param_name'] ] ) ) {
				continue;
			}

			$value = $shortcode_atts[ $param_v['param_name'] ];
			$default_value = $param_v['std'] ?? '';

			if ( $value === $default_value || ! $this->check_css_dependency( $param_v, $value ) ) {
				continue;
			}

			$css_args    = $param_v['css'];
			$selector    = '';
			$media_query = '';

			if ( \is_array( $css_args ) ) {
				$property = $css_args['property'] ?? $param_v['param_name'];
				if ( ! empty( $css_args['selector'] ) ) {
					$selectors = $css_args['selector'];
					if ( \is_array( $selectors ) ) {
						foreach ( $selectors as $k => $v ) {
							if ( $parsed_selector = $this->parse_selector( $v, $shortcode_atts ) ) {
								$selectors[ $k ] = $parsed_selector;
							} else {
								unset( $selectors[ $k ] );
							}
						}
						$selector = \implode( ',', $selectors );
					} else {
						$selector = $this->parse_selector( $selectors, $shortcode_atts );
					}
				}
				if ( ! empty( $css_args['media_query'] ) ) {
					$media_query = $css_args['media_query'];
				}
			} else {
				$property = ( true === $css_args ) ? $param_v['param_name'] : $css_args;
			}

			if ( ! $selector ) {
				$shortcode_classname = $this->get_shortcode_classname();
				$selector = $shortcode_classname ? ".{$shortcode_classname}.{$this->unique_classname}" : ".{$this->unique_classname}";
			}

			if ( ! $property ) {
				continue;
			}

			if ( isset( $param_v['type'] ) && 'vcex_trbl' === $param_v['type'] && is_array( $value ) ) {
				$value = $this->parse_trbl_array_value( $value );
				if ( ! $value ) {
					continue;
				}
			}

			$param_settings = [
				'selector'    => $selector,
				'property'    => $property,
				'val'         => $value,
				'media_query' => $media_query,
				'important'   => $css_args['important'] ?? false,
			];

			$css_params[] = \array_filter( $param_settings );

		} // end foreach

		if ( \is_array( $css_params ) ) {
			foreach ( $css_params as $css_param ) {
				$this->add_css_to_array( $css_param );
			}
		}
	}

	/**
	 * Generates css for a specific param.
	 */
	public function add_css_to_array( $param ) {
		if ( \is_array( $param['property'] ) ) {
			$property = \array_map( [ $this, 'parse_property' ], $param['property'] );
		} else {
			$property = $this->parse_property( $param['property'] );
		}

		if ( \is_string( $property ) && ! \in_array( $property, [ 'margin', 'padding' ], true ) && \str_contains( $param['val'], '|' ) ) {
			$this->parse_responsive_param( $param['selector'], $property, $param['val'] );
		} else {
			$css_args = [];
			if ( \is_array( $property ) ) {
				foreach ( $property as $single_property ) {
					$css_args[ $single_property ] = $param['val'];
				}
			} else {
				$css_args[ $property ] = $param['val'];
			}
			$css = \vcex_inline_style( $css_args, false );
			if ( isset( $param['important'] ) && true === $param['important'] ) {
				$css = \str_replace( ';', '!important;', $css );
			}
			if ( $css ) {
				$media_query = $param['media_query'] ?? 0;
				$this->add_to_css_array( $media_query, $param['selector'], $css );
			}
		}
	}

	/**
	 * Wrapper function for add_css_to_array with parsing for the selector.
	 *
	 * This function is intended to be used outside the class when adding extra styles to an
	 * element.
	 */
	public function add_extra_css( array $param ): void {
		if ( ! empty( $param['selector'] ) ) {
			$param['selector'] = $this->parse_selector( $param['selector'], [] );
			$this->add_css_to_array( $param );
		}
	}

	/**
	 * Parses property name.
	 */
	protected function parse_property( $property = '' ): string {
		if ( ! \str_starts_with( $property, '--' ) ) {
			$property = \str_replace( '-', '_', $property );
		}
		return $property;
 	}

 	/**
	 * Parses property name.
	 */
	protected function parse_responsive_param( $selector = '', $property = '', $value = '' ) {
		$parsed_value = \vcex_parse_multi_attribute( $value );
		if ( ! \is_array( $parsed_value ) ) {
			return;
		}
		foreach ( $parsed_value as $device => $device_value ) {
			$css = \vcex_inline_style( [
				$property => $device_value,
			], false );
			if ( $css ) {
				$media_query = $this->get_media_query_from_device( $device ) ?: 0;
				$this->add_to_css_array( $media_query, $selector, $css );
			}
		}
	}

	/**
	 * Adds item to the css array.
	 */
	protected function add_to_css_array( $media_query, $selector, $css ) {
		$this->css_array[ $media_query ][ $selector ][] = $css;
	}

 	/**
	 * Loops through selectors and combines CSS.
	 */
	protected function parse_selectors_css( $selectors = [] ): string {
		$css = '';
		foreach ( $selectors as $selector => $properties ) {
			$prop_string = \implode( '', $properties );
			if ( $this->use_important ) {
				$prop_string = \str_replace( ';', '!important;', $prop_string );
			}
			$css .= "{$selector}{{$prop_string}}";
		}
		return $css;
	}

	/**
	 * Returns media query based on device prefix.
	 */
	protected function get_media_query_from_device( $device ): ?string {
		$breakpoints = \vcex_get_css_breakpoints();
		if ( ! empty( $breakpoints[ $device ] ) ) {
			$safe_bk = \esc_attr( $breakpoints[ $device ] );
			return "@media (max-width:{$safe_bk})";
		}
		return null;
	}

	/**
	 * Returns CSS.
	 */
	public function get_css(): string {
		$css = '';
		if ( $this->css_array ) {
			foreach ( $this->css_array as $media_query => $selectors ) {
				$parsed_css = $this->parse_selectors_css( $selectors );
				if ( $media_query ) {
					$css .= "{$media_query}{{$parsed_css}}";
				} else {
					$css .= $parsed_css;
				}
			}
		}
		return $css;
	}

	/**
	 * Returns style.
	 */
	public function render_style( $echo_style = true ) {
		$check_storage = $this->check_storage();
		$css = $this->get_css();
		if ( $check_storage ) {
			$css_to_store = \str_replace( $this->unique_classname, 'vcex_unique_classname', $css );
			if ( $key = \array_search( $css_to_store, self::$styles_list ) ) {
				$this->unique_classname = $key;
				return true;
			}
		}
		if ( $css ) {
			if ( $check_storage ) {
				self::$styles_list[ $this->unique_classname ] = $css_to_store;
			}
			$render_style = (bool) \apply_filters( 'vcex_shortcode_css_render_style', true );
			if ( $render_style ) {
				if ( $echo_style ) {
					echo "<style>{$css}</style>";
				} else {
					return $css;
				}
			}
			return true;
		}
	}

	/**
	 * Returns true or false if CSS should be calculated for the specific param.
	 */
	protected function check_css_dependency( array $param, $value ): bool {
		return true;
		/*if ( ! isset( $param['css_condition'] ) ) {
			return true;
		}
		return \is_callable( $param['css_condition'] ) && \call_user_func( $param['css_condition'], $value );*/
	}

	/**
	 * Parses a selector to add the unique class id.
	 */
	protected function parse_selector( string $selector, array $atts ): string {
		if ( '.overlay-parent' === $selector
			&& ( empty( $atts['overlay_style'] ) || 'none' === $atts['overlay_style'] )
		) {
			return '';
		}
		$target = $this->get_unique_selector();
		if ( 'self' === $selector || '{{WRAPPER}}' === $selector ) {
			return $target;
		} else if ( 'vcex_off_canvas_menu' === $this->shortcode_tag && '.wpex-off-canvas' === $selector ) {
			return "{$target}{$selector}";
		} else if ( \str_contains( $selector, '{{WRAPPER}}' ) ) {
			return \str_replace( '{{WRAPPER}}', $target, $selector );
		} else {
			return "{$target} {$selector}";
		}
	}

	/**
	 * Returns unique classname for element.
	 */
	protected function create_unique_classname(): string {
		return \vcex_element_unique_classname();
	}

	/**
	 * Returns shortcode element classname.
	 */
	protected function get_shortcode_classname(): string {
		if ( 'vcex_off_canvas_menu' == $this->shortcode_tag || 'vcex_horizontal_menu' == $this->shortcode_tag ) {
			return ''; // don't use wrapper on newer elements - @todo remove from all elements in the future
		}
		$classname = \str_replace( '_', '-', $this->shortcode_tag );
		if ( $this->shortcode_has_wrapper( $this->shortcode_tag ) ) {
			$classname .= '-wrap';
		}
		switch ( $classname ) {
			case 'vcex-alert':
				$classname = 'wpex-alert';
				break;
			case 'vcex-list-item':
				$classname = 'vcex-list_item';
				break;
			case 'vcex-image-flexslider':
				$classname = 'vcex-image-slider';
				break;
			case 'vcex-image-galleryslider':
				$classname = 'vcex-image-gallery-slider';
				break;
		}
		return $classname;
	}

	/**
	 * Checks if a given shortcode has a wrapper.
	 */
	protected function shortcode_has_wrapper( $shortcode_tag ): bool {
		return \in_array( $shortcode_tag, [
			'vcex_recent_news',
			'vcex_countdown',
			'vcex_image_ba',
			'vcex_skillbar',
			'vcex_blog_grid',
			'vcex_portfolio_grid',
			'vcex_post_type_grid',
		], true );
	}

	/**
	 * Checks if we should check for existing styles and apply existing ones to shortcodes with the same styling.
	 *
	 * @todo this should perhaps be enabled by default but would need the ability to disable via the admin or
	 * allow it to be enabled in the admin under "Optimizations".
	 *
	 * The issue with this function is if any theme function runs do_shortcode() on any content
	 * before it's actually rendered, the CSS may not be inserted on the page. No way around that :/
	 */
	protected function check_storage(): bool {
		$check = (bool) \apply_filters( 'vcex_shortcode_css_store_styles', false );
		if ( $check && \vcex_vc_is_inline() ) {
			$check = false;
		}
		return $check;
	}

	/**
	 * Checks if we should apply important attributes to all custom inline styles.
	 */
	protected function important_check(): bool {
		return (bool) \apply_filters( 'vcex_shortcode_css_use_important_rule', $this->use_important, $this->shortcode_tag );
	}

	/**
	 * Returns array of stored styles.
	 */
	public static function get_styles_list(): array {
		return self::$styles_list;
	}

	/**
	 * Returns unique classname.
	 */
	public function get_unique_classname(): string {
		return $this->unique_classname;
	}

	/**
	 * Returns unique selector.
	 */
	public function get_unique_selector(): string {
		$shortcode_classname = $this->get_shortcode_classname();
		return $shortcode_classname ? ".{$shortcode_classname}.{$this->unique_classname}" : ".{$this->unique_classname}";
	}

	/**
	 * Parses trbl array value (used with Elementor).
	 */
	private function parse_trbl_array_value( $value ) {
		$unit    = $value['unit'] ?? 'px';
		$new_val = [];
		$directions = [ 'top', 'bottom', 'left', 'right' ];
		foreach ( $directions as $dir ) {
			$dir_val = isset( $value[ $dir ] ) ? \trim( (string) $value[ $dir ] ) : '';
			if ( $dir_val || '0' === $dir_val ) {
				$dir_val = $value[ $dir ];
				$new_val[] = "{$dir}:{$dir_val}{$unit}";
			}
		}
		return \implode( '|', $new_val );
	}

}
