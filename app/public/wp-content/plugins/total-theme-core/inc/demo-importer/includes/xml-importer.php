<?php

namespace TotalTheme\Demo_Importer;

use TotalTheme_WP_Import;

\defined( 'ABSPATH' ) || exit;

class XML_Importer {

	/**
	 * Demo being processed.
	 */
	private $demo = '';

	/**
	 * Demo Data.
	 */
	private $demo_data = [];

	/**
	 * Array of imported posts.
	 */
	private $imported_posts = [];

	/**
	 * Array of imported menu_items.
	 */
	private $imported_menu_items = [];

	/**
	 * Array of imported attachments.
	 */
	private $imported_attachments = [];

	/**
	 * Array of imported categories.
	 */
	private $imported_categories = [];

	/**
	 * Array of imported terms.
	 */
	private $imported_terms = [];

	/**
	 * URL remap.
	 */
	private $broken_wpb_images = [];

	/**
	 * Args.
	 */
	private $args = [];

	/**
	 * Constructor.
	 */
	public function __construct( $demo, $demo_data, $args ) {
		$this->demo      = $demo;
		$this->demo_data = $demo_data;
		$this->args      = $args;

		\add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array', PHP_INT_MAX );
		\add_filter( 'wp_import_post_comments', '__return_false' );
		\add_filter( 'totaltheme_demo_importer_post_content', [ $this, 'filter_post_content' ] );
		\add_filter( 'totaltheme_demo_importer_meta_value', [ $this, 'filter_meta_value' ], 10, 2 );
		\add_filter( 'totaltheme_demo_importer_menu_item_url', [ $this, 'filter_menu_item_url' ] );
		
		\add_action( 'totaltheme_demo_importer_insert_post', [ $this, 'on_insert_post' ] );
		\add_action( 'totaltheme_demo_importer_insert_menu_item', [ $this, 'on_insert_menu_item' ] );
		\add_action( 'totaltheme_demo_importer_insert_attachment', [ $this, 'on_insert_attachment' ] );
		\add_action( 'totaltheme_demo_importer_insert_category', [ $this, 'on_insert_category' ] );
		\add_action( 'totaltheme_demo_importer_insert_term', [ $this, 'on_insert_term' ] );

		$this->run_wordpress_importer();

		if ( $this->args['fetch_attachments'] ) {
			$this->fix_wpb_broken_background_images();
		}

		if ( $this->imported_menu_items ) {
			$this->remove_duplicate_menu_items();
		}
	}

	/**
	 * Filter the post content.
	 */
	public function filter_post_content( $post_content ) {
		if ( $post_content && \is_string( $post_content ) ) {
			$post_content = Helpers::replace_demo_urls( $this->demo, $post_content );
			if ( $this->args['fetch_attachments'] ) {
				$post_content = $this->wpb_background_image_check( $post_content, 'post_content' );
			}
		}
		return $post_content;
	}

	/**
	 * Filter the meta value.
	 */
	public function filter_meta_value( $value, $key ) {
		if ( $value && \is_string( $value ) ) {
			if ( '_elementor_element_cache' === $key ) {
				$value = ''; // skip cache
			} elseif ( '_elementor_data' === $key ) {
				$value = Helpers::replace_demo_urls_json( $this->demo, $value );
			} elseif ( '_wpb_shortcodes_custom_css' === $key ) {
				$value = Helpers::replace_demo_urls( $this->demo, $value );
				if ( $this->args['fetch_attachments'] ) {
					$value = $this->wpb_background_image_check( $value, 'post_meta' );
				}
			}
		}
		return $value;
	}

	/**
	 * Filter the meta value.
	 */
	public function filter_menu_item_url( $url ) {
		$url = Helpers::replace_demo_urls( $this->demo, $url );
		return $url;
	}

	/**
	 * Runs on the totaltheme_demo_importer_insert_post hook.
	 */
	public function on_insert_post( $post_id ): void {
		$this->imported_posts[] = $post_id;
		Helpers::update_imported_data_list( 'posts', $post_id );
	}

