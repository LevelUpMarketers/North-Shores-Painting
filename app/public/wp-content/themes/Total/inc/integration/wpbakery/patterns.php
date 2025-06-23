<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

final class Patterns {

	/**
	 * Template Type Name.
	 */
	public const TEMPLATE_TYPE = 'totaltheme_patterns';

	/**
	 * Init.
	 */
	public static function init() {
		if ( ! self::is_enabled() ) {
			return;
		}
		
		if ( \is_admin() || self::is_loading_pattern() ) {
			\add_action( 'vc_get_all_templates', [ self::class, 'register_templates' ] );
			\add_action( 'vc_templates_render_category', [ self::class, 'render_patterns_tab' ] );
			\add_action( 'vc_templates_render_backend_template', [ self::class, 'render_backend_pattern' ], 10, 2 );
			\add_action( 'vc_templates_render_frontend_template', [ self::class, 'render_frontend_pattern' ], 10, 2 );
			\add_action( 'vc_frontend_editor_enqueue_js_css', [ self::class, 'enqueue_scripts' ] );
			\add_action( 'vc_backend_editor_enqueue_js_css', [ self::class, 'enqueue_scripts' ] );
		}
	}

	/**
	 * Checks if the functionality is enabled.
	 */
	protected static function is_enabled(): bool {
		return \wp_validate_boolean( \get_theme_mod( 'section_templates_enable', true ) );
	}

	/**
	 * Checks if currently inserting a pattern via ajax.
	 */
	protected static function is_loading_pattern(): bool {
		return \function_exists( 'vc_post_param' ) && \in_array( \vc_post_param( 'action' ), [ 'vc_frontend_load_template', 'vc_backend_load_template' ], true );
	}

	/**
	 * Returns current category name.
	 */
	public static function get_category_name() {
		return \esc_html__( 'Patterns', 'total' );
	}

	/**
	 * Returns current category name.
	 */
	public static function get_category_description() {
		return \esc_html__( 'Append a pattern to the current layout.', 'total' );
	}

	/**
	 * Register templates.
	 */
	public static function register_templates( $data ) {
		$templates = self::get_patterns();

		if ( ! $templates ) {
			return $data;
		}

		$total_cat = [
			'category'             => self::TEMPLATE_TYPE,
			'category_name'        => self::get_category_name(),
			'category_description' => self::get_category_description(),
			'category_weight'      => \apply_filters( 'totaltheme/integration/wpbakery/patterns/category_weight', 11 ),
		];

		$total_cat_templates = [];

		$count = 0;
		foreach ( $templates as $template_id => $template_data ) {
			$count ++;
			$total_cat_templates[] = [
				'unique_id' => $template_id,
				'name'      => $template_data['name'],
				'type'      => self::TEMPLATE_TYPE,
				'content'   => $template_data['content'],
				'weight'    => $count,
			];
		}

		$total_cat['templates'] = $total_cat_templates;

		$data[] = $total_cat;

		return $data;
	}

	/**
	 * Returns patterns list.
	 */
	protected static function get_patterns(): array {
		$patterns      = [];
		$theme_uri     = \untrailingslashit( \WPEX_THEME_URI );
		$ph_location   = "{$theme_uri}/inc/integration/wpbakery/patterns/placeholders/";
		$ph_logo       = "{$ph_location}logo.svg";
		$ph_logo_white = "{$ph_location}logo-dark.svg";
		$ph_landscape  = "{$ph_location}landscape.png";
		$ph_portrait   = "{$ph_location}portrait.png";
		$ph_square     = "{$ph_location}square.png";
		$banner_728x90 = "{$ph_location}banner_728x90.png";
		foreach ( self::get_categories() as $key => $val ) {
			$file = \WPEX_INC_DIR . "integration/wpbakery/patterns/categories/{$key}.php";
			if ( \file_exists( $file ) ) {
				require $file;
			}
		}
		$patterns = \apply_filters( 'wpex_wpbakery_section_templates', $patterns );
		return (array) \apply_filters( 'totaltheme/integration/wpbakery/patterns/list', $patterns );
	}

