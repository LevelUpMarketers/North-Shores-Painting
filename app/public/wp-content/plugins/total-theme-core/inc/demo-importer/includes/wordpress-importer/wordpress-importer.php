<?php
/**
 * Modified WordPress Importer.
 * 
 * @version 0.8.2
 */

if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
	return;
}

/** Disable verbose errors */
if ( ! defined( 'IMPORT_DEBUG' ) ) {
	define( 'IMPORT_DEBUG', false );
}

// Import file.
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( ! \class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( \file_exists( $class_wp_importer ) ) {
		require_once $class_wp_importer;
	}
}

// Parsers.
if ( ! class_exists( 'WXR_Parser' ) ) {
	require_once dirname( __FILE__ ) . '/parsers/class-wxr-parser.php';
}

if ( ! class_exists( 'WXR_Parser_SimpleXML' ) ) {
	require_once dirname( __FILE__ ) . '/parsers/class-wxr-parser-simplexml.php';
}

if ( ! class_exists( 'WXR_Parser_XML' ) ) {
	require_once dirname( __FILE__ ) . '/parsers/class-wxr-parser-xml.php';
}

if ( ! class_exists( 'WXR_Parser_Regex' ) ) {
	require_once dirname( __FILE__ ) . '/parsers/class-wxr-parser-regex.php';
}

// Custom Import Class.
require_once dirname( __FILE__ ) . '/class-totaltheme-wp-import.php';

/**** TOTAL THEME ****/
function total_theme_core_importer_init() {
	load_plugin_textdomain( 'wordpress-importer' );
	$GLOBALS['wp_import'] = new TotalTheme_WP_Import();
	register_importer( 'totaltheme', 'TotalTheme', esc_html__( 'Import <strong>posts, pages, comments, custom fields, categories, and tags</strong> from a WordPress export file.', 'wordpress-importer' ), array( $GLOBALS['wp_import'], 'dispatch' ) );
}
add_action( 'admin_init', 'total_theme_core_importer_init' );