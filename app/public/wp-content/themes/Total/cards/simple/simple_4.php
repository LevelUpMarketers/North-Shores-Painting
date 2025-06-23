<?php

defined( 'ABSPATH' ) || exit;

$html = $this->get_primary_term( [
	'class' => 'wpex-mb-5',
	'term_class' => 'wpex-text-3 wpex-hover-underline',
] );

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-xl',
] );

return $html;
