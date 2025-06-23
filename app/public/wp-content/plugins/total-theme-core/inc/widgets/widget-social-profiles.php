<?php

namespace TotalThemeCore\Widgets;

\defined( 'ABSPATH' ) || exit;

class Widget_Social_Profiles extends \TotalThemeCore\WidgetBuilder {

	/**
	 * Widget args.
	 */
	private $args;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->args = [
			'id_base' => 'wpex_fontawesome_social_widget',
			'name'    => $this->branding() . \esc_html__( 'Social Links', 'total-theme-core' ),
			'options' => [
				'customize_selective_refresh' => true,
			],
			'fields'  => [
				[
					'id'    => 'title',
					'label' => \esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				],
				[
					'id'      => 'description',
					'label'   => \esc_html__( 'Description', 'total-theme-core' ),
					'type'    => 'textarea',
				],
				[
					'id'      => 'style',
					'label'   => \esc_html__( 'Style', 'total-theme-core' ),
					'type'    => 'select',
					'default' => 'flat-color',
					'choices' => $this->get_social_styles(),
				],
				[
					'id'      => 'align',
					'label'   => \esc_html__( 'Align', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => [
						''       => \esc_html__( 'Default', 'total-theme-core' ),
						'left'   => \esc_html__( 'Left', 'total-theme-core' ),
						'center' => \esc_html__( 'Center', 'total-theme-core' ),
						'right'  => \esc_html__( 'Right', 'total-theme-core' ),
					],
				],
				[
					'id'      => 'target',
					'label'   => \esc_html__( 'Link Target', 'total-theme-core' ),
					'type'    => 'select',
					'default' => 'blank',
					'choices' => [
						'blank' => \esc_html__( 'New Tab', 'total-theme-core' ),
						'self' => \esc_html__( 'Same Tab', 'total-theme-core' ),
					],
				],
				[
					'id'      => 'space_between',
					'label'   => \esc_html__( 'Spacing', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => function_exists( 'wpex_utl_margins' ) ? \wpex_utl_margins() : [],
				],
				[
					'id'          => 'size',
					'label'       => \esc_html__( 'Dimensions', 'total-theme-core' ),
					'type'        => 'text',
					'placeholder' => '40px',
				],
				[
					'id'          => 'font_size',
					'label'       => \esc_html__( 'Size', 'total-theme-core' ),
					'type'        => 'text',
					'placeholder' => '1em',
				],
				[
					'id'          => 'border_radius',
					'label'       => \esc_html__( 'Border Radius', 'total-theme-core' ),
					'type'        => 'text',
					'placeholder' => '4px',
				],
				[
					'id'    => 'nofollow',
					'label' => \esc_html__( 'Nofollow?', 'total-theme-core' ),
					'type'  => 'checkbox',
				],
				[
					'id'    => 'expand',
					'label' => \esc_html__( 'Expand items to fit the widget area?', 'total-theme-core' ),
					'type'  => 'checkbox',
				],
				[
					'id'     => 'social_profiles',
					'label'  => \esc_html__( 'Profiles', 'total-theme-core' ),
					'type'   => 'repeater',
					'fields' => [
						[
							'id'      => 'site',
							'label'   => \esc_html__( 'Site', 'total-theme-core' ),
							'type'    => 'select',
							'choices' => $this->get_social_choices(),
						],
						[
							'id'       => 'url',
							'label'    => \esc_html__( 'URL', 'total-theme-core' ),
							'type'     => 'text',
							'sanitize' => 'url',
						],
					],
				],
			],
		];

		$this->create_widget( $this->args );
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		$instance        = $this->parse_instance( $instance );
		$social_profiles = $this->get_social_profiles( $instance );

		// Return if no services defined.
		if ( ! $social_profiles ) {
			return;
		}

		// Before widget hook.
		echo \wp_kses_post( $args[ 'before_widget' ] );

		// Display widget title.
		$this->widget_title( $args, $instance );

		// Define vars.
		$output = '';
		$is_legacy_preview = $this->is_legacy_preview_view_mode();
		$type = $instance['type'] ?? ''; // deprecated
		$style = $this->parse_style( $instance['style'], $type ); // Fallback for OLD styles pre-1.0.0
		$space_between = ! empty( $instance['space_between'] ) ? \absint( $instance['space_between'] ) : '5';
		$nofollow = $instance['nofollow'] ? \wp_validate_boolean( $instance['nofollow'] ) : false;
		$expand = $instance['expand'] ? \wp_validate_boolean( $instance['expand'] ) : false;
		$size = $instance['size'] ? \ttc_sanitize_data( $instance['size'], 'px' ) : '';
		$font_size = $instance['font_size'] ? \ttc_sanitize_data( $instance['font_size'], 'font_size' ) : '';
		$border_radius = $instance['border_radius'] ? \ttc_sanitize_data( $instance['border_radius'], 'border_radius' ) : '';
		$link_target = $instance['target'] ?? '';

		// Get align class.
		$allowed_aligns = [ 'left', 'center', 'right' ];

		if ( ! empty( $instance['align'] ) && in_array( $instance['align'], $allowed_aligns ) ) {
			$align = ' text' . $instance['align'];
		} else {
			$align = '';
		}

		// Inline CSS
		$inline_css = '';

		if ( $font_size ) {
			$inline_css .= 'font-size:' . \esc_attr( $font_size ) . ';';
		}

		if ( $size ) {
			$inline_css .= 'height:' . \esc_attr( $size ) . ';';
			if ( ! $expand ) {
				$inline_css .= 'width:' . \esc_attr( $size ) . ';';
			}
		}

		if ( $border_radius ) {
			if ( $is_legacy_preview ) {
				$inline_css .= 'border-radius:' . \esc_attr( $border_radius ) . ' !important;';
			} else {
				$inline_css .= 'border-radius:' . \esc_attr( $border_radius ) . ';';
			}
		}

		if ( $inline_css ) {
			$css_target = $is_legacy_preview ? '.wpex-social-btn' : '#' . \esc_attr( $this->id ) .' .wpex-social-btn';
			$output .= '<style>' . $css_target . '{' . \esc_attr( $inline_css ) . '}' . '</style>';
		}

		// Begin widget output.
		$output .= '<div class="wpex-fa-social-widget' . \esc_attr( $align ) . '">';

			// Description.
			if ( ! empty( $instance['description'] ) ) :

				$output .= '<div class="desc wpex-last-mb-0 wpex-mb-20 wpex-clr">';
					$output .= \wp_kses_post( $instance['description'] );
				$output .= '</div>';

			endif;

			$ul_class = 'wpex-list-none wpex-m-0 wpex-last-mr-0 wpex-text-md';

			if ( $expand ) {
				$ul_class .= ' wpex-flex wpex-flex-wrap';
			}

			$output .= '<ul class="' . \esc_attr( $ul_class ) . '">';

				// Loop through each item in the array.
				foreach ( $social_profiles as $profile ) :

					if ( empty( $profile['site'] ) || empty( $profile['label'] ) || empty( $profile['url'] ) ) {
						continue;
					}

					$link = ! empty( $profile['url'] ) ? \do_shortcode( $profile['url'] ) : null;

					if ( ! $link ) {
						continue;
					}

					if ( str_contains( $link, '@' ) ) {
						if ( str_starts_with( $link, 'mailto:' ) ) {
							$link = 'mailto:' . antispambot( str_replace( 'mailto:', '', wp_strip_all_tags( $link ) ) );
						} elseif ( is_email( $link ) ) {
							$link = 'mailto:' . antispambot( $link );
						}
					}

					$site = $profile['site'];

					$a_attrs = [
						'href'   => \esc_url( $link ),
						'class'  => $this->get_social_brand_classname( $site ),
						'rel'    => $nofollow ? 'nofollow' : '',
						'target' => $link_target,
					];

					if ( \function_exists( '\wpex_get_social_button_class' ) ) {
						$a_attrs['class'] .= ' ' . \esc_attr( \wpex_get_social_button_class( $style ) );
					}

					$li_class = 'wpex-inline-block wpex-mb-' . $space_between . ' wpex-mr-' . $space_between;

					if ( $expand ) {
						$li_class .= ' wpex-flex-grow';
						$a_attrs['class'] .= ' wpex-w-100';
					}

					$output .= '<li class="' . \esc_attr ( $li_class ) . '">';

						$output .= '<a';

							if ( \function_exists( '\wpex_parse_attrs' ) ) {
								$output .= ' ' . \wpex_parse_attrs( $a_attrs );
							} else {
								foreach ( $a_attrs as $attr_k => $attr_v ) {
									$output .= ' ' . $attr_k . '=' . '"' . \esc_attr( $attr_v ) . '"';
								}
							}

						$output .= '>';

							if ( \function_exists( '\totaltheme_get_icon' ) ) {
								$output .= \totaltheme_get_icon( $profile['icon'] );
							}
							
							if ( ! empty( $profile['label'] ) ) {
								$output .= '<span class="screen-reader-text">' . \esc_html( $profile['label'] ) . '</span>';
							}

						$output .= '</a>';

					$output .= '</li>';

				endforeach;

			$output .= '</ul>';

		$output .= '</div>';

		// @codingStandardsIgnoreLine
		echo $output;

		// After widget hook.
		echo \wp_kses_post( $args[ 'after_widget' ] );
	}

	/**
	 * Return social profiles.
	 */
	protected function get_social_profiles( $instance ) {
		$profiles = [];
		$choices  = $this->get_social_profile_options();

		if ( ! empty( $instance['social_services'] ) && empty( $instance['social_profiles'] ) ) {
			foreach ( $instance['social_services'] as $service => $settings ) {
				if ( empty( $settings['url'] ) ) {
					continue;
				}
				if ( 'vimeo-square' === $service ) {
					$service = 'vimeo';
				}
				$profiles[] = [
					'site' => $service,
					'url'  => $settings['url'],
				];
			}

		} elseif ( ! empty( $instance['social_profiles'] ) ) {
			$profiles = $instance['social_profiles'];
		}

		foreach ( $profiles as $profile_k => $profile_v ) {
			$site = $profile_v['site'] ?? null;
			if ( empty( $site ) || ! \array_key_exists( $site, $choices ) ) {
				unset( $profiles[ $profile_k ] );
			} else {
				$profiles[ $profile_k ]['label'] = $choices[ $site ]['name'] ?? $choices[ $site ]['label'] ?? $site;
				$profiles[ $profile_k ]['icon']  = $choices[ $site ]['icon'] ?? $choices[ $site ]['icon_class'] ?? $site;
			}
		}

		return $profiles;
	}

	/**
	 * Return social styles list.
	 */
	protected function get_social_styles() {
		return function_exists( 'wpex_social_button_styles' ) ? \wpex_social_button_styles() : [];
	}

	/**
	 * Returns social choices.
	 */
	protected function get_social_choices(): array {
		$choices = [
			'' => \esc_html__( '— Select —', 'total-theme-core' ),
		];
		foreach ( $this->get_social_profile_options() as $option_k => $option_v ) {
			$choices[ $option_k ] = $option_v['name'] ?? $option_v['label'] ?? $option_k;
		}
		return $choices;
	}

	/**
	 * Returns social profiles list.
	 */
	protected function get_social_profile_options(): array {
		$options = [];

		if ( \function_exists( 'wpex_social_profile_options_list' ) ) {
			$options = \wpex_social_profile_options_list();
		}

		/**
		 * Filters the list of available options for the social profiles widget.
		 *
		 * @deprecated 1.4.9
		 */
		$old_filter = (array) \apply_filters( 'wpex_social_widget_profiles', [] );

		if ( ! empty( $old_filter ) && is_array( $old_filter ) ) {
			foreach ( $old_filter as $k => $v ) {
				$options[ $k ] = [
					'label' => $v['name'] ?? $v['label'] ?? '',
					'icon'  => $v['icon'] ?? $v['icon_class'] ?? $k,
				];
			}
		}

		return $options;
	}

	/**
	 * Parses style attribute for fallback styles.
	 */
	protected function parse_style( $style = '', $type = '' ) {
		if ( 'color' === $style && 'flat' === $type ) {
			return 'flat-color';
		} elseif ( 'color' === $style && 'graphical' === $type ) {
			return 'graphical-rounded';
		} elseif ( 'black' === $style && 'flat' === $type ) {
			return 'black-rounded';
		} elseif ( 'black' === $style && 'graphical' === $type ) {
			return 'black-rounded';
		} elseif ( 'black-color-hover' === $style && 'flat' === $type ) {
			return 'black-ch-rounded';
		} elseif ( 'black-color-hover' === $style && 'graphical' === $type ) {
			return 'black-ch-rounded';
		}
		return $style;
	}

	/**
	 * Returns correct brand classname.
	 */
	protected function get_social_brand_classname( $key = '' ) {
		switch ( $key ) {
			case 'vimeo-square':
				$key = 'vimeo';
				break;
		}
		return 'wpex-' . \sanitize_html_class( $key );
	}

}

register_widget( 'TotalThemeCore\Widgets\Widget_Social_Profiles' );
