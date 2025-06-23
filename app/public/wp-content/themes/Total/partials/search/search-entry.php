<?php

use TotalTheme\Search\Entry;

/**
 * Search entry layout.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Search\Entry' ) ) {
	return;
}

?>

<article id="post-<?php the_ID(); ?>" <?php Entry::wrapper_class(); ?>>
	<?php if ( ! Entry::render_card() ) { ?>
		<div <?php Entry::inner_class(); ?>>
			<?php if ( apply_filters( 'wpex_search_has_post_thumbnail', has_post_thumbnail() ) ) : ?>
				<?php get_template_part( 'partials/search/search-entry-thumbnail' ); ?>
			<?php endif; ?>
			<div <?php Entry::content_class(); ?>>
				<?php get_template_part( 'partials/search/search-entry-header' ); ?>
				<?php get_template_part( 'partials/search/search-entry-excerpt' ); ?>
			</div>
		</div>
		<?php Entry::divider(); ?>
	<?php } ?>
</article>
