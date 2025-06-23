<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Used to build WP Queries for vcex elements.
 *
 * @todo rename class to Post_Query for consistency with new Term_Query
 */
class Query_Builder {

	/**
	 * Defines the pre_query var.
	 */
	protected $pre_query = null;

	/**
	 * Shortcode/Element attributes.
	 */
	public $atts = [];

	/**
	 * Shortcode tag.
	 */
	public $shortcode_tag = [];

	/**
	 * Return Fields.
	 */
	public $fields = 'all';

	/**
	 * Query args.
	 */
	public $args = [];

	/**
	 * Array of clauses to add to the query.
	 */
	private $clauses = [];

	/**
	 * Stores dynamic strings for multiple use.
	 */
	private $dynamic_strings;

	/**
	 * Check if currently handling an ajax request.
	 */
	private $doing_ajax = false;

	/**
	 * Stores the current post ID.
	 */
	private $post_id = 0;

	/**
	 * Class Constructor.
	 */
	public function __construct( array $atts, string $shortcode_tag = '', string $fields = 'all' ) {
		$this->atts          = $atts;
		$this->shortcode_tag = $shortcode_tag;
		$this->fields        = $fields;

		$pre_query = $this->get_pre_query();

		if ( $pre_query && \is_a( $pre_query, 'WP_Query' ) ) {
			$this->pre_query = $pre_query;
			return;
		}

		$this->doing_ajax = \wp_doing_ajax();
		$this->post_id = $this->get_current_post_ID(); // must run after the doing_ajax check.

		// Ajax fix.
		if ( $this->doing_ajax ) {
			$this->args['post_status'] = [ 'publish' ];
		}

		// Auto Query.
		if ( $this->is_auto_query() ) {
			return $this->auto_query();
		}

		// Custom query.
		if ( $this->is_custom_query() ) {
			return $this->custom_query( $this->atts['custom_query_args'] );
		}

		// Custom query types.
		if ( isset( $atts['query_type'] ) && 'custom' !== $atts['query_type'] ) {
			switch ( $atts['query_type'] ) {
				case 'callback':
					if ( isset( $atts['query_callback'] ) ) {
						$callback = $atts['query_callback'];
						if ( \vcex_validate_user_func( $callback ) && \is_callable( $callback ) ) {
							$query = \call_user_func( $callback, $this->atts );
							if ( $query && \is_array( $query ) ) {
								$this->args = $query;
							} else {
								$this->args = [];
							}
							if ( ! empty( $this->atts['pagination'] ) && 'false' !== $this->atts['pagination'] ) {
								$this->parse_pagination( null );
							}
						}
						return;
					}
					break;
				default:
					$query_method = "query_type_{$atts['query_type']}";
					if ( \method_exists( $this, $query_method ) ) {
						return $this->$query_method();
					}
					break;
			}
		}

		// Loop through shortcode atts and run class methods.
		foreach ( $atts as $key => $value ) {
			$method = "parse_{$key}";
			if ( \method_exists( $this, $method ) ) {
				$this->$method( $value );
			}
		}
	}

	/**
	 * Pre-Query.
	 */
	private function get_pre_query() {
		return \apply_filters( 'vcex_pre_query', null, $this->atts, $this->shortcode_tag, $this->fields );
	}

	/**
	 * Check if this is an auto query.
	 */
	private function is_auto_query() {
		if ( isset( $this->atts['query_type'] ) ) {
			return ( 'auto' === $this->atts['query_type'] ) ? true : false;
		} else {
			return \vcex_validate_att_boolean( 'auto_query', $this->atts );
		}
	}

	/**
	 * Check if this is a custom query.
	 */
	private function is_custom_query() {
		if ( empty( $this->atts['custom_query_args'] ) ) {
			return false;
		}
		if ( isset( $this->atts['query_type'] ) ) {
			return ( 'custom' === $this->atts['query_type'] ) ? true : false;
		} elseif ( \vcex_validate_att_boolean( 'custom_query', $this->atts ) ) {
			return true;
		}
	}

	/**
	 * Set args for the term_card query type.
	 */
	private function query_type_term_card() {
		$card = vcex_card_instance();
		
		if ( ( empty( $card->term ) || empty( $card->term->taxonomy ) || empty( $card->term->term_id ) ) && ! vcex_get_template_edit_mode() ) {
			return $this->set_empty_query();
		}

		if ( ! empty( $card->term ) ) {
			$this->add_tax_query( [ [
				'taxonomy' => $card->term->taxonomy,
				'field' => 'term_id',
				'terms' => [ $card->term->term_id ]
			] ] );
		}
	
		$this->parse_post_type( $this->atts['post_types'] ?? get_post_type( $this->post_id ) );
		$this->parse_order( null );
		$this->parse_orderby( null );
		$this->parse_posts_per_page( null );
	//	$this->parse_pagination( null ); // not allowed yet - need to figure out a way to pass the current card ID to the ajax
	}

	/**
	 * Set args for the post_children query type.
	 */
	private function query_type_post_series() {
		if ( ! \taxonomy_exists( 'post_series' )
			|| ! \class_exists( '\TotalThemeCore\Post_Series', false )
		) {
			return $this->set_empty_query();
		}

		$terms = \get_the_terms( $this->post_id, 'post_series' );

		if ( empty( $terms ) || \is_wp_error( $terms ) ) {
			return $this->set_empty_query();
		}

		$this->args = [
			'post_type'      => \get_post_type( $this->post_id ),
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		//	'post__not_in'   => [ $this->post_id ],
			'tax_query'      => [ [
				'taxonomy' => 'post_series',
				'field'    => 'id',
				'terms'    => $terms[0]->term_id
			] ],
		];

		$order = ( ! empty( $this->atts['order'] ) && 'default' !== $this->atts['order'] ) ? $this->atts['order'] : \TotalThemeCore\Post_Series::instance()->get_query_order();
		$orderby = ( ! empty( $this->atts['orderby'] ) && 'default' !== $this->atts['orderby'] ) ? $this->atts['orderby'] : 'date';

		$this->parse_order( $order );
		$this->parse_orderby( $orderby );
	}

	/**
	 * Set args for the post_children query type.
	 */
	private function query_type_post_children() {
		$this->args = [
			'post_type'   => \get_post_type( $this->post_id ),
			'post_parent' => $this->post_id,
		];

		$order = ( ! empty( $this->atts['order'] ) && 'default' !== $this->atts['order'] ) ? $this->atts['order'] : 'ASC';
		$orderby = ( ! empty( $this->atts['orderby'] ) && 'default' !== $this->atts['orderby'] ) ? $this->atts['orderby'] : 'menu_order';

		$this->parse_order( $order );
		$this->parse_orderby( $orderby );
		$this->parse_posts_per_page( null );
		$this->parse_pagination( null );
	}

	/**
	 * Set args for the custom field query type.
	 */
	private function query_type_custom_field() {
		$custom_field = ! empty( $this->atts['query_custom_field'] ) ? \sanitize_text_field( $this->atts['query_custom_field'] ) : '';
		
		if ( ! $custom_field ) {
			return $this->set_empty_query();
		}

		$post_ids = [];
		$posts = \vcex_get_meta_value( $custom_field );

		if ( $posts && \is_string( $posts ) ) {
			$posts = \wp_parse_list( $posts );
		}

		if ( ! $posts || ! is_array( $posts ) ) {
			return $this->set_empty_query();
		}

		foreach ( $posts as $post ) {
			if ( \is_a( $post, 'WP_Post' ) ) {
				if ( isset( $post->ID ) ) {
					$post_ids[] = $post->ID;
				}
			} elseif ( \is_numeric( $post ) ) {
				$post_ids[] = $post;
			}
		}

		$post_ids = \array_filter( \array_unique( \array_map( 'absint', $post_ids ) ) );

		if ( ! $post_ids ) {
			return $this->set_empty_query();
		}

		$this->args = [
			'post_type'           => 'any',
			'posts_per_page'      => '-1',
			'post__in'            => $post_ids,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
		];

		$order_by = ! empty( $this->atts['orderby'] ) ? $this->atts['orderby'] : 'post__in';
		$default_order = ( 'post__in' === $order_by ) ? 'ASC' : 'DESC';
		$order = ! empty( $this->atts['order'] ) ? $this->atts['order'] : $default_order;

		if ( 'post__in' === $order_by && 'DESC' === $order ) {
			$this->args['post__in'] = array_reverse( $post_ids );
		}

		$this->parse_orderby( $order_by );
		$this->parse_order( $order );
	}

