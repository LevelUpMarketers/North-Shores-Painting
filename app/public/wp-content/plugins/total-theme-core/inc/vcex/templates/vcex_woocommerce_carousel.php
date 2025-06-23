<?php

/**
 * vcex_woocommrece_carousel shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// WooCommerce Only.
if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

// Define vars for Query.
$atts['post_type'] = 'product';
$atts['taxonomy']  = 'product_cat';
$atts['tax_query'] = '';

// Move featured check to the end of array to prevent issues with tax_query.
if ( $atts['featured_products_only'] ) {
	$fonly = $atts['featured_products_only'];
	unset( $atts['featured_products_only'] );
	$atts['featured_products_only'] = $fonly;
}

// Extract attributes.
extract( $atts );

// Build the WordPress query.
$vcex_query = vcex_build_wp_query( $atts, 'vcex_woocommerce_carousel' );

// Output posts.
if ( $vcex_query && $vcex_query->have_posts() ) :

	// All carousels need a unique classname.
	$unique_classname = vcex_element_unique_classname();

	// Get carousel settings.
	$carousel_settings = vcex_get_carousel_settings( $atts, 'vcex_woocommerce_carousel', false );
	$carousel_css = vcex_get_carousel_inline_css( $unique_classname, $carousel_settings );

	if ( $carousel_css ) {
		echo $carousel_css; // @codingStandardsIgnoreLine.
	}

	// Enqueue scripts.
	vcex_enqueue_carousel_scripts();

	// Sanitize Overlay style.
	$overlay_style = empty( $overlay_style ) ? 'none' : $overlay_style;

	// Items to scroll fallback for old setting.
	if ( 'page' === $items_scroll ) {
		$items_scroll = $items;
	}

	// Wrap Classes.
	$wrap_class = [
		'vcex-module',
		'wpex-carousel',
		'wpex-carousel-woocommerce',
		'wpex-clr'
	];

	if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
		$wrap_class[] = 'owl-carousel';
	}

	if ( $carousel_css ) {
		$wrap_class[] = 'wpex-carousel--render-onload';
		$wrap_class[] = $unique_classname;
	}

	if ( $bottom_margin ) {
		$wrap_class[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
	}

	if ( $style && 'default' !== $style ) {
		$wrap_class[] = $style;
		$arrows_position = ( 'no-margins' === $style && 'default' === $arrows_position ) ? 'abs' : $arrows_position;
	}

	$arrows_style = $arrows_style ?: 'default';
	$wrap_class[] = 'arrwstyle-' . sanitize_html_class( $arrows_style );

	if ( $arrows_position && 'default' !== $arrows_position ) {
		$wrap_class[] = 'arrwpos-' . sanitize_html_class( $arrows_position );
	}

	if ( $visibility ) {
		$wrap_class[] = vcex_parse_visibility_class( $visibility );
	}

	if ( $css_animation_class = vcex_get_css_animation( $css_animation ) ) {
		$wrap_class[] = $css_animation_class;
	}

	if ( $classes ) {
		$wrap_class[] = vcex_get_extra_class( $classes );
	}

	// Entry media classes.
	$media_classes = [
		'wpex-carousel-entry-media',
		'wpex-relative',
		'wpex-mb-20',
	];

	if ( $img_filter ) {
		$media_classes[] = vcex_image_filter_class( $img_filter );
	}

	if ( $img_hover_style ) {
		$media_classes[] = vcex_image_hover_classes( $img_hover_style );
	}

	if ( $overlay_style ) {
		$media_classes[] = vcex_image_overlay_classes( $overlay_style );
	}

	if ( 'lightbox' == $thumbnail_link ) {
		$wrap_class[] = 'wpex-carousel-lightbox';
		vcex_enqueue_lightbox_scripts();
	}

	$media_classes = implode( ' ', $media_classes );

	// Content Design.
	$content_style = vcex_inline_style( [
		'background' => $content_background,
		'padding'    => $content_padding,
		'margin'     => $content_margin,
		'border'     => $content_border,
		//'opacity'    => $content_opacity, Removed due to bugs
		'text_align' => $content_alignment,
	] );

	// Price style.
	if ( 'true' == $price ) {
		$price_style = vcex_inline_style( [
			'font_size' => $content_font_size,
			'color'     => $content_color,
		] );
	}

	// Title design.
	if ( $title ) {
		$heading_style = vcex_inline_style( [
			'margin'         => $content_heading_margin,
			'font_size'      => $content_heading_size,
			'font_weight'    => $content_heading_weight,
			'text_transform' => $content_heading_transform,
			'line_height'    => $content_heading_line_height,
			'color'          => $content_heading_color,
		] );
		$heading_link_style = vcex_inline_style( [
			'color' => $content_heading_color,
		] );
	}

	// Readmore design and classes.
	if ( 'true' == $read_more ) {

		// Readmore text.
		$read_more_text = $read_more_text ?: esc_html__( 'view product', 'total-theme-core' );

		// Readmore classes.
		$readmore_classes = vcex_get_button_classes( $readmore_style, $readmore_style_color );

		// Readmore style.
		$readmore_style = vcex_inline_style( array(
			'background'    => $readmore_background,
			'color'         => $readmore_color,
			'font_size'     => $readmore_size,
			'padding'       => $readmore_padding,
			'border_radius' => $readmore_border_radius,
			'margin'        => $readmore_margin,
		) );

		// Readmore data.
		$readmore_hover_data = array();
		if ( $readmore_hover_background ) {
			$readmore_hover_data['background'] = esc_attr( vcex_parse_color( $readmore_hover_background ) );
		}
		if ( $readmore_hover_color ) {
			$readmore_hover_data['color'] = esc_attr( vcex_parse_color( $readmore_hover_color ) );
		}
		if ( $readmore_hover_data ) {
			$readmore_hover_data = htmlspecialchars( wp_json_encode( $readmore_hover_data ) );
		}

	}

	// Disable autoplay.
	if ( vcex_vc_is_inline() || '1' == count( $vcex_query->posts ) ) {
		$atts['auto_play'] = false;
	}

	// Filter wrap classes.
	$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_woocommerce_carousel', $atts );

	// Wrap style.
	$wrap_style = array(
		'animation_delay' => $atts['animation_delay'],
		'animation_duration' => $atts['animation_duration'],
	);

	?>

	<div class="woocommerce <?php echo esc_attr( $wrap_class ); ?>" data-wpex-carousel="<?php echo vcex_carousel_settings_to_json( $carousel_settings ); ?>"<?php vcex_unique_id( $unique_id ); ?><?php echo vcex_inline_style( $wrap_style ); ?>>

		<?php
		// Loop through posts.
		$count=0;
		while ( $vcex_query->have_posts() ) :
			$count++;

			// Get post from query.
			$vcex_query->the_post();

			// Create new post object.
			$post = new stdClass();

			// Get post data.
			$get_post = get_post();

			?>

			<div class="wpex-carousel-slide">

				<?php
				// Post VARS.
				$post->ID        = $get_post->ID;
				$post->title     = $get_post->post_title;
				$post->permalink = vcex_get_permalink( $post->ID );
				$post->esc_title = vcex_esc_title();  ?>

				<?php
				// Media Wrap.
				if ( has_post_thumbnail() ) :

					// Generate thumbnail.
					$post->thumbnail = vcex_get_post_thumbnail( [
						'lazy'          => false,
						'size'          => $img_size,
						'crop'          => $img_crop,
						'width'         => $img_width,
						'height'        => $img_height,
						'apply_filters' => 'vcex_woocommerce_carousel_thumbnail_args',
						'filter_arg1'   => $atts,
					] ); ?>

					<div class="<?php echo esc_attr( $media_classes ); ?>">

						<?php wc_get_template( 'loop/sale-flash.php' ); ?>

						<?php
						// No links.
						if ( 'none' === $thumbnail_link) : ?>

							<?php echo $post->thumbnail; ?>

						<?php
						// Lightbox.
						elseif ( 'lightbox' === $thumbnail_link ) : ?>

							<a href="<?php echo vcex_get_lightbox_image(); ?>" title="<?php echo $post->esc_title; ?>" class="wpex-carousel-entry-img wpex-carousel-lightbox-item" data-count="<?php echo $count; ?>" data-title="<?php echo $post->esc_title; ?>">

								<?php echo $post->thumbnail; ?>

						<?php
						// Link to post.
						else : ?>

							<a href="<?php echo esc_url( $post->permalink ); ?>" title="<?php echo $post->esc_title; ?>" class="wpex-carousel-entry-img">

								<?php echo $post->thumbnail; ?>

						<?php endif; ?>

						<?php
						// Overlay & close link.
						if ( ! in_array( $thumbnail_link, array( 'none', 'nowhere' ) ) ) : ?>

							<?php
							// Inner Overlay.
							if ( $overlay_style ) {
								vcex_image_overlay( 'inside_link', $overlay_style, $atts );
							}

							// Close link.
							echo '</a>';

							// Outside Overlay.
							if ( $overlay_style ) {
								vcex_image_overlay( 'outside_link', $overlay_style, $atts );
							}

						endif; ?>

					</div>

				<?php endif; // Thumbnail check. ?>

				<?php
				// Details.
				if ( 'true' == $title || 'true' == $price || 'true' == $read_more ) : ?>

					<div class="wpex-carousel-entry-details wpex-first-mt-0 wpex-last-mb-0 wpex-clr"<?php echo $content_style; ?>>

						<?php
						// Title.
						if ( 'true' == $title && $post->title ) : ?>

							<div class="wpex-carousel-entry-title entry-title wpex-mb-5"<?php echo $heading_style; ?>>
								<a href="<?php echo esc_url( $post->permalink ); ?>" title="<?php echo $post->esc_title; ?>"<?php echo $heading_link_style; ?>><?php echo wp_kses_post( $post->title ); ?></a>
							</div>

						<?php endif; ?>

						<?php
						// Excerpt.
						if ( 'true' == $price ) : ?>

							<?php if ( $get_price = vcex_get_woo_product_price() ) {

								$price_class = 'wpex-carousel-entry-price price wpex-my-5 wpex-clr';

								if ( $content_color ) {
									$price_class .= ' wpex-child-inherit-color';
								}
								?>

								<div class="<?php echo esc_attr( $price_class ); ?>"<?php echo $price_style; ?>>
									<?php echo wp_kses_post( $get_price ); ?>
								</div>

							<?php } ?>

						<?php endif; ?>

						<?php
						// Display read more button if $read_more is true and $read_more_text isn't empty.
						if ( 'true' == $read_more ) : ?>

							<div class="entry-readmore-wrap wpex-my-15 wpex-clr">

								<?php
								$attrs = array(
									'href'  => $post->permalink,
									'class' => $readmore_classes,
									'style' => $readmore_style,
								);

								if ( $readmore_hover_data ) {
									$attrs['data-wpex-hover'] = $readmore_hover_data;
								} ?>

								<a <?php echo vcex_parse_html_attributes( $attrs ); ?>><?php

									echo esc_html( $read_more_text );

									if ( 'true' == $readmore_rarr ) { ?>

										<span class="vcex-readmore-rarr"><?php echo vcex_readmore_button_arrow(); ?></span>

									<?php }

								?></a>

							</div>

						<?php endif; // End readmore check. ?>

					</div>

				<?php endif; ?>

			</div>

		<?php endwhile; ?>

	</div>

	<?php
	// Remove post object from memory.
	$post = null;

	// Reset the post data to prevent conflicts with WP globals.
	wp_reset_postdata(); ?>

<?php
// If no posts are found display message.
else : ?>

	<?php
	// Display no posts found error if function exists.
	echo vcex_no_posts_found_message( $atts ); ?>

<?php
// End post check
endif;
