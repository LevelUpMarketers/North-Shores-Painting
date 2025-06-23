<?php

/**
 * Page links.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.12
 */

defined( 'ABSPATH' ) || exit;

$safe_align = ( $align = get_theme_mod( 'pagination_align' ) ) ? ' text' . sanitize_html_class( $align ) : '';

// Link pages when using <!--nextpage-->
wp_link_pages( [
	'before'      => '<div class="wpex-pagination wpex-clear wpex-mt-30' . $safe_align . '"><div class="page-links">',
	'after'       => '</div></div>',
	'link_before' => '<span>',
	'link_after'  => '</span>',
] );
