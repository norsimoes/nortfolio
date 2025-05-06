<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$profileData = $data['profile-data'];
$i18nCore = $data['i18n-core'];
$i18n = $data['i18n'];

/* ----------------------------------------------------------------------------
 * Name html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('name', 'input', $profileData);

$nameHtml = $translation->render(
    $i18n['Formulary'][1],
    $i18n['Formulary'][2],
    '',
    $profileData->name ?? '',
    $profileData->name_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Type select box
 * ----------------------------------------------------------------------------
 */
$dropdown = new \Lib\Html\BootstrapSelect();

$dropdown->setAttr('required', 'required');
$dropdown->setAttr('class', 'w-100 mb-2');

$typeHtml = $dropdown->render(
    'type',
    $data['type-list'],
    $profileData->type
);

/* ----------------------------------------------------------------------------
 * Tooltip html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('tooltip', 'input', $profileData);

$tooltipHtml = $translation->render(
    $i18n['Formulary'][14],
    $i18n['Formulary'][15],
    '',
    $profileData->tooltip ?? '',
    $profileData->tooltip_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Icon file
 * ----------------------------------------------------------------------------
 */
$icon = $profileData->icon ? APP_URL_CDN . 'profile/' . $profileData->icon : APP_URL_ASSETS . 'Core/Img/Placeholder/Blank.svg';

$file = new \Lib\Html\FileImage();

$iconHtml = $file->render(
    'icon',
    $icon,
    $i18n['Formulary'][7],
    $i18n['Formulary'][8],
    $data['url-delete-file']
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
            <form method="post" enctype="multipart/form-data" action="<?= $formAction ?>" class="needs-validation">

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

                                <!-- Name -->
                                <?= $nameHtml ?>

                                <!-- Type -->
                                <label title="<?= $i18n['Formulary'][4] ?>"><?= strtolower($i18n['Formulary'][3]) ?></label>
                                <?= $typeHtml ?>

                                <!-- Url -->
                                <div class="mb-2">
                                    <label title="<?= $i18n['Formulary'][13] ?>"><?= strtolower($i18n['Formulary'][12]) ?></label>
                                    <input type="text" class="form-control" name="url" value="<?= $profileData->url ?>">
                                </div>

                                <!-- Tooltip -->
                                <?= $tooltipHtml ?>

                            </div>
                            <div class="col-12 col-xl-6">

                                <!-- Icon -->
                                <?= $iconHtml ?>

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
