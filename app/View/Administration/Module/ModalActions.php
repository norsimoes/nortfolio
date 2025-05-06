<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'];
$i18n = $data['i18n'];

/* ----------------------------------------------------------------------------
 * Prepare actions
 * ----------------------------------------------------------------------------
 */
$actionArr = $data['actions'];

$actionsHtml = '';

if (is_array($actionArr) && count($actionArr) > 0) {

    foreach ($actionArr as $action) {

        $actionsHtml .= '
        <div class="form-check cursor-pointer mb-1">
            <input type="checkbox" class="form-check-input" id="' . $action->call_sign . '" name="' . $action->call_sign . '" checked>
            <label class="form-check-label" for="' . $action->call_sign . '">' . $action->name . '</label>
        </div>
        ';
    }
}

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="modal-body">
    <div class="form-group">
        <?= $actionsHtml ?>
    </div>
</div>
<div class="modal-footer text-right pt-0">
    <button id="submit" type="submit" class="btn btn-sm btn-primary mr-0">
        <?= $i18nCore['Common'][7] ?>
    </button>
    <button type="button" id="j-cancel" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
        <?= $i18nCore['Common'][3] ?>
    </button>
</div>
