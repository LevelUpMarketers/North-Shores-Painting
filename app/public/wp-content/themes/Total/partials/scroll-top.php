<?php

/**
 * Scroll back to top button.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$speed         = (string) get_theme_mod( 'scroll_top_speed' );
$style         = (string) get_theme_mod( 'scroll_top_style' ) ?: 'default';
$arrow         = (string) get_theme_mod( 'scroll_top_arrow' ) ?: 'chevron-up';
$reveal_offset = (string) get_theme_mod( 'local_scroll_reveal_offset' );

// Convert reveal offset into a number.
if ( $reveal_offset ) {
	$reveal_offset = str_replace( 'px', '', $reveal_offset );
}

// Define classnames.
$class = [
	'wpex-z-popover',
	'wpex-flex',
	'wpex-items-center',
	'wpex-justify-center',
	'wpex-fixed',
	'wpex-rounded-full',
	'wpex-text-center',
	'wpex-box-content',
	'wpex-transition-all',
	'wpex-duration-200',
	'wpex-bottom-0',
	'wpex-right-0',
	'wpex-mr-25',
	'wpex-mb-25',
	'wpex-no-underline',
	'wpex-print-hidden',
];

// Add style based classes.
switch ( $style ) {
	case 'default':
		$class[] = 'wpex-surface-2';
		$class[] = 'wpex-text-4';
		$class[] = 'wpex-hover-bg-accent';
	break;
	case 'black':
		$class[] = 'wpex-bg-black';
		$class[] = 'wpex-text-white';
		$class[] = 'wpex-hover-bg-accent';
		$class[] = 'wpex-hover-text-white';
	break;
	case 'accent':
		$class[] = 'wpex-bg-accent';
		$class[] = 'wpex-hover-bg-accent_alt';
	break;
	case 'icon':
		// No extra classes needed for this style.
	break;
}

// Add shadow class.
if ( $shadow = (string) get_theme_mod( 'scroll_top_shadow', '' ) ) {
	$class[] = "wpex-{$shadow}";
}

// Hide arrow if reveal offset isn't 0.
if ( '0' !== $reveal_offset ) {
	$class[] = 'wpex-invisible';
	$class[] = 'wpex-opacity-0';
}

/**
 * Filters the scroll to top link class.
 *
 * @param array $class
 */
$class = (array) apply_filters( 'wpex_scroll_top_class', $class );

// Open breakpoint wrapper.
if ( $breakpoint = (string) get_theme_mod( 'scroll_top_breakpoint' ) ) {
	echo '<div class="' . esc_attr( wpex_utl_visibility_class( 'hide', $breakpoint ) ) . '">';
}

// Define link attributes.
$link_attrs = [
	'href'  => '#top',
	'id'    => 'site-scroll-top',
	'class' => array_map( 'sanitize_html_class', $class ),
];

// Custom reveal offset.
if ( ( $reveal_offset || '0' === $reveal_offset ) && is_numeric( $reveal_offset ) ) {
	$link_attrs['data-scroll-offset'] = esc_attr( $reveal_offset );
}

// Custom scroll speed.
if ( ( $speed || '0' === $speed ) && is_numeric( $speed ) ) {
	$link_attrs['data-scroll-speed'] = esc_attr( $speed );
}

?>

<a <?php echo wpex_parse_attrs( $link_attrs ); ?>><?php

	// Display Icon.
	echo totaltheme_get_icon( $arrow, 'wpex-flex' );

	// Screen reader text.
	echo '<span class="screen-reader-text">' . esc_html__( 'Back To Top', 'total' ) . '</span>';

?></a>

<?php
// Close breakpoint wrapper.
if ( $breakpoint ) {
	echo '</div>';
}
