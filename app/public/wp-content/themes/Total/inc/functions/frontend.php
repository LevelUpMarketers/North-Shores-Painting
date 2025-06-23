<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# General
	# Head Tags
	# Current Post ID
	# Loop
	# Star Ratings
	# Breadcrumbs
	# Post Slider
	# Single/Entry Blocks
	# Post Audio
	# Post Gallery
	# Comments
	# Excerpts
	# Tribe Events

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Outputs the HTML element class attribute.
 */
function wpex_html_class(): void {
	$class = [];
	if ( totaltheme_has_classic_styles() ) {
		$class[] = 'wpex-classic-style';
	}
	$class = (array) apply_filters( 'wpex_html_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Echo Google Analytics tracking code.
 */
function wpex_google_analytics_tag(): void {
	$property_id = (string) apply_filters( 'wpex_google_property_id', get_theme_mod( 'google_property_id' ) );
	if ( ! $property_id ) {
		return;
	}
	$properties = array_map( 'sanitize_text_field', array_filter( explode( ',', $property_id ) ) );
	foreach ( $properties as $property ) {
		if ( str_starts_with( $property, 'G-' ) ) {
			$property_safe = esc_attr( $property );
			echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id={$property_safe}\"></script>";
			echo "<script>";
				echo "window.dataLayer = window.dataLayer || [];";
				echo "function gtag(){dataLayer.push(arguments);}";
				echo "gtag('js', new Date());";
				echo "gtag('config', '{$property_safe}', { 'anonymize_ip': true });";
			echo "</script>";
		}
	}
}

/**
 * Content wrap class.
 */
function totaltheme_content_wrap_class(): void {
	$class = 'container';

	// Check if the content wrap is a flex container.
	$is_flex = totaltheme_call_static( 'Sidebars\Primary', 'is_sticky' ) && wpex_has_sidebar();
	$is_flex = (bool) apply_filters( 'totaltheme/content_wrap/is_flex_container', $is_flex );

	if ( $is_flex ) {
		$class .= ' wpex-flex wpex-flex-wrap wpex-justify-between';
		$layout = wpex_get_post_layout();
		$sidebar_insert_hook = totaltheme_call_static( 'Sidebars\Primary', 'get_insert_hook_name' );
		if ( ( 'left-sidebar' === $layout && 'wpex_hook_primary_after' === $sidebar_insert_hook )
			|| ( 'right-sidebar' === $layout && 'wpex_hook_primary_before' === $sidebar_insert_hook )
		) {
			$class .= ' wpex-flex-row-reverse';
		}
	} else {
		$class .= ' wpex-clr';
	}

	echo ' class="' . esc_attr( $class ) . '"';
}

/**
 * Get estimated read time from content.
 */
function totaltheme_get_estimatd_read_time( $content = '', $args = [], $class_object = null ) {
	if ( ! $content ) {
		return;
	}

	$default_args = [
		'minute_text'      => esc_html__( '%s minute read', 'total' ),
		'second_text'      => esc_html__( '%s second read', 'total' ),
		'words_per_minute' => 200,
	];

	$args = (array) apply_filters( 'wpex_estimated_read_time_args', wp_parse_args( $args, $default_args ), $class_object );

	$words   = str_word_count( wp_strip_all_tags( $content ) );
	$wpm     = $args['words_per_minute'];
	$minutes = ceil( $words / $wpm );

	if ( $minutes > 1 ) {
		return sprintf( $args['minute_text'], $minutes );
	} else {
		$seconds = floor( $words % $wpm / ( $wpm / 60 ) );
		return sprintf( $args['second_text'], $seconds );
	}
}

/**
 * Estimated read time.
 */
function totaltheme_get_post_estimated_read_time( $post = null, $args = [], $class_object = null ) {
	$post = get_post( $post );
	if ( ! empty( $post->post_content ) ) {
		return totaltheme_get_estimatd_read_time( $post->post_content, $args, $class_object );
	}
}

/**
 * Outputs a theme heading.
 */
function wpex_heading( $args = [] ) {
	$echo = $args['echo'] ?? true;
	if ( $echo ) {
		echo wpex_get_heading( $args );
	} else {
		return wpex_get_heading( $args );
	}
}

/**
 * Outputs a theme heading.
 */
function wpex_get_heading( $args = [] ) {
	$defaults = [
		'apply_filters' => '',
		'content'       => '',
		'align'         => get_theme_mod( 'theme_heading_align', null ),
		'tag'           => get_theme_mod( 'theme_heading_tag' ) ?: 'div',
		'style'         => get_theme_mod( 'theme_heading_style' ) ?: 'border-bottom',
		'classes'       => [],
	];

	if ( ! empty( $args['apply_filters'] ) ) {
		$args = apply_filters( "wpex_heading_{$args['apply_filters']}", $args );
	}

	$args = (array) apply_filters( 'wpex_get_heading_args', $args );
	$args = wp_parse_args( $args, $defaults );

	// Style can't be empty if so lets set it to the default.
	if ( empty( $args['style'] ) ) {
		$args['style'] = $defaults['style'];
	}

	// Extract args.
	extract( $args );

	// Return if text is empty.
	if ( ! $content ) {
		return;
	}

	// Add custom classes.
	$add_classes = $classes;
	$classes = [
		'theme-heading',
	];

	if ( $style ) {
		$classes[] = $style;
	}

	if ( $align && in_array( $align, [ 'left', 'center', 'right' ], true ) ) {
		$classes[] = "wpex-text-{$align}";
		// Primarily used when heading has max-width.
		if ( 'center' === $align ) {
			$classes[] = 'wpex-mx-auto';
		} elseif ( 'left' === $align ) {
			$classes[] = 'wpex-mr-auto';
		} elseif ( 'right' === $align ) {
			$classes[] = 'wpex-ml-auto';
		}
	}

	if ( $add_classes && is_array( $add_classes ) ) {
		$classes = array_merge( $classes, $add_classes );
	}

	$safe_tag = tag_escape( $tag ) ?: 'div';

	$html = '<' . $safe_tag . ' class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		$html .= '<span class="text">' . do_shortcode( totaltheme_replace_vars( wp_kses_post( $content ) ) ) . '</span>';
	$html .= '</' . $safe_tag . '>';

	return $html;
}

/*-------------------------------------------------------------------------------*/
/* [ Head Tags ]
/*-------------------------------------------------------------------------------*/

/**
 * Return correct viewport tag
 */
function wpex_get_meta_viewport() {
	$viewport = '';

	// Responsive viewport viewport.
	if ( wpex_is_layout_responsive() ) {
		$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1">';
	}

	// Non responsive meta viewport.
	else {
		$width = get_theme_mod( 'main_container_width', '980' );
		if ( $width && false == strpos( $width, '%' ) ) {
			$width = $width ? intval( $width ) : '980';
			if ( 'boxed' === wpex_site_layout() ) {
				$outer_margin  = intval( get_theme_mod( 'boxed_padding', 30 ) );
				$inner_padding = 30;
				$width = $width + ( $inner_padding * 2 ) + ( $outer_margin * 2 ); // Add inner + outer padding
			}
			$viewport = '<meta name="viewport" content="width=' . absint( apply_filters( 'wpex_viewport_width', $width ) ) . '">';
		} else {
			$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1">';
		}
	}

	/**
	 * Filters the site meta viewport tag.
	 *
	 * @param string $viewport
	 */
	$viewport = (string) apply_filters( 'wpex_meta_viewport', $viewport );

	if ( $viewport ) {
		return $viewport;
	}
}

/**
 * Echo viewport tag.
 */
function wpex_meta_viewport() {
	echo wpex_get_meta_viewport() . "\n";
}
add_action( 'wp_head', 'wpex_meta_viewport', 1 );

/*-------------------------------------------------------------------------------*/
/* [ Current Post ID ]
/*-------------------------------------------------------------------------------*/

/**
 * Alternative to get_the_ID().
 */
function wpex_get_current_post_id() {
	$card_id = get_query_var( 'wpex_card_post_id' );

	if ( ! empty( $card_id ) ) {
		return $card_id;
	}

	$query_var = get_query_var( 'wpex_current_post_id' );

	if ( ! empty( $query_var ) ) {
		return $query_var;
	}

	static $id = null;

	if ( null === $id ) {
		if ( \wp_doing_ajax() ) {
			if ( $referrer = wp_get_referer() ) {
				$id = url_to_postid( $referrer );
			}
		} else {
			if ( ! empty( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], [ 'wp-signup.php', 'wp-login.php' , 'wp-register.php' ], true ) ) {
				$id = 0; // prevents issues where homepage meta settings effect these pages.
			} else {
				if ( is_singular() ) {
					$id = get_queried_object_id() ?: get_the_ID();
				}
				if ( is_home() && $page_for_posts = get_option( 'page_for_posts' ) ) {
					$id = $page_for_posts;
				}
				if ( empty( $id ) && wpex_is_woo_shop() ) {
					$id = totaltheme_wc_get_page_id( 'shop' );
				}
			}
		}
		$id = apply_filters( 'wpex_post_id', $id ); // @deprecated 5.1.2
		$id = apply_filters( 'wpex_current_post_id', $id );
	}

	return $id;
}

/**
 * Set or update the current post id (wpex_current_post_id).
 */
function wpex_set_current_post_id( $post_ID = null ) {
	set_query_var( 'wpex_current_post_id', $post_ID );
}

/*-------------------------------------------------------------------------------*/
/* [ Loop ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns the loop top class.
 */
function wpex_loop_top_class(): void {
	$classes = (array) apply_filters( 'wpex_loop_top_class', wpex_get_archive_grid_class() );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Set loop instance.
 */
function wpex_set_loop_instance( $instance = 'archive' ): void {
	set_query_var( 'wpex_loop', $instance );
}

/**
 * Returns loop instance.
 */
function wpex_get_loop_instance(): string {
	$instance = get_query_var( 'wpex_loop' );
	if ( ! $instance ) {
		global $wpex_loop;
		if ( $wpex_loop ) {
			$instance = $wpex_loop;
		}
	}
	if ( empty( $instance ) ) {
		$instance = 'archive'; // fallback required.
	}
	return (string) $instance;
}

/**
 * Set loop running total.
 */
function wpex_increment_loop_running_count(): void {
	set_query_var( 'wpex_loop_running_count', absint( get_query_var( 'wpex_loop_running_count' ) ) + 1 );
}

/**
 * Set loop counter.
 */
function wpex_set_loop_counter( $count = 0 ): void {
	set_query_var( 'wpex_count', intval( $count ) );
}

/**
 * Returns loop counter.
 */
function wpex_get_loop_counter(): int {
	$count = get_query_var( 'wpex_count' );
	if ( ! $count ) {
		global $wpex_count;
		if ( $wpex_count ) {
			$count = $wpex_count;
		}
	}
	return (int) $count;
}

/**
 * Increase loop counter.
 */
function wpex_increment_loop_counter(): void {
	wpex_set_loop_counter( intval( wpex_get_loop_counter() ) + 1 );
}

/**
 * Maybe reset loop counter.
 */
function wpex_maybe_reset_loop_counter( $check = '' ): void {
	$check = intval( $check );
	if ( $check && $check === wpex_get_loop_counter() ) {
		wpex_set_loop_counter( 0 );
	}
}

/**
 * Clear loop query vars
 */
function wpex_reset_loop_query_vars(): void {
	set_query_var( 'wpex_loop', null );
	set_query_var( 'wpex_count', null );
	set_query_var( 'wpex_loop_running_count', null );
}

/*-------------------------------------------------------------------------------*/
/* [ Star Ratings ]
/*-------------------------------------------------------------------------------*/

/**
 * Get star rating.
 */
if ( ! function_exists( 'wpex_get_star_rating' ) ) {
	function wpex_get_star_rating( $rating = '', $post_id = '', $before = '', $after = '' ): string {
		$post_id = $post_id ?: get_the_ID();
		$rating  = $rating ?: get_post_meta( $post_id, 'wpex_post_rating', true );

		if ( empty( $rating ) ) {
			return '';
		}

		$og_rating = $rating;
		$rating = abs( $rating );

		// Start html
		$html = '<div class="wpex-star-rating wpex-inline-flex wpex-flex-wrap">';

			// Star icons
			$full_star  = totaltheme_get_icon( 'star' );
			$half_star  = totaltheme_get_icon( 'star-half-empty' );
			$empty_star = totaltheme_get_icon( 'star-empty' );

			$max_rating = (int) apply_filters( 'wpex_star_rating_max_value', 5, $post_id );

			// Integers
			if ( ( is_numeric( $rating ) && ( intval( $rating ) == floatval( $rating ) ) ) ) {
				$html .= str_repeat( $full_star, $rating );
				if ( $rating < $max_rating ) {
					$html .= str_repeat( $empty_star, $max_rating - $rating );
				}

			// Fractions
			} else {
				$rating = intval( $rating );
				$html .= str_repeat( $full_star, $rating );
				$html .= $half_star;
				if ( $rating < $max_rating ) {
					$html .= str_repeat( $empty_star, ( $max_rating - 1 ) - $rating );
				}
			}

			// Add screen-reader text
			$html .= '<span class="screen-reader-text">' . esc_html__( 'Rating', 'total' ) . ': ' . esc_html( $og_rating ) . '</span>';

		// Close wrapper
		$html .= '</div>';

		$html = (string) apply_filters( 'wpex_get_star_rating', $html, $rating );

		// Return star rating html
		if ( $html ) {
			$before . $html . $after;
		}

		return $html;
	}

}

/*-------------------------------------------------------------------------------*/
/* [ Breadcrumbs ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns breadcrumbs positon.
 */
function wpex_breadcrumbs_position() {
	$position = get_theme_mod( 'breadcrumbs_position' ) ?: 'page_header_aside';

	// Check renamed styles.
	if ( 'absolute' === $position || 'default' === $position ) {
		$position = 'page_header_aside';
	}

	/**
	 * Filters the breadcrumbs position.
	 *
	 * @param string $postion
	 */
	$position = (string) apply_filters( 'wpex_breadcrumbs_position', $position );

	// If position is empty, let's assume it's custom.
	if ( empty( $position ) ) {
		$position = 'custom';
	}

	return $position;
}

/**
 * Check if breadcrumbs should be contained or not.
 */
function wpex_has_breadcrumbs_container() {
	$check = false;

	$position = wpex_breadcrumbs_position();

	if ( 'header_after' === $position || 'page_header_after' === $position ) {
		$check = true;
	}

	/**
	 * Filters whether the breadcrumbs has the container element class or not.
	 *
	 * @param bool $check
	 */
	$check = (bool) apply_filters( 'wpex_has_breadcrumbs_container', $check );

	return $check;
}

/*-------------------------------------------------------------------------------*/
/* [ Post Slider ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if a post has a slider defined.
 */
function wpex_has_post_slider( $post_id = '' ) {
	return (bool) apply_filters( 'wpex_has_post_slider', (bool) wpex_get_post_slider_shortcode( $post_id ) );
}

/**
 * Get post slider position.
 */
function wpex_post_slider_position( $post_id = '' ) {
	$position = 'below_title';
	$post_id  = $post_id ?: wpex_get_current_post_id();
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_post_slider_shortcode_position', true ) ) {
		$position = $meta;
	}
	return (string) apply_filters( 'wpex_post_slider_position', $position, $meta );
}

/**
 * Get post slider shortcode.
 */
function wpex_get_post_slider_shortcode( $post_id = '' ) {
	$slider  = '';
	$post_id = $post_id ?: wpex_get_current_post_id();
	if ( $post_id ) {
		$slider = get_post_meta( $post_id, 'wpex_post_slider_shortcode', true );
		if ( ! $slider ) {
			$slider = get_post_meta( $post_id, 'wpex_page_slider_shortcode', true ); // deprecated meta.
		}
	}
	return (string) apply_filters( 'wpex_post_slider_shortcode', $slider );
}

/*-------------------------------------------------------------------------------*/
/* [ Single/Entry Blocks ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns array of blocks for the single post type layout.
 */
function wpex_single_blocks( $post_type = '' ) {
	if ( ! $post_type ) {
		$post_type = get_post_type();
	}
	$blocks   = [];
	$fallback = false;
	switch ( $post_type ) {
		case 'page':
			$blocks = TotalTheme\Page\Single_Blocks::get();
			break;
		case 'post':
			$blocks = TotalTheme\Blog\Single_Blocks::get();
			break;
		case 'elementor_library':
			$blocks = [ 'content' ];
			break;
		case 'testimonials':
			$fallback = ! \get_theme_mod( 'testimonials_enable', true );
			break;
		case 'portfolio':
			if ( TotalTheme\Portfolio\Post_Type::is_enabled() ) {
				$blocks = TotalTheme\Portfolio\Single_Blocks::get();
			}
			$fallback = true;
			break;
		case 'staff':
			if ( TotalTheme\Staff\Post_Type::is_enabled() ) {
				$blocks = TotalTheme\Staff\Single_Blocks::get();
			}
			$fallback = true;
			break;
		default:
			$fallback = true;
			break;
	}
	if ( $blocks ) {
		return $blocks;
	} elseif ( $fallback ) {
		return TotalTheme\CPT\Single_Blocks::get();
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Post Audio ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if a given post has a video.
 */
function wpex_has_post_audio( $post_id = null ) {
	return (bool) wpex_get_post_audio( $post_id );
}

/**
 * Returns post audio.
 */
function wpex_get_post_audio( $post_id = '' ) {
	$audio   = '';
	$post_id = $post_id ?: get_the_ID();

	// Check for self-hosted first.
	if ( $self_hosted = get_post_meta( $post_id, 'wpex_post_self_hosted_media', true ) ) {
		if ( is_numeric( $self_hosted ) ) {
			if ( wp_attachment_is( 'audio', $self_hosted ) ) {
				$audio = $self_hosted;
			}
		} else {
			$audio = $self_hosted;
		}
	}

	// Check for wpex_post_audio custom field.
	if ( ! $audio  ) {
		$audio = get_post_meta( $post_id, 'wpex_post_audio', true );
	}

	// Check for post oembed.
	if ( ! $audio ) {
		$audio = get_post_meta( $post_id, 'wpex_post_oembed', true );
	}

	// Check old redux custom field last.
	if ( ! $audio ) {
		$self_hosted = get_post_meta( $post_id, 'wpex_post_self_hosted_shortcode_redux', true );
		if ( is_numeric( $self_hosted ) ) {
			if ( wp_attachment_is( 'audio', $self_hosted ) ) {
				$audio = $self_hosted;
			}
		} else {
			$audio = $self_hosted;
		}
	}

	return (string) apply_filters( 'wpex_get_post_audio', $audio );
}

/**
 * Echo post audio HTML.
 */
function wpex_post_audio_html( $audio = '' ) {
	echo wpex_get_post_audio_html( $audio );
}

/**
 * Returns post audio.
 */
function wpex_get_post_audio_html( $audio = '' ) {
	$audio = $audio ?: wpex_get_post_audio();

	if ( ! $audio ) {
		return;
	}

	// Check if self hosted.
	$self_hosted = ( $audio === get_post_meta( get_the_ID(), 'wpex_post_self_hosted_media', true ) ) ? true : false;

	if ( $self_hosted ) {
		$audio = ( is_numeric( $audio ) ) ? wp_get_attachment_url( $audio ) : $audio;
		/*return wp_audio_shortcode( [
			'src' => $audio,
		] );*/
		return '<audio class="wpex-w-100" controls src="' . esc_url( $audio ) . '"></audio>';
	}

	// Return oEmbed.
	else {
		if ( apply_filters( 'wpex_has_oembed_cache', true ) ) { // filter added for testing purposes only.
			global $wp_embed;
			if ( $wp_embed && is_object( $wp_embed ) ) {
				return $wp_embed->shortcode( array(), $audio );
			}
		} else {
			return wp_oembed_get( $audio );
		}
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Post Gallery @todo move to inc/post/gallery/]
/*-------------------------------------------------------------------------------*/

/**
 * Retrieve attachment IDs.
 *
 * @todo rename to totaltheme_get_post_gallery_attachments();
 */
function wpex_get_gallery_ids( $post_id = '' ) {
	$attachment_ids = [];
	$post_id        = $post_id ?: wpex_get_current_post_id();

	if ( class_exists( 'WC_product' ) && 'product' === get_post_type( $post_id ) ) {
		$product = new WC_product( $post_id );
		if ( $product && method_exists( $product, 'get_gallery_image_ids' ) ) {
			$attachment_ids = $product->get_gallery_image_ids();
		}
	}

	if ( ! $attachment_ids && $meta_ids = get_post_meta( $post_id, '_easy_image_gallery', true ) ) {
		$attachment_ids = $meta_ids;
	}

	if ( $attachment_ids && is_string( $attachment_ids ) ) {
		$attachment_ids = explode( ',', $attachment_ids );
	}

	if ( apply_filters( 'totaltheme/post/gallery/include_post_thumbnail', false )
		&& $featured_image = get_post_thumbnail_id()
	) {
		array_unshift( $attachment_ids, $featured_image );
	}

	$attachment_ids = apply_filters( 'wpex_get_post_gallery_ids', $attachment_ids ); // @deprecated 6.0
	$attachment_ids = (array) apply_filters( 'totaltheme/post/gallery/list', $attachment_ids );

	if ( $attachment_ids ) {
		return array_values( array_filter( $attachment_ids, 'wp_attachment_is_image' ) );
	}
}

/**
 * Get array of gallery image urls.
 *
 * @todo deprecated - not used anywhere.
 */
function wpex_get_gallery_images( $post_id = '', $size = 'full' ) {
	if ( ! $post_id ) {
		$post_id = wpex_get_current_post_id();
	}

	$ids = wpex_get_gallery_ids( $post_id );

	if ( ! $ids ) {
		return;
	}

	$images = [];

	foreach ( $ids as $id ) {
		$img_url = wpex_image_resize( [
			'attachment' => $id,
			'size'       => $size,
			'return'     => 'url',
		] );
		if ( $img_url ) {
			$images[] = $img_url;
		}
	}

	return $images;
}

/**
 * Check if gallery lightbox is enabled.
 */
function wpex_gallery_is_lightbox_enabled( $post_id = '' ) {
	$post_id = $post_id ?: wpex_get_current_post_id();
	return 'on' === get_post_meta( $post_id, '_easy_image_gallery_link_images', true );
}

/**
 * Check if the post has a gallery.
 *
 * @todo rename to totaltheme_has_post_gallery()
 */
function wpex_has_post_gallery( $post_id = '' ): bool {
	return (bool) wpex_get_gallery_ids( $post_id ?: get_the_ID() );
}

/*-------------------------------------------------------------------------------*/
/* [ Comments ]
/*-------------------------------------------------------------------------------*/

/**
 * Comments element class.
 */
function wpex_comments_class() {
	$classes = [
		'comments-area',
	];

	if ( 'full-screen' === wpex_content_area_layout() ) {
		$classes[] = 'container';
	}

	if ( get_option( 'show_avatars' ) ){
		$classes[] = 'show-avatars';
	}

	$classes[] = 'wpex-mb-40';
	$classes[] = 'wpex-clr';

	$classes = (array) apply_filters( 'wpex_comments_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Excerpts ]
/*-------------------------------------------------------------------------------*/

/**
 * Custom excerpt length for standard posts.
 */
function wpex_excerpt_length() {
	$length = get_theme_mod( 'blog_excerpt_length', 40 );
	if ( is_category() ) {
		$term_data = wpex_get_category_meta( '', 'wpex_term_excerpt_length' );
		if ( $term_data || 0 === $term_data || '0' === $term_data ) {
			$length = $term_data;
		}
	}
	return (int) apply_filters( 'wpex_excerpt_length', $length );
}

/**
 * Change default read more style.
 */
function wpex_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'wpex_excerpt_more', 10 );

/**
 * Prevent Page Scroll When Clicking the More Link.
 */
function wpex_remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'wpex_remove_more_link_scroll' );

/*-------------------------------------------------------------------------------*/
/* [ Tribe Events ]
/*-------------------------------------------------------------------------------*/

/**
 * Displays event date.
 */
function wpex_get_tribe_event_date( $instance = '' ) {
	if ( function_exists( 'tribe_get_start_date' ) ) {
		return apply_filters(
			'wpex_get_tribe_event_date',
			tribe_get_start_date( get_the_ID(), false, get_option( 'date_format' ) ),
			$instance
		);
	}
}

/**
 * Gets correct tribe events page ID.
 */
function wpex_get_tribe_events_main_page_id() {
	if ( $mod = get_theme_mod( 'tribe_events_main_page' ) ) {
		return $mod;
	}
	if ( class_exists( 'Tribe__Settings_Manager' ) ) {
		$page_slug = Tribe__Settings_Manager::get_option( 'eventsSlug', 'events' );
		if ( $page_slug && $page = get_page_by_path( $page_slug ) ) {
			return $page->ID;
		}
	}
}
