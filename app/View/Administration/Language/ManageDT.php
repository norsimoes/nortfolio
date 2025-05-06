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
         * Available switch
         * ----------------------------------------------------------------------------
         */
        $toggler = new \Lib\Html\ToggleAvailable($row->language_id, 'Model\Core\Language');

        if ($row->language_id == APP_I18N_ID) {

            $cellAvailable = '<span class="fas fa-fw fa-lock text-primary" title="' . $i18n['Manage'][7] . '"></span>';

        } else {

            if ($data['logged-user']->role_id == 1) {

                $cellAvailable = $toggler->render(1, 0, $data['url-activate'], $data['url-available']);

            } else {

                $cellAvailable = $toggler->renderDisabled(1, 0);
            }
        }

        /* ----------------------------------------------------------------------------
         * Status
         * ----------------------------------------------------------------------------
         */
        $toggler = new \Lib\Html\ToggleSwitch($row->language_id, 'Model\Core\Language');

        if (\Lib\Access::getInstance()->module($data['module-id-edit']) && $data['logged-user']->role_id == 1) {

            $cellStatus = $toggler->render(1, 0, $data['url-status']);

        } else {

            $cellStatus = $toggler->renderDisabled(1, 0);
        }

        /* ----------------------------------------------------------------------------
         * Edit button
         * ----------------------------------------------------------------------------
         */
        if (\Lib\Access::getInstance()->module($data['module-id-edit'])) {

            $link = new \Lib\Html\A();

            $link->setAttr('href', $data['url-edit'] . $row->language_id . '/');
            $link->setAttr('title', $i18n['Manage'][3]);
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

            $link->setAttr('title', $i18n['Manage'][4]);
            $link->setAttr('class', 'btn dt-btn text-secondary');
            $link->setAttr('data-bs-toggle', 'modal');
            $link->setAttr('data-bs-target', '#j-confirmation-modal');
            $link->setAttr('data-url', $data['url-delete'] . $row->language_id . '/');
            $link->setAttr('data-icon', 'fas fa-trash');
            $link->setAttr('data-color', 'danger');
            $link->setAttr('data-title', str_replace('{txt}', $row->reference_name, $i18n['Manage'][5]));
            $link->setAttr('data-text', '<b>' . $i18n['Manage'][6] . '</b>');
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
            'id' => $row->language_id,
            'desc' => $row->reference_name
        ];

        $rowArr[0] = $row->language_id;
        $rowArr[1] = $row->reference_name;
        $rowArr[2] = $row->local_name;
        $rowArr[3] = $row->iso2;
        $rowArr[4] = $row->iso3;
        $rowArr[5] = $cellAvailable;
        $rowArr[6] = $cellAction;

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
