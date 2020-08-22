<?php


namespace WooProductAttributes\Inc;


class Request
{
    private $api;

    public function __construct()
    {
        $this->api = new Api();
    }

    public function action_create_relation(int $category_id, array $attributes)
    {

        $this->api->create_relation($category_id, $attributes);
    }

    public function action_delete_relation(array $relation_ids)
    {
        $this->api->delete_relations($relation_ids);
    }

    public function action_generate_attributes()
    {
        $categories = $this->api::list_categories();
        foreach ($categories as $category) {
            foreach ($category->children as $sub_category) {
                $sub_category->distinct_attributes = $this->api::list_category_attributes($sub_category);
                $rows = $this->api->create_relation($sub_category->term_id, $sub_category->distinct_attributes);
                if ($rows > 0)
                    show_message('<div class="update notice notice-success is-dismissible"><p>Успешно генериране на атрибути за категория ' . $sub_category->name . '</p></div>');
            }
        }
    }
}