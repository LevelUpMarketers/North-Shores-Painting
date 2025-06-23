<?php

use TotalTheme\Header\Logo;

/**
 * Header Logo Wrapper.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Header\Logo' ) ) {
	return;
}

?>

<div id="site-logo" <?php Logo::wrapper_class(); ?>>
	<div id="site-logo-inner" <?php Logo::inner_class(); ?>><?php

		/**
		 * Hook: wpex_hook_site_logo_inner.
		 *
		 * @hooked wpex_header_logo_inner - 10
		 * @see partials/header/header-logo-inner.php
		 */
		wpex_hook_site_logo_inner();

	?></div>

</div>
