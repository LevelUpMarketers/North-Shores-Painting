<?php
/**
 * CPT single content
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10.1
 */

defined( 'ABSPATH' ) || exit;

?>

<article <?php wpex_cpt_single_content_class(); ?>><?php the_content(); ?></article>