<?php

defined( 'ABSPATH' ) || exit;

$template_id = $this->get_template_id();

if ( ! $template_id || 'publish' !== get_post_status( $template_id ) ) {
	return;
}

$content = get_post_field( 'post_content', $template_id );

if ( ! $content ) {
	return;
}

$template_type = totaltheme_get_post_builder_type( $template_id );

if ( ! $template_type ) {
	return;
}

$template_content = '';

$file = WPEX_THEME_DIR . "/cards/template/{$template_type}.php";

if ( file_exists( $file ) ) {
	$template_content = require $file;
}

return $template_content;
