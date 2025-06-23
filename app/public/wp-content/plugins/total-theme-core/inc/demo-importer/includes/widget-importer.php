<?php

namespace TotalTheme\Demo_Importer;

\defined( 'ABSPATH' ) || exit;

class Widget_Importer {

	/**
	 * Demo being imported.
	 */
	private $demo;

	/**
	 * Data to import.
	 */
	private $data;

	/**
	 * Imported Widgets.
	 */
	public $imported_widgets = [];

	/**
	 * Errors.
	 */
	public $errors = [];

	/**
	 * Constructor.
	 */
	public function __construct( $demo ) {
		$this->demo = $demo;
	}

	/**
	 * Set widget data.
	 */
	public function set_widgets_data( $data ) {
		$this->data = (array) $data;
	}

	/**
	 * Run the import.
	 */
	public function run() {
		$widget_instances    = [];
		$registered_sidebars = $this->get_registered_sidebars();
		$available_widgets   = $this->get_available_widgets();

		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[ $widget_data['id_base'] ] = \get_option( "widget_{$widget_data['id_base']}" );
		}

		// Loop through widget import data.
		foreach ( (array) $this->data as $sidebar_id => $widgets ) {

			// Check if sidebar exists and not inactive.
			if ( ! isset( $registered_sidebars[ $sidebar_id ] ) || 'wp_inactive_widgets' === $sidebar_id ) {
				$this->errors[] = \sprintf( \esc_html__( 'The %s sidebar is not available.', 'total-theme-core' ), $sidebar_id );
				continue;
			}

			// Loop widgets
			foreach ( $widgets as $widget_instance_id => $widget ) {
				$fail = false;

				// Get id_base (remove -# from end) and instance ID number.
				$id_base = \preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
				$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

				// Does site support this widget?
				if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
					$fail = true;
					$this->errors[] = \sprintf( \esc_html__( 'Site does not support the %s widget.', 'total-theme-core' ), $id_base );
				}

				// Does widget with identical settings already exist in same sidebar?
				if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {

					// Get existing widgets in this sidebar.
					$sidebars_widgets = \get_option( 'sidebars_widgets' );

					// Check Inactive if that's where will go.
					$sidebar_widgets = isset( $sidebars_widgets[ $sidebar_id ] ) ? $sidebars_widgets[ $sidebar_id ] : [];

					// Loop widgets with ID base.
					$single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : [];

					foreach ( $single_widget_instances as $check_id => $check_widget ) {
						if ( \in_array( "{$id_base}-{$check_id}", $sidebar_widgets, true ) && (array) $widget === $check_widget ) {
							$fail = true;
							$this->errors[] = \sprintf( \esc_html__( 'The %s widget already exists.', 'total-theme-core' ), "{$id_base}-{$check_id}" );
							break;
						}
					}

				}

				// No failure.
				if ( ! $fail ) {

					// All instances for that widget ID base, get fresh every time.
					$single_widget_instances = \get_option( "widget_{$id_base}" );

					// Start fresh if have to.
					$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : [ '_multiwidget' => 1 ];

					// Add widget.
					$widget = (array) $widget;
					foreach ( $widget as $widget_arg => $widget_arg_val ) {
						if ( is_string( $widget_arg_val ) && str_contains( $widget_arg_val, 'total' ) ) {
							$widget_arg_val = Helpers::replace_demo_urls( $this->demo, $widget_arg_val );
							$widget[ $widget_arg ] = $widget_arg_val;
						}
					}
					$single_widget_instances[] = $widget;

					// Get the key it was given.
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1
					// When it's 0 it means there was an error so we try and re-add it.
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number = 1;
						$single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity.
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget.
					\update_option( "widget_{$id_base}", $single_widget_instances );

					// Assign widget instance to sidebar.
					$sidebars_widgets = \get_option( 'sidebars_widgets' );

					// Use ID number from new widget instance.
					$new_instance_id = "{$id_base}-{$new_instance_id_number}";

					// Add new instance to sidebar.
					$sidebars_widgets[ $sidebar_id ][] = $new_instance_id;

					// Save the amended data.
					$result = \update_option( 'sidebars_widgets', $sidebars_widgets );

					// Add result to array of imported widgets.
					if ( $result ) {
						if ( ! isset( $this->imported_widgets[ $sidebar_id ] ) ) {
							$this->imported_widgets[ $sidebar_id ] = [];
						}
						$this->imported_widgets[ $sidebar_id ][] = $new_instance_id;
					}
				}
			}
		}
	}

	/**
	 * Get list of registered sidebars.
	 */
	private function get_registered_sidebars(): array {
		global $wp_registered_sidebars;
		return (array) $wp_registered_sidebars;
	}

	/**
	 * Get a list of available widgets.
	 */
	private function get_available_widgets(): array {
		global $wp_registered_widget_controls;
		$available_widgets = [];
		foreach ( (array) $wp_registered_widget_controls as $widget ) {
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
				$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
				$available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];
			}
		}
		return $available_widgets;
	}

}
