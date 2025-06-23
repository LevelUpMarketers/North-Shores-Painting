<?php

defined( 'ABSPATH' ) || exit;

$patterns['footer-1'] = [];
$patterns['footer-1']['name'] = esc_html__( 'Footer', 'total' ) . ' 1';
$patterns['footer-1']['category'] = 'footer';
$patterns['footer-1']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_row full_width="stretch_row" remove_bottom_col_margin="true" el_class="wpex-py-40 wpex-border-t wpex-border-solid wpex-border-main"][vc_column][vcex_flex_container flex_wrap="true" justify_content="space-between" gap="5px"][vc_column_text]© [current_year] - Site Name[/vc_column_text][vc_column_text]Design by <a href="#">Company X</a>[/vc_column_text][/vcex_flex_container][/vc_column][/vc_row]
CONTENT;

$patterns['footer-2'] = [];
$patterns['footer-2']['name'] = esc_html__( 'Footer', 'total' ) . ' 2';
$patterns['footer-2']['category'] = 'footer';
$patterns['footer-2']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_row full_width="stretch_row" remove_bottom_col_margin="true" wpex_bg_color="#262626" el_class="wpex-py-40"][vc_column][vcex_flex_container flex_wrap="true" justify_content="space-between" gap="5px"][vc_column_text color="#fff"]© [current_year] - Site Name[/vc_column_text][vc_column_text color="#fff"]Design by <a href="#">Company X</a>[/vc_column_text][/vcex_flex_container][/vc_column][/vc_row]
CONTENT;

$patterns['footer-3'] = [];
$patterns['footer-3']['name'] = esc_html__( 'Footer', 'total' ) . ' 3';
$patterns['footer-3']['category'] = 'footer';
$patterns['footer-3']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_row full_width="stretch_row" remove_bottom_col_margin="true" el_class="wpex-py-40 wpex-border-t wpex-border-solid wpex-border-main"][vc_column][vc_column_text text_align="center"]© [current_year] Site Name, Design by <a href="#">Company X</a>[/vc_column_text][/vc_column][/vc_row]
CONTENT;

$patterns['footer-4'] = [];
$patterns['footer-4']['name'] = esc_html__( 'Footer', 'total' ) . ' 5';
$patterns['footer-4']['category'] = 'footer';
$patterns['footer-4']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_row full_width="stretch_row" remove_bottom_col_margin="true" wpex_bg_color="#262626" el_class="wpex-py-40"][vc_column][vc_column_text color="#fff" text_align="center"]© [current_year] Site Name, Design by <a href="#">Company X</a>[/vc_column_text][/vc_column][/vc_row]
CONTENT;

