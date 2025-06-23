<?php

/**
 * vcex_steps shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $atts['steps'] ) ) {
	return;
}

$steps = vcex_vc_param_group_parse_atts( $atts['steps'] );

if ( ! $steps || ! is_array( $steps ) ) {
	return;
}

$steps_count = count( $steps );
$direction = ! empty( $atts['direction'] ) ? sanitize_text_field( $atts['direction'] ) : 'horizontal';
$responsive = ( 'horizontal' === $direction );
$bk = ! empty( $atts['breakpoint'] ) ? sanitize_text_field( $atts['breakpoint'] ) : 'md';
$gap_x = ! empty( $atts['gap_x'] ) ? absint( $atts['gap_x'] ) : '40';
$gap_y = ! empty( $atts['gap_y'] ) ? absint( $atts['gap_y'] ) : '40';
$figure_margin = ! empty( $atts['figure_margin'] ) ? absint( $atts['figure_margin'] ) : 20;
$figure_margin_stacked = ! empty( $atts['figure_margin_stacked'] ) ? absint( $atts['figure_margin_stacked'] ) : $figure_margin;
$text_margin = ! empty( $atts['text_margin'] ) ? absint( $atts['text_margin'] ) : 10;
$center = $responsive ? vcex_validate_att_boolean( 'center', $atts ) : false;
$line_width = absint( ! empty( $atts['line_width'] ) ? $atts['line_width'] : '1px' );
$symbol_style = ! empty( $atts['symbol_style'] ) ? sanitize_text_field( $atts['symbol_style'] ) : 'solid';
$has_accordion = ( 'vertical' === $direction && vcex_validate_att_boolean( 'accordion', $atts ) ) ? true : false;

$shortcode_class = [
	'vcex-steps',
	"vcex-steps--{$direction}",
];

if ( $has_accordion ) {
	$shortcode_class[] = 'vcex-steps--accordion';
}

if ( $extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_steps' ) ) {
	$shortcode_class = array_merge( $shortcode_class, $extra_classes );
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_steps', $atts );

// Calculate element classnames.
$items_class = [
	'vcex-steps__items',
];

if ( $center ) {
	$items_class[] = 'wpex-text-center';
}

$item_class = [
	'vcex-steps-item',
	'wpex-flex',
];

$figure_class = [
	'vcex-steps-item__figure',
	'wpex-flex',
	'wpex-items-center',
];

if ( $center ) {
	if ( $figure_margin_stacked === $figure_margin ) {
		$figure_class[] = "wpex-mb-{$figure_margin}";
	} else {
		$figure_class[] = "wpex-mb-{$figure_margin_stacked}";
		$figure_class[] = "wpex-{$bk}-mb-{$figure_margin}";
	}
} else {
	if ( $responsive ) {
		$figure_class[] = "wpex-mr-{$figure_margin_stacked}";
	} else {
		$figure_class[] = "wpex-mr-{$figure_margin}";
	}
}

$symbol_class = [
	'vcex-steps-item__symbol',
	"vcex-steps-item__symbol--{$symbol_style}",
	'wpex-inline-flex',
	'wpex-items-center',
	'wpex-justify-center',
	'wpex-flex-shrink-0',
	'wpex-leading-none',
	'wpex-tracking-normal',
	'wpex-rounded-full',
];

if ( vcex_has_classic_styles() ) {
	$symbol_class[] = 'wpex-text-md';
} else {
	$symbol_class[] = 'wpex-text-lg';
}

switch ( $symbol_style ) {
	case 'outline':
		$border_width = ( $line_width && 1 !== $line_width ) ? '-' . $line_width : '';
		$symbol_class[] = "wpex-text-accent wpex-border{$border_width} wpex-border-solid";
		break;
	case 'solid':
	default:
		$symbol_class[] = 'wpex-bg-accent wpex-text-on-accent';
		break;
}

$line_class = [
	'vcex-steps-item__line',
	'wpex-block',
	'wpex-grow',
	'wpex-bg-accent',
	"wpex-w-{$line_width}px",
	'wpex-h-100',
];

$content_class = [
	'vcex-steps-item__content',
	'wpex-flex-grow',
	"wpex-mb-{$gap_y}",
];

if ( $responsive ) {
	$items_class[] = 'wpex-grid';
	$items_class[] = "wpex-{$bk}-grid-cols-{$steps_count}";
	$figure_class[] = "wpex-flex-col wpex-{$bk}-flex-row wpex-{$bk}-items-center";
	$figure_class[] = "wpex-{$bk}-mr-0";
	$line_class[] = "wpex-{$bk}-h-{$line_width}px wpex-{$bk}-w-100";
	if ( $center ) {
		$gap_x = $gap_x / 2;
		$item_class[] = 'wpex-flex-col';
		$line_class[array_search( 'wpex-block', $line_class )] = "wpex-hidden wpex-{$bk}-block";
		$content_class[] = "wpex-{$bk}-mx-{$gap_x}";
		$content_class[] = "wpex-{$bk}-my-0";
	} else {
		$item_class[] = "wpex-{$bk}-flex-col";
		$figure_class[] = "wpex-{$bk}-mb-{$figure_margin}";
		$content_class[] = "wpex-{$bk}-mr-{$gap_x}";
		$content_class[] = "wpex-{$bk}-mb-0";
	}
} else {
	$items_class[] = 'wpex-grid';
	$figure_class[] = "wpex-flex-col wpex-mr-{$figure_margin}";
}

$heading_tag_safe = tag_escape( $atts['heading_tag'] ?? null ) ?: 'div';

$heading_class = [
	'vcex-steps-item__heading',
	'wpex-heading',
	'wpex-flex',
	'wpex-flex-wrap',
	'wpex-items-center',
];

if ( vcex_has_classic_styles() ) {
	$heading_class[] = 'wpex-text-md';
} else {
	$heading_class[] = 'wpex-text-lg';
}

if ( $center ) {
	$heading_class[] = 'wpex-justify-center';
}

if ( $has_accordion ) {
	$heading_class[] = 'wpex-relative';
}

$text_class = [
	'vcex-steps-item__text',
	'wpex-last-mb-0',
];

$text_class[] = "wpex-mt-{$text_margin}";

// Convert classes to safe strings.
$item_class_str    = esc_attr( implode( ' ', $item_class ) );
$figure_class_str  = esc_attr( implode( ' ', $figure_class ) );
$symbol_class_str  = esc_attr( implode( ' ', $symbol_class ) );
$line_class_str    = esc_attr( implode( ' ', $line_class ) );
$content_class_str = esc_attr( implode( ' ', $content_class ) );
$heading_class_str = esc_attr( implode( ' ', $heading_class ) );
$text_class_str    = esc_attr( implode( ' ', $text_class ) );

if ( $has_accordion
	&& ! empty( $atts['accordion_animation_duration'] )
	&& '0ms' !== $atts['accordion_animation_duration']
) {
	$data_animation_duration = ' data-vcex-animation-duration="' . esc_attr( absint( $atts['accordion_animation_duration'] ) ) . '"';
} else {
	$data_animation_duration = '';
}

// Shortcode html
$html = '<div class="' . esc_attr( $shortcode_class ) . '"' . vcex_get_unique_id( $atts ) . $data_animation_duration . '>';

	$html .= '<div class="' . esc_attr( implode( ' ', $items_class ) ) . '">';

		$count = 0;
		foreach ( $steps as $step ) {
			$heading = $step['heading'] ?? '';
			$text = $step['text'] ?? '';
			if ( $has_accordion && ( ! $heading || ! $text ) ) {
				continue;
			}
			$count++;

			$html .= '<div class="' . str_replace( 'vcex-steps-item', 'vcex-steps-item vcex-steps-item--' . $count, $item_class_str ) . '">';

				$html .= '<div class="' . $figure_class_str . '">';

					if ( $center ) {
						$before_line_str = $line_class_str; // must reset on each loop
						if ( 1 === $count ) {
							$before_line_str = str_replace( 'wpex-bg-accent', 'wpex-bg-transparent', $line_class_str );
						}
						$html .= '<span class="' . $before_line_str . '"></span>';
					}

					if ( $has_accordion ) {
						$symbol_class_str .= ( 1 === $count ) ? ' wpex-cursor-default' : ' wpex-cursor-pointer';
					}

					$html .= '<span class="' . $symbol_class_str . '">' . $count . '</span>';

					$show_line = true;
					if ( ! $center
						&& $count === $steps_count
						&& ! vcex_validate_boolean( $atts['last_line'] ?? false )
					) {
						$show_line = false;
					}

					if ( $show_line ) {
						if ( $count === $steps_count ) {
							$line_class_str = str_replace( 'wpex-bg-accent', 'wpex-bg-transparent', $line_class_str );
						}
						$html .= '<span class="' . $line_class_str . '"></span>';
					}

				$html .= '</div>';

				$html .= '<div class="' . $content_class_str . '">';

					if ( $heading ) {
						$parsed_heading = vcex_parse_text_safe( $heading );
						$html .= '<' . $heading_tag_safe . ' class="' . $heading_class_str . '">';
							if ( $has_accordion ) {
								$aria_controls = str_replace( ' ', '-', $parsed_heading );
								$aria_controls = strtolower( $aria_controls );
								$aria_controls = preg_replace( '/[^a-z0-9_\-]/', '', $aria_controls );
								$toggle_class = 'wpex-heading vcex-steps-item__toggle';
								if ( 1 === $count ) {
									$aria_exanded = 'true';
									$tabindex = ' tabindex="-1"';
									$toggle_class .= ' wpex-pointer-events-none';
								} else {
									$tabindex = '';
									$aria_exanded = 'false';
								}
								$html .= '<a href="#" role="button" aria-expanded="' . $aria_exanded . '" aria-controls="vcex-steps-item__text--' . esc_attr( $aria_controls )  . '" class="' . $toggle_class . '"' . $tabindex . '>' . $parsed_heading .'</a>';
							} else {
								$html .= $parsed_heading;
							}
						$html .= '</' . $heading_tag_safe . '>';
					}

					if ( $text ) {
						$text_id = '';
						$text_class = $text_class_str; // must reset every time.
						if ( $has_accordion ) {
							if ( $count > 1 ) {
								$text_class .= ' wpex-hidden';
							}
							if ( isset( $aria_controls ) ) {
								$text_id = ' id="vcex-steps-item__text--' . esc_attr( $aria_controls ) . '"';
							}
						}
						$html .= '<div' . $text_id . ' class="' . $text_class . '">' . wpautop( vcex_parse_text_safe( $text ) ) . '</div>';
					}

				$html .= '</div>';

			$html .= '</div>';

		}

	$html .= '</div>';

$html .= '</div>';

// @codingStandardsIgnoreLine
echo $html;
