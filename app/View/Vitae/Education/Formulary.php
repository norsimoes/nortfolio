<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$educationData = $data['education-data'];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

/* ----------------------------------------------------------------------------
 * Name html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('institution', 'input', $educationData);

$institutionHtml = $translation->render(
    $i18n['Formulary'][1],
    $i18n['Formulary'][2],
    '',
    $educationData->institution ?? '',
    $educationData->institution_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Start html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('start', 'input', $educationData);

$startHtml = $translation->render(
    $i18n['Formulary'][3],
    $i18n['Formulary'][4],
    '',
    $educationData->start ?? '',
    $educationData->start_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * End html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('end', 'input', $educationData);

$endHtml = $translation->render(
    $i18n['Formulary'][5],
    $i18n['Formulary'][6],
    '',
    $educationData->end ?? '',
    $educationData->end_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Course html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('course', 'input', $educationData);

$courseHtml = $translation->render(
    $i18n['Formulary'][7],
    $i18n['Formulary'][8],
    '',
    $educationData->course ?? '',
    $educationData->course_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Grade html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('grade', 'input', $educationData);

$gradeHtml = $translation->render(
    $i18n['Formulary'][13],
    $i18n['Formulary'][14],
    '',
    $educationData->grade ?? '',
    $educationData->grade_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Description html
 * ----------------------------------------------------------------------------
 */
$translation = new \Lib\Html\TranslationField('desc', 'textarea', $educationData);

$descriptionHtml = $translation->render(
    $i18n['Formulary'][11],
    $i18n['Formulary'][12],
    '',
    $educationData->description ?? '',
    $educationData->description_i18n ?? []
);

/* ----------------------------------------------------------------------------
 * Back button
 * ----------------------------------------------------------------------------
 */
$button = new \Lib\Html\A();

$button->setAttr('class', 'header-button cursor-pointer ml-2');
$button->setAttr('title', $i18nCore['Manage'][4]);
$button->setAttr('href', $data['url-back'] ?? '');
$button->setContent('<span class="fa fa-fw fa-arrow-left"></span> ');

$backButton = $button->render();

/* ----------------------------------------------------------------------------
 * Form action and labels
 * ----------------------------------------------------------------------------
 */
$formAction = $data['url-formulary-action'] ?? '';
$formSubmitLabel = $data['url-submit-label'] ?? '';
$formCancelLabel = $data['url-cancel-label'] ?? '';

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

                                <!-- Institution -->
                                <?= $institutionHtml ?>

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

                                <!-- Course -->
                                <?= $courseHtml ?>

                                <!-- Description -->
                                <?= $descriptionHtml ?>

                                <!-- Grade -->
                                <?= $gradeHtml ?>

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