	/**
	 * Returns pattern category.
	 */
	protected static function get_pattern_category( string $template_id = '' ): string {
		$category = \trim( \preg_replace( '/[^a-z]/', '', $template_id ) );
		if ( 'calltoaction' === $category ) {
			$category = 'call-to-action';
		}
		return $category;
	}

	/**
	 * Wrapper for vc_slufify()
	 */
	protected static function slugify( $str = '' ) {
		if ( \function_exists( 'vc_slugify' ) ) {
			return \vc_slugify( $str );
		}
		$str = \strtolower( $str );
		$str = \html_entity_decode( $str );
		$str = \preg_replace( '/[^\w ]+/', '', $str );
		$str = \preg_replace( '/ +/', '-', $str );
		return $str;
	}

	/**
	 * Get patterns JSON.
	 */
	protected static function get_patterns_json() {
		$patterns_data = [];
		$theme_uri = \untrailingslashit( \WPEX_THEME_URI );

		foreach ( self::get_patterns() as $pattern_id => $pattern_data ) {
			$pattern = [
				'id'         => $pattern_id,
				'unique_id'  => $pattern_id,
				'id_hash'    => \md5( $pattern_id ),
				'label'      => $pattern_data['name'],
				'name'       => self::slugify( $pattern_data['name'] ),
				'category'   => self::get_pattern_category( $pattern_id ),
				'screenshot' => $pattern_data['screenshot'] ?? "{$theme_uri}/inc/integration/wpbakery/patterns/categories/thumbnails/{$pattern_id}.webp",
			];
			$pattern = \array_map( '\esc_attr', $pattern );
			$patterns_data[] = $pattern;
		}

		return \wp_json_encode( $patterns_data );
	}

	/**
	 * Render the patterns tab.
	 */
	public static function render_patterns_tab( $category ) {
		if ( self::TEMPLATE_TYPE === $category['category'] ) {
			$category['output'] = self::get_vc_tab_template();
		}
		return $category;
	}

	/**
	 * Get categories.
	 */
	protected static function get_categories(): array {
		$categories = [
			'header'         => \esc_html__( 'Header', 'total' ),
			'hero'           => \esc_html__( 'Hero', 'total' ),
			'features'       => \esc_html__( 'Features', 'total' ),
			'statistics'     => \esc_html__( 'Statistics', 'total' ),
			'team'           => \esc_html__( 'Team', 'total' ),
			'call-to-action' => \esc_html__( 'Call to Action', 'total' ),
			'faq'            => \esc_html__( 'FAQ', 'total' ),
			'subscribe'      => \esc_html__( 'Subscribe', 'total' ),
			'pricing'        => \esc_html__( 'Pricing', 'total' ),
			'contact'        => \esc_html__( 'Contact', 'total' ),
			'footer'         => \esc_html__( 'Footer', 'total' ),
		];
		$categories =\apply_filters_deprecated(
			'wpex_wpbakery_section_templates_categories',
			[ $categories ],
			'Total 6.0',
			'totaltheme/integration/wpbakery/patterns/categories'
		);
		return (array) \apply_filters( 'totaltheme/integration/wpbakery/patterns/categories', $categories );
	}

	/**
	 * Renders the items for the patterns tab.
	 *
	 * @param $template
	 * @return string
	 */
	protected static function get_vc_tab_template() {
		\ob_start();
			require_once \WPEX_INC_DIR . 'integration/wpbakery/patterns/category.tpl.php';
		return \ob_get_clean();
	}

