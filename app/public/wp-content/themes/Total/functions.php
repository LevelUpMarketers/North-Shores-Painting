<?php
/**
 * Total WordPress Theme.
 *
 * Theme URI     : https://totalwptheme.com/
 * Documentation : https://totalwptheme.com/docs/
 * License       : https://themeforest.net/licenses/terms/regular
 */

defined( 'ABSPATH' ) || exit;

/**
 * Define theme constants.
 */

// TotalTheme version.
define( 'TOTAL_THEME_ACTIVE', true );
define( 'WPEX_THEME_VERSION', '6.3' );

// Supported Bundled plugin versions.
define( 'WPEX_VC_SUPPORTED_VERSION', '8.4.1' );
define( 'WPEX_THEME_CORE_PLUGIN_SUPPORTED_VERSION', '2.3' );

// Theme Branding.
define( 'WPEX_THEME_BRANDING', get_theme_mod( 'theme_branding', 'Total' ) );

// Theme changelog URL.
define( 'WPEX_THEME_CHANGELOG_URL', 'https://totalwptheme.com/docs/changelog/' );

// Theme directory location and URL.
define( 'WPEX_THEME_DIR', get_template_directory() );
define( 'WPEX_THEME_URI', get_template_directory_uri() );

// Theme Panel slug and hook prefix.
define( 'WPEX_THEME_PANEL_SLUG', 'wpex-panel' );
define( 'WPEX_ADMIN_PANEL_HOOK_PREFIX', 'theme-panel_page_' . WPEX_THEME_PANEL_SLUG );

// Includes folder.
define( 'WPEX_INC_DIR', WPEX_THEME_DIR . '/inc/' );

// Check if js minify is enabled.
define( 'WPEX_MINIFY_JS', 'deprecated' );

// Theme stylesheet and main javascript handles.
define( 'WPEX_THEME_STYLE_HANDLE', 'wpex-style' );
define( 'WPEX_THEME_JS_HANDLE', 'wpex-core' );

// Check if certain plugins are enabled.
define( 'WPEX_VC_ACTIVE', class_exists( 'Vc_Manager', false ) );
define( 'WPEX_WPML_ACTIVE', class_exists( 'SitePress', false ) );
define( 'WPEX_POLYLANG_ACTIVE', class_exists( 'Polylang', false ) );

/**
 * Register autoloader.
 */
require_once WPEX_THEME_DIR . '/inc/autoloader.php';

/**
 * All the magic happens here.
 */
if ( class_exists( 'TotalTheme\Initialize' ) ) {
	TotalTheme\Initialize::instance();
}