	/**
	 * Set args for the author query type.
	 */
	private function query_type_author() {
		$post_id = $this->post_id;

		if ( 'staff' === \get_post_type( $post_id ) && \function_exists( 'wpex_get_user_assigned_to_staff_member' ) ) {
			$post_author = \wpex_get_user_assigned_to_staff_member( $post_id );
		}

		$post_author = $post_author ?? \get_post_field( 'post_author', $post_id );

		if ( $post_author ) {
			$this->args = [
				'author'       => \absint( $post_author ),
				'post__not_in' => [ $post_id ],
			];
		} elseif ( class_exists( 'Assign_Staff_Author_Total' ) ) {
			$this->args = [
				'meta_key'   => 'wpex_author_staff_id',
				'meta_value' => strval( $post_id ),
			];
		} else {
			return $this->set_empty_query();
		}

		$this->parse_post_type( $this->atts['post_types'] ?? 'post' );
		$this->parse_terms_in_out();
		$this->parse_order( null );
		$this->parse_orderby( null );
		$this->parse_posts_per_page( null );
		$this->parse_pagination( null ); // needed for ajax.
	}

	/**
	 * Set args to query related posts.
	 */
	private function query_type_related() {
		$this->parse_post_type( $this->atts['post_types'] ?? \get_post_type( $this->post_id ) );
		$this->parse_order( null );
		$this->parse_orderby( null );
		$this->parse_posts_per_page( null );
		$this->parse_pagination( null );

		if ( ! empty( $this->atts['related_taxonomy'] ) ) {
			$this->args['taxonomy'] = $this->atts['related_taxonomy'];
		}

		if ( \vcex_is_frontend_edit_mode() ) {
			return;
		}

		$related_items = $this->add_related_args();

		if ( ! $related_items ) {
			return $this->set_empty_query();
		}
	}

	/**
	 * Set args to query featured products.
	 */
	private function query_type_woo_featured() {
		$this->args = [
			'post_type' => 'product',
		];
		$this->parse_featured_products_only( true );
		$this->parse_terms_in_out();
		$this->parse_order( null );
		$this->parse_orderby( null );
		$this->parse_posts_per_page( null );
		$this->parse_pagination( null ); // needed for ajax.
	}

	/**
	 * Set args to query best selling products.
	 */
	private function query_type_woo_best_selling() {
		$this->args = [
			'post_type'           => 'product',
			'meta_key'            => 'total_sales',
			'orderby'             => 'meta_value_num',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
		];
		$this->parse_posts_per_page( null );
		$this->parse_terms_in_out();
		$this->parse_pagination( null ); // needed for ajax.
	}

	/**
	 * Set args to query onsale products.
	 */
	private function query_type_woo_onsale() {
		if ( ! \function_exists( 'wc_get_product_ids_on_sale' ) ) {
			return $this->set_empty_query();
		}
		$this->args = [
			'post_type' => 'product',
			'post__in' => wc_get_product_ids_on_sale() ?: [ 0 ],
		];
		$this->parse_order( null );
		$this->parse_orderby( null );
		$this->parse_terms_in_out();
		$this->parse_posts_per_page( null );
		$this->parse_pagination( null ); // needed for ajax.
	}

	/**
	 * Set args to query related products.
	 */
	private function query_type_woo_related() {
		if ( ! \function_exists( 'wc_get_related_products' ) ) {
			return $this->set_empty_query();
		}
		$related = (array) \wc_get_related_products( $this->post_id );
		$this->args = [
			'post_type' => 'product',
			'post__in' => \array_map( 'absint', \array_values( $related ) ),
		];
		$this->parse_order( null );
		$this->parse_orderby( null );
		$this->parse_posts_per_page( null );
		$this->parse_pagination( null ); // needed for ajax.
	}

	/**
	 * Set args to query upsells products.
	 */
	private function query_type_woo_upsells() {
		if ( ! \function_exists( 'wc_get_product' ) || ! \function_exists( 'wc_products_array_filter_visible' ) ) {
			return $this->set_empty_query();
		}
		$post_id = $this->post_id;
		$product = \wc_get_product( $post_id );
		$upsell_ids = ! empty( $product ) ? $product->get_upsell_ids() : '';
		if ( ! $upsell_ids ) {
			return $this->set_empty_query();
		}
		$orderby = \apply_filters( 'woocommerce_upsells_orderby', 'post__in' );
		$order   = \apply_filters( 'woocommerce_upsells_order', 'desc' );
		$limit   = \apply_filters( 'woocommerce_upsells_total', '-1' );
		$upsells = \array_filter( \array_map( 'wc_get_product', $upsell_ids ), 'wc_products_array_filter_visible' );
		$upsell_ids = [];
		foreach ( $upsells as $upsell ) {
			$upsell_ids[] = \absint( $upsell->get_id() );
		}
		$this->args = [
			'posts_per_page' => $limit,
			'post_type'      => 'product',
			'post__in'       => $upsell_ids,
			'orderby'        => $orderby,
			'order'          => $order,
		];
	}

	/**
	 * Just Events: All.
	 */
	private function query_type_just_events_all(): void {
		$this->just_events_common_args();
	}

	/**
	 * Just Events: Upcoming.
	 */
	private function query_type_just_events_upcoming(): void {
		$this->just_events_common_args();
		$this->args['meta_query'] = [
			'relation' => 'AND',
			// Check end date first and fallback to start date incase.
			'just_events_clause' => [
				'relation' => 'OR',
				[
					'key'     => '_just_events_start_date',
					'value'   => $this->get_current_date_time(),
					'compare' => '>',
					'type'    => 'CHAR'
				],
			]
		];
	}

	/**
	 * Just Events: Ongoing.
	 */
	private function query_type_just_events_ongoing(): void {
		$this->just_events_common_args();
		$current_date_time = $this->get_current_date_time();
		$this->args['meta_query'] = [
			'just_events_clause' => [
				'relation' => 'AND',
				[
					'key'     => '_just_events_start_date',
					'value'   => $current_date_time,
					'compare' => '<=',
					'type'    => 'CHAR'
				],
				[
					'key'     => '_just_events_end_date',
					'value'   => $current_date_time,
					'compare' => '>=',
					'type'    => 'CHAR'
				],
			],
		];
	}

	/**
	 * Just Events: Past.
	 */
	private function query_type_just_events_past(): void {
		$this->just_events_common_args();
		$this->args['meta_query'] = [
			'just_events_clause' => [
				[
					'key'     => '_just_events_end_date',
					'value'   => $this->get_current_date_time(),
					'compare' => '<',
					'type'    => 'CHAR'
				],
			],
		];
	}

	/**
	 * Just Events: Today.
	 */
	private function query_type_just_events_today(): void {
		$this->just_events_common_args();
		$timezone = \wp_timezone();
		$current_date_time_datetime = new \DateTime( 'today', $timezone );
		$current_date_time_start = $current_date_time_datetime->format( 'Y-m-d H:i:s' );
		$tomorrow_datetime = new \DateTime( 'tomorrow', $timezone );
		$tomorrow_start = $tomorrow_datetime->format( 'Y-m-d H:i:s' );
		$this->args['meta_query'] = [
			'just_events_clause' => [
				'relation' => 'AND',
				[
					'key'     => '_just_events_start_date',
					'value'   => $current_date_time_start,
					'compare' => '>=',
					'type'    => 'CHAR'
				],
				[
					'key'     => '_just_events_end_date',
					'value'   => $tomorrow_start,
					'compare' => '<=',
					'type'    => 'CHAR'
				],
			],
		];
	}

