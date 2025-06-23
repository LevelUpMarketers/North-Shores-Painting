<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= $this->get_media( [
	'class' => 'wpex-mb-15',
] );

$html .= $this->get_date( [
	'class'  => 'wpex-mb-10',
	'type'   => 'published',
	'format' => ( ! WPEX_WPML_ACTIVE && ! WPEX_POLYLANG_ACTIVE && 0 === strpos( get_locale(), 'en_' ) ) ? 'n/j/Y' : '',
] );

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-2xl',
] );

$html .= $this->get_excerpt( [
	'class'  => 'wpex-mt-15',
	'length' => 30,
] );

$html .= $this->get_more_link( [
	'class'      => 'wpex-mt-15',
	'link_class' => 'wpex-border-0 wpex-border-b wpex-border-solid wpex-pb-5 wpex-no-underline',
] );

return $html;
