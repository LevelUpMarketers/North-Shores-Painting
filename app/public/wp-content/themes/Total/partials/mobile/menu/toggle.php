<?php

use TotalTheme\Mobile\Menu as Mobile_Menu;

/**
 * Toggle (dropdown) mobile menu element that gets populated via JS.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Mobile\Menu' ) ) {
	return;
}

$mm_toggle_style = wpex_header_menu_mobile_toggle_style();
$is_full_height = 'toggle_full' === Mobile_Menu::style();
$position = totaltheme_call_static( 'Mobile\Menu\Toggle', 'get_position' );

// Define append_to or insert_after vars (used for positioning the menu via JS).
if ( 'fixed_top' === $mm_toggle_style ) {
	$append_to = '#wpex-mobile-menu-fixed-top';
} elseif ( 'absolute' === $position ) {
	if ( 'navbar' === $mm_toggle_style ) {
		$append_to = '#wpex-mobile-menu-navbar';
	} else {
		$append_to = '#site-header';
	}
} elseif ( 'afterself' === $position ) {
	$insert_after = '#wpex-mobile-menu-navbar';
} else {
	$insert_after = '#site-header';
}

// Add classes.
$class = [
	'mobile-toggle-nav',
	'wpex-mobile-menu',
];

if ( $is_full_height ) {
	$class[] = 'mobile-toggle-nav--fullscreen';
} else {
	if ( get_theme_mod( 'mobile_menu_toggle_animate', true ) ) {
		$class[] = 'mobile-toggle-nav--animate';
	}
}

if ( 'fixed_top' === $mm_toggle_style && ! $is_full_height ) {
	$class[] = 'wpex-surface-dark';
} else {
	$class[] = 'wpex-surface-1';
}

$class[] = 'wpex-hidden'; // hide by default.
$class[] = 'wpex-text-2'; // fixes issues with the transparent header.
$class[] = "wpex-togglep-{$position}";

// Utility classes
if ( 'absolute' === $position ) {
	$class[] = 'wpex-absolute';
	$class[] = 'wpex-top-100';
	$class[] = 'wpex-left-0';
	$class[] = 'wpex-w-100';
	$class[] = 'wpex-z-dropdown';
}

// Define nav attributes.
$nav_attributes = [
	'class'      => array_unique( $class ),
	'aria-label' => esc_attr( wpex_get_aria_label( 'mobile_menu' ) ),
];

if ( isset( $append_to ) ) {
	$nav_attributes['data-wpex-append-to'] = $append_to;
}

if ( isset( $insert_after ) ) {
	$nav_attributes['data-wpex-insert-after'] = $insert_after;
}

// Inner class.
$inner_class = 'mobile-toggle-nav-inner container';

if ( 'absolute' === $position ) {
	$inner_class .= ' wpex-overflow-y-auto wpex-hide-scrollbar wpex-overscroll-contain';
}

if ( $is_full_height ) {
	$inner_class .= ' wpex-pb-20';
}

?>

<nav <?php echo wpex_parse_attrs( $nav_attributes ); ?>>
	<div class="<?php echo esc_attr( $inner_class ); ?>">
		<?php
		/**
		 * Hook: wpex_hook_mobile_menu_top.
		 */
		Mobile_Menu::hook_top(); ?>
		<ul class="mobile-toggle-nav-ul wpex-h-auto wpex-leading-inherit wpex-list-none wpex-my-0 wpex-mx-auto"></ul>
		<?php
		/**
		 * Displays the mobile menu search form if enabled.
		 */
		if ( $is_full_height ) {
			Mobile_Menu::search_form( [
				'class'        => 'wpex-mt-30',
				'placeholder'  => '',
				'form_class'   => 'wpex-flex wpex-gap-10',
				'input_class'  => 'wpex-w-100',
				'submit_class' => 'theme-button',
				'submit_icon'  => false,
			] );
		} else {
			ob_start();
				Mobile_Menu::search_form( [
					'class'        => 'wpex-relative wpex-pb-20',
					'form_class'   => 'wpex-flex',
					'input_class'  => 'wpex-w-100 wpex-rounded-0 wpex-py-0 wpex-px-10 wpex-outline-0 wpex-border wpex-border-solid wpex-border-main wpex-bg-white wpex-text-gray-800 wpex-shadow-none wpex-text-1em wpex-unstyled-input wpex-leading-relaxed',
					'submit_class' => 'theme-button wpex-rounded-0 wpex-p-0 wpex-tracking-normal wpex-flex-shrink-0 wpex-text-1em',
					'submit_text'  => '',
				] );
			if ( $search_form = ob_get_clean() ) {
				echo '<div class="mobile-toggle-nav-search">' . $search_form . '</div>';
			} 
		}
		?>
		<?php
		/**
		 * Hook: wpex_hook_mobile_menu_bottom.
		 */
		Mobile_Menu::hook_bottom(); ?>
	</div>
</nav>