	/**
	 * Common args for just_events queries.
	 */
	private function just_events_common_args() {
		if ( ! \post_type_exists( 'just_event' ) ) {
			return $this->set_empty_query();
		}

		$this->args['post_type'] = 'just_event';
		$this->args['orderby'] = 'meta_value';
		$this->args['meta_key'] = '_just_events_start_date';

		$this->parse_order( ! empty( $this->atts['order'] ) ? $this->atts['order'] : 'DESC' );
		$this->parse_terms_in_out();
		$this->parse_pagination( null );
		$this->parse_posts_per_page( null );
	}

	/**
	 * Parse auto query args.
	 */
	private function auto_query() {
		if ( \vcex_is_frontend_edit_mode() ) {
			$this->args['post_type'] = ! empty( $this->atts['auto_query_preview_pt'] ) ? $this->atts['auto_query_preview_pt'] : 'post';
			$this->args['posts_per_page'] = \get_option( 'posts_per_page' );
		} elseif ( ! empty( $this->atts['query_vars'] ) ) {
			$query_vars = $this->atts['query_vars'];
			if ( \is_array( $query_vars ) ) {
				$this->args = $query_vars;
			} elseif ( \is_string( $query_vars ) ) {
				$query_vars = \stripslashes_deep( $query_vars );
				$this->args = \json_decode( $query_vars, true );
			}
			if ( ! empty( $this->atts['paged'] ) ) {
				$this->args['paged'] = $this->atts['paged'];
			}
		} else {
			global $wp_query;
			$this->pre_query = $wp_query;
		}
	}

	/**
	 * Custom Query.
	 */
	private function custom_query( $query ) {
		if ( \is_string( $query ) ) {
			$query = \wp_strip_all_tags( $query );

			// Check if it's a callable function.
			if ( \is_callable( $query ) ) {
				if ( \vcex_validate_user_func( $query ) ) {
					$query = \call_user_func( $query, $this->atts );
					if ( $query && \is_array( $query ) ) {
						$this->args = $query;
					} else {
						$this->args = [];
						return false;
					}
				}
			}

			// Not callable.
			else {

				if ( \function_exists( 'vc_value_from_safe' ) ) {
					// Fix for threaded arrays. Ex: &orderby[meta_value_num]=ASC&orderby[menu_order]=ASC&orderby[date]=DESC
					// VC saves the [] as {} to prevent conflicts since shortcodes use []
					$query = \str_replace( '`{`', '[', $query );
					$query = \str_replace( '`}`', ']', $query );
					$query = \html_entity_decode( \vc_value_from_safe( $query ), ENT_QUOTES, 'utf-8' );
				}

				\parse_str( $query, $this->args );
				$this->parse_custom_query_args();
				$this->parse_dynamic_values();
			}
		} elseif ( \is_array( $query ) ) {
			$this->args = $query;
		}

		// Add empty values that should be added.
		if ( empty( $this->args['post_type'] ) ) {
			$this->args['post_type'] = ! empty( $this->atts['post_type'] ) ? $this->atts['post_type'] : '';
		}

		if ( empty( $this->args['posts_per_page'] ) ) {
			$this->args['posts_per_page'] = 4;
		}

		// Turn args into arrays.
		if ( ! empty( $this->args['post__in'] ) ) {
			$this->args['post__in'] = $this->string_to_array( $this->args['post__in'] );
		}
		if ( ! empty( $this->args['post__not_in'] ) ) {
			$this->args['post__not_in'] = $this->string_to_array( $this->args['post__not_in'] );
		}

		// Add related args if enabled.
		if ( ! empty( $this->args['related'] ) ) {
			$this->add_related_args(); // Add related last
		}

		// Add related args if enabled.
		if ( 'product' === $this->args['post_type'] && ! empty( $this->args['featured'] ) && \vcex_validate_boolean( $this->args['featured'] ) ) {
			$this->parse_featured_products_only( true );
		}

		// Pagination is disabled by default on custom queries unless the pagination arg is set to true.
		if ( ! empty( $this->atts['pagination'] ) && 'false' !== $this->atts['pagination'] ) {
			$has_pagination = $this->atts['pagination'];
		} else {
			$has_pagination = \vcex_validate_boolean( $this->args['pagination'] ?? false );
		}

		$this->parse_pagination( $has_pagination );
	}

	/**
	 * Meta Query.
	 */
	private function parse_meta_query( $value ): void {
		if ( ! $value || ! vcex_validate_boolean( $value ) || empty( $this->atts['meta_query_items'] ) ) {
			return;
		}

		$meta_query_items = $this->atts['meta_query_items'];

		if ( ! is_array( $meta_query_items ) ) {
			$meta_query_items = \vcex_vc_param_group_parse_atts( $meta_query_items );
		}

		if ( ! $meta_query_items ) {
			return;
		}

		$meta_args = [
			'relation' => $this->sanitize_relation( $this->atts['meta_query_relation'] ?? 'AND' ),
		];

		foreach ( $meta_query_items as $meta_query_item ) {
			$meta_value = $meta_query_item['value'] ?? '';
			if ( $meta_value && str_contains( $meta_value, ',' ) ) {
				$meta_value = $this->string_to_array( $meta_value );
			}
			$meta_args[] = \array_filter( [
				'key'     => $meta_query_item['key'] ?? '',
				'value'   => $this->parse_dynamic_value( $meta_value ),
				'compare' => $meta_query_item['compare'] ?? '',
				'type'    => $meta_query_item['type'] ?? '',
			] );
		}

		$this->add_meta_query( [ $meta_args ] );
	}

	/**
	 * Posts Status.
	 */
	private function parse_post_status( $value ): void {
		if ( $value ) {
			$this->args['post_status'] = \sanitize_text_field( $value );
		}
	}

	/**
	 * Posts In.
	 */
	private function parse_posts_in( $value ): void {
		if ( $value ) {
			$value = $this->string_to_array( $value );
			if ( $value ) {
				$value = array_map( 'absint', $value );
				\array_walk( $value, [ $this, 'add_post__in' ] );
			}
			$this->args['ignore_sticky_posts'] = true;
		}
	}

	/**
	 * Post In.
	 */
	private function parse_post__in( $value ): void {
		if ( $value ) {
			$value = $this->string_to_array( $value );
			if ( $value ) {
				$value = array_map( 'absint', $value );
				\array_walk( $value, [ $this, 'add_post__in' ] );
			}
			$this->args['ignore_sticky_posts'] = true;
		}
	}

	/**
	 * Parse show sticky posts setting.
	 */
	private function parse_show_sticky_posts( $value ): void {
		if ( \vcex_validate_boolean( $value ) ) {
			$this->show_sticky_posts();
		}
	}

	/**
	 * Exclude sticky posts.
	 */
	private function parse_exclude_sticky_posts( $value ): void {
		if ( $value && \vcex_validate_boolean( $value ) ) {
			$this->exclude_sticky_posts();
		}
	}

	/**
	 * Exclude current post
	 */
	private function parse_exclude_current_post( $value ): void {
		if ( $value && \vcex_validate_boolean( $value ) && $this->post_id ) {
			$this->exclude_post_id( $this->post_id );
		}
	}

	/**
	 * Offset.
	 */
	private function parse_offset( $value ): void {
		if ( $value ) {
			$this->args['offset'] = \absint( $value );
		}
	}

	/**
	 * Limit by Author.
	 */
	private function parse_author_in( $value ): void {
		if ( $value ) {
			$this->args['author__in'] = \array_map( 'absint', $this->string_to_array( $value ) );
			$this->args['ignore_sticky_posts'] = true;
		}
	}

	/**
	 * Show only items with thumbnails.
	 */
	private function parse_thumbnail_query( $value ): void {
		if ( $value && \vcex_validate_boolean( $value ) ) {
			$this->add_meta_query( [
				'key' => '_thumbnail_id',
			] );
		}
	}

	/**
	 * Count.
	 */
	private function parse_count( $value ): void {
		$value = $value ?: -1;
		$this->args['posts_per_page'] = (int) $value;
	}

