<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

/* ----------------------------------------------------------------------------
 * Translation select box
 * ----------------------------------------------------------------------------
 */
$dropdown = new \Lib\Html\BootstrapSelect(false);

$dropdown->setAttr('required', 'required');
$dropdown->setAttr('id', 'translation-id');
$dropdown->setAttr('class', 'w-100');

$translationHtml = $dropdown->render(
    'translation-id',
    $data['translations'] ?? []
);

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="modal-body j-clone-group-wrapper">
    <div class="row">
        <div class="col">

            <p><?= $i18n['ModalCloneGroup'][2] ?></p>

            <!-- Translations -->
            <?= $translationHtml ?>

        </div>
    </div>
</div>

<div class="modal-footer text-right pt-0">
    <button id="submit" type="submit" class="btn btn-sm btn-primary me-0">
        <?= $i18n['ModalCloneGroup'][3] ?>
    </button>
    <button type="button" id="j-cancel" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
        <?= $i18nCore['Common'][3] ?>
    </button>
</div>
