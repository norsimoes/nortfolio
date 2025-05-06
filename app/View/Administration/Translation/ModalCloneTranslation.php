<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$translationData = $data['translation-data'];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

/* ----------------------------------------------------------------------------
 * Name inputs
 * ----------------------------------------------------------------------------
 */
$nameHtml = '';

foreach ($translationData->translations as $language) {

    $nameHtml .= '
    <div class="form-group">
        <label title="' . $i18n['Formulary'][8] . '">' . $i18n['Formulary'][3] . ' ' . strtoupper($language->iso2) . '</label>
        <input type="text" class="form-control" name="' . $language->language_id . '-name" value="' . $language->value . '" required>
    </div>
    ';
}

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="modal-body j-clone-translation-wrapper">
    <div class="row">
        <div class="col">

            <p><?= $i18n['ModalCloneTranslation'][7] ?></p>

            <!-- Call sign -->
            <div class="form-group">
                <label title="<?= $i18n['ModalCloneTranslation'][3] ?>"><?= $i18n['ModalCloneTranslation'][2] ?></label>
                <input type="text" class="form-control" id="call-sign" name="call-sign" value="" required>
            </div>

            <!-- Name -->
            <?= $nameHtml ?>

        </div>
    </div>
</div>

<div class="modal-footer text-right pt-0">
    <button id="submit" type="submit" class="btn btn-sm btn-primary mr-0">
        <?= $i18n['ModalCloneTranslation'][6] ?>
    </button>
    <button type="button" id="j-cancel" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
        <?= $i18nCore['Common'][3] ?>
    </button>
</div>
