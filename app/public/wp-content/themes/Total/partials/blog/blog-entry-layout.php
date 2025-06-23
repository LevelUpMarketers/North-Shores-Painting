<?php

use TotalTheme\Blog\Entry_Blocks as Blocks;

/**
 * Blog entry layout.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

// Quote format has it's own output.
if ( 'quote' === get_post_format() || ! class_exists( 'TotalTheme\Blog\Entry_Blocks' ) ) {
	get_template_part( 'partials/blog/blog-entry-quote' );
	return;
}

?>

<article id="post-<?php the_ID(); ?>" <?php wpex_blog_entry_class(); ?>>

	<?php if ( ! wpex_blog_entry_card() ) { ?>

		<div <?php wpex_blog_entry_inner_class(); ?>><?php

			/**
			 * Renders the blog entry blocks.
			 *
			 * @see inc/blog/entry-blocks.php
			 *
			 * If you wish to create a custom entry design please see the "Custom Cards" documentation.
			 *
			 * @link https://totalwptheme.com/docs/custom-card-builder/
			 */
			Blocks::render();

		?></div>

		<?php wpex_blog_entry_divider(); ?>

	<?php } ?>

</article>
