<?php

use TotalTheme\Page\Single_Blocks as Blocks;

/**
 * Single Page Layout.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Page\Single_Blocks' ) ) {
	return;
}

?>

<article id="single-blocks" <?php Blocks::wrapper_class(); ?>><?php

	/**
	 * Renders the single page blocks.
	 *
	 * @see inc/page/single-blocks.php
	 *
	 * If you wish to create a custom page design please see the "Dynamic Templates" documentation.
	 *
	 * @link https://totalwptheme.com/docs/dynamic-templates/
	 */
	Blocks::render();

?></article>
