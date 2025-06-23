<?php

defined( 'ABSPATH' ) || exit;

// @todo add JS to automatically update Woo fragments when these settings change if possible.
$refresh_desc = esc_html__( 'You must save your options and refresh your live site to preview changes to this setting. You may have to also add or remove an item from the cart to clear the WooCommerce cache.', 'total' );
$refresh_desc_2 = esc_html__( 'You must save your options and refresh your live site to preview changes to this setting.', 'total' );

// General
$this->sections['wpex_woocommerce_general'] = [
	'title' => esc_html__( 'General', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_custom_sidebar',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Custom Sidebar', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'woo_header_product_searchform',
			'control' => [
				'label' => esc_html__( 'Header Product Search', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' =>  esc_html__( 'When enabled the header search function will make use of the WooCommerce search functionality to only search for products.', 'total' ),
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'modal',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'woo_show_og_price',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Show Original Price', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' =>  esc_html__( 'When enabled the original price will display on sale items.', 'total' ),
			],
			'inline_css' => [
				'target' => '.woocommerce ul.products li.product .price del,.woocommerce div.product div.summary del',
				'alter' => 'display',
				'sanitize' => 'checkbox',
			],
		],
		[
			'id' => 'woo_block_notices_enable',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Block Notices', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' =>  esc_html__( 'Enables the new Gutenberg block based notices.', 'total' ),
			],
		],
		[
			'id' => 'woo_added_to_cart_notice',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Added to Cart Popup', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enables a popup when adding products to your cart via the AJAX add to cart function showing the product that was added.', 'total' ),
			],
		],
		[
			'id' => 'woo_add_to_cart_message_enable',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Enable Add to Cart Message', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' =>  esc_html__( 'Can be used to disable the WooCommerce added to cart message that displays on the single product pages.', 'total' ),
			],
		],
		[
			'id' => 'woo_scroll_to_notices_enable',
			'default' => false,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Enable Scroll to Notices', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' =>  esc_html__( 'By default WooCommerce automatically scrolls to notices when updating the cart and check out pages. This can be very annoying so the theme disables it by default, but it can be re-enabled via this setting.', 'total' ),
			],
		],
		[
			'id' => 'woo_sale_flash_text',
			'control' => [
				'label' => esc_html__( 'On Sale Text', 'total' ),
				'type' => 'text',
			],
		],
		[
			'id' => 'woo_quantity_buttons_style',
			'default' => 'vertical',
			'control' => [
				'label' => esc_html__( 'Quantity Buttons Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'vertical' => esc_html__( 'Vertical', 'total' ),
					'horizontal' => esc_html__( 'Horizontal', 'total' ),
					'browser' => esc_html__( 'Browser Default', 'total' ),
					'disabled' => esc_html__( 'Disabled', 'total' ),
				],
			],
		],
	],
];

// Off Canvas Cart
$this->sections['wpex_woo_off_canvas_cart'] = [
	'title' => esc_html__( 'Off Canvas Cart', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_off_canvas_cart_enable',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Enable', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'By default, the Off Canvas cart is activated only when triggered through the header cart icon. This setting allows you to enable the feature globally, making it accessible for use with custom functions like the Header Builder.', 'total' ),
			],
		],
		[
			'id' => 'woo_off_canvas_cart_auto_open',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Auto Open', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Automatically open the Off Canvas cart when products are added via AJAX.', 'total' ),
			],
		],
		[
			'id' => 'woo_off_canvas_cart_title',
			'control' => [
				'label' => esc_html__( 'Title', 'total' ),
				'type' => 'text',
				'input_attrs' => [
					'placeholdeer' => \esc_html__( 'Your Cart', 'total' ),
				],
			],
		],
		[
			'id' => 'woo_off_canvas_cart_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'placeholder' => '448',
			],
			'inline_css' => [
				'target' => '#wpex-off-canvas-cart',
				'alter' => '--wpex-off-canvas-width',
			],
		],
		[
			'id' => 'woo_off_canvas_cart_thumb_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Thumbnail Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'placeholder' => '100',
			],
			'inline_css' => [
				'target' => '#wpex-off-canvas-cart .wpex-mini-cart-item__image',
				'alter' => 'width',
			],
		],
	],
];

