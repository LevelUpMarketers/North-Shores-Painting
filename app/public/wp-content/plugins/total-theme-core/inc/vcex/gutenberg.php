<?php

namespace TotalThemeCore\Vcex;

defined( 'ABSPATH' ) || exit;

/**
 * Gutenberg Integration for vcex shortcodes.
 */
class Gutenberg {

	/**
	 * Are custom blocks registered.
	 */
	public static $has_registered_blocks = false;

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the class instance.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		\add_action( 'init', [ $this, 'register' ] );
		\add_filter( 'widget_block_dynamic_classname', [ $this, 'filter_widget_block_dynamic_classname' ], 10, 2 );

		if ( \is_admin() ) {
			\add_action( 'enqueue_block_editor_assets', [ $this, 'on_enqueue_block_editor_assets' ], 0 );
		}
	}

	/**
	 * Gutenberg support for vcex elements.
	 */
	public function register() {
		if ( true === self::$has_registered_blocks || ! \function_exists( '\register_block_type_from_metadata' ) ) {
			return;
		}

		$this->register_blocks();
		self::$has_registered_blocks = true;

		\add_filter( 'block_categories_all', [ $this, 'add_block_category' ] );
	}

	/**
	 * Register blocks.
	 */
	protected function register_blocks() {
		$blocks = [
			'alert'              => \shortcode_exists( 'vcex_alert' ),
			'button'             => \shortcode_exists( 'vcex_button' ),
			'divider-dots'       => \shortcode_exists( 'vcex_divider_dots' ),
			'contact-form'       => \shortcode_exists( 'vcex_contact_form' ),
			'newsletter-form'    => \shortcode_exists( 'vcex_newsletter_form' ),
			'widget-modern-menu' => \shortcode_exists( 'vcex_widget_modern_menu' ),
			'wpex-card'          => \function_exists( 'wpex_card' ),
			'widget-title'       => true,
		];
		foreach ( $blocks as $block => $is_enabled ) {
			if ( $is_enabled ) {
				$file = TTC_PLUGIN_DIR_PATH . "build/blocks/vcex-{$block}/block.json";
				if ( \file_exists( $file ) ) {
					\register_block_type( $file );
				}
			}
		}
	}

	/**
	 * Gutenberg support for vcex elements.
	 */
	public function add_block_category( $categories ) {
		$category_slugs = \wp_list_pluck( $categories, 'slug' );

		if ( ! \in_array( 'total', $category_slugs, true ) ) {
		    $categories = \array_merge(
		        $categories,
		        [
		            [
		                'title' => 'Total',
		                'slug'  => 'total',
		                'icon'  => '<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><g clip-rule="evenodd" fill="currentColor" fill-rule="evenodd"><path d="m68.3 21.5 33.7-19.5 42.5 24.5 42.4 24.5v39z"/><path d="m17.2 120.7v-20.7-49l60.3 34.9z"/><path d="m186.9 149-42.4 24.5-42.5 24.5-42.4-24.5-15.8-9.2 84.8-49z"/></g></svg>',
		            ],
		        ]
		    );
		}

		return $categories;
	}

	/**
	 * Hooks into the "widget_block_dynamic_classname" filter.
	 */
	public function filter_widget_block_dynamic_classname( $classname, $block_name ) {
		if ( 'vcex/widget-title' === $block_name && is_string( $classname ) ) {
			$classname .= ' widget-title-wrap';
		}
		return $classname;
	}

	/**
	 * Hooks into the "enqueue_block_editor_assets" action
	 *
	 * @todo get this data with AJAX when editing the block only.
	 */
	public function on_enqueue_block_editor_assets(): void {

		// Get data for the Post Card block.
		$post_card_data = [
			'styles'        => [],
			'imageSizes'    => [],
			'overlayStyles' => [],
		];
		
		if ( \is_callable( 'WPEX_Card::get_card_styles' ) ) {
			foreach ( \WPEX_Card::get_card_styles() as $k => $v ) {
				$post_card_data['styles'][] = [
					'label' => esc_html( $v['name'] ),
					'value' => esc_attr( $k ),
				];
			}
		}

		if ( \function_exists( '\get_intermediate_image_sizes' ) ) {
			$get_sizes = (array) \get_intermediate_image_sizes();
			\array_unshift( $get_sizes, 'full' );
			$get_sizes = \array_combine( $get_sizes, $get_sizes );
			foreach ( $get_sizes as $size => $label ) {
				$post_card_data['imageSizes'][] = [
					'label' => esc_html( $label ),
					'value' => esc_attr( $size ),
				];
			}
		}

		if ( \function_exists( '\totaltheme_call_static' ) ) {
			$choices = (array) totaltheme_call_static( 'Overlays', 'get_style_choices' );
			foreach ( $choices as $name => $label ) {
				$post_card_data['overlayStyles'][] = [
					'label' => esc_html( $label ),
					'value' => esc_attr( $name ),
				];
			}
		}

		\wp_add_inline_script(
			'vcex-wpex-card-editor-script',
			'window.wpexCardData = ' . \wp_json_encode( $post_card_data ),
			'before'
		);

	}

}
