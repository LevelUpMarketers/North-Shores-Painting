<?php

namespace TotalTheme\Demo_Importer;

\defined( 'ABSPATH' ) || exit;

class Delete_Imported_Data {

	/**
	 * Array of deleted content.
	 */
	public $deleted_items = [];

	/**
	 * Items that failed to delete.
	 */
	public $failed_items = [];

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Run the import.
	 */
	public function run() {
		$imported_data_list = Helpers::get_imported_data_list();

		if ( ! $imported_data_list ) {
			return;
		}

		@set_time_limit(0);

		foreach ( $imported_data_list as $part => $items ) {
			$method = "delete_{$part}";
			if ( \method_exists( $this, $method ) ) {
				$this->$method( $items );
			}
		}

		if ( ! empty( $this->failed_items ) ) {
			Helpers::update_imported_data_list( 'all', $this->failed_items );
		} else {
			Helpers::delete_imported_data_list();
		}
	}

	/**
	 * Delete nav menu locations.
	 */
	private function delete_nav_menu_locations( $locations ): void {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$current_locations = (array) \get_theme_mod( 'nav_menu_locations' );

		foreach ( $locations as $location ) {
			unset( $current_locations[ $location ] );
		}

		$result = \set_theme_mod( 'nav_menu_locations', $current_locations );	

		if ( true === $result ) {
			$this->deleted_items['nav_menu_locations'] = $locations;
		} else {
			$this->failed_items['nav_menu_locations'] = $sidebars;
		}
	}

	/**
	 * Delete Widgets.
	 */
	private function delete_widgets( array $sidebars = [] ): void {
		if ( ! \current_user_can( 'manage_options' ) ) {
			return;
		}

		$wp_sidebars = (array) get_option( 'sidebars_widgets' );
		
		foreach ( $sidebars as $sidebar => $widgets ) {
			foreach ( (array) $widgets as $widget_k => $widget_v ) {
				if ( in_array( $widget_v, $wp_sidebars[ $sidebar ], true ) ) {
					unset( $wp_sidebars[ $sidebar ][ array_search( $widget_v, $wp_sidebars[ $sidebar ] ) ] );
				}
			}
		}

		$widgets_updated = (bool) \update_option( 'sidebars_widgets', $wp_sidebars );

		if ( $widgets_updated ) {
			$this->deleted_items['widgets'] = $sidebars;
		} else {
			$this->failed_items['widgets'] = $sidebars;
		}
	}

	/**
	 * Delete Posts.
	 */
	private function delete_posts( $posts ): void {
		if ( \current_user_can( 'delete_posts' ) && \current_user_can( 'delete_pages' ) ) {
			foreach ( $posts as $post_id ) {
				if ( 'elementor_library' === get_post_type( $post_id ) ) {
					continue; // Skip Elementor library posts - it causes Elementor debug errors.
				}
				$deleted = \wp_delete_post( $post_id, true );
				if ( $deleted ) {
					$this->add_deleted_item( 'posts', $post_id );
				} elseif ( get_post( $post_id ) ) {
					$this->add_failed_item( 'posts', $post_id );
				}
			}
		}
	}

	/**
	 * Delete Menu Items.
	 */
	private function delete_menu_items( $menu_items ): void {
		if ( $menu_items && is_array( $menu_items ) && \current_user_can( 'delete_posts' ) && \current_user_can( 'delete_pages' ) ) {
			foreach ( $menu_items as $menu_item_id ) {
				$deleted = \wp_delete_post( $menu_item_id, true );
				if ( $deleted ) {
					$this->add_deleted_item( 'menu_items', $menu_item_id );
				} elseif ( get_post( $menu_item_id ) ) {
					$this->add_failed_item( 'menu_items', $menu_item_id );
				}
			}
		}
	}

	/**
	 * Delete Attachments.
	 */
	private function delete_attachments( $attachments ): void {
		if ( \current_user_can( 'delete_posts' ) ) {
			foreach ( $attachments as $attachment ) {
				$deleted = \wp_delete_attachment( $attachment, true );
				if ( $deleted ) {
					$this->add_deleted_item( 'attachments', $attachment );
				} elseif ( get_post( $attachment ) ) {
					$this->add_failed_item( 'attachments', $attachment );
				}
			}
		}
	}

	/**
	 * Delete Categories.
	 */
	private function delete_categories( $categories ): void {
		if ( \current_user_can( 'manage_categories' ) ) {
			foreach ( $categories as $category ) {
				$deleted = \wp_delete_category( $category );
				if ( true === $deleted ) {
					$this->add_deleted_item( 'categories', $category );
				} elseif ( \get_category( $category ) ) {
					$this->add_failed_item( 'categories', $category );
				}
			}
		}
	}

	/**
	 * Delete Terms.
	 */
	private function delete_terms( $terms ): void {
		if ( \current_user_can( 'manage_options' ) ) {
			foreach ( $terms as $term_tax_id ) {
				$term = \get_term_by( 'term_taxonomy_id', $term_tax_id );
				if ( \is_a( $term, 'WP_Term' ) ) {
					$result = \wp_delete_term( $term->term_id, $term->taxonomy );
					if ( 0 === $result ) {
						$this->add_failed_item( 'terms', $term_tax_id );
					} else {
						$this->add_deleted_item( 'terms', $term_tax_id );
					}
				}
			}
		}
	}

	/**
	 * Add deleted item.
	 */
	private function add_deleted_item( $part, $item ): void {
		if ( ! isset( $this->deleted_items[ $part ] ) ) {
			$this->deleted_items[ $part ] = [];
		}
		$this->deleted_items[ $part ][] = $item;
	}

	/**
	 * Add failed item.
	 */
	private function add_failed_item( $part, $item ): void {
		if ( ! isset( $this->failed_items[ $part ] ) ) {
			$this->failed_items[ $part ] = [];
		}
		$this->failed_items[ $part ][] = $item;
	}

}
