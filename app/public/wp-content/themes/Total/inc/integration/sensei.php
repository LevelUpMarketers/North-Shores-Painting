<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Sensei Integration.
 */
final class Sensei {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Sensei.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		\add_action( 'after_setup_theme', array( $this, 'declare_support' ) );
		\add_action( 'wp_enqueue_scripts', array( $this, 'load_custom_stylesheet' ), 10 );
		\add_filter( 'wpex_post_layout_class', array( $this, 'layouts' ), 10 );
		\add_filter( 'wpex_register_sidebars_array', array( $this, 'register_sensei_sidebar' ), 10 );
		\add_filter( 'wpex_get_sidebar', array( $this, 'display_sensei_sidebar' ), 10 );
		\add_action( 'sensei_before_main_content', array( $this, 'before_main_content' ), 10 );
		\add_action( 'sensei_after_main_content', array( $this, 'after_main_content' ), 10 );
		\add_filter( 'wpex_title', array( $this, 'alter_title' ) );
		\add_filter( 'wpex_breadcrumbs_trail', array( $this, 'breadcrumbs_trail' ) );
		\add_filter( 'wpex_has_term_description_above_loop', array( $this, 'has_term_description_above_loop' ) );
		\add_action( 'wpex_hook_content_top', array( $this, 'above_content_module_title' ), 10 );

		// Get global Sensei class.
		global $woothemes_sensei;

