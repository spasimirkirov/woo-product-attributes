<?php

use WooCustomTitles\Inc\Database;

function wct_activation_hook()
{
    $db = new Database();
    $db->create_attribute_templates_table();
    $db->create_title_templates_table();
    update_option("wct_auto_generate", false);
    flush_rewrite_rules();
}

function wct_deactivation_hook()
{
    flush_rewrite_rules();
}

function wct_uninstallation_hook()
{
    $db = new Database();
    $db->drop_title_attributes_table();
    $db->drop_title_templates_table();
    delete_option("wct_auto_generate");
    flush_rewrite_rules();
}

function wct_admin_menu()
{
    $main_page = add_menu_page('Woo Custom Titles', 'Woo Custom Titles', 'manage_options', 'woo_custom_titles');
    $pages = [
        ['woo_custom_titles', 'Generate custom title templates', 'Templates', 'manage_options', 'woo_custom_titles', 'custom_title_home_page'],
        ['woo_custom_titles', 'Generate custom title attributes', 'Attributes', 'manage_options', 'woo_custom_title_attributes', 'custom_title_templates_page'],
        ['woo_custom_titles', 'Custom title settings', 'Settings', 'manage_options', 'woo_custom_titles_settings', 'custom_title_settings_page'],
    ];
    add_action('load-' . $main_page, 'wct_load_admin_scripts');
    foreach ($pages as $page) {
        $sub_page = add_submenu_page(...$page);
        add_action('load-' . $sub_page, 'wct_load_admin_scripts');
    }
}

function custom_title_home_page()
{
    require_once plugin_dir_path(__FILE__) . 'template/home.php';
}

function custom_title_templates_page()
{
//    require_once plugin_dir_path(__FILE__) . 'template/templates.php';
}

function custom_title_settings_page()
{
    require_once plugin_dir_path(__FILE__) . 'template/settings.php';
}

function wct_load_admin_scripts()
{
    add_action('admin_enqueue_scripts', 'wct_enqueue_bootstrap_scripts');
}

function wct_enqueue_bootstrap_scripts()
{
    wp_enqueue_style('wpa_bootstrap4_css', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css');
    wp_enqueue_script('wpa_jquery_slim_min', plugin_dir_path(__FILE__) . 'assets/js/jquery-3.5.1.slim.min.js', array('jquery'), '', true);
    wp_enqueue_script('wpa_popper_min', plugin_dir_path(__FILE__) . 'assets/js/popper.min.js', array('jquery'), '', true);
    wp_enqueue_script('wpa_bootstrap4_js', plugin_dir_path(__FILE__) . 'assets/js/bootstrap.min.js', array('jquery'), '', true);
}

/**
 * Hooks on product meta update
 * @param $meta_id
 * @param $post_id
 * @param $meta_key
 * @param $meta_value
 */
function custom_title_hook_onmetaupdate($meta_id, $post_id, $meta_key, $meta_value)
{
    // loop trough _product_attributes upon product creation and create the new title template
//    if ($meta_key === '_product_attributes') {
//        foreach ($meta_value as $attribute) {
//        }
//    }
}