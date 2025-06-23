<?php

/**
 * Blog entry quote
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

?>

<article id="post-<?php the_ID(); ?>" <?php wpex_blog_entry_class(); ?>>
	<?php if ( ! wpex_blog_entry_card() ) { ?>
		<blockquote <?php wpex_blog_entry_quote_class(); ?>>
			<div class="quote-entry-content"><?php the_content(); ?></div>
			<cite class="quote-entry-author"><span>-</span> <?php the_title(); ?></cite>
		</blockquote>
		<?php if ( ! is_singular( 'post' ) ) {
			wpex_blog_entry_divider();
		} ?>
	<?php } ?>
</article>
