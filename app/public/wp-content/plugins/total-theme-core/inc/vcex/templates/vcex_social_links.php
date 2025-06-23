<?php

/**
 * vcex_social_links shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0.2
 */

defined( 'ABSPATH' ) || exit;

if ( function_exists( 'wpex_social_profile_options_list' ) ) {
	$social_profiles = (array) wpex_social_profile_options_list();
}

// Social profile array can't be empty.
if ( empty( $social_profiles ) ) {
	return;
}

// Define main vars.
$html       = '';
$expand     = vcex_validate_att_boolean( 'expand', $atts );
$show_label = vcex_validate_att_boolean( 'show_label', $atts );
$source     = ! empty( $atts['source'] ) ? sanitize_text_field( $atts['source'] ) : 'custom';
$style      = ! empty( $atts['style'] ) ? sanitize_text_field( $atts['style']) : 'flat';

switch ( $source ) {
	case 'post_author':
		$post_tmp = get_post( vcex_get_the_ID() );
		$post_author = $post_tmp->post_author;
		if ( ! $post_author ) {
			return;
		}
		$loop = [];
		$social_settings = wpex_get_user_social_profile_settings_array();
		foreach ( $social_settings as $id => $label ) {
			if ( $url = get_the_author_meta( "wpex_{$id}", $post_author ) ) {
				$loop[ $id ] = $url;
			}
		}
		$post_tmp = '';
		break;
	case 'staff_member':
		$loop = [];
		if ( function_exists( 'wpex_staff_social_array' ) ) {
			if ( vcex_is_template_edit_mode() ) {
				foreach ( array_slice( wpex_staff_social_array(), 0, 3) as $key => $val ) {
					$loop[ $key ] = '#';
				}
			} else {
				foreach ( wpex_staff_social_array() as $key => $val ) {
					if ( $field_val = get_post_meta( vcex_get_the_ID(), $val['meta'] ?? $key, true ) ) {
						$loop[ $key ] = sanitize_text_field( $field_val );
					}
				}
			}
		}
		break;
	case 'custom_field':
		if ( ! empty( $atts['custom_fields'] ) ) {
			$fields = (array) vcex_vc_param_group_parse_atts( $atts['custom_fields'] ?? [] );
			$loop = [];
			foreach ( $fields as $key => $val ) {
				if ( ! empty( $val['site'] ) && ! empty( $val['key'] ) ) {
					if ( $field_val = get_post_meta( vcex_get_the_ID(), $val['key'], true ) ) {
						$loop[ $val['site'] ] = sanitize_text_field( $field_val );
					} elseif ( vcex_is_template_edit_mode() ) {
						$loop[ $val['site'] ] = '#';
					}
				}
			}
		}
		break;
	case 'custom':
	default:
		$social_links = (array) vcex_vc_param_group_parse_atts( $atts['social_links'] ?? [] );
		$loop = [];
		foreach ( $social_links as $key => $val ) {
			if ( ! empty( $val['site'] ) ) {
				$loop[ $val['site'] ] = vcex_parse_text( $val['link'] ?? '' );
			}
		}
		break;
}

// Loop is required.
if ( empty( $loop ) || ! is_array( $loop ) ) {
	return;
}

// Wrap attributes.
$wrap_attrs = [
	'id' => ! empty( $atts['unique_id'] ) ? $atts['unique_id'] : null,
];

// Wrap classes.
$wrap_classes = [
	'vcex-social-links',
];

if ( $expand ) {
	$wrap_classes[] = 'vcex-social-links--expand';
}

$wrap_classes = array_merge( $wrap_classes, [
	'vcex-module',
	// Utility classes
	'wpex-flex',
	'wpex-flex-wrap',
	'wpex-social-btns',
	'vcex-social-btns', // old class
] );

if ( ! empty( $atts['direction'] ) && 'vertical' === $atts['direction'] ) {
	$wrap_classes[] = 'wpex-flex-col';
	if ( ! empty( $atts['align'] ) ) {
		$wrap_classes[] = vcex_parse_align_items_class( $atts['align'] );
	}
} else {
	$wrap_classes[] = 'wpex-items-center';
	if ( ! empty( $atts['align'] ) ) {
		$wrap_classes[] = vcex_parse_justify_content_class( $atts['align'] );
	}
}

