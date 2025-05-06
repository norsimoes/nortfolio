<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'];
$i18n = $data['i18n'];

/* ----------------------------------------------------------------------------
 * Targets select box
 * ----------------------------------------------------------------------------
 */
$dropdown = new \Lib\Html\BootstrapSelect();

$dropdown->setAttr('data-width', '100%');
$dropdown->setAttr('data-live-search', true);
$dropdown->setAttr('required', 'required');

$moduleHtml = $dropdown->render(
    'parent-module-id',
    $data['targets'] ?? []
);

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="modal-body">
    <label><?= $i18n['Manage'][14] ?></label>
    <?= $moduleHtml ?>
</div>
<div class="modal-footer text-right pt-0">
    <button id="submit" type="submit" class="btn btn-sm btn-primary mr-0">
        <?= $i18nCore['Common'][8] ?>
    </button>
    <button type="button" id="j-cancel" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
        <?= $i18nCore['Common'][3] ?>
    </button>
</div>
