<?php

/**
 * Togglebar button output.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$toggle_bar_style = wpex_togglebar_style();
$default_state    = wpex_togglebar_state();
$visibility       = wpex_togglebar_visibility();

// Link attributes.
$attrs = [
	'href'  => '#',
	'id'    => 'toggle-bar-button',
	'class' => [
		'toggle-bar-btn',
		'fade-toggle',
		'open-togglebar',
		'wpex-block',
		'wpex-text-white',
		'wpex-hover-text-white',
		'wpex-z-overlay-button',
		'wpex-print-hidden',
	],
];

// Set correct position.
if ( 'inline' === $toggle_bar_style ) {
	$attrs['class'][] = 'wpex-absolute';
} else {
	$attrs['class'][] = 'wpex-fixed';
}

// Visibility.
if ( $visibility && $visibility_class = totaltheme_get_visibility_class( $visibility ) ) {
	$attrs['class'][] = $visibility_class;
}

// Add other attributes.
$attrs['aria-controls'] = 'toggle-bar-wrap';
$attrs['aria-expanded'] = ( 'visible' === $default_state ) ? 'true' : 'false';

$inner_html = '';

// Closed icon classes.
$closed_icon = get_theme_mod( 'toggle_bar_button_icon', 'plus' );
$closed_icon = (string) apply_filters( 'wpex_togglebar_icon_class', $closed_icon );

if ( $closed_icon ) {
	$inner_html .= '<span class="toggle-bar-btn__icon toggle-bar-btn__icon--closed wpex-flex" data-open-text>';
		$inner_html .= totaltheme_get_icon( $closed_icon, 'wpex-flex' );
	$inner_html .= '</span>';
}

// Active icon classes.
$active_icon = get_theme_mod( 'toggle_bar_button_icon_active', 'minus' );
$active_icon = (string) apply_filters( 'wpex_togglebar_icon_active_class', $active_icon );
$active_icon = $active_icon ?: $closed_icon;

if ( $active_icon ) {
	$inner_html .= '<span class="toggle-bar-btn__icon toggle-bar-btn__icon--open wpex-flex" data-close-text>';
		$inner_html .= totaltheme_get_icon( $active_icon, 'wpex-flex' );
	$inner_html .= '</span>';
}

// Screen reader Text.
$inner_html .= '<span class="screen-reader-text" data-open-text>' . wpex_get_aria_label( 'toggle_bar_open' ) . '</span>';
$inner_html .= '<span class="screen-reader-text" data-close-text>' . wpex_get_aria_label( 'toggle_bar_close' ) . '</span>';

// Display button.
echo wpex_parse_html( 'a', $attrs, $inner_html );
