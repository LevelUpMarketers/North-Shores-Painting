<?php

defined( 'ABSPATH' ) || exit;

/**
 * Return theme branding.
 */
function vcex_shortcodes_branding() {
	return function_exists( 'wpex_get_theme_branding' ) ? wpex_get_theme_branding() : 'Total Theme';
}

/**
 * Total exclusive setting notice.
 */
function vcex_total_exclusive_notice() {
	return '<div class="vcex-t-exclusive">' . esc_html__( 'This is a Total theme exclusive function.', 'total-theme-core' ) . '</div>';
}


/**
 * Check if we are currently making Vcex ajax request.
 */
function vcex_doing_ajax(): bool {
	return ! empty( $_REQUEST['action'] ) && class_exists( 'TotalThemeCore\Vcex\Ajax' ) && $_REQUEST['action'] === TotalThemeCore\Vcex\Ajax::ACTION;
}

/**
 * Check if currently in front-end editing mode.
 */
function vcex_is_frontend_edit_mode(): bool {
	return totalthemecore_call_static( 'WPBakery\Helpers', 'is_frontend_edit_mode' ) || totalthemecore_call_static( 'Elementor\Helpers', 'is_edit_mode' );
}

/**
 * Check if currently working in the wpbakery front-end editor.
 */
function vcex_vc_is_inline(): bool {
	return (bool) totalthemecore_call_static( 'WPBakery\Helpers', 'is_frontend_edit_mode' );
}

/**
 * Check if we are in edit mode.
 */
function vcex_is_template_edit_mode(): bool {
	return (bool) vcex_get_template_edit_mode();
}

/**
 * Checks if the theme is directionally aware (converts left/right to the opposite).
 */
function vcex_is_bidirectional(): bool {
	return (bool) apply_filters( 'totalthemecore/vcex/is_bidirectional', true );
}

/**
 * Return aria label.
 */
function vcex_get_aria_label( $which ) {
	return function_exists( 'wpex_get_aria_label' ) ? wpex_get_aria_label( $which ) : '';
}

/**
 * Validates a user function for security reasons.
 */
function vcex_validate_user_func( $func = '' ): bool {
	return is_callable( $func ) && totalthemecore_call_non_static( 'Vcex\User_Callback_Functions', 'is_whitelisted', $func );
}

/**
 * Check if responsiveness is enabled.
 */
function vcex_is_layout_responsive(): bool {
	return (bool) apply_filters( 'wpex_is_layout_responsive', get_theme_mod( 'responsive', true ) );
}

/**
 * Return commonly used field descriptions.
 */
function vcex_shortcode_param_description( string $param_type ): string {
	return class_exists( 'TotalThemeCore\Vcex\Param_Description' ) ? (new TotalThemeCore\Vcex\Param_Description)->get( $param_type ) : '';
}

/**
 * Locate shortcode template.
 */
function vcex_get_shortcode_template( string $shortcode_tag ): string {
	if ( is_child_theme() && $user_template_path = locate_template( "vcex_templates/{$shortcode_tag}.php" ) ) {
		if ( ! str_contains( $user_template_path, '/Total/vcex_templates/' ) ) {
			return $user_template_path;
		}
	}
	return TTC_PLUGIN_DIR_PATH . "inc/vcex/templates/{$shortcode_tag}.php";
}

/**
 * Check if a given shortcode should display.
 */
function vcex_maybe_display_shortcode( $shortcode_tag, $atts ): bool {
	$check = true;
	// Shortcodes should display for frontend only.
	// Prevents issues with Gutenberg !!! important !!!
	// @todo is this still needed?
	if ( is_admin() && ! wp_doing_ajax() ) {
		$check = false;
	}
	return (bool) apply_filters( 'vcex_maybe_display_shortcode', $check, $shortcode_tag, $atts );
}

/**
 * Call any shortcode function by it's tagname.
 */
function vcex_do_shortcode_function( $tag, $atts = [], $content = null, $extra_atts = 'deprecated' ) {
	if ( shortcode_exists( $tag ) ) {
		global $shortcode_tags;
		if ( is_callable( $shortcode_tags[ $tag ] ) ) {
			return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
		}
	}
}

/**
 * Check if we are in edit mode.
 */
function vcex_get_template_edit_mode(): string {
	static $mode = null;
	if ( null === $mode ) {
		$mode = '';
		if ( vcex_vc_is_inline()
			&& in_array( get_post_type(), [ 'templatera', 'wpex_card', 'wpex_templates' ], true )
		) {
			$mode = 'wpbakery';
		} elseif ( did_action( 'elementor/loaded' )
			&& in_array( get_post_type(), [ 'wpex_card', 'elementor_library', 'wpex_templates' ], true )
			&& ! empty( $_REQUEST['action'] )
			&& 'elementor_ajax' === $_REQUEST['action']
		) {
			$mode = 'elementor';
		}
	}
	return $mode;
}

/**
 * Get post type cat tax.
 */
function vcex_get_post_type_cat_tax( $post_type = '' ) {
	if ( function_exists( 'wpex_get_post_type_cat_tax' ) ) {
		return wpex_get_post_type_cat_tax( $post_type );
	}
	return 'category';
}

/**
 * Wrapper for totaltheme_get_icon().
 */
function vcex_get_theme_icon_html( $icon_name = '', $extra_class = '', $size = '', $bidi = false ) {
	if ( function_exists( 'totaltheme_get_icon' ) ) {
		return totaltheme_get_icon( $icon_name, $extra_class, $size, $bidi );
	}
}

/**
 * Wrapper for intval with fallback.
 */
function vcex_intval( $val = null, $fallback = null ) {
	if ( 0 === $val ) {
		return 0; // Some settings may need empty values.
	}
	$val = intval( $val ); // sanitize $val first incase it returns 0
	return $val ?: $fallback;
}

/**
 * Parses the group parameter.
 *
 * Performs the same function as vc_param_group_parse_atts()
 */
function vcex_vc_param_group_parse_atts( $atts_string = '' ) {
	if ( is_array( $atts_string ) ) {
		return $atts_string; // already converted
	} elseif ( $atts_string && is_string( $atts_string ) ) {
		return json_decode( urldecode( $atts_string ), true );
	}
}

/**
 * Validate Font Size.
 *
 * @todo deprecate - currently only used for the pricing/button icon transforms.
 */
