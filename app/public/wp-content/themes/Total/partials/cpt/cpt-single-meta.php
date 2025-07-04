<?php
/**
 * CPT single meta
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.7.0
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'partials/meta/meta', get_post_type(), [
    'singular' => true,
] );