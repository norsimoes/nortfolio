<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

$groupData = $data['group-data'];

/* ----------------------------------------------------------------------------
 * Call sign input
 * ----------------------------------------------------------------------------
 */
$callSignHtml = '';

if ($data['role-id'] == 1) {

    $callSignHtml = '
    <div class="mb-3">
        <label title="' . $i18n['Formulary'][5] . '">' . strtolower($i18n['Formulary'][1]) . '</label>
        <input type="text" class="form-control" name="call-sign" value="' . $groupData->call_sign . '" required>
    </div>
    ';

} else {

    $callSignHtml = '
    <input type="hidden" name="call-sign" value="' . $groupData->call_sign . '">
    ';
}

/* ----------------------------------------------------------------------------
 * Languages inputs
 * ----------------------------------------------------------------------------
 */
$valueHtml = '';

foreach ($groupData->translations as $language) {

    $valueHtml .= '
    <div class="mb-3">
        <label title="' . $i18n['Formulary'][6] . ' ' . $language->iso2 . '">' . strtolower($i18n['Formulary'][3]) . ' ' . $language->iso2 . '</label>
        <input type="text" class="form-control" name="' . $language->language_id . '-value" value="' . $language->value . '" required>
    </div>
    ';
}

/* ----------------------------------------------------------------------------
 * Back button
 * ----------------------------------------------------------------------------
 */
$button = new \Lib\Html\A();

$button->setAttr('class', 'header-button cursor-pointer ml-2');
$button->setAttr('title', $i18nCore['Manage'][4]);
$button->setAttr('href', $data['url-back']);
$button->setContent('<span class="fa fa-fw fa-arrow-left"></span> ');

$backButton = $button->render();

/* ----------------------------------------------------------------------------
 * Form action and labels
 * ----------------------------------------------------------------------------
 */
$formAction = $data['url-formulary-action'];
$formSubmitLabel = $data['url-submit-label'];
$formCancelLabel = $data['url-cancel-label'];

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <form method="post" action="<?= $formAction ?>" class="needs-validation" novalidate data-validation-error="<?= $i18n['MsgValidation'][1] ?>">

                <div class="card rounded-0 mb-2">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <div class="card-title"><?= $data['active-module']->name ?></div>
                            <?= $data['inner-breadcrumbs'] ?>
                        </div>
                        <div class="flex-nowrap align-self-center">
                            <div class="text-right">
                                <?= $backButton ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-0">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-6">

                                <!-- Call sign -->
                                <?= $callSignHtml ?>

                                <!-- Value -->
                                <?= $valueHtml ?>

                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-sm btn-primary me-1 cursor-pointer">
                            <?= $formSubmitLabel ?>
                        </button>
                        <a href="<?= $data['url-back'] ?>" class="btn btn-sm btn-secondary cursor-pointer">
                            <?= $formCancelLabel ?>
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
