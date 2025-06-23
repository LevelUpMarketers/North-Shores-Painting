<?php

/**
 * Togglebar HTML.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.20
 */

defined( 'ABSPATH' ) || exit;

?>

<div id="toggle-bar-wrap" <?php wpex_togglebar_class() . wpex_togglebar_data_attributes(); ?>>
	<?php if ( ! wpex_theme_do_location( 'togglebar' ) ) : ?>
		<div id="toggle-bar" <?php wpex_togglebar_inner_class(); ?>><?php
			if ( 'visible' === get_theme_mod( 'toggle_bar_default_state', 'hidden' ) && get_theme_mod( 'toggle_bar_enable_dismiss', false ) ) {
				wpex_get_template_part( 'togglebar_dismiss' );
			}
			wpex_get_template_part( 'togglebar_content' );
		?></div>
	<?php endif;?>
</div>
