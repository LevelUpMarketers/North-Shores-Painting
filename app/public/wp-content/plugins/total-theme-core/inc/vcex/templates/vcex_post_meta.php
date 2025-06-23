<?php

/**
 * vcex_post_meta shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $atts['sections'] ) ) {
	$sections = (array) vcex_vc_param_group_parse_atts( $atts['sections'] );
}

if ( empty( $sections ) ) {
	return;
}

global $post;

if ( ! $post ) {
	return;
}

$output       = '';
$is_edit_mode = vcex_is_template_edit_mode();
$style        = ! empty( $atts['style'] ) ? $atts['style'] : 'horizontal';

$wrap_class = [
	'vcex-post-meta',
	'meta',
	'vcex-module',
];

switch ( $style ) {
	case 'vertical':
		$wrap_class[] = 'meta-vertical';
		break;
	case 'horizontal':
	default:
		$wrap_class[] = 'wpex-flex wpex-flex-wrap wpex-items-center'; // allows vertical alignment for the author avatar.
		break;
}

if ( ! empty( $atts['align'] ) ) {
	$atts['text_align'] = $atts['align'];
	if ( 'horizontal' === $style ) {
		$justify_class = vcex_parse_justify_content_class( $atts['align'] );
		if ( $justify_class ) {
			$wrap_class[] = $justify_class;
		}
	}
}

if ( ! empty( $atts['color'] ) && empty( $atts['link_color'] ) ) {
	$wrap_class[] = 'wpex-child-inherit-color';
}

if ( ! empty( $atts['max_width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$wrap_class[] = vcex_parse_align_class( ! empty( $atts['float'] ) ? $atts['float'] : 'center' );
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_post_meta' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_post_meta', $atts );

// Output starts here.
$output .= '<ul class="' . esc_attr( $wrap_class ) . '">';

	// Separator html.
	if ( 'vertical' !== $style && ! empty( $atts['separator'] ) ) {
		switch ( $atts['separator'] ) {
			case 'dash':
				$separator = '&ndash;';
			break;
			case 'long_dash':
				$separator = '&mdash;';
			break;
			case 'dot':
				$separator = '&middot;';
			break;
			case 'forward_slash':
				$separator = '&sol;';
			break;
			case 'backslash':
				$separator = '&bsol;';
			break;
			case 'pipe':
				$separator = '&vert;';
			break;
			default:
				$separator = '';
			break;
		}

		if ( ! empty( $separator ) ) {
			$separator = '<li class="vcex-post-meta__separator">' . $separator . '</li>';
		}
	}

	// Sections.
	$count = 0;
	foreach ( $sections as $section ) {

		$section_args = wp_parse_args( $section, [
			'type'             => '',
			'label'            => '',
			'icon'             => '',
			'icon_type'        => 'ticons',
			'icon_fontawesome' => '',
			'icon_typicons'    => '',
		] );

		$section_html  = '';

		$icon_out      = vcex_get_icon_html( $section_args, 'icon', 'meta-icon' );
		$label_out     = '';

		// Parse label.
		if ( $section_args['label'] ) {

			$label_font_weight       = ! empty( $atts['label_font_weight'] ) ? $atts['label_font_weight'] : 'bold';
			$label_font_weight_class = vcex_parse_font_weight_class( $label_font_weight );

			$label_out = '<span class="meta-label-wrap"><span class="meta-label ' . $label_font_weight_class . '">';

				$label_out .= wp_strip_all_tags( $section_args['label'] );

				if ( vcex_validate_boolean( $atts['label_colon'] ) ) {
					$label_out .= ':';
				}

			$label_out .= '</span> ';

		}

		// Display sections.
		switch ( $section_args['type'] ) {

			// Date.
			case 'date':

				$section_html .= '<li class="meta-date">';

					if ( $icon_out ) {
						$section_html .= $icon_out;
					}

					if ( $label_out ) {
						$section_html .= $label_out;
					}

					$date_format = $section['date_format'] ?? '';

					$section_html .= '<time datetime="' . esc_attr( get_the_date( 'Y-m-d' ) ) . '">' . get_the_date( $date_format, $post->ID ) . '</time>';

					if ( $label_out ) {
						$section_html .= '</span>';
					}

				$section_html .= '</li>';

				break;

			// Author.
			case 'author':

				$section_html .= '<li class="meta-author">';

					if ( $icon_out ) {
						$section_html .= $icon_out;
					}

					if ( $label_out ) {
						$section_html .= $label_out;
					}

					$section_html .= '<span class="vcard author"><span class="fn">';
					
						$author_name = get_the_author_meta( 'display_name', $post->post_author );
						$author_name = apply_filters( 'the_author', $author_name );
						$author_url  = get_author_posts_url( $post->post_author );
						
						if ( $author_url ) {
							$section_html .= '<a href="' . esc_url( $author_url ) . '">' . esc_html( $author_name ) . '</a>';
						} else {
							$section_html .= esc_html( $author_name );
						}

					$section_html .= '</span></span>';

					if ( $label_out ) {
						$section_html .= '</span>';
					}

				$section_html .= '</li>';

				break;

			// Author with Avatar
			case 'author_w_avatar':

				$section_html .= '<li class="meta-author">';

					if ( $label_out ) {
						$section_html .= $label_out;
					}

					$author_url = get_author_posts_url( $post->post_author );

					if ( $author_url ) {
						$section_html .= '<a class="wpex-inline-flex wpex-items-center" href="' . esc_url( $author_url ) . '">';
					} else {
						$section_html .= '<div class="wpex-inline-flex wpex-items-center">';
					}

					$avatar_args = [
						'class' => 'wpex-align-middle wpex-rounded-full'
					];
					$avatar_size = ! empty( $section['avatar_size'] ) ? absint( $section['avatar_size'] ) : 25;
					$avatar = get_avatar( $post->post_author, absint( $avatar_size ), '', '', $avatar_args );

					if ( $avatar ) {
						$section_html .= '<span class="meta-author-avatar wpex-mr-10">' . $avatar . '</span>';
					}

					$author_name = get_the_author_meta( 'display_name', $post->post_author );
					$author_name = apply_filters( 'the_author', $author_name );

					$section_html .= '<span class="vcard author"><span class="fn">' . esc_html( $author_name ) . '</span></span>';

					if ( $label_out ) {
						$section_html .= '</span>';
					}

					if ( $author_url ) {
						$section_html .= '</a>';
					} else {
						$section_html .= '</div>';
					}
				
				$section_html .= '</li>';

				break;

			// Comments.
			case 'comments':

				$has_link = isset( $section['has_link'] ) ? vcex_validate_boolean( $section['has_link'] ) : false;
				$comment_link = $has_link ? get_comments_link( $post ) : '';
				$comment_number = get_comments_number();

				$section_html .= '<li class="meta-comments comment-scroll">';

					if ( $has_link && $comment_link ) {
						$section_html .= '<a href="' . esc_url( $comment_link ) . '" class="comments-link">';
					}

						if ( $icon_out ) {
							$section_html .= $icon_out;
						}

						if ( $label_out ) {
							$section_html .= $label_out;
						}

						if ( $comment_number == 0 ) {
							$section_html .= esc_html__( '0 Comments', 'total-theme-core' );
						} elseif ( $comment_number > 1 ) {
							$section_html .= $comment_number .' '. esc_html__( 'Comments', 'total-theme-core' );
						} else {
							$section_html .= esc_html__( '1 Comment',  'total-theme-core' );
						}

						if ( $label_out ) {
							$section_html .= '</span>';
						}

					if ( $has_link && $comment_link ) {
						$section_html .= '</a>';
					}

					$section_html .= '</li>';

				break;

			// Post terms.
			case 'post_terms':

				$taxonomy = ! empty( $section['taxonomy'] ) ? $section['taxonomy'] : '';
				$get_terms = '';

				// Get taxonomy dynamically.
				if ( ! empty( $section['taxonomy'] ) ) {
					$taxonomy = $section['taxonomy'];
				} elseif ( function_exists( 'wpex_get_post_type_cat_tax' ) ) {
					$taxonomy = wpex_get_post_type_cat_tax( get_post_type( vcex_get_the_ID() ) );
				}

				if ( $is_edit_mode ) {

					$section_html .= '<li class="meta-post-terms">';

						if ( $icon_out ) {
							$section_html .= $icon_out;
						}

						if ( $label_out ) {
							$section_html .= $label_out;
						}

						$section_html .= '<a href="#">' . esc_html__( 'Sample Item', 'total-theme-core' ) . '</a>';

						if ( $label_out ) {
							$section_html .= '</span>';
						}

					$section_html .= '</li>';

				} elseif ( $taxonomy ) {

					$get_terms = vcex_get_list_post_terms( $taxonomy, true );

					if ( $get_terms ) {

						$section_html .= '<li class="meta-post-terms">';

							if ( $icon_out ) {
								$section_html .= $icon_out;
							}

							if ( $label_out ) {
								$section_html .= $label_out;
							}

							$section_html .= '<span>' . $get_terms . '</span>';

							if ( $label_out ) {
								$section_html .= '</span>';
							}

						$section_html .= '</li>';

					}


				}

				break;

			// Last updated.
			case 'modified_date':

				$section_html .= '<li class="meta-modified-date">';

					if ( $icon_out ) {
						$section_html .= $icon_out;
					}

					if ( $label_out ) {
						$section_html .= $label_out;
					}

					$date_format = $section['date_format'] ?? '';

					$section_html .= '<time datetime="' . esc_attr( get_the_modified_date( 'Y-m-d' ) ) . '">' . get_the_modified_date( $date_format, $post->ID ) . '</time>';

					if ( $label_out ) {
						$section_html .= '</span>';
					}

				$section_html .= '</li>';

				break;

			// Estimated read time.
			case 'estimated_read_time':

				$section_html .= '<li class="meta-read-time">';

					if ( $icon_out ) {
						$section_html .= $icon_out;
					}

					if ( $label_out ) {
						$section_html .= $label_out;
					}

					$post_content = $post->post_content ?? '';

					if ( $post_content ) {

						$words = str_word_count( strip_tags( $post_content ) );
						$wpm = 200; // estimated words per minute.

						$minutes = ceil( $words / $wpm );

						if ( $minutes > 1 ) {
							$text = sprintf( esc_html__( '%s minute read', 'total-theme-core' ), $minutes );
						} else {
							$seconds = floor( $words % $wpm / ( $wpm / 60 ) );
							$text = sprintf( esc_html__( '%s second read', 'total-theme-core' ), $seconds );
						}

						$section_html .= $text;

					}

					if ( $label_out ) {
						$section_html .= '</span>';
					}

				$section_html .= '</li>';

				break;

				// Custom Field.
				case 'custom_field':

					$custom_field_name = $section['custom_field_name'] ?? '';

					if ( $custom_field_name ) {
						$custom_field_val  = '';

						if ( shortcode_exists( 'acf' ) ) {
							$custom_field_val = do_shortcode( '[acf field="' . $custom_field_name . '" post_id="' . $post->ID . '"]' );
						}

						if ( ! $custom_field_val && 0 !== $custom_field_val ) {
							$custom_field_val = get_post_meta( $post->ID, $custom_field_name, true );
						}

						if ( $custom_field_val && is_string( $custom_field_val ) ) {

							$section_html .= '<li class="meta-modified-date">';

								if ( $icon_out ) {
									$section_html .= $icon_out;
								}

								if ( $label_out ) {
									$section_html .= $label_out;
								}

								$section_html .= do_shortcode( wp_kses_post( $custom_field_val ) );

								if ( $label_out ) {
									$section_html .= '</span>';
								}

							$section_html .= '</li>';

						}

					}

					break;

				// Callback.
				case 'callback':

					$callback_function = $section['callback_function'] ?? '';

					if ( $callback_function
						&& function_exists( $callback_function )
						&& vcex_validate_user_func( $callback_function )
					) {

						$section_html .= '<li class="meta-callback">';

							if ( $icon_out ) {
								$section_html .= $icon_out;
							}

							if ( $label_out ) {
								$section_html .= $label_out;
							}

							$section_html .= wp_kses_post( call_user_func( $callback_function ) );

							if ( $label_out ) {
								$section_html .= '</span>';
							}

						$section_html .= '</li>';

					}

					break;

			default:
				$custom_section_output = apply_filters( 'vcex_post_meta_custom_section_output', $section_args['type'], null );
				if ( ! empty( $custom_section_output ) ) {
					$section_html .= $custom_section_output;
				}
				break;
		} // end switch.

		if ( $section_html ) {
			$count++;

			if ( ! empty( $separator ) && $count > 1 ) {
				$output .= $separator;
			}

			$output .= $section_html;

		}

	} // end foreach.

$output .= '</ul>';

// @codingStandardsIgnoreLine
echo $output;