// Header Cart
$this->sections['wpex_woocommerce_menu_cart'] = [
	'title' => esc_html__( 'Header Cart', 'total' ),
	'panel' => 'wpex_woocommerce',
	'description' => esc_html__( 'To properly view the changes for various settings in this panel it will require saving, viewing the live site and adding or removing items from your cart due to WooCommerce caching.', 'total' ),
	'settings' => [
		[
			'id' => 'woo_menu_icon_class',
			'default' => 'shopping-cart',
		//	'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Icon', 'total' ),
				'type' => 'totaltheme_icon',
				'choices' => \totaltheme_call_static( 'Integration\WooCommerce\Cart', 'icon_choices' ),
			],
		],
		[
			'id' => 'woo_menu_cart_enable',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Menu Cart', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'woo_menu_icon_display',
			'default' => 'icon_count',
		//	'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Menu Cart: Display', 'total' ),
				'description' => esc_html__( 'Important: Choosing anything besides "Icon" will require loading the WooCommerce "fragments" script which is known to potentially slow down your site.', 'total' ),
				'type' => 'select',
				'choices' => [
					'icon' => esc_html__( 'Icon', 'total' ),
					'icon_total' => esc_html__( 'Icon with Cart Total', 'total' ),
					'icon_count' => esc_html__( 'Icon with Cart Count', 'total' ),
					'icon_dot' => esc_html__( 'Icon with Dot', 'total' ),
				],
			],
		],
		[
			'id' => 'woo_menu_icon_size',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Menu Cart: Icon Size', 'total' ),
				'type' => 'totaltheme_length_unit',
			],
			'inline_css' => [
				'target' => '.wcmenucart-icon,.wpex-header-cart-icon__icon',
				'alter' => 'font-size',
			],
		],
		[
			'id' => 'woo_menu_icon_style',
			'default' => 'off-canvas',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Menu Cart: Style', 'total' ),
				'type' => 'select',
				'description' => esc_html__( 'Important: Choosing anything besides "Go To Cart" or "Custom Link" will require loading the WooCommerce "fragments" script which is known to potentially slow down your site.', 'total' ),
				'choices' => 'TotalTheme\Integration\WooCommerce\Cart::style_choices',
			],
		],
		[
			'id' => 'woo_dropdown_cart_top_border_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Drop Down Top Border', 'total' ),
				'type' => 'totaltheme_color',
			],
			'inline_css' => [
				'target' => '#current-shop-items-dropdown',
				'alter' => '--wpex-dropmenu-colored-top-border-color',
			],
			'control_display' => [
				'check' => 'woo_menu_icon_style',
				'value' => 'drop_down',
			],
		],
		[
			'id' => 'woo_menu_icon_custom_link',
			'control' => [
				'label' => esc_html__( 'Menu Cart: Custom Link', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'You can use the format /{page_slug}/ to link to a page on the current site.', 'total' ),
			],
		],
		[
			'id' => 'has_woo_mobile_menu_cart_link',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Mobile Menu Cart Link', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled the cart link will display in the mobile menu as well.', 'total' ),
			],
		],
		[
			'id' => 'wpex_woo_menu_icon_bubble',
		//	'transport' => 'postMessage',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Cart Count Bubble', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enable to display the cart number inside a colored circle.', 'total' ),
			],
			'control_display' => [
				'check' => 'woo_menu_icon_display',
				'value' => 'icon_count',
			],
		],
		[
			'id' => 'wpex_woo_menu_icon_bubble_bg',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Cart Count Bubble Background', 'total' ),
				'type' => 'totaltheme_color',
			],
			'control_display' => [
				'check' => 'woo_menu_icon_display',
				'value' => 'icon_count',
			],
			'inline_css' => [
				'target' => '.wcmenucart-details.count.t-bubble,.wpex-header-cart-icon__count--bubble',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'wpex_woo_menu_icon_bubble_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Cart Count Bubble Color', 'total' ),
				'type' => 'totaltheme_color',
			],
			'control_display' => [
				'check' => 'woo_menu_icon_display',
				'value' => 'icon_count',
			],
			'inline_css' => [
				'target' => '.wcmenucart-details.count.t-bubble,.wpex-header-cart-icon__count--bubble',
				'alter' => 'color',
			],
		],
		[
			'id' => 'wpex_woo_menu_icon_dot_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Dot Color', 'total' ),
				'type' => 'totaltheme_color',
			],
			'control_display' => [
				'check' => 'woo_menu_icon_display',
				'value' => 'icon_dot',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-cart-dot-color',
			],
		],
		[
			'id' => 'wpex_woo_menu_icon_dot_size',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Dot Size', 'total' ),
				'type' => 'totaltheme_length_unit',
				'default_unit' => 'em',
				'placeholder' => '0.5',
			],
			'control_display' => [
				'check' => 'woo_menu_icon_display',
				'value' => 'icon_dot',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-cart-dot-size',
			],
		],
		[
			'id' => 'wpex_woo_menu_icon_dot_top',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Dot Vertical Offset', 'total' ),
				'type' => 'totaltheme_length_unit',
				'default_unit' => 'em',
				'placeholder' => '0',
			],
			'control_display' => [
				'check' => 'woo_menu_icon_display',
				'value' => 'icon_dot',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-cart-dot-top',
			],
		],
		[
			'id' => 'wpex_woo_menu_icon_dot_right',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Dot Horizontal Offset', 'total' ),
				'type' => 'totaltheme_length_unit',
				'default_unit' => 'em',
				'placeholder' => '-0.25',
			],
			'control_display' => [
				'check' => 'woo_menu_icon_display',
				'value' => 'icon_dot',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-cart-dot-right',
			],
		],
	],
];

