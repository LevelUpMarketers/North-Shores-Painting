<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Elementor Configuration Class
 */
final class Elementor {

	/**
	 * The theme font group name.
	 */
	public const FONT_GROUP_ID = 'total';

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		\add_action( 'wp_enqueue_scripts', [ self::class, 'front_css' ] );
		\add_action( 'elementor/theme/register_locations', [ self::class, 'register_locations' ] );
		\add_action( 'elementor/frontend/after_enqueue_scripts', [ self::class, 'editor_scripts' ] );
	//	\add_action( 'elementor/editor/after_enqueue_styles', [ self::class, 'editor_styles' ] );
	//	\add_action( 'elementor/preview/enqueue_styles', [ self::class, 'preview_styles' ] );

		// Custom fonts suppport
		\add_action( 'elementor/fonts/groups',  [ self::class, 'font_groups' ] );
		\add_action( 'elementor/fonts/additional_fonts', [ self::class, 'additional_fonts' ] );
		\add_action( 'elementor/fonts/print_font_links/' . self::FONT_GROUP_ID, [ self::class, 'print_fonts' ] );

		// Theme Icons support
		if ( \totaltheme_call_static( 'Theme_Icons', 'is_enabled' ) ) {
			\add_action( 'elementor/icons_manager/additional_tabs', [ self::class, 'icons_manager_additional_tabs' ] );
		}

		// Container compatibility
		if ( self::has_container_compatibility() ) {
			if ( is_admin() ) {
				\add_action( 'elementor/element/kit/section_settings-layout/after_section_start', [ self::class, '_add_container_compat_notice' ], 10, 2 );
			}
			\add_action( 'body_class', [ self::class, '_add_container_compat_body_class' ] );
		}

		// Remove upsells
		if ( ! \defined( 'ELEMENTOR_PRO_VERSION' ) && \get_theme_mod( 'elementor_remove_upsells', true ) ) {
			\totaltheme_init_class( __CLASS__ . '\Remove_Upsells' );
		}

		// Insert edit links to canvas template
		\add_action( 'elementor/page_templates/canvas/after_content', 'wpex_post_edit' );