$patterns['footer-5'] = [];
$patterns['footer-5']['name'] = esc_html__( 'Footer', 'total' ) . ' 5';
$patterns['footer-5']['category'] = 'footer';
$patterns['footer-5']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" el_class="wpex-surface-2"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vc_column_text text_align="center" font_size="lg"]123 Demo Street, San Francisco CA | (123) 456-7891 | email@company-x.com[/vc_column_text][vcex_spacing size="15px"][vc_column_text text_align="center" font_size="lg"]© <a href="#">Company X</a> - [current_year][/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-6'] = [];
$patterns['footer-6']['name'] = esc_html__( 'Footer', 'total' ) . ' 6';
$patterns['footer-6']['category'] = 'footer';
$patterns['footer-6']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vc_column_text text_align="center" font_size="lg" color="#fff"]123 Demo Street, San Francisco CA | (123) 456-7891 | email@company-x.com[/vc_column_text][vcex_spacing size="15px"][vc_column_text text_align="center" font_size="lg" color="#fff"]© <a href="#">Company X</a> - [current_year][/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-7'] = [];
$patterns['footer-7']['name'] = esc_html__( 'Footer', 'total' ) . ' 7';
$patterns['footer-7']['category'] = 'footer';
$patterns['footer-7']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" el_class="wpex-border-t wpex-border-solid wpex-border-main"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_image dark_mode_check="true" source="external" onclick="home" external_image="{$ph_logo}" width="100px"][vcex_spacing size="25px"][vc_column_text color="currentColor"]<a href="#">Shop</a>  |  <a href="#">About</a>  |  <a href="#">Contact</a>[/vc_column_text][vcex_spacing size="20px"][vc_column_text color="currentColor"]© [current_year] All rights reserved.[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-8'] = [];
$patterns['footer-8']['name'] = esc_html__( 'Footer', 'total' ) . ' 8';
$patterns['footer-8']['category'] = 'footer';
$patterns['footer-8']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_image source="external" onclick="home" external_image="{$ph_logo_white}" width="100px"][vcex_spacing size="25px"][vc_column_text color="#fff"]<a href="#">Shop</a>  |  <a href="#">About</a>  |  <a href="#">Contact</a>[/vc_column_text][vcex_spacing size="20px"][vc_column_text color="#fff"]© [current_year] All rights reserved.[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-9'] = [];
$patterns['footer-9']['name'] = esc_html__( 'Footer', 'total' ) . ' 9';
$patterns['footer-9']['category'] = 'footer';
$patterns['footer-9']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" el_class="wpex-border-t wpex-border-solid wpex-border-main"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row][vc_column width="1/3"][vcex_heading text="Location" tag="div" text_align="center" font_size="lg" bottom_margin="30px"][vc_column_text color="currentColor" text_align="center"]555 Fake Street<br />
Imaginary City, CA 90210<br />
USA[/vc_column_text][/vc_column][vc_column width="1/3"][vcex_heading text="Pages" tag="div" text_align="center" font_size="lg" bottom_margin="30px"][vc_column_text text_align="center" color="currentColor"]<a href="#">Home</a></p>
<p><a href="#">About</a></p>
<p><a href="#">Contact</a>[/vc_column_text][/vc_column][vc_column width="1/3"][vcex_heading text="Follow us" tag="div" text_align="center" font_size="lg" bottom_margin="30px"][vc_column_text text_align="center" color="currentColor"]<a href="#">Facebook</a></p>
<p><a href="#">Instagram</a></p>
<p><a href="#">Twitter (X)</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][vc_column_text color="currentColor" text_align="center"]© Company Name [current_year] ~ All rights reserved.[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-10'] = [];
$patterns['footer-10']['name'] = esc_html__( 'Footer', 'total' ) . ' 10';
$patterns['footer-10']['category'] = 'footer';
$patterns['footer-10']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row][vc_column width="1/3"][vcex_heading color="#fff" text="Location" tag="div" text_align="center" font_size="lg" bottom_margin="30px"][vc_column_text color="#fff" text_align="center"]555 Fake Street<br />
Imaginary City, CA 90210<br />
USA[/vc_column_text][/vc_column][vc_column width="1/3"][vcex_heading color="#fff" text="Pages" tag="div" text_align="center" font_size="lg" bottom_margin="30px"][vc_column_text color="#fff" text_align="center"]<a href="#">Home</a></p>
<p><a href="#">About</a></p>
<p><a href="#">Contact</a>[/vc_column_text][/vc_column][vc_column width="1/3"][vcex_heading text="Follow us" tag="div" text_align="center" font_size="lg" bottom_margin="30px" color="#fff"][vc_column_text color="#fff" text_align="center"]<a href="#">Facebook</a></p>
<p><a href="#">Instagram</a></p>
<p><a href="#">Twitter (X)</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][vc_column_text color="#fff" text_align="center"]© Company Name [current_year] ~ All rights reserved.[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-11'] = [];
$patterns['footer-11']['name'] = esc_html__( 'Footer', 'total' ) . ' 11';
$patterns['footer-1']['category'] = 'footer';
$patterns['footer-11']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" el_class="wpex-border-t wpex-border-solid wpex-border-main"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vcex_image source="external" dark_mode_check="true" onclick="home" external_image="{$ph_logo}" width="100px" bottom_margin="40px"][vc_column_text color="currentColor"]Made with ♥  by Company X.<br />
Copyright [current_year]. All rights reserved.[/vc_column_text][/vc_column][vc_column width="1/4"][vcex_heading text="About Us" tag="div" bottom_margin="30px" font_size="base"][vc_column_text color="currentColor" line_height="relaxed"]<a href="#">Home</a><br />
<a href="#">About</a><br />
<a href="#">Contact</a>[/vc_column_text][/vc_column][vc_column width="1/4"][vcex_heading text="Follow Us" tag="div" font_size="base" bottom_margin="30px"][vc_column_text color="currentColor" line_height="relaxed"]<a href="#">Facebook</a><br />
<a href="#">Instagram</a><br />
<a href="#">Twitter (X)</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-12'] = [];
$patterns['footer-12']['name'] = esc_html__( 'Footer', 'total' ) . ' 12';
$patterns['footer-12']['category'] = 'footer';
$patterns['footer-12']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vcex_image source="external" dark_mode_check="true" onclick="home" external_image="{$ph_logo_white}" width="100px" bottom_margin="40px"][vc_column_text color="#fff"]Made with ♥  by Company X.<br />
Copyright [current_year]. All rights reserved.[/vc_column_text][/vc_column][vc_column width="1/4"][vcex_heading text="About Us" tag="div" bottom_margin="30px" font_size="base" color="#fff"][vc_column_text color="#fff" line_height="relaxed"]<a href="#">Home</a><br />
<a href="#">About</a><br />
<a href="#">Contact</a>[/vc_column_text][/vc_column][vc_column width="1/4"][vcex_heading text="Follow Us" tag="div" font_size="base" color="#fff" bottom_margin="30px"][vc_column_text color="#fff" line_height="relaxed"]<a href="#">Facebook</a><br />
<a href="#">Instagram</a><br />
<a href="#">Twitter (X)</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-13'] = [];
$patterns['footer-13']['name'] = esc_html__( 'Footer', 'total' ) . ' 13';
$patterns['footer-13']['category'] = 'footer';
$patterns['footer-13']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" el_class="wpex-border-t wpex-border-solid wpex-border-main"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="4vmax"][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vcex_heading text="Contact Us" tag="div" font_size="2xl" bottom_margin="30px"][vc_column_text color="currentColor"]123 Demo Street, New York, NY 12345<br />(555) 555-5555<br />email@example.com[/vc_column_text][vcex_spacing][vcex_button onclick_url="#" font_size="sm"]Get in Touch[/vcex_button][/vc_column][vc_column width="1/4"][vcex_heading text="Pages" tag="div" font_size="base" bottom_margin="30px"][vc_column_text color="currentColor" line_height="relaxed"]<a href="#">Services</a><br />
<a href="#">Testimonials</a><br />
<a href="#">Pricing &amp; Plans</a>[/vc_column_text][/vc_column][vc_column width="1/4"][vcex_heading text="Follow Us" tag="div" font_size="base" bottom_margin="30px"][vc_column_text color="currentColor" line_height="relaxed"]<a href="#">Facebook</a><br />
<a href="#">Instagram</a><br />
<a href="#">Twitter (X)</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing][vc_column_text color="currentColor"]© [current_year] Company Name<br />
View our <a href="#">Privacy &amp; Cookie Policy.</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="4vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-14'] = [];
$patterns['footer-14']['name'] = esc_html__( 'Footer', 'total' ) . ' 14';
$patterns['footer-14']['category'] = 'footer';
$patterns['footer-14']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="4vmax"][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vcex_heading color="#fff" text="Contact Us" tag="div" font_size="2xl" bottom_margin="30px"][vc_column_text color="#fff"]123 Demo Street, New York, NY 12345<br />(555) 555-5555<br />email@example.com[/vc_column_text][vcex_spacing][vcex_button onclick_url="#" font_size="sm"]Get in Touch[/vcex_button][/vc_column][vc_column width="1/4"][vcex_heading color="#fff" text="Pages" tag="div" font_size="base" bottom_margin="30px"][vc_column_text color="#fff" line_height="relaxed"]<a href="#">Services</a><br />
<a href="#">Testimonials</a><br />
<a href="#">Pricing &amp; Plans</a>[/vc_column_text][/vc_column][vc_column width="1/4"][vcex_heading color="#fff" text="Follow Us" tag="div" font_size="base" bottom_margin="30px"][vc_column_text color="#fff" line_height="relaxed"]<a href="#">Facebook</a><br />
<a href="#">Instagram</a><br />
<a href="#">Twitter (X)</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing][vc_column_text color="#fff"]© [current_year] Company Name<br />
View our <a href="#">Privacy &amp; Cookie Policy.</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="4vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-15'] = [];
$patterns['footer-15']['name'] = esc_html__( 'Footer', 'total' ) . ' 15';
$patterns['footer-15']['category'] = 'footer';
$patterns['footer-15']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" el_class="wpex-border-t wpex-border-solid wpex-border-main"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row][vc_column width="1/3"][vcex_heading text="Hours" tag="div" text_align="center" font_size="base" bottom_margin="30px" text_transform="uppercase" letter_spacing="wider"][vc_column_text color="currentColor" text_align="center"]Monday: <em>CLOSED</em><br />
Tuesday-Sunday: <em>7 AM – 8 PM</em><br />
Saturday-Sunday: <em>10 AM – 11 PM</em></p>
<p>Happy Hour: <em>2 PM - 5 PM</em>[/vc_column_text][/vc_column][vc_column width="1/3"][vcex_heading text="Menu" tag="div" text_align="center" font_size="base" bottom_margin="30px" text_transform="uppercase" letter_spacing="wider"][vc_column_text text_align="center" color="currentColor"]<a href="#">Breafast</a></p>
<p><a href="#">Lunch</a></p>
<p><a href="#">Dinner</a>[/vc_column_text][/vc_column][vc_column width="1/3"][vcex_heading text="Follow Us" tag="div" text_align="center" font_size="base" bottom_margin="30px" text_transform="uppercase" letter_spacing="wider"][vcex_social_links social_links="%5B%7B%22site%22%3A%22yelp%22%2C%22link%22%3A%22%23%22%7D%2C%7B%22site%22%3A%22facebook%22%2C%22link%22%3A%22%23%22%7D%2C%7B%22site%22%3A%22instagram%22%2C%22link%22%3A%22%23%22%7D%5D" show_label="true" direction="vertical" align="center" style="none" classes="wpex-underline" color="currentColor" spacing="20px" padding_y="0px" padding_x="0px"][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][vc_column_text color="currentColor" text_align="center"]Make a reservation via <a href="#">Open Table</a> or Call us at <a href="tel:(555) 123-4567">(555) 123-4567</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;

