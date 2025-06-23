<?php

namespace TotalThemeCore\Widgets;

\defined( 'ABSPATH' ) || exit;

/**
 * Post With Format Icons widget.
 */
class Widget_Recent_Posts_Icons extends \TotalThemeCore\WidgetBuilder {

	/**
	 * Widget args.
	 */
	private $args;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->args = array(
			'id_base' => 'wpex_recent_posts_icons',
			'name' => $this->branding() . \esc_html__( 'Posts With Format Icons', 'total-theme-core' ),
			'options' => array(
				'customize_selective_refresh' => true,
			),
			'fields'  => array(
				array(
					'id'    => 'title',
					'label' => \esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'      => 'display_icon',
					'label'   => \esc_html__( 'Display Icon', 'total-theme-core' ),
					'type'    => 'checkbox',
					'default' => 'on',
				),
				array(
					'id'      => 'number',
					'label'   => \esc_html__( 'Number', 'total-theme-core' ),
					'type'    => 'number',
					'default' => '5',
				),
				array(
					'id'      => 'order',
					'label'   => \esc_html__( 'Order', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'query_order',
					'default' => 'DESC',
				),
				array(
					'id'      => 'orderby',
					'label'   => \esc_html__( 'Order by', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'query_orderby',
					'default' => 'date',
				),
				array(
					'id'      => 'category',
					'label'   => \esc_html__( 'Category', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'categories',
				),
				array(
					'id'       => 'post_type',
					'label'    => \esc_html__( 'Post Type', 'total-theme-core' ),
					'type'     => 'select',
					'choices'  => 'post_types',
					'default'  => 'post',
				),
				array(
					'id'      => 'taxonomy',
					'label'   => \esc_html__( 'Query By Taxonomy', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'taxonomies',
				),
				array(
					'id'          => 'terms',
					'label'       => \esc_html__( 'Include Terms', 'total-theme-core' ),
					'type'        => 'text',
					'description' => \esc_html__( 'Enter a comma seperated list of terms.', 'total-theme-core' ),
				),
				array(
					'id'          => 'terms_exclude',
					'label'       => \esc_html__( 'Exclude Terms', 'total-theme-core' ),
					'type'        => 'text',
					'description' => \esc_html__( 'Enter a comma seperated list of terms.', 'total-theme-core' ),
				),
			),
		);

		$this->create_widget( $this->args );
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		\extract( $this->parse_instance( $instance ) );

		// Before widget hook
		echo \wp_kses_post( $args['before_widget'] );

		// Display widget title
		$this->widget_title( $args, $instance );

		// Define widget output
		$output = '';

		// Query Args
		$query_args = [
			'post_type'           => ! empty( $post_type ) ? $post_type : 'post',
			'posts_per_page'      => $number,
			'orderby'             => $orderby,
			'order'               => $order,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => 1,
			'tax_query'           => [
				'relation'        => 'AND',
			],
		];

		// Exclude current post
		if ( \is_singular() ) {
			$query_args['post__not_in'] = [ get_the_ID() ];
		}

		// Query by category
		if ( 'post' == $post_type && ! empty( $category ) && 'all' != $category ) {
			$query_args['tax_query'] = [ [
				'taxonomy' => 'category',
				'field'    => 'id',
				'terms'    => $category,
			] ];
		}

		// Tax Query
		if ( ! empty( $taxonomy ) ) {

			// Include Terms
			if (  ! empty( $terms ) ) {

				// Sanitize terms and convert to array
				$terms = \str_replace( ', ', ',', $terms );
				$terms = \explode( ',', $terms );

				// Add to query arg
				$query_args['tax_query'][] = [
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $terms,
					'operator' => 'IN',
				];
			}

			// Exclude Terms
			if ( ! empty( $terms_exclude ) ) {

				// Sanitize terms and convert to array
				$terms_exclude = \str_replace( ', ', ',', $terms_exclude );
				$terms_exclude = \explode( ',', $terms_exclude );

				// Add to query arg
				$query_args['tax_query'][] = [
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $terms_exclude,
					'operator' => 'NOT IN',
				];

			}

		}

		/**
		 * Filters the "wpex_recent_posts_icons" widget query args.
		 */
		$query_args = (array) apply_filters( 'wpex_recent_posts_icons_query_args', $query_args, $args, $instance );

		// Get posts
		$wpex_query = new \WP_Query( $query_args );

		// Loop through posts
		if ( $wpex_query->have_posts() ) {

			$output .= '<ul class="widget-recent-posts-icons wpex-bordered-list wpex-clr">';

				$count = 0;

				while ( $wpex_query->have_posts() ) : $wpex_query->the_post();

					$count ++;

					$post_link = \function_exists( '\wpex_get_permalink' ) ? \wpex_get_permalink() : \get_the_permalink();

					$output .= '<li class="widget-recent-posts-icons-li">';

						$output .= '<a class="wpex-flex" href="' . \esc_url( $post_link ) . '">';

							if ( $display_icon && \function_exists( '\totaltheme_get_post_format_icon_name' ) ) {

								$icon = \totaltheme_get_post_format_icon_name();

								if ( $icon ) {

									$output .= '<div class="widget-recent-posts-icons-icon wpex-flex-shrink-0 wpex-mr-10">';

										if ( \function_exists( 'totaltheme_get_icon' ) ) {
											$icon_html = \totaltheme_get_icon( $icon, 'wpex-icon--w' );
										}

										if ( empty( $icon_html ) ) {
											$icon_html = '<span class="' . \esc_attr( $icon ) . '" aria-hidden="true"></span>';
										}

										$output .= $icon_html;

									$output .= '</div>';

								}

							}

							$output .= '<div class="widget-recent-posts-icons-title">' . \esc_html( \get_the_title() ) . '</div>';

						$output .= '</a>';

					$output .= '</li>';

				endwhile;

			$output .= '</ul>';

			\wp_reset_postdata();

		}

		echo $output;

		echo \wp_kses_post( $args['after_widget'] );
	}

}

\register_widget( 'TotalThemeCore\Widgets\Widget_Recent_Posts_Icons' );