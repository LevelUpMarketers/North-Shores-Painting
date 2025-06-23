<?php

defined( 'ABSPATH' ) || exit;

$header_is_custom = $header_is_custom ?? totaltheme_call_static( 'Header\Core', 'is_custom' );
$menu_is_custom   = $menu_is_custom ?? totaltheme_call_static( 'Header\Menu', 'is_custom' );

if ( ! $header_is_custom ) {
	require_once WPEX_CUSTOMIZER_DIR . 'settings/header/general.php';
	require_once WPEX_CUSTOMIZER_DIR . 'settings/header/logo.php';
	require_once WPEX_CUSTOMIZER_DIR . 'settings/header/logo-icon.php';
	require_once WPEX_CUSTOMIZER_DIR . 'settings/header/sticky.php';

	if ( ! $menu_is_custom ) {
		require_once WPEX_CUSTOMIZER_DIR . 'settings/header/menu.php';
		require_once WPEX_CUSTOMIZER_DIR . 'settings/header/dropdowns.php';
		require_once WPEX_CUSTOMIZER_DIR . 'settings/header/megamenus.php';
	}

	require_once WPEX_CUSTOMIZER_DIR . 'settings/header/search.php';

	if ( ! $menu_is_custom || \wpex_has_mobile_menu_alt() ) {
		require_once WPEX_CUSTOMIZER_DIR . 'settings/header/mobile-menu.php';
	}
}

require_once WPEX_CUSTOMIZER_DIR . 'settings/header/transparent-header.php';