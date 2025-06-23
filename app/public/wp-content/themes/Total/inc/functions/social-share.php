<?php

defined( 'ABSPATH' ) || exit;

/**
 * Checks if social share is enabled.
 */
function wpex_has_social_share(): bool {
	if ( is_home() || is_archive() || post_password_required() ) {
		return false;
	}

	// Disabled by default.
	$check = false;

	// Check page settings to overrides theme mods and filters.
	if ( $post_id = wpex_get_current_post_id() ) {

		// Meta check.
		if ( $meta = get_post_meta( $post_id, 'wpex_disable_social', true ) ) {
			if ( 'on' === $meta ) {
				return false;
			} elseif ( 'enable' === $meta ) {
				return true;
			}
		}

		// Dynamic template check.
		if ( totaltheme_location_has_template( 'single' ) ) {
			return true; // so that the post content module works correctly - @todo update so this isn't necessary.
		}

		// Remove on woo cart/checkout pages.
		if ( ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout') && is_checkout() ) ) {
			return false;
		}

		// Check if social share is enabled for specific post types.
		if ( 'product' === get_post_type() ) {
			$check = wp_validate_boolean( get_theme_mod( 'social_share_woo', false ) );
		} else {
			$blocks = wpex_single_blocks();
			// @note can't use array_intersect() cause it will cause errors with callable values.
			if ( $blocks && is_array( $blocks ) && ( in_array( 'social_share', $blocks, true ) || in_array( 'share', $blocks, true ) ) ) {
				$check = true;
			}
		}

	}

	return (bool) apply_filters( 'wpex_has_social_share', $check );
}

/**
 * Checks if there are any social sharing sites enabled.
 */
function wpex_has_social_share_sites(): bool {
	return (bool) wpex_social_share_sites();
}

/**
 * Returns social sharing sites.
 */
function wpex_social_share_sites() {
	$sites = get_theme_mod( 'social_share_sites', [ 'twitter', 'facebook', 'linkedin', 'email' ] );
	$sites = apply_filters( 'wpex_social_share_sites', $sites );
	if ( $sites && is_string( $sites ) ) {
		$sites = explode( ',', $sites );
	}
	return $sites;
}

/**
 * Parses Social share arguments.
 */
function wpex_parse_social_share_args( $args = [] ): array {
	$defaults = [
		'align'         => get_theme_mod( 'social_share_align' ) ?: '',
		'style'         => wpex_social_share_style(),
		'position'      => wpex_social_share_position(),
		'has_labels'    => wpex_social_share_has_labels(),
		'stretch_items' => wp_validate_boolean( get_theme_mod( 'social_share_stretch_items', false ) ),
	];

	if ( 'custom' === $defaults['style'] ) {
		$defaults['link_border_radius'] = get_theme_mod( 'social_share_link_border_radius' );
	}

	if ( ! array_key_exists( 'contain', $args )
		&& 'horizontal' === $defaults['position']
		&& 'full-screen' === wpex_content_area_layout() ) {
		$defaults['contain'] = true; // contain the social share on full-screen layouts.
	}

	$args = wp_parse_args( $args, $defaults );

	if ( 'vertical' === $args['position'] ) {
		$args['has_labels'] = false; // remove labels on vertical share style.
	}

	// Magazine style tweaks.
	if ( 'mag' === $defaults['style'] ) {
		$args['has_labels'] = true;
		$args['position'] = 'horizontal';
	}

	return (array) apply_filters( 'wpex_social_share_args', $args );
}

/**
 * Returns correct social share position.
 */
function wpex_social_share_position(): string {
	$position = ( $position = get_theme_mod( 'social_share_position' ) ) ? \sanitize_text_field( $position ) : '';
	if ( ! $position || 'mag' == wpex_social_share_style() ) {
		$position = 'horizontal';
	}
	return (string) apply_filters( 'wpex_social_share_position', $position );
}

/**
 * Returns correct social share style.
 */
function wpex_social_share_style(): string {
	$style = get_theme_mod( 'social_share_style' );
	if ( function_exists( 'is_product' ) && is_product() ) {
		$woo_style = get_theme_mod( 'woo_product_social_share_style', $style );
		if ( $woo_style ) {
			$style = $woo_style;
		}
	}
	if ( ! $style || ! is_string( $style ) ) {
		$style = 'flat'; // style can't be empty.
	}
	return (string) apply_filters( 'wpex_social_share_style', $style );
}

