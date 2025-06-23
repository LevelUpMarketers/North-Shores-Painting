<?php

/**
 * vcex_star_rating shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1.1
 */

defined( 'ABSPATH' ) || exit;

$rating = ! empty( $atts['value'] ) ? sanitize_text_field( $atts['value'] ) : '{{post_rating}}';
$rating = vcex_parse_text( $rating );

if ( $rating ) {
	$rating = floatval( $rating ); // make sure it's numeric
} elseif ( in_array( get_post_type(), [ 'wpex_templates', 'wpex_card' ], true ) ) {
	$rating = '4.5';
}

if ( ! $rating && '0' !== $rating ) {
	return;
}

$style      = ( ! empty( $atts['style'] ) && 'boxed' === $atts['style'] ) ? 'boxed' : 'plain';
$icon       = ! empty( $atts['icon_type'] ) ? \sanitize_text_field( $atts['icon_type'] ) : 'ticons';
$scale      = ( $atts['scale'] && in_array( $atts['scale'], [ '0-5', '0-10' ], true ) ) ? $atts['scale'] : '0-5';
$max_rating = ( '0-10' === $scale ) ? 10 : 5;

$wrap_class = [
	'vcex-module',
	'vcex-star-rating',
	"vcex-star-rating--{$style}",
	// Utility classes
	'wpex-leading-none',
];

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_star_rating' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_star_rating', $atts );

$label_html = '';
$label_position = ! empty( $atts['label_position'] ) ? wp_strip_all_tags( $atts['label_position'] ) : 'top';

if ( ! empty( $atts['label'] ) ) {
	$label = vcex_parse_text_safe( $atts['label'] );
	if ( $label ) {
		$label_class = 'vcex-star-rating__label';
		$label_class .= " vcex-star-rating__label--{$label_position}";
		switch ( $label_position ) {
			case 'inline':
				$label_class .= ' wpex-mr-5';
				break;
			case 'top':
			default:
				$label_class .= ' wpex-mb-10';
				break;
		}
		$label_html = '<div class="' . esc_attr( $label_class ) . ' wpex-bold">' . $label . '</div>';
	}
}

// Display the shortcode.
$output = '<div class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $atts ) . '>';

	// Display Label.
	if ( $label_html && 'top' == $label_position ) {
		$output .= $label_html;
	}

	$output .= '<div class="vcex-star-rating__inner wpex-inline-flex wpex-flex-wrap wpex-items-center">';

		// Display Label.
		if ( $label_html && 'inline' == $label_position ) {
			$output .= $label_html;
		}

		// Single star HTML
		if ( 'boxed' === $style ) {
			$full_star = '<span class="vcex-star-rating__star vcex-star-rating__star--full wpex-relative wpex-inline-flex wpex-items-center wpex-justify-center wpex-text-white wpex-p-5"><span class="vcex-star-rating__star-fill wpex-bg-accent wpex-absolute wpex-inset-0"></span>' . vcex_get_theme_icon_html( "{$icon}/star", 'wpex-relative' ) . '</span>';
			$half_star = '<span class="vcex-star-rating__star vcex-star-rating__star--half wpex-relative wpex-inline-flex wpex-items-center wpex-justify-center wpex-surface-4 wpex-text-white wpex-p-5"><span class="vcex-star-rating__star-fill wpex-bg-accent wpex-absolute wpex-left-0 wpex-top-0 wpex-h-100 wpex-w-50"></span>' . vcex_get_theme_icon_html( "{$icon}/star-half-empty", 'wpex-relative' ) . '</span>';
			$empty_star = '<span class="vcex-star-rating__star vcex-star-rating__star--empty wpex-inline-flex wpex-items-center wpex-justify-center wpex-surface-4 wpex-text-white wpex-p-5">' . vcex_get_theme_icon_html( "{$icon}/star-empty" ) . '</span>';
		} else {
			$full_star = '<span class="vcex-star-rating__star vcex-star-rating__star--full wpex-inline-flex wpex-items-center wpex-justify-center">' . vcex_get_theme_icon_html( "{$icon}/star" ) . '</span>';
			$half_star = '<span class="vcex-star-rating__star vcex-star-rating__star--half wpex-inline-flex wpex-items-center wpex-justify-center">' . vcex_get_theme_icon_html( "{$icon}/star-half-empty" ) . '</span>';
			$empty_star = '<span class="vcex-star-rating__star vcex-star-rating__star--empty wpex-items-center wpex-justify-center">' . vcex_get_theme_icon_html( "{$icon}/star-empty" ) . '</span>';
		}

		// Display Stars
		$output .= '<div class="vcex-star-rating__stars wpex-flex wpex-flex-wrap wpex-items-center wpex-gap-5">';

			// Integers.
			if ( ( is_numeric( $rating ) && ( intval( $rating ) == floatval( $rating ) ) ) ) {
				$output .= str_repeat( $full_star, $rating );
				if ( $rating < $max_rating ) {
					$output .= str_repeat( $empty_star, $max_rating - $rating );
				}

			// Fractions.
			} else {
				$whole_rating = intval( $rating );
				$output .= str_repeat( $full_star, $whole_rating );
				$output .= $half_star;
				if ( $whole_rating < $max_rating ) {
					$output .= str_repeat( $empty_star, ( $max_rating - 1 ) - $whole_rating );
				}
			}

		$output .= '</div>';

		// Display number.
		if ( vcex_validate_att_boolean( 'show_number', $atts ) ) {
			$output .= '<div class="vcex-star-rating__number wpex-ml-5">';
				$rating_safe = floatval( $rating  );
				$max_rating_safe = absint( $max_rating );
				$output .= "({$rating_safe}/{$max_rating_safe})";
			$output .= '</div>';
		}

	$output .= '</div>';

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
