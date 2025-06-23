<?php

/**
 * vcex_terms_carousel output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Extract shortcode attributes - @todo remove.
extract( $atts );

// Get terms.
$terms = vcex_get_terms( $atts, 'vcex_terms_carousel' );

if ( ! $terms ) {
	return;
}

// Define output.
$output = '';

// Get carousel settings.
$unique_classname  = vcex_element_unique_classname();
$carousel_settings = vcex_get_carousel_settings( $atts, 'vcex_terms_carousel', false );
$carousel_css      = vcex_get_carousel_inline_css( $unique_classname, $carousel_settings );

if ( $carousel_css ) {
	$output .= $carousel_css;
}

// Enqueue scripts.
vcex_enqueue_carousel_scripts();

// Sanitize atts.
$exclude_terms = $exclude_terms ? preg_split( '/\,[\s]*/', $exclude_terms ) : []; // @todo convert slugs to ID's and pass to Term_Query instead.
$title_tag_escaped = $title_tag ? tag_escape( $title_tag ) : 'h2';
$title_overlay_align_items = $title_overlay_align_items ? $title_overlay_align_items : 'center';
$title_overlay_opacity = $title_overlay_opacity ? $title_overlay_opacity : '60';
$title_overlay_style = '';

if ( $title_overlay_bg ) {
	$title_overlay_style = vcex_inline_style( array(
		'background_color' => $title_overlay_bg,
	) );
}

// Validate on/off settings.
$title_overlay    = vcex_validate_boolean( $title_overlay );
$img              = vcex_validate_boolean( $img );
$title            = vcex_validate_boolean( $title );
$description      = vcex_validate_boolean( $description );
$term_count       = vcex_validate_boolean( $term_count );
$term_count_block = vcex_validate_boolean( $term_count_block );
$button           = vcex_validate_boolean( $button );

// Main Classes
$wrap_class = [
	'vcex-module',
	'wpex-carousel',
	'vcex-terms-carousel',
	'wpex-clr',
];

if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
	$wrap_class[] = 'owl-carousel';
}

if ( $carousel_css ) {
	$wrap_class[] = 'wpex-carousel--render-onload';
	$wrap_class[] = $unique_classname;
}

// Arrow style.
$atts['arrows_style'] = $atts['arrows_style'] ? $atts['arrows_style'] : 'default';
$wrap_class[] = 'arrwstyle-' . sanitize_html_class( $atts['arrows_style'] );

// Arrow position.
if ( $arrows_position && 'default' !== $arrows_position ) {
	$wrap_class[] = 'arrwpos-' . sanitize_html_class( $arrows_position );
}

// Margin bottom.
if ( $bottom_margin ) {
	$wrap_class[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
}

// Visiblity.
if ( $visibility ) {
	$wrap_class[] = vcex_parse_visibility_class( $visibility );
}

// CSS animations.
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_class[] = vcex_get_css_animation( $css_animation );
}

// Custom Classes.
if ( $classes ) {
	$wrap_class[] = vcex_get_extra_class( $classes );
}

// Disable autoplay.
if ( '1' == count( $terms ) || vcex_vc_is_inline() ) {
	$auto_play = false;
}

// Entry CSS wrapper.
if ( $entry_css ) {
	$entry_css_class = vcex_vc_shortcode_custom_css_class( $entry_css );
}

// Title style.
$title_style = [
	'font_family'   => $title_font_family,
	'font_size'     => $title_font_size,
	'color'         => $title_color,
	'font_weight'   => $title_font_weight,
	'line_height'   => $title_line_height,
	'text_align'    => $title_text_align,
	'margin_bottom' => $title_bottom_margin,
];
$title_style = vcex_inline_style( $title_style );

// Description style.
$description_style = [
	'font_family' => $description_font_family,
	'font_size'   => $description_font_size,
	'color'       => $description_color,
	'line_height' => $description_line_height,
	'text_align'  => $description_text_align,
];
$description_style = vcex_inline_style( $description_style );

// Apply filter to wrap_classes.
$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_terms_carousel', $atts );

// Display header if enabled.
if ( $atts['header_style'] ) {

	$output .= vcex_get_module_header( [
		'style'   => $atts['header_style'] ?? '',
		'content' => $atts['header'] ?? '',
		'classes' => array( 'vcex-module-heading vcex_terms_carousel-heading' ),
	] );

}

