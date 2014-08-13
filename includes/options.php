<?php 
/**
 * Div Options page
 * Option pages declared by Div Starter
 *
 * @author    Div Blend Team
 * @category  Framework/Options
 * @license   GPL
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*****************************
 * Setup Custom Pages & Menus
 *****************************/
if(function_exists("register_options_page")){
  register_options_page('Theme Settings');
  register_options_page('Company Settings');

  add_action('admin_menu', 'ds_option_page_menus');  # Div Truth Options Menu
}

function ds_option_page_menus() {
  $menu_label = (get_field('ds_menu_label','option')) ? get_field('ds_menu_label','option') : '
   Options';
  if(!file_exists(THEME_APPEARANCE_DIR.'images/admin-logo.png')){
    $menu_icon = THEME_APPEARANCE_URL.'images/admin-logo.png';
  } else {
    $menu_icon = DS_APPEARANCE_URL.'images/admin-logo.png';
  }
  
  add_menu_page("<span class='ds_title'>".$menu_label."</span>", "<span class='ds_title'>Theme Options</span>", 'manage_options','admin.php?page=acf-options-theme-settings',"",$menu_icon,4);
  add_submenu_page('admin.php?page=acf-options-theme-settings', __('Company Settings','theme-main'), __('Company Settings','theme-main'), 'manage_options', 'admin.php?page=acf-options-company-settings');
}

?>