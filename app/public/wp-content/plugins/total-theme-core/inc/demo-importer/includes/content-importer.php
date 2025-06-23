<?php

namespace TotalTheme\Demo_Importer;

use WP_Query;
use WP_Error;
use RevSlider;

\defined( 'ABSPATH' ) || exit;

#[\AllowDynamicProperties]
class Content_Importer {

	/**
	 * Demo name.
	 */
	private $demo = [];

	/**
	 * Demo data.
	 */
	private $demo_data = [];

	/**
	 * Constructor.
	 */
	public function __construct( $demo, $data ) {
		$this->demo      = $demo;
		$this->demo_data = $data;
	}

	/**
	 * Import XML data
	 */
	public function process_xml( $import_images ) {
		if ( ! \current_user_can( 'publish_posts' ) || ! \current_user_can( 'publish_pages' ) ) {
			return new WP_Error( 'xml_import_error', \esc_html__( 'Could not import xml data because the current user can not publish_pages and/or publish_posts', 'total-theme-core' ) );
		}

		if ( ! \defined( 'WP_LOAD_IMPORTERS' ) ) {
			\define( 'WP_LOAD_IMPORTERS', true );
		}

		if ( ! \function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$temp_xml_file = \download_url( "https://totalwpthemedemo.com/{$this->demo}/wp-json/twtd/v1/export/xml" );

		if ( \is_wp_error( $temp_xml_file ) ) {
			return new WP_Error( 'xml_import_error', $temp_xml_file->get_error_message() );
		}

		// Delete default wp pages
		$this->delete_default_wp_pages();

		// Include required files
		require_once TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'includes/xml-importer.php';
		require_once TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'includes/wordpress-importer/wordpress-importer.php';

		// Run the XML Importer
		$xml_importer = new XML_Importer( $this->demo, $this->demo_data, [
			'file'              => $temp_xml_file,
			'fetch_attachments' => (bool) $import_images,
		] );

		$this->xml_imported = true;

		\wp_delete_file( $temp_xml_file );
	}

	/**
	 * Delete default wp pages.
	 */
	private function delete_default_wp_pages(): void {
		if ( ! current_user_can( 'delete_published_pages' ) ) {
			return;
		}
	
		$sample_page = \get_page_by_path( 'sample-page', OBJECT, 'page' );

		if ( ! \is_null( $sample_page ) ) {
			\wp_delete_post( $sample_page->ID, true );
		}

		$hello_world_post = \get_page_by_path( 'hello-world', OBJECT, 'post' );

		if ( ! \is_null( $hello_world_post ) ) {
			\wp_delete_post( $hello_world_post->ID, true );
		}
	}

	/**
	 * Process the theme mods json file
	 */
	public function process_theme_mods() {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return new WP_Error( 'theme_mods_import_error', \esc_html__( 'Customizer settings not imported because the current user can not edit_theme_options.', 'total-theme-core' ) );
		}

		$mods = Helpers::get_remote_data( $this->demo, 'mods' );

		if ( 'error' === $mods ) {
			return new WP_Error( 'theme_mods_import_error', \esc_html__( 'Error trying to fetch customizer settings.', 'total-theme-core' ) );
		} elseif ( '[]' === $mods ) {
			return new WP_Error( 'theme_mods_import_error', \esc_html__( 'No customizer settings required for this demo.', 'total-theme-core' ) );
		}

		$data = \json_decode( $mods, true );

		if ( JSON_ERROR_NONE === \json_last_error() ) {
			$imported_mods = [];
			remove_theme_mods();
			foreach ( $data as $key => $val ) {
				if ( in_array( $key, [ 'header_flex_aside_content', 'header_aside' ], true ) ) {
					$val = Helpers::replace_demo_urls( $this->demo, $val );
				}
				$result = set_theme_mod( $key, $val );
				if ( true === $result ) {
					$imported_mods[] = $key;
				}
			}
			/*if ( $imported_mods ) {
				Helpers::update_imported_data_list( 'theme_mods', $imported_mods );
			}*/
		} else {
			return new WP_Error( 'theme_mods_import_error', \esc_html__( 'There was an error parsing the theme mods json file.', 'total-theme-core' ) );
		}
	}

	/**
	 * Import Revsliders
	 */
	public function process_sliders_import() {
		if ( empty( $this->demo_data['sliders'] ) ) {
			return new WP_Error( 'revslider_import_error', \esc_html__( 'No sliders imported because this demo doesn\'t have any sliders.', 'total-theme-core' ) );
		}

		if ( ! \current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'revslider_import_error', \esc_html__( 'No sliders imported because current user can not manage_options.', 'total-theme-core' ) );
		}

		if ( ! class_exists( 'RevSlider' ) ) {
			return new WP_Error( 'revslider_import_error', \esc_html__( 'Sliders could not be imported because the Revolution slider plugin is disabled.', 'total-theme-core' ) );
		}

		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$sliders = $this->demo_data['sliders'];

		$errors = [];