	/**
	 * Renders the items for the patterns tab.
	 *
	 * @param $template
	 * @return string
	 */
	protected static function render_patterns_tab_item( $template ) {
		$name                = $template['name'];
		$template_id         = $template['unique_id'];
		$template_id_hash    = \md5( $template_id );
		$template_name       = $name;
		$template_name_lower = \function_exists( 'vc_slugify' ) ? \vc_slugify( $template_name ) : '';
		$template_type       = self::TEMPLATE_TYPE;
		$template_category   = self::get_pattern_category( $template_id );
		$theme_uri = \untrailingslashit( \WPEX_THEME_URI );
		$preview_image = "{$theme_uri}/inc/integration/wpbakery/patterns/categories/thumbnails/{$template_id}.png";

			$output .= '<div class="wpex-vc-template-list__item"
						data-template_id="' . \esc_attr( $template_id ) . '"
						data-template_id_hash="' . \esc_attr( $template_id_hash ) . '"
						data-category="' . \esc_attr( $template_type ) . '"
						data-template_unique_id="' . \esc_attr( $template_id ) . '"
						data-template_name="' . \esc_attr( $template_name_lower ) . '"
						data-template_type="' . \esc_attr( $template_type ) . '"
						data-wpex-category="' . \esc_attr( $template_category ) . '"
					>';

			if ( $preview_image ) {
				$output .= '<div class="wpex-vc-template-list__image"><img loading="lazy"  src="' . esc_url( $preview_image ) . '"></div>';
			}

			$output .= '<div class="wpex-vc-template-list__overlay">';
			$output .= '<div class="wpex-vc-template-list__name">' . \esc_html( $template_name ) . '</div>';
				$output .= '<div class="wpex-vc-template-list__actions">';
					$output .= '<a href="https://totalwptheme.com/sections/wpbakery/' . \esc_attr( $template_id ) . '" class="button button-primary" target="_blank" rel="nofollow noopener noreferrer">' . \esc_html__( 'Preview', 'total' ) . '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 6.5c3.79 0 7.17 2.13 8.82 5.5-1.65 3.37-5.02 5.5-8.82 5.5S4.83 15.37 3.18 12C4.83 8.63 8.21 6.5 12 6.5m0-2C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 5c1.38 0 2.5 1.12 2.5 2.5s-1.12 2.5-2.5 2.5-2.5-1.12-2.5-2.5 1.12-2.5 2.5-2.5m0-2c-2.48 0-4.5 2.02-4.5 4.5s2.02 4.5 4.5 4.5 4.5-2.02 4.5-4.5-2.02-4.5-4.5-4.5z"/></svg></a>';
					$output .= '<button type="button" class="wpex-vc-template-list__insert button button-primary" data-template-handler="">' . \esc_html__( 'Insert', 'total' ) . '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><g><rect fill="none" height="24" width="24"/></g><g><path d="M18,15v3H6v-3H4v3c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-3H18z M17,11l-1.41-1.41L13,12.17V4h-2v8.17L8.41,9.59L7,11l5,5 L17,11z"/></g></svg></button></div>';
				$output .= '</div>';
			$output .= '</div>';

		return $output;

	//	return '<script type="text/html" id="vc_template-item">' . $output . '</script>';
	}

	/**
	 * Render pattern for the backend editor.
	 */
	public static function render_backend_pattern( $pattern_id, $template_type ) {
		if ( self::TEMPLATE_TYPE === $template_type ) {
			$pattern_content = (string) self::get_patterns()[$pattern_id]['content'] ?? '';
			if ( $pattern_content ) {
				return \trim( $pattern_content );
			}
		}
		return $pattern_id;
	}

	/**
	 * Renders the pattern for the frontend editor.
	 */
	public static function render_frontend_pattern( $pattern_id, $template_type ) {
		if ( self::TEMPLATE_TYPE === $template_type ) {
			$patterns = self::get_patterns();
			$pattern_content = (string) self::get_patterns()[$pattern_id]['content'] ?? '';
			if ( $pattern_content ) {
				\vc_frontend_editor()->setTemplateContent( \trim( $pattern_content ) );
				\vc_frontend_editor()->enqueueRequired();
				\vc_include_template( 'editors/frontend_template.tpl.php', [
					'editor' => \vc_frontend_editor(),
				] );
				die(); // important wp_die() causes the page to break - can't use.
			}
			\wp_send_json_error( [
				'code' => 'Wrong ID or no Template found #3',
			] );
		}
		return $pattern_id;
	}

	/**
	 * Enqueues the Patterns JS.
	 *
	 */
	public static function enqueue_scripts() {
		\wp_enqueue_script(
			'totaltheme-wpbakery-patterns',
			\totaltheme_get_js_file( 'admin/wpbakery/patterns' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION,
			true
		);
	}

}
