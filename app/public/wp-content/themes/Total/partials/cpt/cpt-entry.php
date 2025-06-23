<?php

use TotalTheme\CPT\Entry_Blocks as Blocks;

/**
 * CTP entry
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\CPT\Entry_Blocks' ) ) {
	return;
}

?>

<article id="post-<?php the_ID(); ?>" <?php wpex_cpt_entry_class(); ?>>
	<?php if ( ! wpex_cpt_entry_card() ) { ?>
		<div <?php wpex_cpt_entry_inner_class(); ?>><?php

			/**
			 * Renders the custom post type entry blocks.
			 *
			 * @see inc/cpt/entry-blocks.php
			 *
			 * If you wish to create a custom entry design please see the "Custom Cards" documentation.
			 *
			 * @link https://totalwptheme.com/docs/how-to-create-custom-theme-cards/
			 */
			Blocks::render();

		?></div>
		<?php wpex_cpt_entry_divider(); ?>
	<?php } ?>
</article>
