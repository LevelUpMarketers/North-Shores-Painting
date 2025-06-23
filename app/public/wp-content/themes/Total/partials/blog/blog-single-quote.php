<?php

use TotalTheme\Blog\Single_Blocks as Blocks;

/**
 * Blog Quote Format Single Layout.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Blog\Single_Blocks' ) ) {
	return;
}

?>

<article id="post-<?php the_ID(); ?>" class="single-quote-format wpex-mb-40">
	<blockquote <?php wpex_blog_entry_quote_class(); ?>>
		<div class="quote-entry-content"><?php the_content(); ?></div>
		<cite class="quote-entry-author"><span>-</span> <?php the_title(); ?></cite>
	</blockquote>
</article>

<?php
$single_blocks = Blocks::get();

if ( $single_blocks && is_array( $single_blocks ) && in_array( 'comments', $single_blocks ) ) {
	comments_template();
}
