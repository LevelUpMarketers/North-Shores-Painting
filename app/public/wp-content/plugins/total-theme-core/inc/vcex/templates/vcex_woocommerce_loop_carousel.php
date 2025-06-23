<?php

/**
 * Visual Composer WooCommerce Loop Carousel.
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

// Define vars.
$atts['post_type'] = 'product';
$atts['taxonomy']  = 'product_cat';
$atts['tax_query'] = '';

// Custom query_products_by argument.
if ( $atts['query_products_by'] ) {
	if ( 'featured' == $atts['query_products_by'] ) {
		$atts['featured_products_only'] = true;
	} elseif ( 'on_sale' == $atts['query_products_by'] ) {
		if ( function_exists( 'wc_get_product_ids_on_sale' ) ) {
			$atts['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		}
	}
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
		echo $carousel_css;
	}

	// Enqueue scripts.
	vcex_enqueue_carousel_scripts();

	// Wrap Classes.
	$wrap_class = [
		'vcex-woocommerce-loop-carousel',
		'wpex-carousel',
		'wpex-carousel-woocommerce-loop',
		'products',
		'wpex-clr',
		'vcex-module',
	];

	if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
		$wrap_class[] = 'owl-carousel';
	}

	if ( $carousel_css ) {
		$wrap_class[] = 'wpex-carousel--render-onload';
		$wrap_class[] = $unique_classname;
	}

	if ( 'true' == $arrows ) {
		$wrap_class[] = $arrows_style ? 'arrwstyle-' . $arrows_style : 'arrwstyle-default';
		if ( $arrows_position && 'default' != $arrows_position ) {
			$wrap_class[] = 'arrwpos-' . $arrows_position;
		}
	}

	if ( $visibility ) {
		$wrap_class[] = vcex_parse_visibility_class( $visibility );
	}

	if ( $css_animation && 'none' != $css_animation ) {
		$wrap_class[] = vcex_get_css_animation( $css_animation );
	}

	if ( $classes ) {
		$wrap_class[] = vcex_get_extra_class( $classes );
	}

	// Disable autoplay.
	if ( vcex_vc_is_inline() || '1' == count( $vcex_query->posts ) ) {
		$atts['auto_play'] = false;
	}

	// VC filter.
	$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_woocommerce_carousel', $atts );

	?>

	<div class="woocommerce wpex-clr">

		<ul class="<?php echo esc_attr( $wrap_class ); ?>" data-wpex-carousel="<?php echo vcex_carousel_settings_to_json( $carousel_settings ); ?>"<?php vcex_unique_id( $unique_id ); ?>>

			<?php
			// Loop through posts.
			while ( $vcex_query->have_posts() ) :

				// Get post from query.
				$vcex_query->the_post();

				if ( function_exists( 'wc_set_loop_prop' ) ) {
					wc_set_loop_prop( 'name', 'wpex_loop' );
				}

				// Get woocommerce template part.
				if ( function_exists( 'wc_get_template_part' ) ) {
					wc_get_template_part( 'content', 'product' );
				}

			endwhile;

			?>

		</ul>

	</div>

	<?php
	// Reset loop.
	if ( function_exists( 'wc_reset_loop' ) ) {
		wc_reset_loop();
	}
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