function vcex_validate_font_size( $input ) {
	$preset_sizes = [ 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl' ];

	if ( in_array( $input, $preset_sizes ) ) {
		return "var(--wpex-text-{$input})";
	}

	if ( str_ends_with( $input, 'px' )
		|| str_ends_with( $input, 'em' )
		|| str_ends_with( $input, 'vw' )
		|| str_ends_with( $input, 'vmin' )
		|| str_ends_with( $input, 'vmax' )
	) {
		$input = $input;
	} else {
		$input = absint( $input ) . 'px';
	}
	if ( '0px' !== $input && '0em' !== $input ) {
		return esc_attr( $input );
	} else {
		return '';
	}
}

/**
 * Validate Attribute Boolean.
 */
function vcex_validate_att_boolean( $key = '', $atts = [], $default = false, $check_blank = false ): bool {
	if ( ! array_key_exists( $key, $atts ) ) {
		return $default;
	}

	$var = (string) $atts[ $key ];

	if ( isset( $atts['is_elementor_widget' ] ) && true === $atts['is_elementor_widget' ] && '' === $var ) {
		return false; // elementor won't save "false" it only saves "true" or empty string.
	}

	// Fallback required from WPBakery update when params are defined as empty such as entry_media=""
	// would result in (bool) True
	if ( $check_blank && ! $var ) {
		return true;
	}

	return vcex_validate_boolean( $var );
}

/**
 * Validate Boolean.
 */
function vcex_validate_boolean( $var ): bool {
	if ( is_bool( $var ) ) {
		return $var;
	}
	if ( is_string( $var ) ) {
		if ( 'true' === $var || 'yes' === $var ) {
			return true;
		} elseif ( 'false' === $var || 'no' === $var ) {
			return false;
		}
	}
	return (bool) $var;
}

/**
 * Validate px.
 */
function vcex_validate_px( $input ) {
	if ( ! $input ) {
		return '';
	} elseif ( 'none' === $input || '0px' === $input ) {
		return '0';
	} elseif ( $input = floatval( $input ) ) {
		return $input . 'px';
	}
}

/**
 * Validate px or percentage value.
 */
function vcex_validate_px_pct( $input ) {
	if ( ! $input ) {
		return '';
	} elseif ( 'none' === $input || '0px' === $input ) {
		return '0';
	} elseif ( str_ends_with( $input, '%' ) ) {
		return sanitize_text_field( $input );
	} elseif ( $input = floatval( $input ) ) {
		return $input . 'px';
	}
}

/**
 * Get site default font size.
 */
function vcex_get_body_font_size() {
	if ( function_exists( 'wpex_get_body_font_size' ) ) {
		return wpex_get_body_font_size();
	}
}

/**
 * Check if an attachment id exists.
 */
function vcex_validate_attachment( $attachment = '' ) {
	if ( 'attachment' === get_post_type( $attachment ) ) {
		return $attachment;
	}
}

/**
 * Parses a textfield text.
 */
function vcex_parse_text( $text = '', $sanitize = false ) {
	// Return if text has no value @note we don't explicitly check if it's a string because it could be a float or int
	if ( ! $text ) {
		return '';
	}

	// Parse html which is encoded as base64 - @todo this may not be needed anymore
	if ( preg_match( '/^#E\-8_/', $text ) ) {
		// @codingStandardsIgnoreLine
		$text = rawurldecode( base64_decode( preg_replace( '/^#E\-8_/', '', $text ) ) );
	}

	// Parses shortcodes inside attrinbutes (wpbakery encodes these)
	if ( str_contains( $text, '`{`' ) ) {
		$text = str_replace( [
			'`{`',
			'`}`',
			'``',
		], [
			'[',
			']',
			'"',
		], $text );
	}

	// Important we must sanitize before replace_vars and do_shortcode
	if ( $sanitize ) {
		if ( str_contains( $text, '@' ) && is_email( $text ) ) {
			// @important we can't use sanitize_text_field because it will strip out %20 which is used for spaces in mailto subjects
			$text = antispambot( wp_strip_all_tags( $text ) );
		} else {
			$text = wp_kses_post( $text );
		}
	}

	if ( function_exists( 'totaltheme_replace_vars' ) ) {
		$text = totaltheme_replace_vars( $text );
	}

	return do_shortcode( $text );
}

/**
 * Parses and sanitizes textfield value.
 */
function vcex_parse_text_safe( $text = '' ) {
	return vcex_parse_text( $text, true );
}

/**
 * Get encoded vc data.
 */
function vcex_vc_value_from_safe( $value, $encode = false ) {
	if ( function_exists( 'vc_value_from_safe' ) ) {
		return vc_value_from_safe( $value );
	}
	$value = preg_match( '/^#E\-8_/', $value ) ? rawurldecode( base64_decode( preg_replace( '/^#E\-8_/', '', $value ) ) ) : $value;
	if ( $encode ) {
		$value = htmlentities( $value, ENT_COMPAT, 'UTF-8' );
	}
	return str_replace( [
		'`{`',
		'`}`',
		'``',
	], [
		'[',
		']',
		'"',
	], $value );
}

/**
 * REturns theme post types.
 */
function vcex_theme_post_types() {
	if ( function_exists( 'wpex_theme_post_types' ) ) {
		return wpex_theme_post_types();
	}
}

/**
 * Convert to array, used for the grid filter.
 */
function vcex_string_to_array( $value = [] ) {
	if ( empty( $value ) && is_array( $value ) ) {
		return null; // @todo why do we do this?
	}
	if ( ! empty( $value ) && is_array( $value ) ) {
		return $value;
	}
	$array = [];
	$items = preg_split( '/\,[\s]*/', $value );
	foreach ( $items as $item ) {
		if ( strlen( $item ) > 0 ) {
			$array[] = $item;
		}
	}
	return $array;
}

/**
 * Combines multiple top/right/bottom/left fields.
 */
function vcex_combine_trbl_fields( $top = '', $right = '', $bottom = '', $left = '' ) {
	$margins = [];
	if ( $top ) {
		$margins['top'] = 'top:' . sanitize_text_field( $top );
	}
	if ( $right ) {
		$margins['right'] = 'right:' . sanitize_text_field( $right );
	}
	if ( $bottom ) {
		$margins['bottom'] = 'bottom:' . sanitize_text_field( $bottom );
	}
	if ( $left ) {
		$margins['left'] = 'left:' . sanitize_text_field( $left );
	}
	if ( $margins ) {
		return implode( '|', $margins );
	}
}

/**
 * Migrate font_container field to individual params.
 */
function vcex_migrate_font_container_param( $font_container_field = '', $target = '', $atts = [] ) {
	if ( empty( $atts[ $font_container_field ] ) ) {
		return $atts;
	}

	$get_typo = vcex_parse_typography_param( $atts[ $font_container_field ] );

	if ( empty( $get_typo ) ) {
		return $atts;
	}

	$params_to_migrate = [
		'font_size',
		'text_align',
		'line_height',
		'color',
		'font_family',
		'tag',
	];

	foreach ( $params_to_migrate as $param ) {
		if ( empty( $get_typo[ $param ] ) ) {
			continue;
		}
		$value = $get_typo[ $param ];
		if ( 'text_align' === $param && ( 'left' === $value || 'justify' === $value ) ) {
			continue; // left text align was never & justify isn't available in the theme so don't migrate
		}
		if ( empty( $atts[ $target . '_' . $param ] ) ) {
			$atts[ $target . '_' . $param ] = $value;
		}
	}

	return $atts;
}

/**
 * Get Terms.
 */
function vcex_get_terms( $atts = [], $shortcode_tag = '' ) {
	$term_query = totalthemecore_init_class( 'Vcex\Term_Query', $atts, $shortcode_tag );
	if ( $term_query ) {
		$terms = $term_query->get_terms();
		if ( $terms && ! is_wp_error( $terms ) ) {
			return $terms;
		}
	}
}

/**
 * Build Query.
 *
 * @todo move $wp_query checks to the Query_Builder class.
 */
function vcex_build_wp_query( $atts = [], $shortcode_tag = '', $fields = '' ) {
	// Auto query (for all elements excerpt Post Cards).
	if ( vcex_validate_att_boolean( 'auto_query', $atts ) ) {

		// Relevanssi fix for search results.
		if ( function_exists( 'relevanssi_do_query' ) && is_search() ) {
			global $wp_query;
			return $wp_query;
		}

		// Return current query.
		if ( ! vcex_validate_att_boolean( 'featured_card', $atts )
			&& ! is_admin() // this should return true for ajax so technically we don't need the extra checks below?
			&& ! vcex_vc_is_inline()
			&& ! vcex_doing_loadmore()
			&& ! vcex_doing_ajax()
		) {
			global $wp_query;
			if ( $wp_query && $wp_query instanceof WP_Query ) {
				$page      = get_query_var( 'paged' ) ?: 1;
				$max_pages = $wp_query->max_num_pages ?? 0;
				if ( $page >= $max_pages ) {
					return $wp_query;
				}
			}
		}

	}

	// Custom query.
	if ( class_exists( 'TotalThemeCore\Vcex\Query_Builder' ) ) {
		return (new TotalThemeCore\Vcex\Query_Builder( $atts, $shortcode_tag, $fields ))->build();
	}
}

/**
 * Get shortcode custom css class.
 */
function vcex_vc_shortcode_custom_css_class( $css = '' ): string {
	if ( $css && function_exists( 'vc_shortcode_custom_css_class' ) ) {
		$css = (string) vc_shortcode_custom_css_class( $css );
	}
	return $css ? trim( $css ) : '';
}

/**
 * Returns inline style tag based on css properties.
 */
function vcex_inline_style( $properties = [], $add_style_tag = true ) {
	if ( is_array( $properties )
		&& class_exists( 'TotalThemeCore\Vcex\Inline_Style' )
		&& $properties = array_filter( $properties )
	) {
		return (new TotalThemeCore\Vcex\Inline_Style( $properties, $add_style_tag ))->return_style();
	}
}

/**
 * Return post id.
 */
function vcex_get_the_ID() {
	return get_the_ID();
}

/**
 * Check if context is card.
 */
function vcex_is_card(): bool {
	return function_exists( 'totaltheme_is_card' ) && totaltheme_is_card();
}

/**
 * Get card instance.
 */
function vcex_card_instance() {
	return function_exists( 'totaltheme_card_instance' ) ? totaltheme_card_instance() : false;
}

/**
 * Returns meta values (post or term).
 */
function vcex_get_meta_value( $selector, $id = false, $format_value = true ) {
	if ( function_exists( 'acf_is_field_key' ) && acf_is_field_key( $selector ) ) {
		return vcex_get_acf_field( $selector, $id, $format_value );
	}
	if ( ! $id ) {
		if ( ! vcex_is_card() && ! in_the_loop() && ( is_tax() || is_tag() || is_category() ) ) {
			return get_term_meta( get_queried_object_id(), $selector, true );
		}
		$id = get_the_id();
	}
	return get_post_meta( $id, $selector, true );
}

/**
 * A wrapper function for ACF get_field()
 */
function vcex_get_acf_field( $selector, $id = false, $format_value = true ) {
	if ( function_exists( 'get_field' ) ) {
		$card = vcex_card_instance();
		if ( ! empty( $card->term ) ) {
			$id = $card->term;
		} elseif ( ! $id && ! $card && ! in_the_loop() && ( is_tax() || is_tag() || is_category() ) ) {
			$id = get_queried_object();
		}
		if ( function_exists( 'acf_get_loop' ) && acf_get_loop() ) {
			return get_sub_field( $selector, $format_value, false );
		} else {
			return get_field( $selector, $id, $format_value );
		}
	}
}

/**
 * Returns an attachment ID from a custom field.
 */
function vcex_get_meta_value_attachment_id( $selector, $id = false ) {
	$attachment = vcex_get_meta_value( $selector, $id );
	if ( ! is_numeric( $attachment ) ) {
		if ( \is_array( $attachment ) ) {
			$attachment = $attachment['ID'] ?? $attachment['id'] ?? null;
		} elseif ( \is_string( $attachment ) ) {
			$attachment = \attachment_url_to_postid( $attachment );
		}
	}
	return (int) $attachment;
}

/**
 * Return post title.
 */
function vcex_get_the_title() {
	if ( function_exists( 'totaltheme_call_non_static' ) ) {
		$card = vcex_card_instance();
		if ( $card ) {
			if ( ! empty( $card->term ) ) {
				return $card->term->name ?? '';
			}
			$post_id = get_the_ID();
		} elseif ( in_the_loop() ) {
			$post_id = get_the_ID();
		} else {
			$post_id = function_exists( 'wpex_get_dynamic_post_id' ) ? wpex_get_dynamic_post_id() : '';
		}
		if ( $post_id ) {
			return totaltheme_call_non_static( 'Title', 'get_unfiltered_post_title', $post_id );
		} else {
			return totaltheme_call_non_static( 'Title', 'get' );
		}
	}
	return get_the_title();
}

/**
 * Return post permalink.
 */
function vcex_get_permalink( $post_id = '' ) {
	return function_exists( 'wpex_get_permalink' ) ? wpex_get_permalink( $post_id ) : get_permalink();
}

/**
 * Return post class.
 */
function vcex_get_post_class( $class = '', $post_id = null ) {
	return 'class="' . esc_attr( implode( ' ', get_post_class( $class, $post_id ) ) ) . '"';
}

/**
 * Get module header output.
 */
function vcex_get_module_header( $args = [] ) {
	if ( function_exists( 'wpex_get_heading' ) ) {
		$header = wpex_get_heading( $args );
	} else {
		$header = '<h2 class="vcex-module-heading">' . do_shortcode( wp_kses_post( $args['content'] ) ) . '</h2>';
	}
	return (string) apply_filters( 'vcex_get_module_header', $header, $args );
}

/**
 * Returns entry image overlay output.
 */
function vcex_get_entry_image_overlay( $position = '', $shortcode_tag = '', $atts = '' ) {
	if ( empty( $atts['overlay_style'] ) || 'none' === $atts['overlay_style'] ) {
		return '';
	}
	ob_start();
		vcex_image_overlay( $position, $atts['overlay_style'], $atts );
	$overlay = ob_get_clean();
	return apply_filters( 'vcex_entry_image_overlay', $overlay, $position, $shortcode_tag, $atts );
}

/**
 * Return post content.
 */
function vcex_the_content( $content = '', $context = '' ) {
	if ( ! empty( $content ) ) {
		if ( function_exists( 'totaltheme_render_content' ) ) {
			return totaltheme_render_content( $content, $context );
		} else {
			return do_shortcode( shortcode_unautop( wpautop( wp_kses_post( $content ) ) ) );
		}
	}
}

/**
 * Return escaped post title.
 */
function vcex_esc_title( $post = '' ) {
	return the_title_attribute( [
		'echo' => false,
		'post' => $post,
	] );
}

/**
 * Wrapper for esc_attr with fallback.
 */
function vcex_esc_attr( $val = null, $fallback = null ) {
	if ( ! $val ) {
		$val = $fallback;
	}
	return esc_attr( $val );
}

/**
 * Wrapper for the wpex_get_star_rating function.
 */
function vcex_get_star_rating( $rating = '', $post_id = '', $before = '', $after = '' ) {
	if ( function_exists( 'wpex_get_star_rating' ) ) {
		return wpex_get_star_rating( $rating, $post_id, $before, $after );
	}
}

/**
 * Wrapper for the vcex_get_user_social_links function.
 */
function vcex_get_user_social_links( $user_id = '', $display = 'icons', $attr = '', $before = '', $after = '' ) {
	if ( function_exists( 'wpex_get_user_social_links' ) ) {
		return wpex_get_user_social_links( $user_id, $display, $attr, $before, $after );
	}
}

/**
 * Wrapper for the wpex_get_social_button_class function.
 */
function vcex_get_social_button_class( $style = 'default' ) {
	if ( function_exists( 'wpex_get_social_button_class' ) ) {
		return wpex_get_social_button_class( $style );
	}
}

/**
 * Get image filter class.
 */
function vcex_image_filter_class( $filter = '' ) {
	if ( function_exists( 'wpex_image_filter_class' ) ) {
		return wpex_image_filter_class( $filter );
	}
}

/**
 * Get image hover classes.
 */
function vcex_image_hover_classes( $hover = '' ) {
	if ( function_exists( 'wpex_image_hover_classes' ) ) {
		return wpex_image_hover_classes( $hover );
	}
}

/**
 * Get image overlay classes.
 */
function vcex_image_overlay_classes( $style = '' ) {
	if ( function_exists( 'totaltheme_call_static' ) ) {
		return (string) totaltheme_call_static(
			'Overlays',
			'get_parent_class',
			$style
		);
	}
}

/**
 * Return image overlay.
 */
function vcex_get_image_overlay( string $position = '', string $style = '', array $atts = [] ): string {
	ob_start();
		vcex_image_overlay( $position, $style, $atts );
	return (string) ob_get_clean();
}

/**
 * Echo image overlay.
 */
function vcex_image_overlay( string $position = '', string $style = '', array $atts = [] ) {
	if ( function_exists( 'totaltheme_render_overlay' ) ) {
		totaltheme_render_overlay( $position, $style, $atts );
	}
}

/**
 * Return button classes.
 */
function vcex_get_button_classes( $style = '', $color = '', $size = '', $align = '' ): string {
	if ( function_exists( 'wpex_get_button_classes' ) ) {
		return (string) wpex_get_button_classes( $style, $color, $size, $align );
	} else {
		return '';
	}
}

/**
 * Return after media content.
 */
function vcex_get_entry_media_after( $instance = '' ) {
	return apply_filters( 'wpex_get_entry_media_after', '', $instance );
}

/**
 * Return excerpt.
 */
function vcex_get_excerpt( $args = '' ) {
	if ( function_exists( 'totaltheme_get_post_excerpt' ) ) {
		return totaltheme_get_post_excerpt( $args );
	} else {
		return get_the_excerpt();
	}
}

/**
 * Return thumbnail.
 */
function vcex_get_post_thumbnail( $args = '' ) {
	if ( function_exists( 'wpex_get_post_thumbnail' ) ) {
		return wpex_get_post_thumbnail( $args );
	} else {
		return get_the_post_thumbnail( 'full' );
	}
}

/**
 * Return WooCommerce price
 */
function vcex_get_woo_product_price( $post_id = '' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( 'product' == get_post_type( $post_id ) ) {
		$product = wc_get_product( $post_id );
		$price = $product->get_price_html();
		if ( $price ) {
			return $price;
		}
	}
}

/**
 * Return button arrow.
 *
 * @todo deprecate.
 */
function vcex_readmore_button_arrow() {
	$arrow = is_rtl() ? '&larr;' : '&rarr;';
	return (string) apply_filters( 'wpex_readmore_button_arrow', $arrow );
}

/**
 * Return font weight class
 */
function vcex_font_weight_class( $font_weight = '' ): string {
	$font_weights = [
		'hairline'  => 'wpex-font-hairline',
		'100'       => 'wpex-font-hairline',
		'thin'      => 'wpex-font-thin',
		'200'       => 'wpex-font-thin',
		'normal'    => 'wpex-font-normal',
		'400'       => 'wpex-font-normal',
		'medium'    => 'wpex-font-medium',
		'500'       => 'wpex-font-medium',
		'semibold'  => 'wpex-font-semibold',
		'600'       => 'wpex-font-semibold',
		'bold'      => 'wpex-font-bold',
		'700'       => 'wpex-font-bold',
		'extrabold' => 'wpex-font-extrabold',
		'bolder'    => 'wpex-font-extrabold',
		'800'       => 'wpex-font-extrabold',
		'black'     => 'wpex-font-black',
		'900'       => 'wpex-font-black',
	];
	return $font_weights[ $font_weight ] ?? '';
}

/**
 * Get theme term data.
 */
function vcex_get_term_data() {
	if ( function_exists( 'wpex_get_term_data' ) ) {
		return wpex_get_term_data();
	}
}

/**
 * Get term thumbnail.
 */
function vcex_get_term_thumbnail_id( $term = '' ) {
	if ( function_exists( 'wpex_get_term_thumbnail_id' ) ) {
		return wpex_get_term_thumbnail_id( $term );
	}
}

/**
 * Get post video.
 */
function vcex_get_post_video( $post_id = '' ) {
	if ( function_exists( 'wpex_get_post_video' ) ) {
		return wpex_get_post_video( $post_id );
	}
}

/**
 * Get post video html.
 */
function vcex_get_post_video_html() {
	if ( function_exists( 'wpex_get_post_video_html' ) ) {
		return wpex_get_post_video_html();
	}
}

/**
 * Get post video html.
 */
function vcex_video_oembed( $video = '', $classes = '', $params = [] ) {
	if ( function_exists( 'wpex_video_oembed' ) ) {
		return wpex_video_oembed( $video, $classes, $params );
	}
}

/**
 * Get post video oembed URL.
 */
function vcex_get_post_video_oembed_url( $post_id = '' ) {
	if ( function_exists( 'wpex_get_post_video_oembed_url' ) ) {
		return wpex_get_post_video_oembed_url( $post_id );
	}
}

/**
 * Get post video oembed URL.
 */
function vcex_get_video_embed_url( $video = '' ) {
	if ( $video && function_exists( 'wpex_get_video_embed_url' ) ) {
		return wpex_get_video_embed_url( $video );
	}
}

/**
 * Get hover animation class
 */
function vcex_hover_animation_class( $animation = '' ) {
	if ( function_exists( 'wpex_hover_animation_class' ) && $class = wpex_hover_animation_class( $animation ) ) {
		wp_enqueue_style( 'wpex-hover-animations' );
		return $class;
	}
}

/**
 * Get first post term.
 */
function vcex_get_first_term( $post = '', $taxonomy = 'category', $terms = '' ) {
	if ( function_exists( 'totaltheme_get_post_primary_term' ) ) {
		$first_term = totaltheme_get_post_primary_term( $post, $taxonomy );
	} else {
		$post_terms = get_the_terms( $post, $taxonomy );
		if ( $post_terms && ! is_wp_error( $post_terms ) ) {
			$first_term = $post_terms[0] ?? '';
		}
	}
	if ( $first_term ) {
		return '<span class="' . esc_attr( 'term-' . absint( $first_term->term_id ) ) . '">' . esc_html( $first_term->name ) . '</span>';
	}
}

/**
 * Get post first term link.
 */
function vcex_get_first_term_link( $post = '', $taxonomy = 'category', $terms = '' ) {
	if ( function_exists( 'wpex_get_first_term_link' ) ) {
		return wpex_get_first_term_link( $post, $taxonomy, $terms );
	} else {
		$post_terms = get_the_terms( $post, $taxonomy );
		if ( $post_terms && ! is_wp_error( $post_terms ) && ! empty( $post_terms[0] ) ) {
			if ( $term_link = get_term_link( $post_terms[0], $taxonomy ) ) {
				return '<a href="' . esc_url( $term_link ) . '" class="' . esc_attr( 'term-' . absint( $post_terms[0]->term_id ) ) . '">' . esc_html( $post_terms[0]->name ) . '</a>';
			} else {
				return '<span class="' . esc_attr( 'term-' . absint( $post_terms[0]->term_id ) ) . '">' . esc_html( $post_terms[0]->name ) . '</span>';
			}
		}
	}
}

/**
 * Get post terms.
 */
function vcex_get_list_post_terms( $taxonomy = 'category', $show_links = true ) {
	if ( function_exists( 'wpex_get_list_post_terms' ) ) {
		return wpex_get_list_post_terms( $taxonomy, $show_links );
	}
}

/**
 * Checks if shortcode has pagination.
 */
function vcex_shortcode_has_pagination( $atts, $vcex_query ) {
	if ( isset( $atts['custom_query'] )
		&& vcex_validate_boolean( $atts['custom_query'] )
		&& ! empty( $vcex_query->query['pagination'] )
	) {
		$check = true;
	} else {
		$check = vcex_validate_att_boolean( 'pagination', $atts, false );
	}
	return $check;
}

/**
 * Get pagination.
 */
if ( ! function_exists( 'vcex_pagination' ) ) {
	function vcex_pagination( $query = '', $echo = true ) {
		if ( class_exists( 'TotalTheme\Pagination\Standard' ) ) {
			$pagination = new \TotalTheme\Pagination\Standard( $query );
			if ( $echo ) {
				$pagination->render();
			} else {
				ob_start();
					$pagination->render();
				return ob_get_clean();
			}
		} else {
			if ( $query ) {
				global $wp_query;
				$temp_query = $wp_query;
				$wp_query = $query;
			}
			ob_start();
			posts_nav_link();
			$wp_query = $temp_query;
			return ob_get_clean();
		}
	}
}

/**
 * Filters module grid to return active blocks.
 */
function vcex_filter_grid_blocks_array( $blocks ) {
	$new_blocks = [];
	foreach ( $blocks as $key => $value ) {
		if ( 'true' == $value ) {
			$new_blocks[$key] = '';
		}
	}
	return $new_blocks;
}

/**
 * Returns classes for grid elements.
 *
 * Does NOT use post_class to prevent conflicts.
 */
function vcex_grid_get_post_class( $classes = [], $post_id = '', $media_check = true ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post_type = get_post_type( $post_id );

	$classes[] = 'entry';
	$classes[] = 'post-' . sanitize_html_class( $post_id );
	$classes[] = 'type-' . sanitize_html_class( $post_type );

	// Add has media class
	if ( $media_check && function_exists( 'wpex_post_has_media' ) ) {
		if ( wpex_post_has_media( $post_id, true ) ) {
			$classes[] = 'has-media';
		} else {
			$classes[] = 'no-media';
		}
	}

	// Add terms classes
	$terms = vcex_get_post_term_classes( $post_id, $post_type );
	if ( $terms && is_array( $terms ) ) {
		foreach ( $terms as $term_class ) {
			if ( ! in_array( $term_class, $classes, true ) ) {
				$classes[] = $term_class;
			}
		}
	}

	// Custom link class
	if ( function_exists( 'wpex_get_post_redirect_link' ) && wpex_get_post_redirect_link() ) {
		$classes[] = 'has-redirect';
	}

	$classes = (array) apply_filters( 'vcex_grid_get_post_class', $classes );
	$classes = array_filter( array_unique( $classes ) );
	$classes = array_map( 'esc_attr', $classes );
	return 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
}

/**
 * Returns entry classes for vcex module entries.
 */
function vcex_get_post_term_classes( $post_id = '', $post_type = '' ) {
	if ( ! defined( 'TOTAL_THEME_ACTIVE' ) ) {
		return [];
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( ! $post_type ) {
		$post_type = get_post_type( $post_id );
	}

	$classes = [];
	$taxonomies = get_object_taxonomies( $post_type, 'names' );

	if ( is_wp_error( $taxonomies ) ) {
		return;
	}

	$taxonomies = (array) apply_filters( 'vcex_post_term_classes_taxonomies', $taxonomies );

	if ( ! $taxonomies ) {
		return;
	}

	$theme_cpts = (array) vcex_theme_post_types();

	foreach ( $taxonomies as $tax ) {
		$terms = get_the_terms( $post_id, $tax );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$prefix = $term->taxonomy;
				if ( $prefix ) {
					if ( 'category' === $prefix ) {
						$prefix = 'cat';
					} elseif ( $theme_cpts && in_array( $post_type, $theme_cpts, true ) ) {
						$prefix = str_replace( [ "{$post_type}_category", "{$post_type}_tag" ], [ 'cat', 'tag' ], $prefix );
					}
					$classes[] = sanitize_html_class( "{$prefix}-{$term->term_id}" );
					if ( $term->parent ) {
						$classes[] = sanitize_html_class( "{$prefix}-{$term->parent}" );
					}
				}
			}
		}
	}

	return $classes;
}

