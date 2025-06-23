<?php

/**
 * vcex_post_terms shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.8.8
 */

defined( 'ABSPATH' ) || exit;

extract( $atts );

// Define main vars.
$post_id         = vcex_get_the_ID();
$button_color    = ! empty( $atts['button_color'] ) ? $atts['button_color'] : '';
$button_align    = ! empty( $atts['button_align'] ) ? $atts['button_align'] : '';
$first_term_only = vcex_validate_att_boolean( 'first_term_only', $atts );
$custom_spacer   = apply_filters( 'vcex_post_terms_default_spacer', $spacer );

// Check if currently in template editing mode.
$template_edit_mode = vcex_is_template_edit_mode();

// Locate taxonomy if one isn't defined.
if ( ! $template_edit_mode && empty( $taxonomy ) && function_exists( 'wpex_get_post_primary_taxonomy' ) ) {
	$taxonomy = wpex_get_post_primary_taxonomy( $post_id );
}

// Taxonomy is required.
if ( ! $template_edit_mode && ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) ) {
	return;
}

// Get module style.
$module_style = ! empty( $style ) ? $style : 'buttons';

// Define terms.
$terms = [];

// Dummy terms for dynamic templates.
if ( $template_edit_mode ) {
	$dummy_link = '#';
	$sample_terms_number = $first_term_only ? 1 : 2;
	for ($i = 0; $i < $sample_terms_number; $i++) {
		$sample_term              = new stdClass();
		$sample_term->term_id     = $i;
		$sample_term->name        = esc_html__( 'Sample Term', 'total-theme-core' ) . ' ' . ( $i + 1 );
		$sample_term->slug        = "sample-term-{$i}";
		$sample_term->taxonomy    = 'sample-taxonomy';
		$sample_term->description = '';
		$sample_term->parent      = 0;
		$sample_term->count       = 1;
		$terms[]                  = $sample_term;
	}
}

// Get featured term.
if ( ! $terms
	&& $first_term_only
	&& function_exists( 'totaltheme_get_post_primary_term' )
	&& $primary_term = totaltheme_get_post_primary_term( '', $taxonomy )
) {
	$terms = [ $primary_term ];
}

// If terms is empty lets query them.
if ( ! $terms ) {

	// Query arguments.
	$query_args = [
		'order'   => $order,
		'orderby' => $orderby,
		'fields'  => 'all',
	];

	/**
	 * Filters the vcex_post_terms shortcode query args.
	 *
	 * @param array $query_args
	 * @param array $shortcode_atts
	 */
	$query_args = (array) apply_filters( 'vcex_post_terms_query_args', $query_args, $atts );

	$terms = wp_get_post_terms( $post_id, $taxonomy, $query_args );

	// Get first term only.
	if ( $first_term_only && isset( $terms[0] ) ) {
		$terms = [ $terms[0] ];
	}

}

// Terms needed.
if ( ! $terms || is_wp_error( $terms ) ) {
	return;
}

// Define output var.
$output = '';

// Wrap classes.
$wrap_class = [
	'vcex-post-terms',
	'vcex-module',
	'wpex-clr',
];

if ( 'center' === $button_align && 'buttons' === $style ) {
	$wrap_class[] = 'textcenter';
	$wrap_class[] = 'wpex-last-mr-0';
}

