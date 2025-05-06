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
         * Icon
         * ----------------------------------------------------------------------------
         */
        $moduleIcon = '<span class="fa-fw ' . $row->icon . '"></span>';

        /* ----------------------------------------------------------------------------
         * Url
         * ----------------------------------------------------------------------------
         */
        $url = '<a href="' . APP_URL . $row->url . '" class="dt-url cursor-pointer">' . $row->url . '</a>';

        /* ----------------------------------------------------------------------------
         * Status
         * ----------------------------------------------------------------------------
         */
        $toggler = new \Lib\Html\ToggleSwitch($row->module_id, 'Model\Core\Module');

        if (\Lib\Access::getInstance()->module($data['module-id-edit']) && $data['logged-user']->role_id == 1) {

            $cellStatus = $toggler->render(1, 0, $data['url-status']);

        } else {

            $cellStatus = $toggler->renderDisabled(1, 0);
        }

        /* ----------------------------------------------------------------------------
         * Get child button
         * ----------------------------------------------------------------------------
         */
        $link = new \Lib\Html\A();

        $folderColor = ($row->child_count > 0) ? 'primary' : 'secondary';

        $link->setAttr('href', $data['url-get-child'] . $row->module_id . '/');
        $link->setAttr('title', $i18n['Manage'][11] . ' ' . $row->child_count);
        $link->setAttr('class', 'btn dt-btn text-' . $folderColor);
        $link->setContent('<span class="fa fa-fw fa-folder-open"></span>');

        $cellAction .= $link->render();

        /* ----------------------------------------------------------------------------
         * Edit button
         * ----------------------------------------------------------------------------
         */
        if (\Lib\Access::getInstance()->module($data['module-id-edit'])) {

            $link = new \Lib\Html\A();

            $link->setAttr('href', $data['url-edit'] . $row->module_id . '/' . $row->parent_module_id . '/');
            $link->setAttr('title', $i18n['Manage'][1]);
            $link->setAttr('class', 'btn dt-btn text-secondary');
            $link->setContent('<span class="fa fa-fw fa-edit"></span>');

            $cellAction .= $link->render();
        }

        /* ----------------------------------------------------------------------------
         * Move button
         * ----------------------------------------------------------------------------
         */
        if (\Lib\Access::getInstance()->module($data['module-id-move'])) {

            if ($row->parent_module_id > 0) {

                $link = new \Lib\Html\A();

                $link->setAttr('title', $i18n['Manage'][15]);
                $link->setAttr('class', 'btn dt-btn text-secondary');
                $link->setAttr('data-bs-toggle', 'modal');
                $link->setAttr('data-bs-target', '#j-template-modal');
                $link->setAttr('data-width', '500px');
                $link->setAttr('data-modal-title', $i18n['Manage'][15]);
                $link->setAttr('data-get-url', $data['url-get-target'] . $row->module_id . '/');
                $link->setAttr('data-post-url', $data['url-move'] . $row->module_id . '/');
                $link->setAttr('data-msg-wrong-response', $i18nCore['AjaxError'][1]);
                $link->setAttr('data-msg-ajax-fail', $i18nCore['AjaxError'][2]);
                $link->setContent('<span class="fas fa-fw fa-location-arrow"></span>');

                $cellAction .= $link->render();
            }
        }

        /* ----------------------------------------------------------------------------
         * Delete button
         * ----------------------------------------------------------------------------
         */
        if (\Lib\Access::getInstance()->module($data['module-id-delete'])) {

            $link = new \Lib\Html\A();

            $link->setAttr('title', $i18n['Manage'][2]);
            $link->setAttr('class', 'btn dt-btn text-secondary');
            $link->setAttr('data-bs-toggle', 'modal');
            $link->setAttr('data-bs-target', '#j-confirmation-modal');
            $link->setAttr('data-url', $data['url-delete'] . $row->module_id . '/' . $row->parent_module_id . '/');
            $link->setAttr('data-icon', 'fas fa-trash');
            $link->setAttr('data-color', 'danger');
            $link->setAttr('data-title', str_replace('{txt}', $row->name, $i18n['Manage'][6]));
            $link->setAttr('data-text', $i18n['Manage'][7] . '<br /><b>' . $i18n['Manage'][8] . '</b>');
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
            'id' => $row->module_id,
            'desc' => $row->name
        ];

        $rowArr[0] = $row->module_id;
        $rowArr[1] = $moduleIcon;
        $rowArr[2] = $row->name;
        $rowArr[3] = $row->route;
        $rowArr[4] = $url;
        $rowArr[5] = $row->position;
        $rowArr[6] = $cellStatus;
        $rowArr[7] = $cellAction;

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
