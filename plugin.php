<?php
/*
Plugin Name: Woo Product Attributes
Plugin URI: https://github.com/spasimirkirov/woo-product_attributes
Description: Extract product attributes and save them to database for other usages
Version: 1.0.0
Author: Spasimir Kirov
Author URI: https://www.vonchronos.com/
License: GPLv2 or later
Text Domain: woo-custom-titles
 */

use WooProductAttributes\Inc\Database;
use WooProductAttributes\Inc\WooProductAttributes;

if (!defined('ABSPATH')) {
    die;
}

// include the Composer autoload file
require plugin_dir_path(__FILE__) . 'vendor/autoload.php';


function woo_product_attributes_activation()
{
    $db = new Database();
    $db->create_product_attributes_table();
    flush_rewrite_rules();
}

function woo_product_attributes_deactivation()
{
    flush_rewrite_rules();
}

function woo_product_attributes_uninstall()
{
    $db = new Database();
    $db->drop_product_attributes_table();
}

$is_woo_active = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
if (!$is_woo_active) {
    add_action('admin_notices', function () {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('WooCommerce е неактивен, моля активирайте го преди да използвате Woo Attribute Generator'); ?></p>
        </div>
        <?php
    });
} else {
    register_activation_hook(__FILE__, 'woo_product_attributes_activation');
    register_deactivation_hook(__FILE__, 'woo_product_attributes_deactivation');
    register_uninstall_hook(__FILE__, 'woo_product_attributes_uninstall');
    $WooAttributePlugin = new WooProductAttributes();
    $WooAttributePlugin->init();

}