// Alignment
if ( ! empty( $atts['max_width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$wrap_class[] = vcex_parse_align_class( ! empty( $atts['align'] ) ? $atts['align'] : 'center' );
}

// Add extra classes.
$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_post_terms' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_post_terms', $atts );

// Wrap style.
$shortcode_css_args = [
	'animation_delay'    => $atts['animation_delay'] ?? null,
	'animation_duration' => $atts['animation_duration'] ?? null,
	'width'              => $atts['max_width'] ?? null,
];

if ( 'buttons' !== $module_style ) {
	$shortcode_css_args['font_family']    = $button_font_family;
	$shortcode_css_args['color']          = $button_color;
	$shortcode_css_args['font_size']      = $button_font_size;
	$shortcode_css_args['font_weight']    = $button_font_weight;
	$shortcode_css_args['text_transform'] = $button_text_transform;
}

if ( $button_color && 'buttons' !== $style ) {
	$shortcode_css_args['--wpex-link-color'] = $button_color;
	$shortcode_css_args['--wpex-hover-link-color'] = $button_color;
}

if ( 'buttons' !== $style || ( 'buttons' === $style && 'plain-text' === $button_style ) ) {
	if ( ! empty( $atts['link_underline'] ) ) {
		$underline = vcex_validate_boolean( $atts['link_underline'] );
		$shortcode_css_args['--wpex-link-decoration-line'] = $underline ? 'underline' : 'none';
	}
	if ( ! empty( $atts['link_underline_hover'] ) ) {
		$underline = vcex_validate_boolean( $atts['link_underline_hover'] );
		$shortcode_css_args['--wpex-hover-link-decoration-line'] = $underline ? 'underline' : 'none';
	}
}

$shortcode_css = vcex_inline_style( $shortcode_css_args );

// Begin output.
$output .= '<div class="' . esc_attr( $wrap_class ) . '"' . vcex_get_unique_id( $unique_id ) . $shortcode_css . '>';

	// Define link vars
	$link_style = '';
	$link_class = [
		'vcex-post-terms__item',
	];
	$link_class_xtra = [];

	// Button Style Classes and inline styles.
	if ( 'buttons' === $module_style ) {

		$link_class_xtra[] = vcex_get_button_classes(
			$button_style,
			$button_color_style,
			$button_size,
			$button_align
		);

		$spacing = $spacing ?: '5';
		$spacing_direction = ( 'right' === $button_align ) ? 'l' : 'r';

		$link_class_xtra[] = 'wpex-m' . $spacing_direction . '-' . sanitize_html_class( absint( $spacing ) );
		$link_class_xtra[] = 'wpex-mb-' . sanitize_html_class( absint( $spacing ) );

		if ( 'false' == $atts['archive_link'] || ! $atts['archive_link'] ) {
			$link_class_xtra[] = 'wpex-cursor-default';
		}

		// Button Style.
		$link_style_args = [
			'margin'         => $button_margin,
			'color'          => ( 'term_color' !== $button_color ) ? $button_color : '',
			'background'     => ( 'term_color' !== $button_background ) ? $button_background : '',
			'padding'        => $button_padding,
			'font_size'      => $button_font_size,
			'font_weight'    => $button_font_weight,
			'border_radius'  => $button_border_radius,
			'text_transform' => $button_text_transform,
			'font_family'    => $button_font_family,
			'letter_spacing' => $button_letter_spacing,
		];

		$link_style = vcex_inline_style( $link_style_args );

	}

	// Get child_of value.
	if ( ! empty( $child_of ) ) {
		$get_child_of = get_term_by( 'slug', trim( $child_of ), $taxonomy );
		if ( $get_child_of ) {
			$child_of_id = $get_child_of->term_id;
		}
	}

	// Get excluded terms.
	if ( ! empty( $exclude_terms ) ) {
		$exclude_terms = preg_split( '/\,[\s]*/', $exclude_terms );
	} else {
		$exclude_terms = [];
	}

	// Before Text.
	if ( 'inline' === $module_style && ! empty( $before_text ) ) {
		$output .= '<span class="vcex-post-terms__label vcex-label">' . vcex_parse_text_safe( $before_text ) . '</span> ';
	}

	// Open UL list.
	elseif ( 'ul' === $module_style ) {
		$output .= '<ul class="vcex-post-terms__list">';
	}

	// Open OL list.
	elseif ( 'ol' === $module_style ) {
		$output .= '<ol class="vcex-post-terms__list">';
	}

	// Loop through terms.
	if ( is_array( $terms ) ) {
		$terms_count = 0;
		$first_run = true;
		foreach ( $terms as $term ) :

			// Skip items that aren't a child of a specific parent..
			if ( ! empty( $child_of_id ) && $term->parent != $child_of_id ) {
				continue;
			}

			// Skip excluded terms.
			if ( in_array( $term->slug, $exclude_terms ) ) {
				continue;
			}

			// Set link class in loop to prevent issues with added term classes.
			$item_link_class = $link_class;
			$item_link_class[] = 'vcex-post-terms__item--' . absint( $term->term_id );

			if ( $link_class_xtra ) {
				$item_link_class = array_merge( $item_link_class, $link_class_xtra );
			}

			// Add to counter.
			$terms_count ++;

			// Add li tags.
			if ( in_array( $module_style, [ 'ul', 'ol' ] ) ) {
				$output .= '<li>';
			}

			// Hover styles
			$link_hover_data = [];

			if ( 'buttons' === $module_style && ! empty( $atts['button_hover_background'] ) ) {
				$button_hover_background = $atts['button_hover_background'];
				if ( 'term_color' === $atts['button_hover_background'] && ! $template_edit_mode ) {
					$button_hover_background = vcex_get_term_color( $term );
				}
				$link_hover_data['background'] = esc_attr( vcex_parse_color( $button_hover_background ) );
			}

			if ( ! empty( $atts['button_hover_color'] ) ) {
				$button_hover_color = $atts['button_hover_color'];
				if ( 'term_color' === $atts['button_hover_color'] && ! $template_edit_mode ) {
					$button_hover_color = vcex_get_term_color( $term );
				}
				$link_hover_data['color'] = esc_attr( vcex_parse_color( $button_hover_color ) );
			}

			$link_hover_data = $link_hover_data ? htmlspecialchars( wp_json_encode( $link_hover_data ) ) : '';

			// Add term colors.
			if ( (bool) vcex_get_term_color( $term ) ) {
				if ( 'term_color' === $button_background ) {
					$item_link_class[] = 'has-term-' . sanitize_html_class( $term->term_id ) . '-background-color';
				}
				if ( 'term_color' === $button_color ) {
					$item_link_class[] = 'has-term-' . sanitize_html_class( $term->term_id ) . '-color';
				}
			}

			/**
			 * Filters the vcex_post_terms element link class.
			 *
			 * @param array $class
			 * @param obj $term
			 * @param array $shortcode_atts
			 */
			$item_link_class = (array) apply_filters( 'vcex_post_terms_link_class', $item_link_class, $term, $atts );

			// Open term element.
			if ( 'true' == $atts['archive_link'] ) {

				$output .= '<a' . vcex_parse_html_attributes( [
					'href'            => $dummy_link ?? get_term_link( $term, $taxonomy ),
					'class'           => $item_link_class,
					'style'           => $link_style,
					'target'          => $archive_link_target,
					'data-wpex-hover' => $link_hover_data,
				] ) . '>';

			} else {

				$output .= '<span' . vcex_parse_html_attributes( [
					'class' => $item_link_class,
					'style' => $link_style,
					'data-wpex-hover' => $link_hover_data,
				] ) . '>';

			}

			// Display title.
			$output .= esc_html( $term->name );

			// Close term element.
			if ( 'true' == $atts['archive_link'] ) {
				$output .= '</a>';
			} else {
				$output .= '</span>';
			}

			// Add spacer for inline style.
			if ( 'inline' === $module_style && $terms_count < count( $terms ) ) {

				if ( $custom_spacer ) {
					$output .= ' ';
					$spacer = $custom_spacer;
				} else {
					$spacer = '&comma;';
				}

				$output .= '<span class="vcex-post-terms__separator vcex-spacer">' . do_shortcode( wp_strip_all_tags( $spacer ) ) . '</span> '; // @note don't use sanitize_text_field because that will trim spaces.

			}

			// Close li tags.
			if ( in_array( $module_style, [ 'ul', 'ol' ] ) ) {
				$output .= '</li>';
			}

			$first_run = false;

		endforeach;
	}

	// Close UL list.
	if ( 'ul' === $module_style ) {
		$output .= '</ul>';
	}

	// Open OL list
	elseif ( 'ol' === $module_style ) {
		$output .= '</ol>';
	}

// Close main wrapper.
$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
