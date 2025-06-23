<?php

use TotalTheme\Mobile\Menu as Mobile_Menu;

/**
 * Full Screen mobile menu element that gets populated via JS.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Mobile\Menu' ) ) {
	return;
}

$legacy_typo = totaltheme_has_classic_styles();
$under_header = 'full_screen_under_header' === Mobile_Menu::style();
$style = ( $style = get_theme_mod( 'full_screen_mobile_menu_style' ) ) ? sanitize_text_field( $style ) : 'white';

$wrap_class = [
	'full-screen-overlay-nav',
];

if ( $under_header ) {
	$wrap_class[] = 'full-screen-overlay-nav--under-header';
}

if ( $style ) {
	$wrap_class[] = $under_header ? "wpex-bg-{$style}" : $style;
}

// Utility classes.
$wrap_class[] = 'wpex-mobile-menu';
$wrap_class[] = 'wpex-invisible';
$wrap_class[] = 'wpex-opacity-0';
$wrap_class[] = 'wpex-z-modal';
$wrap_class[] = 'wpex-fixed';
$wrap_class[] = 'wpex-w-100';
$wrap_class[] = 'wpex-left-0';
$wrap_class[] = 'wpex-overflow-y-auto';
$wrap_class[] = 'wpex-overscroll-contain';
$wrap_class[] = 'wpex-hide-scrollbar';

if ( $legacy_typo ) {
	$wrap_class[] = 'wpex-font-light';
	$wrap_class[] = 'wpex-leading-normal';
	$wrap_class[] = 'wpex-text-5xl';
} else {
	$wrap_class[] = 'wpex-text-xl';
}

if ( ! $under_header ) {
	$wrap_class[] = 'wpex-h-100';
	$wrap_class[] = 'wpex-top-0';
	$wrap_class[] = 'wpex-transition-all';
	$wrap_class[] = 'wpex-duration-400';
}

switch ( $style ) {
	case 'white':
		$wrap_class[] = 'wpex-text-black';
		break;
	case 'black';
		$wrap_class[] = 'wpex-text-white';
		break;
}

?>

<div class="<?php echo esc_attr( implode( ' ', $wrap_class ) ); ?>" aria-expanded="false" aria-label="<?php echo esc_attr( wpex_get_aria_label( 'mobile_menu' ) ); ?>">
	<?php if ( ! $under_header ) { ?>
		<button class="full-screen-overlay-nav-close wpex-unstyled-button wpex-block wpex-fixed wpex-top-0 wpex-right-0 wpex-mr-20 wpex-mt-20 wpex-text-base" aria-label="<?php echo esc_attr( wpex_get_aria_label( 'mobile_menu_close' ) ); ?>"><?php
			echo totaltheme_call_static(
				'Mobile\Menu',
				'get_close_icon',
				'full-screen-overlay-nav-close__icon',
				$legacy_typo ? 'sm' : 'xl'
		); ?></button>
	<?php } ?>
	<div class="full-screen-overlay-nav-content wpex-table wpex-w-100 wpex-h-100 wpex-text-center">
		<div class="full-screen-overlay-nav-content-inner wpex-table-cell wpex-align-middle wpex-h-100 wpex-w-100">
			<?php
			/**
			 * Hook: wpex_hook_mobile_menu_top.
			 */
			Mobile_Menu::hook_top(); ?>
			<nav class="full-screen-overlay-nav-menu"><ul></ul></nav>
			<?php
			/**
			 * Displays the mobile menu search form if enabled.
			 */
			Mobile_Menu::search_form( [
				'class'        => 'wpex-max-w-100 wpex-mx-auto wpex-pt-20',
				'form_class'   => 'wpex-flex',
				'input_class'  => 'wpex-unstyled-input wpex-outline-0 wpex-border-0 wpex-w-100 wpex-py-10 wpex-px-20 wpex-border-b wpex-border-solid wpex-border-current',
				'submit_class' => 'wpex-unstyled-button wpex-hidden',
				'submit_text'  => '',
			] ); ?>
			<?php
			/**
			 * Hook: wpex_hook_mobile_menu_bottom.
			 */
			Mobile_Menu::hook_bottom(); ?>
		</div>
	</div>
</div>
