<?php declare(strict_types=1);

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

final class Plugin {
	
	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		require_once \TTC_PLUGIN_DIR_PATH . 'inc/functions/helpers.php';
		require_once \TTC_PLUGIN_DIR_PATH . 'inc/functions/deprecated.php';
		require_once \TTC_PLUGIN_DIR_PATH . 'inc/autoloader.php'; // must load last.

		\add_action( 'after_setup_theme', [ self::class, 'init_components' ] );
		\add_action( 'init', [ self::class, 'maybe_flush_rewrite_rules' ], 50 );
	}

	/**
	 * Initialize all plugin components.
	 */
	public static function init_components(): void {

		// Don't load on older versions of Total to prevent errors with customers potentially downgrading
		// the theme but not the plugin.
		if ( \defined( 'WPEX_THEME_VERSION' ) && \version_compare( '5.6.0', \WPEX_THEME_VERSION, '>' ) ) {
			return;
		}

		// Demo importer.
		if ( \get_theme_mod( 'demo_importer_enable', true ) ) {
			require_once \TTC_PLUGIN_DIR_PATH . 'inc/demo-importer/plugin.php';
		}

		// Register theme templates.
		if ( \get_theme_mod( 'wpex_templates_enable', true ) ) {
			\totalthemecore_init_class( 'Dynamic_Templates' );
		}

		// Color Palette.
		if ( \get_theme_mod( 'color_palette_enable', true ) ) {
			require_once \TTC_PLUGIN_DIR_PATH . 'inc/lib/wpex-color-palette/class-wpex-color-palette.php';
		}

		// Font Manager.
		if ( \get_theme_mod( 'font_manager_enable', true ) && \defined( 'TOTAL_THEME_ACTIVE' ) ) {
			require_once \TTC_PLUGIN_DIR_PATH . 'inc/lib/wpex-font-manager/class-wpex-font-manager.php';
		}

		// Register Custom shortcodes.
		\totalthemecore_init_class( 'Register_Shortcodes' );

		// Shortcodes Editor Button.
		if ( \get_theme_mod( 'editor_shortcodes_enable', true ) ) {
			totalthemecore_init_class( 'Mce_Buttons' );
		}

		// Custom Widgets.
		if ( \get_theme_mod( 'custom_widgets_enable', true ) ) {
			\totalthemecore_init_class( 'Register_Widgets' );
		}

		// Widget Areas.
		if ( \get_theme_mod( 'widget_areas_enable', true ) ) {
			require_once TTC_PLUGIN_DIR_PATH . 'inc/lib/wpex-widget-areas/class-wpex-widget-areas.php';
		}

		// Total exclusives.
		if ( \defined( 'TOTAL_THEME_ACTIVE' ) ) {

			// Vcex Shortcodes.
			if ( \get_theme_mod( 'extend_visual_composer', true ) ) {
				totalthemecore_init_class( 'Vcex' );
			}

			// WPBakery integration.
			if ( \class_exists( '\Vc_Manager', false ) ) {
				\totalthemecore_init_class( 'WPBakery' );
			}

			// Templatera Integration.
			if ( class_exists( '\VcTemplateManager', false ) ) {
				\totalthemecore_init_class( 'Templatera' );
			}

		}

		// Admin only classes.
		if ( \is_admin() ) {

			if ( \get_theme_mod( 'custom_attachment_fields', true ) ) {
				\totalthemecore_init_class( 'Meta\Attachment_Settings' );
			}

			if ( \apply_filters( 'wpex_metaboxes', true ) ) {
				\totalthemecore_init_class( 'Meta\Main_Metabox' );
			}

			if ( \apply_filters( 'wpex_card_metabox', \get_theme_mod( 'card_metabox_enable', true ) ) ) {
				\totalthemecore_init_class( 'Cards\Meta' );
			}

			if ( \apply_filters( 'wpex_add_user_social_options', true ) ) {
				\totalthemecore_init_class( 'Meta\User_Settings' );
			}

			if ( \get_theme_mod( 'gallery_metabox_enable', true ) ) {
				\totalthemecore_init_class( 'Meta\Gallery_Metabox' );
			}

			\totalthemecore_init_class( 'Meta\Term_Settings' );
		}

		// Term Colors.
		if ( \get_theme_mod( 'term_colors_enable', true ) ) {
			\totalthemecore_init_class( 'Term_Colors' );
		}

		// Term Thumbnails.
		if ( \get_theme_mod( 'term_thumbnails_enable', true ) ) {
			\totalthemecore_init_class( 'Term_Thumbnails' );
		}

		// Category settings.
		\totalthemecore_init_class( 'Meta\Category_Settings' );

		// Portfolio post type.
		if ( \get_theme_mod( 'portfolio_enable', true ) ) {
			\totalthemecore_init_class( 'Cpt\Portfolio' );
		}

		// Staff post type.
		if ( \get_theme_mod( 'staff_enable', true ) ) {
			\totalthemecore_init_class( 'Cpt\Staff' );
		}

		// Testimonials post type.
		if ( \get_theme_mod( 'testimonials_enable', true ) ) {
			\totalthemecore_init_class( 'Cpt\Testimonials' );
		}

		// Post series.
		if ( \get_theme_mod( 'post_series_enable', true ) ) {
			\totalthemecore_init_class( 'Post_Series' );
		}

		// Cards builder.
		if ( \get_theme_mod( 'card_builder_enable', true ) ) {
			\totalthemecore_init_class( 'Cards\Builder' );
		}

		// Custom CSS panel.
		if ( \defined( '\TOTAL_THEME_ACTIVE' ) && \get_theme_mod( 'custom_css_enable', true ) ) {
			\totalthemecore_init_class( 'CSS_Panel' );
		}

	}

	/**
	 * Maybe flush rewrite rules.
	 */
	public static function maybe_flush_rewrite_rules(): void {
		if ( \get_option( 'ttc_flush_rewrite_rules_flag' ) ) {
			\flush_rewrite_rules();
			\delete_option( 'ttc_flush_rewrite_rules_flag' );
		}
	}

}

Plugin::init();
