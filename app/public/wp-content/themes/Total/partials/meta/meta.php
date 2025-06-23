<?php

use TotalTheme\CPT\Meta_Blocks as Blocks;

/**
 * Post meta (date, author, comments, etc) for custom post types.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\CPT\Meta_Blocks' ) ) {
	return;
}

$args = $args ?? [];

if ( isset( $args['blocks'] ) ) {
	$is_custom = true; // note: Custom set blocks bypasses the wpex_meta_blocks filter.
} else {
	$args['blocks'] = Blocks::get( $args['singular'] ?? null );
	$is_custom      = false;
}

if ( empty( $args['blocks'] ) || ! is_array( $args['blocks'] ) ) {
	return;
}

?>

<ul <?php Blocks::wrapper_class( $is_custom ); ?>><?php

	/**
	 * Renders the custom post type meta blocks.
	 *
	 * @see inc/meta.php
	 *
	 * If you wish to alter the meta blocks or add custom blocks please take a look at the documentation link below and if
	 * you have any questions please open a ticket or leave a comment on ThemeForest for assistance.
	 *
	 * @link https://totalwptheme.com/docs/snippets/alter-meta-sections/
	 *
	 */
	Blocks::render( $args );

?></ul>
