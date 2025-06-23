<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * TablePress Integration.
 */
final class TablePress {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of TablePress.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		if ( \wpex_is_request( 'frontend' ) ) {
			\add_action( 'wp_enqueue_scripts', [ $this, 'theme_css' ] );
		}
		if ( \WPEX_VC_ACTIVE ) {
			\add_action( 'vc_after_mapping', [ $this, 'vc_lean_map' ], 0 );
		}
	}

	/**
	 * Loads custom CSS for tablepress styling.
	 */
	public function theme_css() {
		\wp_enqueue_style(
			'wpex-tablepress',
			\totaltheme_get_css_file( 'frontend/tablepress' ),
			array( 'tablepress-default' ),
			\WPEX_THEME_VERSION,
			'all'
		);
	}

	/**
	 * Registers table module for WPBakery plugin.
	 */
	public function vc_lean_map() {
		\vc_lean_map( 'table', array( $this, 'vc_settings' ) );
		\add_filter( 'vc_autocomplete_table_id_callback', array( $this, 'tables_autocomplete_callback' ), 10, 1 );
		\add_filter( 'vc_autocomplete_table_id_render', array( $this, 'tables_autocomplete_render' ), 10, 1 );
	}

	/**
	 * Table vc module settings.
	 */
	public function vc_settings() {
		return array(
			'name' => \esc_html__( 'TablePress', 'total' ),
			'description' => \esc_html__( 'Insert a TablePress table', 'total' ),
			'base' => 'table',
			'icon' => 'vcex_element-icon vcex_element-icon--table',
			'params' => array(
				array(
					'type' => 'autocomplete',
					'heading' => \esc_html__( 'Table', 'total' ),
					'description' => \esc_html__( 'Search tables by name and select your table of choice.', 'total' ),
					'param_name' => 'id',
					'settings' => array(
						'multiple' => false,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'admin_label' => true,
				),

			)
		);

	}

	/**
	 * Return a list of tables to choose from in the WPBakery module.
	 */
	public function tables_autocomplete_callback( $search_string ) {
		$tables = [];

		$tablepress_tables = \get_option( 'tablepress_tables' );

		if ( empty( $tablepress_tables ) ) {
			return $tables;
		}

		$tablepress_tables = \json_decode( $tablepress_tables, true );
		$tablepress_tables = \array_flip( $tablepress_tables['table_post'] );

		$tables_ids = \get_posts( [
			'posts_per_page' => -1,
			'post_type'      => 'tablepress_table',
			's'              => \sanitize_text_field( $search_string ),
			'fields'         => 'ids',
		] );

		if ( ! empty( $tables_ids ) ) {
			foreach ( $tables_ids as $id ) {
				if ( isset( $tablepress_tables[ $id ] ) ) {
					$tables[] = array(
						'label' => \esc_html( \get_the_title( $id ) ),
						'value' => \absint( $tablepress_tables[$id] ),
					);
				}
			}
		}

		return $tables;
	}

	/**
	 * Render tables for WPBakery autocomplete.
	 */
	function tables_autocomplete_render( $data ) {
		$tablepress_tables = \get_option( 'tablepress_tables' );

		if ( empty( $tablepress_tables ) ) {
			return [
				'label' => \esc_html( $data['value'] ),
				'value' => \absint( $data['value'] )
			];
		}

		$tablepress_tables = json_decode( $tablepress_tables, true );
		$tablepress_tables = $tablepress_tables['table_post'];

		return [
			'label' => \esc_html( get_the_title( $tablepress_tables[ $data['value'] ] ) ),
			'value' => \absint( $data['value'] ),
		];
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
