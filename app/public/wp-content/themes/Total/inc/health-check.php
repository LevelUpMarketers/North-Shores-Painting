<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

abstract class Health_Check {

	/**
	 * Health check label.
	 */
	protected $label = '';

	/**
	 * The section to display the results in.
	 */
	protected $status = '';

	/**
	 * Results badge.
	 */
	protected $badge = [
		'label' => '',
		'color' => '',
	];

	/**
	 * Additional result details.
	 */
	protected $description = '';

	/**
	 * A link or button to allow the end user to take action on the result.
	 */
	protected $actions = '';

	/**
	 * The name of the test.
	 */
	protected $test = '';

	/**
	 * Whether or not the test should be ran on AJAX as well.
	 */
	protected $async = false;

	/**
	 * Runs the test and returns the result.
	 */
	abstract public function run();

	/**
	 * Registers the test to WordPress.
	 */
	public function register_test() {
		if ( $this->is_async() ) {
			\add_filter( 'site_status_tests', [ $this, 'add_async_test' ] );
			\add_action( 'wp_ajax_health-check-' . $this->get_test_name(), [ $this, 'get_async_test_result' ] );
			return;
		}

		\add_filter( 'site_status_tests', [ $this, 'add_test' ] );
	}

	/**
	 * Runs the test.
	 */
	public function add_test( $tests ) {
		$tests['direct'][$this->get_test_name()] = [
			'test' => [ $this, 'get_test_result' ],
		];

		return $tests;
	}

	/**
	 * Runs the test in async mode.
	 */
	public function add_async_test( $tests ) {
		$tests['async'][$this->get_test_name()] = [
			'test' => $this->get_test_name(),
		];

		return $tests;
	}

	/**
	 * Formats the test result as an array.
	 */
	public function get_test_result() {
		$this->run();

		return [
			'label'       => $this->label,
			'status'      => $this->status,
			'badge'       => $this->get_badge(),
			'description' => $this->description,
			'actions'     => $this->actions,
			'test'        => $this->test,
		];
	}

	/**
	 * Formats the test result as JSON as response to an AJAX request.
	 */
	public function get_async_test_result() {
		$result = $this->get_test_result();
		\wp_send_json_success( $result );
	}

	/**
	 * Retrieves the badge and ensure usable values are set.
	 */
	protected function get_badge() {
		if ( ! is_array( $this->badge ) ) {
			$this->badge = [];
		}

		if ( empty( $this->badge['label'] ) ) {
			$this->badge['label'] = \sprintf(
				\esc_html_x( '%1$s Theme', '*theme name* Theme', 'total' ),
				'Total'
			);
		}

		if ( empty( $this->badge['color'] ) ) {
			$this->badge['color'] = 'green';
		}

		return $this->badge;
	}

	/**
	 * WordPress converts underscores to dashes.
	 */
	protected function get_test_name() {
		return \str_replace( '_', '-', $this->test );
	}

	/**
	 * Checks if the health check is async.
	 */
	protected function is_async() {
		return ! empty( $this->async );
	}

}
