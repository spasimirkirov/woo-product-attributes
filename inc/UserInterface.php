<?php


namespace WooProductAttributes\Inc;


class UserInterface extends WooProductAttributes
{
    public $pages;

    public function register_pages()
    {
        $this->pages['main'] = add_menu_page('Woo Product Attributes', 'Woo Product Attributes', 'manage_options', 'woo_product_attributes');
        $this->pages['sub'] = [
            ['woo_product_attributes', 'Woo Custom Titles', 'Атробути', 'manage_options', 'woo_product_attributes', [$this, 'woo_product_attributes_page']],
        ];
    }

    public function woo_product_attributes_page()
    {
        require_once $this->template_path . '/list.php';
    }

    public function load_scripts()
    {
        add_action('load-' . $this->pages['main'], [$this, 'enqueue_scripts']);
        foreach ($this->pages['sub'] as $page) {
            $sub_page = add_submenu_page(...$page);
            add_action('load-' . $sub_page, [$this, 'enqueue_scripts']);
        }
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('bootstrap4_css', $this->plugin_url . 'assets/css/bootstrap.min.css');
        wp_enqueue_script('jquery_slim_min', $this->plugin_url . 'assets/js/jquery-3.5.1.slim.min.js', array('jquery'), '', true);
        wp_enqueue_script('popper_min', $this->plugin_url . 'assets/js/popper.min.js', array('jquery'), '', true);
        wp_enqueue_script('bootstrap4_js', $this->plugin_url . 'assets/js/bootstrap.min.js', array('jquery'), '', true);
        wp_enqueue_script('plugin_main_js', $this->plugin_url . 'assets/js/main.js', '', '', true);
    }
}