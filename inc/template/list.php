<?php

if (isset($_POST['submit_relation_action'])) {
    $requestApi = new \WooProductAttributes\Inc\Request();
    if ($_POST['action'] === 'none')
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили действие</p></div>');

    if ($_POST['action'] === 'generate')
        $requestApi->action_generate_attributes();

    if ($_POST['action'] === 'delete') {
        (!isset($_POST['relation_ids']) || empty($_POST['relation_ids'])) ?
            show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили релации за изтриване</p></div>') :
            $requestApi->action_delete_relation($_POST['relation_ids']);
    }
}

$available_relations = \WooProductAttributes\Inc\Api::list_relations();
?>
<div class="container-fluid my-2">
    <div class="row">
        <div class="col-12">
            <div class="card-header bg-dark text-light">
                Списък със синхронизирани продуктови атрибути
            </div>
            <div class="p-2">
                <form action="" method="post">
                    <table class="table table-hover table-sm">
                        <tr>
                            <th>
                                <label>
                                    <input id="checkbox_select_all" type="checkbox">
                                    Категория
                                </label>
                            </th>
                            <th>Мета атрибути</th>
                        </tr>
                        <?php foreach ($available_relations as $i => $relation): ?>
                            <tr>
                                <td>
                                    <label for="taxonomy_<?= $i ?>">
                                        <input class="checkbox-taxonomy" type="checkbox" name="relation_ids[]"
                                               value="<?= $relation['id'] ?>">
                                        <?= $relation['category_name'] ?>
                                    </label>
                                </td>
                                <td>
                                    <?= implode(', ', unserialize($relation['meta_value'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($available_relations)): ?>
                            <tr>
                                <td colspan="3">Не са намерени записи на релации</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                    <div class="form-group">
                        <label for="select_action">Изберете действие</label>
                        <div class="form-row mb-4">
                            <select class="custom-select form-control mr-1" id="select_action" name="action">
                                <option value="none">Избери</option>
                                <option value="generate">Генериране</option>
                                <?php if (!empty($available_relations)): ?>
                                    <option value="delete">Изтриване</option>
                                <?php endif; ?>
                            </select>
                            <input class="btn btn-primary btn-sm" name="submit_relation_action" type="submit"
                                   value="Изпълни">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>