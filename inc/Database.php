<?php

namespace WooProductAttributes\Inc;

class Database
{
    public function wpdb()
    {
        global $wpdb;
        return $wpdb;
    }

    function create_product_attributes_table()
    {
        $charset_collate = $this->wpdb()->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->wpdb()->base_prefix}woo_product_attributes` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	    `category_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
	    `meta_value` LONGTEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
        PRIMARY KEY  (`id`),
	    UNIQUE INDEX `UNIQUE KEY` (`category_id`)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    function drop_product_attributes_table()
    {
        $this->wpdb()->query("DROP TABLE IF EXISTS `{$this->wpdb()->base_prefix}woo_product_attributes`");
    }

    function select_attributes_relation(array $params = [])
    {
        if (!isset($params['output']))
            $params['output'] = 'ARRAY_A';

        $sql = "SELECT a.`id`, a.`category_id`, b.`name` as `category_name`, a.`meta_value` FROM `{$this->wpdb()->base_prefix}woo_product_attributes` as a";
        $sql .= " INNER JOIN `{$this->wpdb()->base_prefix}terms` as b ON a.`category_id` = b.`term_id`";

        $sql .= isset($params['id']) ?
            $this->wpdb()->prepare(" WHERE `id` = '%d'", $params['id']) :
            $this->wpdb()->prepare(" WHERE `id` > '0'");

        if (isset($params['category_id']))
            $sql .= $this->wpdb()->prepare(" AND `category_id` = '%d'", $params['category_id']);

        if (isset($params['col']))
            return $this->wpdb()->get_col($sql, $params['col']);

        if (isset($params['row']))
            return $this->wpdb()->get_row($sql, $params['output'], $params['row']);

        return $this->wpdb()->get_results($sql, $params['output']);
    }

    function insert_attribute_relation($category_id, $product_attributes)
    {
        $sql = $this->wpdb()->prepare("INSERT INTO `{$this->wpdb()->base_prefix}woo_product_attributes` (`category_id`, `meta_value`) VALUES ('%d','%s');", $category_id, $product_attributes);
        return $this->wpdb()->query($sql);
    }

    function update_attribute_relation($category_id, $product_attributes)
    {
        $sql = $this->wpdb()->prepare("UPDATE `{$this->wpdb()->base_prefix}woo_product_attributes` SET `meta_value` = '%s' WHERE `category_id` = '%d';", $product_attributes, $category_id);
        return $this->wpdb()->query($sql);
    }

    function delete_attribute_relation(array $relation_ids)
    {
        $sql = "DELETE FROM `{$this->wpdb()->base_prefix}woo_product_attributes` WHERE `id` IN(" . implode(",", $relation_ids) . ");";
        return $this->wpdb()->query($sql);
    }

    public function select_product_categories($parent = 0)
    {
        //The args. Don't set parent
        $args = array(
            'hide_empty' => 1,
            'orderby' => 'name',
            'order' => 'ASC',
            'taxonomy' => 'product_cat',
            'pad_counts' => 1
        );
        //I'll leave it to you to check for error objects etc.
        $categories = get_categories($args);
        return wp_list_filter($categories, array('parent' => $parent));
    }

    public function select_product_attributes($post_ids)
    {
        $prefix = $this->wpdb()->base_prefix;
        $sql = "SELECT `post_id`, `meta_value` as `_product_attributes` FROM `{$prefix}postmeta` WHERE meta_key ='_product_attributes'";
        $sql .= " AND `post_id` IN(" . implode(', ', $post_ids) . ")";
        return $this->wpdb()->get_results($sql, 'ARRAY_A');
    }

    public function select_distinct_relation_metas()
    {
        $metas = [];
        $relations = $this->select_attributes_relation(['col' => 3]);
        $relation_array = array_map('unserialize', $relations);
        foreach ($relation_array as $relation) {
            foreach ($relation as $meta)
                if (!in_array($meta, $metas))
                    $metas[] = $meta;
        }
        return $metas;
    }

}

