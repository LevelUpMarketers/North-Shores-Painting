<?php
/**
 * BBPress Customizer Settings.
 *
 * @package TotalTheme
 * @subpackage Integration/BBPress
 * @version 5.4
 */

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_bbpress'] = array(
	'title' => esc_html__( 'bbPress (Total)', 'total' ),
	'settings' => array(
		array(
			'id' => 'bbpress_forums_layout',
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'Forum Archive Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			),
		),
		array(
			'id' => 'bbpress_single_forum_layout',
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'Single Forum Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			),
		),
		array(
			'id' => 'bbpress_topics_layout',
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'Topics Archive Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			),
		),
		array(
			'id' => 'bbpress_single_topic_layout',
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'Single Topic Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			),
		),
		array(
			'id' => 'bbpress_user_layout',
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'User Page Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			),
		),
	)
);