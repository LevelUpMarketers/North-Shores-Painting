<?php

namespace TotalThemeCore\Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Post Grid widget.
 */
class Widget_Recent_Posts_Thumb_Grid extends \TotalThemeCore\WidgetBuilder {

	/**
	 * Widget args.
	 */
	private $args;

	/**
	 * Register widget with WordPress.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->args = array(
			'id_base' => 'wpex_recent_posts_thumb_grid',
			'name'    => $this->branding() . esc_html__( 'Posts Thumbnail Grid', 'total-theme-core' ),
			'options' => array(
				'customize_selective_refresh' => true,
			),
			'fields' => array(
				array(
					'id'    => 'title',
					'label' => esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'      => 'number',
					'label'   => esc_html__( 'Number', 'total-theme-core' ),
					'type'    => 'number',
					'default' => 6,
				),
				array(
					'id'      => 'columns',
					'label'   => esc_html__( 'Columns', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'grid_columns',
					'default' => '3',
				),
				array(
					'id'      => 'gap',
					'label'   => esc_html__( 'Column Gap', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'grid_gaps',
				),
				array(
					'id'       => 'post_type',
					'label'    => esc_html__( 'Post Type', 'total-theme-core' ),
					'type'     => 'select',
					'choices'  => 'post_types',
					'default'  => 'post',
				),
				array(
					'id'      => 'taxonomy',
					'label'   => esc_html__( 'Query By Taxonomy', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'taxonomies',
				),
				array(
					'id'          => 'terms',
					'label'       => esc_html__( 'Include Terms', 'total-theme-core' ),
					'type'        => 'text',
					'description' => esc_html__( 'Enter a comma seperated list of terms.', 'total-theme-core' ),
				),
				array(
					'id'          => 'terms_exclude',
					'label'       => esc_html__( 'Exclude Terms', 'total-theme-core' ),
					'type'        => 'text',
					'description' => esc_html__( 'Enter a comma seperated list of terms.', 'total-theme-core' ),
				),
				array(
					'id'      => 'order',
					'label'   => esc_html__( 'Order', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'query_order',
					'default' => 'DESC',
				),
				array(
					'id'      => 'orderby',
					'label'   => esc_html__( 'Order by', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'query_orderby',
					'default' => 'date',
				),
				array(
					'id'      => 'img_hover',
					'label'   => esc_html__( 'Image Hover', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'image_hovers',
				),
				array(
					'id'      => 'img_filter',
					'label'   => esc_html__( 'Image Filter', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'image_filters',
				),
				array(
					'id'      => 'img_aspect_ratio',
					'label'   => \esc_html__( 'Image Aspect Ratio', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'aspect_ratio',
				),
				array(
					'id'      => 'img_border_radius',
					'label'   => esc_html__( 'Image Border Radius', 'total-theme-core' ),
					'type'    => 'select',
					'default' => 'square',
					'choices' => 'border_radius',
				),
				array(
					'id'      => 'img_size',
					'label'   => esc_html__( 'Image Size', 'total-theme-core' ),
					'type'    => 'select',
					'default' => 'wpex-custom',
					'choices' => 'intermediate_image_sizes',
				),
				array(
					'id'    => 'img_width',
					'label' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'    => 'img_height',
					'label' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'      => 'img_crop_location',
					'label'   => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'image_crop_locations',
				),
				array(
					'id'    => 'img_lightbox',
					'label' => esc_html__( 'Open Images in Lightbox?', 'total-theme-core' ),
					'type'  => 'checkbox',
				),
			),
		);

		$this->create_widget( $this->args );

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 * @since 1.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		// Parse and extract widget settings.
		extract( $this->parse_instance( $instance ) );

		// Before widget hook.
		echo wp_kses_post( $args['before_widget'] );

		// Display widget title.
		$this->widget_title( $args, $instance );

		// Define widget output.
		$output = '';

		// Grid class.
		$grid_class = array(
			'wpex-recent-posts-thumb-grid',
			'wpex-inline-grid',
			'wpex-grid-cols-' . absint( $columns ?: 3 ),
			'wpex-gap-' . absint( $gap ?: 5 ),
		);

		if ( $img_lightbox ) {
			$grid_class[] = 'wpex-lightbox-group';
			vcex_enqueue_lightbox_scripts();
		}

		$grid_class = array_map( 'sanitize_html_class', $grid_class );

		$output .= '<div class="' . esc_attr( implode( ' ', $grid_class ) ) . '">';

			// Query args.
			$query_args = array(
				'post_type'      => $post_type,
				'posts_per_page' => $number,
				'no_found_rows'  => true,
				'tax_query'      => array( 'relation' => 'AND' ),
				'meta_query'     => array(
					array( 'key' => '_thumbnail_id' ) // thumbnails are required
				),
			);

			// Order params - needs FALLBACK don't ever edit!
			if ( ! empty( $orderby ) ) {
				$query_args['order']   = $order;
				$query_args['orderby'] = $orderby;
			} else {
				$query_args['orderby'] = $order; // THIS IS THE FALLBACK
			}

			// Tax Query.
			if ( ! empty( $taxonomy ) ) {

				// Include Terms.
				if (  ! empty( $terms ) ) {

					// Sanitize terms and convert to array.
					$terms = str_replace( ', ', ',', $terms );
					$terms = explode( ',', $terms );

					// Add to query arg.
					$query_args['tax_query'][] = array(
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $terms,
						'operator' => 'IN',
					);

				}

				// Exclude Terms.
				if ( ! empty( $terms_exclude ) ) {

					// Sanitize terms and convert to array.
					$terms_exclude = str_replace( ', ', ',', $terms_exclude );
					$terms_exclude = explode( ',', $terms_exclude );

					// Add to query arg.
					$query_args['tax_query'][] = array(
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $terms_exclude,
						'operator' => 'NOT IN',
					);

				}

			}

			// Exclude current post.
			if ( is_singular() ) {
				$query_args['post__not_in'] = array( get_the_ID() );
			}

			// Query posts.
			$wpex_query = new \WP_Query( $query_args );

			// Loop through posts.
			while ( post_type_exists( $post_type ) && $wpex_query->have_posts() ) : $wpex_query->the_post();

				$thumbnail_id = get_post_thumbnail_id();

				if ( empty( $thumbnail_id ) || '0' === $thumbnail_id ) {
					continue;
				}

				$output .= '<div class="wpex-recent-posts-thumb-grid__item">';

					if ( $img_lightbox ) {
						$link = vcex_get_lightbox_image( $thumbnail_id );
						$link_class = 'wpex-lightbox-group-item';
					} else {
						$link = function_exists( 'wpex_get_permalink' ) ? wpex_get_permalink() : get_the_permalink();
						$link_class = '';
					}

					if ( $img_hover && function_exists( 'wpex_image_hover_classes' ) ) {
						$img_hover_classes = wpex_image_hover_classes( $img_hover );
						if ( $img_hover_classes ) {
							$link_class .= ' ' . $img_hover_classes;
						}
					}

					if ( $img_filter && function_exists( 'wpex_image_filter_class' ) ) {
						$link_class .= ' ' . wpex_image_filter_class( $img_filter );
					}

					// Open link tag.
					$output .= '<a href="' . esc_url( $link ) . '"';

						if ( $link_class ) {
							$output .= ' class="' . esc_attr( $link_class ) .'"';
						}

						if ( $img_lightbox ) {

							$output .= ' data-title="' . vcex_esc_title() .'"';

							if ( $caption = wp_get_attachment_caption( $thumbnail_id ) ) {
								$output .= ' data-caption="' . esc_attr( $caption ) .'"';
							}

						} else {

							$output .= ' title="' . vcex_esc_title() .'"';

						}

					$output .= '>';

						$image_class = 'wpex-align-middle';

						if ( $img_border_radius && 'square' !== $img_border_radius ) {
							$image_class .= ' wpex-' . sanitize_html_class( $img_border_radius );
						}

						if ( ! empty( $img_aspect_ratio )
							&& \function_exists( 'totaltheme_get_aspect_ratio_choices' )
							&& \array_key_exists( $img_aspect_ratio, \totaltheme_get_aspect_ratio_choices() )
						) {
							$image_class .= ' wpex-object-cover wpex-aspect-' . str_replace( '/', '-', $img_aspect_ratio );
						}

						if ( function_exists( 'wpex_get_post_thumbnail' ) ) {
							$output .= wpex_get_post_thumbnail( [
								'size'   => $img_size,
								'width'  => $img_width,
								'height' => $img_height,
								'crop'   => $img_crop_location,
								'class'  => $image_class,
							] );
						} else {
							$output .= get_the_post_thumbnail( get_the_ID(), $img_size, [
								'class' => $image_class,
							] );
						}

					$output .= '</a>';

				$output .= '</div>';

			// End loop.
			endwhile;

			// Reset global query post data.
			wp_reset_postdata();

		$output .= '</div>';

		// Echo output.
		echo $output;

		echo wp_kses_post( $args['after_widget'] );

	}

}
register_widget( 'TotalThemeCore\\Widgets\\Widget_Recent_Posts_Thumb_Grid' );