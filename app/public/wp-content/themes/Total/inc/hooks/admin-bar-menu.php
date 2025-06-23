<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into the admin_bar_menu hook.
 * 
 */
class Admin_Bar_Menu {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $admin_bar ) {
		if ( ! \current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( \is_admin() ) {
			if ( 'post' === \get_current_screen()->base ) {
				$template_id = (int) \totaltheme_call_static( 'Theme_Builder\Post_Template', 'get_template_id' );
			}
		} elseif ( $theme_builder = \totaltheme_get_instance_of( 'Theme_Builder' ) ) {
			$template_id = $theme_builder->get_location_template_id( \is_singular() ? 'single' : 'archive' );
		}

		if ( ! empty( $template_id ) && $edit_template_link = \get_edit_post_link( $template_id ) ) {
			$admin_bar->add_menu( [
				'id'     => 'custom-menu-item',
				'title'  => \esc_html__( 'Edit Template', 'total' ),
				'href'   => \esc_url( $edit_template_link ),
				'parent' => 'top-secondary',
				'group'  => false,
				'meta'   => '',
			] );
		}
	}

}