	/**
	 * Posts Per Page.
	 */
	private function parse_posts_per_page( $value ): void {
		$value = $value ?? $this->atts['posts_per_page'] ?? '';
		$value = $value ?: -1;
		$this->args['posts_per_page'] = (int) $value;
	}

	/**
	 * Pagination.
	 */
	private function parse_pagination( $value ): void {
		if ( ! empty( $this->atts['paged'] ) ) {
			$this->args['paged'] = $this->atts['paged'];
		} else {
			if ( $this->has_loadmore() || $this->is_ajaxed_pagination() ) {
				$value = true; // always true when loadmore is enabled.
			}
			if ( $value ) {
				if ( \get_query_var( 'page' ) ) {
					$paged = \get_query_var( 'page' );
				} elseif ( \get_query_var( 'paged' ) ) {
					$paged = \get_query_var( 'paged' );
				} else {
					$paged = 1;
				}
				$this->args['paged'] = $paged;
			} else {
				$this->args['no_found_rows'] = true;
			}
		}
	}

	/**
	 * Ignore sticky posts.
	 */
	private function parse_ignore_sticky_posts( $value ): void {
		if ( $value && \vcex_validate_boolean( $value ) ) {
			$this->args['ignore_sticky_posts'] = true;
		}
	}

	/**
	 * Orderby.
	 */
	private function parse_orderby( $value ): void {
		$value = $value ?? $this->atts['orderby'] ?? '';
		if ( $value && 'menu_order' !== $value ) {
			$this->args['ignore_custom_sort'] = true; // Fix for post types order plugin.
		}
		if ( 'woo_price' === $value ) {
			$this->add_clause( [ $this, 'post_clauses_orderby_price' ] );
		} elseif ( 'woo_best_selling' === $value ) {
			$this->add_clause( [ $this, 'post_clauses_orderby_sales' ] );
		} elseif ( 'woo_top_rated' === $value ) {
			$this->add_clause( [ $this, 'post_clauses_orderby_rating' ] );
		} elseif ( ! empty( $this->atts['posts_in'] ) && ! $value ) {
			$this->args['orderby'] = 'post__in';
		} elseif ( ! empty( $value ) && \is_string( $value ) && 'default' !== $value ) {
			$this->args['orderby'] = \sanitize_sql_orderby( $value );
		}
	}

	/**
	 * Orderby meta key.
	 */
	private function parse_orderby_meta_key( $value ): void {
		if ( $value
			&& ! empty( $this->args['orderby'] )
			&& \in_array( $this->args['orderby'], [ 'meta_value', 'meta_value_num' ], true )
		) {
			$this->args['meta_key'] = \sanitize_text_field( $value );
		}
	}

	/**
	 * Order.
	 */
	private function parse_order( $value ): void {
		$value = $value ?? $this->atts['order'] ?? '';
		$this->args['order'] = ( 'ASC' === \strtoupper( $value ) ) ? 'ASC' : 'DESC';
	}

	/**
	 * Post Types.
	 */
	private function parse_post_type( $value ): void {
		$value = $value ?: [ 'post' ];
		$this->args['post_type'] = $this->string_to_array( $value );
	}

	/**
	 * Post Types.
	 */
	private function parse_post_types( $value ): void {
		$value = $value ?: [ 'post' ];
		$this->args['post_type'] = $this->string_to_array( $value );
	}

	/**
	 * Author.
	 */
	private function parse_authors( $value ): void {
		if ( $value ) {
			$this->args['author'] = \sanitize_text_field( $value );
		}
	}

	/**
	 * Terms in, terms out.
	 */
	private function parse_terms_in_out(): void {
		if ( ! empty( $this->atts['terms_in'] ) && ! $this->ignore_tax_query() ) {
			$this->terms_in_out( 'in' );
		}
		if ( ! empty( $this->atts['terms_not_in'] ) ) {
			$this->terms_in_out( 'not_in' );
		}
	}

	/**
	 * Tax Query.
	 */
	private function parse_tax_query( $value ): void {
		// New method.
		$this->parse_terms_in_out();

		// The older/deprecated method.
		if ( 'true' === $value ) {
			$this->tax_query_terms();
		} elseif ( 'false' !== $value ) {
			$this->include_exclude_cats();
		}
	}

	/**
	 * Adds tax query based on tax_query_terms att.
	 *
	 * @deprecated 5.6 - replaced by terms_in
	 */
	private function tax_query_terms(): void {
		if ( empty( $this->atts['tax_query_taxonomy'] )
			|| empty( $this->atts['tax_query_terms'] )
			|| ( 'wpex_post_cards' === $this->shortcode_tag && ! empty( $this->atts['terms_in'] ) )
			|| ! \taxonomy_exists( $this->atts['tax_query_taxonomy'] )
		) {
			return;
		}

		$tax_query_taxonomy = $this->atts['tax_query_taxonomy'];
		$tax_query_terms    = $this->string_to_array( $this->atts['tax_query_terms'] );

		if ( ! $tax_query_terms ) {
			return;
		}

		if ( 'post_format' === $tax_query_taxonomy && \in_array( 'post-format-standard', $tax_query_terms ) ) {

			$all_formats = [
				'post-format-aside',
				'post-format-gallery',
				'post-format-link',
				'post-format-image',
				'post-format-quote',
				'post-format-status',
				'post-format-audio',
				'post-format-chat',
				'post-format-video',
			];

			foreach ( $tax_query_terms as $k => $v ) {
				if ( \in_array( $v, $all_formats ) ) {
					unset( $all_formats[ $k ] );
				}
			}

			$this->args['tax_query'] = [
				'relation' => 'AND',
				[
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => $all_formats,
					'operator' => 'NOT IN',
				],
			];

		} else {

			$this->args['tax_query'] = [
				'relation' => 'AND',
				[
					'taxonomy' => $tax_query_taxonomy,
					'field'    => 'slug',
					'terms'    => $tax_query_terms,
				],
			];

		}

	}

	/**
	 * Parses the terms_in and terms_not_in atts.
	 */
	private function terms_in_out( $in_out = 'in' ): void {
		$att = "terms_{$in_out}";

		if ( empty( $this->atts[ $att ] ) ) {
			return;
		}

		$terms = $this->atts[ $att ];

		if ( \is_string( $terms ) ) {
			$terms = preg_split( '/\,[\s]*/', $terms );
		}

		if ( ! $terms || ! \is_array( $terms ) ) {
			return;
		}

		if ( ! isset ( $this->args['tax_query'] ) ) {
			$this->args['tax_query'] = [
				'relation' => 'AND',
			];
		}

		$tax_queries = [];

		foreach ( $terms as $term ) {
			$term_obj = get_term_by( 'term_taxonomy_id', $term );
			if ( $term_obj && ! is_wp_error( $term_obj ) ) {
				if ( isset( $tax_queries[ $term_obj->taxonomy ] ) ) {
					$tax_queries[ $term_obj->taxonomy ][] = $term;
				} else {
					$tax_queries[ $term_obj->taxonomy ] = [ $term ];
				}
			}
			// Non- existing category.
			elseif ( 'in' === $in_out ) {
				// Do nothing - let the query display all items.
			}
		}

		$operator = ( 'not_in' === $in_out ) ? 'NOT IN' : 'IN';

		foreach ( $tax_queries as $taxonomy => $terms ) {
			$this->args['tax_query'][] = [
				'taxonomy' => $taxonomy,
				'operator' => $operator,
				'terms'    => $terms,
				'field'    => 'term_taxonomy_id', // !!! important !!!
			];
		}

	}

