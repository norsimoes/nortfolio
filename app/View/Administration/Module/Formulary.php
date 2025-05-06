<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$moduleObj = $data['module-data'];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

$parentModuleId = $data['parent-module-id'];

$isActive = $moduleObj->is_active ?? 1;
$checkedStatus = $moduleObj ? ($isActive == 1 ? 'checked' : '') : 'checked';
$isActiveReadonly = $data['role-id'] == 1 ? '' : 'onclick="return false;"';

/* ----------------------------------------------------------------------------
 * Call sign input
 * ----------------------------------------------------------------------------
 */
$callSignHtml = '';

if ($data['role-id'] == 1) {

    $callSignHtml = '
    <div class="mb-3">
        <label title="' . $i18n['Formulary'][2] . '">' . strtolower($i18n['Formulary'][1]) . '</label>
        <input type="text" class="form-control" name="call-sign" value="' . $moduleObj->call_sign . '" required data-validation-error="ooops">
    </div>
    ';

} else {

    $callSignHtml = '
    <input type="hidden" name="call-sign" value="' . $moduleObj->call_sign . '">
    ';
}

/* ----------------------------------------------------------------------------
 * Name html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('name','input', $moduleObj);

$nameHtml = $translation->render(
    $i18n['Formulary'][3],
    $i18n['Formulary'][4],
    $i18n['MsgValidation'][1],
    $moduleObj->name ?? '',
    $moduleObj->name_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Desc html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('desc', 'textarea', $moduleObj);

$descHtml = $translation->render(
    $i18n['Formulary'][5],
    $i18n['Formulary'][6],
    '',
    $moduleObj->desc ?? '',
    $moduleObj->desc_i18n ?? []
);

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
 * Form action and button labels
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
                            <div class="col-12 col-xl-6">

                                <input type="hidden" name="parent-module-id" value="<?= $parentModuleId ?>">

                                <!-- Call sign -->
                                <?= $callSignHtml ?>

                                <!-- Name -->
                                <?= $nameHtml ?>

                                <!-- Description -->
                                <?= $descHtml ?>

                                <!-- Icon -->
                                <div class="mb-3">
                                    <label title="<?= $i18n['Formulary'][8] ?>"><?= strtolower($i18n['Formulary'][7]) ?></label>
                                    <div class="input-group icon-select">
                                        <input type="text" class="form-control iconpicker" name="icon" value="<?= $moduleObj->icon ?>" data-placement="topRight" required data-validation-error="ooops">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="height: 36px"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status  -->
                                <div class="form-check cursor-pointer">
                                    <input type="checkbox" class="form-check-input" id="is-active" name="is-active" value="1" <?= $checkedStatus ?> <?= $isActiveReadonly ?>>
                                    <label class="form-check-label" for="is-active" title="<?= $i18n['Formulary'][10] ?>">
                                        <?= strtolower($i18n['Formulary'][9]) ?>
                                    </label>
                                </div>

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
