<?php

namespace TotalThemeCore\WPBakery\Autocomplete;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery AutoComplete => Users.
 */
final class Users {

	public static function callback( $search_string ) {
		$users_list = [];
		$users = \get_users( [
			'search' => "{$search_string}*",
		] );
		if ( $users && ! \is_wp_error( $users ) ) {
			foreach ( $users as $user ) {
				$users_list[] = [
					'label' => \esc_html( $user->display_name ),
					'value' => $user->ID,
				];
			}
		}
		return $users_list;
	}

	public static function render( $data ) {
		$user = $data['value'];
		$user_data = \get_userdata( $user );
		if ( \is_object( $user_data ) ) {
			$label = ! empty( $user_data->nickname ) ? $user_data->nickname : $user_data->name;
			return [
				'value' => $user,
				'label' => $label,
			];
		} else{
			return false;
		}
	}

}
