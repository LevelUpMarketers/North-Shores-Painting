<?php

/**
 * vcex_pricing shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

extract( $atts );

$style = ! empty( $atts['style'] ) ? sanitize_text_field( $atts['style'] ) : 'default';
$css_animation = ! empty( $atts['css_animation'] ) ? sanitize_text_field( $atts['css_animation'] ) : '';
$is_featured = vcex_validate_att_boolean( 'featured', $atts );

// Define output var.
$output = '';

// Define pricing item classes.
$class = [
	'vcex-module',
	'vcex-pricing',
	'wpex-flex',
	'wpex-flex-col',
	"vcex-pricing-style-{$style}",
	'wpex-leading-normal',
];

switch ( $style ) {
	case 'default':
		$class[] = 'wpex-surface-1';
		break;
	case 'alt-1':
		$class[] = 'wpex-surface-1';
		$class[] = 'wpex-p-40';
		$class[] = 'wpex-text-center';
		$class[] = 'wpex-border-solid';
		if ( $is_featured ) {
			$class[] = 'wpex-border-2';
			$class[] = 'wpex-border-accent';
		} else {
			$class[] = 'wpex-border';
			$class[] = 'wpex-border-main';
		}
		$class[] = 'wpex-last-mb-0';
		break;
	case 'alt-2':
		$class[] = 'wpex-surface-1';
		$class[] = 'wpex-p-20';
		$class[] = 'wpex-border-solid';
		if ( $is_featured ) {
			$class[] = 'wpex-border-2';
			$class[] = 'wpex-border-accent';
		} else {
			$class[] = 'wpex-border';
			$class[] = 'wpex-border-main';
		}
		$class[] = 'wpex-last-mb-0';
		break;
	case 'alt-3':
		$class[] = 'wpex-p-25';
		$class[] = 'wpex-text-center';
		if ( $is_featured ) {
			$class[] = 'wpex-bg-accent';
		} else {
			if ( empty( $atts['el_class'] ) || ! str_contains( (string) $atts['el_class'], 'wpex-surface-' ) ) {
				$class[] = 'wpex-surface-2';
			}
			$class[] = 'wpex-text-1';
		}
		$class[] = 'wpex-last-mb-0';
		break;
}

if ( $is_featured ) {
	$class[] = 'featured';
}

if ( ! empty( $atts['shadow'] ) ) {
	$class[] = vcex_parse_shadow_class( $atts['shadow'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['el_class'] ) ) {
	$class[] = vcex_get_extra_class( $atts['el_class'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['hover_animation'] ) ) {
	$class[] = vcex_hover_animation_class( $atts['hover_animation'] );
}

if ( $css ) {
	$class[] = vcex_vc_shortcode_custom_css_class( $css );
}

$class = vcex_parse_shortcode_classes( $class, 'vcex_pricing', $atts );

$wrap_attrs = [
	'id'    => ! empty( $atts['unique_id'] ) ? $atts['unique_id'] : null,
	'class' => $class
];

/*-----------------------------------------------------*/
/* [ Begin output ]
/*-----------------------------------------------------*/
if ( $css_animation && 'none' !== $css_animation ) {

	$css_animation_style = vcex_inline_style( [
		'animation_delay' => $atts['animation_delay'],
		'animation_duration' => $atts['animation_duration'],
	] );

	$animation_classes = [ trim( vcex_get_css_animation( $css_animation ) ) ];

	if ( ! empty( $atts['visibility'] ) ) {
		$animation_classes[] = vcex_parse_visibility_class( $atts['visibility'] );
	}

	$output .= '<div class="' . esc_attr( implode( ' ', $animation_classes ) ) . '"' . $css_animation_style . '>';
}

