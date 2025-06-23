<?php

/**
 * vcex_terms_grid shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

// Extract shortcode attributes - @todo remove.
extract( $atts );

// Get terms.
$terms = vcex_get_terms( $atts, 'vcex_terms_grid' );

if ( ! $terms ) {
	return;
}

// Define output.
$output = '';

// Sanitize atts.
$exclude_terms = $exclude_terms ? preg_split( '/\,[\s]*/', $exclude_terms ) : [];  // @todo convert slugs to ID's and pass to Term_Query instead.
$title_tag_escaped = $title_tag ? tag_escape( $title_tag ) : 'h2';
$title_overlay_align_items = $title_overlay_align_items ?: 'center';
$title_overlay_opacity = $title_overlay_opacity ?: '60';
$title_overlay_style = '';
$archive_link = vcex_validate_boolean( $archive_link );

if ( $title_overlay_bg ) {
	$title_overlay_style = vcex_inline_style( array(
		'background_color' => $title_overlay_bg,
	) );
}

// Validate on/off settings.
$title_overlay    = vcex_validate_att_boolean( 'title_overlay', $atts );
$img              = vcex_validate_att_boolean( 'img', $atts, true );
$title            = vcex_validate_att_boolean( 'title', $atts, true );
$description      = vcex_validate_att_boolean( 'description', $atts, true );
$term_count       = vcex_validate_att_boolean( 'term_count', $atts );
$term_count_block = $term_count && vcex_validate_att_boolean( 'term_count_block', $atts, true );
$button           = vcex_validate_att_boolean( 'button', $atts );

// Wrap classes
$wrap_classes = [
	'vcex-module',
	'vcex-terms-grid',
	'wpex-row',
	'wpex-clr'
];

if ( 'masonry' === $grid_style ) {
	$wrap_classes[] = 'vcex-isotope-grid';
	$wrap_classes[] = 'wpex-overflow-hidden';
	vcex_enqueue_isotope_scripts();
}

if ( $columns_gap ) {
	$wrap_classes[] = 'gap-' . sanitize_html_class( $columns_gap );
}