/**
 * Returns correct class for columns.
 */
function vcex_get_grid_column_class( $atts ) {
	if ( isset( $atts['single_column_style'] ) && 'left_thumbs' === $atts['single_column_style'] ) {
		return 'span_1_of_1';
	}
	$return_class = '';
	if ( ! empty( $atts['columns'] ) ) {
		$return_class .= ' span_1_of_' . sanitize_html_class( $atts['columns'] );
	}
	if ( ! empty( $atts['columns_responsive_settings'] ) ) {
		$rs = vcex_parse_multi_attribute( $atts['columns_responsive_settings'], [] );
		foreach ( $rs as $key => $val ) {
			if ( $val ) {
				$return_class .= ' span_1_of_' . sanitize_html_class( $val ) . '_' . sanitize_html_class( $key );
			}
		}
	}
	return trim( $return_class );
}

/**
 * Get carousel settings.
 */
function vcex_get_carousel_settings( $atts, $shortcode, $json = true ) {
	if ( $json ) {
		return totalthemecore_call_static( 'Vcex\Carousel\Core', 'get_settings_json', $atts, $shortcode );
	}
	return totalthemecore_call_static( 'Vcex\Carousel\Core', 'get_settings', $atts, $shortcode );
}

/**
 * Parses carousel settings and returns json string.
 */
