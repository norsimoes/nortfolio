<?php

/* ----------------------------------------------------------------------------
 * Get i18n
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

/* ----------------------------------------------------------------------------
 * Generate a new DataTables
 * ----------------------------------------------------------------------------
 */
$dataTables = new \Lib\Plugin\DataTables();

$dataTables->setAttr('class', 'table table-striped table-hover j-data-table');
$dataTables->setAttr('data-source', $data['url-data-tables-source']);

$dataTables->setHeaders(
    [$i18n['DataTables'][1]],
    [$i18n['DataTables'][2]],
    [$i18n['DataTables'][3]],
    [$i18n['DataTables'][4]],
    [$i18n['DataTables'][5]],
    [$i18n['DataTables'][8], 'data-sort-column="ASC"'],
    [$i18n['DataTables'][6], 'class="text-center" data-col-class="text-center"'],
    [$i18n['DataTables'][7], 'class="text-center" data-no-sort="" data-col-class="text-center"']
);

$dataTablesHtml = $dataTables->render();

/* ----------------------------------------------------------------------------
 * Add button
 * ----------------------------------------------------------------------------
 */
$addButton = '';

if (\Lib\Access::getInstance()->module($data['module-id-add'])) {

    $button = new \Lib\Html\A();

    $button->setAttr('class', 'header-button ml-2 cursor-pointer');
    $button->setAttr('title', $i18n['Manage'][3]);
    $button->setAttr('href', $data['url-add'] . $data['parent-module-id'] . '/');
    $button->setContent('<span class="fa fa-fw fa-plus"></span>');

    $addButton = $button->render();
}

/* ----------------------------------------------------------------------------
 * Sort button
 * ----------------------------------------------------------------------------
 */
$sortButton = '';

if (\Lib\Access::getInstance()->module($data['module-id-sort'])) {

    $button = new \Lib\Html\A();

    if ($data['total-items'] <= 1) {

        $button->setAttr('class', 'header-button header-button-disabled');
        $button->setAttr('title', $i18nCore['Manage'][9]);

    } else {

        $button->setAttr('class', 'header-button cursor-pointer');
        $button->setAttr('title', $i18n['Manage'][10]);
        $button->setAttr('href', $data['url-sort']);
    }

    $button->setContent('<span class="fa fa-fw fa-sort"></span>');

    $sortButton = $button->render();
}

/* ----------------------------------------------------------------------------
 * Actions button
 * ----------------------------------------------------------------------------
 */
$actionsButton = '';

if (\Lib\Access::getInstance()->module($data['module-id-actions'])) {

    $button = new \Lib\Html\Button();

    if ($data['grand-parent-module-id'] == 0) {

        $button->setAttr('class', 'header-button header-button-disabled');
        $button->setAttr('title', $i18n['Manage'][12]);

    } else {

        $button->setAttr('title', $i18n['Manage'][13]);
        $button->setAttr('class', 'header-button cursor-pointer');
        $button->setAttr('data-bs-toggle', 'modal');
        $button->setAttr('data-bs-target', '#j-template-modal');
        $button->setAttr('data-width', '500px');
        $button->setAttr('data-modal-title', $i18n['Manage'][13]);
        $button->setAttr('data-get-url', $data['url-get-actions']);
        $button->setAttr('data-post-url', $data['url-post-actions']);
        $button->setAttr('data-msg-wrong-response', $i18nCore['AjaxError'][1]);
        $button->setAttr('data-msg-ajax-fail', $i18nCore['AjaxError'][2]);
    }

    $button->setContent('<span class="fa fa-fw fa-star"></span>');

    $actionsButton = $button->render();
}

/* ----------------------------------------------------------------------------
 * Back button
 * ----------------------------------------------------------------------------
 */
$button = new \Lib\Html\A();

$button->setAttr('class', 'header-button cursor-pointer');
$button->setAttr('title', $i18nCore['Manage'][4]);
$button->setAttr('href', $data['url-back']);
$button->setContent('<span class="fa fa-fw fa-arrow-left ml-3"></span>');

$backButton = $button->render();

/* ----------------------------------------------------------------------------
 * Reset filters button
 * ----------------------------------------------------------------------------
 */
$button = new \Lib\Html\Button();

$button->setAttr('class', 'header-button cursor-pointer j-dt-reset');
$button->setAttr('title', $i18nCore['Manage'][5]);
$button->setContent('<span class="fas fa-sync-alt"></span>');

$resetFiltersButton = $button->render();

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="container-fluid">

    <div class="card rounded-0 mb-2">
        <div class="card-header d-flex justify-content-between">
            <div>
                <div class="card-title"><?= $i18n['Manage'][16] ?></div>
                <div class="card-desc"><?= $data['inner-breadcrumbs'] ?></div>
            </div>
            <div class="d-flex justify-content-end align-items-center">
                <?= $addButton . $sortButton . $actionsButton . $backButton . $resetFiltersButton ?>
            </div>
        </div>
    </div>

    <div class="card rounded-0">
        <div class="card-body">
            <?= $dataTablesHtml ?>
        </div>
    </div>

</div>
