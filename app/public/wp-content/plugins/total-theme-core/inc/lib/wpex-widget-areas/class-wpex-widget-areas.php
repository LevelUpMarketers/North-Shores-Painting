<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPEX_Widget_Areas' ) ) {

	/**
	 * WPEX Widget Areas.
	 */
	class WPEX_Widget_Areas {

		/**
		 * Post type used to store the custom widget areas.
		 */
		const POST_TYPE = 'wpex_widget_area';

		/**
		 * Instance.
		 */
		private static $instance = null;

		/**
		 * Create or retrieve the instance of WPEX_Widget_Areas.
		 */
		public static function instance() {
			if ( null === static::$instance ) {
				static::$instance = new self();
			}
			return static::$instance;
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			$this->global_hooks();
			if ( is_admin() ) {
				$this->admin_hooks();
			}
		}

		/**
		 * Global hooks.
		 */
		public function global_hooks() {

			// Register the "wpex_widget_areas" post_type.
			add_action( 'init', [ $this, 'register_post_type' ] );

			// Create wpex_widget_areas posts from deprecated theme_mod.
			if ( get_theme_mod( 'widget_areas' ) ) {
				add_action( 'admin_notices', [ $this, 'migration_notice' ] );
				add_action( 'wp_ajax_wpex_widget_areas_migrate', [ $this, 'migrate_widget_areas_ajax' ] );
			}

			// Register widget areas with WP.
			add_action( 'init', [ $this, 'register_widget_areas' ], 1000 ); // use high priority so they display last.

			// Add metaboxes.
			if ( class_exists( 'WPEX_Meta_Factory' ) ) {
				add_action( 'admin_init', [ $this, 'register_metaboxes' ] );
			}

			// Replace widget areas.
			add_action( 'get_header', [ $this, 'init_replace_widget_areas' ] );

			// Require conditionals class.
			if ( ! class_exists( 'WPEX_Widget_Areas_Conditions' ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'class-wpex-widget-areas-conditions.php';
			}

			// Initialize the widget area conditions metabox class.
			new WPEX_Widget_Areas_Conditions;
		}

		/**
		 * Admin hooks.
		 */
		public function admin_hooks() {
			add_action( 'admin_head', [ $this, 'remove_admin_column_filter' ] );
			add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ $this, 'set_admin_columns' ] );
			add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ $this, 'show_admin_columns' ], 10, 2 );
			if ( class_exists( 'Vc_Manager' ) ) {
				add_filter( 'vc_is_valid_post_type_be', [ $this, 'disable_wpbakery' ], 10, 2 );
				add_filter( 'vc_show_button_fe', [ $this, 'remove_wpbakery_button_fe' ], 10, 3 );
			}
		}

		/**
		 * Register wpex_widget_areas type.
		 */
		public function register_post_type() {
			register_post_type( self::POST_TYPE, [
				'labels' => array(
					'name' => esc_html__( 'Widget Areas', 'total-theme-core' ),
					'singular_name' => esc_html__( 'Widget Area', 'total-theme-core' ),
					'add_new' => esc_html__( 'Add Widget Area' , 'total-theme-core' ),
					'add_new_item' => esc_html__( 'Add Widget Area' , 'total-theme-core' ),
					'edit_item' => esc_html__( 'Edit Widget Area' , 'total-theme-core' ),
					'new_item' => esc_html__( 'Widget Area' , 'total-theme-core' ),
					'view_item' => esc_html__( 'View Widget Area', 'total-theme-core' ),
					'search_items' => esc_html__( 'Search Widget Areas', 'total-theme-core' ),
					'not_found' => esc_html__( 'No Widget Areas found', 'total-theme-core' ),
					'not_found_in_trash' => esc_html__( 'No Widget Areas found in Trash', 'total-theme-core' ),
				),
				'public' => false,
				'query_var' => true,
				'_builtin' => false,
				'show_ui' => true,
				'show_in_nav_menus' => false,
				'show_in_admin_bar' => false,
				'capability_type' => 'page',
				'hierarchical' => false,
				'menu_position' => null,
				'rewrite' => false,
				'supports' => [ 'title' ],
				'show_in_menu' => 'themes.php',
				// Admins only
				'capabilities' => [
					'edit_post'          => 'edit_theme_options',
					'read_post'          => 'edit_theme_options',
					'delete_post'        => 'edit_theme_options',
					'edit_posts'         => 'edit_theme_options',
					'edit_others_posts'  => 'edit_theme_options',
					'delete_posts'       => 'edit_theme_options',
					'publish_posts'      => 'edit_theme_options',
					'read_private_posts' => 'edit_theme_options',
				],
			] );
		}

		/**
		 * Remove the admin columns sort filter.
		 */
		public function remove_admin_column_filter() {
			if ( self::POST_TYPE === get_current_screen()->post_type ) {
				add_filter( 'months_dropdown_results', '__return_empty_array' );
			}
		}

		/**
		 * Set admin columns.
		 */
		public function set_admin_columns( $columns ) {
			$columns['id']              = esc_html__( 'ID', 'total-theme-core' );
			$columns['area_to_replace'] = esc_html__( 'Area To Replace', 'total-theme-core' );
			$columns['conditions']      = esc_html__( 'Condition(s)', 'total-theme-core' );
			unset( $columns['date'] );
			return $columns;
		}

		/**
		 * Show admin columns.
		 */
		public function show_admin_columns( $column, $post_id ) {
			$registered_sidebars = $this->get_registered_sidebars();
			switch( $column ) {
				case 'id' :
					echo esc_html( $this->get_widget_area_id( $post_id ) );
				break;
				case 'area_to_replace' :
					$area_to_replace = get_post_meta( $post_id, '_wpex_widget_area_to_replace', true );
					if ( $area_to_replace && array_key_exists( $area_to_replace, $registered_sidebars ) ) {
						echo esc_html( $registered_sidebars[$area_to_replace] );
					} else {
						echo '&#8212;';
					}
				break;
				case 'conditions' :
					$conditions = get_post_meta( $post_id, '_wpex_widget_area_conditions', true );
					if ( is_array( $conditions ) ) {
						WPEX_Widget_Areas_Conditions::selected_conditions_display( $conditions );
					} else {
						echo '&#8212;';
					}
				break;
			}
		}

		/**
		 * Disable wpbakery builder from post type.
		 */
		public function disable_wpbakery( $check, $type ) {
			if ( self::POST_TYPE === $type ) {
				return false;
			}
			return $check;
		}

		/**
		 * Removes the edit with wpbakery button from the admin screen.
		 */
		public function remove_wpbakery_button_fe( $result, $post_id, $type ) {
			if ( self::POST_TYPE === $type ) {
				return false;
			}
			return $result;
		}

		/**
		 * Get widget area ID.
		 */
		public function get_widget_area_id( $post_id ) {
			$post = get_post( $post_id );
			if ( $post && isset( $post->post_name ) ) {
				return $post->post_name;
			}
		}

		/**
		 * Get widget area to replace.
		 */
		public function get_widget_area_to_replace( $post_id ) {
			return get_post_meta( $post_id, '_wpex_widget_area_to_replace', true );
		}

		/**
		 * Get widget area conditions
		 */
		public function get_widget_area_conditions( $post_id ) {
			return get_post_meta( $post_id, '_wpex_widget_area_conditions', true );
		}

		/**
		 * Return widget area posts.
		 */
		public function get_widget_area_posts() {
			static $widget_area_posts = null;
			if ( null === $widget_area_posts && post_type_exists( self::POST_TYPE ) ) {
				$get_widget_area_posts = new WP_Query( [
					'posts_per_page'   => apply_filters( 'wpex_widget_areas_upper_limit', 200 ),
					'orderby'          => 'date',
					'order'            => 'ASC',
					'post_type' 	   => self::POST_TYPE,
					'post_status'      => 'publish',
					'fields'           => 'ids',
				//	'suppress_filters' => false, // @todo disable filters to prevent translation issues?
				] );
				if ( $get_widget_area_posts && $get_widget_area_posts instanceof WP_Query ) {
					$widget_area_posts = $get_widget_area_posts->posts;
				}
			}
			if ( $widget_area_posts ) {
				return $widget_area_posts;
			}
		}

		/**
		 * Return widget areas.
		 */
		public function get_widget_areas() {
			$widget_areas = [];

			// Get deprecated mod widget areas.
			$deprecated_mod = get_theme_mod( 'widget_areas' );

			if ( $deprecated_mod && is_array( $deprecated_mod ) ) {
				foreach ( $deprecated_mod as $widget_area_name ) {
					$widget_areas[] = [
						'id'   => sanitize_key( $widget_area_name ),
						'name' => $widget_area_name,
					];
				}
			}

			// Get custom widget area posts.
			$custom_widget_areas = $this->get_widget_area_posts();

			if ( is_array( $custom_widget_areas ) && count( $custom_widget_areas ) > 0 ) {
				foreach ( $custom_widget_areas as $widget_area ) {
					$id = $this->get_widget_area_id( $widget_area );
					if ( $id ) {
						$widget_areas[] = [
							'id'              => $id,
							'name'            => get_the_title( $widget_area ),
							'area_to_replace' => $this->get_widget_area_to_replace( $widget_area ),
							'conditions'      => $this->get_widget_area_conditions( $widget_area ),
						];
					}
				}
			}

			return $widget_areas;
		}

		/**
		 * Register the custom widget areas.
		 */
		public function register_widget_areas() {
			$widget_areas = $this->get_widget_areas();

			if ( ! is_array( $widget_areas ) ) {
				return;
			}

			foreach ( $widget_areas as $widget_area ) {
				$this->register_widget_area( $widget_area );
			}
		}

		/**
		 * Register a single custom widget area.
		 */
		public function register_widget_area( $args ) {
			$this->register_sidebar( $args );
		}

		/**
		 * Register a custom sidebar widget area.
		 */
		public function register_sidebar( $args ) {
			if ( empty( $args['id'] ) || empty( $args['name'] ) ) {
				return;
			}

			$args = [
				'id'   => $args['id'],
				'name' => $args['name'],
			];

			if ( class_exists( 'TotalTheme\Helpers\Register_Widget_Area' ) ) {
				new TotalTheme\Helpers\Register_Widget_Area( 'sidebar', $args );
			} else {
				register_sidebar( $args );
			}
		}

		/**
		 * Migrate old widget areas from theme mods to posts via ajax.
		 */
		public function migrate_widget_areas_ajax() {
			check_ajax_referer( 'wpex_migrate_widget_areas_nonce', 'nonce' );

			$converted_widget_areas = [];

			$deprecated_mod = get_theme_mod( 'widget_areas' );

			if ( ! $deprecated_mod || ! is_array( $deprecated_mod ) ) {
				die();
			}

			@set_time_limit(0);

			// Save widget area backup just incase.
			add_option( 'widget_areas_backup', $deprecated_mod, '', false );

			// Get widget area posts.
			$widget_areas = get_posts( [
				'orderby'          => 'date',
				'order'            => 'ASC',
				'numberposts' 	   => 50,
				'post_type' 	   => self::POST_TYPE,
				'post_status'      => 'publish',
			//	'suppress_filters' => true,
			] );

			// Insert widget areas based on old theme_mod.
			foreach ( $deprecated_mod as $widget_area ) {

				$widget_area_slug = sanitize_key( $widget_area );

				if ( $widget_areas && array_key_exists( $widget_area_slug, $widget_areas ) ) {
					continue; // post already exists.
				}

				$post_id = wp_insert_post( [
					'post_type'   => self::POST_TYPE,
					'post_title'  => $widget_area,
					'post_status' => 'publish',
					'post_name'   => $widget_area_slug,
				] );

				// If post was created successfully, let's add the '_slug' post meta and remove it from theme_mod.
				if ( $post_id ) {
					$post = get_post( $post_id );
					if ( $post ) {
						$key = array_search( $post->post_title, $deprecated_mod, true );
						if ( false !== $key ) {
							unset( $deprecated_mod[$key] );
							set_theme_mod( 'widget_areas', $deprecated_mod );
						}
						$converted_widget_areas[] = $widget_area;
					}
				}

			}

			if ( $converted_widget_areas ) {
				echo json_encode( $converted_widget_areas );
			}

			die();
		}

		/**
		 * Display migration notice.
		 */
		public function migration_notice() {
			$current_screen = get_current_screen();

			if ( empty( $current_screen->id ) || 'edit-wpex_widget_area' !== $current_screen->id ) {
				return;
			}

			wp_enqueue_script(
				'totalthemecore-admin-widget-areas-migrate',
				\totalthemecore_get_js_file( 'admin/widget-areas-migrate' ),
				[],
				'1.0',
				true
			);

			?>

			<div id="wpex-migrate-widget-areas-notice" class="notice notice-warning">
				<p style="font-size:16px;"><?php esc_html_e( 'Please click the button below to migrate your old widget areas to the new system.', 'total-theme-core' ); ?></p>
				<p><a href="#" class="button button-primary" data-wpex-migrate-widget-areas data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpex_migrate_widget_areas_nonce' ) ); ?>"><?php esc_html_e( 'Migrate Widget Areas', 'total-theme-core' ); ?></a></strong></p>
				<p class="wpex-migrate-widget-areas-loader hidden"><svg height="20px" width="20px" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg"><circle cx="18" cy="18" r="18" fill="#a2a2a2" fill-opacity=".5"/><circle cx="18" cy="8" r="4" fill="#fff"><animateTransform attributeName="transform" dur="1100ms" from="0 18 18" repeatCount="indefinite" to="360 18 18" type="rotate"/></circle></svg></p>
			</div>
		<?php }

		/**
		 * Register metaboxes.
		 */
		public function register_metaboxes(): void {
			new WPEX_Meta_Factory( [
				'id'       => 'wpex_widget_areas_area_to_replace',
				'title'    => esc_html__( 'Widget Area to Replace', 'total-theme-core' ),
				'screen'   => [ self::POST_TYPE ],
				'context'  => 'normal',
				'priority' => 'default',
				'fields'   => [ $this, 'get_metabox_fields' ],
			] );
		}

		/**
		 * Register metaboxes.
		 */
		public function get_metabox_fields(): array {
			return [
				[
					'id' => '_wpex_widget_area_to_replace',
					'type' => 'select',
					'choices' => [ $this, 'get_area_to_replace_choices' ],
				],
			];
		}

		/**
		 * Get array of registered sidebars.
		 */
		public function get_registered_sidebars() {
			global $wp_registered_sidebars;

			$registered_sidebars = [];
			$custom_sidebars     = [];

			// Exclude the custom widget areas from the dropdown.
			$custom_widget_areas = $this->get_widget_area_posts();

			if ( is_array( $custom_widget_areas ) && count( $custom_widget_areas ) > 0 ) {
				foreach ( $custom_widget_areas as $widget_area ) {
					$id = $this->get_widget_area_id( $widget_area );
					if ( $id ) {
						$custom_sidebars[] = $id;
					}
				}
			}

			if ( is_array( $wp_registered_sidebars ) && ( count( $wp_registered_sidebars ) > 0 ) ) {
				foreach ( $wp_registered_sidebars as $k => $v ) {
					if ( ! in_array( $v['id'], $custom_sidebars ) ) {
						$registered_sidebars[$v['id']] = $v['name'];
					}
				}
			}

			return $registered_sidebars;
		}

		/**
		 * init_replace_widget_areas function.
		 */
		public function init_replace_widget_areas() {
			add_filter( 'sidebars_widgets', [ $this, 'replace_widget_areas' ] );
		}

		/**
		 * Used to replace widget areas with custom ones.
		 */
		public function replace_widget_areas( $sidebars_widgets ) {
			if ( is_admin() ) {
				return $sidebars_widgets;
			}

			$custom_widget_areas = $this->get_widget_areas();

			if ( $custom_widget_areas ) {
				foreach ( $custom_widget_areas as $custom_widget_area ) {
					if ( empty( $custom_widget_area['area_to_replace'] ) ) {
						continue;
					}
					$area_to_replace = $custom_widget_area['area_to_replace'];
					if ( isset( $sidebars_widgets[$custom_widget_area['id']] )
						&& array_key_exists( $area_to_replace, $sidebars_widgets )
						&& $this->maybe_replace_widget_area( $custom_widget_area )
					) {
						$widgets = $sidebars_widgets[$custom_widget_area['id']];
						// Important, only override if we have widgets, otherwise the is_active_sidebar
						// check will fail and so the widget area may fallback to the incorrect sidebar area.
						if ( $widgets ) {
							unset( $sidebars_widgets[$area_to_replace] );
							$sidebars_widgets[$area_to_replace] = $widgets;
						}
					}
				}
			}

			return $sidebars_widgets;
		}

		/**
		 * Returns true if we should replace the current widget area based on the widget conditionals.
		 */
		public function maybe_replace_widget_area( $widget_area ) {
			if ( empty( $widget_area['conditions'] ) ) {
				return true;
			}
			return WPEX_Widget_Areas_Conditions::frontend_check( $widget_area['conditions'] );
		}

		/**
		 * Callback function for the metabox.
		 */
		public function get_area_to_replace_choices() {
			$area_to_replace_choices = [
				'' => esc_html__( '— Select —', 'total-theme-core' )
			];

			$registered_sidebars = $this->get_registered_sidebars();

			if ( is_array( $registered_sidebars ) && count( $registered_sidebars ) > 0 ) {
				$area_to_replace_choices = $area_to_replace_choices + $registered_sidebars ;
			}
			return $area_to_replace_choices;
		}

	}

	WPEX_Widget_Areas::instance();

}
