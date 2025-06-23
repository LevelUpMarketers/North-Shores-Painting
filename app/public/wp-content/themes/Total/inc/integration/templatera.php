<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Templatera Integration.
 */
class Templatera {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		\add_action( 'init', [ self::class, 'remove_notices' ] );
		\add_filter( 'manage_templatera_posts_columns', [ self::class, 'define_columns' ] );
		\add_action( 'manage_templatera_posts_custom_column', [ self::class, 'columns_display' ], 10, 2 );
		\add_action( 'wp_loaded', [ self::class, 'register_shortcode' ], 50 );
		\add_action( 'vc_after_init', [ self::class, 'remove_parmams' ] );
	}

	/**
	 * Remove params.
	 */
	public static function remove_parmams(): void {
		\vc_remove_param( 'templatera', 'use_template_scope' );
	}

	/**
	 * Remove notices.
	 */
	public static function remove_notices(): void {
		\remove_action( 'admin_notices', 'templatera_notice' );
		\remove_action( 'admin_notices', 'templatera_notice_version' );
	}

	/**
	 * Define new admin dashboard columns.
	 */
	public static function define_columns( $columns ) {
		$columns[ 'wpex_templatera_shortcode' ] = \esc_html__( 'Shortcode', 'total' );
		$columns[ 'wpex_templatera_id' ]        = \esc_html__( 'ID', 'total' );
    	return $columns;
	}

	/**
	 * Display new admin dashboard columns.
	 */
	public static function columns_display( $column, $post_id ): void {
		switch ( $column ) {
			case 'wpex_templatera_shortcode' :
				echo '<input type="text" onClick="this.select();" value=\'[templatera id="' . \esc_attr( \absint( $post_id ) ) . '"]\' readonly>';
			break;
			case 'wpex_templatera_id' :
				echo \esc_html( \absint( $post_id ) );
			break;
		}
	}

	/**
	 * Register templatera shortcode to fix issues with dynamic elements.
	 */
	public static function register_shortcode(): void {
		\add_shortcode( 'templatera', [ self::class, 'add_shortcode' ], 99 );
	}

	/**
	 * New templatera shortcode output.
	 */
	public static function add_shortcode( $atts, $content = '' ) {
		if ( ! \class_exists( '\WPBMap' ) || ! \function_exists( 'visual_composer' ) ) {
			return;
		}

		$id = '';
		$el_class = '';

		\extract( \shortcode_atts( [
			'el_class' => '',
			'id' => '',
		], $atts ) );

		if ( empty( $id ) || ! \in_array( \get_post_type( $id ), [ 'templatera', 'wpex_templates' ] ) ) {
			return;
		}

		/*if ( \function_exists( '\wpex_parse_obj_id' ) ) {
			$id = \wpex_parse_obj_id( $id, 'templatera' );
		}*/

		$content = \get_post_field( 'post_content', $id );

		if ( ! $content ) {
			return;
		}

		$output = '<div class="templatera_shortcode' . ( $el_class ? ' ' . \esc_attr( $el_class ) : '' ) . '">';
			\ob_start();
				if ( \function_exists( '\vc_modules_manager' ) && \vc_modules_manager()->is_module_on( 'vc-custom-css' ) ) {
					\vc_modules_manager()->get_module( 'vc-custom-css' )->output_custom_css_to_page( $id );
				}
				if ( \function_exists( '\visual_composer' ) && \is_callable( [ \visual_composer(), 'addShortcodesCss' ] ) ) {
					\visual_composer()->addShortcodesCss( $id );
				}
			$output .= \ob_get_clean();
			$output .= \do_shortcode( $content );
		$output .= '</div>';

		return $output;
	}

}
