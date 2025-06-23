<?php

use TotalTheme\Blog\Meta_Blocks as Blocks;

/**
 * Blog single meta.
 *
 * @package Total WordPress theme
 * @subpackage Partials\Blog
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Blog\Meta_Blocks' ) ) {
	return;
}

$args             = $args ?? [];
$args['singular'] = true;

if ( ! isset( $args['blocks'] ) ) {
	$args['blocks'] = Blocks::get( $args['singular'] );
}

if ( empty( $args['blocks'] ) || ! is_array( $args['blocks'] ) ) {
	return;
}

$args['hook_name']          = 'blog_single_meta';
$args['categories_tax']     = 'category';
$args['first_category_tax'] = $args['categories_tax'];

?>

<ul <?php Blocks::wrapper_class( $args['singular'] ); ?>><?php

	/**
	 * Renders the single blog meta blocks.
	 *
	 * @see inc/meta.php
	 *
	 * If you wish to alter the meta blocks or add custom blocks please take a look at the documentation link below and if
	 * you have any questions please open a ticket or leave a comment on ThemeForest for assistance.
	 *
	 * @link https://totalwptheme.com/docs/snippets/add-new-blog-entrypost-meta-item/
	 *
	 */
	Blocks::render( $args );

?></ul>
