<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

$roleData = $data['role-data'];

/* ----------------------------------------------------------------------------
 * Call sign input
 * ----------------------------------------------------------------------------
 */
$callSignHtml = '';

if ($data['role-id'] == 1) {

    $callSignHtml = '
    <div class="mb-3">
        <label title="' . $i18n['Formulary'][2] . '">' . strtolower($i18n['Formulary'][1]) . '</label>
        <input type="text" class="form-control" name="call-sign" value="' . $roleData->call_sign . '" required data-validation-error="ooops">
    </div>
    ';

} else {

    $callSignHtml = '
    <input type="hidden" name="call-sign" value="' . $roleData->call_sign . '">
    ';
}

/* ----------------------------------------------------------------------------
 * Name html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('name','input', $roleData);

$nameHtml = $translation->render(
    $i18n['Formulary'][3],
    $i18n['Formulary'][4],
    $i18n['MsgValidation'][1],
    $roleData->name ?? '',
    $roleData->name_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Desc html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('desc', 'textarea', $roleData);

$descHtml = $translation->render(
    $i18n['Formulary'][5],
    $i18n['Formulary'][6],
    '',
    $roleData->desc ?? '',
    $roleData->desc_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Interface select box
 * ----------------------------------------------------------------------------
 */
$dropdown = new \Lib\Html\BootstrapSelect();

$dropdown->setAttr('required', 'required');
$dropdown->setAttr('class', 'w-100');

$interfaceHtml = $dropdown->render(
    'module-id',
    $data['interface-list'],
    $roleData->module_id
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
            <form method="post" action="<?= $formAction ?>" class="needs-validation">

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
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12 col-xl-6">

                                <!-- Call sign -->
                                <?= $callSignHtml ?>

                                <!-- Name -->
                                <?= $nameHtml ?>

                                <!-- Description -->
                                <?= $descHtml ?>

                                <!-- Interface-->
                                <label title="<?= $i18n['Formulary'][8] ?>"><?= strtolower($i18n['Formulary'][7]) ?></label>
                                <?= $interfaceHtml ?>

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
