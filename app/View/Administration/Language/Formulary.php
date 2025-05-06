<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$languageData = $data['language-data'];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

$isActive = $languageData->is_active ?? 1;
$checkedStatus = $languageData ? ($isActive == 1 ? 'checked' : '') : 'checked';

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

                                <!-- Reference name -->
                                <div class="mb-2">
                                    <label title="<?= $i18n['Formulary'][2] ?>"><?= strtolower($i18n['Formulary'][1]) ?></label>
                                    <input type="text" class="form-control" name="reference-name" value="<?= $languageData->reference_name ?>" required>
                                </div>

                                <!-- Local name -->
                                <div class="mb-2">
                                    <label title="<?= $i18n['Formulary'][4] ?>"><?= strtolower($i18n['Formulary'][3]) ?></label>
                                    <input type="text" class="form-control" name="local-name" value="<?= $languageData->local_name ?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-xl-6">

                                        <!-- Iso2 -->
                                        <div class="mb-2">
                                            <label title="<?= $i18n['Formulary'][6] ?>"><?= strtolower($i18n['Formulary'][5]) ?></label>
                                            <input type="text" class="form-control" name="iso2" value="<?= $languageData->iso2 ?>" required>
                                        </div>

                                    </div>
                                    <div class="col-12 col-xl-6">

                                        <!-- Iso3 -->
                                        <div class="mb-2">
                                            <label title="<?= $i18n['Formulary'][8] ?>"><?= strtolower($i18n['Formulary'][7]) ?></label>
                                            <input type="text" class="form-control" name="iso3" value="<?= $languageData->iso3 ?>" required>
                                        </div>

                                    </div>
                                </div>

                                <!-- Status  -->
                                <div class="form-check cursor-pointer">
                                    <input type="checkbox" class="form-check-input" id="is-active" name="is-active" value="1" <?= $checkedStatus ?>>
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
