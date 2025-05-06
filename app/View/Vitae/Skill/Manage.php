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
    [$i18n['DataTables'][8]],
    [$i18n['DataTables'][6], 'data-sort-column="ASC"'],
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

    $button->setAttr('class', 'header-button cursor-pointer');
    $button->setAttr('title', $i18nCore['Manage'][1]);
    $button->setAttr('href', $data['url-add']);
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
        $button->setContent('<span class="fa fa-fw fa-sort"></span>');

    } else {

        $button->setAttr('class', 'header-button cursor-pointer');
        $button->setAttr('title', $i18nCore['Manage'][8]);
        $button->setAttr('href', $data['url-sort']);
        $button->setContent('<span class="fa fa-fw fa-sort"></span> ');
    }

    $sortButton = $button->render();
}

/* ----------------------------------------------------------------------------
 * Back button
 * ----------------------------------------------------------------------------
 */
$button = new \Lib\Html\A();

$button->setAttr('class', 'header-button cursor-pointer');
$button->setAttr('title', $i18nCore['Manage'][4]);
$button->setAttr('href', $data['url-back']);
$button->setContent('<span class="fa fa-fw fa-arrow-left"></span> ');

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
                <div class="card-title"><?= $data['active-module']->name ?></div>
                <div class="card-desc"><?= $data['active-module']->desc ?></div>
            </div>
            <div class="d-flex justify-content-end align-items-center">
                <?= $addButton . $sortButton . $backButton . $resetFiltersButton ?>
            </div>
        </div>
    </div>

    <div class="card rounded-0">
        <div class="card-body">
            <?= $dataTablesHtml ?>
        </div>
    </div>

</div>