	/**
	 * Include/Exclude categories
	 */
	private function include_exclude_cats(): void {
		if ( empty( $this->atts['include_categories'] ) && empty( $this->atts['exclude_categories'] ) ) {
			return;
		}

		$terms = $this->get_terms();

		// Return if no terms.
		if ( empty( $terms ) ) {
			$this->args['tax_query'] = NULL;
		}

		// The tax query relation.
		$this->args['tax_query'] = [
			'relation' => 'AND',
		];

		// Get taxonomies.
		$taxonomies = $this->get_taxonomies();

		if ( 1 === \count( $taxonomies ) ) {

			// Includes.
			if ( ! empty( $terms['include'] ) ) {
				$this->args['tax_query'][] = [
					'taxonomy' => $taxonomies[0],
					'field'    => 'term_id',
					'terms'    => $terms['include'],
					'operator' => 'IN',
				];
			}

			// Excludes.
			if ( ! empty( $terms['exclude'] ) ) {
				$this->args['tax_query'][] = [
					'taxonomy' => $taxonomies[0],
					'field'    => 'term_id',
					'terms'    => $terms['exclude'],
					'operator' => 'NOT IN',
				];
			}

		}

		// More then 1 taxonomy.
		elseif ( $taxonomies ) {

			// Merge terms.
			$merge_terms = \array_merge( $terms['include'], $terms['exclude'] );

			// Loop through terms to build tax_query.
			$get_terms = \get_terms( $taxonomies, [
				'include' => $merge_terms,
			] );
			foreach ( $get_terms as $term ) {
				$operator = \in_array( $term->term_id, $terms['exclude'] ) ? 'NOT IN' : 'IN';
				$this->args['tax_query'][] = [
					'field'    => 'term_id',
					'taxonomy' => $term->taxonomy,
					'terms'    => $term->term_id,
					'operator' => $operator,
				];
			}

		}
	}

	/**
	 * Include Categories.
	 */
	private function include_categories() {
		if ( empty( $this->atts['include_categories'] ) ) {
			return;
		}
		$taxonomies = $this->get_taxonomies();
		$taxonomy   = $taxonomies[0];
		return $this->sanitize_autocomplete( $this->atts['include_categories'], $taxonomy );
	}

	/**
	 * Exclude Categories.
	 */
	private function exclude_categories() {
		if ( empty( $this->atts['exclude_categories'] ) ) {
			return;
		}
		$taxonomies = $this->get_taxonomies();
		$taxonomy   = $taxonomies[0];
		return $this->sanitize_autocomplete( $this->atts['exclude_categories'], $taxonomy );
	}

	/**
	 * Get taxonomies.
	 */
	private function get_taxonomies() {
		if ( ! empty( $this->atts['taxonomy'] ) ) {
			return [ $this->atts['taxonomy'] ];
		} elseif ( ! empty( $this->atts['post_type'] ) ) {
			$tax = vcex_get_post_type_cat_tax( $this->atts['post_type'] );
			if ( $tax ) {
				return $this->string_to_array( $tax );
			}
		} elseif( ! empty( $this->atts['taxonomies'] ) ) {
			return $this->string_to_array( $this->atts['taxonomies'] );
		}
	}

	/**
	 * Get the terms to include in the Query.
	 */
	private function get_terms() {
		$terms = [
			'include' => [],
			'exclude' => [],
		];

		$include_categories = $this->include_categories();
		if ( ! empty( $include_categories ) ) {
			foreach ( $include_categories as $cat ) {
				$terms['include'][] = $cat;
			}
		}

		$exclude_categories = $this->exclude_categories();
		if ( ! empty( $exclude_categories ) ) {
			foreach ( $exclude_categories as $cat ) {
				$terms['exclude'][] = $cat;
			}
		}

		return $terms;
	}

	/**
	 * Featured products only.
	 */
	private function parse_featured_products_only( $value ): void {
		if ( $value && \vcex_validate_boolean( $value ) ) {
			if ( empty( $this->args['tax_query'] ) ) {
				$this->args['tax_query'] = [];
			}
			$this->args['tax_query']['relation'] = 'AND';
			$this->args['tax_query'][] = [
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured'
			];
		}
	}

	/**
	 * Products out of stock.
	 */
	private function parse_exclude_products_out_of_stock( $value ): void {
		if ( $value && \vcex_validate_boolean( $value ) ) {
			$this->args['meta_query'] = [
				[
					'key'     => '_stock_status',
					'value'   => 'outofstock',
					'compare' => 'NOT IN'
				],
			];
		}
	}

