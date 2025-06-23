<?php

/**
 * Mobile Menu alternative.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

?>

<div id="mobile-menu-alternative" class="wpex-hidden"><?php
	wp_nav_menu( array(
		'container'      => false,
        'fallback_cb'    => false,
		'theme_location' => 'mobile_menu_alt',
        // Important classes to match main menu.
		'menu_class'     => 'dropdown-menu',
        'link_before'    => '<span class="link-inner">',
        'link_after'     => '</span>',
	) );
?></div>