function vcex_carousel_settings_to_json( $settings = [] ) {
	return totalthemecore_call_static( 'Vcex\Carousel\Core', 'to_json', $settings );
}

/**
 * Get carousel inline CSS.
 */
function vcex_get_carousel_inline_css( $class = '', $settings = [] ) {
	$check = (bool) apply_filters( 'vcex_optimize_carousels_onload', true, $settings );
	if ( $check && class_exists( 'TotalThemeCore\Vcex\Carousel\Inline_CSS' ) ) {
		ob_start();
			(new TotalThemeCore\Vcex\Carousel\Inline_CSS( $class, $settings ) )->render();
		return ob_get_clean();
	}
}

/**
 * Returns animation class and loads animation js.
 */
function vcex_get_css_animation( $css_animation = '' ): string {
	if ( ! $css_animation || 'none' === $css_animation ) {
		return '';
	}

	wp_enqueue_script( 'wpex-vc_waypoints' );
	wp_enqueue_script( 'vc_waypoints' );
	wp_enqueue_style( 'vc_animate-css' );

	$css_animation_safe = sanitize_html_class( $css_animation );
	$css_animation_class = " wpb_animate_when_almost_visible wpb_{$css_animation_safe} {$css_animation_safe}";

	return $css_animation_class;
}