	/**
	 * Returns today's date.
	 */
	private function get_current_date_time(): string {
		$datetime = new \DateTimeImmutable( 'now', \wp_timezone() );
		return $datetime->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Converts a string to an Array.
	 */
	private function string_to_array( $value ) {
		if ( $value ) {
			if ( \is_array( $value ) ) {
				return $value;
			}
			$array = [];
			$items = \preg_split( '/\,[\s]*/', $value );
			foreach ( $items as $item ) {
				if ( \strlen( $item ) > 0 ) {
					$array[] = $item;
				}
			}
			return $array;
		}
	}

	/**
	 * Add new item to the clauses.
	 */
	private function add_clause( $callback ): void {
		$this->clauses[] = $callback;
	}

	/**
	 * Add clause filters.
	 */
	private function add_clause_filters(): void {
		foreach ( $this->clauses as $k => $callback ) {
			if ( \is_callable( $callback ) ) {
				\add_filter( 'posts_clauses', $callback );
			} else {
				unset( $this->clauses[ $k ] );
			}
		}
	}

	/**
	 * Remove clause filters.
	 */
	private function remove_clause_filters(): void {
		foreach ( $this->clauses as $callback ) {
			\remove_filter( 'posts_clauses', $callback );
		}
	}

	/**
	 * Sanitizes relation.
	 */
	private function sanitize_relation( string $relation = '' ): string {
		$relation = $relation ? \strtoupper( \sanitize_text_field( $relation ) ) : 'AND';
		return \in_array( $relation, [ 'AND', 'OR' ], true ) ? $relation : 'AND';
	}

	/**
	 * Sanitizes autocomplete data and returns ID's of terms to include or exclude.
	 */
	private function sanitize_autocomplete( $terms, $taxonomy ) {
		if ( \is_string( $terms ) ) {
			$terms = \preg_split( '/\,[\s]*/', $terms );
		}
		if ( ! \is_array( $terms ) ) {
			return;
		}
		$return = [];
		foreach ( $terms as $term ) {
			$field = ( \is_numeric( $term ) ) ? 'id' : 'slug';
			$term_data = \get_term_by( $field, $term, $taxonomy );
			if ( $term_data ) {
				$return[] = $term_data->term_id;
			}
		}
		return $return;
	}

	/**
	 * Returns related tax query.
	 */
	private function add_related_args() {
		$post_id          = $this->post_id;
		$related_terms    = [];
		$related_taxonomy = '';

		if ( empty( $this->args['post_type'] ) ) {
			$this->args['post_type'] = \get_post_type( $post_id );
		}

		if ( isset( $this->args['post__not_in'] ) && \is_array( $this->args['post__not_in'] ) ) {
			$this->args['post__not_in'][] = $post_id;
		} else {
			$this->args['post__not_in'] = [ $post_id ];
		}

		if ( isset( $this->args['taxonomy'] ) ) {
			$related_taxonomy = $this->args['taxonomy'];
			unset( $this->args['taxonomy'] );
		} else {
			$post_type = \is_array( $this->args['post_type'] ) ? $this->args['post_type'][0] : $this->args['post_type'];
			if ( \function_exists( '\wpex_get_ptu_type_mod' ) ) {
				$related_taxonomy = \wpex_get_ptu_type_mod( $post_type, 'related_taxonomy' );
			}
			if ( ! $related_taxonomy && \function_exists( 'wpex_get_post_type_cat_tax' ) ) {
				$related_taxonomy = \wpex_get_post_type_cat_tax( $post_type );
			}
		}

		if ( $related_taxonomy && \taxonomy_exists( $related_taxonomy ) ) {
			if ( \function_exists( 'totaltheme_get_post_primary_term' ) ) {
				$primary_term = \totaltheme_get_post_primary_term( $post_id, $related_taxonomy, false );
			}
			if ( ! empty( $primary_term ) ) {
				$related_terms = [ $primary_term->term_id ];
			} else {
				$get_terms = \get_the_terms( $post_id, $related_taxonomy );
				if ( $get_terms && ! \is_wp_error( $get_terms ) ) {
					$related_terms = \wp_list_pluck( $get_terms, 'term_id' );
				}
			}
			if ( $related_terms ) {
				$this->args['tax_query'] = [
					'relation' => 'AND',
					[
						'taxonomy' => $related_taxonomy,
						'field'    => 'term_id',
						'terms'    => $related_terms,
					]
				];
			} elseif ( ! \apply_filters( 'vcex_query_builder_related_fallback_items', true ) ) {
				$this->args['tax_query'] = [
					'relation' => 'AND',
					[
						'taxonomy' => $related_taxonomy,
						'field'    => 'term_id',
						'terms'    => [],
					]
				];
			}
		}

		return $related_terms;
	}

	/**
	 * This function allows for dynamic values when building queries.
	 */
	private function get_dynamic_strings() {
		if ( ! is_null( $this->dynamic_strings ) ) {
			return $this->dynamic_strings;
		}

		$strings = [
			'current_post'   => $this->post_id,
			'current_term'   => $this->get_current_term(),
			'current_author' => $this->get_current_author(),
			'current_user'   => 'get_current_user_id',
			'today'          => \date( 'Ymd' ),
			'gt'             => '>',
			'gte'            => '>=',
			'lt'             => '<',
			'lte'            => '<=',
		];

		$this->dynamic_strings = (array) \apply_filters( 'vcex_grid_advanced_query_dynamic_values', $strings );

		return $this->dynamic_strings;
	}

	/**
	 * This function allows for dynamic values when building queries.
	 */
	private function parse_dynamic_values() {
		if ( ! \is_array( $this->args ) ) {
			return $this->args;
		}
		$this->args = $this->array_search_replace_dynamic_value( $this->args );
	}

	/**
	 * This function allows for dynamic values when building queries.
	 */
	private function parse_custom_query_args() {
		if ( ! \is_array( $this->args ) ) {
			return $this->args;
		}
		$this->args = $this->array_search_replace_custom_arg( $this->args );
	}

	/**
	 * Searches and replaces custom query arguments.
	 */
	private function array_search_replace_custom_arg( $array = [] ) {
		foreach ( $array as $key => $val ) {
			if ( \is_array( $val ) ) {
				$array[ $key ] = $this->array_search_replace_custom_arg( $val );
			} elseif (
				\in_array( $key, $this->array_supported_params() )
				&& \is_string( $val )
				&& false !== \strpos( $val, ',' )
			) {
				$array[ $key ] = \explode( ',', $val );
			}
		}
		return $array;
	}

	/**
	 * Searches and replaces a dynamic value in an array.
	 */
	private function array_search_replace_dynamic_value( $array = [] ) {
		foreach ( $array as $key => $val ) {
			if ( \is_array( $val ) ) {
				$array[ $key ] = $this->array_search_replace_dynamic_value( $val );
			} else {
				$array[ $key ] = $this->parse_dynamic_value( $val );
			}
		}
		return $array;
	}

	/**
	 * Parses a specific dynamic value.
	 */
	private function parse_dynamic_value( $val = '' ) {
		if ( $val && \is_string( $val ) ) {
			$dynamic_strings = $this->get_dynamic_strings();
			// Must be a registered dynamic string.
			if ( \array_key_exists( $val, $dynamic_strings ) ) {
				$dynamic_val = $dynamic_strings[ $val ];
				$strings_w_value = [
					'current_post',
					'current_term',
					'current_author',
					'current_user',
					'today',
					'gt',
					'gte',
					'lt',
					'lte',
				];
				if ( ! \in_array( $dynamic_val, $strings_w_value, true ) && \is_callable( $dynamic_val ) ) {
					$val = \call_user_func( $dynamic_val );
				} else {
					$val = $dynamic_val;
				}
			}
		}
		return $val;
	}

	/**
	 * Check if loadmore is enabled.
	 */
	private function has_loadmore() {
		if ( ( ! empty( $this->atts['pagination'] ) && \in_array( $this->atts['pagination'], [ 'loadmore', 'infinite_scroll' ] ) ) || \vcex_validate_att_boolean( 'pagination_loadmore', $this->atts ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check if we are using ajaxed pagination.
	 */
	private function is_ajaxed_pagination() {
		if ( ! empty( $this->atts['pagination'] ) && 'numbered_ajax' === $this->atts['pagination'] ) {
			return true;
		}
		return false;
	}

	/**
	 * Return post ID.
	 */
	private function get_current_post_ID() {
		if ( ! empty( $this->atts['post_id'] ) ) {
			return $this->atts['post_id']; // used for threaded cards using ajax.
		}
		$id = 0;
		if ( $this->doing_ajax && ! \totalthemecore_call_static( 'Vcex\Post_Cards', 'is_threaded' ) ) {
			$id = \url_to_postid( \wp_get_referer() );
		}
		return $id ?: \get_the_ID();
	}

	/**
	 * Return current user ID.
	 */
	private function get_current_author() {
		return \get_the_author_meta( 'ID' );
	}

	/**
	 * Get current term.
	 */
	private function get_current_term() {
		return \is_tax() ? \get_queried_object()->term_id : '';
	}

	/**
	 * Get the singular post type that is being displayed.
	 */
	private function get_post_type() {
		$post_type = null;

		if ( isset( $this->args['post_type'] ) ) {
			if ( \is_string( $this->args['post_type'] ) ) {
				return $this->args['post_type'];
			} elseif ( \is_array( $this->args['post_type'] ) && 1 === \count( $this->args['post_type'] ) ) {
				return $this->args['post_type'][0];
			}
		}

		return $post_type;
	}

	/**
	 * Exclude offset posts.
	 */
	private function maybe_exclude_offset_posts() {
		if ( empty( $this->args['offset'] ) ) {
			return;
		}

		$query_args = $this->args;
		$query_args['posts_per_page'] = $this->args['offset'];
		$query_args['fields'] = 'ids';

		// Exclude sticky posts when finding the offset items.
		if ( isset( $this->args['post__not_in'] ) && \is_array( $this->args['post__not_in'] ) ) {
			$query_args['post__not_in'] = \array_merge( $this->args['post__not_in'], \get_option( 'sticky_posts' ) );
		} else {
			$query_args['post__not_in'] = \get_option( 'sticky_posts' );
		}

		unset( $query_args['offset'] );

		$excluded_posts = new \WP_Query( $query_args );

		if ( $excluded_posts->have_posts() ) {
			$excluded_posts = $excluded_posts->posts;
			if ( \is_array( $excluded_posts ) ) {
				if ( isset( $this->args['post__not_in'] ) && \is_array( $this->args['post__not_in'] ) ) {
					$this->args['post__not_in'] = \array_merge( $this->args['post__not_in'], $excluded_posts );
				} else {
					$this->args['post__not_in'] = $excluded_posts;
				}
				unset( $this->args['offset'] );
			}
		}
	}

	/**
	 * Exclude featured card from query.
	 */
	private function maybe_exclude_featured_card() {
		if ( ! \vcex_validate_att_boolean( 'featured_card', $this->atts )
			|| empty( $this->atts['featured_post_id'] )
		) {
			return;
		}

		$this->exclude_post_id( $this->atts['featured_post_id'] );
		if ( $this->args['posts_per_page'] && \floatval( $this->args['posts_per_page'] ) > 1 ) {
			if ( $this->is_auto_query() && ( \vcex_doing_ajax() || \vcex_doing_loadmore() ) ) {
				return; // not needed here.
			}
			$this->args['posts_per_page'] = \absint( $this->args['posts_per_page'] ) - 1;
		}
	}

	/**
	 * Exclude post ID from query.
	 */
	private function exclude_post_id( int $post_id = 0 ) {
		if ( ! $post_id ) {
			return;
		}

		$this->add_post__not_in( $post_id );

		// Needs to be removed from post__in if it was there for some reason - important!
		if ( isset( $this->args['post__in'] ) && \is_array( $this->args['post__in'] ) ) {
			if ( ( $key = \array_search( $post_id, $this->args['post__in'] ) ) !== false ) {
				unset( $this->args['post__in'][ $key ] );
			}
		}
	}

	/**
	 * Returns an array of params that support array values.
	 */
	private function array_supported_params() {
		return [
			'post_type',
			'terms',
			'author__in',
			'author__not_in',
			'category__and',
			'category__in',
			'category__not_in',
			'tag__and',
			'tag__in',
			'tag__not_in',
			'tag_slug__and',
			'tag_slug__in',
			'post_parent__in',
			'post_parent__not_in',
			'post__in',
			'post__not_in',
			'post_name__in',
		];
	}

	/**
	 * Check URL sorting.
	 */
	private function check_url_sort() {
		if ( $url_sort = totalthemecore_call_static( 'Vcex\Url_Sort_Query', 'get_query' ) ) {
			$this->atts['ajax_filter'] = [ 'selection' => $url_sort ];
		}
	}

	/**
	 * Check ajax.
	 */
	private function filter() {
		if ( ! $this->doing_ajax
			&& empty( $this->atts['ajax_filter'] )
			&& \vcex_validate_att_boolean( 'url_sort', $this->atts )
		) {
			$this->check_url_sort();
		}

		if ( empty( $this->atts['ajax_filter'] ) ) {
			return;
		}

		$ajax_args = $this->atts['ajax_filter'];

		if ( \is_string( $ajax_args ) ) {
			$ajax_args = \json_decode( \stripslashes( $ajax_args ), true );
		}

		if ( ! \is_array( $ajax_args ) ) {
			return;
		}

		$parsed_filter = [];

		$filter_selection = $ajax_args['selection'] ?? null;

		if ( ! \is_array( $filter_selection ) || ! $filter_selection ) {
			return;
		}

		$tax_args  = [];
		$meta_args = [];

		// Loop through selection.
		foreach ( $filter_selection as $type => $value ) {
			$safe_type  = \sanitize_text_field( $type );
			$safe_value = \sanitize_text_field( $value );

			if ( ! $safe_value && '0' !== $safe_value ) {
				continue; // sanity check / @note some values can be 0 if checking for false or 0 meta values.
			}

			if ( \in_array( $safe_type, [ 'search', 'order', 'orderby', 'post_type', 'post', 'sticky' ] ) ) {
				$query_type = $safe_type;
			} else {
				$query_type = \taxonomy_exists( $safe_type ) ? 'taxonomy' : 'meta';
			}

			switch ( $query_type ) {
				case 'order':
				case 'orderby':
					$this->args[ $query_type ] = $safe_value;
					break;
				case 'search':
					$this->args['s'] = $safe_value;
					break;
				case 'post_type':
					$this->args['post_type'] = $this->string_to_array( $safe_value );
					break;
				case 'meta':
					$meta_item = $this->parse_filter_meta_val( $safe_value );
					if ( ! empty( $meta_item['value'] ) ) {
						$meta_item['key'] = $safe_type;
						$meta_args[] = $meta_item;
					}
					break;
				case 'taxonomy':
					$tax_item = $this->parse_filter_tax_val( $safe_value );
					if ( isset( $tax_item['value'] ) && \is_array( $tax_item['value'] ) ) {
						$tax_item_terms = $tax_item['value'];

						// Default tax item args.
						$tax_item_args = [
							'taxonomy'         => $safe_type,
							'field'            => \is_numeric( $tax_item_terms[0] ) ? 'term_id' : 'slug',
							'operator'         => $tax_item['operator'] ?? 'IN',
							'include_children' => $tax_item['include_children'] ?? $ajax_args['include_children'] ?? true,
						];

						// Single tax term.
						if ( 1 === \count( $tax_item['value'] ) ) {
							if ( \is_numeric( $tax_item['value'][0] ) && \floatval( $tax_item['value'][0] ) < 0 ) {
								$tax_item_args['operator'] = 'NOT IN';
								$tax_item['value'] = [ \absint( $tax_item['value'][0] ) ];
							}
							$tax_item_args['terms'] = $tax_item['value'];
							$tax_args[] = $tax_item_args;
						}

						/**
						 * Multiple tax terms.
						 *
						 * We seperate each term into it's own tax_query array so we can use negative term ID's for excluding terms.
						 *
						 * @note There is a WP bug when including parent and child terms in the same array.
						 */
						else {
							
							// Create a new tax item array to combine the terms.
							$tax_item_array = [
								'relation' => $this->sanitize_relation( $tax_item['relation'] ?? $ajax_args['tax_relation'] ?? $ajax_args['relation'] ?? 'AND' ),
							];

							// Loop through each term and add it to the tax item array.
							foreach ( $tax_item_terms as $tax_item_term ) {
								$tax_item_term_args = $tax_item_args; // must reset for each item.
								// Allows using negative numbers as NOT IN checks.
								if ( \is_numeric( $tax_item_term ) && \floatval( $tax_item_term ) < 0 ) {
									$tax_item_term_args['operator'] = 'NOT IN';
									$tax_item_term = \absint( $tax_item_term );
								}
								$tax_item_term_args['terms'] = $tax_item_term;
								$tax_item_array[] = $tax_item_term_args;
							}
							$tax_args[] = $tax_item_array;
						}
						
					}
					break;
				case 'sticky':
					if ( \vcex_validate_boolean( $safe_value ) ) {
						$this->show_sticky_posts();
					} else {
						$this->exclude_sticky_posts();
					}
					break;
				case 'post':
					$operator = \strtoupper( \sanitize_text_field( $filter_item['operator'] ?? 'IN' ) );
					if ( \str_contains( $safe_value, ',' ) ) {
						$posts = $this->string_to_array( $safe_value );
					} else {
						$posts = [ $safe_value ];
					}
					if ( $posts ) {
						foreach ( $posts as $post_id ) {
							switch ( $operator ) {
								case 'IN':
									$this->add_post__in( absint( $post_id ) );
									break;
								case 'NOT IN':
									$this->add_post__not_in( absint( $post_id ) );
									break;
							}
						}
					}
					break;
			}

		}

		if ( $tax_args ) {
			if ( \count( $tax_args ) > 1 ) {
				$tax_args = \array_merge( [
					'relation' => $this->sanitize_relation( $ajax_args['tax_relation'] ?? $ajax_args['relation'] ?? 'AND' ),
				], $tax_args );
			}
			$this->add_tax_query( $tax_args );
		}

		if ( $meta_args ) {
			if ( \count( $meta_args ) > 1 ) {
				$meta_args = \array_merge( [
					'relation' => $this->sanitize_relation( $ajax_args['meta_relation'] ?? $ajax_args['relation'] ?? 'AND' ),
				], $meta_args );
				$this->add_meta_query( $meta_args );
			} else {
				if ( ! empty( $this->args['meta_query'] ) ) {
					$this->args['meta_query'] = \array_merge( $this->args['meta_query'], $meta_args );
				} else {
					$this->args['meta_query'] = $meta_args;
				}
			}
		}

	}

	/**
	 * Parses filter item taxonomy value.
	 */
	private function parse_filter_tax_val( string $val ) {
		if ( \str_contains( $val, '|' ) ) {
			$array = \explode( '|', $val );
			return [
				'value'    => \array_map( '\sanitize_text_field', $this->string_to_array( $array[0] ) ),
				'relation' => \strtoupper( \html_entity_decode( \sanitize_text_field( $array[1] ) ) )
			];
		}
		return [
			'value' => \array_map( '\sanitize_text_field', $this->string_to_array( $val ) ),
		];
	}

	/**
	 * Parses filter item meta value.
	 */
	private function parse_filter_meta_val( string $val ) {
		if ( ! str_contains( $val, '|' ) ) {
			return [
				'value'   => \sanitize_text_field( $val ),
				'type'    => 'CHAR',
				'compare' => '=',
			];
		}
		if ( $array = \explode( '|', $val ) ) {
			$new_val = [
				'value'   => '',
				'type'    => 'CHAR',
				'compare' => '=',
			];
			$new_val['value'] = \sanitize_text_field( $array[0] );
			if ( isset( $array[2] ) ) {
				$new_val['compare'] = \html_entity_decode( \sanitize_text_field( $array[1] ) );
				$new_val['type'] = \sanitize_text_field( $array[2] );
			} elseif ( isset( $array[1] ) ) {
				$meta_types = [
					'NUMERIC',
					'BINARY',
					'CHAR',
					'DATE',
					'DATETIME',
					'DECIMAL',
					'SIGNED',
					'TIME',
					'UNSIGNED'
				];
				if ( \in_array( $array[1], $meta_types, true ) ) {
					$new_val['type'] = $array[1];
				} else {
					$new_val['compare'] = \html_entity_decode( \sanitize_text_field( $array[1] ) );
				}
			}

			// Sanitize value.
			$new_val['value'] = ( 'INT' === $new_val['type'] ) ? \floatval( $new_val['value'] ) : \sanitize_text_field( $new_val['value'] );

			return $new_val;
		}
	}

	/**
	 * Checks if the original tax query should be ignored or not.
	 */
	private function ignore_tax_query(): bool {
		return ( isset( $this->atts['ignore_tax_query'] ) && \vcex_validate_boolean( $this->atts['ignore_tax_query'] ) ) || ( isset( $this->atts['ajax_filter']['ignore_tax_query'] ) && \vcex_validate_boolean( $this->atts['ajax_filter']['ignore_tax_query'] ) );
	}

	/**
	 * Show sticky posts only.
	 */
	private function show_sticky_posts(): void {
		$sticky_posts = $this->get_sticky_posts();
		if ( ! $sticky_posts ) {
			$this->set_empty_query();
		} else {
			\array_walk( $sticky_posts, [ $this, 'add_post__in' ] );
			$this->args['ignore_sticky_posts'] = true;
			unset( $this->args['offset'] );
		}
	}


	/**
	 * Exclude sticky posts.
	 */
	private function exclude_sticky_posts(): void {
		$sticky_posts = $this->get_sticky_posts();
		if ( $sticky_posts ) {
			\array_walk( $sticky_posts, [ $this, 'add_post__not_in' ] );
			$this->args['ignore_sticky_posts'] = true;
		}
	}

	/**
	 * Adds additional tax query.
	 */
	private function add_tax_query( $args ) {
		if ( ! empty( $this->args['tax_query'] ) ) {
			$this->args['tax_query'][] = $args;
		} else {
			$this->args['tax_query'] = $args;
		}
	}

	/**
	 * Adds additional meta query.
	 */
	private function add_meta_query( $args ) {
		if ( ! empty( $this->args['meta_query'] ) ) {
			$this->args['meta_query'][] = $args;
		} else {
			$this->args['meta_query'] = $args;
		}
	}

	/**
	 * Adds new post__in item
	 */
	private function add_post__in( $post_id ) {
		if ( empty( $this->args['post__in'] ) ) {
			$this->args['post__in'] = [];
		}
		$this->args['post__in'][] = $post_id;
	}

	/**
	 * Adds new post__not_in item.
	 */
	private function add_post__not_in( $post_id ) {
		if ( empty( $this->args['post__not_in'] ) ) {
			$this->args['post__not_in'] = [];
		}
		$this->args['post__not_in'][] = $post_id;
	}

	/**
	 * Get current term.
	 */
	private function final_checks() {
		$this->maybe_exclude_offset_posts();

		if ( 'wpex_post_cards' === $this->shortcode_tag ) {
			$this->maybe_exclude_featured_card();
		}

		$this->filter();

		// Always set ignore_sticky_posts to true when using post__in.
		if ( ! empty( $this->args['post__in'] ) ) {
			$this->args['ignore_sticky_posts'] = true;
		}

		// Remove relation on single taxonomy arrays.
		if ( isset( $this->args['tax_query'] )
			&& \is_array( $this->args['tax_query'] )
			&& isset( $this->args['tax_query']['relation'] )
			&& 2 === \count( $this->args['tax_query'] )
		) {
			unset( $this->args['tax_query']['relation'] );
		}

		// Check URL search param.
		if ( ! empty( $this->atts['url_search_param'] ) ) {
			$url_search_param_safe = \sanitize_text_field( $this->atts['url_search_param'] );
			if ( $url_search_param_safe && isset( $_GET[ $url_search_param_safe ] ) ) {
				$search_terms_safe = \sanitize_text_field( \urldecode( $_GET[ $url_search_param_safe ] ) );
				if ( $search_terms_safe ) {
					$this->args['s'] = str_replace( ' ', '+', $search_terms_safe ); // WP uses + between keywords.
				}
			}
		}
	}

	/**
	 * Set the query to return nothing.
	 */
	private function set_empty_query() {
		$this->args = [
			'post__in' => [ 0 ],
		];
	}

	/**
	 * Returns sticky posts.
	 */
	private function get_sticky_posts() {
		$sticky_posts = \get_option( 'sticky_posts' );
		if ( $sticky_posts && \is_array( $sticky_posts ) ) {
			// Limit sticky post queries to 50 posts to prevent long queries.
			if ( count( $sticky_posts ) > 50 ) {
				$sticky_posts = array_slice( $sticky_posts, 0, (int) apply_filters( 'totalthemecore/vcex/post_query/max_sticky_posts', 50 ) );
			}
			return \array_map( 'absint', $sticky_posts );
		}
	}

	/**
	 * Used for posts_clauses join arg.
	 * 
	 * @see WC_Query->append_product_sorting_table_join()
	 */
	private function wc_query_append_product_sorting_table_join( $sql ) {
		global $wpdb;
		if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
			$sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}
		return $sql;
	}

	/**
	 * Hooks into posts_clauses to order by price.
	 */
	public function post_clauses_orderby_price( $args ) {
		$this->parse_order( null );

		$args['join'] = $this->wc_query_append_product_sorting_table_join( $args['join'] );
		if ( 'ASC' === $this->args['order'] ) {
			$args['orderby'] = ' wc_product_meta_lookup.min_price ASC, wc_product_meta_lookup.product_id ASC ';
		} else {
			$args['orderby'] = ' wc_product_meta_lookup.max_price DESC, wc_product_meta_lookup.product_id DESC ';
		}
		return $args;
	}

	/**
	 * Hooks into posts_clauses to order by sales.
	 */
	public function post_clauses_orderby_sales( $args ) {
		$this->parse_order( null );
		$order = $this->args['order'];
		
		$args['join']    = $this->wc_query_append_product_sorting_table_join( $args['join'] );
		$args['orderby'] = " wc_product_meta_lookup.total_sales {$order}, wc_product_meta_lookup.product_id {$order} ";
		return $args;
	}

	/**
	 * Hooks into posts_clauses to order by rating.
	 */
	public function post_clauses_orderby_rating( $args ) {
		$this->parse_order( null );
		$order = $this->args['order'];

		$args['join']    = $this->wc_query_append_product_sorting_table_join( $args['join'] );
		$args['orderby'] = " wc_product_meta_lookup.average_rating {$order}, wc_product_meta_lookup.rating_count {$order}, wc_product_meta_lookup.product_id {$order} ";
		return $args;
	}

	/**
	 * Build and return the query.
	 *
	 * @todo rename filter to vcex_shortcode_query_args
	 */
	public function build() {
		if ( ! \is_null( $this->pre_query ) ) {
			return $this->pre_query;
		}

		$this->final_checks();

		$this->args = (array) \apply_filters(
			'totalthemecore/vcex/post_query/args', // consistent with term_query class.
			$this->args,
			$this->atts,
			$this->shortcode_tag
		);

		/*** deprecated ***/
		$this->args = \apply_filters( 'vcex_grid_query', $this->args, $this->atts );
		$this->args = \apply_filters( 'vcex_query_args', $this->args, $this->atts, $this->shortcode_tag );

		/** These args can not be filtered since they are used for theme functions ***/
		if ( $this->fields && 'all' !== $this->fields ) {
			$this->args['fields'] = $this->fields;
		}
		if ( isset( $this->atts['unfiltered_query_args'] ) && \is_array( $this->atts['unfiltered_query_args'] ) ) {
			foreach ( $this->atts['unfiltered_query_args'] as $k => $v ) {
				$this->args[ $k ] = $v;
			}
		}

		$this->add_clause_filters();
		
		$new_query = new \WP_Query( $this->args );

		$this->remove_clause_filters();

		return $new_query;
	}

}
