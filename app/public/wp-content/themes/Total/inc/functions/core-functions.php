<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# General
	# Licensing
	# Dynamic Templates
	# Cards
	# Taxonomy & Terms
	# Drawers
	# Overlays
	# Sliders
	# Images
	# Icons
	# Lightbox
	# User Social Links
	# WooCommerce
	# Elementor
	# WPBakery
	# PHP Helpers
	# Fallbacks
	# PHP 8.0 Polyfills
	# Other

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Call a static class method or create new class instance.
 */
function totaltheme_call_static( string $class_name, string $method, ...$params ) {
	if ( 'WPEX_Card' !== $class_name && ! str_starts_with( $class_name, 'TotalTheme' ) ) {
		$class_name = 'TotalTheme\\' . $class_name;
	}
	if ( class_exists( $class_name ) ) {
		if ( 'init' === $method && ! method_exists( $class_name, 'init' ) ) {
			$method = 'instance';
		}
		if ( 'instance' === $method && ! method_exists( $class_name, 'instance' ) ) {
			return new $class_name( ...$params );
		}
		if ( method_exists( $class_name, $method ) ) {
			return $class_name::$method( ...$params );
		}
	}
}

/**
 * Call a non static class method.
 */
function totaltheme_call_non_static( string $class_name, string $method, ...$params ) {
	$instance = totaltheme_call_static( $class_name, 'instance' ); // don't pass params here.
	if ( $instance && method_exists( $instance, $method ) ) {
		return $instance->$method( ...$params );
	}
}

/**
 * Initialize and return class.
 */
function totaltheme_init_class( string $class_name, ...$args ) {
	return totaltheme_call_static( $class_name, 'init', ...$args );
}

/**
 * Return class instance.
 */
function totaltheme_get_instance_of( string $class_name ) {
	return totaltheme_call_static( $class_name, 'instance' );
}

/**
 * Used with add_action() to perform the theme actions.
 */
function totaltheme_action_callback( ...$params ) {
	totaltheme_call_static( 'TotalTheme\Hooks\\' . current_action(), 'callback', ...$params );
}

/**
 * Used with add_filter() to perform theme filters.
 */
function totaltheme_filter_callback( ...$params ) {
	$static = totaltheme_call_static( 'TotalTheme\Hooks\\' . current_filter(), 'callback', ...$params );
	if ( null !== $static ) {
		return $static;
	}
	return $params[0] ?? '';
}

/**
 * Returns theme version numbers.
 */
function totaltheme_get_version( string $what = '' ): string {
	switch ( $what ) {
		// Version saved when theme is first activated.
		case 'initial':
			$version = get_option( 'totaltheme_initial_version' );
			break;
		// Version saved in the database which gets updated after each theme update.
		// @note option ranamed in v5.10
		case 'db':
			$version = get_option( 'totaltheme_version' ) ?: get_option( 'total_version' );
			break;
		// The theme's actual version number.
		default:
			$version = WPEX_THEME_VERSION;
			break;
	}
	return (string) $version;
}

/**
 * Check whether the user's theme version is a specific version minimum.
 */
function totaltheme_version_check( string $what, string $version2, string $operator = '>=' ): bool {
	if ( $version1 = totaltheme_get_version( $what ) ) {
		return version_compare( $version1, $version2, $operator );
	}
	return false;
}

/**
 * Returns a CSS file URL.
 */
function totaltheme_get_css_file( string $file ): string {
	return WPEX_THEME_URI . "/assets/css/{$file}.min.css";
}

/**
 * Returns a JS file URL.
 */
function totaltheme_get_js_file( string $file ): string {
	return WPEX_THEME_URI . "/assets/js/{$file}.min.js";
}

/**
 * Outputs a theme component.
 * 
 * Currently colorpicker is the only component so we can keep the code simple for now.
 */
function totaltheme_component( string $name = '', array $args = [] ): void {
	if ( ! \wp_script_is( 'totaltheme-components' ) ) {
		wp_enqueue_script( 'totaltheme-components' );
		wp_enqueue_style( 'totaltheme-components' );
	}

	if ( 'color' === $name ) {
		$args = wp_parse_args( $args, [
			'id'                 => '',
			'value'              => '',
			'default'            => '',
			'exclude'            => '',
			'include'            => '',
			'dropdown_placement' => '',
			'input_class'        => '',
			'input_name'         => '',
			'color_scheme'       => '',
			'allow_global'       => true,
			'parse_vars'         => false,
		] );
		echo '<div class="totaltheme-component-color-wrap">';
			echo '<div class="totaltheme-component-color"';
				if ( $args['id'] ) {
					echo ' data-id="' . esc_attr( $args['id'] ) . '"';
				}
				if ( $args['default'] ) {
					echo ' data-default="' . esc_attr( $args['default'] ) . '"';
				}
				if ( $args['allow_global'] ) {
					if ( is_bool( $args['allow_global'] ) ) {
						$args['allow_global'] = (int) $args['allow_global'];
					}
					echo ' data-allow-global="' . esc_attr( $args['allow_global'] ) .'"';
					if ( $args['exclude'] ) {
						echo ' data-exclude="' . esc_attr( $args['exclude'] ) . '"';
					}
					if ( $args['include'] ) {
						echo ' data-include="' . esc_attr( $args['include'] ) . '"';
					}
				}
				if ( $args['color_scheme'] ) {
					echo ' data-color-scheme="' . esc_attr( $args['color_scheme'] ) . '"';
				}
				if ( $args['dropdown_placement'] ) {
					echo ' data-dropdown-placement="' . esc_attr( (string) $args['dropdown_placement'] ) . '"';
				}
				if ( $args['parse_vars'] ) {
					echo ' data-parse-vars="1"';
				}
			echo '>' . \esc_html__( 'Javascript required', 'total' ) . '</div>';
			echo '<input type="hidden" class="' . esc_attr( trim( "totaltheme-component-color__hidden-input {$args['input_class']}" ) ) . '"';
				if ( $args['input_name'] ) {
					echo ' name="' . esc_attr( $args['input_name'] ) . '"';
				}
				if ( $args['value'] ) {
					echo ' value="' . esc_attr( $args['value'] ) . '"';
				}
			echo '>';
		echo '</div>';
	}
}

/**
 * Replace Vars.
 */
function totaltheme_replace_vars( $context = '' ) {
	if ( $context && class_exists( 'TotalTheme\Replace_Vars' ) ) {
		return (new TotalTheme\Replace_Vars)->replace( $context );
	} else {
		return $context;
	}
}

/**
 * Check integration status.
 */
function totaltheme_is_integration_active( string $integration ): bool {
	return (bool) totaltheme_call_static(
		'Integrations',
		'is_integration_active',
		$integration
	);
}

/**
 * Returns list of theme color schemes.
 */
function totaltheme_get_color_schemes(): array {
	return [
		[
			'id'      => 'dark',
			'name'    => esc_html__( 'Dark', 'total-theme-core' ),
			'builtin' => true,
		]
	];
}

/**
 * Returns classname for given color scheme.
 */
function totaltheme_get_color_scheme_classname( string $scheme ): string {
	$scheme_safe = sanitize_html_class( $scheme );
	return "wpex-surface-{$scheme_safe}";
}

/**
 * Returns theme color palette.
 */
function totaltheme_get_color_palette( string $group = 'all' ): array {
	return (array) totaltheme_call_static( 'Color_Palette', "get_{$group}_colors" );
}

/**
 * Get post excerpt.
 */
function totaltheme_get_post_excerpt( $args = [] ) {
	if ( ! is_array( $args ) ) {
		$args = [ 'length' => $args ];
	}
	if ( class_exists( 'TotalTheme\Helpers\Post_Excerpt_Generator' ) ) {
		$generator = new TotalTheme\Helpers\Post_Excerpt_Generator( $args );
		return $generator->get_excerpt();
	}
}

/**
 * Get Theme Branding.
 */
