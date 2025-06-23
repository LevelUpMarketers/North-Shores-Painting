<?php

namespace TotalThemeCore\Shortcodes;

\defined( 'ABSPATH' ) || exit;

class Shortcode_Wpex_Template {

	/**
	 * Shortcode tag.
	 */
	public const TAG = 'wpex_template';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( ! \shortcode_exists( 'wpex_template' ) ) {
			\add_shortcode( self::TAG, [ self::class, 'output' ] );
		}
		if ( ! \shortcode_exists( 'templatera' ) ) {
			\add_shortcode( 'templatera', [ self::class, 'output' ] );
		}
		if ( \function_exists( '\vc_lean_map' ) ) {
			\add_action( 'vc_after_mapping', [ $this, 'on_vc_after_mapping' ] );
		}
	}

	/**
	 * Shortcode title.
	 */
	public static function get_title() {
		return \esc_html__( 'Template Part', 'total-theme-core' );
	}

	/**
	 * Shortcode output.
	 */
	public static function output( $atts ): string {
		$atts = \shortcode_atts( [
			'el_class'   => '',
			'id'         => '',
			'visibility' => '',
		], $atts );

		$id = $atts['id'] ?? null;
		$post_type = \get_post_type( $id );

		if ( ! $id || ! \in_array( $post_type, [ 'templatera', 'wpex_templates' ] ) ) {
			return '';
		}

		if ( \function_exists( '\wpex_parse_obj_id' ) ) {
			$id = \wpex_parse_obj_id( $id, 'wpex_templates' );
		}

		if ( 'publish' !== \get_post_status( $id ) ) {
			return '';
		}

		$content = \get_post_field( 'post_content', $id );

		if ( ! $content || \str_contains( $content, 'vcex_post_content' ) ) {
			return '';
		}

		$class = ( 'templatera' == $post_type ) ? 'templatera_shortcode' : 'wpex-template-shortcode';

		if ( ! empty( $atts['visibility'] )
			&& \function_exists( '\totaltheme_get_visibility_class' )
			&& $visibility_class = totaltheme_get_visibility_class( $atts['visibility'] )
		) {
			$class .= " {$visibility_class}";
		}

		if ( ! empty( $atts['el_class'] ) && $el_class_safe = \esc_attr( $atts['el_class'] ) ) {
			$class .= " {$el_class_safe}";
		}

		$html = '<div class="' . esc_attr( $class ) . '">';
			\ob_start();
				if ( \function_exists( '\vc_modules_manager' ) && \vc_modules_manager()->is_module_on( 'vc-custom-css' ) ) {
					\vc_modules_manager()->get_module( 'vc-custom-css' )->output_custom_css_to_page( $id );
				}
				if ( \function_exists( '\visual_composer' ) && \is_callable( [ \visual_composer(), 'addShortcodesCss' ] ) ) {
					\visual_composer()->addShortcodesCss( $id );
				}
			$html .= \ob_get_clean();
			if ( \function_exists( '\totaltheme_replace_vars' ) ) {
				$content = \totaltheme_replace_vars( $content );
			}
			if ( \function_exists( '\totaltheme_get_post_builder_type' ) ) {
				$template_type = \totaltheme_get_post_builder_type( $id );
				if ( 'elementor' === $template_type && function_exists( 'wpex_get_elementor_content_for_display' ) ) {
					$html .= \wpex_get_elementor_content_for_display( $template_id );
				} elseif ( \function_exists( '\totaltheme_render_content' ) ) {
					$html .= \totaltheme_render_content( $content );
				} else {
					$html .= \do_shortcode( \wp_kses_post( $content ) );
				}
			} else {
				$html .= \do_shortcode( $content );
			}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Hook into WPBakery vc_after_mapping hook.
	 */
	public function on_vc_after_mapping() {
		\vc_lean_map( self::TAG, [ $this, 'vc_lean_map' ] );
	}

	/**
	 * Array of shortcode parameters.
	 */
	public static function get_params_list(): array {
		$choices = [
			\esc_html( '— Select —', 'total-theme-core' ) => '',
		];
		if ( \function_exists( '\vc_request_param' )
			&& \in_array( \vc_request_param( 'action' ), [ 'edit', 'vc_edit_form' ], true )
			&& \function_exists( 'totaltheme_call_non_static' )
		) {
			$templates = (array) totaltheme_call_non_static( 'Theme_Builder', 'get_template_choices', 'part', false );
			if ( $templates ) {
				$choices = $choices + \array_flip( $templates ); // can't use array_merge because we need to keep keys.
			}
		}
		return [
			[
				'type'       => 'dropdown',
				'heading'    => \esc_html__( 'Template', 'total-theme-core' ),
				'param_name' => 'id',
				'value'      => $choices,
			],
			[
				'type'       => 'vcex_select',
				'heading'    => \esc_html__( 'Visibility', 'total-theme-core' ),
				'param_name' => 'visibility',
			],
			[
				'type'        => 'textfield',
				'heading'     => \esc_html__( 'Extra class name', 'total-theme-core' ),
				'description' => \function_exists( 'vcex_shortcode_param_description' ) ? \vcex_shortcode_param_description( 'el_class' ) : '',
				'param_name'  => 'el_class',
			],
		];
	}

	/**
	 * Map wpex_template shortcode to WPbakery.
	 */
	public function vc_lean_map(): array {
		return [
			'name'             => self::get_title(),
			'description'      => \esc_html__( 'Insert a dynamic template part.', 'total-theme-core' ),
			'base'             => self::TAG,
			'icon'             => 'vcex_element-icon vcex_element-icon--wpex_template',
			'category'         => \function_exists( 'vcex_shortcodes_branding' ) ? \vcex_shortcodes_branding() : '',
			'params'           => self::get_params_list(),
			'admin_enqueue_js' => \totalthemecore_get_js_file( 'admin/wpbakery/views/template' ),
			'js_view'          => 'wpexTemplateView',
		];
	}

}
