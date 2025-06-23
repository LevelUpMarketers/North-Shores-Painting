<?php

defined( 'ABSPATH' ) || exit;

if ( totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
    require_once WPEX_CUSTOMIZER_DIR . 'settings/general/dark-mode.php';
}

require_once WPEX_CUSTOMIZER_DIR . 'settings/general/page-header-title.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/breadcrumbs.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/theme-heading.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/pages.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/search.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/search-modal.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/social-share.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/pagination.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/load-more.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/next-prev.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/local-scroll.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/scroll-top.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/lightbox.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/overlays.php';
require_once WPEX_CUSTOMIZER_DIR . 'settings/general/post-slider.php';
