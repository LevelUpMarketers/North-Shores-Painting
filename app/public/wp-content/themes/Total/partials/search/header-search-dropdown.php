<?php

use TotalTheme\Header\Menu\Search;

/**
 * Site header search dropdown HTML
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Header\Menu\Search' ) ) {
	return;
}

?>

<div id="searchform-dropdown" <?php Search::drop_widget_class(); ?>><?php echo Search::get_form( [
	'style'        => 'header-dropdown',
	'form_class'   => 'wpex-flex',
	'input_class'  => 'wpex-block wpex-border-0 wpex-outline-0 wpex-w-100 wpex-h-auto wpex-leading-relaxed wpex-rounded-0 wpex-text-2 wpex-surface-2 wpex-p-10 wpex-text-1em wpex-unstyled-input',
	'submit_class' => 'wpex-hidden wpex-rounded-0 wpex-py-10 wpex-px-15',
	'autocomplete' => 'off',
	'submit_icon'  => 'search',
	'submit_text'  => '',
] );
?></div>