$patterns['footer-16'] = [];
$patterns['footer-16']['name'] = esc_html__( 'Footer', 'total' ) . ' 16';
$patterns['footer-16']['category'] = 'footer';
$patterns['footer-16']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][/vc_column][/vc_row][vc_section full_width="stretch_row" wpex_bg_color="#262626"][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][vc_row][vc_column width="1/3"][vcex_heading color="#fff" text="Hours" tag="div" text_align="center" font_size="base" bottom_margin="30px" text_transform="uppercase" letter_spacing="wider"][vc_column_text color="#fff" text_align="center"]Monday: <em>CLOSED</em><br />
Tuesday-Sunday: <em>7 AM – 8 PM</em><br />
Saturday-Sunday: <em>10 AM – 11 PM</em></p>
<p>Happy Hour: <em>2 PM - 5 PM</em>[/vc_column_text][/vc_column][vc_column width="1/3"][vcex_heading color="#fff" text="Menu" tag="div" text_align="center" font_size="base" bottom_margin="30px" text_transform="uppercase" letter_spacing="wider"][vc_column_text text_align="center" color="#fff"]<a href="#">Breafast</a></p>
<p><a href="#">Lunch</a></p>
<p><a href="#">Dinner</a>[/vc_column_text][/vc_column][vc_column width="1/3"][vcex_heading color="#fff" text="Follow Us" tag="div" text_align="center" font_size="base" bottom_margin="30px" text_transform="uppercase" letter_spacing="wider"][vcex_social_links social_links="%5B%7B%22site%22%3A%22yelp%22%2C%22link%22%3A%22%23%22%7D%2C%7B%22site%22%3A%22facebook%22%2C%22link%22%3A%22%23%22%7D%2C%7B%22site%22%3A%22instagram%22%2C%22link%22%3A%22%23%22%7D%5D" show_label="true" direction="vertical" align="center" style="none" classes="wpex-underline" color="#fff" spacing="20px" padding_y="0px" padding_x="0px"][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="5vmax"][vc_column_text color="#fff" text_align="center"]Make a reservation via <a href="#">Open Table</a> or Call us at <a href="tel:(555) 123-4567">(555) 123-4567</a>[/vc_column_text][/vc_column][/vc_row][vc_row remove_bottom_col_margin="true"][vc_column][vcex_spacing size="6.5vmax"][/vc_column][/vc_row][/vc_section]
CONTENT;
// IMPORTANT - Space required to prevent HEREDOC errors.
