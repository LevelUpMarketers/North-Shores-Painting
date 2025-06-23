<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# Archives
	# Entry
	# Single
	# Slider
	# Related
	# Cards
	# Deprecated

/*-------------------------------------------------------------------------------*/
/* [ Archives ]
/*-------------------------------------------------------------------------------*/

/**
 * Exclude categories from the blog.
 * 
 * @note This function runs on pre_get_posts
 */
if ( ! function_exists( 'wpex_blog_exclude_categories' ) ) {
	function wpex_blog_exclude_categories( $deprecated = true ) {
		$cats = get_theme_mod( 'blog_cats_exclude' );
		if ( $cats && ! is_array( $cats ) ) {
			$cats = explode( ',', $cats ); // Convert to array
		}
		return $cats;
	}
}

/**
 * Returns the grid style.
 */
function wpex_blog_grid_style(): string {
	$style = (string) get_theme_mod( 'blog_grid_style' );
	if ( $cat_meta = wpex_get_category_meta( '', 'wpex_term_grid_style' ) ) {
		$style = $cat_meta;
	}
	if ( ! $style ) {
		$style = 'fit-rows';
	}
	return (string) apply_filters( 'wpex_blog_grid_style', $style );
}

/**
 * Checks if it's a fit-rows style grid.
 *
 * @deprecated
 */
function wpex_blog_fit_rows(): bool {
	$check = false;
	if ( 'grid-entry-style' === wpex_blog_entry_style() ) {
		$check = true;
	}
	return (bool) apply_filters( 'wpex_blog_fit_rows', $check );
}

/**
 * Returns the correct pagination style.
 */
function wpex_blog_pagination_style(): string {
	$style = get_theme_mod( 'blog_pagination_style' );
	if ( is_category() && $cat_meta = wpex_get_category_meta( '', 'wpex_term_pagination' ) ) {
		$style = $cat_meta;
	}
	return (string) apply_filters( 'wpex_blog_pagination_style', $style );
}

/**
 * Get blog wrap classes.
 */
function wpex_get_blog_wrap_classes( $classes = NULL ): string {
	if ( $classes ) {
		return (string) $classes;
	}

	$classes = [
		'entries',
	];

	$style = wpex_blog_entry_style();

	// Grid classes.
	if ( $style === 'grid-entry-style' || 'wpex_card' === $style ) {

		$classes[] = 'wpex-row';

		if ( 'masonry' === wpex_blog_grid_style() ) {
			$classes[] = 'wpex-masonry-grid';
			$classes[] = 'blog-masonry-grid';
			wpex_enqueue_isotope_scripts(); // good place to load masonry scripts
		} else {
			$classes[] = 'blog-grid';
		}

		$gap = wpex_get_category_meta( '', 'wpex_term_grid_gap' ) ?: get_theme_mod( 'blog_grid_gap' );

		if ( $gap ) {
			$classes[] = wpex_gap_class( $gap );
		}

	}

	// Left thumbs extra classes.
	if ( 'thumbnail-entry-style' === $style ) {
		$classes[] = 'left-thumbs';
	}

	// Add some margin when author is enabled.
	if ( $style === 'grid-entry-style' && get_theme_mod( 'blog_entry_author_avatar' ) ) {
		$classes[] = 'grid-w-avatars';
	}

	// Equal heights class.
	if ( wpex_blog_entry_equal_heights() ) {
		$classes[] = 'blog-equal-heights';
	}

	// Infinite scroll classes.
	if ( 'infinite_scroll' === wpex_blog_pagination_style() ) {
		$classes[] = 'infinite-scroll-wrap';
	}

	$classes[] = 'wpex-clr';

	// Sanitize.
	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filter the blog wrap element classes.
	 *
	 * @param array|string $classes
	 */
	$classes = apply_filters( 'wpex_blog_wrap_classes', $classes );

	// Turn classes into space seperated string.
	if ( is_array( $classes ) ) {
		$classes = implode( ' ', $classes );
	}

	return (string) $classes;
}


/**
 * Adds main classes to blog post entries
 */
function wpex_blog_wrap_classes( $classes = NULL ) {
	echo wpex_get_blog_wrap_classes();
}