		// Loop through slider zips and upload to media library then import.
		foreach ( (array) $sliders as $slider_url ) {

			// Download slider zip.
			$tmp_file = download_url( $slider_url );

			// Check for download errors.
			if ( $error = \is_wp_error( $tmp_file ) ) {
				$errors[] = new WP_Error( 'revslider_import_error', \esc_html__( 'Slider error:', 'total-theme-core' ) . ' ' . $temp->get_error_message() );
			}
			
			// No download errors so lets sideload the slider zip.
			else {
				$id = \media_handle_sideload( [
					'name'     => \basename( $slider_url ),
					'tmp_name' => $tmp_file
				], 0 );

				// Check for handle sideload errors.
				if ( ! \is_wp_error( $id ) ) {
					$slider = new RevSlider();
					$slider->importSliderFromPost( true, true, \get_attached_file( $id ) );
				} elseif ( \is_object( $id ) ) {
					$errors[] = new WP_Error( 'revslider_import_error', \esc_html__( 'Slider error:', 'total-theme-core' ) . ' ' . $id->get_error_message() );
				}
			}

			// Delete temp slider zip.
			\wp_delete_file( $tmp_file );
		}

		return $errors;
	}

	/**
	 * Process the widget import
	 */
	public function process_widget_import() {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return new WP_Error( 'widgets_import_error', \esc_html__( 'No widgets imported because the current user can\'t edit_theme_options.', 'total-theme-core' ) );
		}

		$widgets = Helpers::get_remote_data( $this->demo, 'widgets' );

		if ( 'error' === $widgets ) {
			return new WP_Error( 'widgets_import_error', \esc_html__( 'Error trying to fetch demo widgets.', 'total-theme-core' ) );
		} elseif ( '[]' === $widgets || ! $widgets ) {
			return new WP_Error( 'widgets_import_error', \esc_html__( 'No widgets required for this demo.', 'total-theme-core' ) );
		}

		$widgets_data = \json_decode( $widgets, true );

		if ( JSON_ERROR_NONE === \json_last_error() ) {
			$widget_importer = Helpers::new_widget_importer( $this->demo );
			$widget_importer->set_widgets_data( $widgets_data );
			$widget_importer->run();
			if ( ! empty( $widget_importer->imported_widgets ) ) {
				Helpers::update_imported_data_list( 'widgets', $widget_importer->imported_widgets );
			}
		}
	}

	/**
	 * Elementor settings.
	 */
	public function import_elementor_settings() {
		if ( 'Elementor' !== Helpers::get_demo_builder( $this->demo_data ) ) {
			return;
		}

		// Disable elementor color schemes and typography
		if ( \current_user_can( 'manage_options' ) ) {
			\update_option( 'elementor_disable_color_schemes', 1, false );
			\update_option( 'elementor_disable_typography_schemes', 1, false );
		}

		// Locate site kit from imported CPT and set it as the default kit
		$query = new WP_Query( [
			'post_type'      => 'elementor_library',
			'posts_per_page' => 1,
			'orderby'         => 'date',
			'order'           => 'ASC',
			'fields'          => 'ids',
		] );

		if ( ! empty( $query->posts[0] ) ) {
			\update_option( 'elementor_active_kit', $query->posts[0], false );
		}
	}

	/**
	 * Set menu locations.
	 */
	public function set_menus() {
		if ( empty( $this->demo_data['nav_menu_locations'] ) || ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$demo_menus    = (array) $this->demo_data['nav_menu_locations'];
		$locations     = (array) \get_theme_mod( 'nav_menu_locations' );
		$locations_set = [];

		foreach ( $demo_menus as $location => $slug ) {
			if ( $menu = \get_term_by( 'slug', $slug, 'nav_menu') ) {
				$locations_set[] = $location;
				$locations[ $location ] = $menu->term_id;
			}
		}

		if ( $locations_set ) {
			$result = \set_theme_mod( 'nav_menu_locations', $locations );
			
			if ( true === $result ) {
				Helpers::update_imported_data_list( 'nav_menu_locations', $locations_set );
			}
		}
	}

	/**
	 * Set homepage.
	 */
	public function set_homepage() {
		if ( ! empty( $this->demo_data['homepage_slug'] ) && \current_user_can( 'manage_options' ) ) {
			$page = \get_page_by_path( $this->demo_data['homepage_slug'] );
			if ( ! empty( $page->ID ) ) {
				$result = \update_option( 'page_on_front', $page->ID );
				if ( true === $result ) {
					\update_option( 'show_on_front', 'page' );
					Helpers::update_imported_data_list( 'page_on_front', $page->ID );
				}
			}
		}
	}

	/**
	 * Set posts page.
	 */
	public function set_posts_page() {
		if ( ! empty( $this->demo_data['page_for_posts'] ) && \current_user_can( 'manage_options' ) ) {
			$posts_page = \get_page_by_path( $this->demo_data['page_for_posts'] );
			if ( ! empty( $posts_page->ID ) ) {
				$result = \update_option( 'page_for_posts', $posts_page->ID );
				if ( true === $result ) {
					Helpers::update_imported_data_list( 'page_for_posts', $posts_page->ID );
				}
			}
		}
	}

	/**
	 * Set shop page.
	 */
	public function set_shop_page() {
		if ( ! empty( $this->demo_data['shop_slug'] ) && \current_user_can( 'manage_options' ) ) {
			$shop = \get_page_by_path( $this->demo_data['shop_slug'] );
			if ( ! empty( $shop->ID ) ) {
				$result = \update_option( 'woocommerce_shop_page_id', $shop->ID );
				if ( true === $result ) {
					Helpers::update_imported_data_list( 'woocommerce_shop_page_id', $shop->ID );
				}
			}
		}
	}

}
