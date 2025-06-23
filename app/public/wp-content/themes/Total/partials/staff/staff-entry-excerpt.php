<?php

/**
 * Staff entry excerpt
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

// Get excerpt length
$excerpt_length = wpex_staff_entry_excerpt_length();

// Return if excerpt length is set to 0
if ( empty( $excerpt_length ) || '0' === $excerpt_length ) {
	return;
}

?>

<div <?php wpex_staff_entry_excerpt_class(); ?>><?php
	echo totaltheme_get_post_excerpt( [
		'length'   => $excerpt_length,
		'readmore' => false,
	] );
?></div>