/**
 * Return unique ID for responsive class.
 *
 * @todo deprecate | no longer used.
 */
function vcex_get_reponsive_unique_id( $unique_id = '' ) {
	return $unique_id ? "wpex-{$unique_id}" : uniqid( 'wpex-' );
}

/**
 * Return responsive font-size data.
 *
 * @deprecated Since 5.2 in exchange for inline style tags.
 * @todo deprecate completely.
 */
function vcex_get_responsive_font_size_data( $value ) {
	if ( ! $value ) {
		return;
	}
	if ( ! str_contains( $value, '|' ) ) {
		return;
	}
	$data = vcex_parse_multi_attribute( $value );
	if ( ! $data && ! is_array( $data ) ) {
		return;
	}
	wp_enqueue_script( 'vcex-responsive-css' );
	$sanitized_data = [];
	foreach ( $data as $key => $val ) {
		$sanitized_data[$key] = vcex_validate_font_size( $val, 'font_size' );
	}
	return $sanitized_data;
}

/**
 * Return responsive font-size data.
 *
 * @deprecated Since 5.2 in exchange for inline style tags.
 * @todo deprecate completely.
 */
function vcex_get_module_responsive_data( $atts, $type = '' ) {
	if ( ! $atts ) {
		return; // No need to do anything if atts is empty
	}
	wp_enqueue_script( 'vcex-responsive-css' );
	$return = [];
	$parsed_data = [];
	$settings = [ 'font_size' ];
	if ( $type && ! is_array( $atts ) ) {
		$settings = [ $type ];
		$atts = [ $type => $atts ];
	}
	foreach ( $settings as $setting ) {
		if ( 'font_size' === $setting ) {
			$value = $atts['font_size'] ?? '';
			if ( ! $value ) {
				break;
			}
			$value = vcex_get_responsive_font_size_data( $value );
			if ( $value ) {
				$parsed_data['font-size'] = $value;
			}
		}
	}
	if ( $parsed_data ) {
		return "data-wpex-rcss='" . htmlspecialchars( wp_json_encode( $parsed_data ) ) . "'";
	}
}

/**
 * Get unique element classname.
 */
function vcex_element_unique_classname( $prefix = 'vcex' ) {
	return sanitize_html_class( uniqid( $prefix . '_' ) );
}

/**
 * Get responsive CSS for given element.
 *
 * @todo deprecated!
 */
function vcex_element_responsive_css( $atts = [], $target = '' ) {
	if ( ! $atts || ! $target ) {
		return;
	}

	$css      = '';
	$css_list = [];

	$target = sanitize_text_field( $target );
	if ( ! str_starts_with( $target, '.' ) ) {
		$target = '.' . $target;
	}

	if ( ! empty( $atts['font_size'] ) && str_contains( $atts['font_size'], '|' ) ) {
		$font_size = $atts['font_size'];

		// Parse data to return array.
		$font_size_opts = vcex_parse_multi_attribute( $font_size );

		if ( is_array( $font_size_opts ) ) {
			foreach ( $font_size_opts as $font_size_device => $font_size_v ) {
				$safe_font_size = vcex_validate_font_size( $font_size_v );
				if ( $safe_font_size ) {
					$css_list[$font_size_device]['font-size'] = $safe_font_size;
				}
			}
		}
	}

	if ( $css_list ) {
		foreach ( $css_list as $device => $device_properties ) {
			$media_rule = vcex_get_css_media_rule( $device );
			if ( $media_rule ) {
				$css .= $media_rule . '{';
			}
			$css .= $target . '{';
				foreach ( $device_properties as $property_k => $property_v ) {
					$css .= $property_k . ':' . esc_attr( $property_v ) . '!important;';
				}
			$css .= '}';
			if ( $media_rule ) {
				$css .= '}';
			}
		}
	}

	return $css;
}

/**
 * Get responsive CSS from an element attribute.
 */
function vcex_responsive_attribute_css( $attribute = '', $target_element = '', $target_property = '' ) {
	$values = vcex_parse_multi_attribute( $attribute );

	if ( ! is_array( $values ) || empty( $values ) ) {
		return;
	}

	$css = '';
	$safe_target = '.' . sanitize_html_class( $target_element );
	$safe_property = wp_strip_all_tags( trim( $target_property ) );

	foreach ( $values as $device_abrev => $value ) {

		// Get CSS from value, pass through vcex_inline_style for sanitization.
		$bk_css = vcex_inline_style( [
			$target_property => $value,
		], false );

		if ( ! $bk_css ) {
			continue;
		}

		$media_rule = vcex_get_css_media_rule( $device_abrev );

		if ( $media_rule ) {
			$css .= $media_rule . '{';
				$css .= $safe_target . '{';
					$css .= esc_attr( $bk_css );
				$css .= '}';
			$css .= '}';
		} else {
			$css .= $safe_target . '{';
				$css .= esc_attr( $bk_css );
			$css .= '}';
		}

	}

	return $css;
}

