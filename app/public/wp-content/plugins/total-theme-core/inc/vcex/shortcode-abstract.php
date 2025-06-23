<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

abstract class Shortcode_Abstract {

	/**
	 * Holds aray of scripts to register.
	 */
	protected $scripts = [];

	/**
	 * Holds array of styles to register.
	 */
	protected $styles = [];

	/**
	 * Returns shortcode title.
	 */
	abstract public static function get_title();

	/**
	 * Returns shortcode description.
	 */
	abstract public static function get_description();

	/**
	 * Returns shortcode parameters.
	 */
	abstract public static function get_params_list();

	/**
	 * Constructor
	 */
	public function __construct() {

		// Register shortcode with WP unless it's an alias.
		if ( 'staff_social' !== static::TAG ) {
			\add_shortcode( static::TAG, [ static::class, 'output' ] );
		}

		// Register scripts.
		if ( $this->scripts ) {
			$this->scripts();
		}

		// Register styles.
		if ( $this->styles ) {
			$this->styles();
		}

		// Register the shortcode with WPBakery.
		if ( \function_exists( 'vc_lean_map' ) ) {
			\add_action( 'vc_after_mapping', [ static::class, 'on_vc_after_mapping' ] );
		}
	}

	/**
	 * Register scripts.
	 */
	protected function scripts() {
		foreach ( $this->scripts as $script_args ) {
			\totalthemecore_call_non_static( 'TotalThemeCore\Vcex\Scripts', 'register_script', $script_args );
		}
	}

	/**
	 * Register styles.
	 */
	protected function styles() {
		foreach ( $this->styles as $style_args ) {
			\totalthemecore_call_non_static( 'TotalThemeCore\Vcex\Scripts', 'register_style', $style_args );
		}
	}

	/**
	 * Style dependencies.
	 */
	public static function get_style_depends(): array {
		return [];
	}

	/**
	 * Script dependencies.
	 */
	public static function get_script_depends(): array {
		return [];
	}