if ( $bottom_margin ) {
	$wrap_classes[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
}

if ( $visibility ) {
	$wrap_classes[] = vcex_parse_visibility_class( $visibility );
}

if ( $classes ) {
	$wrap_classes[] = vcex_get_extra_class( $classes );
}

$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_terms_grid', $atts );

// Entry CSS wrapper.
$entry_css_class = $entry_css ? vcex_vc_shortcode_custom_css_class( $entry_css ) : '';

// Display header if enabled.
if ( $header ) {
	$output .= vcex_get_module_header( [
		'style'   => $header_style,
		'content' => $header,
		'classes' => [ 'vcex-module-heading', 'vcex_terms_grid-heading' ],
	] );
}

// Button settings.
if ( $button ) {
	$button_text = ! empty( $atts['button_text'] ) ? $atts['button_text'] : esc_html__( 'visit category', 'total-theme-core' );
	$button_align = '';
	if ( ! empty( $atts['button_align'] ) ) {
		$button_align = ' wpex-text-' . sanitize_html_class( $atts['button_align'] );
	}
	$button_classes = vcex_get_button_classes( $button_style, $button_style_color );
}

// Begin output.
$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $atts[ 'unique_id' ] ) . '>';

	// Start counter.
	$counter = 0;

	// Loop through terms.
	$first_run = true;
	foreach ( $terms as $term ) :

		// Don't show excluded items.
		if ( in_array( $term->slug, $exclude_terms ) ) {
			continue;
		}

		// Store term link for use later.
		$term_link = get_term_link( $term, $term->taxonomy );

		// Add to counter.
		$counter++;

		if ( $first_run ) {

			// Entry classes.
			$entry_classes = [
				'vcex-terms-grid-entry',
				'wpex-last-mb-0',
			];

			if ( 'masonry' == $grid_style ) {
				$entry_classes[] = 'vcex-isotope-entry';
			}

			$entry_classes[] = vcex_get_grid_column_class( $atts );;

			if ( 'false' == $columns_responsive ) {
				$entry_classes[] = 'nr-col';
			} else {
				$entry_classes[] = 'col';
			}

			if ( $css_animation_class = vcex_get_css_animation( $css_animation ) ) {
				$entry_classes[] = $css_animation_class;
			}

			$entry_classes = implode( ' ', $entry_classes );

		}

		$output .= '<div class="' . esc_attr( $entry_classes ) . ' term-' . sanitize_html_class( $term->term_id ) . ' term-' . sanitize_html_class( $term->slug ) . ' col-' . sanitize_html_class( $counter ) . '">';

			if ( $entry_css_class ) {
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

						$thumbnail_class = 'wpex-align-middle';

						if ( $title_overlay ) {
							$thumbnail_class .= ' wpex-w-100';
						}
						
						if ( ! empty( $atts['img_aspect_ratio'] ) ) {
							$thumbnail_class .= ' ' . vcex_parse_aspect_ratio_class( $atts['img_aspect_ratio'] );
							$thumbnail_class .= ' wpex-object-cover';
						}

					}

					if ( $img_id ) {

						$output .= '<div class="' . esc_attr( $media_classes ) . '">';

							if ( $archive_link && ! empty( $term_link ) ) {
								$output .= '<a href="' . esc_url( $term_link ) . '" title="' . esc_attr( $term->name ) . '">';
							}

								// Display post thumbnail.
								$output .= vcex_get_post_thumbnail( [
									'attachment' => $img_id,
									'alt'        => $term->name,
									'width'      => $img_width,
									'height'     => $img_height,
									'crop'       => $img_crop,
									'size'       => $img_size,
									'class'      => $thumbnail_class,
								] );

								// Overlay title.
								if ( $title_overlay && $title && ! empty( $term->name ) ) :

									$output .= '<div class="vcex-terms-grid-entry-overlay wpex-absolute wpex-inset-0 wpex-text-white">';

										$output .= '<span class="vcex-terms-grid-entry-overlay-bg wpex-block wpex-absolute wpex-inset-0 wpex-bg-black wpex-opacity-' . sanitize_html_class( $title_overlay_opacity ) . '"' . $title_overlay_style . '></span>';

										$output .= '<div class="vcex-terms-grid-entry-overlay-content wpex-relative wpex-flex wpex-items-' . sanitize_html_class( $title_overlay_align_items ) . ' wpex-p-20 wpex-h-100 wpex-w-100">';

												$title_classes = [
													'vcex-terms-grid-entry-title',
													'entry-title',
													'wpex-flex-grow',
													'wpex-text-xl',
													'wpex-text-center',
												];

												if ( empty( $title_color ) ) {
													$title_classes[] = 'wpex-inherit-color-important';
												}

												$output .= '<' . $title_tag_escaped . ' class="' . esc_attr( implode( ' ', $title_classes ) ) . '">';

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
								$output .= vcex_get_entry_image_overlay( 'inside_link', 'vcex_terms_grid', $atts );

							if ( $archive_link && ! empty( $term_link ) ) {
								$output .= '</a>';
							}

							// Outside Overlay.
							$output .= vcex_get_entry_image_overlay( 'outside_link', 'vcex_terms_grid', $atts );

						$output .= '</div>';

					} // End img ID check.

				} // End image check.

				// Inline title and description.
				if ( ! $title_overlay || ! $img ) {

					// Show title
					if ( $title && ! $title_overlay && ! empty( $term->name ) ) {

						$output .= '<' . $title_tag_escaped . ' class="vcex-terms-grid-entry-title entry-title wpex-mb-5">';

							if ( $archive_link && ! empty( $term_link ) ) {
								$output .= '<a href="' . esc_url( $term_link  ) . '">';
							}

								$output .= esc_html( $term->name );

								if ( 'true' == $atts[ 'term_count' ] ) {
									$output .= ' <span class="vcex-terms-grid-entry-count">(' . absint( $term->count ) . ')</span>';
								}

							if ( $archive_link && ! empty( $term_link ) ) {
								$output .= '</a>';
							}

						$output .= '</' . $title_tag_escaped . '>';

					}

					// Display term description.
					if ( $description && $term->description ) {
						$output .= '<div class="vcex-terms-grid-entry-excerpt wpex-mb-15 wpex-clr">';
							$output .= do_shortcode( wp_kses_post( $term->description ) );
						$output .= '</div>';
					}

					// Display button.
					if ( $button ) {

						$output .= '<div class="vcex-terms-grid-entry-button wpex-my-15 wpex-clr' . esc_attr( $button_align ) . '">';

							$output .= '<a href="' . esc_url( $term_link ) . '" class="' . esc_attr( $button_classes ) . '">';

								$output .= vcex_parse_text_safe( str_replace( '{{term_name}}', $term->name, $button_text ) );

							$output .= '</a>';

						$output .= '</div>';

					} // end button check.

				}

			$output .= '</div>';

		// Close entry.
		if ( $entry_css_class ) {
			$output .= '</div>';
		}

		// Clear counter.
		if ( $counter === absint( $columns ) ) {
			$counter = 0;
		}

		$first_run = false;

	endforeach;

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
