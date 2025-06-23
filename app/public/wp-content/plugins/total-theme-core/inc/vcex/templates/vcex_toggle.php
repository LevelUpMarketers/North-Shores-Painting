<?php

/**
 * vcex_toggle shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $content ) || empty( $atts['heading'] ) ) {
	return;
}

if ( vcex_validate_att_boolean( 'parse_content', $atts, true ) ) {
	$content_parsed = vcex_the_content( $content );
} else {
	$content_parsed = $content;
}

if ( ! $content_parsed ) {
	return;
}

$is_open        = ( ! empty( $atts['state'] ) && 'open' === $atts['state'] );
$aria_expanded  = $is_open ? 'true' : 'false';
$faq_microdata  = vcex_validate_att_boolean( 'faq_microdata', $atts );
$icon_position  = ! empty( $atts['icon_position'] ) ? $atts['icon_position'] : 'left';
$icon_spacing   = ! empty( $atts['icon_spacing'] ) ? absint( $atts['icon_spacing'] ) : '10';
$icon_type      = ! empty( $atts['icon_type'] ) ? $atts['icon_type'] : 'plus';
$parsed_heading = vcex_parse_text( $atts['heading'] );

// Define unique content ID.
if ( ! empty( $atts['content_id'] ) ) {
	$content_id = sanitize_text_field( vcex_parse_text( $atts['content_id'] ) );
} else {
	$content_id = sanitize_text_field( $parsed_heading );
}

if ( ! empty( $content_id ) ) {
	$content_id = str_replace( ' ', '-', $content_id );
	$content_id = strtolower( $content_id );
	$content_id = preg_replace( '/[^a-z0-9_\-]/', '', $content_id ); // only allow letters and numbers.
	$content_id = esc_attr( $content_id );
} else {
	$content_id = uniqid( 'vcex_' );
}

// Define element classes.
$shortcode_class = [
	'vcex-toggle',
	'vcex-module',
];

if ( $is_open ) {
	$shortcode_class[] = 'vcex-toggle--active';
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_toggle' );

if ( $extra_classes ) {
	$shortcode_class = array_merge( $shortcode_class, $extra_classes );
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_toggle', $atts );

$animate = ( isset( $atts['animate'] ) && 'false' == $atts['animate'] ) ? 'false' : 'true';

$shortcode_html_attrs = [
	'class'         => trim( $shortcode_class ),
	'data-animate'  => esc_attr( $animate ),
	'data-duration' => '300',
];

if ( $faq_microdata ) {
	$shortcode_html_attrs['itemscope'] = 'itemscope';
	$shortcode_html_attrs['itemprop']  = 'mainEntity';
	$shortcode_html_attrs['itemtype']  = 'https://schema.org/Question';
}

$output = '<div' . vcex_parse_html_attributes( $shortcode_html_attrs ) . '>';

	// Heading
	$heading_class = 'vcex-toggle__heading';

	if ( vcex_validate_att_boolean( 'heading_inline', $atts ) ) {
		$heading_class .= ' wpex-flex';
	}

	if ( ! empty( $atts['heading_el_class'] ) ) {
		$heading_class .= ' ' . vcex_get_extra_class( $atts['heading_el_class'] );
	}

	$output .= '<div class="' . esc_attr( trim( $heading_class ) ) . '">';

		// Trigger
		$trigger_class = 'vcex-toggle__trigger wpex-flex wpex-items-center wpex-transition-colors';

		if ( 'right' === $icon_position ) {
			$trigger_class .= ' wpex-flex-row-reverse';
		}

		$output .= '<a href="#' . esc_attr( $content_id ) . '" class="' . esc_attr( $trigger_class ) . '" aria-expanded="' . esc_attr( $aria_expanded ) . '" aria-controls="' . esc_attr( $content_id ) . '">';

			// Icon.
			$icon_class = 'vcex-toggle__icon wpex-flex wpex-items-center wpex-justify-center';

			if ( 'right' === $icon_position ) {
				$icon_class .= " wpex-ml-{$icon_spacing}";
			} else {
				$icon_class .= " wpex-mr-{$icon_spacing}";
			}

			$output .= '<div class="' . esc_attr( $icon_class ) . '" aria-hidden="true">';

				// Open Icon
				$output .= '<div class="vcex-toggle__icon-open wpex-flex wpex-flex-col wpex-items-center">';
					switch ( $icon_type ) {
						case 'angle':
							$open_icon = '<svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 24 24" width="1.5em" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M24 24H0V0h24v24z" fill="none" opacity=".87"/><path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41z"/></svg>';
							break;
						case 'plus':
						default:
							$open_icon = '<svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 24 24" width="1.5em" fill="currentColor" class="vcex-toggle__icon-open"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>';
							break;
					}
					$output .= (string) apply_filters( 'vcex_toggle_open_icon_svg', $open_icon, $atts );
				$output .= '</div>';

				// Close icon
				$output .= '<div class="vcex-toggle__icon-close wpex-flex wpex-flex-col wpex-items-center">';
					switch ( $icon_type ) {
						case 'angle':
							$close_icon = '<svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 24 24" width="1.5em" fill="currentColor" class="vcex-toggle__icon-close"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 8l-6 6 1.41 1.41L12 10.83l4.59 4.58L18 14l-6-6z"/></svg>';
							break;
						case 'plus':
						default:
							$close_icon = '<svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 24 24" width="1.5em" fill="currentColor" class="vcex-toggle__icon-close"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 13H5v-2h14v2z"/></svg>';
							break;
					}
					$output .= (string) apply_filters( 'vcex_toggle_close_icon_svg', $close_icon, $atts );
				$output .= '</div>';

			$output .= '</div>';

			// Title
			$title_tag_escaped = ! empty( $atts['heading_tag'] ) ? tag_escape( $atts['heading_tag'] ) : 'div';
			$title_class = 'vcex-toggle__title';

			if ( 'right' === $icon_position ) {
				$title_class .= ' wpex-mr-auto';
			}

			$output .= '<' . $title_tag_escaped . ' class="' . esc_attr( $title_class ) . '"';
				if ( $faq_microdata ) {
					$output .= ' itemprop="name"';
				}
			$output .= '>';
				$output .= wp_kses_post( $parsed_heading );
			$output .= '</' . $title_tag_escaped . '>';

		$output .= '</a>';

	$output .= '</div>'; // heading close

	// Content
	$content_class = 'vcex-toggle__content wpex-last-mb-0 wpex-my-10 wpex-clr';

	if ( ! empty( $atts['animation_speed'] ) ) {
		$content_class .= ' wpex-duration-' . sanitize_html_class( absint( $atts['animation_speed'] ) );
	}

	$output .= '<div id="' . esc_attr( $content_id ) . '" class="' . esc_attr( $content_class ) . '"';

		if ( $faq_microdata ) {
			$output .= ' itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"';
		}

	$output .= '>';

		if ( $faq_microdata ) {
			$output .= '<div itemprop="text">';
		}

		$output .= $content_parsed;

		if ( $faq_microdata ) {
			$output .= '</div>';
		}

	$output .= '</div>';

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
