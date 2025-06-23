<?php

/**
 * vcex_widget_modern_menu shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.2
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalThemeCore\Widgets\Widget_Modern_Menu' ) || ! function_exists( 'the_widget' ) || empty( $atts['menu_id'] ) ) {
	return;
}

the_widget( 'TotalThemeCore\Widgets\Widget_Modern_Menu', [
	'nav_menu'                => (int) $atts['menu_id'],
	'style'                   => ! empty( $atts['style'] ) ? sanitize_text_field( $atts['style'] ) : 'bordered',
	'el_class'                => ! empty( $atts['el_class'] ) ? sanitize_text_field( $atts['el_class'] ) : '',
	'aria_label'              => ! empty( $atts['aria_label'] ) ? sanitize_text_field( $atts['aria_label'] ) : '',
	'arrow_position'          => ! empty( $atts['arrow_position'] ) ? sanitize_text_field( $atts['arrow_position'] ) : '',
	'show_descriptions'       => vcex_validate_att_boolean( 'show_descriptions', $atts, false ),
	'hide_dropdowns'          => vcex_validate_att_boolean( 'hide_dropdowns', $atts, false ),
	'show_arrows'             => vcex_validate_att_boolean( 'show_arrows', $atts, true ),
	'active_highlight'        => vcex_validate_att_boolean( 'active_highlight', $atts, true ),
	'expand_active_dropdowns' => vcex_validate_att_boolean( 'expand_active_dropdowns', $atts, true ),
], [] );