function wpex_get_theme_branding() {
	if ( WPEX_THEME_BRANDING && 'disabled' !== WPEX_THEME_BRANDING ) {
		return sanitize_text_field( WPEX_THEME_BRANDING );
	}
}

/**
 * Check current request.
 */
function wpex_is_request( string $type ): bool {
	$check = false;
	switch ( $type ) {
		case 'admin':
			$check = is_admin();
			break;
		case 'ajax':
			$check = wp_doing_ajax();
			break;
		case 'frontend':
			$check = ( ! is_admin() || wp_doing_ajax() || totaltheme_is_wpb_frontend_editor() );
			break;
	}
	return (bool) $check;
}

/**
 * Return assets url for loading scripts.
 */
function wpex_asset_url( string $file = '' ): string {
	if ( $file ) {
		$file = WPEX_THEME_URI . '/assets/' . ltrim( $file, '/' );
	}
	return $file;
}

/**
 * Helper function for resizing images using the WPEX_Image_Resize class.
 */
function wpex_image_resize( $args ) {
	return TotalTheme\Resize_Image::getInstance()->process( $args );
}

/**
 * Returns current URL.
 */
function wpex_get_current_url(): ?string {
	global $wp;
	return $wp ? home_url( add_query_arg( [], $wp->request ) ) : null;
}

/**
 * Returns theme custom post types.
 *
 * @todo rename to totaltheme_post_types() and update Total Theme Core to use new function.
 */
function wpex_theme_post_types(): array {
	$post_types = [
		'portfolio'    => 'portfolio',
		'staff'        => 'staff',
		'testimonials' => 'testimonials',
	];
	return (array) apply_filters( 'wpex_theme_post_types', $post_types );
}

/**
 * Returns body font size.
 *
 * @todo deprecate
 */
function wpex_get_body_font_size() {
	$body_typo = get_theme_mod( 'body_typography' );
	if ( ! empty( $body_typo['font-size'] ) && is_array( $body_typo['font-size'] ) ) {
		$font_size = ! empty( $font_size['d'] ) ?  $font_size['d'] : $font_size[0];
		$font_size = sanitize_text_field( $font_size );
	} else {
		$font_size = totaltheme_has_classic_styles() ? 13 : 16;
	}
	return apply_filters( 'wpex_get_body_font_size', $font_size );
}

/**
 * Echo the post URL.
 */
function wpex_permalink( $post = '' ) {
	echo esc_url( wpex_get_permalink( $post ) );
}

/**
 * Return the post URL.
 */
function wpex_get_permalink( $post = '' ) {
	$post      = get_post( $post );
	$permalink = wpex_get_post_redirect_link( $post ) ?: get_permalink( $post );
	return (string) apply_filters( 'wpex_permalink', $permalink, $post );
}

/**
 * Get custom post link.
 */
function wpex_get_post_redirect_link( $post = '' ) {
	$post = get_post( $post );
	if ( is_object( $post ) && isset( $post->ID ) ) {
		return (string) get_post_meta( $post->ID, 'wpex_post_link', true );
	}
}

/**
 * Return custom permalink.
 *
 * @todo rename to wpex_get_post_redirection() for better consistency.
 */
function wpex_get_custom_permalink() {
	if ( $custom_link = get_post_meta( get_the_ID(), 'wpex_post_link', true ) ) {
		$custom_link = ( 'home_url' === $custom_link ) ? home_url( '/' ) : $custom_link;
		return esc_url( $custom_link );
	}
}

/**
 * Returns separator used for inline lists.
 */
function wpex_inline_list_sep( $context = '', $before = '', $after = '' ) {
	return apply_filters( 'wpex_inline_list_sep', $before . ', ' . $after, $context );
}

/**
 * Returns hover animation class.
 */
function wpex_hover_animation_class( $animation ) {
	return 'hvr hvr-' . sanitize_html_class( $animation );
}

/**
 * Returns visibility class.
 */
function totaltheme_get_visibility_class( $visibility ) {
	if ( ! $visibility || 'always-visible' === $visibility ) {
		return;
	}
	switch ( $visibility ) {
		case 'hidden-toggle-element':
			$class = 'wpex-toggle-element';
			break;
		case 'visible-toggle-element':
			$class = 'wpex-toggle-element wpex-toggle-element--visible';
			break;
		case 'hidden-toggle-element-persist':
			$class = 'wpex-toggle-element wpex-toggle-element--persist';
			break;
		case 'visible-toggle-element':
			$class = 'wpex-toggle-element wpex-toggle-element--visible';
			break;
		case 'visible-toggle-element-persist':
			$class = 'wpex-toggle-element wpex-toggle-element--visible wpex-toggle-element--persist';
			break;
		default:
			$use_opacity_in_wpb = [
				'hidden',
				'hidden-desktop-large',
				'hidden-desktop',
				'visible-tablet',
				'visible-tablet-portrait',
				'visible-tablet-landscape',
				'visible-phone',
				'visible-phone-small',
			];
			if ( totaltheme_is_wpb_frontend_editor() && in_array( $visibility, $use_opacity_in_wpb, true ) ) {
				// @note we need to change this classback for any element added outside of the content wrapper
				// we do this with JS via vc_reload.js
				$class = 'vc-inline-' . sanitize_html_class( $visibility );
			} else {
				$class = sanitize_html_class( $visibility );
			}
			break;
	}
	return $class;
}

/**
 * Returns typography style class.
 */
function wpex_typography_style_class( $style ) {
	if ( $style && 'none' !== $style && array_key_exists( $style, wpex_typography_styles() ) ) {
		return 'typography-' . sanitize_html_class( $style );
	}
}

/**
 * Returns Google Fonts URL if you want to change it to another CDN.
 */
function wpex_get_google_fonts_url() {
	return apply_filters( 'wpex_get_google_fonts_url', '//fonts.googleapis.com' );
}

/**
 * Returns array of widget areays.
 */
function wpex_get_breadcrumbs_output() {
	if ( $custom_breadcrumbs = apply_filters( 'wpex_custom_breadcrumbs', null ) ) {
		return wp_kses_post( $custom_breadcrumbs );
	}

	if ( class_exists( 'WPEX_Breadcrumbs' ) ) {
		$breadcrumbs = new WPEX_Breadcrumbs();
		return $breadcrumbs->output;
	}
}

/**
 * Return Image URL from an input (can be a URL or an ID)
 */
function wpex_get_image_url( $image ): string {
	if ( empty( $image ) ) {
		return ''; // @important - Don't return 0 or false values as a URL.
	}
	if ( is_numeric( $image ) ) {
		$image = wp_get_attachment_url( $image );
	}
	if ( $image && is_string( $image ) ) {
		return trim( set_url_scheme( $image ) );
	} else {
		return '';
	}
}

/**
 * Returns the number of columns for a particular grid.
 */
function wpex_get_array_first_value( $input ) {
	if ( is_array( $input ) ) {
		return reset( $input );
	}
	return $input;
}

/**
 * Returns current query vars.
 *
 * @todo deprecate - not being used anywhere.
 */
function wpex_get_query_vars() {
	$loadmore = \TotalTheme\Pagination\Load_More::get_data();
	if ( $loadmore ) {
		return $loadmore['query_vars'] ?? [];
	}
	global $wp_query;
	if ( isset( $wp_query ) ) {
		return $wp_query->query_vars;
	}
}

/**
 * Returns array of widget areas.
 */
function wpex_get_widget_areas() {
	global $wp_registered_sidebars;
	$widgets_areas = [];
	if ( ! empty( $wp_registered_sidebars ) ) {
		foreach ( $wp_registered_sidebars as $widget_area ) {
			$name = $widget_area['name'] ?? '';
			$id = $widget_area['id'] ?? '';
			if ( $name && $id ) {
				$widgets_areas[ $id ] = $name;
			}
		}
	}
	return $widgets_areas;
}

/**
 * Get Post Type Unlimited post type mod value.
 */