/**
 * Return breakpoint widths.
 */
function vcex_get_css_breakpoints() {
	$breakpoints = [
		'tl' => '1024px',
		'tp' => '959px',
		'pl' => '767px',
		'pp' => '479px',
	];
	return (array) apply_filters( 'vcex_css_breakpoints', $breakpoints );
}

/**
 * Return the @media rule for a specific breakpoint.
 */
function vcex_get_css_media_rule( $breakpoint = '' ) {
	if ( ! $breakpoint || 'd' === $breakpoint ) {
		return;
	}
	$breakpoints = vcex_get_css_breakpoints();
	if ( ! empty( $breakpoints[ $breakpoint ] ) ) {
		return '@media (max-width:' . esc_attr( $breakpoints[ $breakpoint ] ) . ')';
	}
}

/**
 * Get Extra class.
 */
function vcex_get_extra_class( $classes = '', $add_sep = false ) {
	$classes = trim( wp_strip_all_tags( $classes ) );
	if ( $classes ) {
		$class_escaped = esc_attr( str_replace( '.', '', $classes ) );
		if ( function_exists( 'totaltheme_replace_vars' ) ) {
			$class_escaped = totaltheme_replace_vars( $class_escaped );
		}
		if ( $add_sep ) {
			$class_escaped = '|| ' . $class_escaped;
		}
		return $class_escaped;
	}
}

/**
 * Generates various types of HTML based on a value.
 *
 * @todo deprecate - Note: it's still being used for some elements.
 */
function vcex_html( $type, $value, $trim = false ) {
	$return = '';

	if ( ! $value ) {
		return;
	}

	// ID attribute.
	if ( 'id_attr' === $type ) {
		$value  = trim ( str_replace( '#', '', $value ) );
		$value  = str_replace( ' ', '', $value );
		if ( $value ) {
			$return = ' id="'. esc_attr( $value ) .'"';
		}
	}

	// Title attribute.
	if ( 'title_attr' === $type ) {
		$return = ' title="'. esc_attr( $value ) .'"';
	}

	// Link Target.
	elseif ( 'target_attr' === $type ) {
		if ( str_contains( $value, 'blank' ) ) {
			$return = ' target="_blank"';
		}
	}

	// Link rel.
	elseif ( 'rel_attr' === $type ) {
		if ( 'nofollow' === $value ) {
			$return = ' rel="nofollow"';
		}
	}

	if ( $trim ) {
		return trim( $return );
	} else {
		return $return;
	}
}

/**
 * Notice when no posts are found.
 */
function vcex_no_posts_found_message( $atts = [] ) {
	if ( vcex_doing_loadmore() ) {
		return;
	}

	if ( ! empty( $atts['no_posts_found_message'] ) ) {
		return '<div class="vcex-no-posts-found">' . esc_html( $atts['no_posts_found_message'] ) . '</div>';
	}

	$check = vcex_vc_is_inline() || vcex_validate_att_boolean( 'auto_query', $atts );
	$check = (bool) apply_filters( 'vcex_has_no_posts_found_message', $check, $atts );

	if ( $check ) {
		$message = '<div class="vcex-no-posts-found">' . esc_html__( 'Nothing found.', 'total-theme-core' ) . '</div>';
	} else {
		$message = '';
	}

	return (string) apply_filters( 'vcex_no_posts_found_message', $message, $atts );
}

/**
 * Echos unique ID html for VC modules.
 */
function vcex_unique_id( $id = '' ) {
	echo vcex_get_unique_id( $id );
}

/**
 * Returns unique ID html for VC modules.
 */
function vcex_get_unique_id( $id = '' ) {
	if ( is_array( $id ) ) {
		$id = $id['unique_id'] ?? '';
	}
	if ( $id && is_string( $id ) ) {
		return ' id="' . esc_attr( trim( $id ) ) . '"'; // do not remove empty space at front!!
	}
}

/**
 * Returns lightbox image.
 * 
 * @important Default value must be set to NULL.
 */
function vcex_get_lightbox_image( $thumbnail_id = null ) {
	if ( function_exists( 'wpex_get_lightbox_image' ) ) {
		return wpex_get_lightbox_image( $thumbnail_id );
	}
}

/**
 * Returns term color.
 */
function vcex_get_term_color( $term ) {
	if ( class_exists( 'TotalThemeCore\Term_Colors', false )
		&& is_callable( 'TotalThemeCore\Term_Colors::get_term_color' )
		&& $term_obj = get_term( $term )
	) {
		return TotalThemeCore\Term_Colors::get_term_color( $term_obj );
	}
}

/**
 * Returns attachment data
 */
function vcex_get_attachment_data( $attachment = '', $return = 'array' ) {
	if ( function_exists( 'wpex_get_attachment_data' ) ) {
		return wpex_get_attachment_data( $attachment, $return );
	} elseif ( $attachment && 'none' !== $attachment ) {
		$data = [
			'url'         => wp_get_attachment_url( $attachment ),
			'src'         => wp_get_attachment_url( $attachment ), // fallback
			'alt'         => get_post_meta( $attachment, '_wp_attachment_image_alt', true ),
			'title'       => get_the_title( $attachment ),
			'caption'     => wp_get_attachment_caption( $attachment ),
			'description' => get_post_field( 'post_content', $attachment ),
			'video'       => false,
		];
		if ( 'array' === $return ) {
			return $data;
		} else{
			return $data[ $return ] ?? '';
		}
	}
}

/**
 * Returns post gallery ID's
 */
function vcex_get_post_gallery_ids( $post_id = '', $fallback = '' ) {

	/**
	 * Filters the post gallery image ids before trying to fetch them.
	 *
	 * @param string|array $ids
	 */
	$filter_val = apply_filters( 'vcex_pre_get_post_gallery_ids', null );

	if ( ! empty( $filter_val ) ) {
		return $filter_val;
	}

	if ( function_exists( 'wpex_get_gallery_ids' ) ) {
		$attachment_ids = wpex_get_gallery_ids( $post_id );
		if ( ! $attachment_ids && vcex_is_template_edit_mode() ) {
			if ( $fallback ) {
				return $fallback;
			}
		}
		return $attachment_ids;
	}
}

/**
 * Helper function for building links using link param.
 */
function vcex_build_link( $link, $fallback = '' ) {
	if ( empty( $link ) ) {
		return $fallback;
	}

	// Most likely an elementor link.
	if ( is_array( $link ) ) {
		return $link;
	}

	// Return if there isn't any link caused by editor bug saving only pipes.
	if ( '||' === $link || '|||' === $link || '||||' === $link ) {
		return;
	}

	// Return simple link escaped (fallback for old textfield input).
	if ( ! str_contains( $link, 'url:' ) ) {
		return $link;
	}

	// Build link.
	// Needs to use total function to fix issue with fallbacks.
	$link = vcex_parse_multi_attribute( $link, [
		'url'    => '',
		'title'  => '',
		'target' => '',
		'rel'    => '',
	] );

	return is_array( $link ) ? array_map( 'trim', $link ) : '';
}

/**
 * Returns link data (used for fallback link settings).
 */
function vcex_get_link_data( $return, $link, $fallback = '' ) {
	$link = vcex_build_link( $link, $fallback );

	if ( 'url' === $return ) {
		if ( is_array( $link ) && ! empty( $link['url'] ) ) {
			return $link['url'];
		} else {
			return is_array( $link ) ? $fallback : $link;
		}
	}

	if ( 'title' === $return ) {
		if ( is_array( $link ) && ! empty( $link['title'] ) ) {
			return $link['title'];
		} else {
			return $fallback;
		}
	}

	if ( 'target' === $return ) {
		if ( is_array( $link ) ) {
			if ( ! empty( $link['target'] ) ) {
				return $link['target'];
			} elseif ( isset( $link['is_external'] ) && 'on' === $link['is_external'] ) {
				return 'blank';
			}
		} else {
			return $fallback;
		}
	}

	if ( 'rel' === $return ) {
		if ( is_array( $link ) ) {
			if ( ! empty( $link['rel'] ) ) {
				return $link['rel'];
			} elseif ( isset( $link['nofollow'] ) && 'on' === $link['nofollow'] ) {
				return 'nofollow';
			}
		} else {
			return $fallback;
		}
	}
}

/**
 * Get source value.
 */