/**
 * Check if social share labels should display.
 */
function wpex_social_share_has_labels(): bool {
	$check = get_theme_mod( 'social_share_label', true );
	if ( function_exists( 'is_product' ) && is_product() ) {
		$check = get_theme_mod( 'woo_social_share_label', true );
	}
	return (bool) apply_filters( 'wpex_social_share_has_labels', $check );
}

/**
 * Checks if we are using custom social share.
 */
function wpex_has_custom_social_share(): bool {
	return (bool) wpex_custom_social_share();
}

/**
 * Checks if we are using custom social share.
 */
function wpex_custom_social_share() {
	$custom_share = get_theme_mod( 'social_share_shortcode' );
	return apply_filters( 'wpex_custom_social_share', $custom_share );
}

/*-------------------------------------------------------------------------------*/
/* [ Classes ]
/*-------------------------------------------------------------------------------*/

/**
 * Social share class.
 */
function wpex_social_share_class( $args = [] ) {
	$classes = [];

	if ( empty( $args ) && wpex_has_custom_social_share() ) {

		$classes = [
			'wpex-custom-social-share',
			'wpex-mb-40',
			'wpex-clr',
		];

		if ( 'full-screen' === wpex_content_area_layout() ) {
			$classes[] = 'container';
		}

	} else {

		$args         = wpex_parse_social_share_args( $args );
		$style        = ! empty( $args['style'] ) ? $args['style'] : '';
		$position     = ! empty( $args['position'] ) ? $args['position'] : '';
		$has_labels   = ! empty( $args['has_labels'] );
		$strech_items = ! empty( $args['stretch_items'] );
		$instance     = $args['instance'] ?? '';

		$classes = [
			'class' => 'wpex-social-share',
		];

		if ( $style ) {
			$style_class = sanitize_html_class( $style );
			$classes[] = "style-{$style_class}";
		}

		if ( $position ) {
			$classes[] = sanitize_html_class( "position-{$position}" );
		}

		// Vertical only classes.
		if ( 'vertical' === $position ) {
			if ( totaltheme_call_static( 'Header\Vertical', 'is_enabled' ) ) {
				$position = ( 'left' === totaltheme_call_static( 'Header\Vertical', 'position' ) ) ? 'right' : 'left';
			} else {
				$position = 'left';
			}
			$classes[] = sanitize_html_class( "on-{$position}" );
			if ( 'rounded' === $style || 'mag' === $style ) {
				$classes[] = 'has-side-margin';
			}
			$classes[] = 'wpex-fixed';
			$classes[] = 'wpex-z-sticky';
			$classes[] = 'wpex-w-100';
			$classes[] = 'wpex-lg-w-auto';
			$classes[] = 'wpex-lg-top-50';
			$classes[] = 'wpex-bottom-0';
			$classes[] = 'wpex-lg-bottom-auto';
			$classes[] = '-wpex-lg-translate-y-50';
			$classes[] = sanitize_html_class( "wpex-{$position}-0" );
			if ( 'rounded' === $style || 'mag' === $style ) {
				$margin_dir = ( 'right' === $position ) ? 'r' : 'l';
				$classes[] = "wpex-lg-m{$margin_dir}-10";
			}
		}
		// Horizontal classes.
		elseif ( 'horizontal' === $position ) {
			$classes[] = 'wpex-mx-auto';
			if ( 'vcex_social_share' !== $instance ) {
				$classes[] = 'wpex-mb-40';
			}
		}

		if ( ! $has_labels ) {
			$classes[] = 'disable-labels';
		}

		if ( $strech_items && 'horizontal' === $position ) {
			$classes[] = 'wpex-social-share--stretched';
		}

		if ( ! empty( $args['contain'] ) ) {
			$classes[] = 'container';
		}

	}

	$classes[] = 'wpex-print-hidden';
	$classes = (array) apply_filters( 'wpex_social_share_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Heading ]
/*-------------------------------------------------------------------------------*/

/**
 * Checks if the social sharing style supports a custom heading.
 */
function wpex_has_social_share_heading(): bool {
	$check = ( 'horizontal' === wpex_social_share_position() );
	return (bool) apply_filters( 'wpex_has_social_share_heading', $check );
}

/**
 * Returns the social share heading.
 */
function wpex_social_share_heading() {
	if ( ! wpex_has_social_share_heading() ) {
		return;
	}

	$heading = wpex_get_translated_theme_mod( 'social_share_heading', esc_html__( 'Share This', 'total' ) );

	if ( function_exists( 'is_product' ) && is_product() ) {
		$heading = wpex_get_translated_theme_mod( 'woo_product_social_share_heading', $heading );
	}

	$heading = apply_filters( 'wpex_social_share_heading', $heading );

	if ( $heading ) {

		$heading_args = [
			'tag'           => get_theme_mod( 'social_share_heading_tag' ) ?: 'h3',
			'content'		=> $heading,
			'classes'		=> ['social-share-title'],
			'apply_filters'	=> 'social_share',
		];

		if ( function_exists( 'is_product' ) && is_product() ) {
			$heading_args['style'] = 'plain';
		}

		wpex_heading( $heading_args );

	}
}

/*-------------------------------------------------------------------------------*/
/* [ Data ]
/*-------------------------------------------------------------------------------*/

/**
 * Output social share data.
 */
function wpex_social_share_data( $post_id = 0, $sites = [] ) {
	$data = wpex_get_social_share_data( $post_id, $sites );
	if ( ! empty( $data ) && is_array( $data ) ) {
		$html = '';
		foreach ( $data as $k => $v ) {
			$html .=' data-' . esc_attr( $k ) .'="' . esc_attr( $v ) . '"';
		}
		echo trim( $html );
	}
}

/**
 * Return social share data.
 */
function wpex_get_social_share_data( $post_id = 0, $sites = [] ) {
	if ( ! $post_id ) {
		$post_id = wpex_get_current_post_id();
	}

	if ( ! $sites ) {
		$sites = wpex_social_share_sites();
	}

	if ( $post_id ) {
		$url = get_permalink( $post_id );
	} else {
		$url = wpex_get_current_url();
	}

	$url = (string) apply_filters( 'wpex_social_share_url', $url );

	$data = [
		'target' => '_blank',
	];

	// Post Data.
	if ( $post_id ) {
		$title = the_title_attribute( [
			'echo' => false,
			'post' => get_post( $post_id ),
		] );
		if ( in_array( 'pinterest', $sites ) || in_array( 'linkedin', $sites ) ) {
			$summary = totaltheme_get_post_excerpt( apply_filters( 'wpex_social_share_excerpt_args', [
				'post_id' => $post_id,
				'length'  => 30,
				'more'    => '',
			] ) );
		}
	}

	// Most likely an archive.
	else {
		$title   = get_the_archive_title();
		$summary = get_the_archive_description();
	}

	// Source.
	$source = apply_filters( 'wpex_social_share_data_source', home_url( '/' ) );
	$data['source'] = rawurlencode( esc_url( $source ) );

	// URL.
	$url = apply_filters( 'wpex_social_share_data_url', $url );
	$data['url'] = rawurlencode( esc_url( $url ) );

	// Title.
	$title = apply_filters( 'wpex_social_share_data_title', $title );
	$data['title'] = html_entity_decode( wp_strip_all_tags( $title ) );

	// Thumbnail.
	if ( is_singular() && has_post_thumbnail() ) {
		$image = apply_filters( 'wpex_social_share_data_image', wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ) );
		$data['image'] = rawurlencode( esc_url( $image ) );
	}

	// Add twitter handle.
	if ( $handle = get_theme_mod( 'social_share_twitter_handle' ) ) {
		$data['twitter-handle'] = esc_attr( $handle );
	}

	// Share summary.
	if ( ! empty( $summary ) ) {
		$summary = apply_filters( 'wpex_social_share_data_summary', wp_strip_all_tags( strip_shortcodes( $summary ) ) );
		$data['summary'] = rawurlencode( html_entity_decode( $summary ) );
	}

	// Get WordPress SEO meta share values.
	if ( class_exists( 'WPSEO_Meta' ) && method_exists( 'WPSEO_Meta', 'get_value' ) ) {
		$twitter_title = WPSEO_Meta::get_value( 'twitter-title', $post_id );
		if ( ! empty( $twitter_title ) ) {
			if ( class_exists( 'WPSEO_Replace_Vars' ) ) {
				$replace_vars = new WPSEO_Replace_Vars();
				$twitter_title = $replace_vars->replace( $twitter_title, get_post() );
			}
			$data['twitter-title'] = rawurlencode( html_entity_decode( wp_strip_all_tags( $twitter_title ) ) );
		}
		$twitter_desc =  WPSEO_Meta::get_value( 'twitter-description', $post_id );
		if ( ! empty( $twitter_desc ) ) {
			if ( class_exists( 'WPSEO_Replace_Vars' ) ) {
				$replace_vars = new WPSEO_Replace_Vars();
				$twitter_desc = $replace_vars->replace( $twitter_desc, get_post() );
			}
			if ( $twitter_title ) {
				$data['twitter-title'] = rawurlencode( html_entity_decode( wp_strip_all_tags( $twitter_title . ': ' . $twitter_desc ) ) );
			} else {
				$data['twitter-title'] = rawurlencode( html_entity_decode( wp_strip_all_tags( $data['title'] . ': ' . $twitter_desc ) ) );
			}
		}
	}

	// Email data.
	if ( in_array( 'email', $sites ) ) {
		$data['email-subject'] = apply_filters( 'wpex_social_share_data_email_subject', esc_html__( 'I wanted you to see this link', 'total' ) );
		$body = esc_html__( 'I wanted you to see this link', 'total' ) . ' '. rawurlencode( esc_url( $url ) );
		$data['email-body'] = apply_filters( 'wpex_social_share_data_email_body', $body );
	}

	// Specs.
//	$data['specs'] = 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600';

	return (array) apply_filters( 'wpex_get_social_share_data', $data );
}

/*-------------------------------------------------------------------------------*/
/* [ Items List ]
/*-------------------------------------------------------------------------------*/

/**
 * Output social share list.
 */
function wpex_social_share_list( $args = [], $sites = [] ) {
	if ( ! $sites ) {
		$sites = wpex_social_share_sites();
	}

	$items = wpex_social_share_items();

	if ( empty( $sites ) || empty( $items ) ) {
		return;
	}

	wp_enqueue_script( 'wpex-social-share' );

	$args         = wpex_parse_social_share_args( $args );
	$style        = $args['style'];
	$position     = $args['position'] ?? '';
	$strech_items = ! empty( $args['stretch_items'] );
	$has_labels   = ! empty( $args['has_labels'] ) || 'mag' === $style;
	$sq_links     = ( ! $has_labels || 'vertical' === $position );

	// Define initial classes.
	$list_class  = [ 'wpex-social-share__list', 'wpex-m-0', 'wpex-p-0', 'wpex-list-none' ];
	$item_class  = [ 'wpex-social-share__item', 'wpex-m-0', 'wpex-p-0' ];
	$icon_class  = [ 'wpex-social-share__icon' ];
	$label_class = [ 'wpex-social-share__label', 'wpex-label' ];

	// List align.
	if ( ! empty( $args['align'] ) ) {
		$align_classes = [
			'left'   => 'wpex-justify-start',
			'start'  => 'wpex-justify-start',
			'center' => 'wpex-justify-center',
			'right'  => 'wpex-justify-end',
			'end'    => 'wpex-justify-end',
		];
		if ( isset( $align_classes[ $args['align'] ] ) ) {
			$list_class[] = "{$align_classes[$args['align']]}";
		}
	}

	// Stretched classes.
	if ( $strech_items ) {
		$item_class[] = 'wpex-flex-grow wpex-w-auto';
	}

	// Horizontal classes.
	if ( 'horizontal' === $position ) {
		$list_class[] = 'wpex-flex wpex-flex-wrap';
		$item_class[] = 'wpex-inline-block';
		if ( 'mag' === $style ) {
			$list_class[] = $has_labels ? 'wpex-gap-25' : 'wpex-gap-5';
		} else {
			$list_class[] = 'wpex-gap-5';
		}
	}
	// Vertical classes.
	elseif ( 'vertical' === $position ) {
		$list_class[] = 'wpex-flex wpex-lg-flex-col';
		$item_class[] = 'wpex-flex wpex-flex-grow';
		if ( 'rounded' === $style ) {
			$list_class[] = 'wpex-lg-gap-5';
		} elseif ( 'minimal' === $style ) {
			$item_class[] = '-wpex-lg-mb-1';
		}
	}

	// Style classes (for any position).
	if ( 'mag' === $style ) {
		$list_class[] = 'wpex-text-1em';
		$item_class[] = 'wpex-dark-mode-invert';
		$icon_class = array_merge( $icon_class, [
			'wpex-flex',
			'wpex-items-center',
			'wpex-justify-center',
			'wpex-bg-black',
			'wpex-text-white',
			'wpex-rounded-full',
		] );
		$label_class[] = 'wpex-text-black';
		$label_class[] = 'wpex-bold';
	} elseif ( 'custom' === $style ) {
		if ( ! empty( $args['link_border_radius'] ) ) {
			$link_border_radius_class = 'wpex-' . sanitize_html_class( $args['link_border_radius'] );
		}
		if ( isset( $link_border_radius_class ) && 'vertical' === $args['position'] ) {
			$item_class[] = 'wpex-ml-5 wpex-mb-5';
		}
	}
	
	// Convert classes to a string early instead of in loop.
	$icon_class_string_safe = esc_attr( implode( ' ', array_unique( $icon_class ) ) );
	$label_class_string_safe = esc_attr( implode( ' ', array_unique( $label_class ) ) );

	?>

	<ul class="<?php echo esc_attr( implode( ' ', array_unique( $list_class ) ) ); ?>"><?php

		// Remove twitter if x-twitter exists to prevent duplicate.
		if ( in_array( 'twitter', $sites, true ) && in_array( 'x-twitter', $sites, true ) ) {
			$twitter_key = array_search( 'twitter', $sites );
			if ( false !== $twitter_key ) {
				unset( $sites[ $twitter_key ] );
			}
		}

		// Loop through sites and save new array with filters for output
		foreach ( $sites as $site ) :

			if ( ! isset( $items[ $site ] ) ) {
				continue;
			}

			$item = $items[ $site ] ?? '';

			if ( ! $item ) {
				continue;
			}

			// Define li class.
			$li_class = isset( $item['li_class'] ) ? ' ' . $item['li_class'] : '';

			// Define link class.
			$site_safe = sanitize_html_class( $site );
			$link_class = "wpex-social-share__link wpex-social-share__link--{$site_safe} wpex-{$site_safe} wpex-flex wpex-items-center wpex-justify-center wpex-no-underline wpex-gap-10 wpex-duration-150 wpex-transition-colors";

			if ( $sq_links ) {
				$link_class .= ' wpex-social-share__link--sq';
			}

			if ( isset( $link_border_radius_class ) ) {
				$link_class .= ' ' . sanitize_html_class( $link_border_radius_class );
			}

			// Style specific classes.
			if ( 'flat' === $style ) {
				$link_class .= ' wpex-social-bg';
			} elseif ( 'three-d' === $style ) {
				$link_class .= ' wpex-social-bg';
			} elseif ( 'rounded' === $style ) {
				$link_class .= ' wpex-social-border wpex-social-color wpex-rounded-full wpex-box-content wpex-border-2 wpex-border-solid wpex-border-current wpex-surface-1';
			} elseif ( 'minimal' === $style ) {
				$link_class .= ' wpex-surface-1 wpex-text-4 wpex-social-color-hover wpex-border wpex-border-solid wpex-border-surface-3';
			} else {
				$link_class .= ' wpex-inherit-color';
			}

			// Filter link class.
			$link_class = apply_filters( 'wpex_social_share_item_link_class', $link_class, $site );
			$aria_label = ! empty( $item['reader_text'] ) ? $item['reader_text'] : $site;
			?>
			<li class="<?php echo esc_attr( implode( ' ', array_unique( $item_class ) ) ); ?><?php echo esc_attr( $li_class ); ?>">
				<?php if ( isset( $item['href'] ) ) { ?>
					<a href="<?php echo esc_attr( $item['href'] ); ?>" role="button" class="<?php echo esc_attr( $link_class ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>">
				<?php } else { ?>
					<a href="#" role="button" class="<?php echo esc_attr( $link_class ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>">
				<?php }
					// Display icon.
					$icon_name = $item['icon_class'] ?? $item['icon'] ?? $site;
					$icon_html_safe = totaltheme_get_icon( $icon_name );
					if ( ! $icon_html_safe ) {
						$icon_html_safe = '<span class="' . esc_attr( $icon_name ) . '" aria-hidden="true"></span>';
					}
					echo '<span class="' . $icon_class_string_safe . '">' . $icon_html_safe . '</span>';
					// Display label.
					if ( $has_labels ) { ?>
						<span class="<?php echo $label_class_string_safe; ?>"><?php
							$custom_label = wpex_get_translated_theme_mod( "social_share_{$site}_label" );
							$label = $custom_label ?: $item['label'];
							if ( $label ) {
								echo esc_html( $label );
							}
						?></span>
					<?php } ?>
				</a>
			</li>
		<?php endforeach; ?></ul>
	<?php
}
