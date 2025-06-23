<?php

/**
 * vcex_users_grid shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

// Extract shortcode attributes.
extract( $atts );

// Declare and sanitize main vars.
$output = '';
$avatar_size = ! empty( $avatar_size ) ? absint( $avatar_size ) : '150';
$show_name = vcex_validate_att_boolean( 'name', $atts, true );
$show_social_links = vcex_validate_att_boolean( 'social_links', $atts, true );
$show_description = vcex_validate_att_boolean( 'description', $atts, true );

// Get user roles to query.
if ( ! empty( $role__in ) ) {
	if ( is_string( $role__in ) ) {
		$role__in = preg_split( '/\,[\s]*/', $role__in );
	}
} else {
	$role__in = [];
}

// Query arguments.
$args = apply_filters( 'vcex_users_grid_query_args', [
	'order'    => $order,
	'orderby'  => $orderby,
	'role__in' => $role__in,
] );

// Get users.
$users = get_users( $args );

// No users, lets bail completely!
if ( ! $users ) {
	return;
}

// Get onclick action (with fallback for old link_to_author_page param).
if ( isset( $link_to_author_page ) ) {
	if ( 'true' == $link_to_author_page ) {
		$onclick = 'author_page';
	} elseif ( 'false' == $link_to_author_page ) {
		$onclick = 'disable';
	}
}

// Display header if enabled.
if ( $header ) {
	$output .= vcex_get_module_header( [
		'style'   => $header_style,
		'content' => $header,
		'classes' => [ 'vcex-module-heading vcex_users_grid-heading' ],
	] );
}

// Wrap classes.
$wrap_classes = [
	'vcex-module',
	'vcex-users-grid',
	'wpex-row',
	'wpex-clr',
];

if ( 'masonry' === $grid_style ) {
	vcex_enqueue_isotope_scripts();
	$wrap_classes[] = 'vcex-isotope-grid';
	$wrap_classes[] = 'wpex-overflow-hidden';
}

if ( $columns_gap ) {
	$wrap_classes[] = 'gap-' . sanitize_html_class( $columns_gap );
}

if ( $bottom_margin_class = vcex_parse_margin_class( $bottom_margin, 'bottom' ) ) {
	$wrap_classes[] = $bottom_margin_class;
}

if ( $visibility_class = vcex_parse_visibility_class( $visibility ) ) {
	$wrap_classes[] = $visibility_class;
}

if ( $el_class = vcex_get_extra_class( $classes ) ) {
	$wrap_classes[] = $el_class;
}

$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_users_grid', $atts );

