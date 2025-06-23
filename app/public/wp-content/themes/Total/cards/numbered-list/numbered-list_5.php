<?php
defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow">';

	// Number
	$html .= $this->get_number( array(
		'class' => 'wpex-text-accent wpex-font-bold wpex-leading-none wpex-mb-15',
		'css' => 'font-size:6em;',
	) );

	// Title
	$html .= $this->get_title( array(
		'class' => 'wpex-heading wpex-text-xl',
	) );

	// Divider.
	$html .= $this->get_empty_element( array(
		'html_tag' => 'span',
		'class' => 'wpex-card-divider wpex-inline-block wpex-bg-accent wpex-my-10',
		'css' => 'width:30px;height:2px;',
	) );

	// Excerpt
	$html .= $this->get_excerpt( array(
		'class' => 'wpex-mb-15 wpex-text-3',
	) );

	// More Link
	$html .= $this->get_more_link( array(
		'class' => 'wpex-font-semibold',
		'link_class' => 'wpex-hover-underline',
		'text' => esc_html__( 'Learn more', 'total' ),
		'suffix' => ' &rarr;',
	) );

$html .= '</div>';

return $html;