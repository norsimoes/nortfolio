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
    [$i18n['DataTables'][1], 'data-sort-column="ASC"'],
    [$i18n['DataTables'][2]],
    [$i18n['DataTables'][3]],
    [$i18n['DataTables'][4]],
    [$i18n['DataTables'][5]],
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

    $button->setAttr('class', 'header-button cursor-pointer');
    $button->setAttr('title', $i18n['Manage'][2]);
    $button->setAttr('href', $data['url-add']);
    $button->setContent('<span class="fa fa-fw fa-plus"></span>');

    $addButton = $button->render();
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
                <div class="card-title"><?= $i18n['Manage'][1] ?></div>
                <div class="card-desc"><?= $data['active-module']->desc ?></div>
            </div>
            <div class="d-flex justify-content-end align-items-center">
                <?= $addButton . $backButton . $resetFiltersButton ?>
            </div>
        </div>
    </div>

    <div class="card rounded-0">
        <div class="card-body">
            <?= $dataTablesHtml ?>
        </div>
    </div>

</div>
