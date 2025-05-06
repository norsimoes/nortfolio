<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18nCore = $data['i18n-core'] ?? [];
$i18n = $data['i18n'] ?? [];

/* ----------------------------------------------------------------------------
 * Modules
 * ----------------------------------------------------------------------------
 */
function modulesHtml($moduleData = [], $controllerActions = [], $defaultAction = '', $rolePermissions = [], $level = 1): string
{
    $modulesHtml = '';

    foreach ($moduleData as $moduleId => $module) {

        $checked = in_array($moduleId, $rolePermissions) ? 'checked' : '';

        /*
         * Module actions
         */
        $actionsHtml = '';
        $actions = $module['actions'] ?: [];

        $actionsHtml .= '
        <td>
            <div class="form-check flex-center m-0" title="' . $defaultAction . '">
                <input type="checkbox" class="form-check-input cursor-pointer" id="' . $moduleId . '" name="' . $moduleId . '" value="" ' . $checked . '>
                <label class="form-check-label" for="' . $moduleId . '"></label>
            </div>
        </td>
        ';

        if (!empty($controllerActions)) {

            foreach ($controllerActions as $actionName => $controllerAction) {

                if (in_array($controllerAction, $actions)) {

                    $actionModuleId = array_search($controllerAction, $actions);

                    $checked = in_array($actionModuleId, $rolePermissions) ? 'checked' : '';

                    $actionsHtml .= '
                    <td>
                        <div class="form-check flex-center m-0" title="' . $actionName . '">
                            <input type="checkbox" class="form-check-input cursor-pointer j-checkbox" id="' . $actionModuleId . '" name="' . $actionModuleId . '" value="" ' . $checked . '>
                            <label class="form-check-label" for="' . $actionModuleId . '"></label>
                        </div>
                    </td>
                    ';

                } else {

                    $actionsHtml .= '<td></td>';
                }
            }
        }

        /*
         * Module row
         */
        $indent = ($level > 1) ? $level * 15 : '5';
        $icon = $module['icon'] ? '<span class="fa-fw ' . $module['icon'] . ' me-2"></span>' : '';

        $modulesHtml .= '
        <tr>
            <td style="padding-left: ' . $indent . 'px">' . $icon . $module['name'] .'</td>
            ' . $actionsHtml . '
        </tr>
        ';

        if (is_array($module['child'])) $modulesHtml .= modulesHtml($module['child'], $controllerActions, $defaultAction, $rolePermissions, $level + 1);
    }

    return $modulesHtml;
}

/* ----------------------------------------------------------------------------
 * Interfaces
 * ----------------------------------------------------------------------------
 */
$interfacesHtml = '';

if (!empty($data['interfaces'])) {

    foreach ($data['interfaces'] as $interface) {

        $modules = modulesHtml($data['modules'][$interface->module_id], $data['controller-actions'], $data['default-action'], $data['role-permissions']);

        if ($modules) {

            /*
             * Prepare interface header
             */
            $actionsHtml = '
            <td>' . $i18n['Permission'][3] . '</td>
            <td class="text-center">' . $data['default-action'] . '</td>
            ';

            if (!empty($data['controller-actions'])) {

                foreach ($data['controller-actions'] as $actionName => $controllerAction) {

                    $actionsHtml .= '<td class="text-center">' . $actionName . '</td>';
                }
            }

            /*
             * Prepare interface table
             */
            $interfaceModules = '
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        ' . $actionsHtml . '
                    </tr>
                </thead>
                <tbody>
                    ' . $modules . '
                </tbody>
            </table>
            ';

        } else {

            $interfaceModules = '<div class="interface-empty">' . $i18n['Permission'][5] . '</div>';
        }

        /*
         * Interfaces html
         */
        $icon = $interface->icon ? '<span class="fa-fw ' . $interface->icon . ' me-2"></span>' : '';

        $checked = (in_array($interface->module_id, $data['role-permissions'])) ? 'checked' : '';

        $interfacesHtml .= '
        <div class="card interface-card">
            <div class="interface-header cursor-pointer">
                <div class="form-check cursor-pointer d-flex align-items-center mx-1" title="' . $i18n['Permission'][6] . '">
                    <input type="checkbox" class="form-check-input" id="' . $interface->module_id . '" name="' . $interface->module_id . '" value="" ' . $checked . '>
                    <label class="custom-control-label" for="' . $interface->module_id . '"></label>
                </div>
                <div class="interface-title d-flex align-items-center h-100" data-bs-toggle="collapse" data-bs-target="#details' . $interface->module_id . '">
                    ' . $icon . $interface->name . '
                </div>
                <div class="check-all">
                    <div class="form-check cursor-pointer flex-center" title="' . $i18n['Permission'][4] . '">
                        <input type="checkbox" class="form-check-input j-check-all" id="all-' . $interface->module_id . '">
                        <label class="custom-control-label" for="all-' . $interface->module_id . '"></label>
                    </div>
                </div>
            </div>
            <div id="details' . $interface->module_id . '" class="collapse" data-bs-parent="#accordion">
                <div class="interface-body">
                    ' . $interfaceModules . '
                </div>
            </div>
        </div>
        ';
    }
}

/* ----------------------------------------------------------------------------
 * Back button
 * ----------------------------------------------------------------------------
 */
$button = new \Lib\Html\A();

$button->setAttr('class', 'header-button cursor-pointer');
$button->setAttr('title', $i18nCore['Manage'][4]);
$button->setAttr('href', $data['url-back']);
$button->setContent('<span class="fa fa-fw fa-arrow-left"></span> ');

$backButton = $button->render();

/* ----------------------------------------------------------------------------
 * Form action and labels
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
            <form method="post" action="<?= $formAction ?>">

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
                            <div class="col">

                                <div class="card-title"><?= $data['role-data']->name ?></div>
                                <div class="card-desc"><?= $data['role-data']->desc ?></div>

                                <div id="accordion" class="accordion mt-4">
                                    <?= $interfacesHtml ?>
                                </div>

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