// Archives
$this->sections['wpex_woocommerce_archives'] = [
	'title' => esc_html__( 'Shop & Archives', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_shop_disable_default_output',
			'control' => [
				'label' => esc_html__( 'Custom Shop Page', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enable to remove the default WooCommerce archive from your main shop page.', 'total' ),
			],
		],
		[
			'id' => 'woo_shop_title',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Shop Page Header Title', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enables the page header title for the main shop page.', 'total' ),
			],
		],
		[
			'id' => 'woo_archive_has_page_header',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Shop Archives Page Header Title', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enables the page header title on shop archives such as categories and tags.', 'total' ),
			],
		],
		[
			'id' => 'woo_shop_term_page_header_image_enabled',
			'control' => [
				'label' => esc_html__( 'Page Header Background', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled the product category and tags will use the background page header style by default when a thumbnail is set just like standard categories.', 'total' ),
			],
			'control_display' => [
				'check' => 'woo_shop_title',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_shop_sort',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Sort Dropdown', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enables the select option to sort products on the shop archives.', 'total' ),
			],
		],
		[
			'id' => 'woo_shop_result_count',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Results Count', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enables the "Showing x results" text on the shop archive.', 'total' ),
			],
		],
		[
			'id' => 'woo_category_description_position',
			'default' => 'under_title',
			'control' => [
				'label' => esc_html__( 'Category Description Position', 'total' ),
				'type' => 'select',
				'choices' => [
					''			  => esc_html__( 'Default', 'total' ),
					'under_title' => esc_html__( 'Under Title', 'total' ),
					'above_loop'  => esc_html__( 'Before Entries', 'total' ),
					'hidden'      => esc_html__( 'Hidden', 'total' ),
				],

			],
		],
		[
			'id' => 'woo_archive_template_id',
			'control' => [
				'label' => esc_html__( 'Dynamic Template', 'total' ),
				'type' => 'totaltheme_template_select',
				'template_type' => 'archive',
			],
		],
		[
			'id' => 'woo_shop_posts_per_page',
			'default' => '12',
			'control' => [
				'label' => esc_html__( 'Products Per Page', 'total' ),
				'type' => 'text',
			],
		],
		[
			'id' => 'woo_shop_layout',
			'default' => 'full-width',
			'control' => [
				'label' => esc_html__( 'Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		[
			'id' => 'woocommerce_shop_columns',
			'default' => '4',
			'control' => [
				'label' => esc_html__( 'Columns', 'total' ),
				'type' => 'wpex-columns',
			],
		],
		[
			'id' => 'woo_shop_columns_gap',
			'control' => [
				'label' => esc_html__( 'Gap', 'total' ),
				'type' => 'select',
				'choices' => 'column_gap',
			],
		],
	],
];

