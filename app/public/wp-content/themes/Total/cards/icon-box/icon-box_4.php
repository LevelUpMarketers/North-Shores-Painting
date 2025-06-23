<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-1 wpex-shadow-lg wpex-p-30 wpex-text-center">';

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
		'class' => 'wpex-mt-auto wpex-font-semibold',
		'link_class' => 'wpex-hover-underline',
		'text' => esc_html__( 'Learn more', 'total' ),
		'suffix' => ' &rarr;',
	] );

$html .= '</div>';

return $html;
