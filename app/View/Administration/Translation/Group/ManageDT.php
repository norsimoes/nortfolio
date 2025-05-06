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
         * Get child button
         * ----------------------------------------------------------------------------
         */
        $link = new \Lib\Html\A();

        $folderColor = ($row->item_count > 0) ? 'primary' : 'secondary';

        $link->setAttr('href', $data['url-get-item'] . $row->translation_group_id . '/');
        $link->setAttr('title', $i18n['Manage'][9] . ' ' . $row->item_count);
        $link->setAttr('class', 'btn dt-btn text-' . $folderColor);
        $link->setContent('<span class="fa fa-fw fa-folder-open"></span>');

        $cellAction .= $link->render();

        /* ----------------------------------------------------------------------------
         * Edit button
         * ----------------------------------------------------------------------------
         */
        if (\Lib\Access::getInstance()->module($data['module-id-edit'])) {

            $link = new \Lib\Html\A();

            $link->setAttr('href', $data['url-edit'] . $row->translation_group_id . '/');
            $link->setAttr('title', $i18n['Manage'][10]);
            $link->setAttr('class', 'btn dt-btn text-secondary');
            $link->setContent('<span class="fa fa-fw fa-edit"></span>');

            $cellAction .= $link->render();
        }

        /* ----------------------------------------------------------------------------
         * Clone button
         * ----------------------------------------------------------------------------
         */
        if (\Lib\Access::getInstance()->module($data['module-id-clone'])) {

            $link = new \Lib\Html\A();

            $link->setAttr('title', $i18n['ModalCloneGroup'][1]);
            $link->setAttr('class', 'btn dt-btn text-secondary');
            $link->setAttr('data-bs-toggle', 'modal');
            $link->setAttr('data-bs-target', '#j-template-modal');
            $link->setAttr('data-width', '500px');
            $link->setAttr('data-modal-title', $i18n['ModalCloneGroup'][1]);
            $link->setAttr('data-get-url', $data['url-clone-form']);
            $link->setAttr('data-post-url', $data['url-clone'] . $row->translation_id . '/' . $row->translation_group_id . '/');
            $link->setAttr('data-msg-wrong-response', $i18nCore['AjaxError'][1]);
            $link->setAttr('data-msg-ajax-fail', $i18nCore['AjaxError'][2]);
            $link->setContent('<span class="fas fa-fw fa-clone"></span>');

            $cellAction .= $link->render();
        }

        /* ----------------------------------------------------------------------------
         * Delete button
         * ----------------------------------------------------------------------------
         */
        if (\Lib\Access::getInstance()->module($data['module-id-delete'])) {

            $link = new \Lib\Html\A();

            $link->setAttr('title', $i18n['Manage'][13]);
            $link->setAttr('class', 'btn dt-btn text-secondary');
            $link->setAttr('data-bs-toggle', 'modal');
            $link->setAttr('data-bs-target', '#j-confirmation-modal');
            $link->setAttr('data-url', $data['url-delete'] . $row->translation_group_id . '/');
            $link->setAttr('data-icon', 'fas fa-trash');
            $link->setAttr('data-color', 'danger');
            $link->setAttr('data-title', str_replace('{txt}', $row->value, $i18n['Manage'][11]));
            $link->setAttr('data-text', $i18n['Manage'][12] . '<br /><b>' . $i18nCore['Manage'][7] . '</b>');
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
            'id' => $row->translation_group_id,
            'desc' => $row->value
        ];

        $rowArr[0] = $row->translation_group_id;
        $rowArr[1] = $row->call_sign;
        $rowArr[2] = $row->value;
        $rowArr[3] = $row->item_count;
        $rowArr[4] = $cellAction;

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