function vcex_get_source_value( $source = '', $atts = [] ) {
	if ( $source && class_exists( 'TotalThemeCore\Vcex\Source_Value' ) ) {
		return (new TotalThemeCore\Vcex\Source_Value( $source, $atts ))->get_value();
	}
}

/**
 * Return shortcode CSS.
 *
 * @todo rename to vcex_get_wpb_shortcodes_custom_css
 */
function vcex_wpb_shortcodes_custom_css( $post_id = '' ) {
	$meta = get_post_meta( $post_id, '_wpb_shortcodes_custom_css', true );
	if ( $meta ) {
		return '<style data-type="vc_shortcodes-custom-css">' . wp_strip_all_tags( $meta ) . '</style>';
	}
}

/**
 * Get shortcode style classes based on global params.
 */
function vcex_get_shortcode_extra_classes( array $atts = [], string $shortcode_tag = '' ) {
	if ( empty( $atts ) ) {
		return [];
	}

	$extra_classes = [];

	if ( ! empty( $atts['text_align'] ) ) {
		$extra_classes[] = vcex_parse_text_align_class( $atts['text_align'] );
	}

	if ( ! empty( $atts['font_size'] ) && ! in_array( $shortcode_tag, [ 'vcex_page_title', 'vcex_post_meta' ] ) ) {
		$extra_classes[] = vcex_parse_font_size_class( $atts['font_size'] );
	}

	if ( ! empty( $atts['bottom_margin'] ) ) {
		$extra_classes[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
	}

	if ( ! empty( $atts['padding_all'] ) && ! in_array( $shortcode_tag, [ 'vcex_list_item' ], true ) ) {
		$extra_classes[] = vcex_parse_padding_class( $atts['padding_all'] );
	}

	if ( ! empty( $atts['padding_y'] ) ) {
		$extra_classes[] = vcex_parse_padding_class( $atts['padding_y'], 'block' );
	}

	if ( ! empty( $atts['padding_x'] ) ) {
		$extra_classes[] = vcex_parse_padding_class( $atts['padding_x'], 'inline' );
	}

	if ( ! empty( $atts['border_style'] ) ) {
		$extra_classes[] = vcex_parse_border_style_class( $atts['border_style'] );
	}

	if ( isset( $atts['border_width'] ) ) {
		$extra_classes[] = vcex_parse_border_width_class( $atts['border_width'] );
	}

	if ( isset( $atts['border_radius'] ) ) {
		$extra_classes[] = vcex_parse_border_radius_class( $atts['border_radius'] );
	}

	if ( ! empty( $atts['visibility'] ) ) {
		$extra_classes[] = vcex_parse_visibility_class( $atts['visibility'] );
	}

	if ( ! empty( $atts['shadow'] ) ) {
		$extra_classes[] = vcex_parse_shadow_class( $atts['shadow'] );
	}

	if ( ! empty( $atts['shadow_hover'] ) ) {
		$extra_classes[] = vcex_parse_shadow_class( $atts['shadow_hover'], 'hover' );
	}

	if ( ! empty( $atts['css_animation'] ) ) {
		$extra_classes[] = vcex_get_css_animation( $atts['css_animation'] );
	}

	if ( ! empty( $atts['css'] ) ) {
		$extra_classes[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
	}

	if ( ! empty( $atts['el_class'] ) ) {
		$extra_classes[] = vcex_get_extra_class( $atts['el_class'] );
	} elseif ( ! empty( $atts['classes'] ) ) {
		$extra_classes[] = vcex_get_extra_class( $atts['classes'] );
	}

	return array_filter( $extra_classes );
}

/**
 * Returns array of carousel settings.
 */
function vcex_vc_map_carousel_settings( $dependency = [], $group = '' ) {
	return totalthemecore_call_static( 'Vcex\Carousel\Core', 'get_shortcode_params', $dependency, $group ) ?: [];
}

/**
 * Returns array for adding CSS Animation to VC modules.
 */
function vcex_vc_map_add_css_animation( $args = [] ) {
	// Fallback pre VC 5.0
	if ( ! function_exists( 'vc_map_add_css_animation' ) ) {
		$animations = apply_filters( 'wpex_css_animations', [
			''              => esc_html__( 'None', 'total-theme-core'),
			'top-to-bottom' => esc_html__( 'Top to bottom', 'total-theme-core' ),
			'bottom-to-top' => esc_html__( 'Bottom to top', 'total-theme-core' ),
			'left-to-right' => esc_html__( 'Left to right', 'total-theme-core' ),
			'right-to-left' => esc_html__( 'Right to left', 'total-theme-core' ),
			'appear'        => esc_html__( 'Appear from center', 'total-theme-core' ),
		] );
		return [
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Appear Animation', 'total-theme-core' ),
			'param_name' => 'css_animation',
			'value'      => array_flip( $animations ),
			'dependency' => [ 'element' => 'filter', 'value' => 'false' ],
			'editors'    => [ 'wpbakery' ],
		];
	}
	// New since VC 5.0.
	$defaults = [
		'type'       => 'animation_style',
		'heading'    => esc_html__( 'CSS Animation', 'total-theme-core' ),
		'param_name' => 'css_animation',
		'value'      => 'none',
		'std'        => 'none',
		'settings'   => [
			'type'   => 'in',
			'custom' => [
				[
					'label'  => esc_html__( 'Default', 'total-theme-core' ),
					'values' => [
						esc_html__( 'Top to bottom', 'total-theme-core' ) => 'top-to-bottom',
						esc_html__( 'Bottom to top', 'total-theme-core' ) => 'bottom-to-top',
						esc_html__( 'Left to right', 'total-theme-core' ) => 'left-to-right',
						esc_html__( 'Right to left', 'total-theme-core' ) => 'right-to-left',
						esc_html__( 'Appear from center', 'total-theme-core' ) => 'appear',
					],
				],
			],
		],
		'description' => esc_html__( 'Select a CSS animation for when the element "enters" the browser\'s viewport. Note: Animations will not work with grid filters as it creates a conflict with re-arranging items.', 'total-theme-core' ),
		'editors' => [ 'wpbakery' ],
	];
	$args = wp_parse_args( $args, $defaults );
	return (array) apply_filters( 'vc_map_add_css_animation', $args );
}

/**
 * Custom field placeholder.
 */
function vcex_custom_field_placeholder( $field_name = '' ) {
	if ( function_exists( 'get_field_object' ) && str_starts_with( $field_name, 'field_' ) ) {
		$field_obj = get_field_object( $field_name );
		if ( ! empty( $field_obj['default'] ) ) {
			return $field_obj['default'];
		}
		$field_type = $field_obj['type'] ?? 'text';
		if ( 'number' === $field_type ) {
			return '5';
		} else {
			$label = $field_obj['label'] ?? $field_obj['name'] ?? $field_name;
			return "ACF - {$label}";
		}
	} else {
		$vals = [
			'wpex_post_rating'         => '5',
			'wpex_post_title'          => esc_html__( 'Custom Page Title', 'total-theme-core' ),
			'wpex_post_subheading'     => esc_html__( 'Page Subheading', 'total-theme-core' ),
			'wpex_callout_text'        => esc_html__( 'Callout Text', 'total-theme-core' ),
			'wpex_portfolio_budget'    => esc_html__( 'Budget', 'total-theme-core' ),
			'wpex_portfolio_company'   => esc_html__( 'Company Name', 'total-theme-core' ),
			'wpex_staff_position'      => esc_html__( 'Position', 'total-theme-core' ),
			'wpex_testimonial_author'  => esc_html__( 'Author', 'total-theme-core' ),
			'wpex_testimonial_company' => esc_html__( 'Company', 'total-theme-core' ),
		];
		if ( isset( $vals ) && array_key_exists( $field_name, $vals ) ) {
			return $vals[ $field_name ];
		}
	}
	return esc_html__( 'Custom field placeholder', 'total-theme-core' );
}

/**
 * Returns image from source.
 */
function vcex_get_image_from_source( string $source = '', array $atts = [], bool $fallback = false ) {
	if ( class_exists( 'TotalThemeCore\Vcex\Helpers\Get_Image_From_Source' ) ) {
		return (new TotalThemeCore\Vcex\Helpers\Get_Image_From_Source( $source, $atts, $fallback ))->get();
	}
}

/**
 * Returns the post cards query.
 */
function vcex_reset_postdata() {
	$post_cards = totalthemecore_get_instance_of( 'Vcex\Post_Cards' );
	if ( $post_cards && ! empty( $post_cards->query ) ) {
		$post_cards->query->reset_postdata();
	} else {
		wp_reset_postdata();
	}
}

/**
 * Returns icon HTML.
 */
function vcex_get_icon_html( $atts, $icon_location = 'icon', $extra_class = '' ) {
	$icon_attrs = [];

	if ( $extra_class ) {
		if ( is_array( $extra_class ) ) {
			$icon_attrs = $extra_class;
		} else {
			$icon_attrs['class'] = $extra_class;
		}
	}

	/*** Elementor Icons ***/
	if ( isset( $atts[ $icon_location ] ) && is_array( $atts[ $icon_location ] ) ) {
		if ( isset( $atts[ $icon_location ]['library'] ) && 'ticon' === $atts[ $icon_location ]['library'] ) {
			if ( ! empty( $atts[ $icon_location ]['value'] ) ) {
				return vcex_get_theme_icon_html( $atts[ $icon_location ]['value'], $icon_attrs );
			}
		} else {
			if ( is_callable( 'Elementor\Icons_Manager::render_icon' ) ) {
				ob_start();
					Elementor\Icons_Manager::render_icon( $atts[ $icon_location ] );
				$icon = ob_get_clean();
				if ( $icon ) {
					if ( ! empty( $icon_attrs['class'] ) ) {
						if ( is_array( $icon_attrs['class'] ) ) {
							array_unshift( $icon_attrs['class'], 'vcex-elementor-icon wpex-icon' );
						} elseif ( is_string( $icon_attrs['class'] ) ) {
							$icon_attrs['class'] =  'vcex-elementor-icon wpex-icon ' . $icon_attrs['class'];
						}
					} else {
						$icon_attrs['class'] = 'vcex-elementor-icon wpex-icon';
					}
					$icon_attrs['aria-hidden'] = 'true';
					return vcex_parse_html( 'span', $icon_attrs, $icon );
				}
			}
		}
	}

	/*** Non-Elementor Icons ***/
	else {
		$icon = '';
		$icon_type = ! empty( $atts['icon_type'] ) ? sanitize_text_field( $atts['icon_type'] ) : 'ticons';

		// Custom icon set for specific library
		// We must exclude ticons because originally there may not have been an option to choose the library type!
		if ( 'ticons' !== $icon_type && ! empty( $atts[ "{$icon_location}_{$icon_type}" ] ) ) {
			$icon = $atts[ "{$icon_location}_{$icon_type}" ];
		}

		// Parse the default icon parameter which could be anything really
		elseif ( ! empty( $atts[ $icon_location ] ) ) {
			$icon = (string) $atts[ $icon_location ];
			$icon_type = vcex_get_icon_type_from_class( $icon ); // we must always make this check!

			// converts old 4.7 fontawesome icons to ticons.
			if ( 'ticons' === $icon_type ) {
				$icon = str_replace( 'fa fa-', '', $icon );
			}
		}

		if ( ! $icon || 'icon' === $icon || 'none' === $icon ) {
			return '';
		}

		if ( 'ticons' === $icon_type ) {
			$icon_html = vcex_get_theme_icon_html( $icon, $icon_attrs );
		} else {
			if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ) {
				vc_icon_element_fonts_enqueue( $icon_type );
			}
			$icon_attrs['aria-hidden'] = 'true';
			if ( isset( $icon_attrs['class'] ) ) {
				$icon_attrs['class'] .= " {$icon}";
			} else {
				$icon_attrs['class'] = $icon;
			}
			$icon_html = '<span ' . trim( vcex_parse_html_attributes( $icon_attrs ) ) . '></span>';
		}

		return $icon_html;
	}
}

/**
 * Returns correct icon family for specific icon class.
 */
function vcex_get_icon_type_from_class( string $icon ): string {
	if ( str_starts_with( $icon, 'ticon' ) || str_contains( $icon, 'fa fa-' ) ) {
		return 'ticons';
	} elseif ( str_contains( $icon, 'fa-' ) ) {
		return 'fontawesome';
	} elseif ( str_contains( $icon, 'vc-oi' ) ) {
		return 'openiconic';
	} elseif ( str_contains( $icon, 'typcn' ) ) {
		return 'typicons';
	} elseif ( str_contains( $icon, 'entypo-icon' ) ) {
		return 'entypo';
	} elseif ( str_contains( $icon, 'vc_li' ) ) {
		return 'linecons';
	} elseif ( str_contains( $icon, 'vc-material' ) ) {
		return 'material';
	} elseif ( str_starts_with( $icon, 'vc-mono' ) ) {
		return 'monosocial';
	} elseif ( str_starts_with( $icon, 'vc_pixel_icon' ) ) {
		return 'pixelicons';
	}
	return 'ticons'; // if all else fails it's a theme icon.
}

/**
 * Check if legacy typography enabled in Total.
 */
function vcex_has_classic_styles(): bool {
	return function_exists( 'totaltheme_has_classic_styles' ) && totaltheme_has_classic_styles();
}

/**
 * Add dark suffix to a url.
 */
function vcex_add_dark_suffix( $src = '' ): string {
	if ( ! $src ) {
		return '';
	}
	$url_parts = parse_url( $src );
	$path = $url_parts['path'];
	$path_info = pathinfo( $path );
	$file_name = $path_info['filename'];
	$extension = $path_info['extension'];
	$new_file_name = "{$file_name}-dark.{$extension}";
	$new_src = $url_parts['scheme'] . '://' . $url_parts['host'] . '/' . ltrim( dirname( $path ), '/' ) . '/' . $new_file_name;
	return $new_src;
}

/**
 * Creates dummy post. 
 */
function vcex_get_dummy_post() {
	static $dummy_post = null;
	if ( null === $dummy_post ) {
		$post_id = -99; // negative ID, to avoid clash with a valid post
		$post = new stdClass();
		$post->ID = $post_id;
		$post->post_author = 1;
		$post->post_date = current_time( 'mysql' );
		$post->post_date_gmt = current_time( 'mysql', 1 );
		$post->post_title = 'Example Post Title';
		$post->post_content = 'Example post content.';
		$post->post_status = 'publish';
		$post->comment_status = 'closed';
		$post->ping_status = 'closed';
		$post->post_name = 'dummy-post-' . rand( 1, 99999 ); // append random number to avoid clash
		$post->post_type = 'post';
		$post->filter = 'raw'; // !! important !!
		$dummy_post = new WP_Post( $post );
		wp_cache_add( $post_id, $dummy_post, 'posts' );
	}
	return $dummy_post;
}

/**
 * Return flex basis.
 */
function vcex_get_flex_basis( $basis, $gap = '' ) {
	if ( '1' === $basis || 1 === $basis ) {
		return '100%';
	}
	$numeric_bases = [ '2', '3', '4', '5', '6' ];
	if ( in_array( $basis, $numeric_bases, true ) ) {
		$basis = absint( $basis );
		if ( $gap ) {
			$gap_count = $basis - 1;
			if ( is_numeric( $gap ) ) {
				$gap = "{$gap}px";
			}
			return "calc((100% / {$basis}) - (({$gap} * {$gap_count}) / {$basis}))";
		} else {
			return "calc(100% / {$basis})";
		}
	}
	return sanitize_text_field( $basis );
}

/**
 * Return placeholder image.
 */
function vcex_get_placeholder_image( $size = '', $attrs = '' ) {
	if ( ! function_exists( 'wpex_get_placeholder_image' ) ) {
		return;
	}
	$class = $attrs['class'] ?? [];
	unset( $attrs['class'] );
	if ( $size ) {
		$ph_aspect_ratio = get_theme_mod( "{$size}_image_aspect_ratio" );
		if ( $ph_aspect_ratio ) {
			$class[] = 'wpex-aspect-' . sanitize_html_class( str_replace( '/', '-', $ph_aspect_ratio ) );
			$ph_object_fit = get_theme_mod( "{$size}_image_fit", 'cover' );
			if ( $ph_object_fit ) {
				$class[] = 'wpex-object-' . sanitize_html_class( $ph_object_fit);
			}
			$ph_object_position = get_theme_mod( "{$size}_image_position" );
			if ( $ph_object_position ) {
				$class[] = 'wpex-object-' . sanitize_html_class( $ph_object_position );
			}
		}
	}
	if ( $class ) {
		$attrs['class'] = $class;
	}
	return wpex_get_placeholder_image( $attrs );
}
