<?php


namespace WooProductAttributes\Inc;


class WooProductAttributes
{
    protected $plugin_url;
    protected $plugin_path;
    protected $template_path;

    public function __construct()
    {
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->template_path = plugin_dir_path(__FILE__) . 'template';
    }

    public function init()
    {
        add_action('admin_menu', [$this, 'register_interface']);
    }

    public function register_interface()
    {
        $interfaceService = new UserInterface();
        $interfaceService->register_pages();
        $interfaceService->load_scripts();
    }
}