<?php

use TotalTheme\Search\Entry;

/**
 * Search entry excerpt.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$more_tag_check = (bool) apply_filters( 'wpex_check_more_tag', false );

if ( $more_tag_check ) {
	$post_content = (string) (get_post()->post_content ?? '');
	$has_more_tag = $post_content && \str_contains( $post_content, '<!--more-->' );
}

?>

<div <?php Entry::excerpt_class(); ?>><?php
	if ( $more_tag_check && $has_more_tag ) {
		the_content( '', '&hellip;' );
	} else {
		echo totaltheme_get_post_excerpt( (array) apply_filters( 'wpex_search_entry_excerpt_args', [
			'length'   => Entry::excerpt_length(),
			'readmore' => false,
		] ) );
	}
?></div>
