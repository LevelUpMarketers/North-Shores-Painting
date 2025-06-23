<?php
/**
 * Testimonials entry avatar.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! has_post_thumbnail() ) {
	return;
}

$custom_dims    = $args['custom_dims'] ?? false;
$thumbnail_args = $args['thumbnail_args'] ?? array();

?>

<div <?php wpex_testimonials_entry_media_class( $custom_dims ); ?>><?php

	wpex_testimonials_entry_thumbnail( $thumbnail_args );

?></div>