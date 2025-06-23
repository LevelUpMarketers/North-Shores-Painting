<?php

use TotalTheme\Search\Entry;

/**
 * Search entry header
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

?>

<header <?php Entry::header_class(); ?>>
	<h2 <?php Entry::title_class(); ?>><a href="<?php wpex_permalink(); ?>"><?php the_title(); ?></a></h2>
</header>
