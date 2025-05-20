<?php

/* ----------------------------------------------------------------------------
 * Get i18n
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

/* ----------------------------------------------------------------------------
 * Back button
 * ----------------------------------------------------------------------------
 */
$button = new \Lib\Html\A();

$button->setAttr('class', 'header-button ml-2 cursor-pointer');
$button->setAttr('title', $i18nCore['Manage'][4]);
$button->setAttr('href', $data['url-back']);
$button->setContent('<span class="fa fa-fw fa-arrow-left ml-3"></span>');

$backButton = $button->render();

/* ----------------------------------------------------------------------------
 * Blank data
 * ----------------------------------------------------------------------------
 */
$educationArr = $data['education-data'] ?? [];

$educationHtml = '';

if (!empty($educationArr)) {

    foreach ($educationArr as $education) {

        $educationHtml .= '
        <div id="item-' . $education->education_id . '" class="j-sort-tile sort-row">

            <div class="row-content">
                <div class="sort-handle j-sort-handle cursor-pointer" title="' . $i18nCore['Manage'][10] . '">
                    <span class="fa fa-fw fa-sort"></span>
                </div>
                <div class="ms-2">
                    ' . $education->course . ' @ ' . $education->institution . '
                </div>
            </div>

        </div>
        ';
    }
}

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col">

            <div class="card rounded-0 mb-2">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <div class="card-title"><?= $data['active-module']->name ?></div>
                        <div class="card-desc"><?= $data['active-module']->desc ?></div>
                    </div>
                    <div class="flex-nowrap align-self-center">
                        <div class="text-right">
                            <?= $backButton ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card rounded-0">
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div id="j-sort" class="card-body" data-post-url="<?= $data['url-sort'] ?>" data-msg-wrong-response="<?= $i18nCore['AjaxError'][1] ?>" data-msg-ajax-fail="<?= $i18nCore['AjaxError'][2] ?>">
                            <?= $educationHtml ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
