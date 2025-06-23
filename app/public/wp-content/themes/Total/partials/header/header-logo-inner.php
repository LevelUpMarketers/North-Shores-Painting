<?php

use TotalTheme\Header\Logo as Header_Logo;

/**
 * Displays the header logo.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Header\Logo' ) ) {
	return;
}

Header_Logo::render();
