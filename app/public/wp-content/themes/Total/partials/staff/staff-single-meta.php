<?php

use TotalTheme\Staff\Meta_Blocks as Blocks;

/**
 * Staff single meta.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

if ( ! class_exists( 'TotalTheme\Staff\Meta_Blocks' ) ) {
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

$args['hook_name']          = 'staff_single_meta';
$args['categories_tax']     = 'staff_category';
$args['first_category_tax'] = $args['categories_tax'];

?>

<ul id="staff-single-meta" <?php Blocks::wrapper_class( $args['singular'] ); ?>><?php

	/**
	 * Renders the single portfolio meta blocks.
	 *
	 * @see inc/meta.php
	 */
	Blocks::render( $args );

?></ul>
