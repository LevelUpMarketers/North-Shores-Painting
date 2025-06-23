<?php

namespace TotalTheme;

use TotalTheme\Helpers\Add_Template;

\defined( 'ABSPATH' ) || exit;

/**
 * Registers ajax callbacks.
 */
class Register_AJAX_Callbacks {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Hook into actions and filters.
	 */
	public static function init() {
		\totaltheme_init_class( 'TotalTheme\Search\Ajax' );

		\add_action( 'wp_ajax_wpex_ajax_load_more', 'TotalTheme\Pagination\Load_More::get_posts' );
		\add_action( 'wp_ajax_nopriv_wpex_ajax_load_more', 'TotalTheme\Pagination\Load_More::get_posts' );

		if ( \is_admin() ) {
			\add_action( 'wp_ajax_totaltheme_add_template', [ self::class, 'add_template' ] );
			\add_action( 'wp_ajax_totaltheme_icon_select_get_icon', [ self::class, 'icon_select_get_icon' ] );
		}
	}

	/**
	 * Adds a new template.
	 */
	public static function add_template(): void {
		if ( ! \current_user_can( 'publish_pages' ) || empty( $_POST['post_title'] ) || ! \post_type_exists( 'wpex_templates' ) ) {
			\wp_die();
		}

		\check_ajax_referer( Add_Template::NONCE, 'nonce' );

		$result = [
			'success' => 0
		];

		$type = $_POST['type'] ?? '';

		$template_id = (new Add_Template( $_POST['post_title'], $type))->template_id;

		if ( $template_id ) {
			$result['success'] = 1;
			$result['template_id'] = \absint( $template_id );
		//	$result['edit_url'] = \esc_url( get_edit_post_link( $template_id ) );
		}

		echo \wp_json_encode( $result );

		\wp_die();
	}

	/**
	 * Adds a new template.
	 */
	public static function icon_select_get_icon(): void {
		\check_ajax_referer( 'totaltheme_icon_select', 'nonce' );
		if ( ! empty( $_POST['icon'] ) ) {
			echo \totaltheme_get_icon( $_POST['icon'] );
		}
		\wp_die();
	}

}
