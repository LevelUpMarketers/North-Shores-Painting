<?php

defined( 'ABSPATH' ) || exit;

/**
 * Return default aria-labels.
 */
function wpex_aria_label_defaults() {
	return [
		'site_navigation'    => esc_attr_x( 'Main menu', 'screen reader text', 'total' ),
		'footer_callout'     => '',
		'footer_bottom_menu' => esc_attr_x( 'Footer menu', 'screen reader text', 'total' ),
		'mobile_menu_open'   => esc_attr_x( 'Open mobile menu', 'screen reader text', 'total' ),
		'mobile_menu_close'  => esc_attr_x( 'Close mobile menu', 'screen reader text', 'total' ),
		'mobile_menu'        => esc_attr_x( 'Mobile menu', 'screen reader text', 'total' ),
		'search'             => esc_attr_x( 'Search', 'screen reader text', 'total' ),
		'submit_search'      => esc_attr_x( 'Submit search', 'screen reader text', 'total' ),
		'breadcrumbs'        => esc_attr_x( 'You are here:', 'screen reader text', 'total' ),
		'dark_mode_toggle'   => esc_attr_x( 'Toggle dark mode', 'screen reader text', 'total' ),
		'toggle_bar_open'    => esc_attr_x( 'Show notice', 'screen reader text', 'total' ),
		'toggle_bar_close'   => esc_attr_x( 'Hide notice', 'screen reader text', 'total' ),
		// Cart.
		'cart_open'          => esc_attr_x( 'Open shopping cart', 'screen reader text', 'total' ),
		'cart_close'         => esc_attr_x( 'Close shopping cart', 'screen reader text', 'total' ),
		// @deprecated in 5.3
		'mobile_menu_search' => esc_attr_x( 'Search', 'screen reader text', 'total' ),
		'search_submit'      => esc_attr_x( 'Submit search', 'screen reader text', 'total' ),
	];
}

/**
 * Get aria label based on location.
 */
function wpex_get_aria_label( $location = null ) {
	if ( ! $location || ! get_theme_mod( 'aria_labels_enable', true ) ) {
		return;
	}
	$defaults = wpex_aria_label_defaults();
	$labels = wp_parse_args( (array) get_theme_mod( 'aria_labels' ), $defaults );
	$label = $labels[ $location ] ?? '';
	$label = (string) apply_filters( 'wpex_aria_label', $label, $location );
	return $label;
}

/**
 * Output aria-label HTML.
 */
function wpex_aria_label( $location ) {
	$label = wpex_get_aria_label( $location );
	if ( ! empty( $label ) ) {
		echo ' aria-label="' . esc_attr( trim( $label ) ) .'"';
	}
}
