<?php

namespace TotalThemeCore\Vcex\Carousel;

\defined( 'ABSPATH' ) || exit;

/**
 * Core Carousel methods.
 */
class Core {

	/**
	 * Returns list of style dependencies.
	 */
	public static function get_style_depends() {
		return [
			'wpex-owl-carousel',
		];
	}

	/**
	 * Returns list of script dependencies.
	 */
	public static function get_script_depends() {
		return [
			'wpex-owl-carousel',
			'vcex-carousels',
		];
	}

	/**
	 * Registers the carousel scripts.
	 */
	public static function register_scripts() {
		\wp_register_style(
			'wpex-owl-carousel',
			\vcex_get_css_file( 'vendor/wpex-owl-carousel' ),
			[],
			TTC_VERSION
		);

		\wp_register_script(
			'wpex-owl-carousel',
			\vcex_get_js_file( self::use_owl_classnames() ? 'vendor/wpex-owl-carousel-classic' : 'vendor/wpex-owl-carousel' ),
			[ 'jquery' ],
			TTC_VERSION,
			true
		);

		\wp_register_script(
			'vcex-carousels',
			\vcex_get_js_file( 'frontend/carousels' ),
			[ 'jquery', 'wpex-owl-carousel', 'imagesloaded' ],
			TTC_VERSION,
			true
		);

		\wp_localize_script( 'vcex-carousels', 'vcex_carousels', self::get_default_settings() );
	}

	/**
	 * Enqueues the carousel scripts.
	 */
	public static function enqueue_scripts() {
		foreach ( self::get_style_depends() as $style ) {
			\wp_enqueue_style( $style );
		}

		foreach ( self::get_script_depends() as $script ) {
			\wp_enqueue_script( $script );
		}
	}

