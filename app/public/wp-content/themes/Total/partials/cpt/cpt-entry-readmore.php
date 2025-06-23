<?php
/**
 * CTP entry button.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.7.2
 */

defined( 'ABSPATH' ) || exit;

$button_text = wpex_get_cpt_entry_button_text();

if ( ! $button_text ) {
    return;
}

$aria_label = sprintf( esc_attr_x( '%s about %s', '*read more text* about *post name* aria label', 'total' ), $button_text, wpex_get_esc_title() );

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

<div <?php wpex_cpt_entry_button_wrap_class(); ?>>
	<a <?php echo wpex_parse_attrs( $link_attrs ); ?> <?php wpex_cpt_entry_button_class(); ?>><?php echo do_shortcode( wp_kses_post( $button_text ) ); ?></a>
</div>