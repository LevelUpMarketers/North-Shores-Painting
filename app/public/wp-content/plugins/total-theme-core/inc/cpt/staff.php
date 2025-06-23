<?php

namespace TotalThemeCore\Cpt;

use TotalThemeCore\Custom_Post_Type_Abstract;

\defined( 'ABSPATH' ) || exit;

/**
 * Staff Post Type.
 */
if ( class_exists( 'TotalThemeCore\Custom_Post_Type_Abstract' ) ) {
	final class Staff extends Custom_Post_Type_Abstract {

		/**
		 * Check if staff user relationships are enabled and exist.
		 */
		private static $has_user_relations;

		/**
		 * Constructor.
		 */
		public function __construct() {
			parent::__construct( 'staff' );
			$this->init_hooks();
		}

		/**
		 * Register action hooks.
		 */
		private function init_hooks(): void {
			if ( \is_admin() ) {
				\add_filter( 'wpex_metabox_array', [ self::class, 'add_meta' ], 5, 2 );
				if ( \apply_filters( 'wpex_staff_users_relations', true ) ) {
					\add_filter( 'personal_options', [ self::class, 'add_staff_connection_user_field' ] );
					\add_action( 'personal_options_update', [ self::class, 'update_user_staff_connection' ] );
					\add_action( 'edit_user_profile_update', [ self::class, 'update_user_staff_connection' ] );
				}
			}
			if ( ! \is_admin() || \wp_doing_ajax() ) {
				\add_action( 'wpex_post_subheading', [ self::class, 'add_position_to_subheading' ] );
				if ( \apply_filters( 'wpex_staff_users_relations', true ) && ! empty( (array) \get_option( 'wpex_staff_users_relations' ) ) ) {
					\add_filter( 'pre_get_avatar', [ self::class, 'filter_avatar' ], 10, 3 );
					\add_filter( 'the_author', [ self::class, 'filter_the_author' ] );
					\add_filter( 'author_link', [ self::class, 'filter_author_link' ], 10, 2 );
					\add_filter( 'get_comment_author', [ self::class, 'filter_comment_author' ], 10, 3 );
					\add_filter( 'get_comment_author_url', [ self::class, 'filter_comment_url' ], 10, 3 );
					\add_filter( 'get_the_author_description', [ self::class, 'filter_the_author_description' ], 10, 2 );
				}
			}
		}

		/**
		 * Display position for page header subheading.
		 */
		public static function add_position_to_subheading( $subheading ) {
			if ( \is_singular( 'staff' )
				&& \get_theme_mod( 'staff_single_header_position', true )
				&& ! \in_array( 'title', \wpex_staff_single_blocks() )
				&& $meta = \get_post_meta( \get_the_ID(), 'wpex_staff_position', true )
			) {
				$subheading = $meta;
			}
			return $subheading;
		}

		/**
		 * Adds field to user dashboard to connect to staff member.
		 */
		public static function add_staff_connection_user_field( $user ) {
			$staff_posts = \get_posts( [
				'post_type'      => 'staff',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			] );

			if ( ! $staff_posts ) {
				return;
			}

			$meta_value = \get_user_meta( $user->ID, 'wpex_staff_member_id', true ); ?>
				<tr>
					<th scope="row"><?php \esc_html_e( 'Connect to Staff Member', 'total-theme-core' ); ?></th>
					<td>
						<fieldset>
							<select type="text" id="wpex_staff_member_id" name="wpex_staff_member_id">
								<option value="" <?php \selected( $meta_value, '', true ); ?>><?php esc_html_e( '— Select —', 'total-theme-core' ); ?></option>
								<?php foreach ( $staff_posts as $id ) { ?>
									<option value="<?php echo \esc_attr( $id ); ?>" <?php \selected( $meta_value, $id, true ); ?>><?php echo \esc_html( \get_the_title( $id ) ); ?></option>
								<?php } ?>
							</select>
						</fieldset>
					</td>
				</tr>
			<?php
		}

		/**
		 * Update user/staff connection.
		 */
		public static function update_user_staff_connection( $user_id ) {
			if ( ! \array_key_exists( 'wpex_staff_member_id', $_POST ) ) {
				return;
			}

			$meta      = $_POST['wpex_staff_member_id'] ?? '';
			$relations = \get_option( 'wpex_staff_users_relations' );
			$relations = \is_array( $relations ) ? $relations : [];

			if ( $meta ) {
				$meta_escaped = \absint( $meta );
				$relations[ $user_id ] = $meta_escaped;
				\update_option( 'wpex_staff_users_relations', $relations );
				\update_user_meta( $user_id, 'wpex_staff_member_id', $meta_escaped );
			} else {
				unset( $relations[ $user_id ] );
				\update_option( 'wpex_staff_users_relations', $relations );
				\delete_user_meta( $user_id, 'wpex_staff_member_id' );
			}
		}

		/**
		 * Filter the user avatar when a staff member is connected to it.
		 */
		public static function filter_avatar( $html, $id_or_email, $args ) {
			if ( ! \function_exists( 'wpex_get_post_thumbnail' ) ) {
				return $html;
			}

			$user = self::process_user_identifier( $id_or_email );

			if ( \is_object( $user ) && isset( $user->ID ) ) {

				$staff_member_id = self::get_user_related_staff_member_id( $user->ID );

				if ( $staff_member_id ) {

					$staff_thumbnail = \get_post_thumbnail_id( $staff_member_id );

					if ( $staff_thumbnail ) {

						$class = [
							'avatar',
							'avatar-' . (int) $args['size'],
							'photo',
						];

						$staff_avatar_args = [
							'attachment' => $staff_thumbnail,
							'size'       => 'wpex_custom',
							'width'      => $args['height'],
							'height'     => $args['width'],
							'alt'        => $args['alt'],
						];

						if ( ! empty( $args['class'] ) ) {
							if ( \is_array( $args['class'] ) ) {
								$class = \array_merge( $class, $args['class'] );
							} else {
								$class[] = $args['class'];
							}
						}

						$staff_avatar_args['class'] = $class;

						if ( ! empty( $args['extra_attr'] ) ) {
							$staff_avatar_args['attributes'] = \array_map( 'esc_attr', $args['extra_attr'] );
						}

						if ( $staff_avatar = \wpex_get_post_thumbnail( $staff_avatar_args ) ) {
							$html = $staff_avatar;
						}

					}

				}

			}
			return $html;
		}

		/**
		 * Alter the author name when a staff member is connected to an author.
		 */
		public static function filter_the_author( $author ) {
			global $authordata;
			if ( is_object( $authordata )
				&& isset( $authordata->ID )
				&& $staff_member = self::get_user_related_staff_member_id( $authordata->ID )
			) {
				$author = \get_the_title( $staff_member );
			}
			return $author;
		}

		/**
		 * Filter the author url when a staff member is connected to an author.
		 */
		public static function filter_author_link( $link, $author_id ) {
			if ( $author_id && $staff_member = self::get_user_related_staff_member_id( $author_id ) ) {
				$link = \get_permalink( $staff_member );
			}
			return $link;
		}

		/**
		 * Filter the comment author url when a staff member is connected to an author.
		 */
		public static function filter_comment_author( $author, $comment_ID, $comment ) {
			if ( \is_object( $comment ) && isset( $comment->comment_author_email ) ) {
				$user = \get_user_by( 'email', $comment->comment_author_email );
				if ( \is_object( $user ) && isset( $user->ID ) ) {
					$staff_member = self::get_user_related_staff_member_id( $user->ID );
					if ( $staff_member ) {
						$author = \get_the_title( $staff_member );
					}
				}
			}
			return $author;
		}

		/**
		 * Filter the comment author url when a staff member is connected to an author.
		 */
		public static function filter_comment_url( $url, $id, $comment ) {
			if ( \is_object( $comment ) && isset( $comment->comment_author_email ) ) {
				$user = \get_user_by( 'email', $comment->comment_author_email );
				if ( \is_object( $user ) && isset( $user->ID ) ) {
					$staff_member = self::get_user_related_staff_member_id( $user->ID );
					if ( $staff_member ) {
						$url = \get_permalink( $staff_member );
					}
				}
			}
			return $url;
		}

		/**
		 * Filter the author description if empty.
		 */
		public static function filter_the_author_description( $description, $user_id ) {
			if ( ! $description && $staff_member = self::get_user_related_staff_member_id( $user_id ) ) {
				$post = get_post( $staff_member );
				if ( ! empty( $post->post_excerpt ) ) {
					$description = $post->post_excerpt;
				}
			}
			return $description;
		}

		/**
		 * Adds staff meta options.
		 */
		public static function add_meta( $meta_settings, $post ): array {
			$social_fields = \function_exists( 'wpex_staff_social_meta_array' ) ? \wpex_staff_social_meta_array() : '';

			$fields = [
				'position' => [
					'title'      => \esc_html__( 'Position', 'total-theme-core' ),
					'id'         => 'wpex_staff_position',
					'type'       => 'text',
					'allow_html' => true,
					'icon'       => $social_fields ? 'user' : '',
				],
			];

			if ( $social_fields ) {
				$fields = \array_merge( $fields, $social_fields );
			}
			
			$meta_settings['staff'] = [
				'title'     => \get_post_type_object( 'staff' )->labels->singular_name ?? '',
				'post_type' => [ 'staff' ],
				'settings'  => $fields,
			];
			
			return $meta_settings;
		}

		/**
		 * Check if currently on a taxonomy page for the post type.
		 */
		protected static function is_staff_tax(): bool {
			return \function_exists( 'wpex_is_staff_tax' ) && \wpex_is_staff_tax();
		}

		/**
		 * Helper: Returns user related staff member ID.
		 */
		private static function get_user_related_staff_member_id( $user_id ) {
			if ( $relations = (array) \get_option( 'wpex_staff_users_relations' ) ) {
				return ! empty( $relations[ $user_id ] ) ? $relations[ $user_id ] : null;
			}
		}

		/**
		 * Helper: Returns staff member from id_or_email
		 */
		private static function process_user_identifier( $id_or_email = '' ) {
			if ( ! $id_or_email ) {
				return;
			}
			// Process the user identifier.
			if ( is_numeric( $id_or_email ) ) {
				return get_user_by( 'id', absint( $id_or_email ) );
			} elseif( is_string( $id_or_email ) ) {
				if ( strpos( $id_or_email, '@md5.gravatar.com' ) ) {
					list( $id_or_email ) = explode( '@', $id_or_email );
				}
				return get_user_by( 'email', $id_or_email );
			} elseif( $id_or_email instanceof WP_User ) {
				// User object.
				return $id_or_email;
			} elseif( $id_or_email instanceof WP_Post ) {
				// Post object.
				return get_user_by( 'id', (int) $id_or_email->post_author );
			} elseif( $id_or_email instanceof WP_Comment ) {
				if ( ! is_avatar_comment_type( get_comment_type( $id_or_email ) ) ) {
					return;
				}
				if ( ! empty( $id_or_email->user_id ) ) {
					return get_user_by( 'id', (int) $id_or_email->user_id );
				}
			}
		}

		/**
		 * Instance.
		 */
		public static function instance() {
			return new self; // soft deprecated in 1.7.1
		}

		/**
		 * Return staff icon.
		 */
		public static function get_admin_menu_icon(): string {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
			return 'businessman';
		}

		/**
		 * Return staff name.
		 */
		public static function get_post_type_name(): string {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
			return 'Staff';
		}

		/**
		 * Return staff singular name.
		 */
		public static function get_singular_name(): void {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
		}

		/**
		 * Check if the REST API is enabled for the post type.
		 */
		public static function show_in_rest(): void {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
		}

		/**
		 * Check if this post type has front-end posts.
		 */
		public static function has_single(): void {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
		}

	}
}