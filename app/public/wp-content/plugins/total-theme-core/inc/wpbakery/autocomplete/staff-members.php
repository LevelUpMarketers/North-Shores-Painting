<?php

namespace TotalThemeCore\WPBakery\Autocomplete;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery AutoComplete => Staff Members.
 */
final class Staff_Members {

	public static function callback( $search_string ) {
		$staff_members = [];
		$staff_ids = \get_posts( [
			'posts_per_page' => -1,
			'post_type'      => 'staff',
			's'              => $search_string,
			'fields'         => 'ids',
		] );
		if ( ! empty( $staff_ids ) ) {
			foreach ( $staff_ids as $id ) {
				$staff_members[] = [
					'label' => \get_the_title( $id ),
					'value' => $id,
				];
			}
		}
		return $staff_members;
	}

	public static function render( $data ) {
		return [
			'label' => \get_the_title( $data['value'] ),
			'value' => $data['value'],
		];
	}

}