// Begin output.
$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

	// Define loop vars.
	$first_run = true;
	$counter = 0;

	// Loop through users.
	foreach ( $users as $user ) :

		$counter++;

		$author_link = ''; // Reset after each user.

		// Get author description (check early so we can check later if the description should display or not).
		if ( 'true' == $description ) {
			$get_description = get_the_author_meta( 'description', $user->ID );
		}

		// Get entry classes.
		if ( $first_run ) {

			$entry_classes = [
				'vcex-users-grid-entry',
				'wpex-clr',
			];

			if ( 'masonry' === $grid_style ) {
				$entry_classes[] = 'vcex-isotope-entry';
			}

			$entry_classes[] = vcex_get_grid_column_class( $atts );

			if ( 'false' == $columns_responsive ) {
				$entry_classes[] = 'nr-col';
			} else {
				$entry_classes[] = 'col';
			}

			if ( $css_animation_class = vcex_get_css_animation( $css_animation ) ) {
				$entry_classes[] = $css_animation_class;
			}

			if ( $content_alignment ) {
				$entry_classes[] = vcex_parse_text_align_class( $content_alignment );
			}

			$entry_classes[] = 'wpex-last-mb-0';

		}

		$entry_classes['counter'] = 'col-' . sanitize_html_class( $counter ) . '';

		// Befin entry output.
		$output .= '<div class="' . esc_attr( implode( ' ', $entry_classes ) ) . '">';

			if ( $entry_css ) {
				$output .= '<div class="entry-css-wrap wpex-clr ' . esc_attr( vcex_vc_shortcode_custom_css_class( $entry_css ) ) .'">';
			}

			// Avatar.
			if ( 'true' == $avatar ) {

				//$atts['media_type'] = 'thumbnail'; // users grid doesn't need to check for hover styles or overlays.

				if ( $first_run ) {

					$media_classes = [
						'entry-media',
						'wpex-mb-20',
						'wpex-clr',
					];

					if ( $avatar_hover_style ) {
						$media_classes[] .= vcex_image_hover_classes( $avatar_hover_style );
					}

				}

				$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_media_class( null, 'vcex_users_grid', $atts ) ) ) . '">';

					if ( 'disable' !== $onclick ) {
						if ( 'author_page' === $onclick ) {
							$author_link = get_author_posts_url( $user->ID );
						} elseif ( 'user_website' === $onclick ) {
							$author_link = $user->user_url;
						}
						if ( $author_link ) {
							$author_link = '<a href="' . esc_url( $author_link ) . '" title="' . esc_attr( $user->display_name ) . '">';
							$output .= $author_link;
						}
					}

					// Avatar image classes.
					$avatar_classes = [
						'wpex-align-middle',
					];

					if ( $avatar_border_radius ) {
						$avatar_classes[] = 'wpex-' . sanitize_html_class( $avatar_border_radius );
					}

					$avatar_args = apply_filters( 'vcex_users_grid_avatar_args', [
						'class' => implode( ' ', $avatar_classes ),
					] );

					// Meta based avatar.
					if ( ! empty( $avatar_meta_field ) ) {
						if ( $avatar = get_user_meta( $user->ID, $avatar_meta_field, true ) ) {
							if ( is_numeric( $avatar ) ) {
								$get_avatar = wp_get_attachment_image(
									$avatar,
									[ $avatar_size, $avatar_size ],
									false,
									$avatar_args
								);
							}
							if ( ! empty( $get_avatar ) ) {
								$output .= $get_avatar;
							} else {
								$output .= '<img src="' . esc_url( $avatar ) . '" alt="' . esc_attr( $user->display_name ) . '">';
							}
						}
					}

					// Standard avatar.
					else {
						$output .= get_avatar( $user->ID, $avatar_size, '', $user->display_name, $avatar_args );
					}

					if ( $author_link ) {
						$output .= '</a>';
					}
				$output .= '</div>';

			}

			/*--------------------------------*/
			/* [ Entry Content ]
			/*--------------------------------*/
			if ( $show_name || $show_description || $show_social_links ) {

				$details_output = '';

				// Display name.
				if ( $show_name ) {
					if ( $first_run ) {
						$name_tag_escaped = $name_heading_tag ? tag_escape( $name_heading_tag ) : 'div';
						$name_classes = 'entry-title wpex-clr';
						if ( $name_color && 'disable' !== $onclick ) {
							$name_classes .= ' wpex-child-inherit-color';
						}
						$name_css = vcex_inline_style( [
							'color'          => $name_color,
							'font_size'      => $name_font_size,
							'font_weight'    => $name_font_weight,
							'font_family'    => $name_font_family,
							'margin_bottom'  => $name_margin_bottom,
							'text_transform' => $name_text_transform,
						] );
					}
					$details_output .= '<' . $name_tag_escaped . ' class="' . esc_attr( $name_classes ) . '"' . $name_css . '>';
						if ( $author_link ) {
							$details_output .= $author_link;
						}
						$details_output .= $user->display_name;
						if ( $author_link ) {
							$details_output .= '</a>';
						}
					$details_output .= '</' . $name_tag_escaped . '>';
				}

				// Description.
				if ( $show_description ) {
					if ( $first_run ) {
						$description_css = vcex_inline_style( [
							'color'          => $description_color,
							'font_size'      => $description_font_size,
							'font_weight'    => $description_font_weight,
							'font_family'    => $description_font_family,
						] );
					}
					if ( ! empty( $get_description ) ) {
						$details_output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_excerpt_class( null, 'vcex_users_grid', $atts ) ) ) . '" '. $description_css .'>';
							$details_output .= wpautop( wp_kses_post( $get_description ) );
						$details_output .= '</div>';
					}
				}

				// Display social.
				if ( $show_social_links ) {
					if ( $first_run ) {
						$social_links_inline_css = vcex_inline_style( [
							'padding'   => $social_links_padding,
							'font_size' => $social_links_size,
						] );
						$social_links_style = vcex_get_social_button_class( $social_links_style );
					}
					$details_output .= '<div class="entry-social-links wpex-mt-15 wpex-last-mr-0 wpex-clr"' . $social_links_inline_css . '>';
						$details_output .= vcex_get_user_social_links( $user->ID, 'icons', [
							'class' => [
								$social_links_style,
								'wpex-mt-5',
								'wpex-mr-5'
							],
						] );
					$details_output .= '</div>';
				}

				if ( $details_output ) {
					if ( $first_run ) {
						$details_style = vcex_inline_style( [
							'background_color' => $content_background_color,
							'border_color'     => $content_border_color
						] );
					}
					$output .= '<div class="' . esc_attr( implode( ' ', vcex_get_entry_details_class( [ 'vcex-users-grid-details' ], 'vcex_users_grid', $atts ) ) ) . '"' . $details_style . '>' . $details_output . '</div>';
				}

			}

			if ( $entry_css ) {
				$output .= '</div>';
			}

		$output .= '</div>'; // end entry.

		// Clear counter.
		if ( $counter === (int) $columns ) {
			$counter = 0;
		}

		$first_run = false;

	// End loop.
	endforeach;

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