$output .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	/*-----------------------------------------------------*/
	/* [ Plan ]
	/*-----------------------------------------------------*/
	if ( $plan ) {

		$plan_class = [
			'vcex-pricing-plan',
			'vcex-pricing-header', // legacy class pre v5
		];

		switch ( $style ) {
			case 'alt-1':
				$plan_class[] = 'wpex-font-medium';
				$plan_class[] = 'wpex-text-3xl';
				$plan_class[] = 'wpex-text-1';
				break;
			case 'alt-2';
				$plan_class[] = 'wpex-font-bold';
				$plan_class[] = 'wpex-text-2xl';
				$plan_class[] = 'wpex-text-1';
				$plan_class[] = 'wpex-border-b-3';
				$plan_class[] = 'wpex-border-solid';
				$plan_class[] = 'wpex-border-accent';
				$plan_class[] = 'wpex-mb-10';
				$plan_class[] = 'wpex-pb-10';
				break;
			case 'alt-3':
				break;
			case 'default':
				if ( $is_featured ) {
					$plan_class[] = 'wpex-bg-accent';
					$plan_class[] = 'wpex-border-transparent';
				} else {
					$plan_class[] = 'wpex-surface-3';
					$plan_class[] = 'wpex-text-1';
					$plan_class[] = 'wpex-border-surface-4';
				}
				$plan_class[] = 'wpex-border';
				$plan_class[] = 'wpex-border-solid';
				$plan_class[] = 'wpex-py-15';
				$plan_class[] = 'wpex-px-20';
				$plan_class[] = 'wpex-text-center';
				$plan_class[] = 'wpex-uppercase';
				$plan_class[] = 'wpex-bold';
				break;
		}

		// Filter plan classes
		$plan_class = apply_filters( 'vcex_pricing_plan_class', $plan_class, $atts );

		// Display pricing plan
		$output .= '<div class="' . esc_attr( implode( ' ', $plan_class ) ) . '">' . vcex_parse_text_safe( $plan ) . '</div>';

	}

	/*-----------------------------------------------------*/
	/* [ Cost ]
	/*-----------------------------------------------------*/
	if ( $cost ) {

		// Set default cost classes
		$cost_class = [
			'vcex-pricing-cost',
		];

		// Custom priing style utility classes
		switch ( $style ) {
			case 'alt-1':
				$cost_class[] = 'wpex-text-accent';
				$cost_class[] = 'wpex-text-2xl';
				$cost_class[] = 'wpex-font-medium';
				$cost_class[] = 'wpex-mb-25';
				$cost_class[] = 'wpex-leading-normal';
				break;
			case 'alt-2':
				$cost_class[] = 'wpex-text-2xl';
				$cost_class[] = 'wpex-text-1';
				$cost_class[] = 'wpex-font-medium';
				$cost_class[] = 'wpex-border-b';
				$cost_class[] = 'wpex-border-solid';
				$cost_class[] = 'wpex-border-main';
				$cost_class[] = 'wpex-mb-20';
				$cost_class[] = 'wpex-pb-30';
				break;
			case 'alt-3':
				$cost_class[] = 'wpex-text-5xl';
				$cost_class[] = 'wpex-font-bold';
				if ( ! $is_featured ) {
					$cost_class[] = 'wpex-text-1';
				}
				break;
			case 'default':
				$cost_class[] = 'wpex-surface-2';
				$cost_class[] = 'wpex-p-20';
				$cost_class[] = 'wpex-border-x';
				$cost_class[] = 'wpex-border-solid';
				$cost_class[] = 'wpex-border-surface-4';
				$cost_class[] = 'wpex-text-center';
				break;
		}

		$cost_class = apply_filters( 'vcex_pricing_cost_class', $cost_class, $atts );

		// Display cost element
		$output .= '<div class="' . esc_attr( implode( ' ', $cost_class ) ) . '">';

			/*-----------------------------------------------------*/
			/* [ Amount ]
			/*-----------------------------------------------------*/
			$amount_class = [
				'vcex-pricing-ammount', // yes I know it has a typo
			];

			switch ( $style ) {
				case 'default':
					$amount_class[] = 'wpex-text-6xl';
					$amount_class[] = 'wpex-leading-tight';
					$amount_class[] = 'wpex-font-light';
					break;
			}

			$amount_class = apply_filters( 'vcex_pricing_amount_class', $amount_class, $atts );

			// Display amount
			$output .= '<span class="' . esc_attr( implode( ' ', $amount_class ) ) . '">' . vcex_parse_text_safe( $cost ) . '</span>';

			/*-----------------------------------------------------*/
			/* [ Per ]
			/*-----------------------------------------------------*/
			if ( $per ) {

				$per_class = [
					'vcex-pricing-per',
				];

				switch ( $style ) {
					case 'default':
						$per_class[] = 'wpex-text-sm';
						$per_class[] = 'wpex-text-4';
						$per_class[] = 'wpex-leading-none';
						break;
					case 'alt-2':
						$per_class[] = 'wpex-text-xs';
						$per_class[] = 'wpex-font-normal';
						$per_class[] = 'wpex-text-3';
						$per_class[] = 'wpex-ml-5';
						break;
					case 'alt-3':
					$per_class[] = 'wpex-text-xs';
						break;
				}

				if ( 'block' === $per_display ) {
					$per_class[] = 'wpex-mt-10';
				}

				$per_class = apply_filters( 'vcex_pricing_per_class', $per_class, $atts );

				// Display per
				$output .= '<span class="' . esc_attr( implode( ' ', $per_class ) ) . '">' . vcex_parse_text_safe( $per ) . '</span>';

			}

		$output .= '</div>';

	}

	/*-----------------------------------------------------*/
	/* [ Features ]
	/*-----------------------------------------------------*/
	if ( $content ) {

		$content_class = [
			'vcex-pricing-content',
			'wpex-flex-grow',
			'wpex-last-mb-0',
			'wpex-clr',
		];

		switch ( $style ) {
			case 'alt-1':
				$content_class[] = 'wpex-mb-25';
				break;
			case 'alt-2':
				$content_class[] = 'wpex-my-20';
				break;
			case 'alt-3':
				$content_class[] = 'wpex-my-20';
				break;
			case 'default':
				$content_class[] = 'wpex-text-center';
				$content_class[] = 'wpex-p-20';
				$content_class[] = 'wpex-border';
				$content_class[] = 'wpex-border-solid';
				$content_class[] = 'wpex-border-surface-4';
				break;
		}

		$content_class = apply_filters( 'vcex_pricing_content_class', $content_class, $atts );

		// Display features
		$output .= '<div class="' . esc_attr( implode( ' ', $content_class ) ) . '">' . vcex_the_content( $content ) . '</div>';
	}

	/*-----------------------------------------------------*/
	/* [ Button ]
	/*-----------------------------------------------------*/
	if ( $button_url && ! $custom_button ) {
		$button_url_temp = $button_url; // fallback for old option
		$button_url = vcex_get_link_data( 'url', $button_url_temp );
	}

	if ( $button_url || $custom_button ) {

		$button_wrap_class = [
			'vcex-pricing-button',
			'wpex-mt-auto',
		];

		switch ( $style ) {
			case 'alt-1':
				break;
			case 'default':
				$button_wrap_class[] = 'wpex-p-20';
				$button_wrap_class[] = 'wpex-border';
				$button_wrap_class[] = 'wpex-border-t-0';
				$button_wrap_class[] = 'wpex-border-solid';
				$button_wrap_class[] = 'wpex-border-surface-4';
				$button_wrap_class[] = 'wpex-text-center';
				break;
		}

		$button_wrap_class = apply_filters( 'vcex_pricing_button_class', $button_wrap_class, $atts );

		$button_url = $custom_button ? false : $button_url; // Set button url to false if custom_button isn't empty.

		if ( $button_url || $custom_button ) {

			$output .= '<div class="' . esc_attr( implode( ' ', $button_wrap_class ) ) . '">';

				/**
				 * Custom button.
				 */
				if ( $custom_button = vcex_parse_textarea_html( $custom_button ) ) {
					$output .= vcex_parse_text( $custom_button );
				}

				/**
				 * Theme button.
				 */
				elseif ( $button_url ) {

					if ( ! $button_style_color && 'alt-3' === $style && $is_featured ) {
						$button_style_color = 'white';
					}

					$button_title  = vcex_get_link_data( 'title', $button_url_temp );
					$button_target = vcex_get_link_data( 'target', $button_url_temp );
					$button_rel    = vcex_get_link_data( 'rel', $button_url_temp );
					$button_class  = [
						'vcex-pricing-button__link',
						vcex_get_button_classes( $button_style, $button_style_color ),
					];

					if ( 'default' !== $style ) {
						if ( vcex_has_classic_styles() ) {
							$button_class[] = 'wpex-p-10';
						}
						$button_class[] = 'wpex-w-100';
						$button_class[] = 'wpex-text-center';
					}

					switch ( $style ) {
						case 'alt-1':
							$button_class[] = 'wpex-rounded-full';
							break;
						case 'alt-3':
							$button_class[] = 'wpex-font-bold';
							break;
					}

					// Custom Button Classes.
					if ( 'true' == $button_local_scroll ) {
						$button_class[] = 'local-scroll-link';
					}

					if ( ! empty( $atts['button_transform'] ) ) {
						$button_class[] = vcex_parse_text_transform_class( $atts['button_transform'] );
					}

					// Define button attributes.
					$button_attrs = [
						'href'   => esc_url( do_shortcode( $button_url ) ),
						'title'  => esc_attr( do_shortcode( $button_title ) ),
						'target' => $button_target,
						'rel'    => $button_rel,
						'class'  => $button_class
					];

					$output .= '<a' . vcex_parse_html_attributes( $button_attrs ) . '>';

						// Button Icon Left.
						if ( $button_icon_left = vcex_get_icon_html( $atts, 'button_icon_left' ) ) {
							$button_icon_left_class = 'vcex-icon-wrap theme-button-icon-left';
							if ( ! empty( $atts['button_icon_left_transform'] ) ) {
								$button_icon_left_class .= ' theme-button-icon-animate-h';
							}
							$output .= '<span class="' . esc_attr( $button_icon_left_class ) . '">' . $button_icon_left . '</span>';
						}

						// Button text
						$output .= vcex_parse_text_safe( $button_text );

						// Button Icon Right
						if ( $button_icon_right = vcex_get_icon_html( $atts, 'button_icon_right' ) ) {
							$button_icon_right_class = 'vcex-icon-wrap theme-button-icon-right';
							if ( ! empty( $atts['button_icon_right_transform'] ) ) {
								$button_icon_right_class .= ' theme-button-icon-animate-h';
							}
							$output .= '<span class="' . esc_attr( $button_icon_right_class ) . '">' . $button_icon_right . '</span>';

						}

					$output .= '</a>';

				}

			$output .= '</div>';

		}

	} // End button checks.

$output .= '</div>';

if ( $css_animation && 'none' !== $css_animation ) {
	$output .= '</div>';
}

// @codingStandardsIgnoreLine
echo $output;