// Entry settings
$this->sections['wpex_woocommerce_entry'] = [
	'title' => esc_html__( 'Product Entry', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_entry_card_style',
			'control' => [
				'label' => esc_html__( 'Card Style', 'total' ),
				'type' => 'totaltheme_card_select',
			],
		],
		[
			'id' => 'woo_show_entry_title',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Entry Title', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style',
			],
			'inline_css' => [
				'target' => '.woocommerce .products .product .woocommerce-loop-product__title',
				'alter' => 'display',
				'sanitize' => 'checkbox',
			],
		],
		[
			'id' => 'woo_show_entry_rating',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Entry Rating', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style',
			],
			'inline_css' => [
				'target' => '.woocommerce .products .product .star-rating',
				'alter' => 'display',
				'sanitize' => 'checkbox',
			],
		],
		[
			'id' => 'woo_show_entry_price',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Entry Price', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style',
			],
			'inline_css' => [
				'target' => '.woocommerce .products .product .price',
				'alter' => 'display',
				'sanitize' => 'checkbox',
			],
		],
		[
			'id' => 'woo_show_entry_add_to_cart',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Add to Cart Button', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style',
			],
			'inline_css' => [
				'target' => '.woocommerce .products .product a.button',
				'alter' => 'display',
				'sanitize' => 'checkbox',
			],
		],
		[
			'id' => 'woo_entry_equal_height',
			'control' => [
				'label' => esc_html__( 'Bottom Align Buttons', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style',
			],
			'control_display' => [
				'check' => 'woo_default_entry_buttons',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_default_entry_buttons',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Always Visible Add to Cart Button', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => $refresh_desc_2,
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style',
			],
		],
		[
			'id' => 'woo_entry_align',
			'control' => [
				'label' => esc_html__( 'Entry Alignment', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default','total' ),
					'left' => esc_html__( 'Left','total' ),
					'right' => esc_html__( 'Right','total' ),
					'center' => esc_html__( 'Center','total' ),
				],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style',
			],
		],
		[
			'id' => 'woo_product_entry_style',
			'default' => 'image-swap',
			'control' => [
				'label' => esc_html__( 'Entry Media Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'featured-image' => esc_html__( 'Featured Image', 'total' ),
					'image-swap' => esc_html__( 'Image Swap', 'total' ),
					'gallery-slider' => esc_html__( 'Gallery Slider', 'total' ),
				],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style',
			],
		],
	],
];

