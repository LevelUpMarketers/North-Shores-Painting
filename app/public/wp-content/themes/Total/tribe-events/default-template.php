<?php

use Tribe\Events\Views\V2\Template_Bootstrap;

/**
 * Default Page Template for "The Events Calendar Plugin"
 *
 * @package Total WordPress theme
 * @subpackage Tribe\Events
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

?>

<div id="content-wrap" <?php totaltheme_content_wrap_class(); ?>>

	<?php wpex_hook_primary_before(); ?>

	<div id="primary" class="content-area wpex-clr">

		<?php wpex_hook_content_before(); ?>

		<div id="content" class="site-content wpex-clr">

			<?php wpex_hook_content_top(); ?>

			<?php
			// Tribe events content.
			if ( function_exists( 'tribe' ) && class_exists( 'Tribe\Events\Views\V2\Template_Bootstrap' ) ) {
				echo tribe( Template_Bootstrap::class )->get_view_html();
			} ?>

			<?php wpex_hook_content_bottom(); ?>

		</div>

		<?php wpex_hook_content_after(); ?>

	</div>

	<?php wpex_hook_primary_after(); ?>

</div>

<?php
get_footer();
