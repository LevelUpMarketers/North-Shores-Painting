<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * Register custom widgets.
 */
final class Register_Widgets {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		\add_action( 'widgets_init', [ self::class, 'on_init' ] );
		\add_action( 'admin_print_scripts-widgets.php', [ self::class, 'enqueue_scripts' ] );
	}

	/**
	 * Register custom widgets.
	 */
	public static function on_init() {
		$widgets_list = self::get_widgets_list();

		if ( empty( $widgets_list ) || ! \is_array( $widgets_list ) ) {
			return;
		}

		foreach ( $widgets_list as $custom_widget ) {
			$file = \TTC_PLUGIN_DIR_PATH . "inc/widgets/{$custom_widget}.php";
			if ( \file_exists( $file ) && class_exists( '\TotalThemeCore\WidgetBuilder' ) ) {
				require_once $file;
			}
		}
	}

	/**
	 * Return custom widgets list.
	 */
	protected static function get_widgets_list() {
		$widgets_list = [
			'about'              => 'widget-about',
			'advertisement'      => 'widget-advertisement',
			'newsletter'         => 'widget-newsletter',
			'simple-newsletter'  => 'widget-simple-newsletter',
			'info'               => 'widget-business-info',
			'social-fontawesome' => 'widget-social-profiles',
			'simple-menu'        => 'widget-simple-menu',
			'modern-menu'        => 'widget-modern-menu',
			'facebook-page'      => 'widget-facebook',
			'google-map'         => 'widget-google-map',
			'video'              => 'widget-video',
			'posts-thumbnails'   => 'widget-recent-posts-thumb',
			'posts-grid'         => 'widget-recent-posts-thumb-grid',
			'posts-icons'        => 'widget-recent-posts-icons',
			'users-grid'         => 'widget-users-grid',
			'taxonomy-terms'     => 'widget-taxonomy-terms',
			'comments-avatar'    => 'widget-recent-comments-avatar',
		];

		if ( \function_exists( 'templatera_init' ) || \defined( '\TOTAL_THEME_ACTIVE' ) ) {
			$widgets_list['templatera'] = 'widget-templatera'; // now used for dynamic templates also.
		}

		if ( \class_exists( 'bbPress' ) ) {
			$widgets_list['bbpress-forum-info'] = 'widget-bbPress-forum-info';
			$widgets_list['bbpress-topic-info'] = 'widget-bbPress-topic-info';
		}

		return (array) \apply_filters( 'wpex_custom_widgets', $widgets_list );

	}

	/**
	 * Custom Widgets scripts.
	 */
	public static function enqueue_scripts() {
		\wp_enqueue_style(
			'totalthemecore-admin-custom-widgets',
			\totalthemecore_get_css_file( 'admin/custom-widgets' ),
			[],
			'1.0'
		);

		\wp_enqueue_script(
			'totalthemecore-admin-custom-widgets',
			\totalthemecore_get_js_file( 'admin/custom-widgets' ),
			[ 'jquery' ],
			'1.0',
			true
		);

		\wp_localize_script( 'totalthemecore-admin-custom-widgets', 'wpexCustomWidgets', [
			'confirm' => \esc_html__( 'Do you really want to delete this item?', 'total-theme-core' ),
		] );
	}

}
