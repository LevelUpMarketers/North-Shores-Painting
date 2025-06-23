<?php
/**
 * Blog entry readmore button.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.7.2
 */

defined( 'ABSPATH' ) || exit;

$more_link_text = wpex_get_translated_theme_mod( 'blog_entry_readmore_text' ) ?: esc_html__( 'Read more', 'total' );

/**
 * Filters the blog post readmore link text
 *
 * @param string $more_link_text
 */
$more_link_text = (string) apply_filters( 'wpex_post_readmore_link_text', $more_link_text );

if ( ! $more_link_text ) {
	return;
}

$aria_label = sprintf( esc_attr_x( '%s about %s', '*read more text* about *post name* aria label', 'total' ), $more_link_text, wpex_get_esc_title() );

/**
 * Filters the more_link aria label.
 *
 * @param string $aria_label
 */
$aria_label = (string) apply_filters( 'wpex_aria_label', $aria_label, 'more_link' );

$link_attrs = [
    'href'       => wpex_get_permalink(),
    'aria-label' => strip_shortcodes( $aria_label ),
];

?>

<div <?php wpex_blog_entry_button_wrap_class(); ?>><a <?php echo wpex_parse_attrs( $link_attrs ); ?> <?php wpex_blog_entry_button_class(); ?>><?php echo do_shortcode( wp_strip_all_tags( $more_link_text ) ); ?></a></div>