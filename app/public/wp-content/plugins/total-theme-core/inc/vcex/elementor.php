<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Elementor integration.
 */
final class Elementor {

	/**
	 * Custom widgets category id.
	 */
	const CATEGORY_ID = 'vcex';

	/**
	 * Dynamic Category ID.
	 */
	const DYNAMIC_CATEGORY_ID = 'vcex_dynamic';

	/**
	 * WooCommerce Category ID.
	 */
//	const WOO_CATEGORY_ID = 'vcex_woocommerce';

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Scripts.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Class Constructor.
	 */
	private function __construct() {
		if ( ! \defined( 'VCEX_ELEMENTOR_INTEGRATION' ) ) {
			\define( 'VCEX_ELEMENTOR_INTEGRATION', true );
		}
		\add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		\add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		\add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'editor_scripts' ] );
	}

	/**
	 * Register widgets.
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! \class_exists( '\TotalThemeCore\Vcex\Elementor\Register_Controls' ) ) {
			return; // Preload required class
		}

		$widgets = [
			'Alert',
			'Animated_Text',
			'Button',
			'Bullets',
			'Callout',
			'Contact_Form',
			'Countdown',
			'Custom_Field',
			'Divider',
			'Divider_Multicolor',
			'Divider_Dots',
			'Feature_Box',
			'Heading',
			'Horizontal_Menu',
			'Icon',
			'Icon_Box',
			'Image',
			'Image_Banner',
			'Image_Before_After',
			'Image_Swap',
			'Leader',
			'List_Item',
			'Login_Form',
			'Milestone',
			'Navbar',
			'Newsletter',
			'Off_Canvas_Menu',
			'Pricing',
			'Teaser',
			'Testimonials_Slider',
			'Toggle',
			'Skillbar',
			'Star_Rating',
			'Searchbar',
			'Social_Links',
			'Social_Share',
			'Steps',
			'Post_Cards',
			'Term_Cards',
			'Video',

			// Galleries.
			'Image_Grid',
			'Image_Carousel',
			'Image_Slider',

			// Dynamic
			'Page_Title',
			'Post_Media',
			'Post_Meta',
			'Post_Next_Prev',
			'Post_Terms',
			'Post_Content',
			'Post_Excerpt',
			'Post_Comments',
			'Author_Bio',
			'Breadcrumbs',
			'Term_Description',

			// Widgets
			'Widget_Modern_Menu',
		];

		if ( \function_exists( 'totaltheme_call_static' ) && (bool) \totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
			$widgets[] = 'Dark_Mode_Toggle';
		}

		if ( \class_exists( '\acf_pro' ) ) {
			$widgets[] = 'ACF_Repeater';
		}

		if ( \class_exists( '\TotalThemeCore\Post_Series', false ) ) {
			$widgets[] = 'Post_Series';
		}

		if ( \class_exists( '\WooCommerce', false ) ) {
			$widgets[] = 'Cart_Link';
		//	$widgets[] = 'WooCommerce_Template';
		}

		if ( \class_exists( '\Just_Events\Plugin', false ) ) {
			$widgets[] = 'Just_Events_Date';
			$widgets[] = 'Just_Events_Time';
		}

		foreach ( $widgets as $widget_name ) {
			$widget_class_name = 'TotalThemeCore\Vcex\Elementor\Widgets\\' . $widget_name;
			if ( \class_exists( $widget_class_name ) ) {
				$widget = new $widget_class_name();
				if ( \shortcode_exists( $widget->get_name() ) ) {
					$widgets_manager->register( $widget );
				}
			}
		}
	}

	/**
	 * Register Category.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category( self::CATEGORY_ID, [
			'title' => 'Total',
			'icon'  => 'ticon ticon-totaltheme',
		] );
		$elements_manager->add_category( self::DYNAMIC_CATEGORY_ID, [
			'title' => 'Total' . ' - ' . \esc_html__( 'Dynamic', 'total-theme-core' ),
			'icon'  => 'ticon ticon-totaltheme',
		] );
	}

	/**
	 * Enqueue JS in the editor.
	 */
	public function editor_scripts(): void {
		if ( ! totalthemecore_call_static( 'Elementor\Helpers', 'is_edit_mode' ) ) {
			return;
		}
		\wp_enqueue_script(
			'totalthemecore-admin-elementor-vcex-preview',
			\totalthemecore_get_js_file( 'admin/elementor/vcex/preview' ),
			[],
			TTC_VERSION,
			true
		);
	}

}
