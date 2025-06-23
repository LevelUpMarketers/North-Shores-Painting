<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_header_mobile_menu'] = [
	'title' => esc_html__( 'Mobile Menu', 'total' ),
	'panel' => 'wpex_header',
	'settings' => [
		[
			'id' => 'mobile_menu_search',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Mobile Menu Search Box', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'mobile_menu_breakpoint',
			'control' => [
				'label' => esc_html__( 'Mobile Menu Breakpoint', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'description' => esc_html__( 'Enter a custom viewport width in pixels for when the default menu will become the mobile menu. Enter 9999 to display the mobile menu always.', 'total' ),
				'placeholder' => '959',
			],
		],
		[
			'id' => 'mobile_menu_style',
			'default' => 'sidr',
			'control' => [
				'label' => esc_html__( 'Mobile Menu Style', 'total' ),
				'type' => 'select',
				'choices' => 'TotalTheme\Mobile\Menu::style_choices',
			],
		],
		[
			'id' => 'mobile_menu_toggle_style',
			'default' => 'icon_buttons',
			'control' => [
				'label' => esc_html__( 'Toggle Button Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'icon_buttons' => esc_html__( 'Side Hamburger Icon', 'total' ),
					'icon_buttons_under_logo' => esc_html__( 'Centered Hamburger Icon', 'total' ),
					'centered_logo' => esc_html__( 'Centered Logo (Flex Header Styles only)', 'total' ),
					'next_to_logo' => esc_html__( 'Hamburger Icon Next to Logo (Flex Header Styles only)', 'total' ),
					'navbar' => esc_html__( 'Navbar', 'total' ),
					'fixed_top' => esc_html__( 'Fixed Top Navbar', 'total' ),
					'custom'  => esc_html__( 'Custom', 'total' ),
				],
				'description' => esc_html__( 'If you select "custom" the theme will load the needed code for your mobile menu which you can then open/close by adding any link to the page with the classname "mobile-menu-toggle".', 'total' )
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'disabled',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'mobile_menu_open_submenu_icon',
			'default' => 'angle-down',
			'control' => [
				'type' => 'totaltheme_icon',
				'label' => esc_html__( 'Dropdown Arrow Type', 'total' ),
				'choices' => [
					'angle-down',
					'caret-down',
					'plus',
					'material-add',
					'material-arrow-down-ios',
				],
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'sidr', 'toggle', 'toggle_inline', 'toggle_full' ],
			],
		],
		[
			'id' => 'mobile_menu_open_submenu_icon_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Dropdown Arrow Size', 'total' ),
			],
			'inline_css' => [
				'target' => '.wpex-mobile-menu .wpex-open-submenu__icon',
				'alter' => 'font-size',
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'sidr', 'toggle', 'toggle_inline', 'toggle_full' ],
			],
		],
		/*** Mobile Menu > Toggle Settings ***/
		[
			'id' => 'mobile_toggle_styling',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Toggle Styling', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_toggle_style',
				'value' => [ 'navbar', 'fixed_top' ],
			],
		],
		[
			'id' => 'mobile_menu_navbar_position',
			'default' => 'wpex_hook_header_bottom',
			'control' => [
				'label' => esc_html__( 'Toggle Button Position', 'total' ),
				'type' => 'select',
				'choices' => [
					'wpex_hook_header_bottom' => esc_html__( 'Header Bottom', 'total' ),
					'outer_wrap_before' => esc_html__( 'Top of site', 'total' ),
				],
				'description' => esc_html__( 'note: will always display at the top of the site when the Transparent Header is enabled.', 'total' )
			],
			'control_display' => [
				'check' => 'mobile_menu_toggle_style',
				'value' => 'navbar',
			],
		],
		[
			'id' => 'mobile_menu_toggle_fixed_top_bg',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Toggle Background', 'total' ),
				'type' => 'totaltheme_color',
			],
			'control_display' => [
				'check' => 'mobile_menu_toggle_style',
				'value' => [ 'navbar', 'fixed_top' ],
			],
			'inline_css' => [
				'target' => '#wpex-mobile-menu-fixed-top, #wpex-mobile-menu-navbar',
				'alter' => 'background',
			],
		],
		[
			'id' => 'mobile_menu_toggle_text',
			'control' => [
				'label' => esc_html__( 'Toggle Text', 'total' ),
				'type' => 'text',
				'input_attrs' => [
					'placeholder' => esc_html__( 'Menu', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'mobile_menu_toggle_style',
				'value' => [ 'navbar', 'fixed_top' ],
			],
		],
		[
			'id' => 'mobile_menu_toggle_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'text',
				'label' => esc_html__( 'Toggle Font Size', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_toggle_style',
				'value' => [ 'navbar', 'fixed_top' ],
			],
			'inline_css' => [
				'target' => '#wpex-mobile-menu-navbar,#wpex-mobile-menu-fixed-top',
				'alter' => 'font-size',
				'sanitize' => 'font-size',
			],
		],
		[
			'id' => 'mobile_menu_toggle_height',
			'transport' => 'postMessage',
			'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::pixel',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Toggle Height', 'total' ),
				'units' => [ 'px' ],
			],
			'control_display' => [
				'check' => 'mobile_menu_toggle_style',
				'value' => [ 'navbar', 'fixed_top' ],
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-mobile-menu-toggle-height',
				'sanitize' => 'px',
			],
		],
		/*** Mobile Menu > Mobile Icons Styling ***/
		[
			'id' => 'mobile_menu_icons_styling',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Icons Styling', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_icons',
			],
		],
		[
			'id' => 'mobile_menu_icon_toggle_state',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Hamburger Icon Active State', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled the hamburger icon will turn into an "x" while active.', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_icons',
			],
		],
		[
			'id' => 'mobile_menu_icon_animate',
			'transport' => 'postMessage',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Hamburger Icon Animation', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Animates the active state transition.', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_mobile_menu_hamburger_animate',
			],
		],
		[
			'id' => 'mobile_menu_icon_rounded',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Hamburger Icon Rounded Edges', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_icons',
			],
		],
		[
			'id' => 'mobile_menu_icon_side_margin',
			'transport' => 'postMessage',
			'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::pixel',
			'control' => [
				'label' => esc_html__( 'Hamburger Icon Side Margin', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'description' => esc_html__( 'Adds a space between the hamburger icon and the content beside it.', 'total' ),
				'placeholder' => '25',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'eight', 'nine' ],
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-mobile-menu-toggle-side-margin',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'mobile_menu_icon_label',
			'control' => [
				'label' => esc_html__( 'Hamburger Icon Label', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Adds custom text next to your hamburger menu icon.', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_icons',
			],
		],
		[
			'id' => 'mobile_menu_icon_label_font_size',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Hamburger Icon Label Font Size', 'total' ),
				'type' => 'text',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_hamburger_label',
			],
			'inline_css' => [
				'target' => '.mobile-menu-toggle__label',
				'alter' => 'font-size',
				'sanitize' => 'font-size',
			],
		],
		[
			'id' => 'mobile_menu_icon_label_position',
			'control' => [
				'label' => esc_html__( 'Hamburger Icon Label Position', 'total' ),
				'default' => 'right',
				'type' => 'select',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_hamburger_label',
				'choices' => [
					'right' => esc_html__( 'Right', 'total' ),
					'left' => esc_html__( 'Left', 'total' ),
				],
			],
		],
		[
			'id' => 'mobile_menu_icon_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Hamburger Icon Width', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_icons',
				'placeholder' => '22',
			],
			'inline_css' => [
				'target' => '.mobile-menu-toggle',
				'alter' => '--wpex-hamburger-icon-width',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'mobile_menu_icon_item_height',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Hamburger Icon Bar Height', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_icons',
				'description' => esc_html__( 'Alters the height of each bar of the hamburger icon.', 'total' ),
				'placeholder' => '3',
			],
			'inline_css' => [
				'target' => '.mobile-menu-toggle',
				'alter' => '--wpex-hamburger-icon-bar-height',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'mobile_menu_icon_gutter',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Hamburger Icon Gutter', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_icons',
				'description' => esc_html__( 'Alters the space between each bar of the hamburger icon.', 'total' ),
				'placeholder' => '4',
			],
			'inline_css' => [
				'target' => '.mobile-menu-toggle',
				'alter' => '--wpex-hamburger-icon-gutter',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'mobile_menu_icon_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_icons',
			],
			'inline_css' => [
				'target' => '#mobile-menu',
				'alter' => [
					'--wpex-link-color',
					'--wpex-hover-link-color',
				],
			],
		],
		[
			'id' => 'mobile_menu_icon_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color: Hover', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_mobile_menu_icons',
			],
			'inline_css' => [
				'target' => '#mobile-menu',
				'alter' => '--wpex-hover-link-color',
			],
		],

		/*** Mobile Menu > Full-Screen ***/
		[
			'id' => 'mobile_menu_full_screen_styling',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Full-Screen Menu Styling', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'full_screen', 'full_screen_under_header' ]
			],
		],
		[
			'id' => 'full_screen_mobile_menu_style',
			'default' => 'white',
			'control' => [
				'label' => esc_html__( 'Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'white'	=> esc_html__( 'White', 'total' ),
					'black'	=> esc_html__( 'Black', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'full_screen', 'full_screen_under_header' ]
			],
		],
		[
			'id' => 'mobile_menu_full_screen_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'full_screen', 'full_screen_under_header' ]
			],
			'inline_css' => [
				'target' => '.full-screen-overlay-nav',
				'alter' => 'background-color',
				'important' => true,
			],
		],
		[
			'id' => 'mobile_menu_full_screen_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'full_screen', 'full_screen_under_header' ]
			],
			'inline_css' => [
				'target' => '.full-screen-overlay-nav',
				'alter' => 'color',
				'important' => true,
			],
		],
		/*** Mobile Menu > Sidr ***/
		[
			'id' => 'mobile_menu_sidr_styling',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Sidebar Menu Styling', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_sidr_dark_surface',
			'transport' => 'postMessage',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Dark Surface', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Adds dark styling to the element, disable to use default color scheme.', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_sidr_displace',
			'control' => [
				'label' => esc_html__( 'Site Displacement', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enable to display/push the site content over when opening the sidebar mobile menu.', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_sidr_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', '%', 'vw', 'func' ],
				'placeholder' => '320',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-sidr-width',
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_sidr_direction',
			'control' => [
				'label' => esc_html__( 'Open Direction', 'total' ),
				'type' => 'select',
				'choices' => [
					''	=> esc_html__( 'Default', 'total' ),
					'right'	=> esc_html__( 'From Right', 'total' ),
					'left'	=> esc_html__( 'From Left', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_sidr_gutter',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'label' => esc_html__( 'Gutter', 'total' ),
				'description' => esc_html__( 'Adds padding around the mobile menu content.', 'total' ),
				'placeholder' => '0',
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-sidr-gutter',
				'sanitize' => 'margin',
			],
		],
		[
			'id' => 'mobile_menu_sidr_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
			'inline_css' => [
				'target' => '#sidr-main,.sidr-class-dropdown-menu ul',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'mobile_menu_sidr_borders',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Borders', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
			'inline_css' => [
				'target' => '#sidr-main',
				'alter' => '--wpex-border-main',
			],
		],
		[
			'id' => 'mobile_menu_links',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
			'inline_css' => [
				'target' => '#sidr-main',
				'alter' => [
					'color', // legacy from 5.4 !!important!!!
					'--wpex-link-color', // !! don't target hover color !!!
					'--wpex-text-2', // targets elements inside the dark-surface
				],
			],
		],
		[
			'id' => 'mobile_menu_links_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Links: Hover', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
			'inline_css' => [
				'target' => '#sidr-main',
				'alter' => '--wpex-hover-link-color',
			],
		],
		/*** Mobile Menu > Toggle Menu ***/
		[
			'id' => 'mobile_menu_toggle_styling',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Dropdown Menu Styling', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'toggle', 'toggle_inline', 'toggle_full' ]
			],
		],
		[
			'id' => 'mobile_menu_toggle_animate',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Dropdown Animation', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'toggle', 'toggle_inline' ]
			],
		],
		[
			'id' => 'mobile_menu_toggle_has_border_top',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Top Border', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'toggle', 'toggle_inline', 'toggle_full' ]
			],
		],
		[
			'id' => 'mobile_menu_toggle_has_border_bottom',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Bottom Border', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'toggle', 'toggle_inline', 'toggle_full' ]
			],
		],
		[
			'id' => 'toggle_mobile_menu_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'toggle', 'toggle_inline', 'toggle_full' ]
			],
			'inline_css' => [
				'target' => '.mobile-toggle-nav',
				'alter' => 'background'
			],
		],
		[
			'id' => 'toggle_mobile_menu_borders',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Borders', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'toggle', 'toggle_inline', 'toggle_full' ]
			],
			'inline_css' => [
				'target' => '.mobile-toggle-nav-ul,.mobile-toggle-nav-ul a',
				'alter' => 'border-color'
			],
		],
		[
			'id' => 'toggle_mobile_menu_links',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'toggle', 'toggle_inline', 'toggle_full' ]
			],
			'inline_css' => [
				'target' => '.mobile-toggle-nav',
				'alter' => [
					'color',
					'--wpex-link-color',
				],
			],
		],
		[
			'id' => 'toggle_mobile_menu_links_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Links: Hover', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => [ 'toggle', 'toggle_inline', 'toggle_full' ]
			],
			'inline_css' => [
				'target' => '.mobile-toggle-nav',
				'alter' => '--wpex-hover-link-color'
			],
		],
		// Mobile menu link padding.
		[
			'id' => 'mobile_menu_links_padding_y',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Links: Vertical Padding', 'total' ),
				'placeholder' => '10',
			],
			'inline_css' => [
				'target' => '.full-screen-overlay-nav-menu__link,.mobile-toggle-nav__link,.sidr-mobile-nav-menu__link',
				'alter' => 'padding-block',
				'sanitize' => 'padding',
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'disabled',
				'compare' => 'not_equal',
			],
		],
		/*** Mobile Menu > Top - Currently only used for the Sidr Mobile Menu ***/
		[
			'id' => 'mobile_menu_top_heading',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Mobile Menu Top', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_sidr_close_align',
			'transport' => 'postMessage',
			'default' => 'left',
			'control' => [
				'label' => esc_html__( 'Close Icon Align', 'total' ),
				'description' => esc_html__( 'Important: If you have added a logo to your mobile menu this setting will be ignored.', 'total' ),
				'type' => 'select',
				'choices' => [
					'left'	=> esc_html__( 'Start', 'total' ),
					'right'	=> esc_html__( 'End', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_logo_enable',
			'default' => false,
			'control' => [
				'type' => 'totaltheme_toggle',
				'label' => esc_html__( 'Mobile Menu Logo', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_logo_img',
			'control' => [
				'type' => 'media',
				'mime_type' => 'image',
				'label' => esc_html__( 'Mobile Menu Logo Image', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_title_center',
			'default' => true,
			'control' => [
				'type' => 'totaltheme_toggle',
				'label' => esc_html__( 'Center Logo', 'total' ),
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_logo_img_max_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Mobile Menu Logo Image Max Width', 'total' ),
				'placeholder' => 'none',
			],
			'inline_css' => [
				'target' => '.wpex-mobile-menu__logo img',
				'alter' => 'max-width',
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
		[
			'id' => 'mobile_menu_top_gap',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Mobile Menu Top Gap', 'total' ),
				'description' => esc_html__( 'Controls the gap between the the Logo and the close button.', 'total' ),
				'choices' => 'column_gap',
			],
			'control_display' => [
				'check' => 'mobile_menu_style',
				'value' => 'sidr',
			],
		],
	],
];
