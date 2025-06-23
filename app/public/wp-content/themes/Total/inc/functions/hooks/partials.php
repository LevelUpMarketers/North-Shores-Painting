<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# Accessibility
	# Toggle Bar
	# Top Bar
	# Header
	# Menu
	# Mobile Menu
	# Page Header
	# Sidebar
	# Blog
	# Footer
	# Footer Bottom
	# Archive Loop
	# Other

/*-------------------------------------------------------------------------------*/
/* -  Accessibility
/*-------------------------------------------------------------------------------*/

/**
 * Get skip to content link.
 */
function wpex_skip_to_content_link() {
	if ( get_theme_mod( 'skip_to_content', true ) ) {
		get_template_part( 'partials/accessibility/skip-to-content' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Toggle Bar
/*-------------------------------------------------------------------------------*/

/**
 * Get togglebar layout template part if enabled.
 *
 * @todo rename to wpex_togglebar for consistency?
 */
function wpex_toggle_bar() {
	if ( wpex_has_togglebar() ) {
		wpex_get_template_part( 'togglebar' );
	}
}

/**
 * Get togglebar button template part.
 *
 * @todo rename to wpex_togglebar_button for consistency?
 */
function wpex_toggle_bar_button() {
	if ( ! wpex_has_togglebar() || ( 'visible' === get_theme_mod( 'toggle_bar_default_state', 'hidden' ) && get_theme_mod( 'toggle_bar_enable_dismiss', false ) ) ) {
		return;
	}
	wpex_get_template_part( 'togglebar_button' );
}

/*-------------------------------------------------------------------------------*/
/* -  Top Bar
/*-------------------------------------------------------------------------------*/

/**
 * Get Top Bar layout template part if enabled.
 *
 * @todo rename to wpex_topbar for consistency?
 */
function wpex_top_bar() {
	if ( totaltheme_call_static( 'Topbar\Core', 'is_enabled' ) ) {
		wpex_get_template_part( 'topbar' );
	}
}

/**
 * Get topbar innercontent.
 */
function wpex_topbar_inner() {
	if ( 'two' === totaltheme_call_static( 'Topbar\Core', 'style' ) ) {
		wpex_topbar_social();
		wpex_tobar_content();
	} else {
		wpex_tobar_content();
		wpex_topbar_social();
	}
}

/**
 * Get topbar content.
 */
function wpex_tobar_content() {
	wpex_get_template_part( 'topbar_content' );
}

/**
 * Get topbar social.
 */
function wpex_topbar_social() {
	wpex_get_template_part( 'topbar_social' );
}

/*-------------------------------------------------------------------------------*/
/* -  Header
/*-------------------------------------------------------------------------------*/

/**
 * Header Inner flex box open.
 */
function wpex_header_flex_open() {
	if ( ! totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
		return;
	}

	$class[] = 'wpex-flex';

	if ( ! in_array( totaltheme_call_static( 'Header\Core', 'style' ), [ 'seven', 'eight', 'nine', 'ten' ] ) ) {
		$class[] = 'wpex-flex-wrap'; // these headers have a fixed height so don't allow wrapping.
	}
	$class[] = 'wpex-justify-between';
	$class[] = 'wpex-items-center';
	$class[] = 'wpex-w-100'; // prevents issues when the #site-header-inner is also flex.

	/**
	 * Filters the flex header classes.
	 *
	 * @param array $classes
	 */
	$class = (array) apply_filters( 'wpex_site_header_flex_class', $class );

	echo '<div id="site-header-flex" class="' . esc_attr( implode( ' ', $class ) ) . '">';

	// @todo add wpex_header_flex_top hook
}

/**
 * Header Inner flex box close.
 */
function wpex_header_flex_close() {
	if ( totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
		// @todo add wpex_header_flex_bottom hook
		echo '</div>';
	}
}

/**
 * Get the header template part if enabled.
 */
function wpex_header() {
	if ( totaltheme_call_static( 'Header\Core', 'is_enabled' ) ) {
		wpex_get_template_part( 'header' );
	}
}

/**
 * Get the header logo template part.
 */
function wpex_header_logo() {
	wpex_get_template_part( 'header_logo' );
}

/**
 * Get the header logo inner content.
 */
function wpex_header_logo_inner() {
	wpex_get_template_part( 'header_logo_inner' );
}

/**
 * Get the header aside content template part.
 */
function wpex_header_aside() {
	if ( totaltheme_call_static( 'Header\Aside', 'is_supported' ) ) {
		wpex_get_template_part( 'header_aside' );
	}
}

/**
 * Get the flex header aside content.
 */
function wpex_header_flex_aside() {
	if ( in_array( totaltheme_call_static( 'Header\Core', 'style' ), [ 'seven', 'eight', 'nine', 'ten' ] ) ) {
		wpex_get_template_part( 'header_flex_aside' );
	}
}

/**
 * Add search dropdown to header inner.
 */
function wpex_header_inner_search_dropdown() {
	if ( totaltheme_call_static( 'Header\Menu\Search', 'is_enabled' )
		&& 'drop_down' === totaltheme_call_static( 'Header\Menu\Search', 'style' )
		&& ! wpex_maybe_add_header_drop_widget_inline( 'search' )
	) {
		wpex_get_template_part( 'header_search_dropdown' );
	}
}

/**
 * Get header search dropdown template part.
 *
 * @deprecated 4.5.4
 */
function wpex_search_dropdown() {
	wpex_get_template_part( 'header_search_dropdown' );
}

/**
 * Get header search replace template part.
 */
function wpex_search_header_replace() {
	if ( totaltheme_call_static( 'Header\Menu\Search', 'is_enabled' )
		&& 'header_replace' === totaltheme_call_static( 'Header\Menu\Search', 'style' )
	) {
		wpex_get_template_part( 'header_search_replace' );
	}
}

/**
 * Gets header search overlay template part.
 */
function wpex_search_overlay() {
	if ( totaltheme_call_static( 'Header\Menu\Search', 'is_enabled' )
		&& 'overlay' === totaltheme_call_static( 'Header\Menu\Search', 'style' )
	) {
		wpex_get_template_part( 'header_search_overlay' );
	}
}

/**
 * Overlay Header Wrap Open.
 */
function wpex_overlay_header_wrap_open() {
	if ( totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) {
		echo '<div id="overlay-header-wrap" class="wpex-clr">';
	}
}

/**
 * Overlay Header Wrap Close.
 */
function wpex_overlay_header_wrap_close() {
	if ( totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) {
		echo '</div>';
	}
}

/**
 * Overlay Header Template
 */
function wpex_overlay_header_template() {
	if ( totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) {
		totaltheme_call_static( 'Header\Overlay', 'render_template' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Menu
/*-------------------------------------------------------------------------------*/

/**
 * Outputs the main header menu.
 */
function wpex_header_menu() {
	if ( ! totaltheme_call_static( 'Header\Menu', 'is_enabled' ) ) {
		return;
	}

	$get          = false;
	$header_style = totaltheme_call_static( 'Header\Core', 'style' );

	switch ( current_action() ) {
		case 'wpex_hook_header_inner':
			if ( 'one' === $header_style
				|| 'five' === $header_style
				|| 'six' === $header_style
				|| 'dev' === $header_style
				|| 'seven' === $header_style
				|| 'eight' === $header_style
				|| 'nine' === $header_style
				|| 'ten' === $header_style
			) {
				$get = true;
			}
			break;
		case 'wpex_hook_header_top':
			if ( 'four' === $header_style ) {
				$get = true;
			}
			break;
		case 'wpex_hook_header_bottom':
			if ( 'two' === $header_style || 'three' === $header_style ) {
				$get = true;
			}
			break;
	}

	if ( $get ) {
		wpex_get_template_part( 'header_menu' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Menu > Mobile
/*-------------------------------------------------------------------------------*/

/**
 * Gets the template part for the fixed top mobile menu style.
 */
function wpex_mobile_menu_fixed_top() {
	if ( totaltheme_call_static( 'Mobile\Menu', 'is_enabled' ) && 'fixed_top' === wpex_header_menu_mobile_toggle_style() ) {
		wpex_get_template_part( 'header_mobile_menu_fixed_top' );
	}
}

/**
 * Gets the template part for the navbar mobile menu_style.
 */
function wpex_mobile_menu_navbar() {
	if ( ! totaltheme_call_static( 'Mobile\Menu', 'is_enabled' ) ) {
		return;
	}

	if ( 'navbar' !== wpex_header_menu_mobile_toggle_style() ) {
		return;
	}

	$get = false;

	if ( 'outer_wrap_before' === get_theme_mod( 'mobile_menu_navbar_position' ) ) {
		$before_wrap = true;
	} else {
		// Force before_wrap position for overlay header.
		// @todo when using the responsive overlay maybe we should use javascript to decide?
		$before_wrap = (bool) totaltheme_call_static( 'Header\Overlay', 'is_enabled' );
	}

	switch ( current_action() ) {
		case 'wpex_outer_wrap_before':
			if ( $before_wrap ) {
				$get = true;
			}
			break;
		case 'wpex_hook_header_bottom':
			if ( ! $before_wrap ) {
				$get = true;
			}
			break;
	}

	if ( $get ) {
		wpex_get_template_part( 'header_mobile_menu_navbar' );
	}
}

/**
 * Gets the template part for the "icons" style mobile menu.
 */
function wpex_mobile_menu_icons() {
	$toggle_style   = wpex_header_menu_mobile_toggle_style();
	$styles_w_icons = array(
		'icon_buttons',
		'icon_buttons_under_logo',
		'centered_logo',
		'next_to_logo'
	);

	if ( ! in_array( $toggle_style, $styles_w_icons ) ) {
		return;
	}

	if ( ! totaltheme_call_static( 'Mobile\Menu', 'is_enabled' ) ) {
		return;
	}

	wpex_get_template_part( 'header_mobile_menu_icons' );
}

/**
 * Get mobile menu alternative if enabled.
 */
function wpex_mobile_menu_alt() {
	if ( wpex_has_mobile_menu_alt() ) {
		wpex_get_template_part( 'header_mobile_menu_alt' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Page Header
/*-------------------------------------------------------------------------------*/

/**
 * Get page header template part if enabled.
 */
function wpex_page_header() {
	if ( totaltheme_call_static( 'Page\Header', 'is_enabled' ) && ! wpex_theme_do_location( 'page_header' ) ) {
		wpex_get_template_part( 'page_header' );
	}
}

/**
 * Get page header content template part.
 */
function wpex_page_header_content() {
	if ( has_action( 'wpex_hook_page_header_content' ) ) {
		wpex_get_template_part( 'page_header_content' );
	}
}

/**
 * Get page header aside template part.
 */
function wpex_page_header_aside() {
	if ( has_action( 'wpex_hook_page_header_aside' ) ) {
		wpex_get_template_part( 'page_header_aside' );
	}
}

/**
 * Get page header title template part if enabled.
 */
function wpex_page_header_title() {
	if ( totaltheme_call_static( 'Page\Header', 'has_title' ) ) {
		wpex_get_template_part( 'page_header_title' );
	}
}

/**
 * Get post heading template part.
 */
function wpex_page_header_subheading() {
	if ( ! totaltheme_call_static( 'Page\Header', 'has_subheading' ) ) {
		remove_action( 'wpex_hook_page_header_aside', 'wpex_page_header_subheading' );
		return;
	}

	$location = get_theme_mod( 'page_header_subheading_location' ) ?: 'page_header_content';

	if ( 'page_header_aside' !== $location ) {
		remove_action( 'wpex_hook_page_header_aside', 'wpex_page_header_subheading' );
	}

	if ( current_action() === "wpex_hook_{$location}" ) {
		wpex_get_template_part( 'page_header_subheading' );
	}
}

/**
 * Get breadcrumbs.
 */
function wpex_display_breadcrumbs() {
	if ( wpex_has_breadcrumbs() ) {
		$position = wpex_breadcrumbs_position();
		if ( 'page_header_aside' !== $position ) {
			remove_action( 'wpex_hook_page_header_aside', 'wpex_display_breadcrumbs', 20 );
		}
		if ( current_action() === "wpex_hook_{$position}" ) {
			wpex_get_template_part( 'breadcrumbs' );
		}
	} else {
		remove_action( 'wpex_hook_page_header_aside', 'wpex_display_breadcrumbs', 20 );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Sidebar
/*-------------------------------------------------------------------------------*/

/**
 * Gets sidebar template.
 */
function wpex_get_sidebar_template() {
	if ( current_action() === totaltheme_call_static( 'Sidebars\Primary', 'get_insert_hook_name' )
		&& ! in_array( wpex_content_area_layout(), [ 'full-screen', 'full-width' ], true )
	) {
		get_sidebar( apply_filters( 'wpex_get_sidebar_template', null ) );
	}
}

/**
 * Displays sidebar inside the sidebar template.
 */
function wpex_display_sidebar() {
	if ( wpex_has_sidebar() && $sidebar = totaltheme_call_static( 'Sidebars\Primary', 'get_sidebar_name' ) ) {
		dynamic_sidebar( $sidebar );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Blog
/*-------------------------------------------------------------------------------*/

/**
 * Blog single media above content.
 */
function wpex_blog_single_media_above() {
	if ( ! is_singular() ) {
		return;
	}

	// Blog media position.
	$position = apply_filters( 'wpex_blog_single_media_position', wpex_get_custom_post_media_position() );

	// Display the post media above the post (this is a meta option).
	if ( 'above' === $position && ! post_password_required() ) {

		// Standard posts.
		if ( 'post' === get_post_type() ) {
			wpex_get_template_part( 'blog_single_media', get_post_format() );
		}

		// Other post types.
		else {
			wpex_get_template_part( 'cpt_single_media' );
		}

	}
}

/*-------------------------------------------------------------------------------*/
/* -  Footer
/*-------------------------------------------------------------------------------*/

/**
 * Gets the footer callout template part.
 */
function wpex_footer_callout() {
	if ( totaltheme_call_static( 'Footer\Callout', 'is_enabled' ) && ! wpex_theme_do_location( 'footer_callout' ) ) {
		wpex_get_template_part( 'footer_callout' );
	}
}

/**
 * Gets the footer layout template part.
 */
function wpex_footer() {
	if ( totaltheme_call_static( 'Footer\Core', 'is_enabled' ) ) {
		wpex_get_template_part( 'footer' );
	}
}

/**
 * Get the footer widgets template part.
 */
function wpex_footer_widgets() {
	wpex_get_template_part( 'footer_widgets' );
}

/**
 * Gets the footer bottom template part.
 */
function wpex_footer_bottom() {
	if ( totaltheme_call_static( 'Footer\Bottom\Core', 'is_enabled' ) ) {
		wpex_get_template_part( 'footer_bottom' );
	}
}

/**
 * Gets the scroll to top button template part.
 */
function wpex_scroll_top() {
	if ( get_theme_mod( 'scroll_top', true ) ) {
		wpex_get_template_part( 'scroll_top' );
	}
}

/**
 * Footer reaveal open code.
 */
function wpex_footer_reveal_open() {
	if ( totaltheme_call_static( 'Footer\Core', 'is_enabled' ) && totaltheme_call_static( 'Footer\Core', 'has_reveal' ) ) {
		wpex_get_template_part( 'footer_reveal_open' );
	}
}

/**
 * Footer reaveal close code.
 */
function wpex_footer_reveal_close() {
	if ( totaltheme_call_static( 'Footer\Core', 'is_enabled' ) && totaltheme_call_static( 'Footer\Core', 'has_reveal' ) ) {
		wpex_get_template_part( 'footer_reveal_close' );
	}
}

/**
 * Site Frame Border.
 */
function wpex_site_frame_border() {
	if ( wp_validate_boolean( get_theme_mod( 'site_frame_border', false ) ) || is_customize_preview() ) {
		if ( is_user_logged_in() ) {
			echo '<style>.admin-bar #wpex-sfb-t{inset-block-start:var(--wpadminbar-height,0px)}</style>';
		}
		echo '<div id="wpex-sfb-l" class="wpex-bg-accent wpex-fixed wpex-z-backdrop wpex-inset-y-0 wpex-left-0"></div><div id="wpex-sfb-r" class="wpex-bg-accent wpex-fixed wpex-z-backdrop wpex-inset-y-0 wpex-right-0"></div><div id="wpex-sfb-t" class="wpex-sticky-el-offset wpex-ls-offset wpex-bg-accent wpex-fixed wpex-z-backdrop wpex-inset-x-0 wpex-top-0"></div><div id="wpex-sfb-b" class="wpex-bg-accent wpex-fixed wpex-z-backdrop wpex-inset-x-0 wpex-bottom-0"></div>';
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Footer Bottom
/*-------------------------------------------------------------------------------*/

/**
 * Footer bottom flex box open.
 */
function wpex_footer_bottom_flex_open() { ?>
<div <?php totaltheme_call_static( 'Footer\Bottom\Core', 'flex_class' ); ?>>
<?php }

/**
 * Footer bottom flex box close.
 */
function wpex_footer_bottom_flex_close() {
	echo '</div>';
}

/**
 * Footer bottom copyright.
 */
function wpex_footer_bottom_copyright() {
	wpex_get_template_part( 'footer_bottom_copyright' );
}

/**
 * Footer bottom menu.
 */
function wpex_footer_bottom_menu() {
	wpex_get_template_part( 'footer_bottom_menu' );
}

/*-------------------------------------------------------------------------------*/
/* -  Other
/*-------------------------------------------------------------------------------*/

/**
 * Get term description.
 */
function wpex_term_description() {
	if ( ! is_tax() && ! is_category() && ! is_tag() ) {
		return;
	}
	switch ( current_action() ) {
		case 'wpex_hook_content_top':
			$get = wpex_has_term_description_above_loop();
			break;
		default:
			$get = true;
			break;
	}
	if ( $get ) {
		wpex_get_template_part( 'term_description' );
	}
}

/**
 * Get next/previous links.
 */
function wpex_next_prev() {
	if ( wpex_has_next_prev() ) {
		wpex_get_template_part( 'next_prev' );
	}
}

/**
 * Get next/previous links.
 */
function wpex_post_edit(): void {
	if ( get_theme_mod( 'edit_post_link_enable', true )
		&& is_user_logged_in()
		&& is_singular()
		&& current_user_can( 'edit_post', get_the_ID() )
		&& ! totaltheme_is_wpb_frontend_editor()
		&& ! totaltheme_is_elementor_editor()
		&& ! is_customize_preview()
	) {
		wpex_get_template_part( 'post_edit' );
	}
}

/**
 * Site Top div.
 */
function wpex_ls_top() {
	echo '<span data-ls_id="#site_top" tabindex="-1"></span>';
}

/**
 * Returns social sharing template part.
 */
function wpex_social_share() {
	wpex_get_template_part( 'social_share' );
}

/**
 * Adds a hidden searchbox in the footer for use with the mobile menu.
 *
 * @deprecated 5.4
 */
function wpex_mobile_searchform() {
	wpex_get_template_part( 'mobile_searchform' );
}

/**
 * Outputs page/post slider based on the wpex_post_slider_shortcode custom field.
 */
function wpex_post_slider( $post_id = '', $postion = '' ) {
	if ( ! $post_id ) {
		$post_id = wpex_get_current_post_id();
	}

	if ( ! wpex_has_post_slider( $post_id ) ) {
		return;
	}

	$get      = false;
	$position = wpex_post_slider_position( $post_id );

	switch ( current_action() ) {
		case 'wpex_hook_topbar_before':
			if ( 'above_topbar' === $position ) {
				$get = true;
			}
			break;
		case 'wpex_hook_header_before':
			if ( 'above_header' === $position ) {
				$get = true;
			}
			break;
		case 'wpex_hook_header_bottom':
			if ( 'above_menu' === $position ) {
				$get = true;
			}
			break;
		case 'wpex_hook_page_header_before':
			if ( 'above_title' === $position ) {
				$get = true;
			}
			break;
		case 'wpex_hook_main_top':
			if ( 'below_title' === $position ) {
				$get = true;
			}
			break;
	}
	if ( $get ) {
		wpex_get_template_part( 'post_slider' );
	}
}
