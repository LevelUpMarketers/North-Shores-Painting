<?php
/**
 * Plugin Name: Total Theme Core
 * Plugin URI: https://totalwptheme.com/docs/total-theme-core-plugin/
 * Description: Adds core functionality to the Total WordPress theme including post types, shortcodes, builder elements, meta options and more. This is an optional plugin but highly recommended so you don't miss out on functionality.
 * Version: 2.3
 * Author: WPExplorer
 * Author URI: https://www.wpexplorer.com/
 * License: Custom license
 * License URI: http://themeforest.net/licenses/terms/regular
 *
 * Text Domain: total-theme-core
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit;

define( 'TTC_VERSION', '2.3' );
define( 'TTC_MAIN_FILE_PATH', __FILE__ );
define( 'TTC_PLUGIN_DIR_PATH', plugin_dir_path( TTC_MAIN_FILE_PATH ) );
define( 'TTC_PLUGIN_DIR_URL', plugin_dir_url( TTC_MAIN_FILE_PATH ) );

/**
 * Load Total Theme Core textdomain.
 */
function total_theme_core_load_plugin_textdomain() {
	load_plugin_textdomain( 'total-theme-core', false, basename( dirname( TTC_MAIN_FILE_PATH ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'total_theme_core_load_plugin_textdomain' );

/**
 * Run on plugin activation.
 */
function total_theme_core_activation_hook() {
	if ( ! get_option( 'ttc_flush_rewrite_rules_flag' ) ) {
		add_option( 'ttc_flush_rewrite_rules_flag', true );
	}
}
register_activation_hook( TTC_MAIN_FILE_PATH, 'total_theme_core_activation_hook' );

/**
 * Flush Rewrite rules on deactivation.
 */
register_deactivation_hook( TTC_MAIN_FILE_PATH, 'flush_rewrite_rules' );

/**
 * All the magic happens here.
 */
require_once TTC_PLUGIN_DIR_PATH . 'inc/plugin.php';