	/**
	 * Returns shortcode params.
	 */
	public static function get_shortcode_params( $dependency = [], $group = '' ) {
		$carousel_defaults = self::get_default_settings();
		$params = [
			[
				'type' => 'vcex_subheading',
				'param_name' => 'vcex_subheading__carousel',
				'text' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Arrows', 'total-theme-core' ),
				'param_name' => 'arrows',
				'std' => 'true',
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_select',
				'choices' => 'carousel_arrow_styles',
				'heading' => \esc_html__( 'Arrows Style', 'total-theme-core' ),
				'param_name' => 'arrows_style',
				'dependency' => [ 'element' => 'arrows', 'value' => 'true' ],
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_select',
				'choices' => 'carousel_arrow_positions',
				'heading' => \esc_html__( 'Arrows Position', 'total-theme-core' ),
				'param_name' => 'arrows_position',
				'dependency' => [ 'element' => 'arrows', 'value' => 'true' ],
				'std' => 'default',
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Dot Navigation', 'total-theme-core' ),
				'param_name' => 'dots',
				'std' => 'false',
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Auto Play', 'total-theme-core' ),
				'param_name' => 'auto_play',
				'std' => 'false',
				'admin_label' => true,
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_select',
				'heading' => \esc_html__( 'Autoplay Type', 'total-theme-core' ),
				'param_name' => 'autoplay_type',
				'std' => 'default',
				'choices' => [
					'default' => \esc_html__( 'Default', 'total-theme-core' ),
					'smooth' => \esc_html__( 'Smooth', 'total-theme-core' ),
				],
				'description' => \esc_html__( 'The "Smooth" autoplay type will remove the carousel arrows and dot navigation. Items will scroll automatically and can\'t be paused, ideal for displaying logos.', 'total-theme-core' ),
				'dependency' => [ 'element' => 'auto_play', 'value' => 'true' ],
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_text',
				'input_type' => 'number',
				'heading' => \esc_html__( 'Autoplay Interval Timeout', 'total-theme-core' ),
				'param_name' => 'timeout_duration',
				'placeholder' => $carousel_defaults['autoplayTimeout'] ?? '5000',
				'description' => esc_html__( 'Time in milliseconds between each auto slide.', 'total-theme-core' ),
				'dependency' => [ 'element' => 'autoplay_type', 'value' => 'default' ],
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Pause on Hover', 'total-theme-core' ),
				'param_name' => 'hover_pause',
				'std' => 'true',
				'dependency' => [ 'element' => 'autoplay_type', 'value' => 'default' ],
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Infinite Loop', 'total-theme-core' ),
				'description' => \esc_html__( 'Enable to loop between slides. Disable to stop the carousel when it reaches the last slide.', 'total-theme-core' ),
				'param_name' => 'infinite_loop',
				'std' => 'true',
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'heading' => \esc_html__( 'Center Item', 'total-theme-core' ),
				'description' => \esc_html__( 'Enable to center the middle slide when displaying slides divisible by 2.', 'total-theme-core' ),
				'param_name' => 'center',
				'std' => 'false',
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_text',
				'input_type' => 'number',
				'heading' => \esc_html__( 'Animation Speed', 'total-theme-core' ),
				'param_name' => 'animation_speed',
				'placeholder' => $carousel_defaults['smartSpeed'] ?? '250',
				'description' => \esc_html__( 'Time it takes to transition between slides.', 'total-theme-core' ),
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
					'label_block' => true,
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'std' => 'false',
				'heading' => \esc_html__( 'Auto Width', 'total-theme-core' ),
				'param_name' => 'auto_width',
				'description' => \esc_html__( 'If enabled the carousel will display items based on their width showing as many as possible.', 'total-theme-core' ),
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_text',
				'input_type' => 'number',
				'heading' => \esc_html__( 'Items To Display', 'total-theme-core' ),
				'param_name' => 'items',
				'placeholder' => $carousel_defaults['items'] ?? '4',
				'dependency' => [ 'element' => 'auto_width', 'value' => 'false' ],
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
					'label_block' => true,
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_ofswitch',
				'std' => 'false',
				'heading' => \esc_html__( 'Auto Height?', 'total-theme-core' ),
				'param_name' => 'auto_height',
				'dependency' => [ 'element' => 'items', 'value' => '1' ],
				'description' => \esc_html__( 'Allows the carousel to change height based on the active item. This setting is used only when you are displaying 1 item per slide.', 'total-theme-core' ),
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_text',
				'input_type' => 'number',
				'heading' => \esc_html__( 'Items To Scrollby', 'total-theme-core' ),
				'param_name' => 'items_scroll',
				'placeholder' => $carousel_defaults['slideBy'] ?? '1',
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
					'label_block' => true,
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_text',
				'input_type' => 'number',
				'heading' => \esc_html__( 'Tablet: Items To Display', 'total-theme-core' ),
				'param_name' => 'tablet_items',
				'placeholder' => $carousel_defaults['responsive']['768']['items'] ?? '3',
				'dependency' => [ 'element' => 'auto_width', 'value' => 'false' ],
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
					'label_block' => true,
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_text',
				'input_type' => 'number',
				'heading' => \esc_html__( 'Mobile Landscape: Items To Display', 'total-theme-core' ),
				'param_name' => 'mobile_landscape_items',
				'placeholder' => $carousel_defaults['responsive']['480']['items'] ?? '2',
				'dependency' => [ 'element' => 'auto_width', 'value' => 'false' ],
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
					'label_block' => true,
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_text',
				'input_type' => 'number',
				'heading' => \esc_html__( 'Mobile Portrait: Items To Display', 'total-theme-core' ),
				'param_name' => 'mobile_portrait_items',
				'placeholder' => $carousel_defaults['responsive']['0']['items'] ?? '1',
				'dependency' => [ 'element' => 'auto_width', 'value' => 'false' ],
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
					'label_block' => true,
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
			[
				'type' => 'vcex_text',
				'input_type' => 'number',
				'heading' => \esc_html__( 'Margin Between Items', 'total-theme-core' ),
				'description' => vcex_shortcode_param_description( 'px' ),
				'param_name' => 'items_margin',
				'placeholder' => $carousel_defaults['margin'] ?? '15',
				'elementor' => [
					'group' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
					'label_block' => true,
				],
				'editors' => [ 'wpbakery', 'elementor' ],
			],
		];

		if ( $dependency ) {
			foreach ( $params as $key => $value ) {
				if ( empty( $params[ $key ]['dependency'] ) ) {
					$params[ $key ]['dependency'] = $dependency;
				}
			}
		}

		if ( $group ) {
			foreach ( $params as $key => $value ) {
				$params[ $key ]['group'] = $group;
			}
		}

		return $params;
	}

	/**
	 * Return carousel default settings.
	 */
	public static function get_default_settings() {
		$has_classic_styles = vcex_has_classic_styles();

		$settings = [
			'nav'                  => 'true',
			'dots'                 => 'false',
			'autoplay'             => 'false',
			'lazyLoad'             => 'false',
			'loop'                 => 'true',
			'autoplayHoverPause'   => 'true',
			'center'               => 'false',
			'smartSpeed'           => '250',
			'slideBy'              => '1',
			'autoplayTimeout'      => '5000',
			'margin'               => '15',
			'items'                => '4',
			'autoHeight'           => 'false',
			'autoWidth'            => 'false',
			'slideTransition'      => '',
			'rtl'                  => is_rtl(),
			'navClass'             => [
				$has_classic_styles ? 'wpex-carousel__arrow wpex-carousel__arrow--prev owl-nav__btn owl-prev theme-button' : 'wpex-carousel__arrow wpex-carousel__arrow--prev theme-button',
				$has_classic_styles ? 'wpex-carousel__arrow wpex-carousel__arrow--next owl-nav__btn owl-next theme-button' : 'wpex-carousel__arrow wpex-carousel__arrow--next theme-button',
			],
			'responsive'           => [
				'0' => [
					'items' => '1',
				],
				'480' => [
					'items' => '2',
				],
				'768' => [
					'items' => '3',
				],
				'960' => [
					'items' => '4',
				],
			],
			'prevIcon'             => self::get_prev_icon(),
			'nextIcon'             => self::get_next_icon(),
			'i18n'                 => [
				'next'             => \esc_html__( 'Next', 'total-theme-core' ),
				'prev'             => \esc_html__( 'Previous', 'total-theme-core' ),
				'go_to_slide'      => \esc_html__( 'Go to slide', 'total-theme-core' ),
				'nav_esc'          => \esc_html__( 'Press escape to go to the first slide', 'total-theme-core' ),
				'instructions'     => \esc_html__( 'Use the left and right arrow keys to access the carousel navigation buttons', 'total-theme-core' ),
			],
		];

		$settings = \apply_filters( 'vcex_carousel_default_settings', $settings ); // @deprecated
		$settings = \apply_filters( 'totalthemecore/vcex/carousel/owl/default_options', $settings );

		return self::parse_booleans( (array) $settings );
	}

	/**
	 * Return carousel settings.
	 *
	 */
	public static function get_settings( $atts, $shortcode ) {
		$defaults = self::get_default_settings();
		$margin   = $defaults['margin'] ?? '15';
		$slideby  = $settings['slideBy'] ?? 1;

		$settings = [
			'nav'                => self::parse_setting_bool( 'arrows', $atts, $defaults['nav'] ?? 'true' ),
			'dots'               => self::parse_setting_bool( 'dots', $atts, $defaults['dots'] ?? 'false' ),
			'autoplay'           => self::parse_setting_bool( 'auto_play', $atts, $defaults['autoplay'] ?? 'false' ),
			'loop'               => self::parse_setting_bool( 'infinite_loop', $atts, $defaults['loop'] ?? 'true' ),
			'center'             => self::parse_setting_bool( 'center', $atts, $defaults['center'] ?? 'false' ),
			'autoplayHoverPause' => self::parse_setting_bool( 'hover_pause', $atts, $defaults['autoplayHoverPause'] ?? 'true' ),
			// @important - We need to define all these settings so the inline CSS works correctly on load.
			'margin'             => ( ! empty( $atts['items_margin'] ) || '0' === $atts['items_margin'] ) ? \absint( $atts['items_margin'] ) : $margin,
			'slideBy'            => ! empty( $atts['items_scroll'] ) ? \absint( $atts['items_scroll'] ) : $slideby,
			'items'              => ! empty( $atts['items'] ) ? \absint( $atts['items'] ) : '4',
			'responsive'         => $defaults['responsive'] ?? [],
		];

		if ( ! empty( $atts['animation_speed'] ) || '0' === $atts['animation_speed'] ) {
			$settings['smartSpeed'] = \floatval( $atts['animation_speed'] );
		}

		if ( ! empty( $atts['timeout_duration'] ) ) {
			$settings['autoplayTimeout'] = \absint( $atts['timeout_duration'] );
		}

		if ( ! empty( $atts['mobile_portrait_items'] ) ) {
			$settings['responsive']['0']['items'] = \absint( $atts['mobile_portrait_items'] );
		}

		if ( ! empty( $atts['mobile_landscape_items'] ) ) {
			$settings['responsive']['480']['items'] = \absint( $atts['mobile_landscape_items'] );
		}

		if ( ! empty( $atts['tablet_items'] ) ) {
			$settings['responsive']['768']['items'] = \absint( $atts['tablet_items'] );
		}

		if ( ! empty( $atts['items'] ) ) {
			$settings['responsive']['960']['items'] = \absint( $atts['items'] );
		}

		if ( ! empty( $atts['style'] ) && $atts['style'] == 'no-margins' ) {
			$settings['margin'] = 0;
		}

		if ( ! empty( $atts['auto_width'] ) && 1 !== $settings['items'] ) {
			$settings['autoWidth'] = self::parse_setting_bool( 'auto_width', $atts );
		}

		if ( ! empty( $atts['auto_height'] ) ) {
			$settings['autoHeight'] = self::parse_setting_bool( 'auto_height', $atts );
		}

		if ( ! empty( $atts['animation_transition'] ) && \in_array( $atts['animation_transition'], [ 'ease-in', 'ease-out', 'linear' ], true ) ) {
			$settings['slideTransition'] = \sanitize_text_field( $atts['animation_transition'] );
		}

		if ( 1 === (int) $settings['items'] && ! empty( $atts['out_animation'] ) ) {
			$settings['animateOut'] = \sanitize_text_field( $atts['out_animation'] );
		}

		// Smooth auto play.
		if ( isset( $atts['autoplay_type'] ) && 'smooth' === $atts['autoplay_type'] ) {
			if ( isset( $settings['smartSpeed'] ) ) {
				$settings['autoplayTimeout'] = $settings['smartSpeed'];
			} else {
				$settings['autoplayTimeout'] = $settings['smartSpeed'] = $defaults['autoplayTimeout'] ?? 5000;
			}
			$settings['slideTransition']    = 'linear';
			$settings['autoplayDelay']      = false;
			$settings['mouseDrag']          = false;
			$settings['touchDrag']          = false;
			$settings['autoplayHoverPause'] = false;
			$settings['nav']                = false;
			$settings['dots']               = false;
		}
		
		$settings = \apply_filters( 'vcex_get_carousel_settings', $settings, $atts, $shortcode ); // @deprecated
		$settings = \apply_filters( 'vcex_carousel_settings', $settings, $atts, $shortcode ); // @deprecated
		$settings = \apply_filters( 'totalthemecore/vcex/carousel/owl/options', $settings, $atts, $shortcode );

		return self::parse_settings( (array) $settings );
	}

	/**
	 * Parses a boolean setting to make sure it's on/off.
	 */
	protected static function parse_setting_bool( $key = '', $atts = [], $default = '' ) {
		if ( isset( $atts['is_elementor_widget'] )
			&& true === $atts['is_elementor_widget']
			&& isset( $atts[ $key ] )
			&& '' === $atts[ $key ]
		) {
			return 'false';
		}
		if ( empty( $atts[ $key ] ) || '' === $atts[ $key ] ) {
			return $default;
		}
		return $atts[ $key ];
	}

	/**
	 * Parses booleans to ensure they are strings.
	 */
	protected static function parse_booleans( $settings = [] ) {
		if ( \is_array( $settings ) ) {
			foreach ( $settings as $key => $val ) {
				if ( \is_bool( $val ) ) {
					$settings[ $key ] = $val ? 'true' : 'false'; // settings must be strings and not actual booleans.
				}
			}
		}
		return $settings;
	}

	/**
	 * Parses settings array.
	 */
	protected static function parse_settings( $settings = [] ) {
		$settings = self::parse_booleans( $settings );

		// Checks deprecated params.
		if ( ! empty( $settings['itemsMobilePortrait'] ) ) {
			$settings['responsive']['0']['items'] = $settings['itemsMobilePortrait'];
			unset( $settings['itemsMobilePortrait'] );
		}

		if ( ! empty( $settings['itemsMobileLandscape'] ) ) {
			$settings['responsive']['480']['items'] = $settings['itemsMobileLandscape'];
			unset( $settings['itemsMobileLandscape'] );
		}

		if ( ! empty( $settings['itemsTablet'] ) ) {
			$settings['responsive']['768']['items'] = $settings['itemsTablet'];
			unset( $settings['itemsTablet'] );
		}

		return $settings;
	}

	/**
	 * Return carousel settings in json format.
	 */
	public static function get_settings_json( $atts, $shortcode ) {
		return self::to_json( self::get_settings( $atts, $shortcode ) );
	}

	/**
	 * Loops through array to remove duplicates and leaves only different values.
	 */
	protected static function remove_array_dups( $new_settings = [], $defaults = [] ) {
		foreach ( $new_settings as $setting_k => $setting_v ) {
			if ( isset( $defaults[$setting_k] )
				&& ! \is_array( $setting_v ) // don't mess with the arrays because it breaks the responsiveness.
				&& ( $defaults[$setting_k] === $setting_v )
			) {
				unset( $new_settings[$setting_k] );
			}
		}
		return $new_settings;
	}

	/**
	 * Parses settings array and converts to json.
	 */
	public static function to_json( $settings = [] ) {
		if ( ! $settings ) {
			return;
		}
		$defaults = self::get_default_settings();
		$settings = self::remove_array_dups( $settings, $defaults );
		if ( $settings ) {
			return \esc_attr( \wp_json_encode( $settings ) );
		}
	}

	/**
	 * Returns prev icon html.
	 */
	protected static function get_prev_icon() {
		return (string) \vcex_get_theme_icon_html(
			\apply_filters( 'totalthemecore/vcex/carousel/owl/prev_icon_name', 'chevron-left' ),
			'wpex-carousel__arrow-icon wpex-flex',
			'',
			true
		);
	}

	/**
	 * Returns next icon html.
	 */
	protected static function get_next_icon() {
		return (string) \vcex_get_theme_icon_html(
			\apply_filters( 'totalthemecore/vcex/carousel/owl/next_icon_name', 'chevron-right' ),
			'wpex-carousel__arrow-icon wpex-flex',
			'',
			true
		);
	}

	/**
	 * Returns array of out animation choices.
	 */
	public static function get_out_animation_choices(): array {
		return [
			''        => \esc_html__( 'Slide', 'total-theme-core' ),
			'fadeOut' => \esc_html__( 'Fade Out', 'total-theme-core' ),
		];
	}

	/**
	 * Check if we should be replacing the owl classnames.
	 */
	public static function use_owl_classnames(): bool {
		// @todo should be add a check to see if 'owl-carousel' is registered so it's automatic to prevent conflicts?
		// could cause issues with customers updating from pre 6.0
		return (bool) \apply_filters( 'totalthemecore/vcex/carousel/owl/use_owl_classnames', vcex_has_classic_styles() );
	}
}