// Single
$this->sections['wpex_woocommerce_single'] = [
	'title' => esc_html__( 'Single Product', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_product_has_page_header',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Page Header Title', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
			],
		],
		[
			'id' => 'woo_show_post_rating',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Product Rating', 'total' ),
				'description' => esc_html__( 'Display the total product rating.', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
		],
		[
			'id' => 'woo_product_meta',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Product Meta', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Categories, Tags, SKU, etc.', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
		],
		[
			'id' => 'woo_next_prev',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Next/Previous Links', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'woo_single_product_sticky_gallery',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Sticky Product Gallery', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'woo_single_product_sticky_summary',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Sticky Product Summary', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'If you enable this option we recommend disabling the "Product Gallery Slider" and setting the "Gallery Thumbnails Columns" to 1. This will create the popular effect where it looks like the images scroll vertically.', 'total' ),
			],
		],
		[
			'id' => 'woo_single_product_vertical_align',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Vertical Align', 'total' ),
				'description' => esc_html__( 'Enable to vertically align the product Gallery and side content.', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'woo_single_product_layout_reverse',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Reverse Layout', 'total' ),
				'description' => esc_html__( 'Enable to display the gallery on the right and the details on the left.', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'woo_product_qty_btn_wrapper',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Group Quantity and Button', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'By default the theme displays the quantity input and add to cart button side by side (always disabled when using the WooCommerce Payments plugin to prevent conflicts).', 'total' ),
			],
			'control_display' => [
				'check' => 'woo_product_add_to_cart_full_width',
				'value' => 'false',
			],
		],
		[
			'id' => 'woo_shop_single_title',
			'default' => esc_html__( 'Store', 'total' ),
			'sanitize_callback' => 'wp_kses_post',
			'control' => [
				'label' => esc_html__( 'Page Header Title Text', 'total' ),
				'description' => \sprintf( esc_html__( 'This field supports %sdynamic variables%s', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/dynamic-variables/" target="_blank" rel="noopener noreferrer">', '&#8599;</a>' ),
				'type' => 'text',
			],
			'control_display' => [
				'check' => 'woo_product_has_page_header',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_layout',
			'default' => 'full-width',
			'control' => [
				'label' => esc_html__( 'Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		[
			'id' => 'woo_singular_template',
			'control' => [
				'label' => esc_html__( 'Dynamic Template', 'total' ),
				'type' => 'totaltheme_template_select',
				'template_type' => 'single',
			],
		],
		[
			'id' => 'woo_product_gallery_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Product Gallery Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'default_unit' => '%',
				'placeholder' => '52',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-woo-product-gallery-width',
			],
		],
		[
			'id' => 'woo_product_summary_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Product Summary Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'default_unit' => '%',
				'placeholder' => '44',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-woo-product-summary-width',
			],
		],
		[
			'id' => 'woo_product_summary_text_align',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Product Summary Text Align', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'center' => esc_html__( 'Center', 'total' ),
				],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
			'inline_css' => [
				'target' => '.woocommerce .product .summary',
				'alter' => 'text-align',
			],
		],
		[
			'id' => 'woocommerce_upsells_count',
			'default' => '4',
			'control' => [
				'label' => esc_html__( 'Up-Sells Count', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter 0 to disable.', 'total' ),
			],
		],
		[
			'id' => 'woocommerce_upsells_columns',
			'default' => '4',
			'control' => [
				'label' => esc_html__( 'Up-Sells Columns', 'total' ),
				'type' => 'wpex-columns',
			],
		],
		[
			'id' => 'woocommerce_related_count',
			'default' => '4',
			'control' => [
				'label' => esc_html__( 'Related Items Count', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter 0 to disable.', 'total' ),
			],
		],
		[
			'id' => 'woocommerce_related_columns',
			'default' => '4',
			'control' => [
				'label' => esc_html__( 'Related Products Columns', 'total' ),
				'type' => 'wpex-columns',
			],
		],
	],
];

// Gallery
$this->sections['wpex_woocommerce_product_gallery'] = [
	'title' => esc_html__( 'Single Product Gallery', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_product_gallery_slider',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Product Gallery Slider', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'woo_product_gallery_slider_arrows',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Product Gallery Slider Arrows', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'woo_product_gallery_slider',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_gallery_zoom',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Product Image Zoom', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enables zooming on the product images while hovering.', 'total' ),
			],
		],
		[
			'id' => 'woo_product_gallery_slider_animation_speed',
			'default'  => '600',
			'control' => [
				'label' => esc_html__( 'Product Gallery Slider Animation Speed', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter a value in milliseconds.', 'total' )
			],
			'control_display' => [
				'check' => 'woo_product_gallery_slider',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_gallery_lightbox',
			'default' => 'total',
			'control' => [
				'label' => esc_html__( 'Product Gallery Lightbox', 'total' ),
				'type' => 'select',
				'choices' => [
					'disabled' => esc_html__( 'Disabled', 'total' ),
					'total' => esc_html__( 'Theme Lightbox', 'total' ),
					'woo' => esc_html__( 'WooCommerce Lightbox', 'total' ),
				],
			],
		],
		[
			'id' => 'woo_product_gallery_lightbox_titles',
			'control' => [
				'label' => esc_html__( 'Lightbox Titles', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'woo_product_gallery_lightbox',
				'value' => 'total',
			],
		],
		[
			'id' => 'woocommerce_gallery_thumbnails_count',
			'default' => 5,
			'control' => [
				'label' => esc_html__( 'Gallery Thumbnails Columns', 'total' ),
				'type' => 'select',
				'choices' => [
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
				],
			],
		],
		[
			'id' => 'woocommerce_gallery_thumbnails_gap',
			'transport' => 'refresh',
			'control' => [
				'type' => 'text',
				'label' => esc_html__( 'Gallery Thumbnails Gap', 'total' ) . ' (px)',
				'input_attrs' => [
					'placeholder' => '8px',
				],
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-woo-product-gallery-thumbs-gap',
				'sanitize' => 'px',
			],
		],
	],
];

// Tabs
$this->sections['wpex_woocommerce_tabs'] = [
	'title' => esc_html__( 'Single Product Tabs', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_product_tabs_position',
			'default' => '',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Product Tabs Position', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'right' => esc_html__( 'Next to Image', 'total' ),
				],
				'description' => $refresh_desc_2,
			],
		],
		[
			'id' => 'woo_product_accordion_tabs',
			'control' => [
				'label' => esc_html__( 'Accordion Tabs', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Converts the default WooCommerce tabs into an Accordion.', 'total' ),
			],
		],
		[
			'id' => 'woo_product_accordion_tabs_animate',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Animate Accordion', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'woo_product_accordion_tabs',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_accordion_tabs_first_open',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Open First Accordion', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'woo_product_accordion_tabs',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_accordion_tab_title_tag',
			'default' => 'h3',
			'control' => [
				'label' => esc_html__( 'Accordion Title HTML Tag', 'total' ),
				'type' => 'select',
				'choices' => 'html_tag',
			],
			'control_display' => [
				'check' => 'woo_product_accordion_tabs',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_accordion_style',
			'default' => 'w-borders',
			'control' => [
				'label' => esc_html__( 'Accordion Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'w-borders' => esc_html__( 'With Borders', 'total' ),
					'none' => esc_html__( 'None', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'woo_product_accordion_tabs',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_accordion_icon_type',
			'default' => 'plus',
			'control' => [
				'label' => esc_html__( 'Accordion Icon Type', 'total' ),
				'type' => 'select',
				'choices' => [
					'plus' => esc_html__( 'Plus', 'total' ),
					'angle' => esc_html__( 'Angle', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'woo_product_accordion_tabs',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_accordion_icon_position',
			'default' => 'right',
			'control' => [
				'label' => esc_html__( 'Accordion Icon Position', 'total' ),
				'type' => 'select',
				'choices' => [
					'right' => esc_html__( 'Right', 'total' ),
					'left' => esc_html__( 'Left', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'woo_product_accordion_tabs',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_responsive_tabs',
			'control' => [
				'label' => esc_html__( 'Expanded Tabs on Mobile', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Hides the single product tab links and displays the content vertically with headings on devices smaller than 768px.', 'total' ),
			],
			'control_display' => [
				'check' => 'woo_product_accordion_tabs',
				'value' => 'false',
			],
		],
		[
			'id' => 'woo_product_tabs_margin_top',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Tabs Top Margin', 'total' )
			],
			'inline_css' => [
				'target' => '.woocommerce-tabs,.wpex-woo-product-accordion',
				'alter' => 'margin-block-start',
			],
		],
		[
			'id' => 'woo_product_tab_title_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Tab Title Font Size', 'total' )
			],
			'inline_css' => [
				'target' => '.wpex-woo-product-accordion .vcex-toggle__heading,.woocommerce-tabs .wc-tabs li a',
				'alter' => 'font-size',
			],
		],
		[
			'id' => 'woo_product_accordion_title_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Accordion Title Color', 'total' )
			],
			'inline_css' => [
				'target' => '.wpex-woo-product-accordion .vcex-toggle__heading',
				'alter' => 'color',
			],
			'control_display' => [
				'check' => 'woo_product_accordion_tabs',
				'value' => 'true',
			],
		],
		[
			'id' => 'woo_product_accordion_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Accordion Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.wpex-woo-product-accordion .vcex-toggle',
				'alter' => '--wpex-border-main',
			],
			'control_display' => [
				'check' => 'woo_product_accordion_tabs',
				'value' => 'true',
			],
		],
	],
];

// Social Share Buttons
$this->sections['wpex_woocommerce_social_share'] = [
	'title' => esc_html__( 'Social Share Buttons', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'social_share_woo',
			'control' => [
				'label' => esc_html__( 'Social Share', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'woo_social_share_label',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Labels', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'woo_product_social_share_style',
				'value' => [ 'flat', 'minimal', 'three-d', 'rounded', 'custom' ],
			],
		],
		[
			'id' => 'woo_product_social_share_location',
			'control' => [
				'label' => esc_html__( 'Location', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'woocommerce_share' => esc_html__( 'With Main Details', 'total' ),
				],
			],
		],
		[
			'id' => 'woo_product_social_share_heading',
			'default' => esc_html__( 'Share This', 'total' ),
			'control' => [
				'label' => esc_html__( 'Heading Text', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Leave blank to disable.', 'total' ),
			],
		],
		[
			'id' => 'woo_product_social_share_style',
			'default' => 'flat',
			'control' => [
				'label' => esc_html__( 'Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'flat' => esc_html__( 'Flat', 'total' ),
					'minimal' => esc_html__( 'Minimal', 'total' ),
					'three-d' => esc_html__( '3D', 'total' ),
					'rounded' => esc_html__( 'Rounded', 'total' ),
					'mag' => esc_html__( 'Magazine', 'total' ),
					'custom' => esc_html__( 'Custom', 'total' ),
				],
			],
		],
	],
];

// Cart
$this->sections['wpex_woocommerce_cart'] = [
	'title' => esc_html__( 'Cart', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_cart_breakpoint',
			'control' => [
				'label' => esc_html__( 'Cart Responsive breakpoint', 'total' ),
				'type' => 'totaltheme_length_unit',
				'placeholder' => '767',
				'units' => [ 'px' ],
				'description' => esc_html__( 'Breakpoint where the cart goes from a single vertical column to a table.', 'total' ),
			],
			'inline_css' => [
				'target' => 'table.cart .product-thumbnail img',
				'alter' => 'max-width',
			],
		],
		[
			'id' => 'woo_cart_thumb_max_width',
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Cart Product Thumbnail Max Width', 'total' ),
				'type' => 'totaltheme_length_unit',
			],
			'inline_css' => [
				'target' => 'table.cart .product-thumbnail img',
				'alter' => 'max-width',
			],
		],
		[
			'id' => 'woocommerce_cross_sells_count',
			'default' => '2',
			'control' => [
				'label' => esc_html__( 'Cross-Sells Count', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter 0 to disable.', 'total' ),
			],
		],
		[
			'id' => 'woocommerce_cross_sells_columns',
			'default' => '2',
			'control' => [
				'label' => esc_html__( 'Cross-Sells Columns', 'total' ),
				'type' => 'wpex-columns',
			],
		],
	],
];

// Checkout
$this->sections['wpex_woocommerce_checkout'] = [
	'title' => esc_html__( 'Checkout', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_checkout_single_col',
			'control' => [
				'label' => esc_html__( 'Single Column Checkout', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'woo_checkout_shipping_placement',
			'default' => 'right_col',
			'control' => [
				'label' => esc_html__( 'Shipping & Order Notes Placement', 'total' ),
				'description' => esc_html__( 'The WooCommerce shipping address and order notes both use the same action hook so you can\'nt move them independently.', 'total' ),
				'type' => 'select',
				'choices' => [
					'right_col' => esc_html__( 'Right Column', 'total' ),
					'left_col' => esc_html__( 'Left Column', 'total' ),
				],
			],
		],
		[
			'id' => 'woo_checkout_order_review_placement',
			'default' => 'right_col',
			'control' => [
				'label' => esc_html__( 'Order Review Placement', 'total' ),
				'type' => 'select',
				'choices' => [
					'right_col' => esc_html__( 'Right Column (below additonal information)', 'total' ),
					'left_col' => esc_html__( 'After Columns (below billing and additional information)', 'total' ),
				],
			],
		],
	],
];

// Styling
$this->sections['wpex_woocommerce_styling'] = [
	'title' => esc_html__( 'Styling', 'total' ),
	'panel' => 'wpex_woocommerce',
	'settings' => [
		[
			'id' => 'woo_product_add_to_cart_full_width',
			'control' => [
				'label' => esc_html__( 'Full Width Add to Cart Button', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'onsale_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'On Sale Tag Background', 'total' ),
			],
			'inline_css' => [
				'target' => '.woocommerce span.onsale',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'onsale_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'On Sale Tag Color', 'total' )
			],
			'inline_css' => [
				'target' => '.woocommerce span.onsale',
				'alter' => 'color',
			],
		],
		[
			'id' => 'woo_onsale_border_radius',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'em', 'rem', '%', 'var' ],
				'label' => esc_html__( 'On Sale Tag Border Radius', 'total' )
			],
			'inline_css' => [
				'target' => '.woocommerce span.onsale, .woocommerce .outofstock-badge',
				'alter' => 'border-radius',
				'sanitize' => 'fallback_px',
			],
		],
		[
			'id' => 'woo_onsale_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'label' => esc_html__( 'On Sale Tag Padding', 'total' ),
			],
			'inline_css' => [
				'target' => '.woocommerce span.onsale, .woocommerce .outofstock-badge',
				'alter' => 'padding',
			],
		],
		[
			'id' => 'woo_add_to_cart_popup_button_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Hover Add to Cart Button Background', 'total' )
			],
			'inline_css' => [
				'target' => '.wpex-loop-product-add-to-cart',
				'alter' => '--wpex-woo-btn-bg',
			],
		],
		[
			'id' => 'woo_add_to_cart_popup_button_gutter',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Hover Add to Cart Button Gutter', 'total' ),
				'description' => esc_html__( 'Can be used to add spacing around the button.', 'total' ),
			],
			'inline_css' => [
				'target' => '.wpex-loop-product-add-to-cart',
				'alter' => '--wpex-woo-btn-gutter',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'woo_product_title_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Product Entry Title Color', 'total' )
			],
			'inline_css' => [
				'target' => [
					'.woocommerce ul.products li.product .woocommerce-loop-product__title,.woocommerce ul.products li.product .woocommerce-loop-category__title',
				],
				'alter' => 'color',
			],
		],
		[
			'id' => 'woo_product_title_link_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Product Entry Title Color: Hover', 'total' )
			],
			'inline_css' => [
				'target' => [
					'.woocommerce ul.products li.product .woocommerce-loop-product__title:hover,.woocommerce ul.products li.product .woocommerce-loop-category__title:hover',
				],
				'alter' => 'color',
			],
		],
		[
			'id' => 'woo_price_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Global Price Color', 'total' )
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-woo-price-color',
			],
		],
		[
			'id' => 'woo_product_entry_price_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Product Entry Price Color', 'total' )
			],
			'inline_css' => [
				'target' => 'li.product .price',
				'alter' => '--wpex-woo-price-color',
			],
		],
		[
			'id' => 'woo_product_price_font_weight',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Product Price Font Weight', 'total' ),
				'description' => esc_html__( 'Alters the font weight for the product price on entries and the single product.', 'total' ),
				'choices' => [
					''    => esc_html__( 'Default', 'total' ),
					'100' => esc_html__( 'Extra Light: 100', 'total' ),
					'200' => esc_html__( 'Light: 200', 'total' ),
					'300' => esc_html__( 'Book: 300', 'total' ),
					'400' => esc_html__( 'Normal: 400', 'total' ),
					'500' => esc_html__( 'Medium: 500', 'total' ),
					'600' => esc_html__( 'Semibold: 600', 'total' ),
					'700' => esc_html__( 'Bold: 700', 'total' ),
					'800' => esc_html__( 'Extra Bold: 800', 'total' ),
					'900' => esc_html__( 'Black: 900', 'total' ),
				],
			],
			'inline_css' => [
				'target' => '.price',
				'alter' => 'font-weight',
			],
		],
		[
			'id' => 'woo_single_price_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Single Product Price Color', 'total' )
			],
			'inline_css' => [
				'target' => '.product .summary',
				'alter' => '--wpex-woo-price-color',
			],
		],
		[
			'id' => 'woo_stars_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Star Ratings Color', 'total' )
			],
			'inline_css' => [
				'target' => [
					'.woocommerce p.stars',
					'.woocommerce .star-rating',
				],
				'alter' => '--wpex-star-rating-color',
			],
		],
		[
			'id' => 'woo_single_tabs_active_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Product Tabs Active Border Color', 'total' )
			],
			'inline_css' => [
				'target' => [
					'.woocommerce div.product .woocommerce-tabs ul.tabs li.active a',
				],
				'alter' => 'border-color',
			],
		],
	],
];
