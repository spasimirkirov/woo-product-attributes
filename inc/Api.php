<?php


namespace WooProductAttributes\Inc;


class Api
{
    public $db;

    /**
     * Api constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * @return int|\WP_Error|\WP_Term[]
     */
    public static function list_categories()
    {
        $db = new Database();
        $categories = $db->select_product_categories();
        foreach ($categories as $parent) {
            $parent->children = $db->select_product_categories($parent->term_id);
        }
        return $categories;
    }

    public static function list_distinct_relation_metas()
    {
        $db = new Database();
        return $db->select_distinct_relation_metas();
    }

    /**
     * @param $category_repo
     * @return array
     */
    public function list_category_attributes($category_repo)
    {
        $db = new Database();
        $product_ids = get_posts(array(
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $category_repo->slug,
                    'operator' => 'IN',
                )
            ),
        ));
        $meta_attributes = $db->select_product_attributes($product_ids);
        $distinct_meta_attributes = [];
        foreach ($meta_attributes as $meta_attribute_array) {
            $attributes = unserialize($meta_attribute_array['_product_attributes']);
            foreach ($attributes as $attribute) {
                if ($attribute['name'] && !in_array($attribute['name'], $distinct_meta_attributes) && !$attribute['is_taxonomy'])
                    $distinct_meta_attributes[] = $attribute['name'];
            }
        }
        return $distinct_meta_attributes;
    }

    public function generate_attributes(\WP_Term $category)
    {
        $distinct_attributes = $this->list_category_attributes($category);
        $rows = $this->create_relation($category->term_id, $distinct_attributes);
        if ($rows > 0)
            show_message('<div class="update notice notice-success is-dismissible"><p>Успешно генериране на атрибути за категория ' . $category->name . '</p></div>');
    }

    public function create_relation(int $category_id, array $attributes)
    {
        $relation = $this->db->select_attributes_relation(['category_id' => $category_id, 'row' => 0]);
        $serialized_attributes = serialize($attributes);
        return $relation ?
            $this->db->update_attribute_relation($category_id, $serialized_attributes) :
            $this->db->insert_attribute_relation($category_id, $serialized_attributes);
    }

    public function get_relations()
    {
        return $this->db->select_attributes_relation();
    }

    public static function list_relations()
    {
        $api = new Api();
        return $api = $api->get_relations();
    }


    public function delete_relations(array $relation_ids)
    {
        return $this->db->delete_attribute_relation($relation_ids);
    }
}