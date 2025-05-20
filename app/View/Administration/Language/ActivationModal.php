<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$translationData = $data['translation-data'];
$i18n = $data['i18n'] ?? [];

/* ----------------------------------------------------------------------------
 * Prepare output
 * ----------------------------------------------------------------------------
 */
$outputHtml = '';

if ($translationData->total_new == 0 && $translationData->total_new_route == 0) {

    $outputHtml = '
    <p class="font-weight-bold">' . $i18n['ActivationModal'][4] . '</p>
    <p>' . $i18n['ActivationModal'][5] . '</p>
    ';

} else {

    $tableContent = '';

    foreach ($translationData->db_data as $obj) {

        $tableContent .= '
        <tr>
            <td>' . $obj->db_name . '</td>
            <td>' . $obj->new . '</td>
        </tr>
        ';
    }

    $outputHtml = '
    <p>' . $i18n['ActivationModal'][6] . '</p>
    <table class="table table-sm small p-0 text-secondary">
        <tr class="font-weight-bold">
            <td>' . $i18n['ActivationModal'][7] . '</td>
            <td>' . $i18n['ActivationModal'][8] . '</td>
        </tr>
        ' . $tableContent . '
    </table>
    <p class="font-weight-bold">' . str_replace(['{total}', '{dbs}'], [$translationData->total_new, $translationData->db_total], $i18n['ActivationModal'][9]) . '</p>
    <table class="table table-sm small p-0 text-secondary">
        <tr class="font-weight-bold">
            <td class="w-75">' . $i18n['ActivationModal'][10] . '</td>
            <td>' . $translationData->total_new_route . '</td>
        </tr>
    </table>
    <p>' . $i18n['ActivationModal'][5] . '</p>
    ';
}

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="modal-body">

    <div class="text-center mt-3">
        <div class="fa-stack fa-2x mb-3">
            <i class="fas fa-circle fa-stack-2x text-primary"></i>
            <i class="fa fa-stack-1x fa-inverse fa-language"></i>
        </div>
        <div class="modal-title mb-3"><?= $i18n['ActivationModal'][1] ?></div>
    </div>

    <div><?= $outputHtml ?></div>

</div>

<div class="modal-footer text-right pt-0">
    <button id="submit" type="submit" class="btn btn-sm btn-primary mr-0">
        <?= $i18n['ActivationModal'][2] ?>
    </button>
    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
        <?= $i18n['ActivationModal'][3] ?>
    </button>
</div>
