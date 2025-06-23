<?php

/**
 * Header Flex Container Side Content.
 *
 * This is an older partial file. It's not recommended to modify the header aside content via this file.
 * If you wish to modify the contents of the header aside element hook into the "" filter instead.
 * 
 * Alternatively consider using the Header Builder or creating a new Dynamic Template Part and inserting
 * your template part shortcode into your header aside field in the customizer.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

totaltheme_call_static( 'Header\Flex\Aside', 'render' );