/*-------------------------------------------------------------------------------*/
/* [ Entries ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns blog entry style.
 */
function wpex_blog_entry_style(): string {
	static $style = null;
	if ( null === $style ) {
		if ( wpex_blog_entry_card_style() ) {
			$style = 'wpex_card';
		} else {
			$style = get_theme_mod( 'blog_style' );
			$cat_meta = wpex_get_category_meta( '', 'wpex_term_style' );
			if ( $cat_meta ) {
				$cat_meta_safe = sanitize_html_class( $cat_meta );
				$style = "{$cat_meta_safe}-entry-style";
			}
			if ( ! $style ) {
				$style = 'large-image-entry-style';
			}
			$style = apply_filters( 'wpex_blog_style', $style ); // @deprecated
			$style = (string) apply_filters( 'wpex_blog_entry_style', $style );
		}
	}
	return $style;
}

/**
 * Blog Entry media type.
 */
function wpex_blog_entry_media_type(): string {
	$type = 'thumbnail';
	switch ( get_post_format() ) {
		case 'video':
			if ( ! post_password_required()
				&& get_theme_mod( 'blog_entry_video_output', true )
				&& wpex_has_post_video()
			) {
				$type = 'video';
			}
			break;
		case 'audio':
			if ( apply_filters( 'wpex_blog_entry_audio_embed', get_theme_mod( 'blog_entry_audio_output', false ) )
				&& ! post_password_required()
				&& wpex_has_post_audio()
			) {
				$type = 'audio';
			}
			break;
		case 'gallery':
			if ( wpex_blog_entry_slider_enabled() && wpex_has_post_gallery() ) {
				$type = 'gallery';
			}
			break;
		case 'link':
			if ( (bool) wpex_get_custom_permalink() ) {
				$type = 'link';
			}
			break;
	}

	// Check for thumbnail existense so we don't end up with empty media div
	if ( ( 'thumbnail' === $type || 'link' === $type ) && ! has_post_thumbnail() ) {
		$type = '';
	}

	return (string) apply_filters( 'wpex_blog_entry_media_type', $type );
}

/**
 * Check if blog entry has an avatar enabled.
 */
function wpex_has_blog_entry_avatar(): bool {
	$check = get_theme_mod( 'blog_entry_author_avatar', false );
	return (bool) apply_filters( 'wpex_has_blog_entry_avatar', $check );
}

/**
 * Blog entry divider.
 */
function wpex_blog_entry_divider(): void {
	$divider = '';
	switch ( wpex_blog_entry_style() ) {
		case 'large-image-entry-style':
			$divider = 'wpex-divider wpex-my-40';
			break;
		case 'thumbnail-entry-style':
			$divider = 'wpex-divider wpex-my-30';
			break;
	}
	if ( $divider ) {
		$divider = '<div class="entry-divider ' . esc_attr( $divider ) . '"></div>';
	}
	echo (string) apply_filters( 'wpex_blog_entry_divider', $divider );
}

/**
 * Checks if the blog entries should have equal heights.
 */
function wpex_blog_entry_equal_heights(): bool {
	$check = ( get_theme_mod( 'blog_archive_grid_equal_heights', false ) && 'grid-entry-style' === wpex_blog_entry_style() && 'masonry' !== wpex_blog_grid_style() );
	return (bool) apply_filters( 'wpex_blog_entry_equal_heights', $check );
}

/**
 * Returns columns for the blog entries.
 */
function wpex_blog_entry_columns( $entry_style = '' ) {
	if ( ! $entry_style ) {
		$entry_style = wpex_blog_entry_style();
	}

	if ( ! in_array( $entry_style, [ 'grid-entry-style', 'wpex_card' ], true ) ) {
		return 1; // always 1 unless it's a grid
	}

	// Get columns from customizer setting.
	$columns = get_theme_mod( 'blog_grid_columns', '2' );

	// Category meta check.
	$cat_meta = wpex_get_category_meta( '', 'wpex_term_grid_cols' );
	if ( $cat_meta ) {
		$columns = $cat_meta;
	}

	// Set default columns to 2 if a value isn't set.
	if ( is_array( $columns ) ) {
		if ( empty( $columns['d'] ) ) {
			$columns['d'] = '2';
		}
	} elseif ( ! $columns ) {
		$columns = '2';
	}

	return apply_filters( 'wpex_blog_entry_columns', $columns );
}