function wpex_get_ptu_type_mod( $post_type = '', $setting_id = '', $default = '' ) {
	if ( \totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
		return TotalTheme\Integration\Post_Types_Unlimited::get_setting_value( $post_type, $setting_id, $default );
	}
}

/**
 * Get Post Type Unlimited tax mod value.
 */
function wpex_get_ptu_tax_mod( $taxonomy = '', $setting_id = '', $default = '' ) {
	if ( \totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
		return TotalTheme\Integration\Post_Types_Unlimited::get_tax_setting_value( $taxonomy, $setting_id, $default );
	}
}

/**
 * Returns a staff member based on a user.
 */
function totaltheme_get_user_related_staff_member_id( $user_id ) {
	if ( $relations = (array) get_option( 'wpex_staff_users_relations' ) ) {
		return ! empty( $relations[ $user_id ] ) ? $relations[ $user_id ] : null;
	}
}

/**
 * Returns the user assigned to a staff member.
 */
function wpex_get_user_assigned_to_staff_member( $staff_member_id = '' ) {
	$relations = get_option( 'wpex_staff_users_relations' );
	if ( is_array( $relations ) ) {
		return array_search( $staff_member_id, $relations );
	}
}

/**
 * Returns your Google reCAPTCHA keys.
 */
function wpex_get_recaptcha_keys( $type = '' ) {
	$get_keys = get_option( 'wpex_recaptcha_keys' );
	$keys = [
		'site'   => $get_keys['site_key'] ?? '',
		'secret' => $get_keys['secret_key'] ?? '',
	];
	if ( $type ) {
		if ( 'site' === $type ) {
			return $keys['site'];
		}
		if ( 'secret' === $type ) {
			return $keys['secret'];
		}
	} else {
		return $keys;
	}
}

/**
 * Returns loop pagination.
 */
function wpex_loop_pagination( $loop_type = '', $count = 'deprecated' ) {
	\TotalTheme\Pagination\Core::render( $loop_type );
}

/*-------------------------------------------------------------------------------*/
/* [ Licensing ]
/*-------------------------------------------------------------------------------*/

/**
 * Gets the network saved theme license.
 */
function totaltheme_get_network_license() {
	if ( is_multisite() ) {
		switch_to_blog( get_main_site_id() );
			$license = totaltheme_get_license( false );
		restore_current_blog();
		return $license;
	}
}

/**
 * Get theme license.
 *
 * Please purchase a legal copy of the theme and don't just hack this
 * function. First of all if you hack it, you won't get updates because
 * there is added validation on our updates API so it won't work.
 * And second, a lot of time and resources has gone into the development
 * of this awesome theme, purchasing a valid license is the right thing to do.
 */
function totaltheme_get_license( $check_network = true ) {
	$license = get_option( 'totaltheme_license', 'not-set' );
	if ( 'not-set' === $license ) {
		$license = get_option( 'active_theme_license' );
		if ( $license ) {
			update_option( 'totaltheme_license', $license );
		}
	}
	if ( ! $license && $check_network && is_multisite() && ! is_main_site() ) {
		$license = totaltheme_get_network_license();
	}
	return sanitize_text_field( $license );
}

/*-------------------------------------------------------------------------------*/
/* [ Dynamic Templates ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns the dynamic template type given a template id.
 */
function totaltheme_get_dynamic_template_type( int $template_id ): string {
	return (string) get_post_meta( $template_id, 'wpex_template_type', true ) ?: '';
}

/**
 * Returns the builder being used for a given post.
 */
function totaltheme_get_post_builder_type( $post_id, $post_content = null ): ?string {
	if ( totaltheme_is_integration_active( 'wpbakery' ) ) {
		if ( null === $post_content ) {
			$post_content = (string) get_post_field( 'post_content', $post_id );
		}
		if ( $post_content && str_contains( $post_content, 'vc_row' ) ) {
			return 'wpbakery';
		}
	}
	if ( totaltheme_is_integration_active( 'elementor' ) ) {
		if ( 'elementor_library' === get_post_type( $post_id ) ) {
			return 'elementor';
		}
		if ( class_exists( 'Elementor\Plugin' ) && is_callable( [ 'Elementor\Plugin', 'instance' ] ) ) {
			if ( Elementor\Plugin::instance()->documents->get( $post_id )->is_built_with_elementor() ) {
				return 'elementor';
			}
		}
	}
	if ( has_blocks( $post_id ) ) {
		return 'gutenberg';
	}
	return null;
}

/**
 * Function used to render content for display (shortcodes, blocks, etc).
 */
