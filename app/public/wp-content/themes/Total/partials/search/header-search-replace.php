<?php

/**
 * Site header search replace.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

use TotalTheme\Header\Menu\Search;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Header\Menu\Search' ) ) {
	return;
}

$class = [
    'header-searchform-wrap',
    'wpex-absolute',
    'wpex-z-dropdown',
    'wpex-inset-0',
    'wpex-transition-all',
    'wpex-duration-300',
    'wpex-invisible',
    'wpex-opacity-0',
    'wpex-text-xl',
];

if ( get_theme_mod( 'menu_search_slide_down' ) ) {
    $class[] = '-wpex-translate-y-50';
}

$button_hover_class = '';

if ( ! get_theme_mod( 'header_background' ) && ! get_theme_mod( 'header_color' ) ) {
    $button_hover_class = ' wpex-hover-text-1';
}

?>

<div id="searchform-header-replace" class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
	<div class="searchform-header-replace__inner container wpex-flex wpex-justify-between wpex-items-center wpex-relative wpex-h-100">
        <?php
        $input_class = 'wpex-unstyled-input wpex-outline-0 wpex-h-100 wpex-w-100';
        if ( totaltheme_has_classic_styles() ) {
            $input_class .= ' wpex-uppercase wpex-tracking-wide';
        }
        echo Search::get_form( [
            'style'        => 'header-replace',
            'form_class'   => 'wpex-h-100 wpex-w-100',
            'input_class'  =>  $input_class,
            'submit_class' => 'wpex-unstyled-button wpex-hidden wpex-absolute wpex-top-50 wpex-right-0 wpex-mr-25 -wpex-translate-y-50',
            'submit_text'  => '',
            'autocomplete' => 'off',
        ] ); ?>
        <button id="searchform-header-replace-close" class="wpex-unstyled-button wpex-transition-colors wpex-flex wpex-items-center wpex-justify-center wpex-user-select-none<?php echo esc_attr( $button_hover_class ); ?>">
            <?php
            $close_icon_size = ( $size = get_theme_mod( 'search_header_replace_close_icon_size' ) ) ? sanitize_text_field( $size ) : '';
            echo totaltheme_get_icon(
                'material-close',
                'searchform-header-replace-close__icon wpex-flex',
                $close_icon_size
            ); ?>
            <span class="screen-reader-text"><?php esc_html_e( 'Close search', 'total' ); ?></span>
        </button>
    </div>
</div>
