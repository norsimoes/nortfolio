<?php

/* ----------------------------------------------------------------------------
 * Get i18n
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

/* ----------------------------------------------------------------------------
 * DataTables rows
 * ----------------------------------------------------------------------------
 */
$tableDataArr = [];

if (isset($data['records'])) {

    foreach ($data['records'] as $row) {

        $rowArr = [];
        $cellAction = '';

        /* ----------------------------------------------------------------------------
         * Edit button
         * ----------------------------------------------------------------------------
         */
        if (\Lib\Access::getInstance()->module($data['module-id-edit'])) {

            $link = new \Lib\Html\A();

            $link->setAttr('href', $data['url-edit'] . $row->translation_item_id . '/');
            $link->setAttr('title',  $i18n['Manage'][15]);
            $link->setAttr('class', 'btn dt-btn text-primary');
            $link->setContent('<span class="fa fa-fw fa-edit"></span>');

            $cellAction .= $link->render();
        }

        /* ----------------------------------------------------------------------------
         * Delete button
         * ----------------------------------------------------------------------------
         */
        if (\Lib\Access::getInstance()->module($data['module-id-delete'])) {

            $link = new \Lib\Html\A();

            $link->setAttr('title', $i18n['Manage'][16]);
            $link->setAttr('class', 'btn dt-btn text-secondary');
            $link->setAttr('data-bs-toggle', 'modal');
            $link->setAttr('data-bs-target', '#j-confirmation-modal');
            $link->setAttr('data-url', $data['url-delete'] . $row->translation_item_id . '/');
            $link->setAttr('data-icon', 'fas fa-trash');
            $link->setAttr('data-color', 'danger');
            $link->setAttr('data-title', str_replace('{txt}', $row->value, $i18n['Manage'][17]));
            $link->setAttr('data-text', '<b>' . $i18nCore['Manage'][7] . '</b>');
            $link->setAttr('data-submit-color', 'danger');
            $link->setAttr('data-submit-label', $i18nCore['Common'][4]);
            $link->setAttr('data-cancel-label', $i18nCore['Common'][3]);
            $link->setContent('<span class="fas fa-fw fa-trash"></span>');

            $cellAction .= $link->render();
        }

        /* ----------------------------------------------------------------------------
         * Add row to dataTables
         * ----------------------------------------------------------------------------
         */
        $rowArr['DT_RowData'] = [
            'id' => $row->translation_item_id,
            'desc' => $row->value
        ];

        $rowArr[0] = $row->translation_item_id;
        $rowArr[1] = $row->array_key;
        $rowArr[2] = $row->value;
        $rowArr[3] = $cellAction;

        $tableDataArr[] = $rowArr;
    }

    /* ----------------------------------------------------------------------------
     * Return data
     * ----------------------------------------------------------------------------
     */
    $jsonArr = [
        "draw" => $data['DT_draw'],
        "recordsTotal" => $data['DT_recordsTotal'],
        "recordsFiltered" => $data['DT_recordsFiltered'],
        "data" => $tableDataArr
    ];

    echo json_encode($jsonArr);
}
