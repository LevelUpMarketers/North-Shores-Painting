<?php

/**
 * vcex_skillbar shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $atts['percentage'] ) && 0 !== $atts['percentage'] ) {
	return;
}

// Define vars.
$output              = '';
$percent_intval      = intval( sanitize_text_field( $atts['percentage'] ) );
$style               = ! empty( $atts['style'] ) ? sanitize_text_field( $atts['style'] ) : 'default';
$title_safe          = ! empty( $atts['title'] ) ? vcex_parse_text_safe( $atts['title'] ) : '';
$title_position      = ( 'default' === $style ) ? 'inside' : 'outside';
$animate_percent     = vcex_validate_att_boolean( 'animate_percent', $atts, true );
$border_radius_class = ! empty( $atts['border_radius'] ) ? vcex_parse_border_radius_class( $atts['border_radius'] ) : '';
$has_icon            = false;

// Classes.
$wrap_class = [
	'vcex-module',
	'vcex-skillbar-wrap',
	'wpex-mb-10',
];

$wrap_class[] = 'vcex-skillbar-style-' . sanitize_html_class( $style );

if ( ! empty( $atts['visibility'] ) ) {
    $wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['classes'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['classes'] );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_skillbar', $atts );

// Start shortcode output.
$output .= '<div class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $atts ) . '>';

	// Generate icon output if defined.
	if ( vcex_validate_att_boolean( 'show_icon', $atts, false )
		&& $icon_safe = vcex_get_icon_html( $atts, 'icon' )
	) {
		$has_icon    = true;
		$icon_class  = 'vcex-icon-wrap wpex-inline-flex wpex-items-center';
		$icon_margin_safe = ! empty( $atts['icon_margin'] ) ? sanitize_html_class( (string) absint( $atts['icon_margin'] ) ) : '10';
		if ( $icon_margin_safe ) {
			$icon_class .= " wpex-mr-{$icon_margin_safe}";
		}
		$icon_output = '<span class="' . esc_attr( $icon_class ) . '">';
			$icon_output .= $icon_safe;
		$icon_output .= '</span>';
	}

	// Generate percent output.
	if ( vcex_validate_att_boolean( 'show_percent', $atts, true ) ) {
		$percentage_class = [
			'vcex-skill-bar-percent',
			'wpex-absolute',
			'wpex-top-50',
			'-wpex-translate-y-50',
			'wpex-right-0',
		];

		switch ( $title_position ) {
			case 'inside':
				$percentage_class[] = 'wpex-mr-15';
				break;
			case 'outside':
				$percentage_class[] = 'wpex-text-sm';
				$percentage_class[] = 'wpex-top-50';
				$percentage_class[] = '-wpex-translate-y-50';
				$percentage_class[] = 'wpex-mr-10';
				break;
		}

		$percent_output = '<div class="' . esc_attr( implode( ' ', $percentage_class ) ) . '">' . $percent_intval . '&#37;</div>';
	}

	/*
	 * Title (outside of skillbar).
	 */
	if ( 'alt-1' === $style ) {
		$label_class = [
			'vcex-skillbar-title',
			'wpex-font-semibold',
			'wpex-mb-5',
		];

		if ( $has_icon ) {
			$label_class[] = 'wpex-flex';
			$label_class[] = 'wpex-items-center';
		}

		$output .= '<div class="' . esc_attr( implode( ' ', $label_class ) ) . '">';
			if ( ! empty( $icon_output ) ) {
				$output .= $icon_output;
			}
			$output .= $title_safe;
		$output .= '</div>';
	}

	/*
	 * Inner wrap open.
	 *
	 */
	$inner_class = [
		'vcex-skillbar',
	];

	if ( $animate_percent ) {
		$inner_class[] = 'vcex-skillbar--animated';
	}

	$inner_class[] = 'wpex-block';
	$inner_class[] = 'wpex-relative';

	switch ( $title_position ) {
		case 'inside':
			$inner_class[] = 'wpex-surface-2';
			if ( vcex_validate_att_boolean( 'box_shadow', $atts, true ) ) {
		  		$inner_class[] = 'wpex-shadow-inner';
			}
			if ( ! empty( $atts['color'] ) ) {
				$inner_class[] = 'wpex-text-white';
			} else {
				$inner_class[] = 'wpex-text-on-accent';
			}
			break;
		case 'outside':
			$inner_class[] = 'wpex-surface-3';
			$inner_class[] = 'wpex-text-3';
			$inner_class[] = 'wpex-font-semibold';
			break;
	}

	if ( $border_radius_class ) {
		$inner_class[] = $border_radius_class;
		$inner_class[] = 'wpex-overflow-hidden';
	}

	$inner_attrs = [
		'class' => $inner_class,
	];

	if ( $animate_percent )  {
		wp_enqueue_script( 'vcex-skillbar' );
		$inner_attrs['data-percent'] = "{$percent_intval}&#37;";
		if ( vcex_validate_att_boolean( 'animate_percent_onscroll', $atts, false ) ) {
			$inner_attrs['data-animate-on-scroll'] = 'true';
		}
	}

	$output .= '<div' . vcex_parse_html_attributes( $inner_attrs ) . '>';

		/*
		 * Percentage.
		 */
		$bar_class = 'vcex-skillbar-bar wpex-relative wpex-w-0 wpex-h-100 wpex-bg-accent';

		if ( ! empty( $atts['color'] ) ) {
			$bar_class .= ' wpex-text-white';
		}

		if ( $animate_percent ) {
			$bar_class .= ' wpex-transition-width wpex-duration-700';
		}

		if ( $border_radius_class ) {
			$bar_class .= " {$border_radius_class}";
		}

		$output .= '<div class="' . esc_attr( $bar_class ) . '">';
			if ( 'inside' === $title_position && ! empty( $percent_output ) ) {
				$output .= $percent_output;
			}
		$output .= '</div>';

		/*
		 * Title
		 */
		if ( 'inside' === $title_position ) {
			$output .= '<div class="vcex-skillbar-title wpex-absolute wpex-top-50 -wpex-translate-y-50 wpex-left-0">';

				$title_inner_class = [
					'vcex-skillbar-title-inner',
					'wpex-px-15',
				];
				if ( $has_icon ) {
					$title_inner_class[] = 'wpex-flex';
					$title_inner_class[] = 'wpex-items-center';
				}

				$output .= '<div class="' . esc_attr( implode( ' ', $title_inner_class ) ) . '">';

					// Display Icon.
					if ( ! empty( $icon_output ) ) {
						$output .= $icon_output;
					}

					// Title.
					if ( 'default' === $style ) {
						$output .= $title_safe;
					}

				$output .= '</div>';

			$output .= '</div>';
		}

		// Display percent outside of colored background.
		if ( 'outside' === $title_position && ! empty( $percent_output ) ) {
			$output .= $percent_output;
		}

	$output .= '</div>';

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
