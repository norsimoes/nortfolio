<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

$userData = $data['user-data'];

$status = $userData->status ?? 0;
$checkedStatus = $userData->user_id ? ($status == 1 ? 'checked' : '') : 'checked';

/* ----------------------------------------------------------------------------
 * Role select box
 * ----------------------------------------------------------------------------
 */
$dropdown = new \Lib\Html\BootstrapSelect();

$dropdown->setAttr('id', 'role-id');
$dropdown->setAttr('required', 'required');
$dropdown->setAttr('class', 'w-100');

$roleHtml = $dropdown->render(
    'role-id',
    $data['role-list'],
    $userData->role_id
);

/* ----------------------------------------------------------------------------
 * Language select box
 * ----------------------------------------------------------------------------
 */
$dropdown = new \Lib\Html\BootstrapSelect();

$dropdown->setAttr('id', 'role-id');
$dropdown->setAttr('required', 'required');
$dropdown->setAttr('class', 'w-100');

$languageHtml = $dropdown->render(
    'language-id',
    $data['language-list'],
    $userData->language_id
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

                                <div class="card-subtitle mb-2"><span title="<?= $i18n['Formulary'][2] ?>"><?= $i18n['Formulary'][1] ?></span></div>

                                <!-- Name -->
                                <div class="mb-2">
                                    <label title="<?= $i18n['Formulary'][6] ?>"><?= strtolower($i18n['Formulary'][5]) ?></label>
                                    <input type="text" class="form-control" name="name" value="<?= $userData->name ?>" required>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-xl-6">

                                        <!-- Email -->
                                        <div class="mb-2">
                                            <label title="<?= $i18n['Formulary'][8] ?>"><?= strtolower($i18n['Formulary'][7]) ?></label>
                                            <input type="text" class="form-control" name="email" value="<?= $userData->email ?>" required>
                                        </div>

                                    </div>
                                    <div class="col-12 col-xl-6">

                                        <!-- Phone -->
                                        <div class="mb-2">
                                            <label title="<?= $i18n['Formulary'][10] ?>"><?= strtolower($i18n['Formulary'][9]) ?></label>
                                            <input type="text" class="form-control" name="phone" value="<?= $userData->phone ?>">
                                        </div>

                                    </div>
                                </div>

                                <div class="card-subtitle mt-3 mb-2"><span title="<?= $i18n['Formulary'][12] ?>"><?= $i18n['Formulary'][11] ?></span></div>

                                <div class="row g-3">
                                    <div class="col-12 col-xl-6">

                                        <!-- Password -->
                                        <div class="mb-2">
                                            <label for="usr-password" title="<?= $i18n['Formulary'][14] ?>"><?= strtolower($i18n['Formulary'][13]) ?></label>
                                            <input type="password" class="form-control" id="usr-password" name="usr-password" value="" autocomplete="new-password" <?= $data['password-required'] ?>>
                                        </div>

                                    </div>
                                    <div class="col-12 col-xl-6">

                                        <!-- Password repeat -->
                                        <div class="mb-2">
                                            <label for="usr-password-repeat" title="<?= $i18n['Formulary'][16] ?>"><?= strtolower($i18n['Formulary'][15]) ?></label>
                                            <input type="password" class="form-control" id="usr-password-repeat" name="usr-password-repeat" value="" autocomplete="new-password" <?= $data['password-required'] ?>>
                                        </div>

                                    </div>
                                </div>

                                <div class="card-subtitle mt-3 mb-2"><span title="<?= $i18n['Formulary'][4] ?>"><?= $i18n['Formulary'][3] ?></span></div>

                                <div class="row g-3">
                                    <div class="col-12 col-xl-6">

                                        <!-- Password -->
                                        <div class="mb-2">
                                            <label for="usr-password" title="<?= $i18n['Formulary'][20] ?>"><?= strtolower($i18n['Formulary'][19]) ?></label>
                                            <?= $roleHtml ?>
                                        </div>

                                    </div>
                                    <div class="col-12 col-xl-6">

                                        <!-- Password repeat -->
                                        <div class="mb-2">
                                            <label for="usr-password-repeat" title="<?= $i18n['Formulary'][22] ?>"><?= strtolower($i18n['Formulary'][21]) ?></label>
                                            <?= $languageHtml ?>
                                        </div>

                                    </div>
                                </div>

                                <!-- Status  -->
                                <div class="form-check cursor-pointer mt-2">
                                    <input type="checkbox" class="form-check-input" id="status" name="status" value="1" <?= $checkedStatus ?>>
                                    <label class="form-check-label" for="status" title="<?= $i18n['Formulary'][18] ?>">
                                        <?= strtolower($i18n['Formulary'][17]) ?>
                                    </label>
                                </div>

                            </div>
                            <div class="col-12 col-xl-6">

                                <!-- Image -->

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
