<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$experienceData = $data['experience-data'];
$i18nCore = $data['i18n-core'];
$i18n = $data['i18n'];

/* ----------------------------------------------------------------------------
 * Name html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('name', 'input', $experienceData);

$nameHtml = $translation->render(
    $i18n['Formulary'][1],
    $i18n['Formulary'][2],
    '',
    $experienceData->name ?? '',
    $experienceData->name_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Start html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('start', 'input', $experienceData);

$startHtml = $translation->render(
    $i18n['Formulary'][3],
    $i18n['Formulary'][4],
    '',
    $experienceData->start ?? '',
    $experienceData->start_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * End html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('end', 'input', $experienceData);

$endHtml = $translation->render(
    $i18n['Formulary'][5],
    $i18n['Formulary'][6],
    '',
    $experienceData->end ?? '',
    $experienceData->end_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Company html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('company', 'input', $experienceData);

$companyHtml = $translation->render(
    $i18n['Formulary'][7],
    $i18n['Formulary'][8],
    '',
    $experienceData->company ?? '',
    $experienceData->company_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Location html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('location', 'input', $experienceData);

$locationHtml = $translation->render(
    $i18n['Formulary'][9],
    $i18n['Formulary'][10],
    '',
    $experienceData->location ?? '',
    $experienceData->location_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Description html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('desc', 'textarea', $experienceData);

$descriptionHtml = $translation->render(
    $i18n['Formulary'][11],
    $i18n['Formulary'][12],
    '',
    $experienceData->description ?? '',
    $experienceData->description_i18n ?? []
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

                                <!-- Name -->
                                <?= $nameHtml ?>

                                <div class="row g-3">
                                    <div class="col-12 col-xl-6">

                                        <!-- Start -->
                                        <?= $startHtml ?>

                                    </div>
                                    <div class="col-12 col-xl-6">

                                        <!-- End -->
                                        <?= $endHtml ?>

                                    </div>
                                </div>

                                <!-- Company -->
                                <?= $companyHtml ?>

                                <!-- Location -->
                                <?= $locationHtml ?>

                                <!-- Description -->
                                <?= $descriptionHtml ?>

                                <!-- Url -->
                                <div class="mb-2">
                                    <label title="<?= $i18n['Formulary'][14] ?>"><?= strtolower($i18n['Formulary'][13]) ?></label>
                                    <textarea class="form-control" name="tech" rows="3"><?= $experienceData->tech ?></textarea>
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