if ( ! empty( $atts['spacing'] ) ) {
	$wrap_classes[] = vcex_parse_gap_class( $atts['spacing'] );
} else {
	$wrap_classes[] = 'wpex-gap-5';
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_classes[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_classes[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_classes[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['classes'] ) ) {
	$wrap_classes[] = vcex_get_extra_class( $atts['classes'] );
}

$wrap_classes[] = 'wpex-last-mr-0';

// Link Classes.
$link_class = [
	'vcex-social-links__item',
];

$link_class[] = vcex_get_social_button_class( $style );

if ( $show_label ) {
	$link_class[] = 'wpex-gap-10';
}

if ( ! empty( $atts['height'] ) ) {
	$link_class[] = 'wpex-inline-flex';
	$link_class[] = 'wpex-flex-column';
	$link_class[] = 'wpex-items-center';
	$link_class[] = 'wpex-justify-center';
	$link_class[] = 'wpex-leading-none';
}

if ( ! empty( $atts['color'] ) ) {
	$link_class[] = 'wpex-has-custom-color';
}

// @todo can we do this via inline_css?
$a_style_args = [];
if ( ! empty( $atts['width'] ) ) {
	if ( $expand || $show_label ) {
		$a_style_args['min_width'] = $atts['width'];
	} else {
		$a_style_args['width'] = $atts['width'];
	}
}

$a_style = vcex_inline_style( $a_style_args, false );

// Reset social button widths/paddings.
if ( $expand || $show_label ) {

	if ( $expand ) {
		$link_class[] = 'wpex-flex-grow';
	}

	$link_class[] = 'wpex-w-auto';
//	$link_class[] = 'wpex-h-auto';  // deprecated when switching to flex styles.

	if ( empty( $atts['height'] ) ) {
		$link_class[] = 'wpex-leading-normal';
	}

	if ( empty( $atts['padding_y'] ) ) {
		$link_class[] = 'wpex-py-5';
	}

	if ( empty( $atts['padding_x'] ) ) {
		$link_class[] = 'wpex-px-15';
	}

}

if ( ! empty( $atts['hover_animation'] ) ) {
	$link_class[] = vcex_hover_animation_class( $atts['hover_animation'] );
}

if ( ! empty( $atts['css'] ) ) {
	$link_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
}

// Add attributes to array.
$wrap_attrs['class'] = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_social_links', $atts );

// Begin output.
$html .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	// Loop through social profiles.
	foreach ( $loop as $key => $val ) {
		if ( ! array_key_exists( $key, $social_profiles) || 'googleplus' === $key || 'google-plus' === $key ) {
			continue;
		}

		// Sanitize classname.
		$profile_class = $key;

		// Link output.
		if ( $val ) {

			$label = $social_profiles[ $key ]['label'] ?? ucfirst( sanitize_text_field( $key ) );

			if ( str_contains( $val, '@' ) && ! str_starts_with( $val, 'mailto:' ) && is_email( $val ) ) {
				$val = "mailto:{$val}";
			}

			$a_attrs = [
				'href'   => vcex_parse_text( $val ),
				'class'  => implode( ' ', $link_class ) . " wpex-{$profile_class}",
				'target' => $atts['link_target'] ?? null,
			];

			if ( ! empty( $a_style ) ) {
				$a_attrs['style'] = $a_style;
			}

			$html .= '<a'. vcex_parse_html_attributes( $a_attrs ) .'>';

				$icon_class = 'vcex-social-links__icon';

				// Provide backwards compatibility for icon class.
				if ( ! empty( $social_profiles[ $key ]['icon_class'] ) ) {
					$html .= vcex_get_theme_icon_html( $social_profiles[ $key ]['icon_class'], $icon_class );
				} else {
					$html .= vcex_get_theme_icon_html( $social_profiles[ $key ]['icon'] ?? $key, $icon_class );
				}

				if ( $show_label ) {
					$html .= '<span class="vcex-social-links__label vcex-label">' . esc_html( $label ) . '</span>';
				} else {
					$html .= '<span class="screen-reader-text">' . esc_html( $label ) . '</span>';
				}

			$html .= '</a>';
		}

	}

$html .= '</div>';

// @codingStandardsIgnoreLine
echo $html;
