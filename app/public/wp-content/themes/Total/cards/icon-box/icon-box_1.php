<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-text-center">';

	$html .= $this->get_icon( [
		'size' => 'sm',
		'class' => 'wpex-text-accent wpex-mb-20',
	] );

	$html .= $this->get_title( [
		'class' => 'wpex-heading wpex-text-lg wpex-mb-15',
	] );

	$html .= $this->get_excerpt( [
		'class' => 'wpex-mb-15',
	] );

	$html .= $this->get_more_link( [
		'class' => 'wpex-font-semibold',
		'link_class' => 'wpex-hover-underline',
		'text' => esc_html__( 'Learn more', 'total' ),
		'suffix' => ' &rarr;',
	] );

$html .= '</div>';

return $html;
