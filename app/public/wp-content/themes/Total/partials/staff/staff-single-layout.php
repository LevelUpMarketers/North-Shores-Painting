<?php

use TotalTheme\Staff\Single_Blocks as Blocks;

/**
 * Staff single layout.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Staff\Single_Blocks' ) ) {
	return;
}

?>

<div id="single-blocks" <?php Blocks::wrapper_class(); ?>><?php

	/**
	 * Renders the single staff post blocks.
	 *
	 * @see inc/staff/single-blocks.php
	 *
	 * If you wish to create a custom page design please see the "Dynamic Templates" documentation.
	 *
	 * @link https://totalwptheme.com/docs/dynamic-templates/
	 */
	Blocks::render();

?></div>