		// Set correct post layout for Elementor templates
		\add_filter( 'wpex_post_layout_class', [ self::class, '_filter_post_layout' ] );
	}

	/**
	 * Add notice in the layout tab when container compatibility is enabled.
	 */
	public static function _add_container_compat_notice( $element, $args ): void {
		$element->add_control(
			'totaltheme_container_compat_notice',
			[
				'type' => \Elementor\Controls_Manager::NOTICE,
				'notice_type' => 'warning',
				'dismissible' => false,
				'heading' => esc_html__( 'Theme Notice', 'total' ),
				'content' => esc_html__( 'Container compatibility is enabled. As a result, changes to the "Content Width" and "Container Padding" will not take effect. You can disable this feature in the Theme Panel.', 'total' ),
			]
		);
	}

	/**
	 * Add container compatibility class to the body element.
	 */
	public static function _add_container_compat_body_class( $class ) {
		$class[] = 'wpex-e-con-compat';
		return $class;
	}

	/**
	 * Enqueues front-end CSS.
	 */
	public static function front_css(): void {
		\wp_enqueue_style(
			'wpex-elementor',
			\totaltheme_get_css_file( 'frontend/elementor' ),
			[ 'elementor-frontend' ],
			\WPEX_THEME_VERSION
		);
	}

	/**
	 * Registers Elementor locations.
	 */
	public static function register_locations( $elementor_theme_manager ): void {

		/**
		 * Filters whether the theme should register all core elementor locations via
		 * $elementor_theme_manager->register_all_core_location().
		 */
		$register_core_locations = (bool) \apply_filters( 'total_register_elementor_locations', true );

		if ( $register_core_locations ) {
			$elementor_theme_manager->register_all_core_location();
		}

		$elementor_theme_manager->register_location( 'togglebar', [
			'label'           => \esc_html__( 'Togglebar', 'total' ),
			'multiple'        => true,
			'edit_in_content' => false,
		] );

		$elementor_theme_manager->register_location( 'topbar', [
			'label'           => \esc_html__( 'Top Bar', 'total' ),
			'multiple'        => true,
			'edit_in_content' => false,
		] );

		$elementor_theme_manager->register_location( 'page_header', [
			'label'           => \esc_html__( 'Page Header', 'total' ),
			'multiple'        => true,
			'edit_in_content' => false,
		] );

		$elementor_theme_manager->register_location( 'footer_callout', [
			'label'           => \esc_html__( 'Footer Callout', 'total' ),
			'multiple'        => true,
			'edit_in_content' => false,
		] );

		$elementor_theme_manager->register_location( 'footer_bottom', [
			'label'           => \esc_html__( 'Footer Bottom', 'total' ),
			'multiple'        => true,
			'edit_in_content' => false,
		] );

	}

	/**
	 * Add Theme Font Group.
	 */
	public static function font_groups( $groups ) {
		if ( \wpex_has_registered_fonts() ) {
			return array_merge( [
				self::FONT_GROUP_ID => \esc_html__( 'My Fonts', 'total' ),
			], $groups );
		}
		return $groups;
	}

	/**
	 * Add Theme Font Options.
	 */
	public static function additional_fonts( $additional_fonts ) {
		$user_fonts = [];
		foreach ( \wpex_get_registered_fonts() as $font_name => $font_args ) {
			$user_fonts[ $font_name ] = self::FONT_GROUP_ID;
		}
		return $user_fonts ? array_merge( $user_fonts, $additional_fonts ) : $additional_fonts;
	}

	/**
	 * Enqueue fonts.
	 */
	public static function print_fonts( $font ): void {
		if ( $font ) {
			\wpex_enqueue_font( $font, 'registered' );
		}
	}

	/**
	 * Enqueue JS in the editor.
	 */
	public static function editor_scripts(): void {
		if ( ! \defined( 'VCEX_ELEMENTOR_INTEGRATION' )
			|| ! \class_exists( '\Elementor\Plugin' )
			|| ! \Elementor\Plugin::$instance->preview->is_preview_mode()
		) {
			return;
		}

		\wp_enqueue_script(
			'totaltheme-admin-elementor-preview',
			\totaltheme_get_js_file( 'admin/elementor/preview' ),
			[],
			\WPEX_THEME_VERSION,
			true
		);
	}

	/**
	 * Enqueue CSS in the Editor.
	 */
	public static function editor_styles() {}

	/**
	 * Enqueue CSS for the preview panel.
	 */
	public static function preview_styles() {}

	/**
	 * Adds Theme Icons tab to Icons Manager.
	 */
	public static function icons_manager_additional_tabs( array $tabs ): array {
		$tabs['ticon'] = [
			'name'            => 'ticon',
			'label'           => \esc_html__( 'Theme Icons', 'total' ),
			'prefix'          => 'ticon-',
			'displayPrefix'   => 'ticon',
			'labelIcon'       => 'ticon ticon-totaltheme',
			'ver'             => \WPEX_THEME_VERSION,
			'fetchJson'       => \WPEX_THEME_URI . '/assets/icons/list-elementor.json',
			'native'          => true,
			'render_callback' => [ self::class, 'render_theme_icon' ],
		];
		if ( is_admin() ) {
			// Define URL in admin only to avoid Elementor loading it on the live site
			$tabs['ticon']['url'] = \totaltheme_call_static( 'Theme_Icons', 'get_css_url' );
		}
		return $tabs;
	}

	/**
	 * Callback function for rendering theme icons inside elementor.
	 */
	public static function render_theme_icon( $icon = [], $attributes = [], $tag = 'i' ) {
		if ( empty( $icon['value'] ) || empty( $icon['library'] ) || 'ticon' !== $icon['library'] ) {
			return;
		}
		return \totaltheme_call_static( 'Theme_Icons', 'get_icon', $icon['value'], $attributes );
	}

	/**
	 * Check if a location has a template.
	 */
	public static function location_exists( string $location ): bool {
		return \function_exists( '\elementor_location_exits' ) && \totaltheme_is_integration_active( 'elementor' ) && \elementor_location_exits( $location, true );
	}

	/**
	 * Check if container compatibility is enabled.
	 */
	public static function has_container_compatibility(): bool {
		return \get_theme_mod( 'elementor_container_compat', true );
	}

	/**
	 * Function filter post layout.
	 *
	 * Elementor allows selecting a template for non-page post types.
	 */
	public static function _filter_post_layout( $layout ) {
		if ( \is_singular() ) {
			$page_template = get_page_template_slug();
			if ( 'elementor_canvas' === $page_template || 'elementor_header_footer' === $page_template ) {
				$layout = 'full-screen';
			}
		}
		return $layout;
	}

}