function totaltheme_render_content( $content = '', $context = '' ) {
	if ( $content ) {
		$content_safe = wp_kses_post( $content );
		
		// Remove empty p tags
		$content_safe = totaltheme_remove_empty_p_tags( $content_safe );

		// Replace dynamic variables
		$content_safe = totaltheme_replace_vars( $content_safe );

		// Render blocks
		if ( function_exists( 'do_blocks' ) ) {
			$has_blocks = has_blocks( $content );
			if ( $has_blocks ) {
				$content_safe = do_blocks( $content_safe ); // @note this function runs wpauto on it's own
			}
		}

		// Replaces common plain text characters with formatted entities
		if ( function_exists( 'wptexturize' ) ) {
			$content_safe = wptexturize( $content_safe );
		}

		// Converts lone & characters into &#038; (a.k.a. &amp;)
		if ( function_exists( 'convert_chars' ) ) {
			$content_safe = convert_chars( $content_safe );
		}

		// Replaces double line breaks with paragraph elements
		// @note - running wpautop on posts with blocks causes extra line breaks and p tags after block rendering
		if ( function_exists( 'wpautop' ) && empty( $has_blocks ) ) {
			$content_safe = wpautop( $content_safe );
		}

		// Filters specific tags in post content and modifies their markup (used mainly for images and iframes)
		// @note wp core usually runs this after shortcodes but it can break shortcodes so we run it before do_shortcode() so it doesn't
		// modify the shortcode output.
		if ( function_exists( 'wp_filter_content_tags' ) ) {
			$content_safe = wp_filter_content_tags( $content_safe );
		}

		// Replaces insecure HTTP URLs to the site in the given content, if configured to do so
		if ( function_exists( 'wp_replace_insecure_home_url' ) ) {
			$content_safe = wp_replace_insecure_home_url( $content_safe );
		}

		// Shortcodes
		if ( str_contains( $content_safe, '[' ) ) {

			// Ensures that shortcodes are not wrapped in <p>...</p>

				// Core method - more intensive but doesn't property remove them from threaded shortcodes
				if ( function_exists( 'shortcode_unautop' ) ) {
					$content_safe = shortcode_unautop( $content_safe );
				}

				// The theme's method catches anything the core method misses
				$content_safe = totaltheme_shortcode_unautop( $content_safe );

			// Render shortcodes
			if ( function_exists( 'do_shortcode' ) ) {
				$content_safe = do_shortcode( $content_safe ); // must go after wpautop
			}
		}

		// Converts plain text :) into an emoji
		if ( function_exists( 'convert_smilies' ) && ! get_theme_mod( 'remove_emoji_scripts_enable', true ) ) {
			$content_safe = convert_smilies( $content_safe );
		}

		// Apply filters and return content
		// @this is a deprecated filter not needed anymore.
		return apply_filters( 'wpex_the_content', $content_safe, $context );
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Cards ]
/*-------------------------------------------------------------------------------*/

/***
 * Get card instance.
 */
function totaltheme_card_instance() {
	return class_exists( 'WPEX_Card' ) ? WPEX_Card::instance() : false;
}

/**
 * Display card.
 */
function wpex_card( $args = [] ) {
	echo wpex_get_card( $args );
}

/**
 * Get card.
 */
function wpex_get_card( $args = [] ) {
	if ( class_exists( 'WPEX_Card' ) ) {
		return (new WPEX_Card( $args ))->render();
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Taxonomy & Terms ]
/*-------------------------------------------------------------------------------*/

/**
 * Get category meta value.
 */
function wpex_get_category_meta( $term_id = '', string $key = '' ) {
	if ( ! $term_id ) {
		$term_id = TotalTheme\Pagination\Load_More::get_data( 'term_id' );
	}

	if ( ! $term_id && is_category() ) {
		$term_id = (int) get_queried_object_id();
	}

	if ( ! $term_id ) {
		return;
	}

	$value = get_term_meta( $term_id, $key, true );

	if ( null === $value || ( is_string( $value ) && '' === $value ) ) {
		$key_safe = sanitize_key( $term_id );
		$option = get_option( "category_{$key_safe}" );
		if ( $option ) {
			$value = $option[ $key ] ?? '';
		}
	}

	return $value;
}

/**
 * Get term meta value.
 */
function wpex_get_term_meta( $term_id = '', $key = '', $single = true ) {
	if ( empty( $term_id ) ) {
		$term_id = TotalTheme\Pagination\Load_More::get_data( 'term_id' );
	}
	if ( empty( $term_id ) && ( is_tax() || is_tag() || is_category() ) ) {
		$term_id = get_queried_object_id();
	}
	if ( ! empty( $term_id ) ) {
		return get_term_meta( $term_id, $key, $single );
	}
}

/**
 * Get term card style.
 */
function totaltheme_get_term_card_style( $term ) {
	if ( ! is_object( $term ) || ! is_a( $term, 'WP_Term' ) ) {
		return;
	}
	$term_card = get_term_meta( $term->term_id, 'wpex_entry_card_style', true );
	if ( ! $term_card && $term_parent = wp_get_term_taxonomy_parent_id( $term->term_id, $term->taxonomy ) ) {
		$term_card = totaltheme_get_term_card_style( get_term( $term_parent ) );
	}
	if ( $term_card && is_string( $term_card ) ) {
		return sanitize_text_field( $term_card );
	}
}

/**
 * Get term color classname.
 */
function totaltheme_get_term_color_classname( $term ) {
	$term = get_term( $term );
	if ( ! is_wp_error( $term ) && isset( $term->term_id ) ) {
		return sanitize_html_class( "has-term-{$term->term_id}-color" );
	}
}

/**
 * Get term background color classname.
 */
function totaltheme_get_term_color_background_classname( $term ) {
	$term = get_term( $term );
	if ( ! is_wp_error( $term ) && isset( $term->term_id ) ) {
		return sanitize_html_class( "has-term-{$term->term_id}-background-color" );
	}
}

/**
 * Returns term color.
 */
function totaltheme_get_term_color( $term ) {
	if ( class_exists( 'TotalThemeCore\Term_Colors', false )
		&& is_callable( 'TotalThemeCore\Term_Colors::get_term_color' )
	) {
		return TotalThemeCore\Term_Colors::get_term_color( $term );
	}
}

/**
 * Returns the post primary term color.
 */
function totaltheme_get_post_primary_term_color( $post = '' ) {
	if ( $primary_term = totaltheme_get_post_primary_term( $post ) ) {
		return totaltheme_get_term_color( $primary_term );
	}
}

/**
 * Returns the primary term for a post.
 */
function totaltheme_get_post_primary_term( $post = '', $taxonomy = '', $fallback = true ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return;
	}

	$taxonomy = $taxonomy ?: wpex_get_post_primary_taxonomy( $post );

	if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$primary_term = null;

	if ( class_exists( 'WPSEO_Primary_Term' ) ) {
		$yoast_term = new WPSEO_Primary_Term( $taxonomy, $post->ID );
		if ( $yoast_term ) {
			$yoast_term = $yoast_term->get_primary_term();
			if ( $yoast_term && term_exists( $yoast_term, $taxonomy ) ) {
				$primary_term = $yoast_term;
			}
		}
	}

	/*** @todo deprecate ***/
	$primary_term = apply_filters( 'wpex_get_post_primary_term', $primary_term, $post, $taxonomy );

	if ( $primary_term ) {
		$primary_term = get_term( $primary_term );
		if ( $primary_term && ! is_wp_error( $primary_term ) ) {
			return $primary_term;
		}
	}

	if ( $fallback ) {
		$terms = get_the_terms( $post, $taxonomy );
		if ( ! is_wp_error( $terms ) && ! empty( $terms[0] ) ) {
			return $terms[0];
		}
	}
}

/**
 * Get term thumbnail.
 */
function wpex_get_term_thumbnail_id( $term = '' ) {
	if ( \get_theme_mod( 'term_thumbnails_enable', true )
		&& is_callable( [ 'TotalThemeCore\Term_Thumbnails', 'get_term_thumbnail_id' ] )
	) {
		return TotalThemeCore\Term_Thumbnails::get_term_thumbnail_id( $term );
	}
}

/**
 * Returns post first term link.
 */
function wpex_get_first_term_link( $post = '', $taxonomy = 'category', $terms = '', $before = '', $after = '', $instance = '' ) {
	// Allows post to be an array of args since Total v4.9.
	if ( is_array( $post ) ) {
		if ( isset( $post['instance'] ) ) {
			$post = apply_filters( 'wpex_get_first_term_link_args', $post, $post['instance'] );
		}
		extract( $post );
		$post = get_post(); // reset the post variable.
	}
	if ( $primary_term = totaltheme_get_post_primary_term( $post, $taxonomy ) ) {
		$html_tag = 'a';
		$term_link = esc_url( get_term_link( $primary_term, $taxonomy ) );
		$attrs = [
			'class' => 'term-' . absint( $primary_term->term_id ),
		];
		if ( $term_link ) {
			$attrs['href'] = $term_link;
		} else {
			$html_tag = 'span';
		}
		return $before . wpex_parse_html( $html_tag, $attrs, esc_html( $primary_term->name ) ) . $after;
	}
}

/**
 * Echos post first term link.
 */
function wpex_first_term_link( $post = '', $taxonomy = 'category' ) {
	echo wpex_get_first_term_link( $post, $taxonomy );
}

/**
 * Returns a list of terms for specific taxonomy.
 */
function wpex_get_list_post_terms( $taxonomy = 'category', $show_links = true ) {
	return wpex_list_post_terms( $taxonomy, $show_links, false );
}

/**
 * List terms for specific taxonomy.
 */
function wpex_list_post_terms( $taxonomy = 'category', $show_links = true, $echo = true, $sep = '', $before = '', $after = '', $instance = '' ) {
	if ( is_array( $taxonomy ) ) {
		$defaults = [
			'taxonomy'   => 'category',
			'show_links' => true,
			'echo'       => true,
			'sep'        => '',
			'before'     => '',
			'after'      => '',
			'instance'   => '',
			'class'      => '',
		];
		$args = wp_parse_args( $taxonomy, $defaults );
	} else {
		$args = [
			'taxonomy'   => $taxonomy,
			'show_links' => $show_links,
			'echo'       => $echo,
			'sep'        => $sep,
			'before'     => $before,
			'after'      => $after,
			'instance'   => $instance,
			'class'      => '',
		];
	}

	if ( $echo ) {
		echo wpex_get_post_terms_list( $args );
	} else {
		return wpex_get_post_terms_list( $args );
	}
}

/**
 * Get a list of terms for a specific taxonomy.
 */
function wpex_get_post_terms_list( $args = [] ) {
	extract( $args );

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$list_terms = [];
	$terms      = get_the_terms( get_the_ID(), $taxonomy );

	if ( ! $terms ) {
		return;
	}

	foreach ( $terms as $term ) {
		$attrs = [
			'class' => [
				"term-{$term->term_id}",
			],
		];
		if ( $class ) {
			$attrs['class'][] = $class;
		}
		if ( $show_links && is_taxonomy_viewable( $taxonomy ) ) {
			$attrs['href'] = esc_url( get_term_link( $term->term_id, $taxonomy ) );
			$list_terms[] = wpex_parse_html( 'a', $attrs, esc_html( $term->name ) );
		} else {
			$list_terms[] = wpex_parse_html( 'span', $attrs, esc_html( $term->name ) );
		}
	}

	if ( $list_terms && is_array( $list_terms ) ) {
		if ( empty( $sep ) ) {
			$sep = apply_filters( 'wpex_list_post_terms_sep', wpex_inline_list_sep( 'post_terms_list' ), $instance );
		}
		$list_terms = implode( $sep, $list_terms );
	}

	$list_terms = (string) apply_filters( 'wpex_list_post_terms', $list_terms, $taxonomy );

	if ( $list_terms ) {
		return $before . $list_terms . $after;
	}
}

/**
 * Returns the primary taxonomy of a given post.
 */
function wpex_get_post_primary_taxonomy( $post = null ): string {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$taxonomy = wpex_get_post_type_cat_tax( get_post_type( $post ) );
	$taxonomy = apply_filters( 'wpex_get_post_primary_taxonomy', $taxonomy );
	$taxonomy = (string) apply_filters( 'wpex_post_primary_taxonomy', $taxonomy, $post );
	return $taxonomy;
}

/**
 * Returns the "category" taxonomy for a given post type.
 */
function wpex_get_post_type_cat_tax( $post_type = '' ): string {
	if ( ! $post_type ) {
		$post_type = get_post_type();
	}
	$taxonomy_map = [
		'post'         => 'category',
		'portfolio'    => 'portfolio_category',
		'staff'        => 'staff_category',
		'testimonials' => 'testimonials_category',
		'product'      => 'product_cat',
		'tribe_events' => 'tribe_events_cat',
		'download'     => 'download_category',
	];
	$taxonomy = $taxonomy_map[ $post_type ] ?? '';
	if ( $ptu_main_tax = wpex_get_ptu_type_mod( $post_type, 'main_taxonomy' ) ) {
		if ( taxonomy_exists( $ptu_main_tax ) ) {
			$taxonomy = $ptu_main_tax;
		}
	}
	return (string) apply_filters( 'wpex_get_post_type_cat_tax', $taxonomy, $post_type );
}

/**
 * Retrieve all term data.
 */
function wpex_get_term_data() {
	return get_option( 'wpex_term_data' );
}

/*-------------------------------------------------------------------------------*/
/* [ Overlays ]
/*-------------------------------------------------------------------------------*/

/**
 * Wrapper function used to display overlays.
 */
function totaltheme_render_overlay( string $position = 'inside_link', string $style = '', array $args = [] ) {
	return totaltheme_call_static( 'Overlays', 'render_template', $position, $style, $args );
}

/**
 * Returns overlay slider speed.
 */
function totaltheme_get_overlay_speed( string $speed = '' ) {
	return (string) totaltheme_call_static( 'Overlays', 'get_speed', $speed );
}

/**
 * Returns overlay background color.
 */
function totaltheme_get_overlay_bg_color( string $color = '' ) {
	return (string) totaltheme_call_static( 'Overlays', 'get_bg_color', $color );
}

/**
 * Returns overlay opacity.
 */
function totaltheme_get_overlay_opacity( string $opacity = '' ) {
	return (string) totaltheme_call_static( 'Overlays', 'get_opacity', $opacity );
}

/*-------------------------------------------------------------------------------*/
/* [ Sliders ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns slider settings.
 */
function wpex_get_post_slider_settings( $settings = [] ) {
	$defaults = [
		'filter_tag'      => 'wpex_slider_data',
		'fade'            => ( 'fade' == get_theme_mod( 'post_slider_animation', 'slide' ) ) ? 'true' : 'false',
		'auto-play'       => ( get_theme_mod( 'post_slider_autoplay', false ) ) ? 'true' : 'false',
		'buttons'         => ( get_theme_mod( 'post_slider_dots', false ) ) ? 'true' : 'false',
		'loop'            => ( get_theme_mod( 'post_slider_loop', true ) ) ? 'true' : 'false',
		'arrows'          => ( get_theme_mod( 'post_slider_arrows', true ) ) ? 'true' : 'false',
		'fade-arrows'     => ( get_theme_mod( 'post_slider_arrows_on_hover', false ) ) ? 'true' : 'false',
		'animation-speed' => intval( get_theme_mod( 'post_slider_animation_speed', 600 ) ),
	];

	if ( get_theme_mod( 'post_slider_thumbnails', apply_filters( 'wpex_post_gallery_slider_has_thumbnails', true ) ) ) {
		$defaults['thumbnails']        = 'true';
		$defaults['thumbnails-height'] = intval( get_theme_mod( 'post_slider_thumbnail_height', '60' ) );
		$defaults['thumbnails-width']  = intval( get_theme_mod( 'post_slider_thumbnail_width', '60' ) );
	}

	$settings = wp_parse_args( $settings, $defaults );

	return (array) apply_filters( $settings['filter_tag'], $settings );
}

/**
 * Returns data attributes for post sliders.
 */
function wpex_get_slider_data( $settings = [] ) {
	$settings = wpex_get_post_slider_settings( $settings );
	if ( ! $settings ) {
		return;
	}
	unset( $settings['filter_tag'] ); // not needed for loop.
	extract( $settings );
	$data = '';
	foreach ( $settings as $key => $val ) {
		$data .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
	}
	return $data;
}

/**
 * Echos data attributes for post sliders.
 */
function wpex_slider_data( $args = '' ) {
	echo wpex_get_slider_data( $args );
}

/*-------------------------------------------------------------------------------*/
/* [ Images ]
/*-------------------------------------------------------------------------------*/

/**
 * Echo animation classes for entries.
 */
function wpex_entry_image_animation_classes() {
	if ( $classes = wpex_get_entry_image_animation_classes() ) {
		echo ' ' . esc_attr( $classes );
	}
}

/**
 * Returns animation classes for entries.
 */
function wpex_get_entry_image_animation_classes() {
	$classes = '';
	$type    = get_post_type();
	if ( 'post' === $type ) {
		$type = 'blog';
	}
	$animation = ( $animation = get_theme_mod( "{$type}_entry_image_hover_animation" ) ) ? sanitize_text_field( $animation ) : '';
	if ( $animation ) {
		$classes = wpex_image_hover_classes( $animation );
	}
	return (string) apply_filters( 'wpex_entry_image_animation_classes', $classes );
}

/**
 * Returns attachment data.
 */
function wpex_get_attachment_data( $attachment = 0, $return = 'array' ) {
	$attachment = absint( $attachment );
    if ( ! $attachment || 'none' === $return ) {
        return null;
    }
	switch ( $return ) {
		case 'url':
		case 'src':
			return wp_get_attachment_url( $attachment );
			break;
		case 'alt':
			// @note we must translate the $attachment for WPML
			return get_post_meta( wpex_parse_obj_id( $attachment, 'attachment' ), '_wp_attachment_image_alt', true );
			break;
		case 'title':
			return get_the_title( $attachment );
			break;
		case 'caption':
			return wp_get_attachment_caption( $attachment );
			break;
		case 'description':
			return get_post_field( 'post_content', $attachment );
			break;
		case 'video':
			return wpex_get_attachment_video( $attachment );
			break;
		default:
			$url = wp_get_attachment_url( $attachment );
			return [
				'url'         => $url,
				'src'         => $url, // fallback
				'alt'         => get_post_meta( wpex_parse_obj_id( $attachment, 'attachment' ), '_wp_attachment_image_alt', true ),
				'title'       => get_the_title( $attachment ),
				'caption'     => wp_get_attachment_caption( $attachment ),
				'description' => get_post_field( 'post_content', $attachment ),
				'video'       => wpex_get_attachment_video( $attachment ),
			];
			break;
	}
}

/**
 * Returns attachment video.
 */
function wpex_get_attachment_video( $attachment = '' ) {
	$video = get_post_meta( $attachment, '_video_url', true );
	if ( $video ) {
		$video = esc_url( $video );
	}
	return (string) apply_filters( 'wpex_attachment_video', $video, $attachment );
}

/**
 * Checks if a featured image has a caption.
 */
function wpex_featured_image_caption( $post = '' ) {
	$post    = get_post( $post );
	$caption = wp_get_attachment_caption( get_post_thumbnail_id( $post ) );
	return (string) apply_filters( 'wpex_featured_image_caption', $caption, $post->ID );
}

/**
 * Return placeholder image.
 */
function wpex_get_placeholder_image( $attrs = [] ) {
	if ( $src = wpex_placeholder_img_src() ) {
		return '<img src="' . set_url_scheme( esc_url( $src ) ) . '"' . wpex_parse_attrs( $attrs ) . '>';
	}
}

/**
 * Return placeholder image src.
 */
function wpex_placeholder_img_src() {
	return (string) apply_filters( 'wpex_placeholder_img_src', wpex_asset_url( 'images/placeholder.jpg' ) );
}

/**
 * Returns image hover classnames.
 */
function wpex_image_hover_classes( $style = '' ) {
	if ( $style && 'none' !== $style ) {
		return 'wpex-image-hover ' . sanitize_html_class( $style );
	}
}

/**
 * Returns image rendering class.
 */
function wpex_image_rendering_class( $rendering ) {
	return 'image-rendering-' . sanitize_html_class( $rendering );
}

/**
 * Returns image filter class.
 */
function wpex_image_filter_class( $filter ) {
	if ( $filter && 'none' !== $filter ) {
		return 'image-filter-' . sanitize_html_class( $filter );
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Icons ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns a theme icon.
 */
function totaltheme_get_icon( $icon_name, $extra_class = '', $size = '', $bidi = false ) {
	if ( $icon_name ) {
		return (string) totaltheme_call_static( 'Theme_Icons', 'get_icon', $icon_name, $extra_class, $size, $bidi );
	}
}

/**
 * Get loader icon.
 */
function totaltheme_get_loading_icon( string $name, int $size = 20 ): string {
	if ( ! $name ) {
		$name = 'default';
	} elseif ( 'wp-spinner' === $name || 'spinner' === $name ) {
		$name = 'wordpress';
	}
	$icon = '';
	if ( $file = locate_template( "assets/svgs/loaders/{$name}.svg", false ) ) {
		$icon = (string) file_get_contents( $file );
		if ( $icon && $size ) {
			$icon = str_replace( '<svg', '<svg height="' . esc_attr( $size ) . '" width="' . esc_attr( $size ) . '"', $icon );
		}
	}
	return $icon;
}

/**
 * Returns post format icon name.
 */
function totaltheme_get_post_format_icon_name( $format = '' ): string {
	if ( ! $format ) {
		$format = (string) get_post_format();
	}
	$icon_map = [
		'video'   => 'play-circle-o',
		'audio'   => 'music',
		'image'   => 'image-o',
		'gallery' => 'images-o',
		'quote'   => 'quote-right',
	];
	$icon_name = $icon_map[ $format ] ?? 'file-text-o';
	$icon_name = apply_filters( 'wpex_post_format_icon', $icon_name ); // @deprecated 6.0
	return (string) apply_filters( 'totaltheme/post/format/icon_name', $icon_name );
}

/**
 * Returns a theme SVG icon.
 */
function totaltheme_get_svg( string $name, int $size = 0 ): string {
	if ( str_starts_with( $name, 'loaders/' ) ) {
		$svg = (string) totaltheme_get_loading_icon( str_replace( 'loaders/', '', $name ) );
	} else {
		$svg = (string) totaltheme_call_static( 'Theme_Icons', 'get_icons_list' )[ $name ] ?? '';
	}
	if ( $svg && $size ) {
		$svg = str_replace( '<svg', '<svg height="' . esc_attr( $size ) . '" width="' . esc_attr( $size ) . '"', $svg );
	}
	return $svg;
}

/*-------------------------------------------------------------------------------*/
/* [ Buttons ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns social button class.
 *
 * @todo add opacity hovers here (add new parameter $has_hover = true )
 */
function wpex_get_social_button_class( $style = 'default' ) {
	$class = '';

	if ( 'default' === $style ) {
		$style = (string) apply_filters( 'wpex_default_social_button_style', 'none' );
	}

	switch ( $style ) {
		// No style
		case 'none':
			$class = 'wpex-social-btn-no-style';
			break;
		// Colored
		case 'colored':
			$class = 'wpex-social-btn-colored wpex-social-color';
			break;
		// Minimal
		case 'minimal':
			$class = 'wpex-social-btn-minimal wpex-social-color-hover';
			break;
		case 'minimal-rounded':
			$class = 'wpex-social-btn-minimal wpex-social-color-hover wpex-rounded-sm';
			break;
		case 'minimal-round':
			$class = 'wpex-social-btn-minimal wpex-social-color-hover wpex-rounded-full';
			break;
		// Flat
		case 'flat':
			$class = 'wpex-social-btn-flat wpex-social-color-hover';
			break;
		case 'flat-rounded':
			$class = 'wpex-social-btn-flat wpex-social-color-hover wpex-rounded-sm';
			break;
		case 'flat-round';
			$class = 'wpex-social-btn-flat wpex-social-color-hover wpex-rounded-full';
			break;
		// Flat color
		case 'flat-color':
			$class = 'wpex-social-btn-flat wpex-social-bg';
			break;
		case 'flat-color-rounded':
			$class = 'wpex-social-btn-flat wpex-social-bg wpex-rounded-sm';
			break;
		case 'flat-color-round':
			$class = 'wpex-social-btn-flat wpex-social-bg wpex-rounded-full';
			break;
		// 3D
		case '3d':
			$class = 'wpex-social-btn-3d';
			break;
		case '3d-color':
			$class = 'wpex-social-btn-3d wpex-social-bg';
			break;
		// Accent
		case 'accent':
			$class = 'wpex-social-btn-accent wpex-bg-accent wpex-hover-bg-accent_alt';
			break;
		case 'accent-rounded':
			$class = 'wpex-social-btn-accent wpex-bg-accent wpex-hover-bg-accent_alt wpex-rounded-sm';
			break;
		case 'accent-round':
			$class = 'wpex-social-btn-accent wpex-bg-accent wpex-hover-bg-accent_alt wpex-rounded-full';
			break;
		// Black
		case 'black':
			$class = 'wpex-social-btn-black wpex-hover-opacity-80';
			break;
		case 'black-rounded':
			$class = 'wpex-social-btn-black wpex-hover-opacity-80 wpex-rounded-sm';
			break;
		case 'black-round':
			$class = 'wpex-social-btn-black wpex-hover-opacity-80 wpex-rounded-full';
			break;
		// Black + Color Hover
		case 'black-ch':
			$class = 'wpex-social-btn-black-ch wpex-social-bg-hover';
			break;
		case 'black-ch-rounded':
			$class = 'wpex-social-btn-black-ch wpex-social-bg-hover wpex-rounded-sm';
			break;
		case 'black-ch-round':
			$class = 'wpex-social-btn-black-ch wpex-social-bg-hover wpex-rounded-full';
			break;
		// Graphical
		case 'graphical':
			$class = 'wpex-social-bg wpex-social-btn-graphical';
			break;
		case 'graphical-rounded':
			$class = 'wpex-social-bg wpex-social-btn-graphical wpex-rounded-sm';
			break;
		case 'graphical-round':
			$class = 'wpex-social-bg wpex-social-btn-graphical wpex-rounded-full';
			break;
		// Bordered
		case 'bordered':
			$class = 'wpex-social-btn-bordered wpex-social-border wpex-social-color';
			break;
		case 'bordered-rounded':
			$class = 'wpex-social-btn-bordered wpex-social-border wpex-rounded-sm wpex-social-color';
			break;
		case 'bordered-round':
			$class = 'wpex-social-btn-bordered wpex-social-border wpex-rounded-full wpex-social-color';
			break;
	}

	return (string) apply_filters( 'wpex_get_social_button_class', "wpex-social-btn {$class}" );
}

/**
 * Returns theme button classes based on args.
 */
function wpex_get_button_classes( $style = [], $color = '', $size = '', $align = '' ) {
	$args = $style;

	if ( ! is_array( $args ) ) {
		$args = [
			'style' => $style,
			'color' => $color,
			'size'  => $size,
			'align' => $align,
		];
	}

	$default_args = [
		'style' => (string) get_theme_mod( 'default_button_style' ),
		'color' => (string) get_theme_mod( 'default_button_color' ),
		'size'  => '',
		'align' => '',
	];

	$defaults = (array) apply_filters( 'wpex_button_default_args', $default_args );

	foreach ( $defaults as $key => $value ) {
		if ( empty( $args[ $key ] ) ) {
			$args[ $key ] = $defaults[ $key ];
		}
	}

	extract( $args );

	$classes = [];

	switch ( $style ) {
		case 'plain-text':
			$classes[] = 'theme-txt-link';
			break;
		default:
			if ( $style ) {
				$classes[] = 'theme-button';
				$classes[] = sanitize_html_class( $style );
			} else {
				$classes[] = 'theme-button';
			}
			break;
	}

	if ( $color ) {
		$classes[] = sanitize_html_class( $color );
	}

	if ( $size ) {
		$classes[] = sanitize_html_class( $size );
	}

	if ( $align ) {
		$classes[] = 'align-' . sanitize_html_class( $align );
	}

	$classes = (array) apply_filters( 'wpex_button_classes', $classes, $args );
	$classes = array_map( 'esc_attr', $classes );
	$classes = array_filter( $classes );
	$class   = implode( ' ', $classes );
	$class   = apply_filters( 'wpex_get_theme_button_classes', $class, $style, $color, $size, $align ); // @deprecated

	return $class;
}

/*-------------------------------------------------------------------------------*/
/* [ Lightbox ]
/*-------------------------------------------------------------------------------*/

/**
 * Echo lightbox image URL.
 */
function wpex_lightbox_image( $attachment = '' ) {
	echo wpex_get_lightbox_image( $attachment );
}

/**
 * Returns lightbox image URL.
 */
function wpex_get_lightbox_image( $attachment = null ) {
	if ( null === $attachment ) {
		$card = totaltheme_card_instance();
		if ( ! empty( $card->term ) ) {
			$attachment = wpex_get_term_thumbnail_id( $card->term );
		} elseif ( 'attachment' === get_post_type() ) {
			$attachment = get_the_ID();
		} else {
			$attachment = get_post_thumbnail_id( $attachment );
		}
	}
	if ( $filtered_image = apply_filters( 'wpex_get_lightbox_image', null, $attachment ) ) {
		return $filtered_image;
	}
	if ( is_numeric( $attachment ) ) {
		if ( $attachment ) {
			$image = wpex_get_post_thumbnail_url( [
				'attachment' => $attachment,
				'size'       => apply_filters( 'wpex_get_lightbox_image_size', 'lightbox' ),
				'retina'     => false,
			] );
			if ( $image ) {
				return esc_url( $image );
			}
		}
	} elseif ( is_array( $attachment ) ) {
		return $attachment[0] ?? '';
	} else {
		return $attachment;
	}
}

/**
 * Returns array for use with inline gallery lightbox.
 */
function wpex_parse_inline_lightbox_gallery( $attachments = [] ) {
	if ( ! $attachments || ! is_array( $attachments ) ) {
		return null;
	}
	$gallery      = [];
	$has_titles   = (bool) apply_filters( 'wpex_inline_lightbox_gallery_titles', true );
	$has_captions = (bool) apply_filters( 'wpex_inline_lightbox_gallery_captions', true );
	$count = -1;
	foreach ( $attachments as $attachment ) {
		$video = (string) wpex_get_video_embed_url( wpex_get_attachment_data( $attachment, 'video' ) );
		$image = (string) wpex_get_lightbox_image( $attachment );
		$src   = $video ?: $image;
		if ( $src ) {
			$count ++;
			$gallery[$count]['src'] = esc_url( $src );
			if ( $video && $image ) {
				$gallery[$count]['thumb'] = $image;
			}
			if ( $has_titles ) {
				$title = wpex_get_attachment_data( $attachment, 'alt' );
				if ( $title ) {
					$gallery[$count]['title'] = esc_html( $title );
				}
			}
			if ( $has_captions ) {
				$caption = wpex_get_attachment_data( $attachment, 'caption' );
				if ( $caption ) {
					$gallery[$count]['caption'] = wp_kses_post( $caption );
				}
			}
		}
	}
	return htmlspecialchars( wp_json_encode( $gallery ) );
}

/*-------------------------------------------------------------------------------*/
/* [ User Social Links ]
/*-------------------------------------------------------------------------------*/

/**
 * Echo user social links.
 */
function wpex_user_social_links( $args = [] ) {
	echo wpex_get_user_social_links( $args );
}

/**
 * Display user social links.
 */
function wpex_get_user_social_links( $user_id = '', $display = 'icons', $attr = '', $before = '', $after = '' ) {
	if ( ! $user_id ) {
		return;
	}

	// Allow array for arg 1 since 4.9.
	if ( is_array( $user_id ) ) {
		$defaults = [
			'before'          => '',
			'after'           => '',
			'user_id'         => '',
			'display'         => '',
			'link_attributes' => '',
		];
		extract( wp_parse_args( $user_id, $defaults ) );
		$attr = $link_attributes; // nicer name when passing array as args
	}

	$output     = '';
	$settings   = wpex_get_user_social_profile_settings_array();
	$staff_user = totaltheme_get_user_related_staff_member_id( $user_id );

	// Loop through settings.
	foreach ( $settings as $id => $val ) {
		// Crucial resets.
		$url = $link_content = '';

		if ( $staff_user ) {
			$url = get_post_meta( $staff_user, "wpex_staff_{$id}", true );
		}

		if ( ! $url ) {
			$url = get_the_author_meta( "wpex_{$id}", $user_id );
		}

		if ( ! $url && 'x-twitter' === $id ) {
			if ( $staff_user ) {
				$url = get_post_meta( $staff_user, 'wpex_staff_twitter', true );
			}
			if ( ! $url ) {
				$url = get_the_author_meta( 'wpex_twitter', $user_id );
			}
		}

		if ( ! $url ) {
			continue;
		}

		$label = $val['label'] ?? $val; // Fallback for pre 4.5

		$default_attr = [
			'href'  => esc_url( $url ),
			'class' => [], // reset class for each item.
		];

		$attrs = apply_filters( 'wpex_get_user_social_link_attrs', wp_parse_args( $attr, $default_attr ), $id );

		// Make sure class is an array.
		$attrs['class' ] = wp_parse_list( $attrs['class' ] );

		if ( 'icons' === $display ) {

			$icon_name = $val['icon_class'] ?? $val['svg'] ?? $val['icon'] ?? $id;
			$icon_html = totaltheme_get_icon( $icon_name );

			if ( ! $icon_html ) {
				$icon_html = '<span class="' . esc_attr( $icon_name ) . '" aria-hidden="true"></span>';
			}

			$link_content = $icon_html;

			if ( $label ) {
				$link_content .= '<span class="screen-reader-text">' . esc_html( $label ) . '</span>';
			}

			$attrs['class'][] = 'wpex-' . sanitize_html_class( $id );

		} elseif ( $label ) {
			$link_content = esc_html( $label );
		}

		if ( $link_content ) {
			$attrs['class'] = array_map( 'esc_attr', array_unique( $attrs['class'] ) );
			// @note $link_content has been sanitized.
			$output .= wpex_parse_html( 'a', $attrs, $link_content );
		}

	}

	$output = (string) apply_filters( 'wpex_get_user_social_links', $output );

	if ( $output ) {
		return $before . $output . $after;
	}
}

/*-------------------------------------------------------------------------------*/
/* [ WooCommerce ]
/*-------------------------------------------------------------------------------*/

/**
 * Wrapper for wc_get_page_id which also translates the ID.
 */
function totaltheme_wc_get_page_id( string $page_slug ) {
	if ( function_exists( 'wc_get_page_id' ) ) {
		return (int) wpex_parse_obj_id( wc_get_page_id( $page_slug ) );
	}
}

/**
 * Returns product entry card style.
 */
function wpex_product_entry_card_style() {
	$style = get_theme_mod( 'woo_entry_card_style', null );
	if ( is_tax() ) {
		$term_val = wpex_get_term_meta( get_queried_object_id(), 'wpex_entry_card_style', true );
		if ( ! empty( $term_val ) ) {
			$style = $term_val;
		}
	}
	return (string) apply_filters( 'woo_entry_card_style', $style );
}

/**
 * Outputs placeholder image.
 */
function wpex_woo_placeholder_img() {
	$placeholder = '';
	if ( function_exists( 'wc_placeholder_img_src' ) ) {
		$wc_placeholder_img_src = wc_placeholder_img_src();
		if ( $wc_placeholder_img_src ) {
			$placeholder = '<img src="' . esc_url( $wc_placeholder_img_src ) . '" alt="' . esc_attr__( 'Placeholder Image', 'total' ) . '" class="woo-entry-image-main">';
		}
	}
	echo apply_filters( 'wpex_woo_placeholder_img_html', $placeholder );
}

/**
 * Outputs product price.
 */
function wpex_woo_product_price( $post = '', $before = '', $after = '' ) {
	echo wpex_get_woo_product_price( $post );
}

/**
 * Returns product price.
 */
function wpex_get_woo_product_price( $post = '', $before = '', $after = '' ) {
	$post = get_post( $post );
	if ( 'product' == get_post_type( $post ) ) {
		$product = wc_get_product( $post );
		$price = $product->get_price_html();
		if ( $price ) {
			return $before . $price . $after;
		}
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Elementor ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns elementor content to display on the front-end.
 */
function wpex_get_elementor_content_for_display( $template_id = '' ) {
	if ( shortcode_exists( 'elementor-template' ) ) {
		return do_shortcode( '[elementor-template id="' . absint( $template_id ) . '"]' ); // Elementor Pro.
	}
	if ( class_exists( '\Elementor\Plugin' ) && is_callable( [ '\Elementor\Plugin', 'instance' ] ) ) {
		return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id ); // Elementor Free.
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Enqueue Scripts ]
/*-------------------------------------------------------------------------------*/

/**
 * Define default masonry settings.
 */
function wpex_get_masonry_settings() {
	return (array) apply_filters( 'wpex_masonry_settings', [
		'transformsEnabled'  => true, // I believe this maybe deprecated in isotope now.
		'isOriginLeft'       => ! is_rtl(),
		'transitionDuration' => '0.4s',
		'layoutMode'         => 'masonry',
		'masonry'            => [
			'horizontalOrder' => true,
		],
	] );
}

/**
 * Enqueue masonry scripts.
 */
function wpex_enqueue_masonry_scripts() {
	wpex_enqueue_isotope_scripts();
}

/**
 * Enqueue isotope scripts.
 */
function wpex_enqueue_isotope_scripts() {
	wp_enqueue_script( 'imagesloaded' );
	wp_enqueue_script( 'isotope' );
	wp_enqueue_script( 'wpex-isotope' );
}

/**
 * Enqueue lightbox scripts.
 */
function wpex_enqueue_lightbox_scripts() {
	\TotalTheme\Lightbox::enqueue_scripts();
}

/**
 * Enqueue slider scripts.
 */
function wpex_enqueue_slider_pro_scripts( $noCarouselThumbnails = 'deprecated' ) {
	wp_enqueue_style( 'slider-pro' );
	wp_enqueue_script( 'slider-pro' );
	wp_enqueue_script( 'wpex-slider-pro' );
}

/*-------------------------------------------------------------------------------*/
/* [ PHP Helpers ]
/*-------------------------------------------------------------------------------*/

/**
 * Inserts a new key/value before the key in the array.
 */
function wpex_array_insert_before( $key, array $array, $new_key, $new_value = null ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = [];
		foreach ( $array as $k => $value ) {
			if ( $k === $key ) {
				if ( is_array( $new_key ) && count( $new_key ) > 0) {
					$new = array_merge( $new, $new_key );
				} else {
					$new[ $new_key ] = $new_value;
				}
			}
			$new[ $k ] = $value;
		}
		return $new;
	}
	return false;
}

/**
 * Inserts a new key/value after the key in the array.
 */
function wpex_array_insert_after( $key, array $array, $new_key, $new_value = null ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = [];
		foreach ( $array as $k => $value ) {
			$new[$k] = $value;
			if ( $k === $key ) {
				if ( is_array( $new_key ) && count( $new_key ) > 0) {
					$new = array_merge( $new, $new_key );
				} else {
					$new[$new_key] = $new_value;
				}
			}
		}
		return $new;
	}
	return false;
}

/*-------------------------------------------------------------------------------*/
/* [ Fallbacks ]
/*-------------------------------------------------------------------------------*/

/**
 * Output inline style tag based on attributes.
 */
function wpex_parse_inline_style( $atts = [], $add_style = true ) {
	if ( ! empty( $atts ) && is_array( $atts ) && function_exists( 'vcex_inline_style' ) ) {
		return vcex_inline_style( $atts, $add_style );
	}
}

/*-------------------------------------------------------------------------------*/
/* [ PHP 8.0 Polyfills - @todo deprecate and force 8.0 PHP version ]
/*-------------------------------------------------------------------------------*/

if ( ! function_exists( 'str_starts_with' ) ) {
	function str_starts_with( string $haystack, string $needle ): bool {
		return ( 0 === strpos( $haystack, $needle ) );
	}
}

if ( ! function_exists( 'str_ends_with' ) ) {
	function str_ends_with( string $haystack, string $needle ): bool {
		if ( '' === $haystack && '' !== $needle ) {
			return false;
		}
		$len = strlen( $needle );
		return 0 === substr_compare( $haystack, $needle, -$len, $len );
	}
}

if ( ! function_exists( 'str_contains' ) ) {
    function str_contains( string $haystack, string $needle ): bool {
        return '' === $needle || false !== strpos( $haystack, $needle );
    }
}

/*-------------------------------------------------------------------------------*/
/* [ Other ]
/*-------------------------------------------------------------------------------*/

/**
 * Minify CSS.
 */
function wpex_minify_css( string $css = '' ) {
	if ( ! $css ) {
		return;
	}
	// Normalize whitespace.
	$css = preg_replace( '/\s+/', ' ', $css );
	// Remove space after , : ; { } */ >
	$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );
	// Remove space before , ; { }
	$css = preg_replace( '/ (,|;|\{|})/', '$1', $css );
	return trim( $css );
}

/**
 * Allow to remove method for an hook when, it's a class method used and class doesn't have global for instanciation.
 */
function wpex_remove_class_filter( $hook_name = '', $class_name ='', $method_name = '', $priority = 0 ) {
	global $wp_filter;

	// Make sure class exists
	if ( ! class_exists( $class_name ) ) {
		return false;
	}

	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
		return false;
	}

	// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {

		// Test if filter is an array ! (always for class/method)
		// @todo consider using has_action instead
		// @link https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {

			// Test if object is a class, class and method is equal to param !
			if ( is_object( $filter_array['function'][0] )
				&& get_class( $filter_array['function'][0] )
				&& get_class( $filter_array['function'][0] ) == $class_name
				&& $filter_array['function'][1] == $method_name
			) {
				if ( isset( $wp_filter[ $hook_name ] ) ) {
					// WP 4.7
					if ( is_object( $wp_filter[ $hook_name ] ) ) {
						unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
					}
					// WP 4.6
					else {
						unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
					}
				}
			}

		}

	}

	return false;
}
