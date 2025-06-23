<?php

defined( 'ABSPATH' ) || exit;

// Add Theme Section to WooCommerce tab.
$wp_customize->add_section( 'wpex_woocommerce_vanilla', [
	'title' => esc_html__( 'Theme Settings', 'total' ),
	'theme_supports' => [ 'woocommerce' ],
	'panel' => 'woocommerce',
] );

// Shop Layout.
$wp_customize->add_setting( 'woo_shop_layout' , [
	'default' => 'full-width',
	'transport' => 'refresh',
	'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::select',
] );

$wp_customize->add_control( 'woo_shop_layout', [
	'label' => esc_html__( 'Shop Layout', 'total' ),
	'section' => 'wpex_woocommerce_vanilla',
	'settings' => 'woo_shop_layout',
	'type' => 'select',
	'choices' => wpex_get_post_layouts(),
] );

// Shop Layout.
$wp_customize->add_setting( 'woo_product_layout' , [
	'default' => 'full-width',
	'transport' => 'refresh',
	'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::select',
] );

$wp_customize->add_control( 'woo_product_layout', [
	'label' => esc_html__( 'Single Product Layout', 'total' ),
	'section' => 'wpex_woocommerce_vanilla',
	'settings' => 'woo_product_layout',
	'type' => 'select',
	'choices' => wpex_get_post_layouts(),
] );

// Next/Previous.
$wp_customize->add_setting( 'woo_next_prev' , [
	'default' => true,
	'transport' => 'refresh',
	'sanitize_callback' => 'absint',
] );

$wp_customize->add_control( 'woo_next_prev', [
	'label' => esc_html__( 'Display Next & Previous Links?', 'total' ),
	'section' => 'wpex_woocommerce_vanilla',
	'settings' => 'woo_next_prev',
	'type' => 'checkbox',
] );
