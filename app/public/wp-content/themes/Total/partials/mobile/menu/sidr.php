<?php

use TotalTheme\Mobile\Menu as Mobile_Menu;

/**
 * Elements used for the dynamic sidebar mobile menu which is generated via JS.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.3
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Mobile\Menu' ) ) {
	return;
}

Mobile_Menu::search_form( [
    'submit_text'  => '',
    'form_class'   => 'wpex-relative',
    'input_class'  => 'wpex-unstyled-input wpex-outline-0 wpex-w-100',
    'submit_class' => 'wpex-unstyled-button wpex-block wpex-absolute wpex-top-50 wpex-text-right',
] );

/**
 * Insert mobile menu top/bottom hooks.
 *
 * These elements were previously inserted via the header-menu-mobile-extras.php file
 * but moved here in Total 6.3 for consistency - hence the added check.
 */
if ( is_child_theme() && file_exists( get_stylesheet_directory() . '/partials/header/header-menu-mobile-extras.php' ) ) {
	get_template_part( 'partials/header/header-menu-mobile-extras' );
} else {
    Mobile_Menu::hook_top();
    Mobile_Menu::hook_bottom();
}

?>

<template id="wpex-template-sidr-mobile-menu-top"><?php Mobile_Menu::render_top(); ?></template>

<div class="wpex-sidr-overlay wpex-fixed wpex-inset-0 wpex-hidden wpex-z-backdrop wpex-bg-backdrop"></div>
