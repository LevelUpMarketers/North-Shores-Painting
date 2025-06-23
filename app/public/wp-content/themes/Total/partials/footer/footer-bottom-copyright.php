<?php

use TotalTheme\Footer\Bottom\Copyright;

/**
 * Footer bottom content
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Footer\Bottom\Copyright' ) ) {
	return;
}

$copyright = Copyright::get_content();

if ( ! $copyright ) {
	return;
}

?>

<div id="copyright" class="wpex-last-mb-0"><?php

	echo do_shortcode( wp_kses_post( $copyright ) );

?></div>