$wrap_style = vcex_inline_style( [
	'animation_delay'    => $atts['animation_delay'],
	'animation_duration' => $atts['animation_duration'],
] );

// Begin output.
$output .= '<div class="' . esc_attr( $wrap_class ) . '" data-wpex-carousel="' . vcex_carousel_settings_to_json( $carousel_settings ) . '"' . vcex_get_unique_id( $atts[ 'unique_id' ] ) . $wrap_style . '>';

	// Loop through terms.
	$first_run = true;
	foreach ( $terms as $term ) :

		// Excluded.
		if ( in_array( $term->slug, $exclude_terms ) ) {
			continue;
		}

		// Term data.
		$term_link = get_term_link( $term, $term->taxonomy );

		// Begin entry output.
		$output .= '<div class="vcex-terms-carousel-entry term-' . absint( $term->term_id ) . ' term-' . esc_attr( $term->slug ) . '">';

		// Entry css wrapper.
		if ( ! empty( $entry_css_class ) ) {
			$output .= '<div class="' . esc_attr( $entry_css_class ) . '">';
		}

			// Display image if enabled.
			if ( $img ) {

				// Get thumbnail ID.
				$img_id = vcex_get_term_thumbnail_id( $term->term_id );

				if ( ! $img_id ) {
					$img_id = get_term_meta( $term->term_id, 'thumbnail_id', true ); // WooCommerce but also fallback.
				}

				// Image not defined via meta, display image from first post in term.
				if ( ! $img_id ) {
					$vcex_query = new WP_Query( [
						'post_type'      => 'any',
						'posts_per_page' => '1',
						'no_found_rows'  => true,
						'tax_query'      => [
							[
								'field'    => 'id',
								'taxonomy' => $term->taxonomy,
								'terms'    => $term->term_id,
							]
						],
					] );
					if ( $vcex_query->have_posts() ) {
						while ( $vcex_query->have_posts() ) : $vcex_query->the_post();
							$img_id = get_post_thumbnail_id();
						endwhile;
					}
					wp_reset_postdata();
				}

				if ( $img_id ) {

					if ( $first_run ) {

						$media_classes = [
							'vcex-terms-grid-entry-image',
						];

						if ( $title_overlay ) {
							$media_classes[] = 'vcex-has-overlay';
							$media_classes[] = 'overlay-parent';
						} else {
							$media_classes[] = 'wpex-mb-20';
						}

						if ( $img_filter ) {
							$media_classes[] = vcex_image_filter_class( $img_filter );
						}

						if ( $img_hover_style ) {
							$media_classes[] = vcex_image_hover_classes( $img_hover_style );
						}

						$overlay_style = $overlay_style;

						if ( $overlay_style ) {
							$media_classes[] = vcex_image_overlay_classes( $overlay_style );
						}

						$media_classes = implode( ' ', array_unique( $media_classes ) );

					}

					$output .= '<div class="' . esc_attr( $media_classes ) . '">';

						$output .= '<a href="' . esc_url( $term_link ) . '" title="' . esc_attr( $term->name ) . '">';

							if ( $first_run ) {

								$thumbnail_class = 'wpex-align-middle';

								if ( $title_overlay ) {
									$thumbnail_class .= ' wpex-w-100';
								}

							}

							// Display post thumbnail
							$output .= vcex_get_post_thumbnail( [
								'attachment' => $img_id,
								'alt'        => $term->name,
								'width'      => $img_width,
								'height'     => $img_height,
								'crop'       => $img_crop,
								'size'       => $img_size,
								'class'      => $thumbnail_class,
							] );

							// Overlay title
							if ( $title_overlay && $title && ! empty( $term->name ) ) :

								$output .= '<div class="vcex-terms-grid-entry-overlay wpex-absolute wpex-inset-0 wpex-text-white">';

									$output .= '<span class="vcex-terms-grid-entry-overlay-bg wpex-block wpex-absolute wpex-inset-0 wpex-bg-black wpex-opacity-' . sanitize_html_class( $title_overlay_opacity ) . '"' . $title_overlay_style . '></span>';

									$output .= '<div class="vcex-terms-grid-entry-overlay-content wpex-relative wpex-flex wpex-items-' . sanitize_html_class( $title_overlay_align_items ) . ' wpex-p-20 wpex-h-100 wpex-w-100">';

											$title_classes = array(
												'vcex-terms-grid-entry-title',
												'entry-title',
												'wpex-flex-grow',
												'wpex-text-xl',
												'wpex-text-center',
											);

											if ( empty( $title_color ) ) {
												$title_classes[] = 'wpex-inherit-color-important';
											}

											$output .= '<' . $title_tag_escaped . ' class="' . esc_attr( implode( ' ', $title_classes ) ) . '"' . $title_style . '>';

												$output .= esc_html( $term->name );

												if ( $term_count ) {

													$term_count_class = 'vcex-terms-grid-entry-count';

													if ( $term_count_block ) {
														$term_count_class .= ' wpex-block';
													} else {
														$term_count_class .= ' wpex-ml-5';
													}

													$output .= '<span class="' . esc_attr( $term_count_class ) . '">(' . absint( $term->count ) . ')</span>';
												}

											$output .= '</' . $title_tag_escaped . '>';

									$output .= '</div>';

								$output .= '</div>';

							endif;

							// Data for overlays.
							if ( $img_id ) {
								$atts['lightbox_link'] = vcex_get_lightbox_image( $img_id );
							}
							$atts['overlay_link']     = $term_link;
							$atts['post_title']       = $term->name;
							$atts['overlay_excerpt' ] = $term->description;

							// Inner Overlay.
							$output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_terms_carousel', $atts );

						$output .= '</a>';

						// Outside Overlay.
						$output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_terms_carousel', $atts );

					$output .= '</div>';

				} // End img ID check

			} // End image check

			// Inline title and description.
			if ( ! $title_overlay || ! $img ) {

				// Show title.
				if ( $title && ! $title_overlay && ! empty( $term->name ) ) {

					$output .= '<' . $title_tag_escaped . ' class="vcex-terms-grid-entry-title entry-title wpex-mb-5"' . $title_style . '>';

						$output .= '<a href="' . get_term_link( $term, $term->taxonomy ) . '">';

							$output .= esc_html( $term->name );

							if ( 'true' == $atts[ 'term_count' ] ) {
								$output .= ' <span class="vcex-terms-grid-entry-count">(' . absint( $term->count ) . ')</span>';
							}

						$output .= '</a>';

					$output .= '</' . $title_tag_escaped . '>';

				}

				// Display term description.
				if ( $description && $term->description ) {

					$output .= '<div class="vcex-terms-grid-entry-excerpt wpex-mb-15 wpex-clr"' . $description_style . '>';

						$output .= do_shortcode( wp_kses_post( $term->description ) );

					$output .= '</div>';

				}

				// Display button.
				if ( $button ) {

					if ( $first_run ) {

						$button_data    = array();
						$button_text    = $button_text ? $button_text : esc_html__( 'visit category', 'total-theme-core' );
						$button_align   = $button_align ? ' text' . $button_align  : '';
						$button_classes = vcex_get_button_classes( $button_style, $button_style_color );

						$button_style = vcex_inline_style( array(
							'background'    => $button_background,
							'color'         => $button_color,
							'font_size'     => $button_size,
							'padding'       => $button_padding,
							'border_radius' => $button_border_radius,
							'margin'        => $button_margin,
						) );

						$button_hover_data = [];

						if ( $button_hover_background ) {
							$button_hover_data['background'] = esc_attr( vcex_parse_color( $button_hover_background ) );
						}

						if ( $button_hover_color ) {
							$button_hover_data['color'] = esc_attr( vcex_parse_color( $button_hover_color ) );
						}

						if ( $button_hover_data ) {
							$button_hover_data = htmlspecialchars( wp_json_encode( $button_hover_data ) );
						}

					}

					$output .= '<div class="vcex-terms-grid-entry-button wpex-my-15 wpex-clr' . esc_attr( $button_align ) . '">';

						$button_attrs = [
							'href'            => esc_url( $term_link ),
							'class'           => esc_attr( $button_classes ),
							'style'           => $button_style,
							'data-wpex-hover' => $button_hover_data,
						];

						$output .= '<a' . vcex_parse_html_attributes( $button_attrs ) . '>';

							$output .= do_shortcode( wp_kses_post( $button_text ) );

						$output .= '</a>';

					$output .= '</div>';

				} // end button check

			}

		// Close entry.
		$output .= '</div>';

		// Close entry css.
		if ( ! empty( $entry_css_class ) ) {
			$output .= '</div>';
		}

	endforeach;

	$first_run = false;

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
