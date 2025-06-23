<?php
/**
 * Single blog post content
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10.1
 */

defined( 'ABSPATH' ) || exit;

?>

<div <?php wpex_blog_single_content_class(); ?>><?php the_content(); ?></div>

<?php
// Page links (for the <!-nextpage-> tag)
wpex_get_template_part( 'link_pages' ); ?>