/**
 * Blog Entry Class.
 */
function wpex_blog_entry_class(): void {
	$classes = wpex_blog_entry_classes();
	$classes = (array) apply_filters( 'wpex_blog_entry_class', $classes );
	post_class( $classes );
}

/**
 * Returns blog entry classes.
 */
function wpex_blog_entry_classes(): array {
	$entry_style = wpex_blog_entry_style();

	$classes = [
		'blog-entry',
	];

	if ( 'masonry' === wpex_blog_grid_style() ) {
		$classes[] = 'wpex-masonry-col';
	}

	if ( 'grid-entry-style' === $entry_style || 'wpex_card' === $entry_style ) {

		$grid_class = wpex_row_column_width_class( wpex_blog_entry_columns( $entry_style ) );

		if ( $grid_class ) {
			$classes[] = 'col';
			$classes[] = $grid_class;
		}

		$counter = wpex_get_loop_counter();

		if ( $counter ) {
			$classes[] = 'col-' . sanitize_html_class( $counter );
		}

	}

	if ( 'wpex_card' !== $entry_style ) {
		$classes[] = sanitize_html_class( $entry_style );
	}

	if ( $avatar_enabled = get_theme_mod( 'blog_entry_author_avatar' ) ) {
		$classes[] = 'entry-has-avatar';
	}

	$classes[] = 'wpex-relative';
	$classes[] = 'wpex-clr';

	$classes = array_map( 'esc_attr', $classes );

	return (array) apply_filters( 'wpex_blog_entry_classes', $classes );
}

/**
 * Blog Entry Inner Class.
 */