		// Hook into the global $woothemes_sensei object to tweak things.
		if ( ! empty( $woothemes_sensei ) && is_object( $woothemes_sensei ) ) {

			// Remove duplicate pagination.
			\remove_action( 'sensei_pagination', array( $woothemes_sensei->frontend, 'sensei_output_content_pagination' ), 10 );

			// Remove default wrappers.
			\remove_action( 'sensei_before_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
			\remove_action( 'sensei_after_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );

		}

		// Add custommizer options.
		if ( \is_customize_preview() ) {
			\add_filter( 'wpex_customizer_panels', array( $this, 'customizer_settings' ) );
		}

		// Remove single lesson title.
		if ( 'post_title' === \get_theme_mod( 'lesson_single_header' ) ) {
			\remove_action( 'sensei_single_lesson_content_inside_before', array( 'Sensei_Lesson', 'the_title' ), 15 );
		}

	}

	/**
	 * Declare theme support.
	 */
	public static function declare_support() {
		\add_theme_support( 'sensei' );
	}

	/**
	 * Load custom CSS file for tweaks only when needed.
	 */
	public static function load_custom_stylesheet(): void {
		if ( is_sensei() || is_tax( 'module' ) ) {
			wp_enqueue_style(
				'wpex-sensei',
				totaltheme_get_css_file( 'frontend/sensei' ),
				[],
				WPEX_THEME_VERSION
			);
		}
	}

	/**
	 * Declare layout.
	 */
	public static function layouts( $layout ) {
		if ( \function_exists( 'is_sensei' ) && \is_sensei() ) {
			$layout = \get_theme_mod( 'sensei_page_layout' ) ?: $layout;
		}

		if ( self::is_learner_profile_page() ) {
			$layout = \get_theme_mod( 'sensei_learner_profile_layout' ) ?: $layout;
		}

		if ( self::is_quiz() ) {
			$layout = \get_theme_mod( 'sensei_quiz_layout' ) ?: $layout;
		}

		$types = array( 'course', 'lesson' );

		foreach ( $types as $type ) {
			if ( \is_post_type_archive( $type ) ) {
				$layout = \get_theme_mod( "{$type}_archives_layout" ) ?: $layout;
			}
			if ( \is_singular( $type ) ) {
				$layout = \get_theme_mod( "{$type}_single_layout" ) ?: $layout;
			}
		}

		return $layout;
	}

	/**
	 * Add custom sidebar.
	 */
	public static function register_sensei_sidebar( $sidebars ) {
		$sidebars['sensei_sidebar'] = \esc_html__( 'Sensei Sidebar', 'total' );
		return $sidebars;
	}

	/**
	 * Alter main sidebar to display sensei sidebar.
	 */
	public static function display_sensei_sidebar( $sidebar ) {
		if ( \is_sensei() ) {
			$sidebar = 'sensei_sidebar';
		}
		return $sidebar;
	}

	/**
	 * Before main content wrapper.
	 */
	public static function before_main_content() {
		\ob_start();
		?>

		<div id="content-wrap" <?php totaltheme_content_wrap_class(); ?>>

			<?php \wpex_hook_primary_before(); ?>

			<div id="primary" class="content-area wpex-clr">

				<?php \wpex_hook_content_before(); ?>

				<div id="content" class="site-content wpex-clr">

					<?php \wpex_hook_content_top(); ?>

		<?php
		echo \ob_get_clean();
	}

	/**
	 * After main content wrapper.
	 */
	public static function after_main_content() {
		\ob_start();
		?>

					<?php \wpex_hook_content_bottom(); ?>

				</div>

				<?php \wpex_hook_content_after(); ?>

			</div>

			<?php \wpex_hook_primary_after(); ?>

		</div>

		<?php
		echo \ob_get_clean();
	}

	/**
	 * Alter main page header title.
	 */
	public static function alter_title( $title ) {
		if ( self::is_learner_profile_page() ) {
			$title = \esc_html__( 'Learner Profile', 'total' );
		}

		// Single Quiz.
		if ( \is_singular( 'quiz' ) ) {
			$obj = \get_post_type_object( 'quiz' );
			$title = $obj->labels->name;
		}

		// Module tax.
		elseif ( \is_tax( 'module' ) ) {
			global $wp_query;
			$term = $wp_query->get_queried_object();
			$tax = \get_taxonomy( $term->taxonomy );
			$title = $tax->labels->name;
		}

		// Course Results - MUST BE LAST.
		else {
			global $wp_query;
			if ( isset( $wp_query->query_vars['course_results'] ) ) {
				$title = \esc_html__( 'Course Results', 'total' );
			}
		}

		return $title;
	}

	/**
	 * Alter breadcrumbs trail.
	 *
	 * @todo check and make sure it's using latest breadcrumb helper functions.
	 */
	public static function breadcrumbs_trail( $trail ) {
		if ( self::is_learner_profile_page() ) {
			return '';
		}

		// Add course to single lesson and remove post type archive.
		if ( \is_singular( 'lesson' ) ) {

			unset( $trail['post_type_archive'] );

			$offset = 1;
			$og_trail = $trail;
			$courses_obj = \get_post_type_object( 'course' );
			$courses = '<a href="' . \esc_url( \get_post_type_archive_link( 'course' ) ) . '" itemprop="url"><span itemprop="title">' . \esc_html( $courses_obj->labels->name ) . '</span></a>';
			$lessons_obj = get_post_type_object( 'lesson' );
			$lessons = '<a href="' . \esc_url( \get_post_type_archive_link( 'lesson' ) ) . '" itemprop="url"><span itemprop="title">' . \esc_html( $lessons_obj->labels->name ) . '</span></a>';
			$course_id = \intval( \get_post_meta( \get_the_ID(), '_lesson_course', true ) );
			$course = '<a href="' . \esc_url( \get_permalink( $course_id ) ) . '" itemprop="url"><span itemprop="title">' . \esc_html( \get_the_title( $course_id ) ) . '</span></a>';
			$trail = \array_slice( $og_trail, 0, $offset, true ) + array(
				'courses_archive' => $courses,
				'lessons_archive' => $lessons,
				'lesson_course' => $course,
			) + \array_slice( $og_trail, $offset, NULL, true);

		}

		// Add course to Module.
		elseif ( \is_tax( 'module' ) ) {
			if ( ! empty( $_GET['course_id'] ) ) {
				$course_id = \absint( $_GET['course_id'] );
				$offset = 1;
				$og_trail = $trail;
				$courses_obj = \get_post_type_object( 'course' );
				$courses = '<a href="' . \esc_url( \get_post_type_archive_link( 'course' ) ) . '" itemprop="url"><span itemprop="title">' . \esc_html( $courses_obj->labels->name ) . '</span></a>';
				$lesson = '<a href="' . \esc_url( \get_permalink( $course_id ) ) . '" itemprop="url"><span itemprop="title">' . \esc_html( \get_the_title( $course_id ) ) . '</span></a>';
				$trail = array_slice( $og_trail, 0, $offset, true ) + array(
					'post_type_archive' => $courses,
					'module_course' => $lesson
				) + array_slice( $og_trail, $offset, NULL, true);
			}
		}

		// Course Results.
		else {
			global $wp_query;
			if ( isset( $wp_query->query_vars['course_results'] ) ) {

				// Add link to course.
				$course = \get_page_by_path( $wp_query->query_vars['course_results'], OBJECT, 'course' );
				$course_id = $course->ID;
				$trail['lesson_course'] = '<a href="' . \esc_url( \get_permalink( $course_id ) ) . '" itemprop="url"><span itemprop="title">' . \esc_html( \get_the_title( $course_id ) ) . '</span></a>';

				// And trail end.
				$trail['trail_end'] = \esc_html__( 'Course Results', 'total' );

			}
		}

		return $trail;
	}

	/**
	 * Set module term description above loop.
	 */
	public static function has_term_description_above_loop( $bool ) {
		if ( \is_tax( 'module' ) ) {
			$bool = true;
		}
		return $bool;
	}

	/**
	 * Add title above module term description.
	 */
	public static function above_content_module_title( $bool ) {
		if ( \is_tax( 'module' ) ) {
			echo '<h1>'. \single_term_title( '', false ) .'</h1>';
		}
	}

	/**
	 * Adds Customizer settings.
	 */
	public function customizer_settings( $panels ) {
		$branding = ( $branding = \wpex_get_theme_branding() ) ? ' (' . $branding . ')' : '';
		$panels['sensei'] = [
			'title'    => "Sensei LMS{$branding}",
			'settings' => WPEX_INC_DIR . 'integration/sensei/customizer-settings.php',
		];
		return $panels;
	}

	/**
	 * Check if currently viewing a learner profile page.
	 */
	public static function is_learner_profile_page() {
		if ( \class_exists( '\Sensei_Utils' ) && \Sensei_Utils::is_learner_profile_page() ) {
			return true;
		}
	}

	/**
	 * Check if currently viewing a single quiz page.
	 */
	public static function is_quiz() {
		if ( \is_singular( 'quiz' ) ) {
			return true;
		}
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
