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
            count($category->children) > 0 ?
                array_map([$this->api, 'generate_attributes'], $category->children) :
                $this->api->generate_attributes($category);
        }
    }
}