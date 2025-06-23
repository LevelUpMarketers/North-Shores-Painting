<?php

defined( 'ABSPATH' ) || exit;

/**
 * Image Grid Shortcode.
 */
if ( ! class_exists( 'VCEX_Image_Grid' ) ) {

	class VCEX_Image_Grid extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_image_grid';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title(): string {
			return esc_html__( 'Image Grid', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Responsive image gallery', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'image-gallery',
				'js_view'          => 'vcexBackendViewImageGallery',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$params = array(
				array(
					'type' => 'attach_images',
					'heading' => esc_html__( 'Images', 'total-theme-core' ),
					'param_name' => 'image_ids',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'description' => esc_html__( 'Click the plus icon to add images to your gallery. Once images are added they can be drag and dropped for sorting.', 'total-theme-core' ) . ' ' . sprintf( esc_html__( 'Note: If you are going to be showing a lot of images it would be recommended to either use the Post Gallery field below or an image gallery plugin as mentioned in the  %sdocs%s.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/real-media-library-integration/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
					'std' => '',
					'value' => array(
						esc_html__( 'Default', 'total-theme-core' )  => '',
						esc_html__( 'Date', 'total-theme-core' )     => 'date',
						esc_html__( 'Title', 'total-theme-core' )    => 'title',
						esc_html__( 'Slug', 'total-theme-core' )     => 'name',
						esc_html__( 'Random', 'total-theme-core' )   => 'rand',
					),
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'DESC', 'total-theme-core' ) => 'DESC',
						esc_html__( 'ASC', 'total-theme-core' ) => 'ASC',
					),
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'dependency' => array( 'element' => 'orderby', 'value' => array( 'date', 'title', 'name' ) ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'admin_label' => true,
					'heading' => esc_html__( 'Post Gallery', 'total-theme-core' ),
					'param_name' => 'post_gallery',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Enable to display images from the current post "%sImage Gallery%s".', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/using-post-gallery-image-galleries/" target="_blank" rel="noopener noreferrer">', '</a>' ) . '<br>' . esc_html__( 'You can define images above to display as a fallback in the frontend editor when working with dynamic templates.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'gallery',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'custom_field_gallery',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// General
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Header', 'total-theme-core' ),
					'param_name' => 'header',
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Header Style', 'total-theme-core' ),
					'param_name' => 'header_style',
					'description' => self::param_description( 'header_style' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Horizontal Align', 'total-theme-core' ),
					'param_name' => 'content_alignment',
					'std' => 'center',
					'value' => array(
						esc_html__( 'None', 'total-theme-core' ) => 'none',
						esc_html__( 'Left', 'total-theme-core' ) => 'left',
						esc_html__( 'Center', 'total-theme-core' ) => 'center',
						esc_html__( 'Right', 'total-theme-core' ) => 'right',
					),
					'dependency' => array(
						'element' => 'grid_style',
						'value' => array( 'default', 'fit-rows', 'masonry', 'no-margins', 'css-grid' ),
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'std' => 'none',
					'heading'  => esc_html__( 'Vertical Align', 'total-theme-core' ),
					'param_name' => 'vertical_align',
					'value' => array(
						esc_html__( 'None', 'total-theme-core' ) => 'none',
						esc_html__( 'Top', 'total-theme-core' ) => 'top',
						esc_html__( 'Center', 'total-theme-core' ) => 'center',
						esc_html__( 'Bottom', 'total-theme-core' ) => 'bottom',
					),
					'dependency' => array( 'element' => 'grid_style', 'value' => array( 'default', 'fit-rows', 'css-grid' ) ),
					'description' => esc_html__( 'Selecting a vertical align will transform the entries into flex elements.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				vcex_vc_map_add_css_animation( array(
					'dependency' => array(
						'element' => 'grid_style',
						'value' => array( 'default', 'fit-rows', 'masonry', 'no-margins', 'css-grid' ),
					),
				) ),
				array(
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'param_name' => 'hover_animation',
					'dependency' => array(
						'element' => 'grid_style',
						'value' => array( 'default', 'fit-rows', 'masonry', 'no-margins', 'css-grid' ),
					),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Grid Style', 'total-theme-core' ),
					'param_name' => 'grid_style',
					'value' => array(
						esc_html__( 'Fit Rows', 'total-theme-core' ) => 'default',
						esc_html__( 'Modern CSS Grid', 'total-theme-core' ) => 'css-grid',
						esc_html__( 'Masonry', 'total-theme-core' ) => 'masonry',
						esc_html__( 'No Margins', 'total-theme-core' ) => 'no-margins',
						esc_html__( 'Justified', 'total-theme-core' ) => 'justified',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column clear',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_grid_columns',
					'heading' => esc_html__( 'Columns', 'total-theme-core' ),
					'param_name' => 'columns',
					'std' => '4',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency' => array(
						'element' => 'grid_style',
						'value' => array( 'default', 'fit-rows', 'masonry', 'no-margins', 'css-grid' ),
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Responsive', 'total-theme-core' ),
					'param_name' => 'responsive_columns',
					'value' => array(
						esc_html__( 'Yes', 'total-theme-core' ) => 'true',
						esc_html__( 'No', 'total-theme-core' ) => 'false',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency' => array(
						'element' => 'columns',
						'value' => array( '2', '3', '4', '5', '6', '7', '8', '9', '10' )
					),
					'dependency' => array(
						'element' => 'grid_style',
						'value' => array(
							'default',
							'fit-rows',
							'masonry',
							'no-margins',
							'css-grid'
						),
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_grid_columns_responsive',
					'heading' => esc_html__( 'Responsive Column Settings', 'total-theme-core' ),
					'param_name' => 'columns_responsive_settings',
					'dependency' => array( 'element' => 'responsive_columns', 'value' => 'true' ),
				//	'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Target Height', 'total-theme-core' ),
					'param_name' => 'justified_row_height',
					'std' => '200',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'dependency' => array( 'element' => 'grid_style', 'value' => array( 'justified' ) ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'justified_row_margin',
					'std' => '5',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'dependency' => array( 'element' => 'grid_style', 'value' => array( 'justified' ) ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Last Row', 'total-theme-core' ),
					'param_name' => 'justified_last_row',
					'std' => 'justify',
					'value' => array(
						esc_html__( 'Justfiy', 'total-theme-core' ) => 'justify',
						esc_html__( 'No Justify', 'total-theme-core' ) => 'nojustify',
						esc_html__( 'Hide', 'total-theme-core' ) => 'hide',
						esc_html__( 'Center', 'total-theme-core' ) => 'center',
						esc_html__( 'Left', 'total-theme-core' ) => 'left',
						esc_html__( 'Right', 'total-theme-core' ) => 'right',
					),
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'dependency' => array( 'element' => 'grid_style', 'value' => array( 'justified' ) ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Maintain Horizontal Order', 'total-theme-core' ),
					'description' => esc_html__( 'By default, the masonry script preserves the order of your items. However, if there is a lot of empty space in your grid, you can disable this setting to allow items to better fill the gaps.', 'total-theme-core' ),
					'param_name' => 'masonry_horizontal_order',
					'dependency' => array( 'element' => 'grid_style', 'value' => [ 'masonry', 'no-margins' ] ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Column Gap', 'total-theme-core' ),
					'param_name' => 'columns_gap',
					'choices' => 'gap',
					'dependency' => array(
						'element' => 'grid_style',
						'value' => array( 'default', 'fit-rows', 'masonry', 'no-margins', 'css-grid' ),
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Images Per Page', 'total-theme-core' ),
					'param_name' => 'posts_per_page',
					'value' => '-1',
					'description' => esc_html__( 'This will enable pagination for your gallery. Enter -1 or leave blank to display all images without pagination.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Pagination', 'total-theme-core' ),
					'param_name' => 'pagination',
					'value' => array(
						esc_html__( 'Numbered', 'total-theme-core' ) => 'numbered',
						esc_html__( 'Load More', 'total-theme-core' ) => 'loadmore',
						esc_html__( 'Infinite Scroll', 'total-theme-core' ) => 'infinite_scroll',
						esc_html__( 'Disabled', 'total-theme-core' ) => 'disabled',
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Links
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Image Link', 'total-theme-core' ),
					'param_name' => 'thumbnail_link',
					'std' => 'lightbox',
					'choices' => array(
						'none' => esc_html__( 'None', 'total-theme-core' ),
						'lightbox' => esc_html__( 'Lightbox', 'total-theme-core' ),
						'full_image' => esc_html__( 'Full Image', 'total-theme-core' ),
						'attachment_page' => esc_html__( 'Attachment Page', 'total-theme-core' ),
						'parent_page' => esc_html__( 'Uploaded To Page', 'total-theme-core' ),
						'custom_link' => esc_html__( 'Custom Links', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Link Title Attribute', 'total-theme-core' ),
					'param_name' => 'link_title_tag',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'description' => esc_html__( 'Enables the title tag on the links, based on the image alt text.', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'thumbnail_link',
						'value' => array( 'lightbox', 'attachment_page', 'custom_link', 'full_image' )
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Lightbox Title', 'total-theme-core' ),
					'param_name' => 'lightbox_title',
					'std' => 'alt',
					'choices' => array(
						'false' => esc_html__( 'None', 'total-theme-core' ),
						'alt' => esc_html__( 'Alt', 'total-theme-core' ),
						'title' => esc_html__( 'Title', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'lightbox' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lightbox Gallery', 'total-theme-core' ),
					'param_name' => 'lightbox_gallery',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'lightbox' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lightbox Caption', 'total-theme-core' ),
					'param_name' => 'lightbox_caption',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'lightbox' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Target', 'total-theme-core' ),
					'param_name' => 'custom_links_target',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Same window', 'total-theme-core' ) => '_self',
						esc_html__( 'New window', 'total-theme-core' ) => '_blank'
					),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => array( 'custom_link' ) ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Custom links', 'total-theme-core' ),
					'param_name' => 'custom_links',
					'description' => esc_html__( 'Enter links for each slide here. Divide links with linebreaks (Enter). For images without a link enter a # symbol.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => array( 'custom_link' ) ),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Link Meta Key', 'total-theme-core' ),
					'param_name' => 'link_meta_key',
					'description' => esc_html__( 'If you are using a meta value (custom field) for your image links you can enter the meta key here.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'custom_link' ),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Custom Attributes', 'total-theme-core' ),
					'param_name' => 'link_attributes',
					'description' => esc_html__( 'Enter your custom attributes in the format of key|value. Hit enter after each set of attributes.', 'total-theme-core' ),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
				),
				// Image
				array(
					'type' => 'vcex_select',
					'choices' => 'aspect_ratio',
					'heading' => esc_html__( 'Image Aspect Ratio', 'total-theme-core' ),
					'param_name' => 'img_aspect_ratio',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'object_fit',
					'heading' => esc_html__( 'Image Fit', 'total-theme-core' ),
					'param_name' => 'img_object_fit',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_aspect_ratio', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'heading' => esc_html__( 'Image Border Radius', 'total-theme-core' ),
					'param_name' => 'img_border_radius',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'shadow',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'img_shadow',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'shadow',
					'heading' => esc_html__( 'Shadow: Hover', 'total-theme-core' ),
					'param_name' => 'img_shadow_hover',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'exclude_choices' => array( 'thumb-swap', 'category-tag', 'category-tag-two' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Excerpt Length', 'total-theme-core' ),
					'param_name' => 'overlay_excerpt_length',
					'value' => '15',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'overlay_style', 'value' => 'title-excerpt-hover' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'overlay_button_text',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'overlay_style', 'value' => 'hover-button' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Class', 'total-theme-core' ),
					'param_name' => 'img_el_class',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Title
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'no',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'title',
					'vcex' => array( 'off' => 'no', 'on' => 'yes' ),
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'grid_style',
						'value' => array( 'default', 'fit-rows', 'masonry', 'no-margins', 'css-grid' ),
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Tag', 'total-theme-core' ),
					'param_name' => 'title_tag',
					'std' => 'h2',
					'choices' => array(
						'h2' => 'h2',
						'h3' => 'h3',
						'h4' => 'h4',
						'h5' => 'h5',
						'div' => 'div',
					),
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Based On', 'total-theme-core' ),
					'param_name' => 'title_type',
					'std' => 'title',
					'choices' => array(
						'title' => esc_html__( 'Title', 'total-theme-core' ),
						'alt' => esc_html__( 'Alt', 'total-theme-core' ),
						'caption' => esc_html__( 'Caption', 'total-theme-core' ),
						'description' => esc_html__( 'Description', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'title_color',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
					'css' => [ 'selector' => '.entry-title', 'property' => 'color' ],
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
				),
				array(
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'title_font_family',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-family' ],
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'title_weight',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-weight' ],
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'text_transform',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'title_transform',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'text-transform' ],
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'title_size',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-size' ],
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'title_line_height',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'choices' => 'line_height',
					'css' => [ 'selector' => '.entry-title', 'property' => 'line-height' ],
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'title_margin',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'description' => self::param_description( 'margin' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'margin' ],
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
					
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'title_typo',
					'selector' => '.vcex-image-grid-entry-title > .entry-title',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'yes' ),
					'editors' => [ 'elementor' ],
				),
				// Excerpt
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'excerpt',
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'grid_style',
						'value' => array( 'default', 'fit-rows', 'masonry', 'no-margins', 'css-grid' ),
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Based On', 'total-theme-core' ),
					'param_name' => 'excerpt_type',
					'std' => 'caption',
					'choices' => array(
						'caption' => esc_html__( 'Caption', 'total-theme-core' ),
						'description' => esc_html__( 'Description', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'excerpt_color',
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'color' ],
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'excerpt_font_family',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'font-family' ],
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'excerpt_weight',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'font-weight' ],
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'text_transform',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'excerpt_transform',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'text-transform' ],
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'excerpt_size',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'font-size' ],
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'excerpt_line_height',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'line-height' ],
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'choices' => 'line_height',
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'excerpt_margin',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'margin' ],
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'description' => self::param_description( 'margin' ),
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'excerpt_typo',
					'selector' => '.vcex-image-grid-entry-excerpt',
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'editors' => [ 'elementor' ],
				),
				// Design Options
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'Wrap CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'Entry CSS box', 'total-theme-core' ),
					'param_name' => 'entry_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
				// Deprecated params
				array( 'type' => 'hidden', 'param_name' => 'lightbox_path' ),
				array( 'type' => 'hidden', 'param_name' => 'lightbox_loop' ),
				array( 'type' => 'hidden', 'param_name' => 'rounded_image' ),
				array( 'type' => 'hidden', 'param_name' => 'randomize_images' ),
				array( 'type' => 'hidden', 'param_name' => 'pagination_loadmore' ),
				// Hidden params needed for ajax
				array( 'type' => 'hidden', 'param_name' => 'entry_count' ),
				array( 'type' => 'hidden', 'param_name' => 'post_id' ),
				array( 'type' => 'hidden', 'param_name' => 'paged' ),
			);

			// Real Media Library integration
			if ( \defined( 'RML_VERSION' ) ) {
				$params[] = [
					'type'       => 'vcex_select',
					'choices'    => 'real_media_library_folders',
					'heading'    => \esc_html__( 'Real Media Library Folder', 'total-theme-core' ),
					'param_name' => 'rml_folder',
					'group'      => \esc_html__( 'Gallery', 'total-theme-core' ),
				];
			}

			return $params;
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			if ( empty( $atts['img_border_radius'] ) && isset( $atts['rounded_image'] ) ) {
				if ( 'yes' === $atts['rounded_image'] || 'true' == $atts['rounded_image'] ) {
					$atts['img_border_radius'] = 'round';
				}
				unset( $atts['rounded_image'] );
			}

			return self::parse_deprecated_attributes( $atts );
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = array() ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( isset( $atts['randomize_images'] ) ) {
				if ( empty( $atts['orderby'] ) && 'true' == $atts['randomize_images'] ) {
					$atts['orderby'] = 'rand';
				}
				unset( $atts['randomize_images'] );
			}

			if ( isset( $atts['pagination_loadmore'] )
				&& vcex_validate_att_boolean( 'pagination_loadmore', $atts ) ) {
				$atts['pagination'] = 'loadmore';
				unset( $atts['pagination_loadmore'] );
			} elseif ( isset( $atts['pagination'] ) ) {
				switch ( $atts['pagination'] ) {
					case 'true':
						$atts['pagination'] = 'numbered';
						break;
					case 'false':
						$atts['pagination'] = 'disabled';
						break;
				}
			}

			return $atts;
		}

		/**
		 * Advanced CSS.
		 */
		protected static function css_pre_render( $css, $atts = [] ): void {
			// Set gap
			if ( isset( $atts['columns_gap'] ) && '' !== $atts['columns_gap'] ) {
				$grid_style = $atts['grid_style'] ?? '';
				if ( 'justified' !== $grid_style ) {
					$css->add_extra_css( [
						'selector' => '{{WRAPPER}}',
						'property' => ( 'css-grid' === $grid_style ) ? 'gap' : '--wpex-row-gap',
						'val'      => $atts['columns_gap'],
					] );
				}
			}
		}

	}

}

new VCEX_Image_Grid;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Image_Grid' ) ) {
	class WPBakeryShortCode_Vcex_Image_Grid extends WPBakeryShortCode {}
}