function wpex_blog_entry_inner_class(): void {
	$classes = [
		'blog-entry-inner',
		'entry-inner',
		'wpex-last-mb-0',
	];
	switch ( wpex_blog_entry_style() ) {
		case 'grid-entry-style':
			$classes[] = 'wpex-px-20';
			$classes[] = 'wpex-pb-20';
			$classes[] = 'wpex-border';
			$classes[] = 'wpex-border-solid';
			$classes[] = 'wpex-border-main';
			if ( wpex_blog_entry_equal_heights() ) {
				$classes[] = 'wpex-flex-grow';
			}
			break;
		case 'thumbnail-entry-style':
			$classes[] = 'wpex-md-flex';
			$classes[] = 'wpex-md-flex-wrap';
			$classes[] = 'wpex-md-justify-between';
			break;
	}
	$classes = (array) apply_filters( 'wpex_blog_entry_inner_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Blog Entry Quote Class.
 */
function wpex_blog_entry_quote_class(): void {
	$classes = [
		'post-quote-entry-inner',
		'wpex-my-0',
	];
	$classes = (array) apply_filters( 'wpex_blog_entry_quote_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Blog Entry Media Class.
 */
function wpex_blog_entry_media_class(): void {
	$entry_style = wpex_blog_entry_style();

	$classes = [
		'blog-entry-media',
		'entry-media',
		'wpex-relative',
	];

	if ( 'thumbnail-entry-style' === $entry_style ) {
		$classes[] = 'wpex-mb-20 wpex-md-mb-0';
	} else {
		$classes[] = 'wpex-mb-20';
	}

	if ( 'grid-entry-style' === $entry_style ) {
		$classes[] = '-wpex-mx-20';
	}

	$media_type = wpex_blog_entry_media_type();

	if ( 'thumbnail' === $media_type || 'link' === $media_type ) {
		$overlay = totaltheme_call_static(
			'Overlays',
			'get_entry_image_overlay_style',
			'post'
		);
		if ( $overlay ) {
			$overlay_class = (string) totaltheme_call_static(
				'Overlays',
				'get_parent_class',
				(string) $overlay
			);
			if ( $overlay_class ) {
				$classes[] = $overlay_class;
			}
		}
		if ( $animation_classes = wpex_get_entry_image_animation_classes() ) {
			$classes[] = $animation_classes;
		}
	}

	$classes = (array) apply_filters( 'wpex_blog_entry_media_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( trim( implode( ' ', array_unique( $classes ) ) ) ) . '"';
	}
}

/**
 * Blog Entry Header Class.
 */
function wpex_blog_entry_header_class(): void {
	$classes = [
		'blog-entry-header',
		'entry-header',
	];

	if ( wpex_has_blog_entry_avatar() ) {
		$classes[] = 'wpex-flex';
		$classes[] = 'wpex-items-center';
	}

	switch ( wpex_blog_entry_style() ) {
		case 'grid-entry-style':
			if ( ! wpex_post_has_media() ) {
				$classes[] = 'wpex-mt-20'; // prevent issues when there isn't any media
			}
			$classes[] = 'wpex-mb-10';
			break;

		default:
			$classes[] = totaltheme_has_classic_styles() ? 'wpex-mb-10' : 'wpex-mb-15';
			break;
	}

	$classes = (array) apply_filters( 'wpex_blog_entry_header_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Blog Entry Title Class.
 */
function wpex_blog_entry_title_class(): void {
	$classes = [
		'blog-entry-title',
		'entry-title',
		'wpex-m-0',
	];
	if ( wpex_has_blog_entry_avatar() ) {
		$classes[] = 'wpex-flex-grow';
	}
	switch ( wpex_blog_entry_style() ) {
		case 'grid-entry-style':
			if ( totaltheme_has_classic_styles() ) {
				$classes[] ='wpex-text-lg';
			}
			break;
		case 'thumbnail-entry-style':
			$classes[] = 'wpex-text-2xl';
			break;
		default:
			$classes[] = 'wpex-text-3xl';
			break;
	}
	$classes = (array) apply_filters( 'wpex_blog_entry_title_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Blog Entry Avatar Class.
 */
function wpex_blog_entry_avatar_class(): void {
	$classes = [
		'blog-entry-author-avatar',
		'wpex-flex-shrink-0',
		'wpex-mr-20',
	];
	$classes = (array) apply_filters( 'wpex_blog_entry_avatar_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Blog Entry Content Class | used for left/right layouts.
 */
function wpex_blog_entry_content_class(): void {
	$classes = [
		'blog-entry-content',
		'entry-details',
		'wpex-last-mb-0',
		'wpex-clr',
	];
	$classes = (array) apply_filters( 'wpex_blog_entry_content_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Blog Entry Excerpt Class.
 */
function wpex_blog_entry_excerpt_class(): void {
	$classes = [
		'blog-entry-excerpt',
		'entry-excerpt',
		'wpex-text-pretty',
	];

	switch ( wpex_blog_entry_style() ) {
		case 'grid-entry-style':
			$classes[] = 'wpex-my-15';
			break;
		default:
			$classes[] = 'wpex-my-20';
			break;
	}

	$classes[] = 'wpex-last-mb-0';
	$classes[] = 'wpex-clr';

	$classes = (array) apply_filters( 'wpex_blog_entry_excerpt_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Blog Entry Button Wrap Class.
 */
function wpex_blog_entry_button_wrap_class(): void {
	$classes = [
		'blog-entry-readmore',
		'entry-readmore-wrap',
	];

	switch ( wpex_blog_entry_style() ) {
		case 'grid-entry-style':
			$classes[] = 'wpex-my-15';
			break;
		default:
			$classes[] = 'wpex-my-20';
			break;
	}

	$classes[] = 'wpex-clr';

	$classes = (array) apply_filters( 'wpex_blog_entry_button_wrap_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Blog Entry Button Class.
 */
function wpex_blog_entry_button_class(): void {
	$args = [
		'style' => '',
		'color' => '',
	];

	$args = (array) apply_filters( 'wpex_blog_entry_button_args', $args );

	$button_class = wpex_get_button_classes( $args );

	if ( is_array( $button_class ) ) {
		$classes = $button_class;
	} else {
		$classes = explode( ' ', $button_class );
	}

	$classes = (array) apply_filters( 'wpex_blog_entry_button_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Returns the blog entry thumbnail.
 */
function wpex_blog_entry_thumbnail( $args = '' ): void {
	echo wpex_get_blog_entry_thumbnail( $args );
}

/**
 * Returns the blog entry thumbnail args.
 */
function wpex_get_blog_entry_thumbnail_args( $args = '' ): array {
	if ( $args && ! is_array( $args ) ) {
		$args = [
			'attachment' => $args,
		];
	}

	// Define thumbnail args
	$defaults = [
		'attachment'    => get_post_thumbnail_id(),
		'size'          => 'blog_entry',
		'class'         => 'blog-entry-media-img wpex-align-middle',
		'apply_filters' => 'wpex_blog_entry_thumbnail_args',
	];

	// Parse arguments
	$args = wp_parse_args( $args, $defaults );

	// Check category image width meta
	$cat_meta_image_width = wpex_get_category_meta( '', 'wpex_term_image_width' );
	if ( $cat_meta_image_width ) {
		$args['size']  = 'wpex_custom';
		$args['width'] = $cat_meta_image_width;
	}

	// Check category image height meta
	$cat_meta_image_height = wpex_get_category_meta( '', 'wpex_term_image_height' );
	if ( $cat_meta_image_height ) {
		$args['size']  = 'wpex_custom';
		$args['height'] = $cat_meta_image_height;
	}

	return $args;
}

/**
 * Returns the blog entry thumbnail.
 */
function wpex_get_blog_entry_thumbnail( $args = '' ): string {
	$thumbnail = wpex_get_post_thumbnail( wpex_get_blog_entry_thumbnail_args( $args ) );
	return (string) apply_filters( 'wpex_blog_entry_thumbnail', $thumbnail );
}

/**
 * Check if the blog slider is disabled.
 */
function wpex_blog_entry_slider_enabled(): bool {
	if ( apply_filters( 'wpex_disable_entry_slider', false ) || post_password_required() ) {
		$check = false;
	} else {
		$check = get_theme_mod( 'blog_entry_gallery_output', true );
	}
	return $check;
}

/*-------------------------------------------------------------------------------*/
/* [ Single ]
/*-------------------------------------------------------------------------------*/

/**
 * Blog Single media type.
 */
function wpex_blog_single_media_type(): string {
	$type = '';

	switch ( get_post_format() ) {
		case 'video':
			if ( ! post_password_required()
				&& wpex_has_post_video()
			) {
				$type = 'video';
			} else {
				$type = 'thumbnail';
			}
			break;
		case 'audio':
			if ( ! post_password_required() && wpex_has_post_audio() ) {
				$type = 'audio';
			} else {
				$type = 'thumbnail';
			}
			break;
		case 'gallery':
			$type = ( wpex_has_post_gallery() ) ? 'gallery' : 'thumbnail';
			break;
		case 'link':
			$type = 'link';
			break;
		default:
			$type = 'thumbnail';
			break;
	}

	// Check for thumbnail existense so we don't end up with empty media div
	if ( ( 'thumbnail' === $type || 'link' === $type ) && ! has_post_thumbnail() ) {
		$type = '';
	}

	return (string) apply_filters( 'wpex_blog_single_media_type', $type );
}

/**
 * Blog Single lightbox check.
 */
function wpex_has_blog_single_thumbnail_lightbox(): bool {
	$check = get_theme_mod( 'blog_post_image_lightbox', false );
	return (bool) apply_filters( 'wpex_has_blog_single_thumbnail_lightbox', $check );
}

/**
 * Blog Single caption.
 */
function wpex_blog_single_thumbnail_caption(): void {
	if ( ! get_theme_mod( 'blog_thumbnail_caption' ) ) {
		return;
	}

	$caption     = wpex_featured_image_caption();
	$has_caption = (bool) $caption;

	if ( ! $has_caption ) {
		return;
	}

	$classes = [
		'post-media-caption',
		'wpex-absolute',
		'wpex-inset-x-0',
		'wpex-bottom-0',
		'wpex-p-15',
		'wpex-text-white',
		'wpex-child-inherit-color',
		'wpex-text-sm',
		'wpex-text-center',
		'wpex-last-mb-0',
		'wpex-clr',
	];

	$classes = (array) apply_filters( 'wpex_blog_single_thumbnail_caption_class', $classes );

	?>

	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"><?php echo wp_kses_post( $caption ); ?></div>

<?php }

/**
 * Returns the blog entry thumbnail args.
 *
 * @todo rename to wpex_get_blog_single_thumbnail_args?
 */
function wpex_get_blog_post_thumbnail_args( $args = '' ): array {
	if ( ! is_array( $args ) && ! empty( $args ) ) {
		$args = [
			'attachment' => $args,
		];
	}

	$defaults = [
		'size'          => 'blog_post',
		'class'         => 'blog-single-media-img wpex-align-middle',
		'apply_filters' => 'wpex_blog_post_thumbnail_args',
	];

	$args = wp_parse_args( $args, $defaults );

	if ( 'above' === wpex_get_custom_post_media_position() ) {
		$args['size'] = 'blog_post_full';
	}

	return $args;
}

/**
 * Displays the blog post thumbnail.
 */
function wpex_blog_post_thumbnail( $args = '' ): void {
	echo wpex_get_blog_post_thumbnail( $args );
}

/**
 * Returns the blog post thumbnail.
 */
function wpex_get_blog_post_thumbnail( $args = '' ): string {
	$thumbnail = '';
	$supported = ( 'audio' === get_post_format() ) ? false : true;
	if ( apply_filters( 'wpex_blog_post_supports_thumbnail', $supported ) ) {
		$thumbnail_args = wpex_get_blog_post_thumbnail_args( $args );
		$thumbnail      = wpex_get_post_thumbnail( $thumbnail_args );
		if ( shortcode_exists( 'featured_revslider' ) ) {
			$thumbnail = do_shortcode( "[featured_revslider]{$thumbnail}[/featured_revslider]" );
		}
		$thumbnail = apply_filters( 'wpex_blog_post_thumbnail', $thumbnail );
	}
	return (string) $thumbnail;
}

/**
 * Blog Single Content Class.
 */
function wpex_blog_single_content_class(): void {
	$classes = [
		'single-blog-content',
		'single-content',
		'entry',
	];
	if ( totaltheme_has_classic_styles() ) {
		$classes[] = 'wpex-mt-20';
		$classes[] = 'wpex-mb-40';
	} else {
		$classes[] = 'wpex-my-40';
	}
	$classes[] = 'wpex-clr';
	$classes = (array) apply_filters( 'wpex_blog_single_content_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}

/**
 * Blog Single Header Class.
 */
function wpex_blog_single_header_class(): void {
	$classes = [
		'single-blog-header',
		totaltheme_has_classic_styles() ? 'wpex-mb-10' : 'wpex-mb-15',
	];
	$classes = (array) apply_filters( 'wpex_blog_single_header_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Blog Single Title Class.
 */
function wpex_blog_single_title_class(): void {
	$classes = [
		'single-post-title',
		'entry-title',
		'wpex-m-0',
		'wpex-text-3xl',
	];
	$classes = (array) apply_filters( 'wpex_blog_single_title_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Blog single media class.
 */
function wpex_blog_single_media_class(): void {
	$classes = [
		'single-blog-media',
		'single-media',
		'wpex-relative',
		'wpex-mb-20',
	];

	if ( 'above' === wpex_get_custom_post_media_position() ) {
		$classes[] = 'wpex-md-mb-30';
		$classes[] = 'wpex-w-100';
		$classes[] = 'wpex-shrink-0';
	}

	$classes = (array) apply_filters( 'wpex_blog_single_media_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Related ]
/*-------------------------------------------------------------------------------*/

/**
 * Blog single related query.
 */
function wpex_blog_single_related_query() {
	$post_id = get_the_ID();

	if ( wpex_validate_boolean( get_post_meta( $post_id, 'wpex_disable_related_items', true ) ) ) {
		return false;
	}

	$posts_count = absint( get_theme_mod( 'blog_related_count', 3 ) );

	if ( empty( $posts_count ) ) {
		return false;
	}

	$args = [
		'posts_per_page'      => $posts_count,
		'orderby'             => 'date',
		'order'               => 'desc',
		'post__not_in'        => [ $post_id ],
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'tax_query'           => [
			'relation'  => 'AND',
			[
				'taxonomy' => 'post_format',
				'field'    => 'slug',
				'terms'    => [ 'post-format-quote', 'post-format-link' ],
				'operator' => 'NOT IN',
			],
		],
	];

	// Add custom orderby param.
	$orderby = get_theme_mod( 'blog_related_orderby' );
	if ( $orderby && $orderby_safe = sanitize_sql_orderby( (string) $orderby ) ) {
		$args['orderby'] = $orderby_safe;
	}

	// Add custom order param.
	$order = get_theme_mod( 'blog_related_order' );
	if ( $order && in_array( strtolower( $order ), [ 'asc', 'desc' ] ) ) {
		$args['order'] = $order;
	}

	// Related by taxonomy.
	if ( apply_filters( 'wpex_related_in_same_cat', true ) ) {

		// Add categories to query
		$related_taxonomy = get_theme_mod( 'blog_related_taxonomy', 'category' );

		// Generate related by taxonomy args
		if ( 'null' !== $related_taxonomy && taxonomy_exists( $related_taxonomy ) ) {
			$terms        = '';
			$primary_term = totaltheme_get_post_primary_term( $post_id, $related_taxonomy, false );

			if ( $primary_term ) {
				$terms = [ $primary_term->term_id ];
			} else {
				$get_terms = get_the_terms( $post_id, $related_taxonomy );
				if ( $get_terms && ! is_wp_error( $get_terms ) ) {
					$terms = wp_list_pluck( $get_terms, 'term_id' );
				}
			}

			if ( $terms ) {
				$args['tax_query'][] = [
					'taxonomy' => $related_taxonomy,
					'field'    => 'term_id',
					'terms'    => $terms,
				];
			}
		}

	}

	// If content is disabled make sure items have featured images.
	if ( ! get_theme_mod( 'blog_related_excerpt', true ) ) {
		$args['meta_key'] = '_thumbnail_id';
	}

	/**
	 * Filters the blog post related query args.
	 *
	 * @param array $args
	 * @todo deprecate
	 */
	$args = (array) apply_filters( 'wpex_blog_post_related_query_args', $args );

	if ( $args ) {
		return new wp_query( $args );
	}
}

/**
 * Gets correct heading for the related blog items
 */
function wpex_blog_related_heading(): string {
	$heading = wpex_get_translated_theme_mod( 'blog_related_title' );
	if ( ! $heading ) {
		$heading = esc_html__( 'Related Posts', 'total' );
	}
	return (string) $heading;
}

/**
 * Blog single related class.
 */
function wpex_blog_single_related_class(): void {
	$classes = [
		'related-posts',
		'wpex-overflow-hidden', // for the negative margins on the row
		'wpex-mb-40',
	];

	if ( 'full-screen' === wpex_content_area_layout() ) {
		$classes[] = 'container';
	}

	$classes[] = 'wpex-clr';

	$classes = (array) apply_filters( 'wpex_blog_single_related_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Blog single related row class.
 */
function wpex_blog_single_related_row_class(): void {
	$classes = [
		'wpex-row',
		'wpex-clr',
	];

	if ( $gap = get_theme_mod( 'blog_related_gap' ) ) {
		$classes[] = wpex_gap_class( $gap );
	}

	$classes = (array) apply_filters( 'wpex_blog_single_related_row_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Blog single related entry class.
 */
function wpex_blog_single_related_entry_class(): void {
	$classes = [
		'related-post',
		'col',
	];

	$columns = wpex_blog_single_related_columns();

	if ( $columns ) {
		$classes[] = wpex_row_column_width_class( $columns );
	}

	$counter = wpex_get_loop_counter();

	if ( $counter ) {
		$classes[] = sanitize_html_class( "col-{$counter}" );
	}

	$classes[] = 'wpex-clr';

	$classes = (array) apply_filters( 'wpex_blog_single_related_entry_class', $classes );

	post_class( $classes );
}

/**
 * Returns columns for the blog single related entries.
 *
 * @note this may return a string, int or array.
 */
function wpex_blog_single_related_columns() {
	$columns = get_theme_mod( 'blog_related_columns' ) ?: 3;
	return apply_filters( 'wpex_related_blog_posts_columns', $columns );
}

/*-------------------------------------------------------------------------------*/
/* [ Slider ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns data attributes for the blog gallery slider.
 */
function wpex_blog_slider_data_atrributes(): void {
	echo wpex_get_slider_data( [
		'filter_tag' => 'wpex_blog_slider_data_atrributes',
	] );
}

/*-------------------------------------------------------------------------------*/
/* [ Cards ]
/*-------------------------------------------------------------------------------*/

/**
 * Blog entry card style.
 */
function wpex_blog_entry_card_style(): string {
	static $style = null;
	if ( null === $style ) {
		$style = get_query_var( 'card_style' ); // @todo should be wpex_card_style
		if ( empty( $style ) ) {
			if ( 'related' === wpex_get_loop_instance() ) {
				$style = get_theme_mod( 'blog_related_entry_card_style' );
			} else {
				$style = wpex_get_term_meta( '', 'wpex_entry_card_style', true ) ?: get_theme_mod( 'blog_entry_card_style' );
			}
		}
		$style = (string) apply_filters( 'wpex_blog_entry_card_style', $style );
	}
	return $style;
}

/**
 * Blog entry card.
 */
function wpex_blog_entry_card(): bool {
	$card_style = wpex_blog_entry_card_style();

	if ( ! $card_style ) {
		return false;
	}

	if ( 'related' === wpex_get_loop_instance() ) {
		$thumbnail_size = 'blog_related';
		$excerpt_length = get_theme_mod( 'blog_related_excerpt_length', '15' );
	} else {
		$thumbnail_size = 'blog_entry';
		$excerpt_length = wpex_excerpt_length();

		// Check category image size meta options
		$cat_meta_image_width = wpex_get_category_meta( '', 'wpex_term_image_width' );
		$cat_meta_image_height = wpex_get_category_meta( '', 'wpex_term_image_height' );

		if ( $cat_meta_image_width || $cat_meta_image_height ) {
			$thumbnail_size = [ $cat_meta_image_width, $cat_meta_image_height ];
		}

	}

	$args = [
		'style'          => $card_style,
		'post_id'        => get_the_ID(),
		'thumbnail_size' => $thumbnail_size,
		'excerpt_length' => $excerpt_length,
	];

	$overlay = totaltheme_call_static(
		'Overlays',
		'get_entry_image_overlay_style',
		'post'
	);

	if ( $overlay ) {
		$args['thumbnail_overlay_style'] = $overlay;
	}

	if ( $hover_style = get_theme_mod( 'blog_entry_image_hover_animation', null ) ) {
		$args['thumbnail_hover'] = $hover_style;
	}

	if ( $more_link_text = wpex_get_translated_theme_mod( 'blog_entry_readmore_text' ) ) {
		$args['more_link_text'] = $more_link_text;
	}

	$args = (array) apply_filters( 'wpex_blog_entry_card_args', $args, $card_style );

	wpex_card( $args );

	return true;
}

/*-------------------------------------------------------------------------------*/
/* [ Deprecated ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns post video URL.
 */
function wpex_post_video_url( $post_id = '' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Oembed video.
	if ( $meta = get_post_meta( $post_id, 'wpex_post_oembed', true ) ) {
		return esc_url( $meta );
	}

	// Self Hosted redux video.
	$video = get_post_meta( $post_id, 'wpex_post_self_hosted_shortcode_redux', true );
	if ( is_array( $video ) && ! empty( $video['url'] ) ) {
		return $video['url'];
	}

	// Self Hosted old - Thunder theme compatibility.
	if ( $meta = get_post_meta( $post_id, 'wpex_post_self_hosted_shortcode', true ) ) {
		return $meta;
	}
}

/**
 * Returns post audio URL.
 */
function wpex_post_audio_url( $post_id = '' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Oembed audio url.
	if ( $meta = get_post_meta( $post_id, 'wpex_post_oembed', true ) ) {
		return $meta;
	}

	// Self Hosted redux audio url.
	$audio = get_post_meta( $post_id, 'wpex_post_self_hosted_shortcode_redux', true );
	if ( is_array( $audio ) && ! empty( $audio['url'] ) ) {
		return $audio['url'];
	}

	// Self Hosted old - Thunder theme compatibility.
	if ( $meta = get_post_meta( $post_id, 'wpex_post_self_hosted_shortcode', true ) ) {
		return $meta;
	}
}