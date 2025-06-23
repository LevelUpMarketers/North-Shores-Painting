<?php

/**
 * vcex_post_content shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$post_id = get_the_ID();
$edit_mode = vcex_get_template_edit_mode();

// Prevent the module to display in itself when creating templates which would cause an endless loop.
if ( ( 'wpbakery' === $edit_mode && $post_id === get_queried_object_id() ) || 'elementor' === $edit_mode ) {
	$is_dynamic_template = true;
	$atts['blocks'] = [ 'the_content' ];
}
// Get post content.
else {
	$post_content = get_the_content( '', '', $post_id );
	
	// Return if the current post has this shortcode inside it to prevent infinite loop.
	if ( str_contains( $post_content, 'vcex_post_content' ) ) {
		return;
	}
}

// Sanitize then turn blocks into array.
$blocks = ! empty( $atts['blocks'] ) ? $atts['blocks'] : [ 'the_content' ];

if ( ! $blocks ) {
	return;
}

if ( ! is_array( $blocks ) ) {
	$blocks = (array) explode( ',' , $blocks );
}

// Wrap classes.
$wrap_classes = [
	'vcex-post-content',
];

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_classes[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

// Center content when adding a width.
if ( ! empty( $atts['width'] ) ) {
	$wrap_classes[] = 'wpex-mx-auto';
}

// Add css animation class.
if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_classes[] = vcex_get_css_animation( $atts['css_animation'] );
}

// Add extra classname.
if ( ! empty( $atts['el_class'] ) ) {
	$wrap_classes[] = vcex_get_extra_class( $atts['el_class'] );
}

// Add CSS class.
if ( ! empty( $atts['css'] ) ) {
	$wrap_classes[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
}

// Sidebar check.
$has_sidebar = false;
if ( defined( 'TOTAL_THEME_ACTIVE' ) ) {
	$has_sidebar = 'true' == $atts['sidebar'] && apply_filters( 'vcex_post_content_has_sidebar', true ) ? true : false;
	if ( $has_sidebar && $atts['sidebar_position'] ) {
		$wrap_classes[] = 'vcex-post-content-' . sanitize_html_class( $atts['sidebar_position'] ) . '-sidebar';
	}
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_post_content', $atts );
?>

<div class="<?php echo esc_attr( $wrap_class ); ?>">
	<?php
	// Open sidebar wrapper if enabled.
	if ( $has_sidebar ) { ?>
		<div class="vcex-post-content-blocks wpex-content-w wpex-clr">
	<?php }

	// Display blocks.
	if ( function_exists( 'wpex_get_template_part' ) ) :
		foreach ( $blocks as $block ) :
			switch ( $block ) :
				case 'featured_media':
				?>
					<div id="post-media" class="wpex-mb-20 wpex-clr"><?php
						if ( function_exists( 'wpex_post_media' ) ) {
							wpex_post_media( $post_id );
						} else {
							the_post_thumbnail();
						}
					?></div>
				<?php break;
				case 'title':
					$title_tag = get_query_var( 'wpex_card_post_id' ) ? 'h2' : 'h1';
				?>
					<<?php echo tag_escape( $title_tag ); ?> class="single-post-title entry-title wpex-text-3xl"><?php
						echo get_the_title( $post_id );
					?></<?php echo tag_escape( $title_tag ); ?>>
					<?php break;
				case 'meta':
					wpex_get_template_part( 'post_meta' );
					break;
				case 'the_content':
					$card = vcex_card_instance();
					if ( ! empty( $card->post_id ) ) {
						$wpb_style = totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' );
						if ( is_callable( [ $wpb_style, 'get_style' ] ) ) {
							echo $wpb_style->get_style( $card->post_id );
						}
					}
				?>

					<div class="vcex-post-content-c wpex-clr<?php echo vcex_validate_att_boolean( 'remove_last_mb', $atts ) ? ' wpex-last-mb-0' : ''; ?>"><?php
						if ( isset( $is_dynamic_template ) ) {
							esc_html_e( 'This is a sample post content for working in the frontend editor.', 'total-theme-core' );
						} elseif ( vcex_doing_ajax() || vcex_doing_loadmore() ) {
							echo vcex_parse_text_safe( $post_content );
						} else {
							if ( is_preview() && isset( $_GET['preview_id'] ) && (int) $_GET['preview_id'] === get_the_ID() ) {
								the_content();
								wpex_get_template_part( 'link_pages' );
							} else {
								echo apply_filters( 'the_content', $post_content );
								wpex_get_template_part( 'link_pages' );
							}
						}
					?></div>

					<?php break;
				case 'post_series':
					wpex_get_template_part( 'post_series' );
					break;
				case 'social_share':
					wpex_get_template_part( 'social_share' );
					break;
				case 'author_bio':
					wpex_get_template_part( 'author_bio' );
					break;
				case 'related':
					$post_type = get_post_type( $post_id );
					switch ( $post_type ) {
						case 'post':
							get_template_part( 'partials/blog/blog-single-related' );
							break;
						case 'portfolio':
							get_template_part( 'partials/portfolio/portfolio-single-related' );
							break;
						case 'staff':
							get_template_part( 'partials/staff/staff-single-related' );
							break;
						default:
							get_template_part( 'partials/cpt/cpt-single-related' );
							break;
					}
					break;
				case 'comments':
					comments_template();
					break;
				default:
					if ( is_callable( $block ) && vcex_validate_user_func( $block ) ) {
						call_user_func( $block );
					}
				break;
			endswitch;
		endforeach;
	endif; ?>
	<?php
	// Close sidebar wrapper if enabled.
	if ( $has_sidebar ) { ?>
		</div>
	<?php } ?>
	<?php
	// Display sidebar if enabled.
	if ( $has_sidebar ) { ?>
		<aside id="sidebar" class="vcex-post-content-sidebar sidebar-container sidebar-primary">
			<?php wpex_hook_sidebar_top(); ?>
				<div id="sidebar-inner" class="clr"><?php
					if ( function_exists( 'totaltheme_call_static' ) ) {
						$sidebar = totaltheme_call_static( 'Sidebars\Primary', 'get_sidebar_name' );
						if ( $sidebar ) {
							dynamic_sidebar( $sidebar );
						}
					}
				?></div>
			<?php wpex_hook_sidebar_bottom(); ?>
		</aside>
	<?php } ?>
</div>
