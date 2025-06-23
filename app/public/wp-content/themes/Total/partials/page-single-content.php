<?php

/**
 * Page Content
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

?>

<div <?php wpex_page_single_content_class(); ?>><?php the_content(); ?></div>

<?php
wpex_get_template_part( 'link_pages' );