	/**
	 * Runs on the totaltheme_demo_importer_insert_menu_item hook.
	 */
	public function on_insert_menu_item( $menu_item_id ): void {
		$this->imported_menu_items[] = $menu_item_id;
		Helpers::update_imported_data_list( 'menu_items', $menu_item_id );
	}

	/**
	 * Runs on the totaltheme_demo_importer_insert_attachment hook.
	 */
	public function on_insert_attachment( $attachment ): void {
		$this->imported_attachments[] = $attachment;
		Helpers::update_imported_data_list( 'attachments', $attachment );
	}

	/**
	 * Runs on the totaltheme_demo_importer_insert_category hook.
	 */
	public function on_insert_category( $category ): void {
		$this->imported_categories[] = $category;
		Helpers::update_imported_data_list( 'categories', $category );
	}

	/**
	 * Runs on the totaltheme_demo_importer_insert_term hook.
	 */
	public function on_insert_term( $term ): void {
		$this->imported_terms[] = $term;
		Helpers::update_imported_data_list( 'terms', $term );
	}

	/**
	 * Runs the WP importer.
	 */
	private function run_wordpress_importer() {
		@set_time_limit(0);

		$wp_import = new TotalTheme_WP_Import();
		$wp_import->fetch_attachments = $this->args['fetch_attachments'];
		$wp_import->import( $this->args['file'] );
	}

	/**
	 * Add broken WPBakery images to url remap queue.
	 */
	private function wpb_background_image_check( $text, $context ) {
		if ( \is_string( $text ) && \str_contains( $text, 'url(' ) ) {
			\preg_match_all('~\bbackground(url-image)?\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i', $text, $matches );
			if ( ! empty( $matches['image'] ) ) {
				foreach ( $matches['image'] as $image_url ) {
					if ( \str_contains( $image_url, '?id=' ) ) {
						\preg_match( '/\?id=(\d+)/', $image_url, $image_id );
						if ( ! empty( $image_id[1] ) ) {
							if ( $correct_url = \esc_url( \wp_get_attachment_url( $image_id[1] ) ) ) {
								$text = \str_replace( $image_url, "{$correct_url}?id={$image_id[1]}", $text );
							} else {
								$this->broken_wpb_images[ $image_id[1] ] = $image_url;
							}
						}
					}
				}
			}
		}
		return $text;
	}

	/**
	 * Add broken WPBakery images to url remap queue.
	 */
	public function fix_wpb_broken_background_images() {
		global $wpdb;
		foreach ( $this->broken_wpb_images as $image_id => $from_url ) {
			$image_url = \wp_get_attachment_url( $image_id );
			if ( $image_url ) {
				$to_url = esc_url( add_query_arg( [
					'id' => (int) $image_id,
				], $image_url ) );
				// remap urls in post_content
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url ) );
				// remap urls in _wpb_shortcodes_custom_css meta
				$result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='_wpb_shortcodes_custom_css'", $from_url, $to_url ) );
			}
		}
	}

	/**
	 * Remove duplicated menu items.
	 */
	public function remove_duplicate_menu_items() {
		if ( ! \current_user_can( 'delete_published_posts' ) || empty( $this->demo_data['nav_menu_locations'] ) ) {
			return;
		}

		foreach ( (array) $this->demo_data['nav_menu_locations'] as $menu ) {
			$uniq_items = [];
			foreach ( (array) \wp_get_nav_menu_items( $menu ) as $menu_item ) {
				$uniq_item = \json_encode( [
					'post_parent'      => $menu_item->post_parent ?? '',
					'post_title'       => $menu_item->post_title ?? '',
					'post_content'     => $menu_item->post_content ?? '',
					'type'             => $menu_item->type ?? '',
					'menu_item_parent' => $menu_item->menu_item_parent ?? '',
					'url'              => $menu_item->url ?? '',
					'description'      => $menu_item->description ?? '',
					'classes'          => $menu_item->classes ?? '',
				] );
				if ( \in_array( $uniq_item, $uniq_items, true ) && 'nav_menu_item' === \get_post_type( $menu_item->ID ) ) {
					\wp_delete_post( $menu_item->ID );
				} else {
					$uniq_items[] = $uniq_item;
				}
			}
			unset( $uniq_items );
		}
	}

}
