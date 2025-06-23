<?php

/**
 * The next and previous post links.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.1
 */

defined( 'ABSPATH' ) || exit;

// Define main vars
$post_type     = get_post_type();
$in_same_term  = (bool) get_theme_mod( 'next_prev_in_same_term', true );
$reverse_order = (bool) apply_filters( 'wpex_nex_prev_reverse', get_theme_mod( 'next_prev_reverse_order', false ), $post_type );

// Same term checks
if ( $in_same_term ) {
	if ( taxonomy_exists( 'post_series' ) && get_the_terms( get_the_id(), 'post_series' ) ) {
		$taxonomy = 'post_series';
		if ( is_callable( 'TotalThemeCore\Post_Series::get_query_order' ) && 'DESC' === TotalThemeCore\Post_Series::get_query_order() ) {
			$reverse_order = true;
		}
	} else {
		$taxonomy = (string) apply_filters( 'wpex_next_prev_same_cat_taxonomy', wpex_get_post_type_cat_tax(), $post_type );
		$in_same_term = get_the_terms( get_the_id(), $taxonomy );
	}
}

$in_same_term = (bool) apply_filters( 'wpex_next_prev_in_same_term', $in_same_term, $post_type );

// Text
$prev_text = ( $prev_text = get_theme_mod( 'next_prev_prev_text' ) ) ? esc_html( $prev_text ) : '%title';
$next_text = ( $next_text = get_theme_mod( 'next_prev_next_text' ) ) ? esc_html( $next_text ) : '%title';
$prev_text = apply_filters( 'wpex_prev_post_link_text', $prev_text );
$next_text = apply_filters( 'wpex_next_post_link_text', $next_text );

// Previous title
$prev_icon = totaltheme_get_icon( 'chevron-left', 'wpex-mr-10', 'xs', true );
$prev_post_link_title = $prev_icon . '<span class="screen-reader-text">' . esc_html__( 'previous post', 'total' ) . ': </span>' . $prev_text;
$prev_post_link_title = apply_filters( 'wpex_prev_post_link_title', $prev_post_link_title, $post_type );

// Next title
$next_icon = totaltheme_get_icon( 'chevron-right', 'wpex-ml-10', 'xs', true );
$next_post_link_title = '<span class="screen-reader-text">' . esc_html__( 'next post', 'total' ) . ': </span>' . $next_text . $next_icon;
$next_post_link_title = apply_filters( 'wpex_next_post_link_title', $next_post_link_title, $post_type );

// Reverse links
if ( $reverse_order ) {
	$prev_post_link_title_tmp = $prev_post_link_title;
	$next_post_link_title_tmp = $next_post_link_title;
	$prev_post_link_title     = $next_post_link_title_tmp;
	$next_post_link_title     = $prev_post_link_title_tmp;
}

// Get post links
if ( $in_same_term ) {
	$excluded_terms = apply_filters( 'wpex_next_prev_excluded_terms', null, $post_type );
	$prev_link = get_previous_post_link( '%link', $prev_post_link_title, $in_same_term, $excluded_terms, $taxonomy );
	$next_link = get_next_post_link( '%link', $next_post_link_title, $in_same_term, $excluded_terms, $taxonomy );
} else {
	$prev_link = get_previous_post_link( '%link', $prev_post_link_title, false );
	$next_link = get_next_post_link( '%link', $next_post_link_title, false );
}

// Display next and previous links
if ( $prev_link || $next_link ) :
	?>
	<div class="post-pagination-wrap wpex-py-20 wpex-border-solid wpex-border-t wpex-border-main wpex-print-hidden">
		<ul class="post-pagination container wpex-flex wpex-justify-between wpex-list-none"><?php
			if ( $reverse_order ) {
				echo '<li class="post-prev wpex-flex-grow wpex-ml-10">' . $next_link . '</li>';
				echo '<li class="post-next wpex-flex-grow wpex-mr-10 wpex-text-right">' . $prev_link . '</li>';
			} else {
				echo '<li class="post-prev wpex-flex-grow wpex-mr-10">' . $prev_link . '</li>';
				echo '<li class="post-next wpex-flex-grow wpex-ml-10 wpex-text-right">' . $next_link . '</li>';
			}
		?></ul>
	</div>
	<?php
endif;
