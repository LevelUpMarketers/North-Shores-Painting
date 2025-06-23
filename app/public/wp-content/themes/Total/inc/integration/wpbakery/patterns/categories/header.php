<?php

defined( 'ABSPATH' ) || exit;

$patterns['header-1'] = [];
$patterns['header-1']['name'] = esc_html__( 'Header', 'total' ) . ' 1';
$patterns['header-1']['category'] = 'header';
$patterns['header-1']['content'] = <<<CONTENT
[vc_section][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-30"][vc_column width="1/5"][vcex_image source="external" external_image="{$ph_logo}" dark_mode_check="true" onclick="home" width="100px"][/vc_column][vc_column width="4/5"][vcex_horizontal_menu inner_justify="end" extra_items="search_toggle,dark_mode_toggle" nav_list_justify="end"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" external_image="{$ph_logo}" dark_mode_check="true" onclick="home" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu extra_items="search_toggle,dark_mode_toggle" toggle_align="end"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-2'] = [];
$patterns['header-2']['name'] = esc_html__( 'Header', 'total' ) . ' 2';
$patterns['header-2']['category'] = 'header';
$patterns['header-2']['content'] = <<<CONTENT
[vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-30"][vc_column width="1/5"][vcex_image source="external" external_image="{$ph_logo_white}" onclick="home" width="100px"][/vc_column][vc_column width="4/5"][vcex_horizontal_menu inner_justify="end" item_bg_hover_enable="false" extra_items="search_toggle,dark_mode_toggle" nav_list_justify="end" item_color="#fff" item_color_hover="#ffffffa8" sub_item_color="#000" sub_item_color_hover="#666"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" external_image="{$ph_logo_white}" onclick="home"  width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu extra_items="search_toggle,dark_mode_toggle" toggle_align="end" toggle_color="#fff"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-3'] = [];
$patterns['header-3']['name'] = esc_html__( 'Header', 'total' ) . ' 3';
$patterns['header-3']['category'] = 'header';
$patterns['header-3']['content'] = <<<CONTENT
[vc_section][vc_row remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-30"][vc_column][vcex_image source="external" external_image="{$ph_logo}" dark_mode_check="true" width="100px" align="center" onclick="home" bottom_margin="30px"][vcex_horizontal_menu inner_justify="center" extra_items="search_toggle,dark_mode_toggle" nav_list_justify="center"][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-30"][vc_column][vcex_image source="external" external_image="{$ph_logo}" dark_mode_check="true" width="100px" align="center" onclick="home" bottom_margin="30px"][vcex_off_canvas_menu extra_items="search_toggle,dark_mode_toggle" toggle_align="center"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-4'] = [];
$patterns['header-4']['name'] = esc_html__( 'Header', 'total' ) . ' 4';
$patterns['header-4']['category'] = 'header';
$patterns['header-4']['content'] = <<<CONTENT
[vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-30"][vc_column][vcex_image source="external" external_image="{$ph_logo_white}" width="100px" align="center" onclick="home" bottom_margin="30px"][vcex_horizontal_menu inner_justify="center" item_bg_hover_enable="false" item_color="#fff" item_color_hover="#ffffffa8" sub_item_color="#000" sub_item_color_hover="#666" extra_items="search_toggle,dark_mode_toggle" nav_list_justify="center"][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-30"][vc_column][vcex_image source="external" external_image="{$ph_logo_white}" width="100px" align="center" onclick="home" bottom_margin="30px"][vcex_off_canvas_menu extra_items="search_toggle,dark_mode_toggle" toggle_color="#fff" toggle_align="center"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-5'] = [];
$patterns['header-5']['name'] = esc_html__( 'Header', 'total' ) . ' 5';
$patterns['header-5']['category'] = 'header';
$patterns['header-5']['content'] = <<<CONTENT
[vc_section][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-30"][vc_column width="1/4"][vcex_image source="external" external_image="{$ph_logo}" dark_mode_check="true" onclick="home" width="100px"][/vc_column][vc_column width="1/2" wpex_zindex="10"][vcex_horizontal_menu inner_justify="center" extra_items="search_toggle,dark_mode_toggle" nav_list_justify="center"][/vc_column][vc_column width="1/4"][vcex_button onclick="custom_link" align="right"]Get Started[/vcex_button][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image onclick="home" source="external" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" extra_items="search_toggle,dark_mode_toggle" bottom_button_link="#" bottom_button_text="Get Started"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-6'] = [];
$patterns['header-6']['name'] = esc_html__( 'Header', 'total' ) . ' 6';
$patterns['header-6']['category'] = 'header';
$patterns['header-6']['content'] = <<<CONTENT
[vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-30"][vc_column width="1/4"][vcex_image source="external" external_image="{$ph_logo_white}" onclick="home" width="100px"][/vc_column][vc_column width="1/2" wpex_zindex="10"][vcex_horizontal_menu inner_justify="center" extra_items="search_toggle,dark_mode_toggle" nav_list_justify="center" item_bg_hover_enable="false" item_color="#fff" item_color_hover="#ffffffa8" sub_item_color="#000" sub_item_color_hover="#666"][/vc_column][vc_column width="1/4"][vcex_button onclick="custom_link" align="right"]Get Started[/vcex_button][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image onclick="home" source="external" external_image="{$ph_logo_white}" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" extra_items="search_toggle,dark_mode_toggle" toggle_color="#fff" bottom_button_link="#" bottom_button_text="Get Started"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-7'] = [];
$patterns['header-7']['name'] = esc_html__( 'Header', 'total' ) . ' 7';
$patterns['header-7']['category'] = 'header';
$patterns['header-7']['content'] = <<<CONTENT
[vc_section][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-30"][vc_column width="1/5"][vcex_image source="external" external_image="{$ph_logo}" dark_mode_check="true" onclick="home" width="100px"][/vc_column][vc_column width="4/5"][vcex_horizontal_menu inner_justify="end" extra_items="search_toggle,dark_mode_toggle" nav_list_justify="end"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" external_image="{$ph_logo}" dark_mode_check="true" onclick="home" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" extra_items="search_toggle,dark_mode_toggle"][/vc_column][/vc_row][vc_row][vc_column][vcex_divider color="currentColor"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-8'] = [];
$patterns['header-8']['name'] = esc_html__( 'Header', 'total' ) . ' 8';
$patterns['header-8']['category'] = 'header';
$patterns['header-8']['content'] = <<<CONTENT
[vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-30"][vc_column width="1/5"][vcex_image source="external" external_image="{$ph_logo_white}" onclick="home" width="100px"][/vc_column][vc_column width="4/5"][vcex_horizontal_menu inner_justify="end" extra_items="search_toggle,dark_mode_toggle" nav_list_justify="end" item_bg_hover_enable="false" item_color="#fff" item_color_hover="#ffffffa8" sub_item_color="#000" sub_item_color_hover="#666"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" external_image="{$ph_logo_white}" onclick="home" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" extra_items="search_toggle,dark_mode_toggle" toggle_color="#fff"][/vc_column][/vc_row][vc_row][vc_column][vcex_divider color="#E9E9E933"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-9'] = [];
$patterns['header-9']['name'] = esc_html__( 'Header', 'total' ) . ' 9';
$patterns['header-9']['category'] = 'header';
$patterns['header-9']['content'] = <<<CONTENT
[vc_row content_placement="middle" remove_bottom_col_margin="true" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" external_image="{$ph_logo}" dark_mode_check="true" onclick="home" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" width="100%" transition_duration="0ms" item_font_size="3xl" nav_centered="true" nav_align_items="center" toggle_width="32px" toggle_bar_height="2px" toggle_bar_gap="6px"][/vc_column][/vc_row]
CONTENT;

$patterns['header-10'] = [];
$patterns['header-10']['name'] = esc_html__( 'Header', 'total' ) . ' 10';
$patterns['header-10']['category'] = 'header';
$patterns['header-10']['content'] = <<<CONTENT
[vc_row full_width="stretch_row" wpex_bg_color="#262626" content_placement="middle" remove_bottom_col_margin="true" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" external_image="{$ph_logo_white}" onclick="home"  width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" width="100%" transition_duration="0ms" item_font_size="3xl" nav_centered="true" nav_align_items="center" toggle_width="32px" toggle_bar_height="2px" toggle_bar_gap="6px" toggle_color="#fff" background="#000" item_color="#fff" item_color_hover="#ffffffa8" close_btn_color="#fff" close_btn_color_hover="#ffffffa8"][/vc_column][/vc_row][/vc_column][/vc_row]
CONTENT;

$patterns['header-11'] = [];
$patterns['header-11']['name'] = esc_html__( 'Header', 'total' ) . ' 11';
$patterns['header-11']['category'] = 'header';
$patterns['header-11']['content'] = <<<CONTENT
[vc_row content_placement="middle" remove_bottom_col_margin="true" el_class="wpex-py-30"][vc_column width="1/5"][vcex_image source="external" onclick="home" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][vcex_spacing size="25px" visibility="visible-phone"][/vc_column][vc_column width="4/5"][vcex_flex_container align_items="center" el_class="wpex-md-justify-end" gap="25px"][vcex_horizontal_menu item_bg_hover_enable="false" item_font_weight="bold" item_text_transform="uppercase" item_padding_x="0px" nav_list_gap="5px 25px" item_padding_y="0px" sub_item_padding_block="10px" sub_padding="20px" item_font_size="sm" item_color_hover="accent"][vcex_off_canvas_menu width="440px" swap_side="true" toggle_width="18px" toggle_bar_height="2px" toggle_rounded="true" toggle_color_hover="accent" item_font_size="xl"][/vcex_flex_container][/vc_column][/vc_row]
CONTENT;

$patterns['header-12'] = [];
$patterns['header-12']['name'] = esc_html__( 'Header', 'total' ) . ' 12';
$patterns['header-12']['category'] = 'header';
$patterns['header-12']['content'] = <<<CONTENT
[vc_row full_width="stretch_row" content_placement="middle" wpex_bg_color="#262626" remove_bottom_col_margin="true" el_class="wpex-py-30"][vc_column width="1/5"][vcex_image source="external" onclick="home" external_image="{$ph_logo_white}" width="100px"][vcex_spacing size="25px" visibility="visible-phone"][/vc_column][vc_column width="4/5"][vcex_flex_container align_items="center" el_class="wpex-md-justify-end" gap="25px"][vcex_horizontal_menu item_bg_hover_enable="false" item_font_weight="bold" item_text_transform="uppercase" item_padding_x="0px" sub_item_padding_block="10px" sub_padding="20px" nav_list_gap="5px 25px" item_padding_y="0px" item_font_size="sm" item_color="#fff" item_color_hover="#ffffffa8"][vcex_off_canvas_menu swap_side="true" width="440px" toggle_width="18px" toggle_bar_height="2px" toggle_rounded="true" toggle_color="#fff" toggle_color_hover="#ffffffa8" background="#000" item_color="#fff" item_color_hover="#ffffffa8" close_btn_color="#fff" close_btn_color_hover="#ffffffa8" item_font_size="xl"][/vcex_flex_container][/vc_column][/vc_row]
CONTENT;

$patterns['header-13'] = [];
$patterns['header-13']['name'] = esc_html__( 'Header', 'total' ) . ' 13';
$patterns['header-13']['category'] = 'header';
$patterns['header-13']['content'] = <<<CONTENT
[vc_section][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-desktop-large" el_class="wpex-py-30"][vc_column width="5/12"][vcex_horizontal_menu item_bg_hover_enable="false" nav_list_gap="5px 30px" item_font_weight="700" item_padding_x="0px" item_padding_y="0px" sub_item_padding_block="10px" sub_padding="20px"][/vc_column][vc_column width="1/6"][vcex_image source="external" align="center" onclick="home" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][/vc_column][vc_column width="5/12"][vcex_horizontal_menu item_bg_hover_enable="false" inner_justify="end" nav_list_justify="end" nav_list_gap="5px 30px" item_font_weight="700" item_padding_x="0px" item_padding_y="0px" sub_item_padding_block="10px" sub_padding="20px"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-desktop-large" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" onclick="home" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-14'] = [];
$patterns['header-14']['name'] = esc_html__( 'Header', 'total' ) . ' 14';
$patterns['header-14']['category'] = 'header';
$patterns['header-14']['content'] = <<<CONTENT
[vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-desktop-large" el_class="wpex-py-30"][vc_column width="5/12"][vcex_horizontal_menu item_bg_hover_enable="false" nav_list_gap="5px 30px" item_font_weight="700" item_padding_x="0px" item_padding_y="0px" sub_item_padding_block="10px" sub_padding="20px" item_color="#fff" item_color_hover="#ffffffa8" sub_item_color="#000" sub_item_color_hover="#666"][/vc_column][vc_column width="1/6"][vcex_image source="external" align="center" onclick="home" external_image="{$ph_logo_white}" width="100px"][/vc_column][vc_column width="5/12"][vcex_horizontal_menu inner_justify="end" nav_list_justify="end" item_bg_hover_enable="false" nav_list_gap="5px 30px" item_font_weight="700" item_padding_x="0px" item_padding_y="0px" sub_item_padding_block="10px" sub_padding="20px" item_color="#fff" item_color_hover="#ffffffa8" sub_item_color="#000" sub_item_color_hover="#666"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-desktop-large" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" onclick="home" external_image="{$ph_logo_white}" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" toggle_color="#fff"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-15'] = [];
$patterns['header-15']['name'] = esc_html__( 'Header', 'total' ) . ' 15';
$patterns['header-15']['category'] = 'header';
$patterns['header-15']['content'] = <<<CONTENT
[vc_section][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-25"][vc_column width="3/5"][vcex_flex_container flex_wrap="true" align_items="center" gap="3vw"][vcex_image source="external" onclick="home" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][vcex_horizontal_menu item_bg_hover_enable="false" nav_list_gap="5px 30px" item_font_weight="700" item_padding_x="0px" item_padding_y="0px" sub_item_padding_block="10px" sub_padding="20px"][/vcex_flex_container][/vc_column][vc_column width="2/5"][vcex_searchbar wrap_float="right" button_text="`{`ticon icon=``ionicons-search```}`" button_bg="transparent" button_color="currentColor" input_border_width="0px" classes="wpex-border-b wpex-border-solid wpex-border-current" input_padding="left:0px" button_padding="0px" button_width="fit-content" placeholder="Search..." input_color="currentColor" input_font_weight="700" input_background_color="transparent" input_font_size="xl" placeholder_color="currentColor" wrap_width="400px" button_font_size="20px"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-25"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" onclick="home" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" extra_items="search_toggle"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-16'] = [];
$patterns['header-16']['name'] = esc_html__( 'Header', 'total' ) . ' 16';
$patterns['header-16']['category'] = 'header';
$patterns['header-16']['content'] = <<<CONTENT
[vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-phone" el_class="wpex-py-25"][vc_column width="3/5"][vcex_flex_container flex_wrap="true" align_items="center" gap="3vw"][vcex_image source="external" onclick="home" external_image="{$ph_logo_white}" width="100px"][vcex_horizontal_menu item_bg_hover_enable="false" nav_list_gap="5px 30px" item_font_weight="700" item_padding_x="0px" item_padding_y="0px" sub_item_padding_block="10px" sub_padding="20px" item_color="#fff" item_color_hover="#ffffffa8" sub_item_color="#000" sub_item_color_hover="#666"][/vcex_flex_container][/vc_column][vc_column width="2/5"][vcex_searchbar wrap_float="right" button_text="`{`ticon icon=``ionicons-search```}`" button_bg="transparent" button_color="#fff" input_border_width="0px" classes="wpex-border-b wpex-border-solid wpex-border-white" input_padding="left:0px" button_padding="0px" button_width="fit-content" placeholder="Search..." input_color="#fff" input_font_weight="700" input_background_color="transparent" input_font_size="xl" placeholder_color="#fff" wrap_width="400px" button_font_size="20px"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-phone" el_class="wpex-py-25"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" onclick="home" external_image="{$ph_logo_white}" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" toggle_color="#fff" extra_items="search_toggle"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-17'] = [];
$patterns['header-17']['name'] = esc_html__( 'Header', 'total' ) . ' 17';
$patterns['header-17']['category'] = 'header';
$patterns['header-17']['content'] = <<<CONTENT
[vc_section][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-desktop-large" el_class="wpex-py-25"][vc_column width="3/5"][vcex_flex_container flex_wrap="true" align_items="center" gap="3vw"][vcex_image source="external" onclick="home" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][vcex_horizontal_menu item_bg_hover_enable="false" nav_list_gap="5px 30px" item_padding_x="0px" item_padding_y="0px" sub_item_padding_block="10px" sub_padding="20px"][/vcex_flex_container][/vc_column][vc_column width="2/5"][vcex_flex_container flex_wrap="true" align_items="center" justify_content="end" gap="10px"][vcex_icon icon="search" onclick="search_toggle" custom_size="18px" color="currentColor" color_hover="accent"][vcex_button style="plain-text" border="1px solid currentColor" font_padding="top:5px|right:10px|bottom:5px|left:10px" el_class="wpex-no-underline wpex-ml-15" custom_color="currentColor" border_radius="rounded-sm" custom_hover_color="accent"]Start for Free[/vcex_button][vcex_button style="plain-text" border="1px solid currentColor" font_padding="top:5px|right:10px|bottom:5px|left:10px" el_class="wpex-no-underline" custom_color="currentColor" border_radius="rounded-sm" custom_hover_color="accent" onclick_url="#" icon_left="sign-in"]Sign In[/vcex_button][/vcex_flex_container][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-desktop-large" el_class="wpex-py-25"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" onclick="home" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" extra_items="search_toggle" toggle_width="30px" toggle_bar_height="2px" toggle_bar_gap="6px"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-18'] = [];
$patterns['header-18']['name'] = esc_html__( 'Header', 'total' ) . ' 18';
$patterns['header-18']['category'] = 'header';
$patterns['header-18']['content'] = <<<CONTENT
[vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-desktop-large" el_class="wpex-py-25"][vc_column width="3/5"][vcex_flex_container flex_wrap="true" align_items="center" gap="3vw"][vcex_image source="external" onclick="home" external_image="{$ph_logo_white}" width="100px"][vcex_horizontal_menu item_bg_hover_enable="false" nav_list_gap="5px 30px" item_padding_x="0px" item_padding_y="0px" sub_item_padding_block="10px" sub_padding="20px" item_color="#fff" item_color_hover="#ffffffa8" sub_item_color="#000" sub_item_color_hover="#666"][/vcex_flex_container][/vc_column][vc_column width="2/5"][vcex_flex_container flex_wrap="true" align_items="center" justify_content="end" gap="10px"][vcex_icon icon="search" onclick="search_toggle" custom_size="18px" color="#fff" color_hover="#ffffffa8"][vcex_button style="plain-text" border="1px solid currentColor" font_padding="top:5px|right:10px|bottom:5px|left:10px" el_class="wpex-no-underline wpex-ml-15" custom_color="#fff" border_radius="rounded-sm" custom_hover_color="#ffffffa8"]Start for Free[/vcex_button][vcex_button style="plain-text" border="1px solid currentColor" font_padding="top:5px|right:10px|bottom:5px|left:10px" el_class="wpex-no-underline" custom_color="#fff" border_radius="rounded-sm" custom_hover_color="#ffffffa8" onclick_url="#" icon_left="sign-in"]Sign In[/vcex_button][/vcex_flex_container][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-desktop-large" el_class="wpex-py-25"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" onclick="home" external_image="{$ph_logo_white}" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" extra_items="search_toggle" toggle_color="#fff" toggle_width="30px" toggle_bar_height="2px" toggle_bar_gap="6px"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-19'] = [];
$patterns['header-19']['name'] = esc_html__( 'Header', 'total' ) . ' 19';
$patterns['header-19']['category'] = 'header';
$patterns['header-19']['content'] = <<<CONTENT
[vc_section full_width="stretch_row" el_class="wpex-shadow-lg wpex-lg-shadow-none"][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-desktop" el_class="wpex-py-30"][vc_column width="1/5"][vcex_image source="external" onclick="home" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][/vc_column][vc_column width="4/5"][vcex_image source="external" external_image="{$banner_728x90}" align="right"][/vc_column][/vc_row][vc_row full_width="stretch_row" content_placement="middle" remove_bottom_col_margin="true" visibility="visible-desktop" wpex_bg_color="accent"][vc_column][vcex_horizontal_menu extra_items="search_toggle,dark_mode_toggle" item_bg_hover_enable="false" sub_trigger="click" sub_animate="false" sub_arrow_icon="caret" item_color="on-accent" item_padding_y="15px" sub_bg="accent" sub_border_radius="rounded-0" sub_padding="15px" item_bg_hover="#0000001c" item_border_radius="rounded-0" sub_item_padding_block="10px" el_class="-wpex-mx-10" sub_speed="0ms"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-desktop" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" onclick="home" external_image="{$ph_logo}" dark_mode_check="true" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" extra_items="search_toggle,dark_mode_toggle"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['header-20'] = [];
$patterns['header-20']['name'] = esc_html__( 'Header', 'total' ) . ' 20';
$patterns['header-20']['category'] = 'header';
$patterns['header-20']['content'] = <<<CONTENT
[vc_section full_width="stretch_row" el_class="wpex-shadow-lg wpex-lg-shadow-none" wpex_bg_color="#262626"][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="visible-desktop" el_class="wpex-py-30"][vc_column width="1/5"][vcex_image source="external" onclick="home" external_image="{$ph_logo_white}" width="100px"][/vc_column][vc_column width="4/5"][vcex_image source="external" external_image="{$banner_728x90}" align="right"][/vc_column][/vc_row][vc_row full_width="stretch_row" content_placement="middle" remove_bottom_col_margin="true" visibility="visible-desktop" wpex_bg_color="accent"][vc_column][vcex_horizontal_menu extra_items="search_toggle,dark_mode_toggle" item_bg_hover_enable="false" sub_trigger="click" sub_animate="false" sub_arrow_icon="caret" item_color="on-accent" item_padding_y="15px" sub_bg="accent" sub_border_radius="rounded-0" sub_padding="15px" item_bg_hover="#0000001c" item_border_radius="rounded-0" sub_item_padding_block="10px" el_class="-wpex-mx-10" sub_speed="0ms"][/vc_column][/vc_row][vc_row content_placement="middle" remove_bottom_col_margin="true" visibility="hidden-desktop" el_class="wpex-py-30"][vc_column width="1/2" offset="vc_col-xs-6"][vcex_image source="external" onclick="home" external_image="{$ph_logo_white}" width="100px"][/vc_column][vc_column width="1/2" offset="vc_col-xs-6"][vcex_off_canvas_menu toggle_align="end" extra_items="search_toggle,dark_mode_toggle" toggle_color="#fff"][/vc_column][/vc_row][/vc_section]
CONTENT;
// IMPORTANT - Space required to prevent HEREDOC errors.
