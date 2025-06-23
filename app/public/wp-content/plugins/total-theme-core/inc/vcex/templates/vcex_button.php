<?php

/**
 * vcex_button shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Get onclick attributes early so they can be modified later if needed.
$onclick = $atts['onclick'] ?? '';
$onclick_attrs = vcex_get_shortcode_onclick_attributes( $atts, 'vcex_button' );
$link = $onclick_attrs['href'] ?? '';

// @note: The button has always fallen back to a # symbol for the link.
if ( ! $link && ( 'custom_link' === $onclick || (bool) vcex_get_template_edit_mode() ) ) {
	$link = '#';
}

// Don't show the button if there is no link.
if ( ! (bool) $link ) {
	return;
}

// Declare main vars.
$output        = '';
$layout        = $atts['layout'] ?? '';
$align         = ( empty( $atts['text_align'] ) && ! empty( $atts['align'] ) ) ? $atts['align'] : null;
$text_source   = $atts['text_source'] ?? '';
$button_style  = $atts['style'] ?? '';
$button_color  = $atts['color'] ?? '';
$button_size   = $atts['size'] ?? '';
$button_align  = $atts['align'] ?? '';
$button_state  = $atts['state'] ?? '';
$lightbox_type = $atts['lightbox_type'] ?? '';

// Sanitize content.
switch ( $text_source ) {
	case 'custom_field':
		if ( ! empty( $atts['text_custom_field'] ) ) {
			$content = vcex_get_meta_value( $atts['text_custom_field'] );
			if ( ! empty( $content ) && is_array( $content ) ) {
				$content = $content['title'] ?? ''; // used for link types.
			}
			if ( ! $content && vcex_is_template_edit_mode() ) {
				$content = vcex_custom_field_placeholder( $atts['text_custom_field'] );
			}
		}
		break;
	case 'callback_function':
		if ( ! empty( $atts['text_callback_function'] ) && vcex_validate_user_func( $atts['text_callback_function'] ) ) {
			$content = call_user_func( $atts['text_callback_function'] );
		}
		break;
	default:
		if ( ! empty( $content ) ) {
			$content = str_replace( '{{post_title}}', get_the_title(), $content );
		} else {
			$content = esc_html__( 'Button Text', 'total-theme-core' );
		}
		break;
}

// Don't show button if content is empty.
if ( empty( $content ) || ! is_string( $content ) ) {
	return;
}

// Button Classes.
$button_classes = [
	'vcex-button',
];

$button_classes[] = vcex_get_button_classes( $button_style, $button_color, $button_size, $button_align );

if ( 'plain-text' === $button_style ) {
	$button_classes[] = 'wpex-inline-block'; // fixex issues with margins and other styles.
}

if ( $button_state ) {
	$button_classes[] = sanitize_html_class( $atts['state'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$button_classes[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['layout'] ) ) {
	$button_classes[] = $atts['layout'];
}

if ( ! empty( $atts['el_class'] ) ) {
	$button_classes[] = vcex_get_extra_class( $atts['el_class'] );
}

if ( ! empty( $atts['hover_animation'] ) ) {
	$button_classes[] = vcex_hover_animation_class( $atts['hover_animation'] );
}

if ( ! empty( $atts['css_animation'] ) && 'none' !== $atts['css_animation'] && empty( $atts['css_wrap'] ) ) {
	$button_classes[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['shadow'] ) ) {
	$button_classes[] = vcex_parse_shadow_class( $atts['shadow'] );
}

if ( ! empty( $atts['width'] ) ) {
	$button_classes[] = 'wpex-flex-shrink-0';
}

if ( ! empty( $atts['visibility'] ) ) {
	$button_classes[] = vcex_parse_visibility_class( $atts['visibility'] );
}

// Wrap classes.
$wrap_classes = [];

if ( 'center' === $align ) {
	$wrap_classes[] = 'textcenter'; // @todo update to use wpex-text-center
} elseif ( ! empty( $atts['text_align'] ) ) {
	$wrap_classes[] = 'wpex-text-' . sanitize_html_class( $atts['text_align'] );
}

switch ( $layout ) {
	case 'block':
		$wrap_classes[] = 'theme-button-block-wrap';
		$wrap_classes[] = 'wpex-block';
		$wrap_classes[] = 'wpex-clear';
		break;
	case 'expanded':
		$wrap_classes[]   = 'theme-button-expanded-wrap';
		$button_classes[] = 'expanded';
		break;
}

if ( $wrap_classes ) {
	array_unshift( $wrap_classes, 'theme-button-wrap' );
	if ( $align ) {
		$wrap_classes[] = 'wpex-clr';
	}
	$wrap_classes = implode( ' ', $wrap_classes );
}

// Define button icon_classes.
$icon_left  = ! empty( $atts['icon_left_alt'] ) ? vcex_parse_text_safe( $atts['icon_left_alt'] ) : vcex_get_icon_html( $atts, 'icon_left' );
$icon_right = ! empty( $atts['icon_right_alt'] ) ? vcex_parse_text_safe( $atts['icon_right_alt'] ) : vcex_get_icon_html( $atts, 'icon_right' );

// Responsive styles.
$unique_classname = vcex_element_unique_classname();

$el_responsive_styles = [
	'font_size' => $atts['font_size'],
];

$responsive_css = vcex_element_responsive_css( $el_responsive_styles, $unique_classname );

if ( $responsive_css ) {
	$button_classes[] = $unique_classname;
	$output .= '<style>' . $responsive_css . '</style>';
}

// Add onclick classes to the button classes and unset.
if ( isset( $onclick_attrs['class'] ) ) {
	$button_classes = array_merge( $button_classes, $onclick_attrs['class'] );
	unset( $onclick_attrs['class'] );
}

// Turn arrays into strings.
$button_classes = vcex_parse_shortcode_classes( implode( ' ', $button_classes ), 'vcex_button', $atts );

// Open CSS wrapper.
if ( ! empty( $atts['css_wrap'] ) ) {
	$css_wrap_class = vcex_vc_shortcode_custom_css_class( $atts['css_wrap'] );
	$css_wrap_style_args = [];

	if ( ! empty( $atts['css_animation'] ) && 'none' !== $atts['css_animation'] ) {
		if ( ! empty( $atts['animation_delay'] ) ) {
			$css_wrap_style_args[ 'animation_delay' ] = $atts['animation_delay'];
		}
		if ( ! empty( $atts['animation_duration'] ) ) {
			$css_wrap_style_args[ 'animation_duration' ] = $atts['animation_duration'];
		}
		$css_wrap_class .= ' ' . vcex_get_css_animation( $atts['css_animation'] );
	}

	$css_wrap_style = vcex_inline_style( $css_wrap_style_args );

	$output .= '<div class="' . esc_attr( $css_wrap_class ) . ' wpex-clr"' . $css_wrap_style . '>';

}

	// Open wrapper for specific button styles.
	if ( $wrap_classes ) {
		$output .= '<div class="' . esc_attr( $wrap_classes ) . '">';
	}

		$link_attrs = [
			'id'    => ! empty( $atts['unique_id'] ) ? $atts['unique_id'] : null,
			'href'  => $link,
			'class' => esc_attr( $button_classes ),
		];

		unset( $onclick_attrs['href'] );
		$link_attrs = array_merge( $link_attrs, $onclick_attrs );

		if ( ! empty( $atts['aria_label'] ) ) {
			$link_attrs['aria-label'] = esc_attr( trim( $atts['aria_label'] ) );
		}

		if ( vcex_validate_att_boolean( 'download_attribute', $atts, false ) ) {
			$link_attrs['download'] = 'download';
		}

		if ( 'toggle_element' === $onclick ) {
			$link_attrs['aria-expanded'] = ( 'active' === $button_state ) ? 'true' : 'false';
			$link_attrs['aria-controls'] = str_replace( '#', '', $link_attrs['href'] );
		}

		// Open Link.
		$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

			// Open inner span.
			$output .= '<span class="vcex-button-inner theme-button-inner wpex-flex wpex-flex-wrap wpex-items-center wpex-justify-center">';

				// Left Icon.
				if ( $icon_left ) {
					$icon_left_class = 'vcex-button-icon vcex-icon-wrap theme-button-icon-left';
					if ( ! empty( $atts['icon_left_transform'] ) ) {
						$icon_left_class .= ' theme-button-icon-animate-h';
					}
					$output .= '<span class="' . esc_attr( $icon_left_class ) . '">' . $icon_left . '</span>';
				}

				// Text.
				if ( 'toggle_element' === $onclick ) {
					if ( ! empty( $atts['toggle_element_active_text'] ) ) {
						$output .= '<span class="theme-button-text" data-open-text>' . vcex_parse_text_safe( $content ) . '</span>';
						$output .= '<span class="theme-button-text" data-close-text>' . vcex_parse_text_safe( $atts['toggle_element_active_text'] ) . '</span>';
					} else {
						$output .= '<span class="theme-button-text">' . vcex_parse_text_safe( $content ) . '</span>';
					}
				} else {
					$output .= vcex_parse_text_safe( $content );
				}

				// Icon Right.
				if ( $icon_right ) {
					$icon_right_class = 'vcex-button-icon vcex-icon-wrap theme-button-icon-right';
					if ( ! empty( $atts['icon_right_transform'] ) ) {
						$icon_right_class .= ' theme-button-icon-animate-h';
					}
					$output .= '<span class="' . esc_attr( $icon_right_class ) . '">' . $icon_right . '</span>';
				}

			// Close inner span.
			$output .= '</span>';

		// Close link.
		$output .= '</a>';

	// Close wrapper for specific button styles.
	if ( $wrap_classes ) {
		$output .=  '</div>';
	}

// Close css wrap div.
if ( ! empty( $atts['css_wrap'] ) ) {
	$output .= '</div>';
}

// @codingStandardsIgnoreLine
echo $output . ' ';
