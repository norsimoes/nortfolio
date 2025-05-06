<?php

/* ----------------------------------------------------------------------------
 * Administration interface header template
 * ----------------------------------------------------------------------------
 */
$template = new \Controller\Administration\Template();

$moduleModel = new \Model\Core\Module();

$session = \Lib\Session::getInstance();

$loggedUser = $session->get('user');

$i18n = $template->getI18n();

$i18nIso2 = $session->getI18n('iso2');

/* ----------------------------------------------------------------------------
 * Prepare menu
 * ----------------------------------------------------------------------------
 */
$navigationHtml = '';

$menuActive = $data['menu-active'] ?? '';
$interfaceActive = $data['interface-active'] ?? '';

$menuController = new \Controller\Administration\Menu();
$modulesArr = $menuController->loadMenu($interfaceActive, $menuActive);

if (!empty($modulesArr)) {

    foreach ($modulesArr as $interfaceObj) {

        if (empty($interfaceObj->moduleArr)) continue;

        /*
         * Prepare menu
         */
        $menuHtml = '';

        foreach ($interfaceObj->moduleArr as $module) {

            $css = 'list-group-item list-group-item-action ' . $module->active;

            $menuHtml .= '
            <a href="' . $module->url . '" class="' . $css . '">
                <span>
                    <span class="nav-icon fa-fw ' . $module->icon . '" title="' . $module->name . '" data-side="right"></span>
                    <span class="nav-label">' . $module->name . '</span>
                </span>
            </a>
            ';
        }

        if (count($modulesArr) > 1) {

            /*
             * Prepare navigation
             */
            $show = $interfaceObj->active ? 'show' : '';
            $collapsed = $interfaceObj->active ? '' : 'collapsed';

            $navigationHtml .= '
            <div class="card nav-card">
                <div class="interface-header ' . $collapsed  . '" role="button" data-toggle="collapse" data-target="#' . $interfaceObj->call_sign . '" aria-controls="' . $interfaceObj->call_sign . '">
                    <div>
                        <span class="interface-icon fa-fw ' . $interfaceObj->icon . '" title="' . $interfaceObj->name . '" data-side="right"></span>
                        <span class="interface-label">' . $interfaceObj->name . '</span>
                    </div>
                    <span class="fas fa-angle-down fa-fw arrow"></span>
                </div>
                <div id="' . $interfaceObj->call_sign . '" class="collapse ' . $show . '" data-parent="#tpl-sidebar-navigation">
                    <div class="card-body p-0">
                        ' . $menuHtml . '
                    </div>
                </div>
            </div>
            ';

        } else {

            $navigationHtml .= '
            <div class="card border-0 rounded-0">
                ' . $menuHtml . '
            </div>
            ';
        }
    }
}

/* ----------------------------------------------------------------------------
 * Output html
 * ----------------------------------------------------------------------------
 */
?>

<div id="admin-menu">

    <?= $navigationHtml ?>

</div>
