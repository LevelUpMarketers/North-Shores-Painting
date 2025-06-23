<?php

/**
 * Register Custom WPBakery Shortcode Parameters.
 */

defined( 'ABSPATH' ) || exit;

vc_add_shortcode_param(
    'vcex_text',
    'TotalThemeCore\WPBakery\Params\Text::output'
);

vc_add_shortcode_param(
    'vcex_colorpicker',
    'TotalThemeCore\WPBakery\Params\Colorpicker::output'
 );

vc_add_shortcode_param(
    'vcex_subheading',
    'TotalThemeCore\WPBakery\Params\Subheading::output'
);

vc_add_shortcode_param(
    'vcex_media_select',
    'TotalThemeCore\WPBakery\Params\Media_Select::output'
);

vc_add_shortcode_param(
    'vcex_sorter',
    'TotalThemeCore\WPBakery\Params\Sorter::output'
);

vc_add_shortcode_param(
    'vcex_button_colors',
    'TotalThemeCore\WPBakery\Params\Button_Colors::output'
);

vc_add_shortcode_param(
    'vcex_button_styles',
    'TotalThemeCore\WPBakery\Params\Button_Styles::output'
);

vc_add_shortcode_param(
    'vcex_font_family_select',
    'TotalThemeCore\WPBakery\Params\Font_Family::output'
);

vc_add_shortcode_param(
    'vcex_font_size',
    'TotalThemeCore\WPBakery\Params\Font_Size::output'
);

vc_add_shortcode_param(
    'vcex_grid_columns',
    'TotalThemeCore\WPBakery\Params\Grid_Column::output'
);

vc_add_shortcode_param(
    'vcex_grid_columns_responsive',
    'TotalThemeCore\WPBakery\Params\Grid_Column_Responsive::output'
);

vc_add_shortcode_param(
    'vcex_hover_animations',
    'TotalThemeCore\WPBakery\Params\Hover_Animation::output'
);

vc_add_shortcode_param(
    'vcex_image_crop_locations',
    'TotalThemeCore\WPBakery\Params\Image_Crop_Location::output'
);

vc_add_shortcode_param(
    'vcex_image_sizes',
    'TotalThemeCore\WPBakery\Params\Image_Size::output'
);

vc_add_shortcode_param(
    'vcex_notice',
    'TotalThemeCore\WPBakery\Params\Notice::output'
);

vc_add_shortcode_param(
    'vcex_number',
    'TotalThemeCore\WPBakery\Params\Number::output'
);

vc_add_shortcode_param(
    'vcex_ofswitch',
    'TotalThemeCore\WPBakery\Params\On_Off_Switch::output'
);

vc_add_shortcode_param(
    'vcex_responsive_sizes',
    'TotalThemeCore\WPBakery\Params\Responsive_Input::output'
);

vc_add_shortcode_param(
    'vcex_select_buttons',
    'TotalThemeCore\WPBakery\Params\Select_Buttons::output'
);

vc_add_shortcode_param(
    'vcex_social_button_styles',
    'TotalThemeCore\WPBakery\Params\Select_Social_Button_Style::output'
);

vc_add_shortcode_param(
    'vcex_text_align',
    'TotalThemeCore\WPBakery\Params\Text_Align::output'
);

vc_add_shortcode_param(
    'vcex_trbl',
    'TotalThemeCore\WPBakery\Params\Top_Right_Bottom_Left::output'
);

vc_add_shortcode_param(
    'vcex_preset_textfield',
    'TotalThemeCore\WPBakery\Params\Preset_Textfield::output'
);

vc_add_shortcode_param(
    'vcex_min_max',
    'TotalThemeCore\WPBakery\Params\Min_Max::output'
);

vc_add_shortcode_param(
    'vcex_select',
    'TotalThemeCore\WPBakery\Params\Select::output'
);

vc_add_shortcode_param(
    'vcex_multiselect',
    'TotalThemeCore\WPBakery\Params\MultiSelect::output'
);

vc_add_shortcode_param(
    'vcex_custom_field',
    'TotalThemeCore\WPBakery\Params\Custom_Field::output'
);

vc_add_shortcode_param(
    'vcex_select_callback_function',
    'TotalThemeCore\WPBakery\Params\Select_Callback_Function::output'
);

vc_add_shortcode_param(
    'vcex_select_icon',
    'TotalThemeCore\WPBakery\Params\Select_Icon::output'
);

if ( defined( 'WPCF7_VERSION' ) ) {
	vc_add_shortcode_param(
       'vcex_cf7_select',
       'TotalThemeCore\WPBakery\Params\Cf7_Select::output'
    );
}

// LEGACY - @todo deprecate completely
vc_add_shortcode_param( 'vcex_attach_images', 'vc_attach_images_form_field' );
