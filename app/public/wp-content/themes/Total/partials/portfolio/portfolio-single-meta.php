<?php

use TotalTheme\Portfolio\Meta_Blocks as Blocks;

/**
 * Portfolio single meta.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Portfolio\Meta_Blocks' ) ) {
	return;
}

$args             = $args ?? array();
$args['singular'] = true;

if ( ! isset( $args['blocks'] ) ) {
	$args['blocks'] = Blocks::get( $args['singular'] );
}

if ( empty( $args['blocks'] ) || ! is_array( $args['blocks'] ) ) {
	return;
}

$args['hook_name']          = 'portfolio_single_meta_sections';
$args['categories_tax']     = 'portfolio_category';
$args['first_category_tax'] = $args['categories_tax'];

?>

<ul id="portfolio-single-meta" <?php Blocks::wrapper_class( $args['singular'] ); ?>><?php

	/**
	 * Renders the single portfolio meta blocks.
	 *
	 * @see inc/meta.php
	 */
	Blocks::render( $args );

?></ul>