	/**
	 * Enqueue style.
	 */
	protected static function enqueue_styles( array $atts ): void {
		foreach ( static::get_style_depends() as $style ) {
			\wp_enqueue_style( $style );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	protected static function enqueue_scripts( array $atts ): void {
		foreach ( static::get_script_depends() as $script ) {
			\wp_enqueue_script( $script );
		}
	}

	/**
	 * Return the shortcode parameters.
	 */
	public static function get_params(): array {
		return (array) \apply_filters( 'vcex_shortcode_params', static::get_params_list(), static::TAG );
	}

	/**
	 * Shortcode callback function.
	 */
	public static function output( $atts, $content = null, $shortcode_tag = '' ): ?string {
		$shortcode_tag = $shortcode_tag ?: static::TAG;

		if ( ! \vcex_maybe_display_shortcode( $shortcode_tag, $atts ) ) {
			return null;
		}
	
		if ( \method_exists( static::class, 'shortcode_atts' ) ) {
			$atts = static::shortcode_atts( $atts );
		} else {
			$atts = \vcex_shortcode_atts( $shortcode_tag, $atts, static::class );
		}

		// @todo add ability to define extra CSS to pass via $shortcode_css->add_css_to_array()
		if ( \class_exists( '\TotalThemeCore\Vcex\Shortcode_CSS' ) ) {
			$shortcode_css = new Shortcode_CSS( static::class, (array) $atts );
			if ( $shortcode_css && \method_exists( static::class, 'css_pre_render' ) ) {
				static::css_pre_render( $shortcode_css, $atts );
			}
			$shortcode_style = $shortcode_css->render_style( false );
			if ( $shortcode_style ) {
				$atts['vcex_class'] = $shortcode_css->get_unique_classname();
			}
		}

		\ob_start();
			\do_action( 'vcex_shortcode_before', $shortcode_tag, $atts );
			include \vcex_get_shortcode_template( $shortcode_tag );
			\do_action( 'vcex_shortcode_after', $shortcode_tag, $atts );
		$html = (string) \ob_get_clean();

		if ( $html ) {
			static::enqueue_styles( $atts );
			static::enqueue_scripts( $atts );
			if ( ! empty( $shortcode_style ) ) {
				$html = "<style>{$shortcode_style}</style>{$html}";
			}
		}

		return $html;
	}

	/**
	 * Return parameter description.
	 */
	protected static function param_description( string $type ): ?string {
		if ( \wp_validate_boolean( \get_theme_mod( 'wpb_param_desc_enabled', true ) ) ) {
			return \vcex_shortcode_param_description( $type );
		} else {
			return '';
		}
	}

	/**
	 * Runs on the vc_after_mapping_hook.
	 */
	public static function on_vc_after_mapping() {
		\vc_lean_map( static::TAG, [ static::class, 'vc_lean_map' ] );

		if ( ( \is_admin() || \wp_doing_ajax() ) && isset( $_POST['action'] ) ) {
			if ( 'vc_edit_form' === $_POST['action'] ) {
				if ( \method_exists( static::class, 'vc_edit_form_fields_attributes' ) ) {
					\add_filter( 'vc_edit_form_fields_attributes_' . static::TAG, [ static::class, 'vc_edit_form_fields_attributes' ] );
				} elseif ( method_exists( static::class, 'parse_deprecated_attributes' ) ) {
					\add_filter( 'vc_edit_form_fields_attributes_' . static::TAG, [ static::class, 'parse_deprecated_attributes' ] );
				}
			}
			if ( \in_array( $_POST['action'], [ 'vc_get_autocomplete_suggestion', 'vc_edit_form' ], true )
				&& \method_exists( static::class, 'register_vc_autocomplete_hooks' )
			) {
				static::register_vc_autocomplete_hooks();
			}
		}
	}

	/**
	 * Callback method for the vc_lean_map function.
	 */
	public static function vc_lean_map(): array {
		$params = [];

		if ( \method_exists( static::class, 'get_params' ) ) {
			$params = (array) static::get_params();
			foreach ( $params as $param_k => $param_v ) {
				// Remove empty params
				if ( ! $param_v ) {
					unset( $params[ $param_k ] );
				}
				// Remove non wpbakery params.
				if ( isset( $param_v['editors'] ) && ! in_array( 'wpbakery', $param_v['editors'], true ) ) {
					unset( $params[ $param_k ] );
					continue; // very important!
				} else {
					// Fix some debug errors in WPB @todo remove once they update the plugin.
					if ( ! isset( $params[ $param_k ]['value'] )
						&& isset( $param_v['type'] )
						&& \in_array( $param_v['type'], [ 'attach_image', 'attach_images', 'hidden', 'exploded_textarea', 'iconpicker' ], true )
					) {
						$params[ $param_k ]['value'] = '';
					}
					// Remove Total specific params to prevent any possible conflicts.
					unset( $params[ $param_k ]['elementor'] );
					unset( $params[ $param_k ]['editors'] );
					unset( $params[ $param_k ]['css'] );
				}
			}
			$params = \array_values( $params ); // fixes keys after unsetting - !!! very important !!!
		}

		$settings = [
			'base'        => static::TAG,
			'name'        => static::get_title(),
			'description' => static::get_description(),
			'category'    => \vcex_shortcodes_branding(),
			'params'      => $params,
			'icon'        => 'vcex_element-icon vcex_element-icon--' . static::TAG,
		];

		if ( \method_exists( static::class, 'get_vc_lean_map_settings' ) ) {
			$custom_settings = static::get_vc_lean_map_settings();
			if ( $custom_settings ) {
				$settings = \wp_parse_args( $custom_settings, $settings );
			}
			if ( isset( $settings['admin_enqueue_js'] ) && \is_admin() ) {
				$admin_view = $settings['admin_enqueue_js'];
				if ( ! \str_ends_with( $admin_view, '.js' ) ) {
					$settings['admin_enqueue_js'] = \vcex_get_js_file( "admin/wpbakery/views/{$admin_view}" );
				}
			}
		}

		return (array) $settings;
	}

}
